<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

/**
 * Interface CategoriesImportInterface
 */
interface CategoriesImportInterface
{
    /**
     * Get new category ids
     *
     * @return int[]
     */
    public function getNewCategoryIds(): array;

    /**
     * Set new category ids
     *
     * @param int[] $newCategoryIds
     *
     * @return \Inriver\Adapter\Api\Data\CategoriesImportInterface
     */
    public function setNewCategoryIds(array $newCategoryIds): CategoriesImportInterface;
}
