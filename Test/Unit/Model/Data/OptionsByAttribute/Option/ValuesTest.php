<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data\OptionsByAttribute\Option;

use Inriver\Adapter\Model\Data\OptionsByAttribute\Option\Values;
use PHPUnit\Framework\TestCase;

class ValuesTest extends TestCase
{
    private const STORE_VIEW_CODE = 'store_view_code';
    private const VALUE = 'value';

    public function testSetStoreViewCode(): void
    {
        $values = new Values();

        /** @noinspection PhpUnhandledExceptionInspection */
        $values->setStoreViewCode(self::STORE_VIEW_CODE);
        $this->assertEquals(self::STORE_VIEW_CODE, $values->getStoreViewCode());
    }

    public function testSetValue(): void
    {
        $values = new Values();
        $values->setValue(self::VALUE);
        $this->assertEquals(self::VALUE, $values->getValue());
    }
}
