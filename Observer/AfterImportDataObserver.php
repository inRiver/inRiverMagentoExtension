<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Observer;

use Inriver\Adapter\Helper\Import as InriverImportHelper;
use Inriver\Adapter\Model\Import\Product as InriverProductImport;
use Magento\CatalogUrlRewrite\Observer\AfterImportDataObserver as MagentoAfterImportDataObserver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;

class AfterImportDataObserver extends MagentoAfterImportDataObserver
{
    /**
     * Action after data import. Save new url rewrites and remove old if exist.
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     * @throws UrlAlreadyExistsException
     */
    public function execute(Observer $observer)
    {
        $this->import = $observer->getEvent()->getAdapter();

        if ($this->import instanceof InriverProductImport) {
            $parameters = $this->import->getParameters();
            if (isset($parameters['inriver_import_type']) && $parameters['inriver_import_type'] === InriverImportHelper::INRIVER_IMPORT_TYPE_RELATIONS) {
                return;
            }
        }

        parent::execute($observer);
    }
}
