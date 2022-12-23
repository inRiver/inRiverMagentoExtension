<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 * This file applies minor modifications to native Magento code.
 * It should be kept in sync with the latest Magento version.
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Import\ErrorProcessing;

use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregator as BaseProcessingErrorAggregator;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

/**
 * Import/Export Error Aggregator class
 */
class ProcessingErrorAggregator extends BaseProcessingErrorAggregator implements ProcessingErrorAggregatorInterface
{
    /**
     * Check if import has a fatal error
     *
     * @return bool
     *
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function hasFatalExceptions()
    {
        return (bool)$this->getErrorsCount([ProcessingError::ERROR_LEVEL_CRITICAL]) &&
            $this->validationStrategy == self::VALIDATION_STRATEGY_STOP_ON_ERROR;
    }
}
