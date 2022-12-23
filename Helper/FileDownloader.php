<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\Framework\Filesystem\Io\File;
use Throwable;

use function __;
use function array_key_exists;
use function preg_match;
use function str_replace;

/**
 * Class FileDownloader
 * File downloader helper to create the csv file when receiving an import
 */
class FileDownloader
{
    /** @var \Magento\Framework\Filesystem\Directory\WriteInterface */
    protected $directory;

    /** @var \Magento\Framework\Filesystem\File\ReadFactory */
    protected $readFactory;

    /** @var \Magento\Framework\Filesystem\Io\File */
    protected $file;

    /** @var FileDriver */
    protected $fileDriver;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\File\ReadFactory $readFactory
     * @param \Magento\Framework\Filesystem\Io\File $file
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        ReadFactory $readFactory,
        File $file,
        FileDriver $fileDriver
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->readFactory = $readFactory;
        $this->file = $file;
        $this->fileDriver = $fileDriver;
    }

    /**
     * Download a file from an url
     *
     * @param $url
     * @param $destination
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function download(string $url, string $destination): int
    {
        if (!preg_match('/\bhttps:\/\//i', $url, $matches)) {
            throw new LocalizedException(
                __('Source URL should start with https')
            );
        }

        $normalizedUrl = str_replace($matches[0], '', $url);
        $driver = DriverPool::HTTPS;

        if (!$this->createDestinationFolder($destination)) {
            throw new LocalizedException(__('Cannot create target directory.'));
        }

        if (!$this->directory->isWritable($this->fileDriver->getParentDirectory($destination))) {
            throw new LocalizedException(
                __('Target directory must be writable.')
            );
        }

        try {
            $written = $this->directory->writeFile(
                $destination,
                $this->readFactory->create($normalizedUrl, $driver)->readAll()
            );
        } catch (Throwable $e) {
            throw new LocalizedException(
                __('Cannot download file from %1: %2', $url, $e->getMessage()),
                null,
                ErrorCodesDirectory::CANNOT_DOWNLOAD_CSV_FILE
            );
        }

        return $written;
    }

    /**
     * @param $url
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRemoteFileContent(string $url): string
    {
        if (!preg_match('/\bhttp(?P<isHttps>s)?:\/\//i', $url, $matches)) {
            throw new LocalizedException(
                __('Source URL should start with http or https'),
                null,
                ErrorCodesDirectory::INVALID_URL
            );
        }

        $driver = array_key_exists('isHttps', $matches) ? DriverPool::HTTPS : DriverPool::HTTP;
        $normalizedUrl = str_replace($matches[0], '', $url);

        return $this->readFactory->create($normalizedUrl, $driver)->readAll();
    }

    /**
     * Create destination folder
     *
     * @param string $destination
     *
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function createDestinationFolder(string $destination): bool
    {
        $this->file->setAllowCreateFolders(true);

        return $this->directory->create($this->fileDriver->getParentDirectory($destination));
    }
}
