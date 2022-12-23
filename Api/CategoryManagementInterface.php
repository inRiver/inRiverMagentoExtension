<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

/**
 * Interface CategoryManagementInterface
 */
interface CategoryManagementInterface
{
    /**
     * Move category
     *
     * @param int $categoryId
     * @param int $parentId
     * @param int $afterId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function move($categoryId, $parentId, $afterId = null): bool;

    /**
     * @param \Inriver\Adapter\Api\Data\CategoriesImportInterface $categoriesImport
     *
     * @return bool
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function completed(\Inriver\Adapter\Api\Data\CategoriesImportInterface $categoriesImport): bool;
}
