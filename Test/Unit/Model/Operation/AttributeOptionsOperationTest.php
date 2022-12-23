<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Operation;

use ArrayIterator;
use Inriver\Adapter\Api\Data\OptionsByAttributeInterface;
use Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface;
use Inriver\Adapter\Helper\ErrorCodesDirectory;
use Inriver\Adapter\Model\Data\OptionsByAttribute\Option\Values;
use Inriver\Adapter\Model\Entity\Attribute\OptionManagementExtended;
use Inriver\Adapter\Model\Operation\AttributeOptionsOperation;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Helper\Data;
use PHPUnit\Framework\TestCase;

class AttributeOptionsOperationTest extends TestCase
{
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $scopeConfig;

    /** @var \Magento\Eav\Api\AttributeRepositoryInterface|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $attributeRepository;

    /** @var \Inriver\Adapter\Model\Entity\Attribute\OptionManagementExtended|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $attributeOptionManagement;

    /** @var \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $attributeOptionFactory;

    /** @var \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $attributeOptionLabelFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $storeManager;

    /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $option;

    /** @var \Inriver\Adapter\Api\Data\OptionsByAttributeInterface|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $message;

    /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $collectionFactory;

    /** @var \Magento\Swatches\Helper\Data|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $swatchHelper;

    /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection|\Inriver\Adapter\Test\Unit\Model\Operation\MockObject */
    private $collection;

    public function setUp(): void
    {
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->attributeRepository = $this->createMock(AttributeRepositoryInterface::class);
        $this->attributeOptionManagement = $this->createMock(OptionManagementExtended::class);
        $this->attributeOptionFactory = $this->createMock(AttributeOptionInterfaceFactory::class);
        $this->attributeOptionLabelFactory = $this->createMock(AttributeOptionLabelInterfaceFactory::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->option = $this->createMock(Option::class);
        $this->collectionFactory = $this->createMock(CollectionFactory::class);
        $this->swatchHelper = $this->createMock(Data::class);

        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn(1);
        $this->storeManager->method('getStore')->willReturn($store);

        $this->storeManager->method('getStores')->willReturn([$store]);

        $label = $this->createMock(AttributeOptionLabelInterface::class);
        $this->attributeOptionLabelFactory->method('create')->willReturn($label);

        $attributeOption = $this->createMock(AttributeOptionInterface::class);
        $this->attributeOptionFactory->method('create')->willReturn($attributeOption);

        $this->message = $this->createMock(OptionsByAttributeInterface::class);

        $this->collection = $this->createMock(Collection::class);
        $this->collection->method('setPositionOrder')->willReturnSelf();
        $this->collection->method('setAttributeFilter')->willReturnSelf();
        $this->collection->method('setStoreFilter')->willReturnSelf();
        $this->collection->method('load')->willReturnSelf();

        $this->collectionFactory
            ->method('create')
            ->willReturn($this->collection);

        $this->swatchHelper->method('isSwatchAttribute')->willReturn(false);
    }

    public function testProcessEntityDoesNotExist(): void
    {
        $subject = $this->getNewSubject();

        $this->message->method('getAttributes')->willReturn(['an_attribute']);

        $this->attributeRepository
            ->expects($this->once())
            ->method('get')
            ->willThrowException(new NoSuchEntityException());

        $this->expectExceptionCode(ErrorCodesDirectory::ATTRIBUTE_DOES_NOT_EXIST);
        /** @noinspection PhpUnhandledExceptionInspection */
        $subject->post($this->message);
    }

    public function testProcessCreate(): void
    {
        $subject = $this->getNewSubject();

        $this->message->method('getAttributes')->willReturn(['an_attribute']);
        $this->message->method('getOptions')->willReturn([
            $this->getOptionInstance(
                'the_new_admin_value',
                [
                    'en' => 'the_new_en_value',
                    'fr' => 'the_new_fr_value',
                ]
            ),
        ]);

        $this->collection->method('getIterator')->willReturn(new ArrayIterator([]));

        $attribute = $this->createMock(Attribute::class);
        $attribute->method('getAttributeCode')->willReturn('an_attribute_code');

        $this->attributeRepository->expects($this->once())->method('get')->willReturn($attribute);

        $this->attributeOptionManagement->expects($this->once())->method('add');

        /** @noinspection PhpUnhandledExceptionInspection */
        $subject->post($this->message);
    }

    public function testProcessUpdate(): void
    {
        $subject = $this->getNewSubject();

        $this->message->method('getAttributes')->willReturn(['an_attribute']);
        $this->message->method('getOptions')->willReturn([
            $this->getOptionInstance(
                'the_existing_admin_value',
                [
                    'en' => 'the_existing_en_value',
                    'fr' => 'the_existing_fr_value',
                ]
            ),
        ]);

        $attribute = $this->createMock(AttributeInterface::class);

        $this->collection
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new ArrayIterator([
                $this->getAttributeOptionMock('the_existing_admin_value'),
            ]));

        $attribute->method('getAttributeCode')->willReturn('an_attribute_code');

        $this->attributeRepository->expects($this->once())->method('get')->willReturn($attribute);

        $this->attributeOptionManagement->expects($this->once())->method('update');

        /** @noinspection PhpUnhandledExceptionInspection */
        $subject->post($this->message);
    }

    protected function getNewSubject(): AttributeOptionsOperation
    {
        return new AttributeOptionsOperation(
            $this->scopeConfig,
            $this->attributeRepository,
            $this->attributeOptionManagement,
            $this->attributeOptionFactory,
            $this->attributeOptionLabelFactory,
            $this->storeManager,
            $this->option,
            $this->collectionFactory
        );
    }

    private function getAttributeOptionMock(string $label): \Magento\Eav\Model\Entity\Attribute\Option
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\Magento\Eav\Model\Entity\Attribute\Option $mock */
        $mock = $this->createMock(\Magento\Eav\Model\Entity\Attribute\Option::class);
        $mock->method('getValue')->willReturn($label);

        return $mock;
    }

    /**
     * @param string $adminValue
     * @param array $storeValues
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getOptionInstance(string $adminValue, array $storeValues): OptionInterface
    {
        $option = new \Inriver\Adapter\Model\Data\OptionsByAttribute\Option();
        $option->setAdminValue($adminValue);

        $values = [];

        foreach ($storeValues as $key => $value) {
            $theValue = new Values();
            /** @noinspection PhpUnhandledExceptionInspection */
            $theValue->setStoreViewCode($key);
            $theValue->setValue($value);
            $values[] = $theValue;
        }

        $option->setValues($values);

        return $option;
    }
}
