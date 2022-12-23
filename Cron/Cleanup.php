<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

namespace Inriver\Adapter\Cron;

use Inriver\Adapter\Logger\Logger;
use Inriver\Adapter\Model\Data\Import;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;

use function array_key_exists;
use function time;

class Cleanup
{
    /** @var \Magento\Framework\Filesystem\DirectoryList */
    private $directoryList;

    /** @var \Magento\Framework\Filesystem\Driver\File */
    private $driverFile;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    /** @var \Inriver\Adapter\Logger\Logger */
    private $logger;

    /**
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Driver\File $driverFile
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Inriver\Adapter\Logger\Logger $logger
     */
    public function __construct(DirectoryList        $directoryList,
                                File                 $driverFile,
                                ScopeConfigInterface $scopeConfig,
                                Logger               $logger)
    {
        $this->directoryList = $directoryList;
        $this->driverFile = $driverFile;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $DS = DIRECTORY_SEPARATOR;
        $rootFolder = $this->directoryList->getPath('var') .
            $DS . $this->scopeConfig->getValue(Import::XML_INRIVER_IMPORT_PATH_CSV) .
            $DS . 'archives';
        $this->cleanFolder($rootFolder . $DS . 'success');
        $this->cleanFolder($rootFolder . $DS . 'error');

        return $this;

    }

    /**
     * @param string $folderPath
     */
    public function cleanFolder(string $folderPath)
    {
        $now = time();
        $daysConfig = (int)$this->scopeConfig->getValue(Import::XML_INRIVER_IMPORT_CLEANUP_DAYS);
        $daysInTimestamp = 60 * 60 * 24 * $daysConfig;

        try {
            $paths = $this->driverFile->readDirectory($folderPath);
            foreach ($paths as $filepath) {
                try {
                    $stats = $this->driverFile->stat($filepath);
                    if (array_key_exists('mtime', $stats) && $now - $stats['mtime'] >= $daysInTimestamp) {
                        $this->driverFile->deleteFile($filepath);
                    }
                } catch (\Exception $ex) {
                    $this->logger->addError("Cannot delete file $filepath during cleanup : " . $ex->getMessage());
                }
            }
        } catch (\Exception $ex) {
            $this->logger->addError("Cannot read directory $folderPath during cleanup : " . $ex->getMessage());
        }
    }
}

