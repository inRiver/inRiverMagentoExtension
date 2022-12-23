<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Inriver\Adapter\Api\Data\CategoriesImportInterface;

class CategoriesImport implements CategoriesImportInterface
{
    protected array $newCategoryIds;

    public function getNewCategoryIds(): array
    {
        return $this->newCategoryIds;
    }

    public function setNewCategoryIds(array $newCategoryIds): CategoriesImportInterface
    {
        $this->newCategoryIds = $newCategoryIds;

        return $this;
    }
}
