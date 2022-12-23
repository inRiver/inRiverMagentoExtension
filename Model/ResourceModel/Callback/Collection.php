<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\ResourceModel\Callback;

use Inriver\Adapter\Model\Data\Callback as Model;
use Inriver\Adapter\Model\ResourceModel\Callback as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
