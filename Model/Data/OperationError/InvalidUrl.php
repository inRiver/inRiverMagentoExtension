<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data\OperationError;

use Inriver\Adapter\Helper\ErrorCodesDirectory;
use Magento\Framework\Phrase;

use function __;

/**
 * Class InvalidUrl
 * Specific Operation error class for invalid url
 */
class InvalidUrl extends OperationError
{
    /**
     * Get error code
     *
     * @return int
     */
    public function getCode(): int
    {
        return ErrorCodesDirectory::INVALID_URL;
    }

    /**
     * Get error description
     *
     * @return string
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function getDescription(): Phrase
    {
        return __('Invalid URL provided for CSV file');
    }
}
