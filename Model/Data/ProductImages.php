<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Inriver\Adapter\Api\Data\ProductImagesInterface;

class ProductImages implements ProductImagesInterface
{
    /** @var string */
    private $sku;

    /** @var \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface[] */
    private $images;

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
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface
     */
    public function setSku(string $sku): ProductImagesInterface
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get images
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface[]
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * Set images
     *
     * @param \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface[] $images
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function setImages(array $images): ProductImagesInterface
    {
        $this->images = $images;

        return $this;
    }
}
