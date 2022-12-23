<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data\ProductImagesInterface;

/**
 * Interface ImageInterface
 */
interface ImageInterface
{
    /**
     * Get url
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Set url
     *
     * @param string $url
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface
     */
    public function setUrl(string $url): ImageInterface;

    /**
     * Get mime type
     *
     * @return string
     */
    public function getMimeType(): string;

    /**
     * Set mime type
     *
     * @param string $mimeType
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface
     */
    public function setMimeType(string $mimeType): ImageInterface;

    /**
     * Get image id
     *
     * @return string
     */
    public function getImageId(): string;

    /**
     * Set image id
     *
     * @param string $imageId
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface
     */
    public function setImageId(string $imageId): ImageInterface;

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename(): string;

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface
     */
    public function setFilename(string $filename): ImageInterface;

    /**
     * Get attributes
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface\AttributesInterface[]
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getAttributes(): array;

    /**
     * Set attributes
     *
     * @param \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface\AttributesInterface[] $attributes
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function setAttributes(array $attributes): ImageInterface;
}
