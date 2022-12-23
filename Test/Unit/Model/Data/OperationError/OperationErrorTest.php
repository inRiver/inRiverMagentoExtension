<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data\OperationError;

use Inriver\Adapter\Model\Data\OperationError\OperationError;
use Magento\Framework\Phrase;
use PHPUnit\Framework\TestCase;

use function __;

class OperationErrorTest extends TestCase
{
    public const ERROR_CODE = 8000;
    public const PHRASE = 'The phrase';

    /** @var \Inriver\Adapter\Model\Data\OperationError\OperationError */
    private $testClass;

    public function setUp(): void
    {
        $this->testClass = new class extends OperationError {
            public function getCode(): int
            {
                return OperationErrorTest::ERROR_CODE;
            }

            public function getDescription(): Phrase
            {
                $phrase = OperationErrorTest::PHRASE;

                return __($phrase);
            }
        };
    }

    public function testGetErrorAsString(): void
    {
        $phrase = self::PHRASE;
        $expected = self::ERROR_CODE . ': ' . __($phrase);
        $this->assertEquals($expected, (string) $this->testClass);
    }
}
