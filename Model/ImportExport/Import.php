<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 * This file applies minor modifications to native Magento code.
 * It should be kept in sync with the latest Magento version.
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\ImportExport;

use Inriver\Adapter\Helper\Import as InriverImportHelper;
use Inriver\Adapter\Model\Import\Product as InriverProductImport;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Model\Import\AbstractEntity as ImportAbstractEntity;
use Magento\ImportExport\Model\Import as ParentImport;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Throwable;

use function __;

class Import extends ParentImport
{
    /**
     * @return bool|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isImportTypeDisable(): ?bool
    {
        if ($this->_getEntityAdapter() instanceof InriverProductImport) {
            return $this->_getEntityAdapter()->isImportTypeDisable();
        }

        return false;
    }

    /**
     * Overwrite Magento
     * Create instance of entity adapter and return it
     *
     * @return \Magento\ImportExport\Model\Import\Entity\AbstractEntity|\Magento\ImportExport\Model\Import\AbstractEntity
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getEntityAdapter()
    {
        if (!$this->_entityAdapter) {
            if ($this->getEntity() === InriverImportHelper::INRIVER_ENTITY) {
                $entities = $this->_importConfig->getEntities();

                if (isset($entities[$this->getEntity()])) {
                    try {
                        $this->_entityAdapter = $this->_entityFactory->create($entities[$this->getEntity()]['model']);
                    } catch (Throwable $e) {
                        $this->_logger->critical($e);

                        throw new LocalizedException(
                            __('Please enter a correct entity model.')
                        );
                    }

                    if (
                        !$this->_entityAdapter instanceof AbstractEntity &&
                        !$this->_entityAdapter instanceof ImportAbstractEntity
                    ) {
                        throw new LocalizedException(
                            __(
                                'The entity adapter object must be an instance of %1 or %2.',
                                AbstractEntity::class,
                                ImportAbstractEntity::class
                            )
                        );
                    }

                    // Restore original import entity and mark import as an Inriver Import
                    $this->setEntity(Product::ENTITY);
                    $this->setData(InriverImportHelper::IS_INRIVER_IMPORT, true);
                    $this->_entityAdapter->setIsInriverImportForProductTypeModel();

                    // check for entity codes integrity
                    if ($this->getEntity() !== $this->_entityAdapter->getEntityTypeCode()) {
                        throw new LocalizedException(
                            __('The input entity code is not equal to entity adapter code.')
                        );
                    }
                } else {
                    throw new LocalizedException(__('Please enter a correct entity.'));
                }
                $this->_entityAdapter->setParameters($this->getData());
            } else {
                return parent::_getEntityAdapter();
            }
        }

        return $this->_entityAdapter;
    }
}
