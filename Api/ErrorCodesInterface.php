<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

/**
 * Interface ErrorCodesInterface
 */
interface ErrorCodesInterface
{
    /**
     * Return error codes
     *
     * @return \Inriver\Adapter\Api\Data\ErrorCodeInterface[]
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function get(): array;
}
