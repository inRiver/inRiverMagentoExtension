<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model;

use Inriver\Adapter\Api\Data\InriverMediaGalleryDataInterface;
use Inriver\Adapter\Api\InriverMediaGalleryDataRepositoryInterface;
use Inriver\Adapter\Model\Data\InriverMediaGalleryData as DataInriverMediaGalleryData;
use Inriver\Adapter\Model\Data\InriverMediaGalleryDataFactory;
use Inriver\Adapter\Model\ResourceModel\InriverMediaGalleryData;
use Magento\Framework\Exception\CouldNotSaveException;
use Throwable;

use function __;

class InriverMediaGalleryDataRepository implements InriverMediaGalleryDataRepositoryInterface
{
    /** @var \Inriver\Adapter\Model\ResourceModel\InriverMediaGalleryData */
    protected $resourceModel;

    /** @var \Inriver\Adapter\Model\Data\InriverMediaGalleryDataFactory */
    protected $inriverMediaGalleryDataFactory;

    /**
     * @param \Inriver\Adapter\Model\ResourceModel\InriverMediaGalleryData $resourceModel
     * @param \Inriver\Adapter\Model\Data\InriverMediaGalleryDataFactory $inriverMediaGalleryDataFactory
     */
    public function __construct(
        InriverMediaGalleryData $resourceModel,
        InriverMediaGalleryDataFactory $inriverMediaGalleryDataFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->inriverMediaGalleryDataFactory = $inriverMediaGalleryDataFactory;
    }

    /**
     * @param \Inriver\Adapter\Model\Data\InriverMediaGalleryData $inriverMediaGalleryData
     *
     * @return \Inriver\Adapter\Api\Data\InriverMediaGalleryDataInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(DataInriverMediaGalleryData $inriverMediaGalleryData): InriverMediaGalleryDataInterface
    {
        try {
            $this->resourceModel->save($inriverMediaGalleryData);
        } catch (Throwable $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $inriverMediaGalleryData;
    }

    /**
     * @param int $id
     *
     * @return \Inriver\Adapter\Api\Data\InriverMediaGalleryDataInterface
     */
    public function getById(int $id): InriverMediaGalleryDataInterface
    {
        $inriverMediaGalleryData = $this->inriverMediaGalleryDataFactory->create();
        $this->resourceModel->load($inriverMediaGalleryData, $id);

        return $inriverMediaGalleryData;
    }

    /**
     * @param string $imageId
     *
     * @return \Inriver\Adapter\Api\Data\InriverMediaGalleryDataInterface
     */
    public function getFirstByImageId(string $imageId): InriverMediaGalleryDataInterface
    {
        $inriverMediaGalleryData = $this->inriverMediaGalleryDataFactory->create();
        $this->resourceModel->load($inriverMediaGalleryData, $imageId, 'image_id');

        return $inriverMediaGalleryData;
    }
}
