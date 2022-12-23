<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Inriver\Adapter\Helper\HttpRequest;
use Laminas\Uri\Http;
use Magento\Framework\Webapi\Rest\Request;
use PHPUnit\Framework\TestCase;

class HttpRequestTest extends TestCase
{
    /** @var \GuzzleHttp\Psr7\ResponseFactory|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $responseFactory;

    /** @var \GuzzleHttp\ClientFactory|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $clientFactory;

    /** @var \GuzzleHttp\Client|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $client;

    /** @var \Laminas\Uri\Http|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $http;

    /** @var \GuzzleHttp\Psr7\Response|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $responseException;

    /** @var \GuzzleHttp\Psr7\Response|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $responseSuccess;

    /** @var \GuzzleHttp\Exception\RequestException|\Inriver\Adapter\Test\Unit\Helper\MockObject */
    private $exception;

    public function testRealResponse(): void
    {
        $httpRequest = new HttpRequest(
            $this->clientFactory,
            $this->responseFactory,
            $this->http
        );

        $response = $httpRequest->sendRequest('url', [], Request::HTTP_METHOD_PUT);
        $this->assertEquals($this->responseSuccess, $response);
    }

    public function testException(): void
    {
        $this->client->method('request')->willThrowException($this->exception);
        $httpRequest = new HttpRequest(
            $this->clientFactory,
            $this->responseFactory,
            $this->http
        );

        $response = $httpRequest->sendRequest('url', [], Request::HTTP_METHOD_PUT);
        $this->assertEquals($this->responseException, $response);
    }

    protected function setUp(): void
    {
        $this->responseFactory = $this->createMock(ResponseFactory::class);
        $this->responseException = $this->createMock(Response::class);
        $this->responseSuccess = $this->createMock(Response::class);

        $this->exception = $this->createMock(RequestException::class);

        $this->http = $this->createMock(Http::class);
        $this->http->method('parse')->willReturn($this->http);

        $this->clientFactory = $this->createMock(ClientFactory::class);
        $this->client = $this->createMock(Client::class);
        $this->client->method('request')->willReturn($this->responseSuccess);
        $this->clientFactory->method('create')->willReturn($this->client);
        $this->responseFactory->method('create')->willReturn($this->responseException);
    }
}
