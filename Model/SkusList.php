<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model;

use Inriver\Adapter\Api\SkusListInterface;
use Magento\Framework\App\ResourceConnection;

class SkusList implements SkusListInterface
{

    /** @var \Magento\Framework\App\ResourceConnectionResourceConnection */
    private $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Return all skus with their product types
     * Native API call for product list is too heavy
     *
     * @return string[]
     */
    public function getAllSkusWithType(): array
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName('catalog_product_entity');
        $query = $connection->select()->from(
            $table,
            [
                'sku',
                'type_id',
            ]
        );

        return $connection->fetchAssoc($query);
    }
}
