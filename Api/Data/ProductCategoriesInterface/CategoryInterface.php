<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data\ProductCategoriesInterface;

/**
 * Interface CategoryInterface
 */
interface CategoryInterface
{
    /**
     * Get categoryUniqueId
     *
     * @return string
     */
    public function getCategoryUniqueId(): string;

    /**
     * Set categoryUniqueId
     *
     * @param string $categoryUniqueId
     *
     * @return \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface
     */
    public function setCategoryUniqueId(string $categoryUniqueId): CategoryInterface;

    /**
     * Get position
     *
     * @return int|null
     */
    public function getPosition(): ?int;

    /**
     * Set position
     *
     * @param int|null $position
     *
     * @return \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface
     */
    public function setPosition(?int $position = null): CategoryInterface;
}
