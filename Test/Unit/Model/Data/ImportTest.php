<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data;

use Inriver\Adapter\Api\Data\OperationResultInterfaceFactory;
use Inriver\Adapter\Logger\Logger;
use Inriver\Adapter\Model\Data\Import;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Manager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Filesystem\Io\File;
use Magento\ImportExport\Model\Import\Source\Csv;
use Magento\ImportExport\Model\Import\Source\CsvFactory;
use Magento\ImportExport\Model\ImportFactory;
use PHPUnit\Framework\TestCase;

use function __;

class ImportTest extends TestCase
{
    /** @var \Magento\ImportExport\Model\ImportFactory|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $importFactory;

    /** @var \Inriver\Adapter\Logger\Logger|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $logger;

    /** @var \Magento\Framework\Filesystem\Directory\ReadFactory|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $readFactory;

    /** @var \Magento\ImportExport\Model\Import\Source\CsvFactory|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $csvFactory;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $scopeConfig;

    /** @var \Magento\Framework\Filesystem\Io\File|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $ioFile;

    /** @var \Magento\Framework\Event\Manager|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $eventManager;

    /** @var \Inriver\Adapter\Api\Data\OperationResultInterfaceFactory|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $operationResultFactory;

    public function testFileDoesNotExist(): void
    {
        $this->ioFile->method('fileExists')->willReturn(false);
        $import = $this->createImportMock();
        $this->assertFalse($import->execute('NON_EXISTING_FILENAME'));
    }

    public function testFileValidationFailed(): void
    {
        $this->ioFile->method('fileExists')->willReturn(true);
        $this->ioFile->method('getPathInfo')->willReturn([
            'basename' => '',
            'dirname' => '',
        ]);

        $importExportMock = $this->createMock(\Magento\ImportExport\Model\Import::class);
        $importExportMock->method('setData')->willReturnSelf();
        $this->importFactory->method('create')->willReturn($importExportMock);

        $csv = $this->createMock(Csv::class);
        $this->csvFactory->method('create')->willReturn($csv);

        $importExportMock->method('validateSource')->willReturn(false);

        $import = $this->createImportMock();

        $this->assertFalse($import->execute('FILENAME'));

        $importExportMock->method('validateSource')->willThrowException(new LocalizedException(__('')));

        $this->assertFalse($import->execute('FILENAME'));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testGetFormattedLogTrace(): void
    {
        // TODO: Will be done in error handling and logging story
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testGetErrors(): void
    {
        // TODO: Will be done in error handling and logging story
    }

    protected function setUp(): void
    {
        $this->importFactory = $this->createMock(ImportFactory::class);
        $this->logger = $this->createMock(Logger::class);
        $this->readFactory = $this->createMock(ReadFactory::class);
        $this->csvFactory = $this->createMock(CsvFactory::class);
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->ioFile = $this->createMock(File::class);
        $this->eventManager = $this->createMock(Manager::class);
        $this->operationResultFactory = $this->createMock(OperationResultInterfaceFactory::class);
    }

    private function createImportMock(): Import
    {
        return new Import(
            $this->importFactory,
            $this->logger,
            $this->readFactory,
            $this->csvFactory,
            $this->scopeConfig,
            $this->ioFile,
            $this->eventManager,
            $this->operationResultFactory
        );
    }
}
