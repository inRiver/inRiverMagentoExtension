<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\ResourceModel;

use Inriver\Adapter\Api\Data\CallbackOperationInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CallbackOperation extends AbstractDb
{
    public const TABLE_NAME = 'inriver_async_callback_operation';

    //phpcs:ignore SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, CallbackOperationInterface::CALLBACK_OPERATION_ID);
    }
}
