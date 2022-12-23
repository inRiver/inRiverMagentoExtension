<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Plugin;

use Inriver\Adapter\Helper\Import as InriverImportHelper;
use Inriver\Adapter\Model\Import\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

use function is_string;

/**
 * Class InriverProductImportPlugin InriverProductImportPlugin
 */
class InriverProductImportPlugin
{
    /** @var \Inriver\Adapter\Helper\Import */
    private $inriverImportHelper;

    /**
     * @param \Inriver\Adapter\Helper\Import $inriverImportHelper
     */
    public function __construct(
        InriverImportHelper $inriverImportHelper
    ) {
        $this->inriverImportHelper = $inriverImportHelper;
    }

    /**
     * Plugin for ValidateRow
     *
     * @param \Inriver\Adapter\Model\Import\Product $subject
     * @param array $rowData
     * @param int $rowNum
     *
     * @return array
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function beforeValidateRow(Product $subject, array $rowData, int $rowNum): array
    {
        if ($subject->isImportTypeConfirm() === false) {
            $subject->setIsImportTypeDisable($this->inriverImportHelper->isImportTypeDisable($rowData));
        }

        if (
            $this->inriverImportHelper->isNewProductRowWithNoPrice($rowData) &&
            $subject->isImportTypeDisable() === false
        ) {
            $rowData['price'] = 0.00;
            $rowData['status'] = Status::STATUS_DISABLED;
        }

        if (
            !isset($rowData[\Magento\CatalogImportExport\Model\Import\Product::URL_KEY]) ||
            $rowData[\Magento\CatalogImportExport\Model\Import\Product::URL_KEY] === ''
        ) {
            $newUrl = $this->inriverImportHelper->getUniqueProductUrl($rowData);

            if ($newUrl !== '') {
                $rowData[\Magento\CatalogImportExport\Model\Import\Product::URL_KEY] = $newUrl;
            }
        }

        return [$rowData, $rowNum];
    }

    /**
     * Plugin for ParseMultiselectValues
     *
     * @param \Inriver\Adapter\Model\Import\Product $subject
     * @param array $result
     *
     * @return array
     *
     * @noinspection PhpUnusedParameterInspection
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function afterParseMultiselectValues(Product $subject, array $result): array
    {
        foreach ($result as $key => $attributeValue) {
            if (is_string($attributeValue)) {
                $result[$key] = $this->inriverImportHelper->decodeImportAttributeValue($attributeValue);
            }
        }

        return $result;
    }
}
