<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Class VersionsTest
 *
 * TESTS_WEBSERVICE_APIKEY must be properly set in your test configuration
 * See https://devdocs.magento.com/guides/v2.3/get-started/web-api-functional-testing.html
 */
class VersionsTest extends WebapiAbstract
{
    private const RESOURCE_PATH = '/V1/inriver-adapter/versions';

    public function testGetWithoutAuthentication(): void
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_GET,
                'token' => null,
            ],
        ];

        $this->expectExceptionCode(401);
        $this->_webApiCall($serviceInfo);
    }

    public function testGetWithProperAuthentication(): void
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
        ];

        $data = $this->_webApiCall($serviceInfo);

        $this->assertArrayHasKey('magento_version', $data);
        $this->assertArrayHasKey('magento_edition', $data);
        $this->assertArrayHasKey('adapter_version', $data);
    }
}
