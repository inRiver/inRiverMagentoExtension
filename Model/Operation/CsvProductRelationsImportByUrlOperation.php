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
use Inriver\Adapter\Helper\Import as InriverImportHelper;
use Inriver\Adapter\Model\Data\ImportFactory;
use Inriver\Adapter\Model\Data\ImportProductRelationsFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Magento\Store\Api\WebsiteRepositoryInterface;

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
     *
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
     * @param string $managedWebsiteIds
     *
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function startImport(string $path, string $managedWebsiteIds): array
    {
        /** @var \Inriver\Adapter\Model\Data\ImportProductRelations $import */
        $import = $this->importProductRelationsFactory->create();
        $import->setManagedWebsites($managedWebsiteIds);
        $import->setInriverImportType(InriverImportHelper::INRIVER_IMPORT_TYPE_RELATIONS);
        $import->execute($path);
        return $import->getErrorsAsArray();
    }
}
