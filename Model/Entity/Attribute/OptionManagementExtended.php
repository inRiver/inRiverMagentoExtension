<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Entity\Attribute;

use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Eav\Model\Entity\Attribute\OptionManagement;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Throwable;

use function __;
use function is_array;

/**
 * Class OptionManagementExtended OptionManagementExtended
 */
class OptionManagementExtended extends OptionManagement
{
    /**
     * Add option to attribute.
     *
     * @param int $entityType
     * @param string $attributeCode
     * @param \Magento\Eav\Model\Entity\Attribute\Option $option
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function update(int $entityType, string $attributeCode, Option $option): void
    {
        if ($attributeCode === '') {
            throw new InputException(__('The attribute code is empty. Enter the code and try again.'));
        }

        $attribute = $this->attributeRepository->get($entityType, $attributeCode);

        if (!$attribute->usesSource()) {
            throw new StateException(__('The "%1" attribute doesn\'t work with options.', $attributeCode));
        }

        $optionLabel = $option->getValue();
        $optionId = $option->getData('option_id');
        $options = [];
        $options['value'][$optionId][0] = $optionLabel;
        $options['order'][$optionId] = $option->getSortOrder();

        if (is_array($option->getStoreLabels())) {
            foreach ($option->getStoreLabels() as $label) {
                $options['value'][$optionId][$label->getStoreId()] = $label->getLabel();
            }
        }

        $attribute->setOption($options);

        try {
            /** @noinspection PhpParamsInspection */
            $this->resourceModel->save($attribute);
        } catch (Throwable $e) {
            throw new StateException(
                __('The "%1" attribute can\'t be saved: %2', $attributeCode, $e->getMessage())
            );
        }
    }
}
