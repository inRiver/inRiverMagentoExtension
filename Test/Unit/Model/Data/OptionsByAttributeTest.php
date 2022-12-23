<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data;

use Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface;
use Inriver\Adapter\Model\Data\OptionsByAttribute;
use PHPUnit\Framework\TestCase;

class OptionsByAttributeTest extends TestCase
{
    public function testSetAttributes(): void
    {
        $arrayOfStrings = [
            'string',
            'string',
        ];

        $optionsByAttribute = new OptionsByAttribute();
        $optionsByAttribute->setAttributes($arrayOfStrings);
        $this->assertEquals($arrayOfStrings, $optionsByAttribute->getAttributes());
    }

    public function testSetOptions(): void
    {
        $arrayOfOptions = [
            $this->createMock(OptionInterface::class),
        ];

        $optionsByAttribute = new OptionsByAttribute();
        $optionsByAttribute->setOptions($arrayOfOptions);
        $this->assertEquals($arrayOfOptions, $optionsByAttribute->getOptions());
    }
}
