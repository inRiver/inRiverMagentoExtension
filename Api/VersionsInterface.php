<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

use Inriver\Adapter\Api\Data\AppVersionsInterface;

/**
 * Interface VersionsInterface
 */
interface VersionsInterface
{
    /**
     * Return version information
     *
     * @return \Inriver\Adapter\Api\Data\AppVersionsInterface
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function get(): AppVersionsInterface;
}
