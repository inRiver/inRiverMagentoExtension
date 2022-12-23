<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model;

use Inriver\Adapter\Api\Data\AppVersionsInterface;
use Inriver\Adapter\Model\Versions;
use PHPUnit\Framework\TestCase;

class VersionsTest extends TestCase
{
    /** @var \Inriver\Adapter\Model\Versions */
    private $model;

    /** @var \Inriver\Adapter\Api\Data\AppVersionsInterface|\Inriver\Adapter\Test\Unit\Model\MockObject */
    private $appVersionMock;

    public function testGet(): void
    {
        $this->assertEquals($this->appVersionMock, $this->model->get());
    }

    protected function setUp(): void
    {
        $this->appVersionMock = $this->createMock(AppVersionsInterface::class);

        $this->model = new Versions(
            $this->appVersionMock
        );
    }
}
