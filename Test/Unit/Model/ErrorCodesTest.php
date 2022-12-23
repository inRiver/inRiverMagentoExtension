<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model;

use Inriver\Adapter\Api\Data\ErrorCodeInterfaceFactory;
use Inriver\Adapter\Helper\ErrorCodesDirectory;
use Inriver\Adapter\Model\Data\ErrorCode;
use Inriver\Adapter\Model\ErrorCodes;
use PHPUnit\Framework\TestCase;

class ErrorCodesTest extends TestCase
{
    private const ERROR_CODE = 1000;
    private const ERROR_DESCRIPTION = 'Error description';

    public function testGet(): void
    {
        /** @var \Inriver\Adapter\Helper\ErrorCodesDirectory|\Inriver\Adapter\Test\Unit\Model\MockObject $errorCodesDirectory */
        $errorCodesDirectory = $this->createMock(ErrorCodesDirectory::class);
        $errorCodesDirectory->method('getErrorDescriptions')->willReturn([
            self::ERROR_CODE => self::ERROR_DESCRIPTION,
        ]);

        $errorCode = new ErrorCode();

        /** @var \Inriver\Adapter\Api\Data\ErrorCodeInterfaceFactory|\Inriver\Adapter\Test\Unit\Model\MockObject $errorCodeFactory */
        $errorCodeFactory = $this->createMock(ErrorCodeInterfaceFactory::class);
        $errorCodeFactory->method('create')->willReturn($errorCode);

        $subject = new ErrorCodes($errorCodesDirectory, $errorCodeFactory);

        $this->assertEquals([$errorCode], $subject->get());
    }
}
