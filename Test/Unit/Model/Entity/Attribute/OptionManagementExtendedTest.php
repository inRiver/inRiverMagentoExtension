<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Entity\Attribute;

use Exception;
use Inriver\Adapter\Model\Entity\Attribute\OptionManagementExtended;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Eav\Model\AttributeRepository;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use PHPUnit\Framework\TestCase;
use Throwable;

class OptionManagementExtendedTest extends TestCase
{
    /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute|\Inriver\Adapter\Test\Unit\Model\Entity\Attribute\MockObject */
    private $resourceModel;

    /** @var \Magento\Eav\Model\AttributeRepository|\Inriver\Adapter\Test\Unit\Model\Entity\Attribute\MockObject */
    private $attributeRepository;

    public function setUp(): void
    {
        $this->attributeRepository = $this->createMock(AttributeRepository::class);
        $this->resourceModel = $this->createMock(Attribute::class);
    }

    public function testUpdateNullAttributeCode(): void
    {
        $optionManagement = new OptionManagementExtended(
            $this->attributeRepository,
            $this->resourceModel
        );

        /** @var \Magento\Eav\Model\Entity\Attribute\Option $option */
        $option = $this->createMock(Option::class);

        $this->expectException(InputException::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        $optionManagement->update(1, '', $option);
    }

    public function testUpdateWithNonFiniteSource(): void
    {
        $optionManagement = new OptionManagementExtended(
            $this->attributeRepository,
            $this->resourceModel
        );

        /** @var \Magento\Eav\Model\Entity\Attribute\Option|\Inriver\Adapter\Test\Unit\Model\Entity\Attribute\MockObject $option */
        $option = $this->createMock(Option::class);

        $attribute = $this->createMock(AbstractAttribute::class);
        $attribute->method('usesSource')->willReturn(false);
        $this->attributeRepository->method('get')->willReturn($attribute);

        $this->expectException(StateException::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        $optionManagement->update(1, 'attribute_code', $option);
    }

    public function testUpdateWithSaveFailure(): void
    {
        $optionManagement = new OptionManagementExtended(
            $this->attributeRepository,
            $this->resourceModel
        );

        $storeLabelsArray = [
            $this->createMock(AttributeOptionLabelInterface::class),
        ];

        /** @var \Magento\Eav\Model\Entity\Attribute\Option|\Inriver\Adapter\Test\Unit\Model\Entity\Attribute\MockObject $option */
        $option = $this->createMock(Option::class);
        $option->method('getValue')->willReturn('value');
        $option->method('getLabel')->willReturn('label');
        $option->method('getStoreLabels')->willReturn($storeLabelsArray);

        $attribute = $this->createMock(AbstractAttribute::class);
        $attribute->method('usesSource')->willReturn(true);
        $this->attributeRepository->method('get')->willReturn($attribute);

        $this->resourceModel->method('save')->willThrowException(new Exception());

        $this->expectException(Throwable::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        $optionManagement->update(1, 'attribute_code', $option);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testUpdate(): void
    {
        $optionManagement = new OptionManagementExtended(
            $this->attributeRepository,
            $this->resourceModel
        );

        $storeLabelsArray = [
            $this->createMock(AttributeOptionLabelInterface::class),
        ];

        /** @var \Magento\Eav\Model\Entity\Attribute\Option|\Inriver\Adapter\Test\Unit\Model\Entity\Attribute\MockObject $option */
        $option = $this->createMock(Option::class);
        $option->method('getValue')->willReturn('value');
        $option->method('getLabel')->willReturn('label');
        $option->method('getStoreLabels')->willReturn($storeLabelsArray);

        $attribute = $this->createMock(AbstractAttribute::class);
        $attribute->method('usesSource')->willReturn(true);
        $this->attributeRepository->method('get')->willReturn($attribute);

        /** @noinspection PhpUnhandledExceptionInspection */
        $optionManagement->update(1, 'attribute_code', $option);
    }
}
