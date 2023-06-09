<?php

/** @noinspection MessDetectorValidationInspection */

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Plugin;

use Inriver\Adapter\Helper\Import;
use Inriver\Adapter\Helper\Import as InriverImportHelper;
use Inriver\Adapter\Model\Import\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

use function is_string;

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
     * @param string[] $rowData
     * @param int $rowNum
     *
     * @return string[]
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function beforeValidateRow(Product $subject, array $rowData, int $rowNum): array
    {
        if ($subject->isImportTypeConfirm() === false) {
            $subject->setIsImportTypeDisable($this->inriverImportHelper->isImportTypeDisable($rowData));
        }

        if ($this->inriverImportHelper->isNewProductRowWithNoPrice($rowData) && $subject->isImportTypeDisable() === false) {
            $rowData['price'] = 0.00;

            if (!array_key_exists(Import::COL_STATUS, $rowData)) {
                $rowData[Import::COL_STATUS] = Status::STATUS_DISABLED;
            }
        }

        if (
            !isset($rowData[\Magento\CatalogImportExport\Model\Import\Product::URL_KEY]) ||
            $rowData[Product::URL_KEY] === ''
        ) {
            $newUrl = $this->inriverImportHelper->getUniqueProductUrl($rowData);

            if ($newUrl !== '') {
                $rowData[Product::URL_KEY] = $newUrl;
            }
        }

        return [$rowData, $rowNum];
    }

    /**
     * Plugin for ParseMultiselectValues
     *
     * @param \Inriver\Adapter\Model\Import\Product $subject
     * @param string[] $result
     *
     * @return string[]
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
