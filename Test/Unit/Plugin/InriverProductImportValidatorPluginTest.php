<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Plugin;

use Inriver\Adapter\Helper\Import as InriverImportHelper;
use Inriver\Adapter\Model\Import\Product\Validator;
use Inriver\Adapter\Plugin\InriverProductImportValidatorPlugin;
use PHPUnit\Framework\TestCase;

use function array_pop;

class InriverProductImportValidatorPluginTest extends TestCase
{
    /** @var \Inriver\Adapter\Model\Import\Product\Validator|\Inriver\Adapter\Test\Unit\Plugin\MockObject */
    private $productImportValidator;

    /** @var \Inriver\Adapter\Helper\Import|\Inriver\Adapter\Test\Unit\Plugin\MockObject */
    private $inriverImportHelper;

    public function testTypeSelect(): void
    {
        $inriverProductImportValidatorPlugin = new InriverProductImportValidatorPlugin($this->inriverImportHelper);
        $this->inriverImportHelper->expects($this->once())
            ->method('decodeImportAttributeValue')->willReturn('abc%');

        $return = $inriverProductImportValidatorPlugin->beforeIsAttributeValid(
            $this->productImportValidator,
            'attr1',
            ['type' => 'select'],
            ['attr1' => 'abc%25', 'sku' => 'abc%25', 'attr3' => null]
        );
        $this->assertEquals(
            'abc%',
            $return[2]['attr1']
        );
        $this->assertEquals(
            'abc%25',
            $return[2]['sku']
        );
        $this->assertNull($return[2]['attr3']);
    }

    public function testInNotDecodeList(): void
    {
        $inriverProductImportValidatorPlugin = new InriverProductImportValidatorPlugin($this->inriverImportHelper);
        $this->inriverImportHelper->expects($this->never())
            ->method('decodeImportAttributeValue')->willReturn('abc%');
        $valueToNotDecode = InriverImportHelper::ATTRIBUTES_NOT_TO_DECODE;
        $attributeCode = array_pop($valueToNotDecode);
        $return = $inriverProductImportValidatorPlugin->beforeIsAttributeValid(
            $this->productImportValidator,
            $attributeCode,
            ['type' => 'select'],
            ['attr1' => 'abc%25', $attributeCode => 'abc%25']
        );
        $this->assertEquals(
            'abc%25',
            $return[2]['attr1']
        );
        $this->assertEquals(
            'abc%25',
            $return[2][$attributeCode]
        );
    }

    public function testNotSelect(): void
    {
        $inriverProductImportValidatorPlugin = new InriverProductImportValidatorPlugin($this->inriverImportHelper);
        $this->inriverImportHelper->expects($this->never())
            ->method('decodeImportAttributeValue')->willReturn('abc%');
        $return = $inriverProductImportValidatorPlugin->beforeIsAttributeValid(
            $this->productImportValidator,
            'sku',
            ['type' => 'decimal'],
            ['attr1' => 'abc%25', 'sku' => 'abc%25']
        );
        $this->assertEquals(
            'abc%25',
            $return[2]['attr1']
        );
        $this->assertEquals(
            'abc%25',
            $return[2]['sku']
        );
    }

    protected function setUp(): void
    {
        $this->productImportValidator = $this->createMock(Validator::class);
        $this->inriverImportHelper = $this->createPartialMock(
            InriverImportHelper::class,
            ['decodeImportAttributeValue']
        );
    }
}
