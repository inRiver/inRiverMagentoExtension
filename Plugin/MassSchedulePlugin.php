<?php

/** @noinspection MessDetectorValidationInspection */

/** @noinspection MessDetectorValidationInspection */
/** @noinspection MessDetectorValidationInspection */
/** @noinspection MessDetectorValidationInspection */

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Plugin;

use Inriver\Adapter\Helper\InriverCallback;
use Inriver\Adapter\Model\CallbackOperationRepository;
use Inriver\Adapter\Model\CallbackRepository;
use Inriver\Adapter\Model\Data\Callback;
use Inriver\Adapter\Model\Data\CallbackFactory;
use Inriver\Adapter\Model\Data\CallbackOperationFactory;
use Magento\AsynchronousOperations\Api\Data\AsyncResponseInterface;
use Magento\AsynchronousOperations\Model\BulkSummaryFactory;
use Magento\AsynchronousOperations\Model\MassSchedule;
use Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request;

use function filter_var;

class MassSchedulePlugin
{
    /**
     * @var \Magento\Framework\EntityManager\EntityManager
     */
    private $entityManager;

    /** @var \Magento\Framework\Webapi\Rest\Request */
    private $request;

    /** @var \Inriver\Adapter\Model\CallbackOperationRepository */
    private $callbackOperationRepository;

    /** @var \Inriver\Adapter\Model\Data\CallbackOperationFactory */
    private $callbackOperationFactory;

    /** @var \Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory */
    private $operationCollectionFactory;

    /** @var \Magento\AsynchronousOperations\Model\BulkSummaryFactory */
    private $bulkSummaryFactory;

    /** @var \Inriver\Adapter\Model\CallbackRepository  */
    private $callbackRepository;

    /** @var \Inriver\Adapter\Model\Data\CallbackFactory  */
    private $callbackFactory;

    /**
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param \Inriver\Adapter\Model\CallbackRepository $callbackRepository
     * @param \Inriver\Adapter\Model\CallbackOperationRepository $callbackOperationRepository
     * @param \Inriver\Adapter\Model\Data\CallbackFactory $callbackFactory
     * @param \Inriver\Adapter\Model\Data\CallbackOperationFactory $callbackOperationFactory
     * @param \Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory $operationCollection
     * @param \Magento\AsynchronousOperations\Model\BulkSummaryFactory $bulkSummaryFactory
     * @param \Magento\Framework\EntityManager\EntityManager $entityManager
     */
    public function __construct(
        Request                     $request,
        CallbackRepository          $callbackRepository,
        CallbackOperationRepository $callbackOperationRepository,
        CallbackFactory             $callbackFactory,
        CallbackOperationFactory    $callbackOperationFactory,
        CollectionFactory           $operationCollection,
        BulkSummaryFactory          $bulkSummaryFactory,
        EntityManager $entityManager
    ) {
        $this->request = $request;
        $this->callbackOperationRepository = $callbackOperationRepository;
        $this->callbackOperationFactory = $callbackOperationFactory;
        $this->operationCollectionFactory = $operationCollection;
        $this->callbackRepository = $callbackRepository;
        $this->callbackFactory = $callbackFactory;
        $this->bulkSummaryFactory = $bulkSummaryFactory;
        $this->entityManager = $entityManager;
    }

    public function afterPublishMass(
        MassSchedule $subject,
        AsyncResponseInterface $result
    ): AsyncResponseInterface {
        if ($this->callbackUrlProvided()) {
            $bulkUuid = $result->getBulkUuid();

            $operationList = $this->operationCollectionFactory->create()->addFieldToFilter('bulk_uuid', $bulkUuid);
            $callback = $this->createCallBack($bulkUuid);
            foreach ($operationList as $operation) {
                $callbackOperation = $this->callbackOperationFactory->create();
                $callbackOperation->setOperationId((int) $operation->getData('id'));
                $callbackOperation->setOperationKey((int) $operation->getData('operation_key'));
                $this->callbackOperationRepository->save($callbackOperation);
            }
        }

        return $result;
    }

    private function createCallBack(string $bulkUuid): Callback
    {
        $callbackUrl = $this->request->getHeader(InriverCallback::CALLBACK_HEADER);

        $bulkSummary = $this->bulkSummaryFactory->create();
        $this->entityManager->load($bulkSummary, $bulkUuid);

        try {
            $callback = $this->callbackRepository->getByBulkUuid($bulkUuid);
        } catch (NoSuchEntityException $ex) {
            $callback = $this->callbackFactory->create();
            $callback->setBulkUuid($bulkUuid);
            $callback->setCallbackUrl($callbackUrl);
            $callback->setTopic($bulkSummary->getDescription());
        }

        $callback->setNumberOfOperations($bulkSummary->getOperationCount());
        $this->callbackRepository->save($callback);
        return $callback;
    }

    /**
     * Check if a valid callback url was provided
     *
     * @return bool
     */
    private function callbackUrlProvided(): bool
    {
        $callbackUrl = $this->request->getHeader(InriverCallback::CALLBACK_HEADER);

        return filter_var($callbackUrl, FILTER_VALIDATE_URL) !== false;
    }
}
