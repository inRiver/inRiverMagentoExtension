<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Config\Source;

use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Source\Import\AbstractBehavior;

use function __;

/**
 * Class Behavior Behavior
 */
class Behavior extends AbstractBehavior
{
    /**
     * Get array of possible values
     *
     * @return string[]
     *
     * @abstract
     */
    public function toArray(): array
    {
        return [
            Import::BEHAVIOR_APPEND => __('Append'),
            Import::BEHAVIOR_ADD_UPDATE => __('Add/Update'),
            Import::BEHAVIOR_REPLACE => __('Replace'),
            Import::BEHAVIOR_DELETE => __('Delete'),
        ];
    }

    /**
     * Get current behaviour group code
     *
     * @return string
     *
     * @abstract
     */
    public function getCode(): string
    {
        return 'inriver';
    }

    /**
     * Get array of notes for possible values
     *
     * @param string $entityCode
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    //phpcs:ignore
    public function getNotes($entityCode): array
    {
        $messages = ['catalog_product' => [
            Import::BEHAVIOR_APPEND => __(
                'Add new products and new attribute values. DOES NOT disassociate product_website relations, '
                . 'product categories relations, product links (related/up/cross sells) relations and tier prices'
            ),
            Import::BEHAVIOR_ADD_UPDATE => __(
                'New product data is added to the existing product data for the existing entries in the database. '
                . 'All fields except sku can be updated.'
            ),
            Import::BEHAVIOR_REPLACE => __(
                'The existing product data is replaced with new data. <b>Exercise caution when replacing data '
                . 'because the existing product data will be completely cleared and all references '
                . 'in the system will be lost.</b>'
            ),
            Import::BEHAVIOR_DELETE => __(
                'Any entities in the import data that already exist in the database are deleted from the database.'
            ),
        ]];

        return $messages[$entityCode] ?? [];
    }
}
