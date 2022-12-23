<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Operation;

use Inriver\Adapter\Api\AttributeOptionsInterface;
use Inriver\Adapter\Api\Data\OptionsByAttributeInterface;
use Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface;
use Inriver\Adapter\Helper\ErrorCodesDirectory;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Eav\Model\Entity\Attribute\Option as AttributeOption;
use Magento\Eav\Model\Entity\Attribute\OptionManagement;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

use function __;
use function array_key_exists;

class AttributeOptionsOperation implements AttributeOptionsInterface
{
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    /** @var string */
    protected $sourceUrl;

    /** @var \Inriver\Adapter\Model\Data\Import */
    protected $import;

    /** @var \Inriver\Adapter\Helper\FileDownloader */
    protected $downloader;

    /** @var \Magento\Framework\Filesystem */
    protected $filesystem;

    /** @var \Inriver\Adapter\Helper\FileEncoding */
    protected $fileEncoding;

    /** @var \Magento\Eav\Api\AttributeRepositoryInterface */
    protected $attributeRepository;

    /** @var \Magento\Eav\Model\Entity\Attribute\OptionManagement */
    protected $attributeOptionManagement;

    /** @var \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory */
    protected $attributeOptionFactory;

    /** @var \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory */
    protected $attributeOptionLabelFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManger;

    /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option */
    protected $option;

    /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory */
    protected $attributeOptionCollectionFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     * @param \Magento\Eav\Model\Entity\Attribute\OptionManagement $attributeOptionManagement
     * @param \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $attributeOptionFactory
     * @param \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $attributeOptionLabelFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManger
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option $option
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attributeOptionCollectionFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        AttributeRepositoryInterface $attributeRepository,
        OptionManagement $attributeOptionManagement,
        AttributeOptionInterfaceFactory $attributeOptionFactory,
        AttributeOptionLabelInterfaceFactory $attributeOptionLabelFactory,
        StoreManagerInterface $storeManger,
        Option $option,
        CollectionFactory $attributeOptionCollectionFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->attributeRepository = $attributeRepository;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->attributeOptionFactory = $attributeOptionFactory;
        $this->attributeOptionLabelFactory = $attributeOptionLabelFactory;
        $this->storeManger = $storeManger;
        $this->attributeOptionCollectionFactory = $attributeOptionCollectionFactory;
        $this->option = $option;
    }

    /**
     * Synchronized attribute options
     *
     * @param \Inriver\Adapter\Api\Data\OptionsByAttributeInterface $optionsByAttribute
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function post(OptionsByAttributeInterface $optionsByAttribute): void
    {
        foreach ($optionsByAttribute->getAttributes() as $attribute) {
            $this->processAttribute($attribute, $optionsByAttribute->getOptions(), Product::ENTITY);
        }
    }

    /**
     * @param string $attribute
     * @param \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface[] $newOptions
     * @param string $entityType
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function processAttribute(string $attribute, array $newOptions, string $entityType): void
    {
        try {
            $attribute = $this->attributeRepository->get($entityType, $attribute);
        } catch (NoSuchEntityException $exception) {
            throw new LocalizedException(
                __('Attribute %1 does not exist', $attribute),
                $exception,
                ErrorCodesDirectory::ATTRIBUTE_DOES_NOT_EXIST
            );
        }

        $newOptionList = [];

        foreach ($newOptions as $option) {
            $newOptionList[$option->getAdminValue()] = $option;
        }

        $currentOptions = $this->getAttributeOptions($attribute);

        // Update existing options first
        foreach ($currentOptions as $currentOption) {
            /** @var \Magento\Eav\Model\Entity\Attribute\Option $currentOption */
            if (array_key_exists($currentOption->getValue(), $newOptionList)) {
                $updatedOption = $newOptionList[$currentOption->getValue()];
                $this->updateOption($attribute, $currentOption, $updatedOption);
                unset($newOptionList[$currentOption->getValue()]);
            }
        }

        // Create remaining options
        foreach ($newOptionList as $newOption) {
            $this->createOption($attribute, $newOption);
        }
    }

    /**
     * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
     * @param \Magento\Eav\Model\Entity\Attribute\Option $currentOption
     * @param \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface $updatedOption
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function updateOption(
        AttributeInterface $attribute,
        AttributeOption $currentOption,
        OptionInterface $updatedOption
    ): void {
        $labels = $this->getStoreLabels($updatedOption);
        $currentOption->setLabel($updatedOption->getAdminValue());
        $currentOption->setStoreLabels($labels);

        $this->attributeOptionManagement->update(
            (string) $attribute->getEntityTypeId(),
            (string) $attribute->getAttributeCode(),
            (int) $currentOption->getId(),
            $currentOption
        );
    }

    /**
     * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
     * @param \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface $newOption
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function createOption(AttributeInterface $attribute, OptionInterface $newOption): void
    {
        $labels = $this->getStoreLabels($newOption);

        $newAttributeOption = $this->attributeOptionFactory->create();
        $newAttributeOption->setLabel($newOption->getAdminValue());
        $newAttributeOption->setStoreLabels($labels);
        $newAttributeOption->setSortOrder(0);
        $newAttributeOption->setIsDefault(false);

        $this->attributeOptionManagement->add(
            $attribute->getEntityTypeId(),
            $attribute->getAttributeCode(),
            $newAttributeOption
        );
    }

    /**
     * @param \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface $newOption
     *
     * @return string[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStoreLabels(OptionInterface $newOption): array
    {
        $labels = [];

        foreach ($newOption->getValues() as $value) {
            $storeId = $this->storeManger->getStore($value->getStoreViewCode())->getId();

            $label = $this->attributeOptionLabelFactory->create();
            $label->setLabel($value->getValue());
            $label->setStoreId($storeId);
            $labels[] = $label;
        }

        return $labels;
    }

    /**
     * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
     *
     * @return \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    private function getAttributeOptions(AttributeInterface $attribute): Collection
    {
        $collection = $this->attributeOptionCollectionFactory->create();
        $collection
            ->setPositionOrder('asc')
            ->setAttributeFilter($attribute->getAttributeId())
            ->setStoreFilter('admin')
            ->load();

        return $collection;
    }
}
