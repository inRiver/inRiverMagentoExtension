<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Operation;

use Exception;
use Inriver\Adapter\Api\CategoryManagementInterface;
use Inriver\Adapter\Logger\Logger;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\Category\ChildrenCategoriesProvider;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlPersistInterface;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use function __;

/**
 * Class ProductCategoriesOperation ProductCategoriesOperation
 */
class CategoryManagementOperation implements CategoryManagementInterface
{
    private CategoryCollectionFactory $categoryCollectionFactory;

    /** @var \Magento\Catalog\Model\CategoryRepository */
    private $categoryRepository;

    private ManagerInterface $eventManager;

    /** @var \Inriver\Adapter\Logger\Logger  */
    private $logger;

    private CategoryUrlRewriteGenerator $categoryUrlRewriteGenerator;

    private UrlPersistInterface $urlPersist;

    private StoreManagerInterface $storeManager;

    private ResourceConnection $resourceConnection;

    private ChildrenCategoriesProvider $childrenCategoriesProvider;

    /**
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Inriver\Adapter\Logger\Logger $logger
     */
    public function __construct(
        CategoryCollectionFactory   $categoryCollectionFactory,
        CategoryRepository $categoryRepository,
        CategoryUrlRewriteGenerator $categoryUrlRewriteGenerator,
        ManagerInterface $eventManager,
        Logger $logger,
        UrlPersistInterface $urlPersist,
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection,
        ChildrenCategoriesProvider $childrenCategoriesProvider
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->categoryUrlRewriteGenerator = $categoryUrlRewriteGenerator;
        $this->childrenCategoriesProvider = $childrenCategoriesProvider;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
        $this->urlPersist = $urlPersist;
    }

    /**
     * This is a copy of Magento\Catalog\Model\CategoryManagement::move() with the handling of exception modified
     * If you update magento, you have to validate that this function hasn't changed
     *
     * @param int $categoryId
     * @param int $parentId
     * @param int $afterId
     * @return bool
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function move($categoryId, $parentId, $afterId = null): bool
    {
        $model = $this->categoryRepository->get($categoryId);
        $parentCategory = $this->categoryRepository->get($parentId);

        if ($parentCategory->hasChildren()) {
            $parentChildren = $parentCategory->getChildren();
            $categoryIds = explode(',', $parentChildren);
            $lastId = array_pop($categoryIds);
            $afterId = ($afterId === null || $afterId > $lastId) ? $lastId : $afterId;
        }
        $parentPath = $parentCategory->getPath();
        $path = $model->getPath();
        if ($path && strpos($parentPath, $path) === 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Operation do not allow to move a parent category to any of children category')
            );
        }
        try {
            $model->move($parentId, $afterId);
        } catch (\Exception $e) {
            $message = __('Could not move category %1: %2', $categoryId, $e->getMessage());
            $this->logger->error($message);
            throw new \Magento\Framework\Exception\LocalizedException($message, $e);
        }
        return true;
    }

    /**
     * @param \Inriver\Adapter\Api\Data\CategoriesImportInterface $categoriesImport
     *
     * @return bool
     */
    public function completed(\Inriver\Adapter\Api\Data\CategoriesImportInterface $categoriesImport): bool
    {
        $this->logger->info(
            __('Starting Categories Urls Regeneration Operation')
        );

        $categoryIds = $categoriesImport->getNewCategoryIds();

        // Delete all url paths for all scopes
        // for specified cat ids and their children
        $connection = $this->resourceConnection->getConnection();

        $categories = $this->categoryCollectionFactory->create()
            ->addAttributeToFilter('entity_id', ['in' => $categoryIds])
            ->addAttributeToFilter('level', ['gt' => 1]);
        foreach ($categories as $category) {
            $childIds = array_merge(
                $this->childrenCategoriesProvider->getChildrenIds($category, true),
                [$category->getId()]
            );

            $select = $connection->select()
                ->from(['ev' => $connection->getTableName('catalog_category_entity_varchar')])
                ->join(
                    ['ea' => $connection->getTableName('eav_attribute')],
                    'ea.attribute_id = ev.attribute_id'
                )->join(
                    ['e' => $connection->getTableName('catalog_category_entity')],
                    'e.row_id = ev.row_id'
                )
                ->where(
                    'ea.attribute_code = ?','url_path'
                )->where(
                    'e.entity_id IN (?)', $childIds
                );
            $connection->query($select->deleteFromSelect('ev'));
        }

        foreach ($this->storeManager->getStores() as $store) {
            $rootCategoryId = $store->getRootCategoryId();
            $categories = $this->categoryCollectionFactory->create()
                ->setStoreId($store->getId())
                ->addAttributeToSelect(['name', 'url_key'])
                ->addAttributeToFilter('entity_id', ['in' => $categoryIds])
                ->addAttributeToFilter('path', ['like' => '1/' . $rootCategoryId . '/%'])
                ->addAttributeToFilter('level', ['gt' => 1]);
            foreach ($categories as $category) {
                $category->setStoreId($store->getId());
                $childIds = array_merge(
                    $this->childrenCategoriesProvider->getChildrenIds($category, true),
                    [$category->getId()]
                );
                // Delete all url rewrites
                foreach($childIds as $childId) {
                    $this->urlPersist->deleteByData(
                        [
                            UrlRewrite::ENTITY_ID => $childId,
                            UrlRewrite::ENTITY_TYPE => CategoryUrlRewriteGenerator::ENTITY_TYPE,
                            UrlRewrite::REDIRECT_TYPE => 0,
                            UrlRewrite::STORE_ID => $store->getId()
                        ]
                    );
                }

                // Regenerate good url rewrites
                $newUrls = $this->categoryUrlRewriteGenerator->generate($category);
                try {
                    $this->urlPersist->replace($newUrls);
                } catch (Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }

        $this->logger->info(
            __('Finished Categories Urls Regeneration Operation')
        );

        return true;
    }
}
