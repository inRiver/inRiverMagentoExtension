<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Magento\ImportExport\Model\Import as MagentoImport;

/**
 * Class ImportProductRelations ImportProductRelations
 */
class ImportProductRelations extends Import
{
    protected const ERROR_LOG_PREFIX = 'InRiver Import product relations';

    /**
     * Set import behavior
     *
     * @return void
     */
    protected function setImportBehavior(): void
    {
        $this->getImportModel()->setData('behavior', MagentoImport::BEHAVIOR_ADD_UPDATE);
    }
}
