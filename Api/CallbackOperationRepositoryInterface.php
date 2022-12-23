<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

use Inriver\Adapter\Api\Data\CallbackOperationInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface CallbackOperationRepositoryInterface
{
    /**
     * Update a callbackOperation.
     *
     * @param \Inriver\Adapter\Api\Data\CallbackOperationInterface $callback
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CallbackOperationInterface $callback): bool;

    /**
     * Returns data for a callback.
     *
     * @param int $callbackOperationId
     *
     * @return \Inriver\Adapter\Api\Data\CallbackOperationInterface
     */
    public function get(int $callbackOperationId): CallbackOperationInterface;

    /**
     * Delete callbackOperation.
     *
     * @param \Inriver\Adapter\Api\Data\CallbackOperationInterface $callbackOperation
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CallbackOperationInterface $callbackOperation): void;

    /**
     * Delete a callbackOperation.
     *
     * @param int $callbackOperationId
     *
     * @return void
     */
    public function deleteById(int $callbackOperationId): void;

    /**
     * Returns the list of $callbackOperations for the specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \InvalidArgumentException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Returns the list of $callbackOperations for the specified search criteria.
     *
     * @param string $bulkUuid
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getListByBulkUuid(string $bulkUuid): SearchResultsInterface;

    /**
     * Returns the list of $callbackOperations for the specified search criteria.
     *
     * @param int $callbackId
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getListByCallbackId(int $callbackId): SearchResultsInterface;

    /**
     * Get InRiver operation by operation id
     *
     * @param int $operationId
     *
     * @return \Inriver\Adapter\Api\Data\CallbackOperationInterface
     */
    public function getByOperationId(int $operationId): CallbackOperationInterface;
}
