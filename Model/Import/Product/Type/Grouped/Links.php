<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 * This file applies minor modifications to native Magento code.
 * It should be kept in sync with the latest Magento version.
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Import\Product\Type\Grouped;

use Inriver\Adapter\Helper\Import as InriverImportHelper;
use Magento\GroupedImportExport\Model\Import\Product\Type\Grouped\Links as parentLinks;

class Links extends parentLinks
{
    /**
     * @return string
     */
    protected function getBehavior(): string
    {
        if ($this->behavior === null) {
            $import = $this->importFactory->create();
            // Added inRiver model trigger
            $import->setData(InriverImportHelper::IS_INRIVER_IMPORT, true);
            $this->behavior = $import->getDataSourceModel()->getBehavior();
        }

        return $this->behavior;
    }
}
