<?php

/** @noinspection MessDetectorValidationInspection */

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\MediaGallery;

use Inriver\Adapter\Model\MediaGallery\MediaGalleryManagement;
use Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface;
use Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Action;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\Gallery;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use PHPUnit\Framework\TestCase;

class MediaGalleryManagementTest extends TestCase
{
    /** @var \Magento\Framework\App\ResourceConnection|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject */
    private $resourceConnection;

    /** @var \Magento\Catalog\Model\ProductRepository|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject */
    private $productRepository;

    /** @var \Magento\Framework\EntityManager\MetadataPool|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject */
    private $metadataPool;

    /** @var \Magento\Catalog\Model\Product\Action|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject */
    private $action;

    /** @var \Magento\Catalog\Model\ResourceModel\Product\Gallery|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject */
    private $resourceModel;

    /** @var \Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject */
    private $galleryManagement;

    public function testGetEntryValueById(): void
    {
        $subject = $this->getSubject();
        $result = $subject->getEntryValueById(1);

        $this->assertEquals('string', $result);
    }

    public function testInsertGalleryValueInStore(): void
    {
        $subject = $this->getSubject();

        /** @var \Inriver\Adapter\Test\Unit\Model\MediaGallery\ProductInterface|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject $product */
        $product = $this->createMock(Product::class);

        $subject->insertGalleryValueInStore(1, 1, 1, $product);
    }

    public function testGetStoreEntry(): void
    {
        $subject = $this->getSubject();

        $entryId = 10;

        $productAttributeMediaGalleryEntry = $this->createMock(ProductAttributeMediaGalleryEntryInterface::class);
        $productAttributeMediaGalleryEntry->method('getId')->willReturn($entryId);

        /** @var \Inriver\Adapter\Test\Unit\Model\MediaGallery\ProductInterface|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject $product */
        $product = $this->createMock(Product::class);
        $product->method('getMediaGalleryEntries')->willReturn([$productAttributeMediaGalleryEntry]);

        $this->productRepository->method('get')->willReturn($product);

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $subject->getStoreEntry(
            '',
            0,
            $entryId
        );

        $this->assertEquals($result, $productAttributeMediaGalleryEntry);

        /** @noinspection PhpUnhandledExceptionInspection */
        $nullResult = $subject->getStoreEntry(
            '',
            0,
            20
        );

        $this->assertNull($nullResult);
    }

    public function testGetAttributeIdsByFrontendType(): void
    {
        $subject = $this->getSubject();
        $result = $subject->getAttributeIdsByFrontendType('front-end-type');

        $this->assertEquals([], $result);
    }

    public function testUpdateImageTypes(): void
    {
        $subject = $this->getSubject();

        $types = ['type1', 'type2', 'type3'];

        /** @var \Magento\Catalog\Model\Product|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject $product */
        $product = $this->createMock(Product::class);

        $subject->updateImageTypes(
            $types,
            'the-value',
            $product,
            0
        );
    }

    public function testGetMediaGalleryEntity(): void
    {
        $subject = $this->getSubject();
        $result = $subject->getMediaGalleryEntity(0);

        $this->assertEquals('string', $result);
    }

    public function testGetExistingStoreEntries(): void
    {
        $subject = $this->getSubject();

        /** @var \Magento\Catalog\Model\Product|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject $product */
        $product = $this->createMock(Product::class);

        /** @var \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject $entry */
        $entry = $this->createMock(ProductAttributeMediaGalleryEntryInterface::class);

        $result = $subject->getExistingStoreEntries($product, [], $entry);

        $this->assertEquals([], $result);
    }

    public function testDeleteExistingTypesForStore(): void
    {
        $subject = $this->getSubject();

        /** @var \Magento\Catalog\Model\Product|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject $product */
        $product = $this->createMock(Product::class);

        $subject->deleteExistingTypesForStore($product, 'the-value', 0);
    }

    public function testGetList(): void
    {
        $productAttributeMediaGalleryEntryList = [$this->createMock(ProductAttributeMediaGalleryEntryInterface::class)];

        $this->galleryManagement->method('getList')->willReturn($productAttributeMediaGalleryEntryList);

        $subject = $this->getSubject();
        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $subject->getList('the-sku');

        $this->assertEquals($productAttributeMediaGalleryEntryList, $result);
    }

    public function testDeleteGalleryValueInStore(): void
    {
        $subject = $this->getSubject();

        /** @var \Magento\Catalog\Model\Product|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject $product */
        $product = $this->createMock(Product::class);

        /** @var \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject $entry */
        $entry = $this->createMock(ProductAttributeMediaGalleryEntryInterface::class);

        $subject->deleteGalleryValueInStore($entry, $product, 0);
    }

    public function testDeleteGallery(): void
    {
        $subject = $this->getSubject();
        $subject->deleteGallery(0);
    }

    public function testCreate(): void
    {
        $subject = $this->getSubject();

        /** @var \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface|\Inriver\Adapter\Test\Unit\Model\MediaGallery\MockObject $entry */
        $entry = $this->createMock(ProductAttributeMediaGalleryEntryInterface::class);

        $galleryEntryId = 10;

        $this->galleryManagement->method('create')->willReturn($galleryEntryId);

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $subject->create('sku', $entry);

        $this->assertEquals($galleryEntryId, $result);
    }

    protected function setUp(): void
    {
         $this->resourceConnection = $this->createMock(ResourceConnection::class);
         $this->productRepository = $this->createMock(ProductRepository::class);
         $this->metadataPool = $this->createMock(MetadataPool::class);
         $this->action = $this->createMock(Action::class);
         $this->resourceModel = $this->createMock(Gallery::class);
         $this->galleryManagement = $this->createMock(ProductAttributeMediaGalleryManagementInterface::class);

         $this->metadataPool
             ->method('getMetadata')
             ->willReturn($this->createMock(EntityMetadataInterface::class));

         $select = $this->createMock(Select::class);
         $select->method('from')->willReturnSelf();
         $select->method('where')->willReturnSelf();
         $select->method('limit')->willReturnSelf();

         $adapter = $this->createMock(AdapterInterface::class);
         $adapter->method('select')->willReturn($select);
         $adapter->method('fetchOne')->willReturn('string');
         $adapter->method('fetchAssoc')->willReturn([]);
         $adapter->method('fetchCol')->willReturn([]);

         $this->resourceConnection->method('getConnection')->willReturn($adapter);
    }

    private function getSubject(): MediaGalleryManagement
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new MediaGalleryManagement(
            $this->resourceConnection,
            $this->productRepository,
            $this->metadataPool,
            $this->action,
            $this->resourceModel,
            $this->galleryManagement
        );
    }
}
