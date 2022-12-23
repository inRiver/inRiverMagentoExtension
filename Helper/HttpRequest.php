<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Helper;

use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Laminas\Uri\Http;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class HttpRequest Http Request
 */
class HttpRequest
{
    /** @var \GuzzleHttp\Psr7\ResponseFactory */
    private $responseFactory;

    /** @var \GuzzleHttp\ClientFactory */
    private $clientFactory;

    /** @var \Laminas\Uri\Http */
    private $http;

    /**
     * @param \GuzzleHttp\ClientFactory $clientFactory
     * @param \GuzzleHttp\Psr7\ResponseFactory $responseFactory
     * @param \Laminas\Uri\Http $http
     */
    public function __construct(
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory,
        Http $http
    ) {
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->http = $http;
    }

    /**
     * Do API request with provided params
     *
     * @param string $url
     * @param array $params
     * @param string $requestMethod
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function sendRequest(
        string $url,
        array $params = [],
        string $requestMethod = Request::HTTP_METHOD_PUT
    ): Response {
        $parseUrl = $this->http->parse($url);
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => $parseUrl->getScheme() . '://' . $parseUrl->getHost(),
        ]]);

        try {
            $response = $client->request(
                $requestMethod,
                $url,
                $params
            );
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (GuzzleException $exception) {
            $response = $this->responseFactory->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage(),
            ]);
        }

        return $response;
    }
}
