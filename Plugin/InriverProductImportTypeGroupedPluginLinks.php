<?php

/** @noinspection MessDetectorValidationInspection */

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Plugin;

use Magento\Catalog\Model\ResourceModel\Product\Link;
use Magento\Framework\App\ResourceConnection;
use Magento\GroupedImportExport\Model\Import\Product\Type\Grouped\Links;
use Magento\GroupedProduct\Model\ResourceModel\Product\Link as GroupLink;

/**
 * Class InriverProductImportTypeAbstractPlugin
 * Plugin that helps with dealing with the delta of grouped products
 */
class InriverProductImportTypeGroupedPluginLinks
{
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    /** @var \Magento\Catalog\Model\ResourceModel\Product\Link */
    private $productLink;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Link $productLink
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        Link $productLink,
        ResourceConnection $resource
    ) {
        $this->productLink = $productLink;
        $this->resource = $resource;
    }

    /**
     * Plugin for SaveLinksData
     *
     * Use to remove old link not present in the import
     *
     * @param \Magento\GroupedImportExport\Model\Import\Product\Type\Grouped\Links $subject
     * @param $result
     * @param string[] $linksData
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterSaveLinksData(Links $subject, $result, array $linksData): void
    {
        $connection = $this->resource->getConnection();
        $linkTable = $this->productLink->getMainTable();
        $relationTable = $this->productLink->getTable('catalog_product_relation');
        $dataByParentId = [];

        if ($linksData['product_ids']) {
            foreach ($linksData['relation'] as $productData) {
                $dataByParentId[$productData['parent_id']][] = $productData['child_id'];
            }

            foreach ($dataByParentId as $parentId => $children) {
                $connection->delete(
                    $linkTable,
                    $connection->quoteInto(
                        'product_id = ?',
                        $parentId
                    ) . $connection->quoteInto(
                        ' AND linked_product_id not in (?)',
                        $children
                    ) . $connection->quoteInto(
                        ' AND link_type_id = ?',
                        GroupLink::LINK_TYPE_GROUPED
                    )
                );
                $connection->delete(
                    $relationTable,
                    $connection->quoteInto(
                        'parent_id = ?',
                        $parentId
                    ) . $connection->quoteInto(
                        ' AND child_id not in (?)',
                        $children
                    )
                );
            }
        }
    }
}
