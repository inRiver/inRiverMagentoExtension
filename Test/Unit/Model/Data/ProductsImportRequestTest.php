<?php

/** @noinspection PhpUnhandledExceptionInspection */

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data;

use Inriver\Adapter\Model\Data\ProductsImportRequest;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;

class ProductsImportRequestTest extends TestCase
{
    private const VALID_URL = 'https://example.com';
    private const INVALID_URL = 'example.com';

    public function testSetAndGetUrl(): void
    {
        $testClass = new ProductsImportRequest();
        $testClass->setUrl(self::VALID_URL);
        $this->assertEquals(self::VALID_URL, $testClass->getUrl());
    }

    public function testBadUrl(): void
    {
        $testClass = new ProductsImportRequest();
        $this->expectException(LocalizedException::class);
        $testClass->setUrl(self::INVALID_URL);
    }
}
