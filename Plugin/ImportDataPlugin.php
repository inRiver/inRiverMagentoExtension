<?php

/** @noinspection MessDetectorValidationInspection */

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Plugin;

use Inriver\Adapter\Api\Data\ImportInterface;
use Inriver\Adapter\Exception\EmptyImportException;
use Inriver\Adapter\Helper\Import;
use Inriver\Adapter\Model\ResourceModel\Import\Data;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\CatalogImportExport\Model\Import\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;

use function __;
use function reset;

class ImportDataPlugin
{

    /** @var \Inriver\Adapter\Helper\Import */
    protected $importHelper;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    /**
     * @param \Inriver\Adapter\Helper\Import $importHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Import $importHelper,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->importHelper = $importHelper;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Plugin for getNextBunch
     *
     * @param \Inriver\Adapter\Model\ResourceModel\Import\Data $subject
     * @param string[]|null $result
     *
     * @return string[]|null
     *
     * @noinspection PhpUnusedParameterInspection
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function afterGetNextBunch(Data $subject, ?array $result): ?array
    {
        if ($result !== null) {
            $firstRow = reset($result);

            if (($firstRow !== false) && !$this->importHelper->isImportTypeDisable($firstRow)) {
                return $this->treatmentForInriverImport($result);
            }
        }

        return $result;
    }

    /**
     * @param \Inriver\Adapter\Model\ResourceModel\Import\Data $subject
     * @param callable $proceed
     * @param string $code
     *
     * @return string
     * @throws \Inriver\Adapter\Exception\EmptyImportException
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @noinspection PhpUnusedParameterInspection
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function aroundGetUniqueColumnData(Data $subject, callable $proceed, string $code): string
    {
        try {
            return $proceed($code);
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (LocalizedException $ex) {
            throw new EmptyImportException(
                __($ex->getMessage()),
                $ex,
                $ex->getCode()
            );
        }
    }

    /**
     * @param string[] $result
     *
     * @return string[]
     */
    private function treatmentForInriverImport(array $result): array
    {
        foreach ($result as $rowKey => $rowData) {
            if ($this->importHelper->isNewProductRowWithNoPrice($rowData)) {
                if($this->getForceUpdateStatusConfig() === 0 || !isset($rowData[Import::COL_STATUS]) || $rowData[Import::COL_STATUS] === '') {
                    $result[$rowKey][Import::COL_STATUS] = Status::STATUS_DISABLED;
                }

                $result[$rowKey]['price'] = 0.00;
            }

            if (!isset($rowData[Product::URL_KEY]) || $rowData[Product::URL_KEY] === '') {
                $newUrl = $this->importHelper->getUniqueProductUrl($rowData);

                if ($newUrl !== '') {
                    $result[$rowKey][Product::URL_KEY] = $newUrl;
                }
            }
        }

        return $result;
    }

    public function getForceUpdateStatusConfig(): int
    {
        return (int)$this->scopeConfig->getValue(ImportInterface::XML_INRIVER_FORCE_UPDATE_STATUS_ON_CREATION);
    }
}
