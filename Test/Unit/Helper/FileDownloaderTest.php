<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 * @noinspection PhpUnhandledExceptionInspection
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Helper;

use Inriver\Adapter\Helper\ErrorCodesDirectory;
use Inriver\Adapter\Helper\FileDownloader;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\Framework\Filesystem\File\ReadInterface;
use Magento\Framework\Filesystem\Io\File;
use PHPUnit\Framework\TestCase;

use function __;

class FileDownloaderTest extends TestCase
{
    private const SOME_DESTINATION = 'some/destination';
    private const SOME_URL = 'https://iifma.test';
    private const NON_HTTPS_URL = 'http://iifma.test';
    private const INVALID_URL = 'ftp://iifma.test';

    /** @var \Magento\Framework\Filesystem|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $filesystem;

    /** @var \Magento\Framework\Filesystem\File\ReadFactory|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $readFactory;

    /** @var \Magento\Framework\Filesystem\Io\File|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $file;

    /** @var \Magento\Framework\Filesystem\Driver\File|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $fileDriver;

    public function testCannotCreateDirectory(): void
    {
        $directory = $this->createMock(WriteInterface::class);
        $directory->method('create')->willReturn(false);
        $this->filesystem->method('getDirectoryWrite')->willReturn($directory);

        $fileDownloader = $this->getSubject();

        $this->expectException(LocalizedException::class);
        $fileDownloader->download(self::SOME_URL, self::SOME_DESTINATION);
    }

    public function testTargetDirectoryNotWritable(): void
    {
        $directory = $this->createMock(WriteInterface::class);
        $directory->method('create')->willReturn(true);
        $directory->method('isWritable')->willReturn(false);
        $this->filesystem->method('getDirectoryWrite')->willReturn($directory);

        $fileDownloader = $this->getSubject();

        $this->expectException(LocalizedException::class);
        $fileDownloader->download(self::SOME_URL, self::SOME_DESTINATION);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function testFileDoesNotExist(): void
    {
        $directory = $this->createMock(WriteInterface::class);
        $directory->method('create')->willReturn(true);
        $directory->method('isWritable')->willReturn(true);
        $directory->method('writeFile')->willThrowException(
            new FileSystemException(__('An exception'))
        );
        $this->filesystem->method('getDirectoryWrite')->willReturn($directory);

        $readInterface = $this->createMock(ReadInterface::class);
        $this->readFactory->method('create')->willReturn($readInterface);

        $fileDownloader = $this->getSubject();

        $this->expectExceptionCode(5004);
        $fileDownloader->download(self::SOME_URL, self::SOME_DESTINATION);
    }

    public function testUrlIsNotHttps(): void
    {
        $fileDownloader = $this->getSubject();
        $this->expectException(LocalizedException::class);
        $fileDownloader->download(self::NON_HTTPS_URL, self::SOME_DESTINATION);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function testDownload(): void
    {
        $bytesWritten = 10;

        $directory = $this->createMock(WriteInterface::class);
        $directory->method('create')->willReturn(true);
        $directory->method('isWritable')->willReturn(true);
        $directory->method('writeFile')->willReturn($bytesWritten);
        $this->filesystem->method('getDirectoryWrite')->willReturn($directory);

        $readInterface = $this->createMock(ReadInterface::class);
        $this->readFactory->method('create')->willReturn($readInterface);

        $fileDownloader = $this->getSubject();
        $written = $fileDownloader->download(self::SOME_URL, self::SOME_DESTINATION);

        $this->assertEquals($bytesWritten, $written);
    }

    public function testGetRemoteFileContentInvalidUrl(): void
    {
        $subject = $this->getSubject();

        $this->expectExceptionCode(ErrorCodesDirectory::INVALID_URL);
        $subject->getRemoteFileContent(self::INVALID_URL);
    }

    public function testGetRemoteFileContent(): void
    {
        $subject = $this->getSubject();

        $reader = $this->createMock(ReadInterface::class);
        $reader->expects($this->once())->method('readAll')->willReturn('string');
        $this->readFactory->expects($this->once())->method('create')->willReturn($reader);

        $subject->getRemoteFileContent(self::SOME_URL);
    }

    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->readFactory = $this->createMock(ReadFactory::class);
        $this->file = $this->createMock(File::class);
        $this->fileDriver = $this->createMock(FileDriver::class);
    }

    private function getSubject(): FileDownloader
    {
        return new FileDownloader(
            $this->filesystem,
            $this->readFactory,
            $this->file,
            $this->fileDriver
        );
    }
}
