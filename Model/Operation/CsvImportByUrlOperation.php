<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Operation;

use Inriver\Adapter\Api\Data\ImportInterface;
use Inriver\Adapter\Api\Data\ProductsImportRequestInterface;
use Inriver\Adapter\Api\ProductsImportInterface;
use Inriver\Adapter\Helper\ErrorCodesDirectory;
use Inriver\Adapter\Helper\FileDownloader;
use Inriver\Adapter\Helper\FileEncoding;
use Inriver\Adapter\Model\Data\ImportFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Store\Api\WebsiteRepositoryInterface;

use function __;
use function date;
use function ltrim;

class CsvImportByUrlOperation implements ProductsImportInterface
{
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    /** @var string */
    protected $sourceUrl;

    /** @var \Inriver\Adapter\Model\Data\ImportFactory */
    protected $importFactory;

    /** @var \Inriver\Adapter\Helper\FileDownloader */
    protected $downloader;

    /** @var \Magento\Framework\Filesystem */
    protected $filesystem;

    /** @var \Inriver\Adapter\Helper\FileEncoding */
    protected $fileEncoding;

    /** @var \Magento\Store\Api\WebsiteRepositoryInterface  */
    private $websiteRepository;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Inriver\Adapter\Model\Data\ImportFactory $importFactory
     * @param \Inriver\Adapter\Helper\FileDownloader $downloader
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Inriver\Adapter\Helper\FileEncoding $fileEncoding
     * @param \Magento\Store\Api\WebsiteRepositoryInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory,
        FileDownloader $downloader,
        Filesystem $filesystem,
        FileEncoding $fileEncoding,
        WebsiteRepositoryInterface  $websiteRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->importFactory = $importFactory;
        $this->downloader = $downloader;
        $this->filesystem = $filesystem;
        $this->fileEncoding = $fileEncoding;
        $this->websiteRepository = $websiteRepository;
    }

    /**
     * Create a new CSV import from url
     *
     * @param \Inriver\Adapter\Api\Data\ProductsImportRequestInterface $import
     *
     * @return \Inriver\Adapter\Api\Data\OperationResultInterface[]
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function post(ProductsImportRequestInterface $import): array
    {
        $this->sourceUrl = $import->getUrl();

        $filename = $this->getCsvFile();
        $output = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        $fullPath = $output->getAbsolutePath($filename);
        $this->fileEncoding->removeUtf8Bom($fullPath);
        $managedWebsiteId = $this->getManagedWebsiteIds($import->getManagedWebsites());
        return $this->startImport($fullPath, $managedWebsiteId);
    }

    /**
     * Get CSV file and save to the destination path
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function getCsvFile(): string
    {

        $destination = $this->getTargetDirectory() . '/import-' . date('Ymdhis') . '.csv';
        $bytesWritten = $this->downloader->download($this->sourceUrl, $destination);

        if ($bytesWritten === 0) {
            throw new LocalizedException(
                __('Source CSV file is empty'),
                null,
                ErrorCodesDirectory::SOURCE_CSV_FILE_EMPTY
            );
        }

        return $destination;
    }

    /**
     * Get target directory
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getTargetDirectory(): string
    {
        $importPath = $this->scopeConfig->getValue(ImportInterface::XML_INRIVER_IMPORT_PATH_CSV);

        if ($importPath === null) {
            throw new LocalizedException(
                __('Inriver import path configuration is not set'),
                null,
                ErrorCodesDirectory::INRIVER_IMPORT_PATH_NOT_SET
            );
        }

        return ltrim($importPath, '/');
    }

    /**
     * @param string $path
     *
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function startImport(string $path, string $managedWebsiteIds): array
    {
        $import = $this->importFactory->create();
        $import->setManagedWebsites($managedWebsiteIds);
        $import->execute($path);

        return $import->getErrorsAsArray();
    }

    /**
     *
     */
    private function getManagedWebsiteIds(?array $managedWebsites): string
    {
        if($managedWebsites !== null) {
            $ids = array();
            foreach ($managedWebsites as $code) {
                $ids[] = $this->websiteRepository->get($code)->getId();
            }

            return implode(',', $ids);
        }

        return  '';
    }
}
