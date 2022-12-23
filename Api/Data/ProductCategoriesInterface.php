<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

/**
 * Interface ProductCategoriesInterface
 */
interface ProductCategoriesInterface
{
    /**
     * Get sku
     *
     * @return string
     */
    public function getSku(): string;

    /**
     * Set sku
     *
     * @param string $sku
     *
     * @return \Inriver\Adapter\Api\Data\ProductCategoriesInterface
     */
    public function setSku(string $sku): ProductCategoriesInterface;

    /**
     * Get Categories
     *
     * @return \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface[]
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function getCategories(): array;

    /**
     * Set Categories
     *
     * @param \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface[] $categories
     *
     * @return \Inriver\Adapter\Api\Data\ProductCategoriesInterface
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function setCategories(array $categories): ProductCategoriesInterface;
}
