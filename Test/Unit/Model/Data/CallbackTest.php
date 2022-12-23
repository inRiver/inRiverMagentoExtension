<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 * @noinspection PhpDeprecationInspection
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data;

use Inriver\Adapter\Model\Data\Callback as AdapterCallback;
use Inriver\Adapter\Model\ResourceModel\Callback as CallbackResource;
use Inriver\Adapter\Model\ResourceModel\Callback\Collection;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use PHPUnit\Framework\TestCase;

class CallbackTest extends TestCase
{
    /** @var \Magento\Framework\Model\Context|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $context;

    /** @var \Magento\Framework\Registry|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $registry;

    /** @var \Magento\Framework\Model\ResourceModel\AbstractResource|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $resource;

    /** @var \Inriver\Adapter\Model\ResourceModel\Callback\Collection|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $resourceCollection;

    public function testCallBackId(): void
    {
        $callbackId = 1;
        $callback = $this->getCallback();
        $callback->setCallBackId($callbackId);
        $this->assertEquals(
            $callbackId,
            $callback->getCallBackId()
        );
    }

    public function testInriverNotified(): void
    {
        $inriverNotified = true;
        $callback = $this->getCallback();
        $callback->setInriverNotified($inriverNotified);
        $this->assertEquals(
            $inriverNotified,
            $callback->getInriverNotified()
        );
    }

    public function testNumberOfOperation(): void
    {
        $numberOfOperation = 42;
        $callback = $this->getCallback();
        $callback->setNumberOfOperations($numberOfOperation);
        $this->assertEquals(
            $numberOfOperation,
            $callback->getNumberOfOperations()
        );
    }

    public function testCallbackUrl(): void
    {
        $callBackUrl = 'https://test.test//rest/all/async/bulk/V1/categories/byId';
        $callback = $this->getCallback();
        $callback->setCallbackUrl($callBackUrl);
        $this->assertEquals(
            $callBackUrl,
            $callback->getCallbackUrl()
        );
    }

    public function testBulkUuid(): void
    {
        $bulkUuid = '64bd75e0-afcc-41a7-ac77-3c0d91818bd7';
        $callback = $this->getCallback();
        $callback->setBulkUuid($bulkUuid);
        $this->assertEquals(
            $bulkUuid,
            $callback->getBulkUuid()
        );
    }

    public function testTopic(): void
    {
        $topic = 'Alpha';
        $callback = $this->getCallback();
        $callback->setTopic($topic);
        $this->assertEquals(
            $topic,
            $callback->getTopic()
        );
    }

    protected function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->registry = $this->createMock(Registry::class);
        $this->resource = $this->createMock(CallbackResource::class);
        $this->resourceCollection = $this->createMock(Collection::class);
    }

    private function getCallback(): AdapterCallback
    {
        return new AdapterCallback(
            $this->context,
            $this->registry,
            $this->resource,
            $this->resourceCollection
        );
    }
}
