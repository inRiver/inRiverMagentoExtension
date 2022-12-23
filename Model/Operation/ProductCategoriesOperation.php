<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Operation;

use Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface;
use Inriver\Adapter\Api\ProductCategoriesInterface;
use Inriver\Adapter\Helper\ErrorCodesDirectory;
use Inriver\Adapter\Logger\Logger;
use Inriver\Adapter\Setup\Patch\Data\CategoryPimUniqueId;
use Magento\Catalog\Api\Data\CategoryLinkInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\CatalogInventory\Model\StockRegistryStorage;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Throwable;

use function __;
use function array_keys;
use function array_map;

/**
 * Class ProductCategoriesOperation ProductCategoriesOperation
 */
class ProductCategoriesOperation implements ProductCategoriesInterface
{
    /** @var \Magento\Catalog\Api\ProductRepositoryInterfaceFactory */
    private $productRepositoryFactory;

    /** @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory */
    private $categoryCollectionFactory;

    /** @var \Magento\Catalog\Api\Data\CategoryLinkInterfaceFactory */
    private $categoryLinkInterfaceFactory;

    /** @var \Magento\CatalogInventory\Model\StockRegistryStorage */
    private $stockRegistryStorage;

    /** @var \Inriver\Adapter\Logger\Logger  */
    private $logger;

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Api\Data\CategoryLinkInterfaceFactory $categoryLinkInterfaceFactory
     * @param \Magento\CatalogInventory\Model\StockRegistryStorage $stockRegistryStorage
     * @param \Inriver\Adapter\Logger\Logger $logger
     */
    public function __construct(
        ProductRepositoryInterfaceFactory $productRepositoryFactory,
        CollectionFactory $categoryCollectionFactory,
        CategoryLinkInterfaceFactory $categoryLinkInterfaceFactory,
        StockRegistryStorage $stockRegistryStorage,
        Logger $logger
    ) {
        $this->productRepositoryFactory = $productRepositoryFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryLinkInterfaceFactory = $categoryLinkInterfaceFactory;
        $this->stockRegistryStorage = $stockRegistryStorage;
        $this->logger = $logger;
    }

    /**
     * Synchronized product categories
     *
     * @param \Inriver\Adapter\Api\Data\ProductCategoriesInterface $productCategories
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function post(\Inriver\Adapter\Api\Data\ProductCategoriesInterface $productCategories): array
    {
        $this->logger->addInfo(
            __('Started Product Category Assignement Operation for sku: %1', $productCategories->getSku())
        );
        $result =  $this->processProduct($productCategories->getSku(), $productCategories->getCategories());
        $this->logger->addInfo(
            __('Finished  Product Category Assignement Operation for sku: %1', $productCategories->getSku())
        );
        return $result;
    }

    /**
     * @param string $sku
     * @param \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface[] $categories
     *
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processProduct(string $sku, array $categories): array
    {
        try {
            // Clean both stockregistry and product repository cache to force a reload.
            // Fixes bug if another extension loaded wrong data in them
            $this->stockRegistryStorage->clean();
            $productRepository = $this->productRepositoryFactory->create();
            $productRepository->cleanCache();
            $product = $productRepository->get($sku, true, null, true);
        } catch (NoSuchEntityException $exception) {
            throw new LocalizedException(
                __('The sku %1 does not exist', $sku),
                $exception,
                ErrorCodesDirectory::SKU_NOT_FOUND
            );
        }

        $categoriesArray = $this->linkCategoryIdToCategoryObject($categories);
        $extensionAttributes = $product->getExtensionAttributes();
        $currentCategoryLinks = $extensionAttributes->getCategoryLinks();
        $links = [];

        foreach ($categoriesArray['link'] as $categoryId => $category) {
            $position = $category->getPosition();

            if ($position === null) {
                $position = 0;

                if ($currentCategoryLinks !== null) {
                    foreach ($currentCategoryLinks as $currentCategoryLink) {
                        if ((int) $currentCategoryLink->getCategoryId() === $categoryId) {
                            $position = $currentCategoryLink->getPosition();

                            break;
                        }
                    }
                }
            }

            $links[] = $this->categoryLinkInterfaceFactory->create()
                    ->setPosition($position)
                    ->setCategoryId($categoryId);
        }

        $product->setCategoryIds(array_keys($categoriesArray));
        $extensionAttributes->setCategoryLinks($links);
        $product->setExtensionAttributes($extensionAttributes);

        try {
            $productRepository->save($product);
        } catch (Throwable $exception) {
            throw new LocalizedException(
                __('Cannot save product %1 categories: %2', $sku, $exception->getMessage()),
                null,
                ErrorCodesDirectory::CANNOT_NOT_SAVE_PRODUCT_CATEGORIES
            );
        }

        $errors = [];

        foreach ($categoriesArray['unlink'] as $category) {
            $errors[] = [
                'error_code' => ErrorCodesDirectory::CATEGORY_DOES_NOT_EXIST,
                'error_message' => $category->getCategoryUniqueId(),
            ];
        }

        return $errors;
    }

    /**
     * @param array $categories
     *
     * @return array
     */
    private function getCategoryUniqueIds(array $categories): array
    {
        return array_map(
            static function (CategoryInterface $category) {
                return $category->getCategoryUniqueId();
            },
            $categories
        );
    }

    /**
     * @param array $categories
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function linkCategoryIdToCategoryObject(array $categories): array
    {
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addAttributeToFilter(
            CategoryPimUniqueId::CATEGORY_PIM_UNIQUE_ID,
            ['in' => $this->getCategoryUniqueIds($categories)]
        );

        $categoriesArray = ['link' => [], 'unlink' => []];

        foreach ($categories as $importCategory) {
            $found = false;

            foreach ($categoryCollection as $category) {
                if (
                    $importCategory->getCategoryUniqueId() ===
                    $category->getData(CategoryPimUniqueId::CATEGORY_PIM_UNIQUE_ID)
                ) {
                    $categoriesArray['link'][$category->getId()] = $importCategory;
                    $found = true;
                }
            }

            if (!$found) {
                $categoriesArray['unlink'][] = $importCategory;
            }
        }

        return $categoriesArray;
    }
}
