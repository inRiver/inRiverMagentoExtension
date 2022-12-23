<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\ResourceModel;

use Inriver\Adapter\Api\Data\CallbackInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Callback extends AbstractDb
{
    public const TABLE_NAME = 'inriver_async_callback';

    //phpcs:ignore SlevomatCodingStandard.TypeHints.TypeHintDeclaration
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, CallbackInterface::CALLBACK_ID);
    }
}
