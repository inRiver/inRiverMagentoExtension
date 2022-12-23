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

use Inriver\Adapter\Helper\InriverRequest;
use Inriver\Adapter\Model\CallbackOperationRepository;
use Inriver\Adapter\Model\Data\CallbackOperationFactory;
use Magento\AsynchronousOperations\Model\BulkManagement;
use Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory;
use Magento\Framework\Webapi\Rest\Request;

use function filter_var;

class BulkManagementPlugin
{
    /** @var \Magento\Framework\Webapi\Rest\Request */
    protected $request;

    /** @var \Inriver\Adapter\Model\CallbackOperationRepository */
    protected $callbackOperationRepository;

    /** @var \Inriver\Adapter\Model\Data\CallbackOperationFactory */
    protected $callbackOperationFactory;

    /** @var \Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory */
    private $operationCollectionFactory;

    /**
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param \Inriver\Adapter\Model\CallbackOperationRepository $callbackOperationRepository
     * @param \Inriver\Adapter\Model\Data\CallbackOperationFactory $callbackOperationFactory
     * @param \Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory $operationCollection
     */
    public function __construct(
        Request $request,
        CallbackOperationRepository $callbackOperationRepository,
        CallbackOperationFactory $callbackOperationFactory,
        CollectionFactory $operationCollection
    ) {
        $this->request = $request;
        $this->callbackOperationRepository = $callbackOperationRepository;
        $this->callbackOperationFactory = $callbackOperationFactory;
        $this->operationCollectionFactory = $operationCollection;
    }
    // @codingStandardsIgnoreStart

    /**
     * @param BulkManagement $subject
     * @param string $bulkUuid
     * @param array $operations
     * @param string|null $description
     * @param string|null $userId
     *
     * @return void
     * @throws CouldNotSaveException
     */

    public function beforeScheduleBulk(
        BulkManagement $subject,
        string $bulkUuid,
        array $operations, // @phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        ?string $description, // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        ?string $userId = null // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    ): void {
        if ($this->callbackUrlProvided()) {
            $operationList = $this->operationCollectionFactory->create()
                ->addFieldToFilter('bulk_uuid', $bulkUuid);

            foreach ($operationList as $operation) {
                $callbackOperation = $this->callbackOperationFactory->create();
                $callbackOperation->setOperationId((int) $operation->getData('id'));
                $this->callbackOperationRepository->save($callbackOperation);
            }
        }
    }

    /**
     * Check if a valid callback url was provided
     *
     * @return bool
     */
    private function callbackUrlProvided(): bool
    {
         $callbackUrl = $this->request->getHeader(InriverRequest::CALLBACK_HEADER);

         return filter_var($callbackUrl, FILTER_VALIDATE_URL) !== false;
    }
}
