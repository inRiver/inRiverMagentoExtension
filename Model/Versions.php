<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model;

use Inriver\Adapter\Api\Data\AppVersionsInterface;
use Inriver\Adapter\Api\VersionsInterface;

/**
 * Class Versions Versions
 */
class Versions implements VersionsInterface
{
    /** @var \Inriver\Adapter\Api\Data\AppVersionsInterface */
    protected $appVersions;

    /**
     * @param \Inriver\Adapter\Api\Data\AppVersionsInterface $appVersions
     */
    public function __construct(
        AppVersionsInterface $appVersions
    ) {
        $this->appVersions = $appVersions;
    }

    /**
     * Return version information
     *
     * @return \Inriver\Adapter\Api\Data\AppVersionsInterface
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function get(): AppVersionsInterface
    {
        return $this->appVersions;
    }
}
