<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface CallbackInterface extends ExtensibleDataInterface
{
    public const CALLBACK_ID = 'callback_id';
    public const INRIVER_NOTIFIED = 'inriver_notified';
    public const NUMBER_OF_OPERATIONS = 'number_of_operations';
    public const BULK_UUID = 'bulk_uuid';
    public const CALLBACK_URL = 'callback_url';
    public const TOPIC = 'topic';

    /**
     * @param int $callbackId
     *
     * @return \Inriver\Adapter\Api\Data\CallbackInterface
     */
    public function setCallBackId(int $callbackId): CallbackInterface;

    /**
     * @return int|null
     */
    public function getCallBackId(): ?int;

    /**
     * @param int $numberOfOperations
     *
     * @return \Inriver\Adapter\Api\Data\CallbackInterface
     */
    public function setNumberOfOperations(int $numberOfOperations): CallbackInterface;

    /**
     * @return int
     */
    public function getNumberOfOperations(): int;

    /**
     * @param bool $inriverNotified
     *
     * @return \Inriver\Adapter\Api\Data\CallbackInterface
     */
    public function setInriverNotified(bool $inriverNotified): CallbackInterface;

    /**
     * @return bool
     */
    public function getInriverNotified(): bool;

    /**
     * @param string $callbackUrl
     *
     * @return \Inriver\Adapter\Api\Data\CallbackInterface
     */
    public function setCallbackUrl(string $callbackUrl): CallbackInterface;

    /**
     * @return string
     */
    public function getCallbackUrl(): string;

    /**
     * @param string $bulkUuid
     *
     * @return \Inriver\Adapter\Api\Data\CallbackInterface
     */
    public function setBulkUuid(string $bulkUuid): CallbackInterface;

    /**
     * @return string
     */
    public function getBulkUuid(): string;

    /**
     * @param string $topic
     *
     * @return \Inriver\Adapter\Api\Data\CallbackInterface
     */
    public function setTopic(string $topic): CallbackInterface;

    /**
     * @return string
     */
    public function getTopic(): string;
}
