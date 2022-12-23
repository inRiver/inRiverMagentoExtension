<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

interface InriverMediaGalleryDataInterface
{
    /**
     * Get value id
     *
     * @return int|null
     */
    public function getValueId(): ?int;

    /**
     * Set value id
     *
     * @param int $valueId
     *
     * @return \Inriver\Adapter\Api\Data\InriverMediaGalleryDataInterface
     */
    public function setValueId(int $valueId): InriverMediaGalleryDataInterface;

    /**
     * Get image Id
     *
     * @return string|null
     */
    public function getImageId(): ?string;

    /**
     * Set image Id
     *
     * @param string $imageId
     *
     * @return \Inriver\Adapter\Api\Data\InriverMediaGalleryDataInterface
     */
    public function setImageId(string $imageId): InriverMediaGalleryDataInterface;
}
