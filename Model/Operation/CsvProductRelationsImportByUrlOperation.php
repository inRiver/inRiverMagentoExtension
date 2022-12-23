<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Operation;

use Inriver\Adapter\Api\ProductsImportRelationsInterface;
use Inriver\Adapter\Helper\FileDownloader;
use Inriver\Adapter\Helper\FileEncoding;
use Inriver\Adapter\Model\Data\ImportFactory;
use Inriver\Adapter\Model\Data\ImportProductRelationsFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Magento\Store\Api\WebsiteRepositoryInterface;

/**
 * Class CsvProductRelationsImportByUrlOperation Csv Product Relations Import by url Operation
 */
class CsvProductRelationsImportByUrlOperation extends CsvImportByUrlOperation implements ProductsImportRelationsInterface
{
    /** @var \Inriver\Adapter\Model\Data\ImportFactory */
    protected $importProductRelationsFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Inriver\Adapter\Model\Data\ImportFactory $importFactory
     * @param \Inriver\Adapter\Helper\FileDownloader $downloader
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Inriver\Adapter\Helper\FileEncoding $fileEncoding
     * @param \Inriver\Adapter\Model\Data\ImportProductRelationsFactory $importProductRelationsFactory
     * @param \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory,
        FileDownloader $downloader,
        Filesystem $filesystem,
        FileEncoding $fileEncoding,
        ImportProductRelationsFactory $importProductRelationsFactory,
        WebsiteRepositoryInterface  $websiteRepository
    ) {
        parent::__construct(
            $this->scopeConfig = $scopeConfig,
            $this->importFactory = $importFactory,
            $this->downloader = $downloader,
            $this->filesystem = $filesystem,
            $this->fileEncoding = $fileEncoding,
            $scopeConfig,
            $importFactory,
            $downloader,
            $filesystem,
            $fileEncoding,
            $websiteRepository
        );

        $this->importProductRelationsFactory = $importProductRelationsFactory;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    protected function startImport(string $path): array
    {
        $import = $this->importProductRelationsFactory->create();
        $import->execute($path);

        return $import->getErrorsAsArray();
    }
}
