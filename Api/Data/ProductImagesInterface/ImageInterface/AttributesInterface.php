<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface;

/**
 * Interface AttributesInterface
 */
interface AttributesInterface
{

    /**
     * Get image position
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Set image position
     *
     * @param int $position
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface\AttributesInterface
     */
    public function setPosition(int $position): AttributesInterface;

    /**
     * Get roles
     *
     * @return string[]
     */
    public function getRoles(): array;

    /**
     * Set roles
     *
     * @param string[] $roles
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface\AttributesInterface
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function setRoles(array $roles): AttributesInterface;

    /**
     * Get store views
     *
     * @return string[]
     */
    public function getStoreViews(): array;

    /**
     * Set store views
     *
     * @param string[] $storeViews
     *
     * @return \Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface\AttributesInterface
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function setStoreViews(array $storeViews): AttributesInterface;
}
