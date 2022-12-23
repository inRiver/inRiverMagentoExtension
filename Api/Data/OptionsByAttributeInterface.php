<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface OptionsByAttributeInterface extends ExtensibleDataInterface
{
    /**
     * Get the attribute codes
     *
     * @return string[]
     */
    public function getAttributes(): array;

    /**
     * Set the attribute codes
     *
     * @param string[] $attributes
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface
     */
    public function setAttributes(array $attributes): OptionsByAttributeInterface;

    /**
     * Get options
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface[]
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getOptions(): array;

    /**
     * Set options
     *
     * @param \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface[] $options
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function setOptions(array $options): OptionsByAttributeInterface;
}
