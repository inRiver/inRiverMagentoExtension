<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Helper;

/**
 * Class ErrorCodesDirectory
 * Error codes directory that will be used to give meaningful error messages
 */
class ErrorCodesDirectory
{
    public const SUCCESS = 1000;
    public const GENERAL_ERROR = 2000;
    public const INVALID_URL = 5000;
    public const INRIVER_IMPORT_PATH_NOT_SET = 5001;
    public const SKU_NOT_FOUND = 5002;
    public const SOURCE_CSV_FILE_EMPTY = 5003;
    public const CANNOT_DOWNLOAD_CSV_FILE = 5004;
    public const MESSAGE_TYPE_IS_INVALID = 5005;
    public const CANNOT_DOWNLOAD_MEDIA_FILE = 5006;
    public const ATTRIBUTE_DOES_NOT_EXIST = 5007;
    public const CATEGORY_DOES_NOT_EXIST = 5009;
    public const CANNOT_NOT_SAVE_PRODUCT_CATEGORIES = 5010;
    public const CANNOT_READ_LOCAL_MEDIA_FILE = 5013;

    /**
     * Get errors as array
     *
     * @return string[]
     */
    public function getErrorDescriptions(): array
    {
        return [
            self::GENERAL_ERROR => 'General error',
            self::INVALID_URL => 'Invalid url',
            self::INRIVER_IMPORT_PATH_NOT_SET => 'Inriver import path configuration is not set',
            self::SKU_NOT_FOUND => 'The provided sku was not found',
            self::SOURCE_CSV_FILE_EMPTY => 'Source CSV file is empty',
            self::CANNOT_DOWNLOAD_CSV_FILE => 'Cannot download CSV file',
            self::MESSAGE_TYPE_IS_INVALID => 'Operation message type is invalid',
            self::CANNOT_DOWNLOAD_MEDIA_FILE => 'Cannot download Media file',
            self::ATTRIBUTE_DOES_NOT_EXIST => 'Attribute does not exist',
            self::CATEGORY_DOES_NOT_EXIST => 'Category does not exist',
            self::CANNOT_NOT_SAVE_PRODUCT_CATEGORIES => 'Cannot save product categories',
            self::CANNOT_READ_LOCAL_MEDIA_FILE => 'Cannot read local media file',
        ];
    }
}
