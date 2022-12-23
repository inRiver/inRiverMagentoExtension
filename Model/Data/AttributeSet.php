<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Inriver\Adapter\Api\Data\AttributeSetInterface;
use Magento\Framework\Model\AbstractModel;

class AttributeSet extends AbstractModel implements AttributeSetInterface
{
    /** @var string */
    public $attributeSetName;

    /** @var string[] */
    public $attributes;

    /**
     * @param string $value
     *
     * @return \Inriver\Adapter\Api\Data\AttributeSetInterface
     */
    public function setAttributeSetName(string $value): AttributeSetInterface
    {
        $this->attributeSetName = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getAttributeSetName(): string
    {
        return $this->attributeSetName;
    }

    /**
     * @param string[] $values
     *
     * @return \Inriver\Adapter\Api\Data\AttributeSetInterface
     */
    public function setAttributes(array $values): AttributeSetInterface
    {
        $this->attributes = $values;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
