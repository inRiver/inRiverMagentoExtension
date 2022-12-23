<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

/**
 * Interface AttributeSet
 */
interface AttributeSetInterface
{
    /**
     * @param string $value
     *
     * @return \Inriver\Adapter\Api\Data\AttributeSetInterface
     */
    public function setAttributeSetName(string $value): AttributeSetInterface;

    /**
     * Validation message
     *
     * @return string
     */
    public function getAttributeSetName(): string;

    /**
     * @param string[] $values
     *
     * @return \Inriver\Adapter\Api\Data\AttributeSetInterface
     */
    public function setAttributes(array $values): AttributeSetInterface;

    /**
     * return validation status
     *
     * @return string[]
     */
    public function getAttributes(): array;
}
