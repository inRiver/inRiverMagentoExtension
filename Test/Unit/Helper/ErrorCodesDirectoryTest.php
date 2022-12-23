<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Helper;

use Inriver\Adapter\Helper\ErrorCodesDirectory;
use PHPUnit\Framework\TestCase;

class ErrorCodesDirectoryTest extends TestCase
{
    public function testGetErrorDescriptions(): void
    {
        $subject = new ErrorCodesDirectory();
        $this->assertNotEmpty($subject->getErrorDescriptions());
    }
}
