<?php

/** @noinspection PhpLanguageLevelInspection */

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Plugin;

use Inriver\Adapter\Helper\InriverCallback;
use Magento\AsynchronousOperations\Model\OperationManagement;
use Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory;

/**
 * Class AddCallbackLogicPlugin Add Callback Logic Plugin
 */
class AddCallbackLogicPlugin
{
    /** @var \Inriver\Adapter\Helper\InriverCallback */
    private $inriverCallback;

    /** @var \Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory */
    private $operationCollectionFactory;

    //phpcs:ignoreFile Generic.Files.LineLength.Too.Long
    /**
     * @param \Inriver\Adapter\Helper\InriverCallback $inriverCallback
     * @param \Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory $operationCollectionFactory
     */
    public function __construct(
        InriverCallback $inriverCallback,
        CollectionFactory $operationCollectionFactory
    ) {
        $this->inriverCallback = $inriverCallback;
        $this->operationCollectionFactory = $operationCollectionFactory;
    }

    /**
     * @param \Magento\AsynchronousOperations\Model\OperationManagement $subject
     * @param int $operationId
     * @param int $status
     * @param int|null $errorCode
     * @param string|null $message
     * @param null $data
     * @param null $resultData
     *
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function beforeChangeOperationStatus(
        OperationManagement $subject, //phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        int $operationId,
        int $status,
        ?int $errorCode = null,
        ?string $message = null,
        $data = null, //phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        $resultData = null
    ): void {
        $bulkUuid = $this->getBulkUuidByOperationId($operationId);

        if ($bulkUuid !== '') {
            $this->inriverCallback->createCallbackOperationAfterAsyncOperations(
                $operationId,
                $bulkUuid,
                $status,
                $errorCode,
                $message,
                $resultData
            );
        }
    }

    /**
     * @param \Magento\AsynchronousOperations\Model\OperationManagement $subject
     * @param bool $result
     * @param int $operationId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function afterChangeOperationStatus(
        OperationManagement $subject, // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        bool $result,
        int $operationId
    ): bool {
        $bulkUuid = $this->getBulkUuidByOperationId($operationId);

        if ($bulkUuid !== '') {
            $this->inriverCallback->returnResponseToInriverAfterAsyncOperations($bulkUuid);
        }

        return $result;
    }

    /**
     * @param int $operationId
     *
     * @return string
     */
    private function getBulkUuidByOperationId(int $operationId): string
    {
        /** @var \Magento\AsynchronousOperations\Model\Operation $operation */
        $operation = $this->operationCollectionFactory->create()
            ->addFieldToFilter('id', $operationId)->setPageSize(1)->getFirstItem();

        return $operation->getBulkUuid() ?? '';
    }
}
