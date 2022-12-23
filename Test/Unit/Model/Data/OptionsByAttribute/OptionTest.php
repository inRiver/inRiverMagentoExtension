<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data\OptionsByAttribute;

use Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface\ValuesInterface;
use Inriver\Adapter\Model\Data\OptionsByAttribute\Option;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{

    public function testSetAdminValue(): void
    {
        $option = new Option();
        $option->setAdminValue('admin_value');
        $this->assertEquals('admin_value', $option->getAdminValue());
    }

    public function testSetValues(): void
    {
        $theArray = [
            $this->createMock(ValuesInterface::class),
        ];

        $option = new Option();
        $option->setValues($theArray);
        $this->assertInstanceOf(ValuesInterface::class, $option->getValues()[0]);
    }
}
