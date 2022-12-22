<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

/**
 * Interface ProductCategoriesInterface
 */
interface ProductCategoriesInterface
{
    /**
     * Import product categories
     *
     * @param \Inriver\Adapter\Api\Data\ProductCategoriesInterface $productCategories
     *
     * @return array
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function post(\Inriver\Adapter\Api\Data\ProductCategoriesInterface $productCategories): array;
}
