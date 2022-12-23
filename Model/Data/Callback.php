<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Inriver\Adapter\Api\Data\CallbackInterface;
use Inriver\Adapter\Model\ResourceModel\Callback as ResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Callback
 * Call Model for saving
 */
class Callback extends AbstractModel implements CallbackInterface
{
    /** @var string */
    protected $_idFieldName = self::CALLBACK_ID;

    public function setCallBackId(int $callbackId): CallbackInterface
    {
        return $this->setData(self::CALLBACK_ID, $callbackId);
    }

    public function getCallBackId(): ?int
    {
        return $this->getData(self::CALLBACK_ID) ? (int) $this->getData(self::CALLBACK_ID) : null;
    }

    public function setInriverNotified(bool $inriverNotified): CallbackInterface
    {
        return $this->setData(self::INRIVER_NOTIFIED, $inriverNotified);
    }

    public function getInriverNotified(): bool
    {
        return $this->getData(self::INRIVER_NOTIFIED);
    }

    public function setCallbackUrl(string $callbackUrl): CallbackInterface
    {
        return $this->setData(self::CALLBACK_URL, $callbackUrl);
    }

    public function getCallbackUrl(): string
    {
        return $this->getData(self::CALLBACK_URL) ?? '';
    }

    public function setBulkUuid(string $bulkUuid): CallbackInterface
    {
        return $this->setData(self::BULK_UUID, $bulkUuid);
    }

    public function getBulkUuid(): string
    {
        return $this->getData(self::BULK_UUID) ?? '';
    }

    public function setTopic(string $topic): CallbackInterface
    {
        return $this->setData(self::TOPIC, $topic);
    }

    public function getTopic(): string
    {
        return $this->getData(self::TOPIC) ?? '';
    }

    public function setNumberOfOperations(int $numberOfOperations): CallbackInterface
    {
        return $this->setData(self::NUMBER_OF_OPERATIONS, $numberOfOperations);
    }

    public function getNumberOfOperations(): int
    {
        return (int) $this->getData(self::NUMBER_OF_OPERATIONS);
    }

    /** @noinspection ReturnTypeCanBeDeclaredInspection */
    //phpcs:ignore SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }
}
