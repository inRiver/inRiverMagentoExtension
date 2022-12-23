<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Logger;

use Magento\Framework\Logger\Handler\Base;

/**
 * Class Handler Handler
 */
class Handler extends Base
{
    /** @var string */
    protected $fileName = '/var/log/inriver.log';
}
