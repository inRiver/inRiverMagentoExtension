<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model;

use Inriver\Adapter\Api\CallbackRepositoryInterface;
use Inriver\Adapter\Api\Data\CallbackInterface;
use Inriver\Adapter\Model\Callback\GetList;
use Inriver\Adapter\Model\Data\CallbackFactory;
use Inriver\Adapter\Model\ResourceModel\Callback as CallbackResourceModel;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Throwable;

use function __;

/**
 * Class CallbackRepository
 * A repository for managing the Callback entity
 */
class CallbackRepository implements CallbackRepositoryInterface
{
    /** @var \Inriver\Adapter\Api\Data\CallbackInterface[] */
    private $instances = [];

    /** @var int [] */
    private $bulkUuidToCallback = [];

    /** @var \Inriver\Adapter\Model\Data\CallbackFactory */
    private $callbackFactory;

    /** @var \Inriver\Adapter\Model\ResourceModel\Callback */
    private $callbackResource;

    /** @var \Inriver\Adapter\Model\Callback\GetList */
    private $getListCallback;

    /**
     * @param \Inriver\Adapter\Model\Data\CallbackFactory $callbackFactory
     * @param \Inriver\Adapter\Model\ResourceModel\Callback $callbackResource
     * @param \Inriver\Adapter\Model\Callback\GetList $getListCallback
     */
    public function __construct(
        CallbackFactory $callbackFactory,
        CallbackResourceModel $callbackResource,
        GetList $getListCallback
    ) {
        $this->callbackFactory = $callbackFactory;
        $this->callbackResource = $callbackResource;
        $this->getListCallback = $getListCallback;
    }

    /**
     * @param \Inriver\Adapter\Api\Data\CallbackInterface $callback
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CallbackInterface $callback): bool
    {
        try {
            $this->callbackResource->save($callback);
        } catch (Throwable $e) {
            throw new CouldNotSaveException(
                __('Could not save Callback: %1', $e->getMessage()),
                null
            );
        }

        unset($this->instances[$callback->getId()]);

        return true;
    }

    /**
     * @param int $callbackId
     *
     * @return \Inriver\Adapter\Api\Data\CallbackInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $callbackId): CallbackInterface
    {
        if (!isset($this->instances[$callbackId])) {
            /** @var \Inriver\Adapter\Api\Data\CallbackInterface $callback */
            $callback = $this->callbackFactory->create();
            $this->callbackResource->load($callback, $callbackId);

            if (!$callback->getId()) {
                throw new NoSuchEntityException(
                    __(
                        'No such entity with %fieldName = %fieldValue',
                        [
                            'fieldName' => 'id',
                            'fieldValue' => $callbackId,
                        ]
                    )
                );
            }

            $this->instances[$callbackId] = $callback;
        }

        return $this->instances[$callbackId];
    }

    /**
     * @param string $bulkUuid
     *
     * @return \Inriver\Adapter\Api\Data\CallbackInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByBulkUuid(string $bulkUuid): CallbackInterface
    {
        if (!isset($this->bulkUuidToCallback[$bulkUuid])) {
            $callback = $this->callbackFactory->create();
            $this->callbackResource->load($callback, $bulkUuid, CallbackInterface::BULK_UUID);

            if (!$callback->getId()) {
                throw new NoSuchEntityException(
                    __(
                        'No such entity with %fieldName = %fieldValue',
                        [
                            'fieldName' => CallbackInterface::BULK_UUID,
                            'fieldValue' => $bulkUuid,
                        ]
                    )
                );
            }

            $this->instances[$callback->getId()] = $callback;
            $this->bulkUuidToCallback[$bulkUuid] = $callback->getId();
        }

        return $this->instances[$this->bulkUuidToCallback[$bulkUuid]];
    }

    /**
     * @param \Inriver\Adapter\Api\Data\CallbackInterface $callback
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CallbackInterface $callback): void
    {
        try {
            $this->callbackResource->delete($callback);
        } catch (Throwable $e) {
            throw new LocalizedException(
                __(
                    'Cannot delete callback with id %1: %2',
                    $callback->getId(),
                    $e->getMessage()
                ),
                null
            );
        }

        unset($this->instances[$callback->getId()]);
    }

    /**
     * @param int $callbackId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $callbackId): void
    {
        $callback = $this->get($callbackId);
        $this->delete($callback);
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria): SearchResultsInterface
    {
        return $this->getListCallback->getList($criteria);
    }
}
