<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface CallbackOperationInterface extends ExtensibleDataInterface
{
    public const CALLBACK_OPERATION_ID = 'callback_operation_id';
    public const MESSAGES = 'messages';
    public const ERROR_COUNT = 'error_count';
    public const OPERATION_ID = 'operation_id';

    /**
     * @return int|null
     */
    public function getCallBackOperationId(): ?int;

    /**
     * @param int $callbackOperationId
     *
     * @return \Inriver\Adapter\Api\Data\CallbackOperationInterface
     */
    public function setCallBackOperationId(int $callbackOperationId): CallbackOperationInterface;

    /**
     * @return int|null
     */
    public function getCallBackId(): ?int;

    /**
     * @param int $callbackId
     *
     * @return \Inriver\Adapter\Api\Data\CallbackOperationInterface
     */
    public function setCallBackId(int $callbackId): CallbackOperationInterface;

    /**
     * @param int $errorCount
     *
     * @return \Inriver\Adapter\Api\Data\CallbackOperationInterface
     */
    public function setErrorCount(int $errorCount): CallbackOperationInterface;

    /**
     * @return int
     */
    public function getErrorCount(): int;

    /**
     * @param string $messages
     *
     * @return \Inriver\Adapter\Api\Data\CallbackOperationInterface
     */
    public function setMessages(string $messages): CallbackOperationInterface;

    /**
     * @return string
     */
    public function getMessages(): string;

    /**
     * Get the operation id
     *
     * @return int
     */
    public function getOperationId(): int;

    /**
     * Set operation id
     *
     * @param int $operationId
     *
     * @return \Inriver\Adapter\Api\Data\CallbackOperationInterface
     */
    public function setOperationId(int $operationId): CallbackOperationInterface;
}
