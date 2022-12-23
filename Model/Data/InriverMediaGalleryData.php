<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Inriver\Adapter\Api\Data\InriverMediaGalleryDataInterface;
use Magento\Framework\Model\AbstractModel;

class InriverMediaGalleryData extends AbstractModel implements InriverMediaGalleryDataInterface
{
    /**
     * Get image Id
     *
     * @return string|null
     */
    public function getImageId(): ?string
    {
        return $this->getData('image_id');
    }

    /**
     * Set image Id
     *
     * @param string $imageId
     *
     * @return \Inriver\Adapter\Api\Data\InriverMediaGalleryDataInterface
     */
    public function setImageId(string $imageId): InriverMediaGalleryDataInterface
    {
        $this->setData('image_id', $imageId);

        return $this;
    }

    /**
     * Get value id
     *
     * @return int|null
     */
    public function getValueId(): ?int
    {
        if ($this->getData('value_id') === null) {
            return null;
        }

        return (int) $this->getData('value_id');
    }

    /**
     * Set value id
     *
     * @param int $valueId
     *
     * @return \Inriver\Adapter\Api\Data\InriverMediaGalleryDataInterface
     */
    public function setValueId(int $valueId): InriverMediaGalleryDataInterface
    {
        $this->setData('value_id', $valueId);

        return $this;
    }

    /**
     * @return void
     *
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    //phpcs:ignore SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
    protected function _construct(): void
    {
        $this->_init(\Inriver\Adapter\Model\ResourceModel\InriverMediaGalleryData::class);
    }
}
