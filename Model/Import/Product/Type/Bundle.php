<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Import\Product\Type;

use Magento\BundleImportExport\Model\Import\Product\Type\Bundle as parentBundle;

use function array_keys;

class Bundle extends parentBundle
{
    /**
     * @param string[] $rowData
     * @param int $entityId
     *
     * @return string[]|void
     */
    protected function parseSelections($rowData, $entityId)
    {
        parent::parseSelections($rowData, $entityId);

        if (isset($this->_cachedOptions[$entityId])) {
            $this->deleteOptionsById((int) $entityId);
        }
    }

    /**
     * @param int $productId
     *
     * @return \Inriver\Adapter\Model\Import\Product\Type\Bundle
     */
    private function deleteOptionsById(int $productId): Bundle
    {
        $optionTable = $this->_resource->getTableName('catalog_product_bundle_option');
        $optionValueTable = $this->_resource->getTableName('catalog_product_bundle_option_value');
        $selectionTable = $this->_resource->getTableName('catalog_product_bundle_selection');
        $valuesIds = $this->connection->fetchAssoc(
            $this->connection->select()->from(
                ['bov' => $optionValueTable],
                ['value_id']
            )->joinLeft(
                ['bo' => $optionTable],
                'bo.option_id = bov.option_id',
                ['option_id']
            )->where(
                'parent_id = ?',
                $productId
            )
        );
        $this->connection->delete(
            $optionValueTable,
            $this->connection->quoteInto('value_id IN (?)', array_keys($valuesIds))
        );
        $this->connection->delete(
            $optionTable,
            $this->connection->quoteInto('parent_id = ?', $productId)
        );
        $this->connection->delete(
            $selectionTable,
            $this->connection->quoteInto('parent_product_id = ?', $productId)
        );

        return $this;
    }
}
