<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 * This file applies minor modifications to native Magento code.
 * It should be kept in sync with the latest Magento version.
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Import;

use Inriver\Adapter\Helper\Import as InriverImportHelper;
use Magento\CatalogImportExport\Model\Import\Product as ProductImport;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface as ValidatorInterface;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;

use function array_keys;
use function count;

class Product extends ProductImport
{
    private const IS_DEBUG_MODE = 'inriver/import/debug';

    /** @var bool */
    private $debug = false;

    /** @var bool */
    private $isImportTypeDisable = false;

    /** @var bool */
    private $isImportTypeConfirm = false;

    /**
     * Add error with corresponding current data source row number.
     *
     * @param string $errorCode
     * @param int $errorRowNum
     * @param null $colName
     * @param null $errorMessage
     * @param string $errorLevel
     * @param null $errorDescription
     *
     * @return \Magento\CatalogImportExport\Model\Import\Product
     */
    public function addRowError(
        $errorCode,
        $errorRowNum,
        $colName = null,
        $errorMessage = null,
        $errorLevel = ProcessingError::ERROR_LEVEL_CRITICAL,
        $errorDescription = null
    ) {
        if ($errorCode === ValidatorInterface::ERROR_MEDIA_URL_NOT_ACCESSIBLE && $this->isDebug()) {
            return $this;
        }

        return parent::addRowError($errorCode, $errorRowNum, $colName, $errorMessage, $errorLevel, $errorDescription);
    }

    /**
     * @return bool
     */
    public function isImportTypeDisable(): bool
    {
        return $this->isImportTypeDisable;
    }

    /**
     * @return bool
     */
    public function isImportTypeConfirm(): bool
    {
        return $this->isImportTypeConfirm;
    }

    /**
     * @param bool $isImportTypeDisable
     */
    public function setIsImportTypeDisable(bool $isImportTypeDisable): void
    {
        $this->isImportTypeDisable = $isImportTypeDisable;
        $this->isImportTypeConfirm = true;
    }

    public function setIsInriverImportForProductTypeModel(): void
    {
        foreach ($this->_productTypeModels as $model) {
            $model->{InriverImportHelper::IS_INRIVER_IMPORT} = true;
        }
    }

    /**
     * Overwrite Magento
     * Overwrite to delete website when import type is append
     *
     * @param string[] $websiteData
     *
     * @return \Inriver\Adapter\Model\Import\Product
     */
    protected function _saveProductWebsites(array $websiteData)
    {
        if ($this->getBehavior() === Import::BEHAVIOR_APPEND) {
            static $tableName = null;

            if (!$tableName) {
                $tableName = $this->_resourceFactory->create()->getProductWebsiteTable();
            }

            if ($websiteData) {
                $delProductId = [];

                foreach (array_keys($websiteData) as $sku) {
                    $delProductId[] = $this->skuProcessor->getNewSku($sku)['entity_id'];
                }
                $whereClause = $this->_connection->quoteInto('product_id IN (?)', $delProductId);

                $parameters = $this->getParameters();

                if (isset($parameters['managed_websites']) && $parameters['managed_websites'] !== '') {
                    $whereClause .= ' AND ' . $this->_connection->quoteInto('website_id IN (?)', $parameters['managed_websites']);
                }
                if (count($delProductId) > 0) {
                    $this->_connection->delete($tableName, $whereClause);
                }
            }
        }

        return parent::_saveProductWebsites($websiteData);
    }

    /**
     * Check if Inriver import debug mode is enabled
     *
     * @return bool
     */
    private function isDebug(): bool
    {
        $debug = $this->scopeConfig->getValue(self::IS_DEBUG_MODE);

        if ($debug !== null) {
            return (bool)$debug;
        }

        return $this->debug;
    }
}
