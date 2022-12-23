<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Config\Source;

use Inriver\Adapter\Model\Config\Source\Behavior;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;

class BehaviorTest extends TestCase
{
    private const INRIVER = 'inriver';

    public function testToArray(): void
    {
        $behavior = new Behavior();
        $this->assertNotEmpty($behavior->toArray());
    }

    public function testGetNotes(): void
    {
        $behavior = new Behavior();
        $this->assertNotEmpty($behavior->getNotes(Product::ENTITY));
        $this->assertEmpty($behavior->getNotes(Category::ENTITY));
    }

    public function testGetCode(): void
    {
        $behavior = new Behavior();
        $this->assertEquals(self::INRIVER, $behavior->getCode());
    }
}
