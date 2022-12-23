<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

use Inriver\Adapter\Api\Data\InriverMediaGalleryDataInterface;
use Inriver\Adapter\Model\Data\InriverMediaGalleryData;

interface InriverMediaGalleryDataRepositoryInterface
{
    /**
     * @param \Inriver\Adapter\Model\Data\InriverMediaGalleryData $inriverMediaGalleryData
     *
     * @return \Inriver\Adapter\Api\Data\InriverMediaGalleryDataInterface
     */
    public function save(InriverMediaGalleryData $inriverMediaGalleryData): InriverMediaGalleryDataInterface;

    /**
     * @param int $id
     *
     * @return \Inriver\Adapter\Api\Data\InriverMediaGalleryDataInterface
     */
    public function getById(int $id): InriverMediaGalleryDataInterface;

    /**
     * @param string $imageId
     *
     * @return \Inriver\Adapter\Api\Data\InriverMediaGalleryDataInterface
     */
    public function getFirstByImageId(string $imageId): InriverMediaGalleryDataInterface;
}
