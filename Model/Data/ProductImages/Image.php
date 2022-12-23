<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data\ProductImages;

use Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface;

/**
 * Class Image
 * Save Image class
 */
class Image implements ImageInterface
{
    /** @var string */
    private $url;

    /** @var string */
    private $mimeType;

    /** @var string */
    private $imageId;

    /** @var string */
    private $filename;

    /** @var \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface\AttributesInterface[] */
    private $attributes;

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface
     */
    public function setUrl(string $url): ImageInterface
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get mime type
     *
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * Set mime type
     *
     * @param string $mimeType
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface
     */
    public function setMimeType(string $mimeType): ImageInterface
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get image id
     *
     * @return string
     */
    public function getImageId(): string
    {
        return $this->imageId;
    }

    /**
     * Set image id
     *
     * @param string $imageId
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface
     */
    public function setImageId(string $imageId): ImageInterface
    {
        $this->imageId = $imageId;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface
     */
    public function setFilename(string $filename): ImageInterface
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get attributes
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface\AttributesInterface[]
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set attributes
     *
     * @param \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface\AttributesInterface[] $attributes
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function setAttributes(array $attributes): ImageInterface
    {
        $this->attributes = $attributes;

        return $this;
    }
}
