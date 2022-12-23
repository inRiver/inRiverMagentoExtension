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
    public const XML_INRIVER_IMPORT_PATH_BEHAVIOR = 'inriver/import/behavior';
    public const XML_INRIVER_IMPORT_PATH_DEBUG = 'inriver/import/debug';
    public const XML_INRIVER_IMPORT_CLEANUP_DAYS = 'inriver/import/cleanup_days';
    public const XML_INRIVER_MAX_ALLOWED_ERROR = 'inriver/import/maximum_allowed_error';

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
     * @return array
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
