<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Inriver\Adapter\Api\Data\OptionsByAttributeInterface;

/**
 * Class OptionsByAttribute OptionsByAttribute
 */
class OptionsByAttribute implements OptionsByAttributeInterface
{
    /** @var string[] */
    private $attributes;

    /** @var \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface[] */
    private $options;

    /**
     * Get the attribute codes
     *
     * @return string[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set the attribute codes
     *
     * @param string[] $attributes
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface
     */
    public function setAttributes(array $attributes): OptionsByAttributeInterface
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get options
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface[]
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Set options
     *
     * @param \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface[] $options
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function setOptions(array $options): OptionsByAttributeInterface
    {
        $this->options = $options;

        return $this;
    }
}
