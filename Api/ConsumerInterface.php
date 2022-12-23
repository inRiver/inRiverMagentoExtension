<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

use Magento\Framework\MessageQueue\ConsumerInterface as MagentoConsumerInterface;

/**
 * Interface ConsumerInterface
 */
interface ConsumerInterface extends MagentoConsumerInterface
{
    /**
     * Get operation processor
     *
     * @return \Inriver\Adapter\Api\OperationProcessorInterface
     */
    public function getOperationProcessor(): OperationProcessorInterface;
}
