<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data;

use Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface;
use Inriver\Adapter\Model\Data\ProductImages;
use PHPUnit\Framework\TestCase;

class ProductImagesTest extends TestCase
{

    private const SOME_SKU = 'someSku';

    public function testSetImages(): void
    {
        $subject = $this->getSubject();
        $images = [
            $this->createMock(ImageInterface::class),
        ];

        $subject->setImages($images);
        $this->assertEquals($images, $subject->getImages());
    }

    public function testSetSku(): void
    {
        $subject = $this->getSubject();
        $subject->setSku(self::SOME_SKU);
        $this->assertEquals(self::SOME_SKU, $subject->getSku());
    }

    private function getSubject(): ProductImages
    {
        return new ProductImages();
    }
}
