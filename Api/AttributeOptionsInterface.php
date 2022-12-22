<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

use Inriver\Adapter\Api\Data\OptionsByAttributeInterface;

/**
 * Interface AttributeOptionsInterface
 */
interface AttributeOptionsInterface
{
    /**
     * Synchronized attribute options
     *
     * @param \Inriver\Adapter\Api\Data\OptionsByAttributeInterface $optionsByAttribute
     *
     * @return void
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function post(OptionsByAttributeInterface $optionsByAttribute): void;
}
