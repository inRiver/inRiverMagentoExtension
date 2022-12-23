<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 * @noinspection PhpDeprecationInspection
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data;

use Inriver\Adapter\Model\Data\CallbackOperation;
use Inriver\Adapter\Model\ResourceModel\CallbackOperation as CallbackOperationResource;
use Inriver\Adapter\Model\ResourceModel\CallbackOperation\Collection;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use PHPUnit\Framework\TestCase;

class CallbackOperationTest extends TestCase
{
    /** @var \Magento\Framework\Model\Context|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $context;

    /** @var \Magento\Framework\Registry|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $registry;

    /** @var \Magento\Framework\Model\ResourceModel\AbstractResource|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $resource;

    /** @var \Inriver\Adapter\Model\ResourceModel\CallbackOperation\Collection|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $resourceCollection;

    public function testCallBackOperationId(): void
    {
        $callbackOperationId = 1;
        $callbackOperation = $this->getCallbackOperation();
        $callbackOperation->setCallBackOperationId($callbackOperationId);
        $this->assertEquals(
            $callbackOperationId,
            $callbackOperation->getCallBackOperationId()
        );
    }

    public function testCallBackId(): void
    {
        $callbackId = 1;
        $callbackOperation = $this->getCallbackOperation();
        $callbackOperation->setCallBackId($callbackId);
        $this->assertEquals(
            $callbackId,
            $callbackOperation->getCallBackId()
        );
    }

    public function testErrorCount(): void
    {
        $errorCount = 42;
        $callbackOperation = $this->getCallbackOperation();
        $callbackOperation->setErrorCount($errorCount);
        $this->assertEquals(
            $errorCount,
            $callbackOperation->getErrorCount()
        );
    }

    public function testMessages(): void
    {
        $messages = 'This is a test';
        $callbackOperation = $this->getCallbackOperation();
        $callbackOperation->setMessages($messages);
        $this->assertEquals(
            $messages,
            $callbackOperation->getMessages()
        );
    }

    protected function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->registry = $this->createMock(Registry::class);
        $this->resource = $this->createMock(CallbackOperationResource::class);
        $this->resourceCollection = $this->createMock(Collection::class);
    }

    private function getCallbackOperation(): CallbackOperation
    {
        return new CallbackOperation(
            $this->context,
            $this->registry,
            $this->resource,
            $this->resourceCollection
        );
    }
}
