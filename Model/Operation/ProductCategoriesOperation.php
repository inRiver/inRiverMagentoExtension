<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Operation;

use Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface;
use Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterfaceFactory;
use Inriver\Adapter\Api\ProductCategoriesInterface;
use Inriver\Adapter\Helper\ErrorCodesDirectory;
use Inriver\Adapter\Logger\Logger;
use Inriver\Adapter\Setup\Patch\Data\CategoryPimUniqueId;
use Magento\Catalog\Api\Data\CategoryLinkInterfaceFactory;
use Magento\Catalog\Api\Data\ProductExtensionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\CatalogInventory\Model\StockRegistryStorage;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Throwable;

use function __;
use function array_keys;
use function array_map;

class ProductCategoriesOperation implements ProductCategoriesInterface
{
    /** @var \Magento\Catalog\Api\ProductRepositoryInterfaceFactory */
    private $productRepositoryFactory;

    /** @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory */
    private $categoryCollectionFactory;

    /** @var \Magento\Catalog\Api\Data\CategoryLinkInterfaceFactory */
    private $categoryLinkInterfaceFactory;

    /** @var \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterfaceFactory */
    private $inriverCategoryFactory;

    /** @var \Magento\CatalogInventory\Model\StockRegistryStorage */
    private $stockRegistryStorage;

    /** @var \Inriver\Adapter\Logger\Logger  */
    private $logger;

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterfaceFactory $inriverCategoryFactory
     * @param \Magento\Catalog\Api\Data\CategoryLinkInterfaceFactory $categoryLinkInterfaceFactory
     * @param \Magento\CatalogInventory\Model\StockRegistryStorage $stockRegistryStorage
     * @param \Inriver\Adapter\Logger\Logger $logger
     */
    public function __construct(
        ProductRepositoryInterfaceFactory $productRepositoryFactory,
        CollectionFactory $categoryCollectionFactory,
        CategoryInterfaceFactory $inriverCategoryFactory,
        CategoryLinkInterfaceFactory $categoryLinkInterfaceFactory,
        StockRegistryStorage $stockRegistryStorage,
        Logger $logger
    ) {
        $this->productRepositoryFactory = $productRepositoryFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryLinkInterfaceFactory = $categoryLinkInterfaceFactory;
        $this->stockRegistryStorage = $stockRegistryStorage;
        $this->logger = $logger;
        $this->inriverCategoryFactory = $inriverCategoryFactory;
    }

    /**
     * Synchronized product categories
     *
     * @param \Inriver\Adapter\Api\Data\ProductCategoriesInterface $productCategories
     *
     * @return string[]
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
     * Remove product categories
     *
     * @param \Inriver\Adapter\Api\Data\ProductCategoriesInterface $productCategories
     *
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function delete(\Inriver\Adapter\Api\Data\ProductCategoriesInterface $productCategories): array
    {
        $this->logger->addInfo(
            __('Started Product Category Assignement Operation for sku: %1', $productCategories->getSku())
        );
        $result =  $this->deleteCategoryAssignement($productCategories->getSku(), $productCategories->getCategories());
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
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->productRepositoryFactory->create();
        $product = $this->getProduct($productRepository, $sku);

        $extensionAttributes = $product->getExtensionAttributes();
        $currentCategoryLinks = $extensionAttributes->getCategoryLinks();
        $categoriesArray = $this->linkCategoryIdToCategoryObject($categories);

        foreach ($categoriesArray['link'] as $categoryId => $category) {
            $position = $category->getPosition();
            $found = false;
            if ($currentCategoryLinks !== null) {
                foreach ($currentCategoryLinks as $currentCategoryLink) {
                    if ((int) $currentCategoryLink->getCategoryId() === $categoryId) {
                        if ($position !== null) {
                            $currentCategoryLink->setPosition($position);
                        }
                        $found = true;
                        break;
                    }
                }
            }

            if($position === null) {
                $position = 0;
            }

            if (!$found) {
                $currentCategoryLinks[] = $this->categoryLinkInterfaceFactory->create()
                    ->setPosition($position)
                    ->setCategoryId($categoryId);
            }
        }
        $this->saveProduct(
            $product,
            $categoriesArray,
            $extensionAttributes,
            $currentCategoryLinks,
            $productRepository,
            $sku
        );

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
     * @param string $sku
     * @param \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface[] $categories
     * @param \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface[] $categories
     *
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function deleteCategoryAssignement(string $sku, array $categories): array
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->productRepositoryFactory->create();
        $product = $this->getProduct($productRepository, $sku);

        $extensionAttributes = $product->getExtensionAttributes();
        $currentCategoryLinks = $extensionAttributes->getCategoryLinks();
        $categoriesArray = $this->linkCategoryIdToCategoryObject($categories);

        foreach ($categoriesArray['link'] as $categoryId => $category) {
            if ($currentCategoryLinks !== null) {
                foreach ($currentCategoryLinks as $key => $currentCategoryLink) {
                    if ((int) $currentCategoryLink->getCategoryId() === $categoryId) {
                        unset($currentCategoryLinks[$key]);
                        unset($categoriesArray['link'][$categoryId]);
                        break;
                    }
                }
            }
        }

        $this->saveProduct(
            $product,
            $categoriesArray,
            $extensionAttributes,
            $currentCategoryLinks,
            $productRepository,
            $sku
        );

        return [];
    }



    /**
     * @param \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface[] $categories
     *
     * @return string[]
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
     * @param \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface[] $newCategories
     *
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function linkCategoryIdToCategoryObject(array $newCategories): array
    {

        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addAttributeToFilter(
            CategoryPimUniqueId::CATEGORY_PIM_UNIQUE_ID,
            ['in' => $this->getCategoryUniqueIds($newCategories)]
        );

        $categoriesArray = ['link' => [], 'unlink' => []];

        foreach ($newCategories as $importCategory) {
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

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param array $categoriesArray
     * @param \Magento\Catalog\Api\Data\ProductExtensionInterface|null $extensionAttributes
     * @param $currentCategoryLinks
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param string $sku
     * @throws LocalizedException
     */
    private function saveProduct(
        ProductInterface $product,
        array $categoriesArray,
        ?ProductExtensionInterface $extensionAttributes,
        $currentCategoryLinks,
        ProductRepositoryInterface $productRepository,
        string $sku
    ): void {
        $product->setCategoryIds(array_keys($categoriesArray));
        $extensionAttributes->setCategoryLinks($currentCategoryLinks);
        $product->setExtensionAttributes($extensionAttributes);

        try {
            $productRepository->save($product);
        } catch (Throwable $exception) {
            throw new LocalizedException(
                __('Cannot save product %1 categories. %2' . $exception->getMessage(), $sku),
                null,
                ErrorCodesDirectory::CANNOT_NOT_SAVE_PRODUCT_CATEGORIES
            );
        }
    }

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param string $sku
     * @return ProductInterface
     * @throws LocalizedException
     */
    private function getProduct(ProductRepositoryInterface $productRepository, string $sku): ProductInterface
    {
        try {
            // Clean both stock registry and product repository cache to force a reload.
            // This Fixes a rare bug if another extension loaded wrong data in them.
            $this->stockRegistryStorage->clean();
            $productRepository->cleanCache();
            $product = $productRepository->get($sku, true, null, true);

        } catch (NoSuchEntityException $exception) {
            throw new LocalizedException(
                __('The sku %1 does not exist', $sku),
                $exception,
                ErrorCodesDirectory::SKU_NOT_FOUND
            );
        }
        return $product;
    }
}
