<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Plugin;

use Inriver\Adapter\Helper\Import as InriverImportHelper;
use Inriver\Adapter\Model\ImportExport\Import;
use Inriver\Adapter\Model\ResourceModel\Import\Data;
use Magento\ImportExport\Model\ResourceModel\Import\Data as MagentoImportData;

/**
 * Class UseInriverImportDataPlugin
 * Switch the database where we save import data
 */
class UseInriverImportDataPlugin
{
    /** @var \Inriver\Adapter\Model\ResourceModel\Import\Data */
    private $importData;

    /**
     * @param \Inriver\Adapter\Model\ResourceModel\Import\Data $importData
     */
    public function __construct(
        Data $importData
    ) {
        $this->importData = $importData;
    }

    /**
     * Plugin for GetDataSourceModel
     *
     * @param \Inriver\Adapter\Model\ImportExport\Import $subject
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data $result
     *
     * @return \Magento\ImportExport\Model\ResourceModel\Import\Data
     */
    public function afterGetDataSourceModel(Import $subject, MagentoImportData $result): MagentoImportData
    {
        if ($subject->getData(InriverImportHelper::IS_INRIVER_IMPORT) === true) {
            return $this->importData;
        }

        return $result;
    }
}
