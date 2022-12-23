<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Operation;

use Inriver\Adapter\Helper\FileDownloader;
use Inriver\Adapter\Helper\FileEncoding;
use Inriver\Adapter\Model\Data\ImportProductRelationsFactory;
use Inriver\Adapter\Model\Data\ImportFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;

class CsvProductRelationsImportByUrlOperation extends CsvImportByUrlOperation
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
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory,
        FileDownloader $downloader,
        Filesystem $filesystem,
        FileEncoding $fileEncoding,
        ImportProductRelationsFactory $importProductRelationsFactory
    ) {
        parent::__construct(
            $this->scopeConfig = $scopeConfig,
            $this->importFactory = $importFactory,
            $this->downloader = $downloader,
            $this->filesystem = $filesystem,
            $this->fileEncoding = $fileEncoding
        );

        $this->importProductRelationsFactory = $importProductRelationsFactory;
    }

    /**
     * @param string $path
     *
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function startImport(string $path): array
    {
        /** @var \Inriver\Adapter\Model\Data\ImportProductRelations $import */
        $import = $this->importProductRelationsFactory->create();
        $import->execute($path);

        return $import->getErrorsAsArray();
    }
}
