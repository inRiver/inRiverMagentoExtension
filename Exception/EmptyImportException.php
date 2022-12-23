<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Exception;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class EmptyImportException
 * New Exception for Empty import to have better message
 */
class EmptyImportException extends LocalizedException
{
}
