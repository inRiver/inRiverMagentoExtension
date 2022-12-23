<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 * @noinspection PhpUnhandledExceptionInspection
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Operation;

use Inriver\Adapter\Api\Data\ProductsImportRequestInterface;
use Inriver\Adapter\Helper\ErrorCodesDirectory;
use Inriver\Adapter\Helper\FileDownloader;
use Inriver\Adapter\Helper\FileEncoding;
use Inriver\Adapter\Model\Data\Import;
use Inriver\Adapter\Model\Data\ImportFactory;
use Inriver\Adapter\Model\Operation\CsvImportByUrlOperation;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use PHPUnit\Framework\TestCase;

class CsvImportByUrlOperationTest extends TestCase
{
    public const INRIVER_IMPORT_CSV_PATH = 'inriver/import/csv';
    private const SOME_URL = 'https://iifma.test';
    private const FAKE_PATH = '\fake\path';

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $scopeConfig;

    /** @var \Inriver\Adapter\Model\Data\ImportFactory|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $importFactory;

    /** @var \Inriver\Adapter\Helper\FileDownloader|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $downloader;

    /** @var \Magento\Framework\Filesystem|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $filesystem;

    /** @var \Inriver\Adapter\Helper\FileEncoding|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $fileEncoding;

    /** @var \Inriver\Adapter\Api\Data\ProductsImportRequestInterface|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $message;

    public function testMissingTargetDirectoryConfiguration(): void
    {
        $csvImportByUrlOperation = $this->getNewSubject();

        $this->expectExceptionCode(ErrorCodesDirectory::INRIVER_IMPORT_PATH_NOT_SET);
        $csvImportByUrlOperation->post($this->message);
    }

    public function testEmptyCsvFile(): void
    {
        $this->scopeConfig->method('getValue')->willReturnCallback(static function ($path) {
            if ($path === Import::XML_INRIVER_IMPORT_PATH_CSV) {
                return CsvImportByUrlOperationTest::INRIVER_IMPORT_CSV_PATH;
            }

            return null;
        });

        $this->downloader->method('download')->willReturn(0);

        $csvImportByUrlOperation = $this->getNewSubject();

        $this->expectExceptionCode(ErrorCodesDirectory::SOURCE_CSV_FILE_EMPTY);
        $csvImportByUrlOperation->post($this->message);
    }

    /**
     * Test normal flow execution
     *
     * @doesNotPerformAssertions
     */
    public function testProcess(): void
    {
        $this->scopeConfig->method('getValue')->willReturnCallback(static function ($path) {
            if ($path === Import::XML_INRIVER_IMPORT_PATH_CSV) {
                return CsvImportByUrlOperationTest::INRIVER_IMPORT_CSV_PATH;
            }

            return null;
        });

        $this->downloader->method('download')->willReturn(10);

        $output = $this->createMock(ReadInterface::class);
        $output->method('getAbsolutePath')->willReturn(self::FAKE_PATH);
        $this->filesystem->method('getDirectoryRead')->willReturn($output);

        $csvImportByUrlOperation = $this->getNewSubject();

        $csvImportByUrlOperation->post($this->message);
    }

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $import = $this->createMock(Import::class);
        $this->importFactory = $this->createMock(ImportFactory::class);
        $this->importFactory->method('create')->willReturn($import);
        $this->downloader = $this->createMock(FileDownloader::class);
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->fileEncoding = $this->createMock(FileEncoding::class);

        $this->message = $this->createMock(ProductsImportRequestInterface::class);
        $this->message->method('getUrl')->willReturn(self::SOME_URL);
    }

    private function getNewSubject(): CsvImportByUrlOperation
    {
        return new CsvImportByUrlOperation(
            $this->scopeConfig,
            $this->importFactory,
            $this->downloader,
            $this->filesystem,
            $this->fileEncoding
        );
    }
}
