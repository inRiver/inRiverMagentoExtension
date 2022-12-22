<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 * This file applies minor modifications to native Magento code.
 * It should be kept in sync with the latest Magento version.
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Import\Product\Type;

use Inriver\Adapter\Helper\Import as InriverImportHelper;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as SetCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\ImportExport\Model\ResourceModel\Helper;

use function array_diff;
use function count;

class Configurable extends \Magento\ConfigurableImportExport\Model\Import\Product\Type\Configurable
{
    /** @var \Inriver\Adapter\Helper\Import */
    private $inriverImportHelper;

    /** @var array */
    private $productSuperAttrIds = [];

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attrSetColFac
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $prodAttrColFac
     * @param \Magento\Framework\App\ResourceConnection $resource* @param array $params
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypesConfig
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $_productColFac
     * @param \Inriver\Adapter\Helper\Import $inriverImportHelper
     * @param \Magento\Framework\EntityManager\MetadataPool|null $metadataPool
     */
    public function __construct(
        SetCollectionFactory $attrSetColFac,
        AttributeCollectionFactory $prodAttrColFac,
        ResourceConnection $resource,
        array $params,
        ConfigInterface $productTypesConfig,
        Helper $resourceHelper,
        ProductCollectionFactory $_productColFac,
        InriverImportHelper $inriverImportHelper,
        ?MetadataPool $metadataPool = null
    ) {
        parent::__construct(
            $attrSetColFac,
            $prodAttrColFac,
            $resource,
            $params,
            $productTypesConfig,
            $resourceHelper,
            $_productColFac,
            $metadataPool
        );

        $this->inriverImportHelper = $inriverImportHelper;
    }

    /**
     * Overwrite Magento
     * Override Parse variations to decode values
     *
     * @param array $rowData
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _parseVariations($rowData): array
    {
        $additionalRows = parent::_parseVariations($rowData);

        // InRiver custom code start
        foreach ($additionalRows as $key => $option) {
            $additionalRows[$key]['_super_attribute_option'] =
                $this->inriverImportHelper->decodeImportAttributeValue($option['_super_attribute_option']);
        }

        $productId = $this->_productData[$this->getProductEntityLinkField()];

        if ($productId !== null) {
            if (!isset($this->productSuperAttrIds[$productId])) {
                $this->productSuperAttrIds[$productId] = [];
            }

            foreach ($additionalRows as $data) {
                $this->productSuperAttrIds[$productId][] =
                    $this->_superAttributes[$data['_super_attribute_code']]['id'];
            }
        }

        // InRiver custom code end

        return $additionalRows;
    }

    /**
     *  Overwrite Magento
     *  Take care of the delta during import
     *
     * @return \Magento\ConfigurableImportExport\Model\Import\Product\Type\Configurable
     * @throws \Zend_Db_Exception
     */
    protected function _insertData(): \Magento\ConfigurableImportExport\Model\Import\Product\Type\Configurable
    {
        // InRiver custom code start
        $this->deleteProductAttributeSuperLink();
        $this->deleteProductSuperLink();
        $this->deleteProductRelations();
        $this->productSuperAttrIds = [];
        // InRiver custom code end

        return parent::_insertData();
    }

    /**
     * Delete data in super attribute table that isn't present in the import
     *
     * @return void
     */
    private function deleteProductAttributeSuperLink(): void
    {
        //Delete relation not used anymore
        if (count($this->productSuperAttrIds) > 0) {
            $mainTable = $this->_resource->getTableName('catalog_product_super_attribute');

            foreach ($this->productSuperAttrIds as $productId => $superAttIds) {
                if (!$superAttIds) {
                    continue;
                }

                $select = $this->connection->select()
                    ->from($mainTable, ['attribute_id'])
                    ->where('product_id = ?', $productId);
                $relationExist = $this->connection->fetchCol($select);
                $deleteRelation = array_diff($relationExist, $superAttIds);

                if (count($deleteRelation) > 0) {
                    $quoted = $this->connection->quoteInto('= ?', $productId);
                    $quotedChildren = $this->connection->quoteInto('IN (?)', $deleteRelation);
                    $this->connection->delete(
                        $mainTable,
                        'product_id ' . $quoted . ' AND attribute_id ' . $quotedChildren
                    );
                }
            }
        }
    }

    /**
     * Delete data in super link table that isn't present in the import
     *
     * @return void
     */
    private function deleteProductSuperLink(): void
    {
        if (isset($this->_superAttributesData['super_link'])) {
            $superLinks = [];

            foreach ($this->_superAttributesData['super_link'] as $superLink) {
                $superLinks[$superLink['parent_id']][] = $superLink['product_id'];
            }

            $linkTable = $this->_resource->getTableName('catalog_product_super_link');

            foreach ($superLinks as $parentId => $children) {
                $select = $this->connection->select()
                    ->from($linkTable, ['product_id'])
                    ->where('parent_id = ?', $parentId);
                $relationExist = $this->connection->fetchCol($select);
                $deleteRelation = array_diff($relationExist, $children);

                if (count($deleteRelation) > 0) {
                    $quoted = $this->connection->quoteInto('= ?', $parentId);
                    $quotedChildren = $this->connection->quoteInto('IN (?)', $deleteRelation);
                    $this->connection->delete(
                        $linkTable,
                        'parent_id ' . $quoted . ' AND product_id ' . $quotedChildren
                    );
                }
            }
        }
    }

    /**
     * Delete data in relation table that isn't present in the import
     *
     * @return void
     */
    private function deleteProductRelations(): void
    {
        if (isset($this->_superAttributesData['relation'])) {
            $relations = [];

            foreach ($this->_superAttributesData['relation'] as $relation) {
                $relations[$relation['parent_id']][] = $relation['child_id'];
            }

            $relationTable = $this->_resource->getTableName('catalog_product_relation');

            foreach ($relations as $parentId => $children) {
                $select = $this->connection->select()
                    ->from($relationTable, ['child_id'])
                    ->where('parent_id = ?', $parentId);
                $relationExist = $this->connection->fetchCol($select);
                $deleteRelation = array_diff($relationExist, $children);

                if (count($deleteRelation) > 0) {
                    $quoted = $this->connection->quoteInto('= ?', $parentId);
                    $quotedChildren = $this->connection->quoteInto('IN (?)', $deleteRelation);
                    $this->connection->delete(
                        $relationTable,
                        'parent_id ' . $quoted . ' AND child_id ' . $quotedChildren
                    );
                }
            }
        }
    }
}
