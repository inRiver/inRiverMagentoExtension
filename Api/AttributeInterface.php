<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

interface AttributeInterface
{
    /**
     * Get all attributes from all attribute sets
     *
     * @return \Inriver\Adapter\Api\Data\AttributeSetInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(): array;
}
