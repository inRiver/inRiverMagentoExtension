<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

/**
 * Interface ProductImagesInterface
 */
interface ProductImagesInterface
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
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface
     */
    public function setSku(string $sku): ProductImagesInterface;

    /**
     * Get images
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface[]
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getImages(): array;

    /**
     * Set images
     *
     * @param \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface[] $images
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function setImages(array $images): ProductImagesInterface;
}
