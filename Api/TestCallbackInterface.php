<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

use Inriver\Adapter\Api\Data\ValidationMessageInterface;

interface TestCallbackInterface
{
    /**
     * Test inRiver api callback
     *
     * @param string $validationToken
     * @return \Inriver\Adapter\Api\Data\ValidationMessageInterface
     */
    public function get(string $validationToken): ValidationMessageInterface;
}
