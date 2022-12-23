<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\MediaGallery;

use Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface;
use Magento\Catalog\Model\Product\Action;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\Gallery;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

use function array_key_exists;

class MediaGalleryManagement
{
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resourceConnection;

    /** @var \Magento\Catalog\Model\ProductRepository */
    protected $productRepository;

    /** @var \Magento\Framework\EntityManager\EntityMetadata */
    protected $metadata;

    /** @var string[] */
    protected $imageTypes;

    /** @var \Magento\Catalog\Model\Product\Action */
    protected $action;

    /** @var \Magento\Catalog\Model\ResourceModel\Product\Gallery */
    protected $resourceModel;

    /** @var \Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface */
    protected $galleryManagement;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Catalog\Model\Product\Action $action
     * @param \Magento\Catalog\Model\ResourceModel\Product\Gallery $resourceModel
     * @param \Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface $galleryManagement
     *
     * @throws \Exception
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ProductRepository $productRepository,
        MetadataPool $metadataPool,
        Action $action,
        Gallery $resourceModel,
        ProductAttributeMediaGalleryManagementInterface $galleryManagement
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->productRepository = $productRepository;
        $this->metadata = $metadataPool->getMetadata(ProductInterface::class);
        $this->action = $action;
        $this->resourceModel = $resourceModel;
        $this->galleryManagement = $galleryManagement;
    }

    /**
     * Get gallery entries for a specific store
     *
     * @param string $sku
     * @param int $storeId
     * @param int $entryId
     *
     * @return \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreEntry(
        string $sku,
        int $storeId,
        int $entryId
    ): ?ProductAttributeMediaGalleryEntryInterface {
        $currentMediaGalleryEntries = $this->productRepository->get($sku, false, $storeId)->getMediaGalleryEntries();

        foreach ($currentMediaGalleryEntries as $currentMediaGalleryEntry) {
            if ((int) $currentMediaGalleryEntry->getId() === $entryId) {
                return $currentMediaGalleryEntry;
            }
        }

        return null;
    }

    /**
     * Delete all existing types for an image
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $value
     * @param int $storeId
     */
    public function deleteExistingTypesForStore(ProductInterface $product, string $value, int $storeId): void
    {
        $connection = $this->resourceConnection->getConnection();

        $connection->delete(
            $connection->getTableName('catalog_product_entity_varchar'),
            [
                'attribute_id IN(?)' => $this->getImageTypes(),
                $this->metadata->getLinkField() . ' = ?' => $product->getData($this->metadata->getLinkField()),
                'store_id = ?' => $storeId,
                'value = ?' => $value,
            ]
        );
    }

    /**
     * Get attributes id by frontend type
     *
     * @param string $frontendType
     *
     * @return string[]
     */
    public function getAttributeIdsByFrontendType(string $frontendType): array
    {
        $connection = $this->resourceConnection->getConnection();
        $bind = [':frontend_input' => $frontendType];
        $select = $connection->select()->from(
            $this->resourceConnection->getTableName('eav_attribute'),
            ['attribute_code', 'attribute_id']
        )->where(
            'frontend_input = :frontend_input'
        );

        return $connection->fetchAssoc($select, $bind);
    }

    /**
     * Update image types
     *
     * @param string[] $types
     * @param string $value
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $storeId
     */
    public function updateImageTypes(array $types, string $value, ProductInterface $product, int $storeId): void
    {
        foreach ($types as $key => &$type) {
            if (!array_key_exists($key, $this->getImageTypes())) {
                unset($types[$key]);
            }

            $type = $value;
        }

        unset($type);

        $this->deleteExistingTypesForStore($product, $value, $storeId);
        $this->action->updateAttributes([$product->getId()], $types, $storeId);
    }

    /**
     * Delete gallery value for a store
     *
     * @param \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface $entry
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $storeId
     */
    public function deleteGalleryValueInStore(
        ProductAttributeMediaGalleryEntryInterface $entry,
        ProductInterface $product,
        int $storeId
    ): void {
        $this->resourceModel->deleteGalleryValueInStore(
            $entry->getId(),
            (int) $product->getData($this->metadata->getLinkField()),
            $storeId
        );
    }

    /**
     * Get media gallery values excluding specific stores
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int[] $excludedStores
     * @param \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface $entry
     *
     * @return string[]
     */
    public function getExistingStoreEntries(
        ProductInterface $product,
        array $excludedStores,
        ProductAttributeMediaGalleryEntryInterface $entry
    ): array {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection
            ->select()
            ->from($connection->getTableName('catalog_product_entity_media_gallery_value'), 'record_id')
            ->where('store_id NOT IN(?)', $excludedStores)
            ->where($this->metadata->getLinkField() . ' = ?', (int) $product->getData($this->metadata->getLinkField()))
            ->where('value_id = ?', $entry->getId());

        return $connection->fetchCol($select);
    }

    /**
     * Get a media gallery entity by id
     *
     * @param int $valueId
     *
     * @return string
     */
    public function getMediaGalleryEntity(int $valueId): string
    {
        $tableName = $this->resourceConnection->getTableName('catalog_product_entity_media_gallery');

        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                $tableName,
                ['value']
            )->where('value_id = ?', $valueId)
            ->limit(1);

        return $connection->fetchOne($select);
    }

    /**
     * Insert gallery value for a store
     *
     * @param int $valueId
     * @param int $storeId
     * @param int $position
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     */
    public function insertGalleryValueInStore(
        int $valueId,
        int $storeId,
        int $position,
        ProductInterface $product
    ): void {
        $this->resourceModel->insertGalleryValueInStore([
            'value_id' => $valueId,
            'store_id' => $storeId,
            'position' => $position,
            $this->metadata->getLinkField() => (int) $product->getData($this->metadata->getLinkField()),
        ]);
    }

    /**
     * Delete gallery entry
     *
     * @param int|null $id
     */
    public function deleteGallery(?int $id): void
    {
        $this->resourceModel->deleteGallery($id);
    }

    /**
     * Retrieve the list of gallery entries associated with given product
     *
     * @param $sku
     *
     * @return \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface[]
     * @throws \Inriver\Adapter\Model\MediaGallery\NoSuchEntityException
     *
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function getList(string $sku): ?array
    {
        return $this->galleryManagement->getList($sku);
    }

    /**
     * Create new gallery entry
     *
     * @param string $sku
     * @param \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface $newEntry
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function create(string $sku, ProductAttributeMediaGalleryEntryInterface $newEntry): int
    {
        return (int) $this->galleryManagement->create($sku, $newEntry);
    }

    /**
     * Get an entry value by value_id
     *
     * @param int $valueId
     *
     * @return string
     */
    public function getEntryValueById(int $valueId): string
    {
        $tableName = $this->resourceConnection->getTableName('catalog_product_entity_media_gallery');

        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                $tableName,
                ['value']
            )->where('value_id = ?', $valueId)
            ->limit(1);

        return $connection->fetchOne($select);
    }

    /**
     * Get configured image types
     *
     * @return string[]
     */
    private function getImageTypes(): array
    {
        if ($this->imageTypes === null) {
            $this->imageTypes = $this->getAttributeIdsByFrontendType('media_image');
        }

        return $this->imageTypes;
    }
}
