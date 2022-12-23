<?php

/** @noinspection MessDetectorValidationInspection */

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Plugin;

use Inriver\Adapter\Helper\Import as InriverImportHelper;
use Magento\Catalog\Model\Product;
use Magento\ImportExport\Model\Import\Config;

use function array_key_exists;
use function array_replace_recursive;
use function is_array;

class InriverImportConfigPlugin
{
    /**
     * Plugin for GetEntities
     *
     * @param \Magento\ImportExport\Model\Import\Config $subject
     * @param string[] $result
     *
     * @return string[]
     *
     * @noinspection PhpUnusedParameterInspection
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function afterGetEntities(Config $subject, array $result): array
    {
        if (
            is_array($result)
            && array_key_exists(InriverImportHelper::INRIVER_ENTITY, $result)
            && array_key_exists(Product::ENTITY, $result)
        ) {
            $result[Product::ENTITY] =
                array_replace_recursive($result[Product::ENTITY], $result[InriverImportHelper::INRIVER_ENTITY]);
        }

        return $result;
    }
}
