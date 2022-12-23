<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Import;

use ArrayIterator;
use Inriver\Adapter\Model\Import\Product;
use Magento\CatalogImportExport\Model\Import\Product\ImageTypeProcessor;
use Magento\CatalogImportExport\Model\Import\Product\SkuProcessor;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Type;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\ImportExport\Model\Import\Config as ImportConfig;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /** @var \Inriver\Adapter\Model\Import\Product */
    private $model;

    public function setUp(): void
    {
        $config = $this->createMock(Config::class);
        $importConfig = $this->createMock(ImportConfig::class);
        $importConfig->method('getEntityTypes')->willReturn([]);
        $entityType = $this->createMock(Type::class);
        $entityType->method('getEntityTypeId')->willReturn(4);
        $config->method('getEntityType')->willReturn($entityType);

        $resource = $this->createMock(ResourceConnection::class);
        $adapterInterface = $this->createMock(Mysql::class);
        $resource->method('getConnection')->willReturn($adapterInterface);

        $setColFactory = $this->createMock(CollectionFactory::class);
        $setCol = $this->createMock(Collection::class);
        $setCol->method('setEntityTypeFilter')->willReturn($setCol);
        $setCol->method('getIterator')->willReturn(new ArrayIterator([]));
        $setColFactory->method('create')->willReturn($setCol);

        $imageTypeProcessor = $this->createMock(ImageTypeProcessor::class);
        $imageTypeProcessor->method('getImageTypes')->willReturn([]);
        $skuProcessor = $this->createMock(SkuProcessor::class);
        $skuProcessor->method('reloadOldSkus')->willReturn($skuProcessor);
        $skuProcessor->method('getOldSkus')->willReturn(['old_sku' => 'old_sku']);
        $eventManager = $this->createMock(ManagerInterface::class);
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            Product::class,
            [
                'config' => $config,
                'resource' => $resource,
                'setColFactory' => $setColFactory,
                'eventManager' => $eventManager,
                'importConfig' => $importConfig,
                'skuProcessor' => $skuProcessor,
                'imageTypeProcessor' => $imageTypeProcessor,
            ]
        );
    }

    public function testSetCheckAndIsDisable(): void
    {
        $this->model->setIsImportTypeDisable(true);

        $this->assertTrue($this->model->isImportTypeDisable());
        $this->assertTrue($this->model->isImportTypeConfirm());
    }
}
