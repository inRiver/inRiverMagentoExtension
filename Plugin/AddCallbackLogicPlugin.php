<?php

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

class AddCallbackLogicPlugin
{
    public const BULK_UUID_COLUMN = 'bulk_uuid';
    public const OPERATION_KEY_COLUMN = 'operation_key';

    /** @var \Inriver\Adapter\Helper\InriverCallback */
    private $inriverCallback;

    /** @var \Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory */
    private $operationCollectionFactory;

    /**
     * @param \Inriver\Adapter\Helper\InriverCallback $inriverCallback
     * @param \Magento\AsynchronousOperations\Model\ResourceModel\Operation\CollectionFactory
     *        $operationCollectionFactory
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
     * @param string $bulkUuid
     * @param int $operationKey
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
        string $bulkUuid,
        int $operationKey,
        int $status,
        ?int $errorCode = null,
        ?string $message = null,
        $data = null, //phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        $resultData = null
    ): void {
        if ($bulkUuid !== '') {
            $operationId = $this->getOperationIdByBulkUuidAndOperationKey($bulkUuid, $operationKey);

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
     * @param string $bulkUuid
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
        string $bulkUuid
    ): bool {
        if ($bulkUuid !== '') {
            $this->inriverCallback->returnResponseToInriverAfterAsyncOperations($bulkUuid);
        }

        return $result;
    }

    /**
     * @param string $bulkUuid
     * @param int $operationKey
     *
     * @return int
     */
    private function getOperationIdByBulkUuidAndOperationKey(string $bulkUuid, int $operationKey): int
    {
        /** @var \Magento\AsynchronousOperations\Model\ResourceModel\Operation\Collection $operation */
        $operation = $this->operationCollectionFactory->create()
            ->addFieldToFilter(self::BULK_UUID_COLUMN, $bulkUuid)
            ->addFieldToFilter(self::OPERATION_KEY_COLUMN, $operationKey)
            ->getFirstItem();

        return (int) $operation->getData('id') ?? 0;
    }
}
