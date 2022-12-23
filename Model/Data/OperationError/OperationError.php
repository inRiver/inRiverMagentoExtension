<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data\OperationError;

use Inriver\Adapter\Api\Data\OperationErrorInterface;

/**
 * Class OperationError
 * Abstract class for operation errors
 */
abstract class OperationError implements OperationErrorInterface
{
    /**
     * Get error as string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getCode() . ': ' . $this->getDescription();
    }
}
