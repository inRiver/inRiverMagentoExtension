<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Inriver\Adapter\Api\Data\CallbackInterface;
use Inriver\Adapter\Api\Data\CallbackOperationInterface;
use Inriver\Adapter\Model\ResourceModel\CallbackOperation as ResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CallbackOperation
 * Callback operation model for database save
 */
class CallbackOperation extends AbstractModel implements CallbackOperationInterface
{
    /** @var string */
    protected $_idFieldName = self::CALLBACK_OPERATION_ID;

    public function getCallBackOperationId(): ?int
    {
        return $this->getData(self::CALLBACK_OPERATION_ID) ?
            (int) $this->getData(self::CALLBACK_OPERATION_ID) : null;
    }

    public function setCallBackOperationId(int $callbackOperationId): CallbackOperationInterface
    {
        return $this->setData(self::CALLBACK_OPERATION_ID, $callbackOperationId);
    }

    public function setCallBackId(int $callbackId): CallbackOperationInterface
    {
        return $this->setData(CallbackInterface::CALLBACK_ID, $callbackId);
    }

    public function getCallBackId(): ?int
    {
        return $this->getData(CallbackInterface::CALLBACK_ID) ?
            (int) $this->getData(CallbackInterface::CALLBACK_ID) : null;
    }

    public function setErrorCount(int $errorCount): CallbackOperationInterface
    {
        return $this->setData(self::ERROR_COUNT, $errorCount);
    }

    public function getErrorCount(): int
    {
        return (int) $this->getData(self::ERROR_COUNT);
    }

    public function setMessages(string $messages): CallbackOperationInterface
    {
        return $this->setData(self::MESSAGES, $messages);
    }

    public function getMessages(): string
    {
        return $this->getData(self::MESSAGES) ?? '';
    }

    /**
     * Get the operation id
     *
     * @return int
     */
    public function getOperationId(): int
    {
        return (int) $this->getData(CallbackOperationInterface::OPERATION_ID);
    }

    /**
     * Set operation id
     *
     * @param int $operationId
     *
     * @return \Inriver\Adapter\Api\Data\CallbackOperationInterface
     */
    public function setOperationId(int $operationId): CallbackOperationInterface
    {
        $this->setData(CallbackOperationInterface::OPERATION_ID, $operationId);

        return $this;
    }

    /** @noinspection ReturnTypeCanBeDeclaredInspection */
    //phpcs:ignore SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }
}
