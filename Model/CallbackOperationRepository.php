<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model;

use Inriver\Adapter\Api\CallbackOperationRepositoryInterface;
use Inriver\Adapter\Api\Data\CallbackInterface;
use Inriver\Adapter\Api\Data\CallbackOperationInterface;
use Inriver\Adapter\Model\CallbackOperation\GetList;
use Inriver\Adapter\Model\Data\CallbackOperationFactory;
use Inriver\Adapter\Model\ResourceModel\CallbackOperation as CallbackOperationResourceModel;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Throwable;

use function __;

/**
 * Class CallbackOperationRepository
 * A repository for managing the CallbackOperation entity
 */
class CallbackOperationRepository implements CallbackOperationRepositoryInterface
{
    /** @var \Inriver\Adapter\Api\Data\CallbackOperationInterface[] */
    private $instances = [];

    /** @var \Inriver\Adapter\Model\Data\CallbackOperationFactory */
    private $callbackOperationFactory;

    /** @var \Inriver\Adapter\Model\ResourceModel\CallbackOperation */
    private $callbackOperationResource;

    /** @var \Inriver\Adapter\Api\CallbackRepositoryInterface */
    private $callbackRepository;

    /** @var \Inriver\Adapter\Model\CallbackOperation\GetList */
    private $getCallbackOperationList;

    /** @var \Magento\Framework\Api\FilterBuilder */
    private $filterBuilder;

    /** @var \Magento\Framework\Api\SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /**
     * @param \Inriver\Adapter\Model\Data\CallbackOperationFactory $callbackOperationFactory
     * @param \Inriver\Adapter\Model\ResourceModel\CallbackOperation $callbackOperationResource
     * @param \Inriver\Adapter\Model\CallbackOperation\GetList $getCallbackOperationList
     * @param \Inriver\Adapter\Model\CallbackRepository $callbackRepository
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CallbackOperationFactory $callbackOperationFactory,
        CallbackOperationResourceModel $callbackOperationResource,
        GetList $getCallbackOperationList,
        CallbackRepository $callbackRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->callbackOperationFactory = $callbackOperationFactory;
        $this->callbackOperationResource = $callbackOperationResource;
        $this->getCallbackOperationList = $getCallbackOperationList;
        $this->callbackRepository = $callbackRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param \Inriver\Adapter\Api\Data\CallbackOperationInterface $callbackOperation
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CallbackOperationInterface $callbackOperation): bool
    {
        try {
            $this->callbackOperationResource->save($callbackOperation);
        } catch (Throwable $e) {
            throw new CouldNotSaveException(
                __('Could not save CallbackOperation'),
                $e
            );
        }

        unset($this->instances[$callbackOperation->getId()]);

        return true;
    }

    /**
     * @param int $callbackOperationId
     *
     * @return \Inriver\Adapter\Api\Data\CallbackOperationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $callbackOperationId): CallbackOperationInterface
    {
        if (!isset($this->instances[$callbackOperationId])) {
            /** @var \Inriver\Adapter\Api\Data\CallbackOperationInterface $callbackOperation */
            $callbackOperation = $this->callbackOperationFactory->create();
            $this->callbackOperationResource->load($callbackOperation, $callbackOperationId);

            if (!$callbackOperation->getId()) {
                throw new NoSuchEntityException(
                    __(
                        'No such entity with %fieldName = %fieldValue',
                        [
                            'fieldName' => 'id',
                            'fieldValue' => $callbackOperationId,
                        ]
                    )
                );
            }

            $this->instances[$callbackOperationId] = $callbackOperation;
        }

        return $this->instances[$callbackOperationId];
    }

    /**
     * @param \Inriver\Adapter\Api\Data\CallbackOperationInterface $callbackOperation
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CallbackOperationInterface $callbackOperation): void
    {
        try {
            $this->callbackOperationResource->delete($callbackOperation);
        } catch (Throwable $e) {
            throw new LocalizedException(
                __(
                    'Cannot delete callbackOperation with id %1',
                    $callbackOperation->getId()
                ),
                $e
            );
        }

        unset($this->instances[$callbackOperation->getId()]);
    }

    /**
     * @param int $callbackOperationId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $callbackOperationId): void
    {
        $callbackOperation = $this->get($callbackOperationId);
        $this->delete($callbackOperation);
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria): SearchResultsInterface
    {
        return $this->getCallbackOperationList->getList($criteria);
    }

    /**
     * @param string $bulkUuid
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getListByBulkUuid(string $bulkUuid): SearchResultsInterface
    {
        $callback = $this->callbackRepository->getByBulkUuid($bulkUuid);
        $callbackId = $callback->getCallbackId();

        return $this->getListByCallbackId($callbackId);
    }

    /**
     * @param int $callbackId
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getListByCallbackId(int $callbackId): SearchResultsInterface
    {
        $filter = $this->filterBuilder
            ->setField(CallbackInterface::CALLBACK_ID)
            ->setConditionType('=')
            ->setValue($callbackId)
            ->create();

        $this->searchCriteriaBuilder->addFilters([$filter]);
        $searchCriteria = $this->searchCriteriaBuilder->create();

        return $this->getList($searchCriteria);
    }

    /**
     * Get InRiver operation by operation id
     *
     * @param int $operationId
     *
     * @return \Inriver\Adapter\Api\Data\CallbackOperationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByOperationId(int $operationId): CallbackOperationInterface
    {
        $callbackOperation = $this->callbackOperationFactory->create();

        $this->callbackOperationResource->load(
            $callbackOperation,
            $operationId,
            CallbackOperationInterface::OPERATION_ID
        );

        if (!$callbackOperation->getId()) {
            throw new NoSuchEntityException(
                __(
                    'No such entity with %fieldName = %fieldValue',
                    [
                        'fieldName' => CallbackOperationInterface::OPERATION_ID,
                        'fieldValue' => $operationId,
                    ]
                )
            );
        }

        return $callbackOperation;
    }
}
