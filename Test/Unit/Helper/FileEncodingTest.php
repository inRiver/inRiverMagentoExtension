<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Helper;

use Inriver\Adapter\Helper\FileEncoding;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use PHPUnit\Framework\TestCase;

use function copy;
use function file_exists;
use function unlink;

class FileEncodingTest extends TestCase
{
    public const UTF8_WITH_BOM_FILE = __DIR__ . '/../_files/utf8-bom.csv';
    public const UTF8_WITHOUT_BOM_FILE = __DIR__ . '/../_files/utf8-without-bom.csv';

    /** @var \Magento\Framework\Filesystem\Driver\File|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $fileDriver;

    public function testIsUtf8WithBomWithUtf8BomFile(): void
    {
        $fileEncoding = new FileEncoding($this->fileDriver);
        $this->assertTrue($fileEncoding->isUtf8WithBom(self::UTF8_WITH_BOM_FILE));
    }

    public function testIsUtf8WithBomWithUtf8WithoutBomFile(): void
    {
        $fileEncoding = new FileEncoding($this->fileDriver);
        $this->assertFalse($fileEncoding->isUtf8WithBom(self::UTF8_WITHOUT_BOM_FILE));
    }

    public function testRemoveUtf8Bom(): void
    {
        $fileEncoding = new FileEncoding($this->fileDriver);

        $testFile = self::UTF8_WITH_BOM_FILE . '_TEST';

        if (file_exists($testFile)) {
            unlink($testFile);
        }

        copy(self::UTF8_WITH_BOM_FILE, $testFile);

        $this->assertTrue($fileEncoding->isUtf8WithBom($testFile));
        $fileEncoding->removeUtf8Bom($testFile);
        $this->assertFalse($fileEncoding->isUtf8WithBom($testFile));

        unlink($testFile);
    }

    public function testRemoveUtf8BomWithoutBom(): void
    {
        $fileEncoding = new FileEncoding($this->fileDriver);

        $testFile = self::UTF8_WITHOUT_BOM_FILE . '_TEST';

        if (file_exists($testFile)) {
            unlink($testFile);
        }

        copy(self::UTF8_WITHOUT_BOM_FILE, $testFile);

        $this->assertFalse($fileEncoding->removeUtf8Bom($testFile));

        unlink($testFile);
    }

    protected function setUp(): void
    {
        $this->fileDriver = $this->createMock(FileDriver::class);

        $this->fileDriver
            ->method('fileOpen')
            ->willReturnCallback(
                function ($filePath, $mode) {
                    return fopen($filePath, $mode);
                }
            );

        $this->fileDriver
            ->method('fileClose')
            ->willReturnCallback(
                function ($file) {
                    return fclose($file);
                }
            );

        $this->fileDriver
            ->method('rename')
            ->willReturnCallback(
                function ($oldName, $newName) {
                    return rename($oldName, $newName);
                }
            );
    }
}
