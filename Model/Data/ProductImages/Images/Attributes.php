<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data\ProductImages\Images;

use Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface\AttributesInterface;

/**
 * Class Attributes Attributes
 */
class Attributes implements AttributesInterface
{
    /** @var int */
    private $position;

    /** @var string[] */
    private $roles;

    /** @var string[] */
    private $storeViews;

    /**
     * Get image position
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Set image position
     *
     * @param int $position
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface\AttributesInterface
     */
    public function setPosition(int $position): AttributesInterface
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get roles
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Set roles
     *
     * @param string[] $roles
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface\AttributesInterface
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function setRoles(array $roles): AttributesInterface
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get store views
     *
     * @return string[]
     */
    public function getStoreViews(): array
    {
        return $this->storeViews;
    }

    /**
     * Set store views
     *
     * @param string[] $storeViews
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface\AttributesInterface
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function setStoreViews(array $storeViews): AttributesInterface
    {
        $this->storeViews = $storeViews;

        return $this;
    }
}
