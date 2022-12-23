<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

use Inriver\Adapter\Api\Data\ProductImagesInterface;

/**
 * Interface ImagesInterface
 */
interface ImagesInterface
{
    /**
     * Import product images
     *
     * @param \Inriver\Adapter\Api\Data\ProductImagesInterface $productImage
     *
     * @return string[]
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function post(ProductImagesInterface $productImage): array;
}
