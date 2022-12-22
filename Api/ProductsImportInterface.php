<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

use Inriver\Adapter\Api\Data\ProductsImportRequestInterface;

/**
 * Interface ProductsImportInterface
 */
interface ProductsImportInterface
{
    /**
     * Create a new CSV import from url
     *
     * @param \Inriver\Adapter\Api\Data\ProductsImportRequestInterface $import
     *
     * @return \Inriver\Adapter\Api\Data\OperationResultInterface[]
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function post(ProductsImportRequestInterface $import): array;
}
