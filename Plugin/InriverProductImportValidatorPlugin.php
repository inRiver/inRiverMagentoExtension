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
use Inriver\Adapter\Model\Import\Product\Validator;

use function in_array;
use function is_string;

/**
 * Class InriverProductImportValidatorPlugin
 * Plugin to decode attribute before verifying if it's valid or not
 */
class InriverProductImportValidatorPlugin
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
     * Plugin for IsAttributeValid
     *
     * @param \Inriver\Adapter\Model\Import\Product\Validator $subject
     * @param string $attrCode
     * @param string[] $attrParams
     * @param string[] $rowData
     *
     * @return string[]
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function beforeIsAttributeValid(
        Validator $subject, // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        string $attrCode,
        array $attrParams,
        array $rowData
    ): array {
        if (
            $attrParams['type'] === 'select'
            && is_string($rowData[$attrCode])
            && !in_array($attrCode, Import::ATTRIBUTES_NOT_TO_DECODE, true)
        ) {
            $rowData[$attrCode] = $this->inriverImportHelper->decodeImportAttributeValue($rowData[$attrCode]);
        }

        return [$attrCode, $attrParams, $rowData];
    }
}
