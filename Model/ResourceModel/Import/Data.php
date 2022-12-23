<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\ResourceModel\Import;

use Magento\ImportExport\Model\ResourceModel\Import\Data as ImportData;

/**
 * Class Data Data
 */
class Data extends ImportData
{
    /**
     * Resource initialization
     *
     * @return void
     *
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    //phpcs:ignore SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
    protected function _construct(): void
    {
        $this->_init('inriver_importexport_importdata', 'id');
    }
}
