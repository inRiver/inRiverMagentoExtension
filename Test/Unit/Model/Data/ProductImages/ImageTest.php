<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data\ProductImages;

use Inriver\Adapter\Api\Data\ProductImagesInterface\ImageInterface\AttributesInterface;
use Inriver\Adapter\Model\Data\ProductImages\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    private const THE_MINE_TYPE = 'theMineType';
    private const THE_IMAGE_ID = 'theImageId';
    private const THE_URL = 'theUrl';
    private const THE_FILENAME = 'theFilename';

    public function testSetMimeType(): void
    {
        $subject = $this->getSubject();
        $subject->setMimeType(self::THE_MINE_TYPE);
        $this->assertEquals(self::THE_MINE_TYPE, $subject->getMimeType());
    }

    public function testSetImageId(): void
    {
        $subject = $this->getSubject();
        $subject->setImageId(self::THE_IMAGE_ID);
        $this->assertEquals(self::THE_IMAGE_ID, $subject->getImageId());
    }

    public function testSetUrl(): void
    {
        $subject = $this->getSubject();
        $subject->setUrl(self::THE_URL);
        $this->assertEquals(self::THE_URL, $subject->getUrl());
    }

    public function testSetFilename(): void
    {
        $subject = $this->getSubject();
        $subject->setFilename(self::THE_FILENAME);
        $this->assertEquals(self::THE_FILENAME, $subject->getFilename());
    }

    public function testSetAttributes(): void
    {
        $subject = $this->getSubject();

        $attributes = [$this->createMock(AttributesInterface::class)];

        $subject->setAttributes($attributes);
        $this->assertEquals($attributes, $subject->getAttributes());
    }

    private function getSubject(): Image
    {
        return new Image();
    }
}
