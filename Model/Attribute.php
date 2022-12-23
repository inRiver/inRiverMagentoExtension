<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model;

use Inriver\Adapter\Api\AttributeInterface;
use Inriver\Adapter\Model\Data\AttributeSetFactory;
use Magento\Catalog\Api\ProductAttributeManagementInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;

class Attribute implements AttributeInterface
{
    /** @var \Magento\Catalog\Api\ProductAttributeManagementInterface */
    private $productAttributeManagement;

    /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory */
    private $attributeSetCollectionFactory;

    /** @var \Inriver\Adapter\Model\Data\AttrubteSetFactory  */
    private $setFactory;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollectionFactory
     * @param ProductAttributeManagementInterface $productAttributeManagement
     * @param \Inriver\Adapter\Model\Data\AttributeSetFactory $setFactory
     */
    public function __construct(
        CollectionFactory $attributeSetCollectionFactory,
        ProductAttributeManagementInterface $productAttributeManagement,
        AttributeSetFactory $setFactory
    ) {
        $this->productAttributeManagement = $productAttributeManagement;
        $this->attributeSetCollectionFactory = $attributeSetCollectionFactory;
        $this->setFactory = $setFactory;
    }

    /**
     * Get all attributes from all attribute sets
     *
     * @return \Inriver\Adapter\Api\Data\AttributeSetInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(): array
    {
        $allAttributeSet = $this
            ->attributeSetCollectionFactory
            ->create()
            ->AddFieldToFilter('entity_type_id', ['eq' => 4])
            ->AddFieldToSelect('*');

        $results = array();
        foreach ($allAttributeSet->getItems() as $attributeset) {
            $attributeSetName = $attributeset->getAttributeSetName();
            $attributeSetId = $attributeset->getAttributeSetId();

            $attributeSetResult = $this->setFactory->create();
            $attributeSetResult->setAttributeSetName($attributeSetName);
            $attributeSetResult->setAttributes($this->getAttributesFromSet($attributeSetId));
            $results[$attributeSetName] = $attributeSetResult;
        }
        return $results;
    }

    /**
     * @param string $attributeSetId
     *
     * @return string[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getAttributesFromSet(string $attributeSetId): array
    {
        $productAttributes = $this->productAttributeManagement->getAttributes($attributeSetId);

        $attributeNames = array();
        foreach ($productAttributes as $attribute) {
            $attributeNames[] = $attribute->getAttributeCode();
        }

        return $attributeNames;
    }
}
