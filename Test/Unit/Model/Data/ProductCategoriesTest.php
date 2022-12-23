<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data;

use Inriver\Adapter\Api\Data\ProductCategoriesInterface\CategoryInterface;
use Inriver\Adapter\Model\Data\ProductCategories;
use PHPUnit\Framework\TestCase;

class ProductCategoriesTest extends TestCase
{

    private const SOME_SKU = 'someSku';

    public function testSetImages(): void
    {
        $subject = $this->getSubject();
        $categories = [
            $this->createMock(CategoryInterface::class),
        ];

        $subject->setCategories($categories);
        $this->assertEquals($categories, $subject->getCategories());
    }

    public function testSetSku(): void
    {
        $subject = $this->getSubject();
        $subject->setSku(self::SOME_SKU);
        $this->assertEquals(self::SOME_SKU, $subject->getSku());
    }

    private function getSubject(): ProductCategories
    {
        return new ProductCategories();
    }
}
