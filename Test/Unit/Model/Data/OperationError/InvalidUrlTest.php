<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data\OperationError;

use Inriver\Adapter\Helper\ErrorCodesDirectory;
use Inriver\Adapter\Model\Data\OperationError\InvalidUrl;
use PHPUnit\Framework\TestCase;

use function strlen;

class InvalidUrlTest extends TestCase
{
    public function testGetCode(): void
    {
        $invalidUrl = new InvalidUrl();
        $this->assertEquals(ErrorCodesDirectory::INVALID_URL, $invalidUrl->getCode());
    }

    public function testGetDescription(): void
    {
        $invalidUrl = new InvalidUrl();
        $description = $invalidUrl->getDescription();

        $this->assertGreaterThan(0, strlen((string) $description));
    }
}
