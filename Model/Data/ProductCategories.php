<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Inriver\Adapter\Api\Data\ProductCategoriesInterface;

class ProductCategories implements ProductCategoriesInterface
{
    /** @var string */
    private $sku;

    /** @var \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface[] */
    private $categories;

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * Set sku
     *
     * @param string $sku
     *
     * @return \Inriver\Adapter\Api\Data\ProductCategoriesInterface
     */
    public function setSku(string $sku): ProductCategoriesInterface
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get categories
     *
     * @return \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface[]
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Set categories
     *
     * @param \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface[] $categories
     *
     * @return \Inriver\Adapter\Api\Data\ProductCategoriesInterface
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function setCategories(array $categories): ProductCategoriesInterface
    {
        $this->categories = $categories;

        return $this;
    }
}
