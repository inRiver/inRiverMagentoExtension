<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data\ProductCategories;

use Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface;

/**
 * Class Image
 * Product Category save class
 */
class Category implements CategoryInterface
{
    /** @var string */
    private $categoryUniqueId;

    /** @var int */
    private $position;

    /**
     * Get category unique id
     *
     * @return string
     */
    public function getCategoryUniqueId(): string
    {
        return $this->categoryUniqueId;
    }

    /**
     * Set category unique id
     *
     * @param string $categoryUniqueId
     *
     * @return \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface
     */
    public function setCategoryUniqueId(string $categoryUniqueId): CategoryInterface
    {
        $this->categoryUniqueId = $categoryUniqueId;

        return $this;
    }

    /**
     * Get position
     *
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * Set position
     *
     * @param int|null $position
     *
     * @return \Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface
     */
    public function setPosition(?int $position = null): CategoryInterface
    {
        $this->position = $position;

        return $this;
    }
}
