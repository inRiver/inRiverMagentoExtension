<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data\ProductImages;

use Inriver\Adapter\Model\Data\ProductCategories\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    private const CATEGORY_UNIQUE_ID = 'categoryUniqueId';
    private const POSITION = 24;

    public function testSetCategoryUniqueId(): void
    {
        $subject = $this->getSubject();
        $subject->setCategoryUniqueId(self::CATEGORY_UNIQUE_ID);
        $this->assertEquals(self::CATEGORY_UNIQUE_ID, $subject->getCategoryUniqueId());
    }

    public function testSetPosition(): void
    {
        $subject = $this->getSubject();
        $subject->setPosition(self::POSITION);
        $this->assertEquals(self::POSITION, $subject->getPosition());
    }

    private function getSubject(): Category
    {
        return new Category();
    }
}
