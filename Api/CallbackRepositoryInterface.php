<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

use Inriver\Adapter\Api\Data\CallbackInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface CallbackRepositoryInterface
{
    /**
     * Update a callback.
     *
     * @param \Inriver\Adapter\Api\Data\CallbackInterface $callback
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CallbackInterface $callback): bool;

    /**
     * Returns data for a callback.
     *
     * @param int $callbackId
     *
     * @return \Inriver\Adapter\Api\Data\CallbackInterface
     */
    public function get(int $callbackId): CallbackInterface;

    /**
     * Returns data for a callback.
     *
     * @param string $bulkUuid
     *
     * @return \Inriver\Adapter\Api\Data\CallbackInterface
     */
    public function getByBulkUuid(string $bulkUuid): CallbackInterface;

    /**
     * Delete callback.
     *
     * @param \Inriver\Adapter\Api\Data\CallbackInterface $callback
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CallbackInterface $callback): void;

    /**
     * Delete a callback.
     *
     * @param int $callbackId
     *
     * @return void
     */
    public function deleteById(int $callbackId): void;

    /**
     * Returns the list of $callbacks for the specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \InvalidArgumentException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
