<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Helper;

use Inriver\Adapter\Api\CallbackRepositoryInterface;
use Inriver\Adapter\Api\Data\CallbackInterface;
use Inriver\Adapter\Api\Data\CallbackInterfaceFactory;
use Inriver\Adapter\Helper\InriverRequest;
use Inriver\Adapter\Model\Data\Callback;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request;
use PHPUnit\Framework\TestCase;

class InriverRequestTest extends TestCase
{
    private const CALLBACK_URL = 'https://test.test//rest/all/async/bulk/V1/categories/byId';
    private const BULK_UUID = '64bd75e0-afcc-41a7-ac77-3c0d91818bd7';
    private const TOPIC = 'This is a topic';
    private const NUMBER_OF_OPERATION_SIX = 6;

    /** @var \Inriver\Adapter\Api\CallbackRepositoryInterface|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $callbackRepository;

    /** @var \Inriver\Adapter\Model\Data\Callback|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $callback;

    /** @var \Inriver\Adapter\Model\Data\Callback|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $newCallback;

    /** @var \Inriver\Adapter\Api\Data\CallbackInterfaceFactory|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $callbackInterfaceFactory;

    /** @var \Magento\Framework\Webapi\Rest\Request|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $request;

    public function testHeaderInriverNotPresent(): void
    {
        $this->request->method('getHeader')->willReturn(false);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->getInriverRequest()->captureCallBackUrlFromInriver(
            self::BULK_UUID,
            self::NUMBER_OF_OPERATION_SIX,
            self::TOPIC
        );

        $this->assertEquals(null, $this->callback->getCallBackId());
    }

    public function testHeaderInriverPresentWithBulkUuid(): void
    {
        $this->request->method('getHeader')->willReturn(self::CALLBACK_URL);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->getInriverRequest()->captureCallBackUrlFromInriver(
            self::BULK_UUID,
            self::NUMBER_OF_OPERATION_SIX,
            self::TOPIC
        );

        $this->assertEquals(
            self::NUMBER_OF_OPERATION_SIX,
            $this->callback->getData(CallbackInterface::NUMBER_OF_OPERATIONS)
        );
        $this->assertEquals('', $this->callback->getTopic());
        $this->assertEquals('', $this->callback->getBulkUuid());
    }

    public function testBulkUuidDoesntExist(): void
    {
        $this->request->method('getHeader')->willReturn(self::CALLBACK_URL);
        $this->callbackRepository->method('getByBulkUuid')->willThrowException(new NoSuchEntityException());

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->getInriverRequest()->captureCallBackUrlFromInriver(
            self::BULK_UUID,
            self::NUMBER_OF_OPERATION_SIX,
            self::TOPIC
        );

        $this->assertEquals(
            self::NUMBER_OF_OPERATION_SIX,
            $this->newCallback->getData(CallbackInterface::NUMBER_OF_OPERATIONS)
        );
        $this->assertEquals(self::TOPIC, $this->newCallback->getTopic());
        $this->assertEquals(self::BULK_UUID, $this->newCallback->getBulkUuid());
    }

    protected function setUp(): void
    {
        $this->callback = $this->createPartialMock(
            Callback::class,
            []
        );
        $this->newCallback = $this->createPartialMock(
            Callback::class,
            []
        );
        $this->callbackInterfaceFactory = $this->createMock(CallbackInterfaceFactory::class);
        $this->callbackInterfaceFactory->method('create')->willReturn($this->newCallback);
        $this->callbackRepository = $this->createMock(CallbackRepositoryInterface::class);
        $this->callbackRepository->method('getByBulkUuid')->willReturn($this->callback);
        $this->request = $this->createMock(Request::class);
    }

    /**
     * @return \Inriver\Adapter\Helper\InriverRequest
     */
    private function getInriverRequest(): InriverRequest
    {
        return new InriverRequest(
            $this->callbackRepository,
            $this->callbackInterfaceFactory,
            $this->request
        );
    }
}
