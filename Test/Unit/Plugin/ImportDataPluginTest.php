<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Plugin;

use Inriver\Adapter\Exception\EmptyImportException;
use Inriver\Adapter\Helper\Import;
use Inriver\Adapter\Model\ResourceModel\Import\Data;
use Inriver\Adapter\Plugin\ImportDataPlugin;
use Magento\Catalog\Model\Product\Type;
use Magento\CatalogImportExport\Model\Import\Product;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use stdClass;

use function __;
use function count;

class ImportDataPluginTest extends TestCase
{
    private const NEW_SKU = 'newSKU';
    private const NEW_SKU_WITH_STATUS_AND_URL = 'newSKUStatus';
    private const DEFAULT_URL = 'https://ThisIsDefault.com';
    private const RANDOM_URL = 'https://ThisIsRandom.com';

    private const DEFAULT_ROW_DATA = [
        Product::COL_SKU => self::NEW_SKU,
        Product::COL_TYPE => Type::TYPE_SIMPLE,
    ];

    /** @var \Inriver\Adapter\Model\ResourceModel\Import\Data|\Inriver\Adapter\Test\Unit\Plugin\MockObject */
    private $dataSourceModel;

    /** @var \Inriver\Adapter\Helper\Import|\Inriver\Adapter\Test\Unit\Plugin\MockObject */
    private $importHelper;

    /** @var callable|\PHPUnit\Framework\MockObject\MockObject */
    private $proceed;

    /** @var array */
    private $rowsData = [];

    public function testUrlWithRandomUrl(): void
    {
        $importDataPlugin = $this->getImportDataPlugin();
        $this->importHelper
            ->method('isNewSimpleProductRowWithNoPrice')->willReturn(false);
        $this->importHelper
            ->method('isImportTypeDisable')->willReturn(false);
        $this->importHelper
            ->method('getUniqueProductUrl')->willReturn(self::RANDOM_URL);

        $rowsData = $importDataPlugin->afterGetNextBunch($this->dataSourceModel, $this->getRowsData());

        foreach ($rowsData as $rowData) {
            if ($rowData[Product::COL_SKU] === self::NEW_SKU_WITH_STATUS_AND_URL) {
                $this->assertEquals(
                    self::DEFAULT_URL,
                    $rowData[Product::URL_KEY]
                );
            } elseif ($rowData[Product::COL_SKU] === self::NEW_SKU) {
                $this->assertEquals(
                    self::RANDOM_URL,
                    $rowData[Product::URL_KEY]
                );
            }
        }
    }

    public function testUrlWithNoUrl(): void
    {
        $importDataPlugin = $this->getImportDataPlugin();
        $this->importHelper
            ->method('isNewSimpleProductRowWithNoPrice')->willReturn(false);
        $this->importHelper
            ->method('isImportTypeDisable')->willReturn(false);
        $this->importHelper
            ->method('getUniqueProductUrl')->willReturn('');

        $rowsData = $importDataPlugin->afterGetNextBunch($this->dataSourceModel, $this->getRowsData());

        foreach ($rowsData as $rowData) {
            if ($rowData[Product::COL_SKU] === self::NEW_SKU_WITH_STATUS_AND_URL) {
                $this->assertEquals(
                    self::DEFAULT_URL,
                    $rowData[Product::URL_KEY]
                );
            } elseif ($rowData[Product::COL_SKU] === self::NEW_SKU) {
                $this->assertEquals(
                    false,
                    isset($rowData[Product::URL_KEY])
                );
            }
        }
    }

    public function testIfIsNotNewProduct(): void
    {
        $importDataPlugin = $this->getImportDataPlugin();
        $this->importHelper
            ->method('isNewSimpleProductRowWithNoPrice')->willReturn(false);
        $this->importHelper
            ->method('isImportTypeDisable')->willReturn(false);
        $this->importHelper
            ->method('getUniqueProductUrl')->willReturn(self::RANDOM_URL);

        $rowsData = $importDataPlugin->afterGetNextBunch($this->dataSourceModel, $this->getRowsData());

        foreach ($rowsData as $rowData) {
            if ($rowData[Product::COL_SKU] === self::NEW_SKU_WITH_STATUS_AND_URL) {
                $this->assertEquals(
                    1,
                    $rowData['status']
                );
            } elseif ($rowData[Product::COL_SKU] === self::NEW_SKU) {
                $this->assertEquals(
                    false,
                    isset($rowData['status'])
                );
            }
        }
    }

    public function testIfIsNewProduct(): void
    {
        $importDataPlugin = $this->getImportDataPlugin();
        $this->importHelper
            ->method('isNewSimpleProductRowWithNoPrice')->willReturn(true);
        $this->importHelper
            ->method('isImportTypeDisable')->willReturn(false);
        $this->importHelper
            ->method('getUniqueProductUrl')->willReturn(self::RANDOM_URL);
        $rowsData = $importDataPlugin->afterGetNextBunch($this->dataSourceModel, $this->getRowsData());

        foreach ($rowsData as $rowData) {
            $this->assertEquals(
                2,
                $rowData['status']
            );
        }
    }

    public function testIfIsImportTypeDisableNewProduct(): void
    {
        $importDataPlugin = $this->getImportDataPlugin();
        $this->importHelper
            ->method('isImportTypeDisable')->willReturn(true);
        $this->importHelper
            ->method('isNewSku')->willReturn(true);
        $rowsData = $importDataPlugin->afterGetNextBunch($this->dataSourceModel, $this->getRowsData());

        foreach ($rowsData as $rowData) {
            if ($rowData[Product::COL_SKU] === self::NEW_SKU_WITH_STATUS_AND_URL) {
                $this->assertEquals(
                    1,
                    $rowData['status']
                );
            } elseif ($rowData[Product::COL_SKU] === self::NEW_SKU) {
                $this->assertEquals(
                    false,
                    isset($rowData['status'])
                );
            }
        }
    }

    public function testThrowAroundGetUniqueColumnData(): void
    {
        $importDataPlugin = $this->getImportDataPlugin();
        $this->proceed->expects($this->once())->method('__invoke')->willThrowException(
            new LocalizedException(__(''))
        );
        $this->expectException(EmptyImportException::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        $importDataPlugin->aroundGetUniqueColumnData($this->dataSourceModel, $this->proceed, '0');
    }

    protected function setUp(): void
    {
        $this->dataSourceModel = $this->createMock(Data::class);
        $this->importHelper =
            $this->createPartialMock(
                Import::class,
                [
                    'isNewSimpleProductRowWithNoPrice',
                    'isImportTypeDisable',
                    'isNewSku',
                    'getUniqueProductUrl',
                ]
            );
        $this->proceed = $this->createPartialMock(stdClass::class, ['__invoke']);
    }

    /**
     * @return array
     */
    private function getRowsData(): array
    {
        if (count($this->rowsData) === 0) {
            $this->rowsData[] = self::DEFAULT_ROW_DATA;
            $rowDataWithStatus = self::DEFAULT_ROW_DATA;
            $rowDataWithStatus[Product::COL_SKU] = self::NEW_SKU_WITH_STATUS_AND_URL;
            $rowDataWithStatus['status'] = 1;
            $rowDataWithStatus[Product::URL_KEY] = self::DEFAULT_URL;
            $this->rowsData[] = $rowDataWithStatus;
        }

        return $this->rowsData;
    }

    /**
     * @return \Inriver\Adapter\Plugin\ImportDataPlugin
     */
    private function getImportDataPlugin(): ImportDataPlugin
    {
        return new ImportDataPlugin($this->importHelper);
    }
}
