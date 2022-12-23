<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

/**
 * Interface ImportInterface
 */
interface ImportInterface
{
    public const XML_INRIVER_IMPORT_PATH_CSV = 'inriver/import/path_csv';
    public const XML_INRIVER_IMPORT_CLEANUP_DAYS = 'inriver/import/cleanup_days';
    public const XML_INRIVER_IMPORT_PATH_BEHAVIOR = 'inriver/import/behavior';
    public const XML_INRIVER_IMPORT_PATH_DEBUG = 'inriver/import/debug';
    public const XML_INRIVER_MAX_ALLOWED_ERROR = 'inriver/import/maximum_allowed_error';
    public const XML_INRIVER_MAX_DOWNLOAD_RETRY_ATTEMPT = 'inriver/import/maximum_download_retry_attempt';
    public const XML_INRIVER_INITIAL_DOWNLOAD_RETRY_SLEEP = 'inriver/import/initial_download_retry_sleep';
    public const XML_INRIVER_MAX_DOWNLOAD_IMAGES_RETRY_ATTEMPT = 'inriver/import/maximum_download_image_retry_attempt';
    public const XML_INRIVER_INITIAL_DOWNLOAD_IMAGES_RETRY_SLEEP = 'inriver/import/initial_download_image_retry_sleep';
    public const XML_INRIVER_FORCE_UPDATE_STATUS_ON_CREATION = 'inriver/import/force_update_status_on_creation';

    /**
     * Execute import
     *
     * @param string $filename
     *
     * @return bool
     */
    public function execute(string $filename): bool;

    /**
     * Get errors as array
     *
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getErrorsAsArray(): array;

    /**
     * Get formatted log trace
     *
     * @return string
     */
    public function getFormattedLogTrace(): string;

    /**
     * Get errors
     *
     * @return \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError[]
     */
    public function getErrors(): array;

    /**
     * Set the Managed websites by the adapter
     *
     * @param string $managedWebsites
     */
    public function setManagedWebsites(string $managedWebsites);

    /**
     * returns the Managed websites by the adapter
     *
     * @return string
     */
    public function getManagedWebsites(): string;
}
