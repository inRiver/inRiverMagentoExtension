<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data\ProductImages\Images;

use Inriver\Adapter\Model\Data\ProductImages\Images\Attributes;
use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    public function testSetRoles(): void
    {
        $subject = $this->getSubject();
        $roles = ['aRole'];

        $subject->setRoles($roles);
        $this->assertEquals($roles, $subject->getRoles());
    }

    public function testSetStoreViews(): void
    {
        $subject = $this->getSubject();
        $storeViews = ['aStoreView'];

        $subject->setStoreViews($storeViews);
        $this->assertEquals($storeViews, $subject->getStoreViews());
    }

    private function getSubject(): Attributes
    {
        return new Attributes();
    }
}
