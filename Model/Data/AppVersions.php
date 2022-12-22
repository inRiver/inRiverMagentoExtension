<?php

/** @noinspection PhpLanguageLevelInspection */

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Inriver\Adapter\Api\Data\AppVersionsInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Class AppVersions App Versions
 */
class AppVersions implements AppVersionsInterface
{
    public const MODULE_NAME = 'Inriver_Adapter';

    /** @var \Magento\Framework\App\ProductMetadataInterface */
    protected $productMetadata;

    /** @var \Magento\Framework\Module\ModuleListInterface */
    protected $moduleList;

    /**
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList
    ) {
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleList;
    }

    /**
     * Return current Magento version
     *
     * @return string
     */
    public function getMagentoVersion(): string
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * Return current Magento edition
     *
     * @return string
     */
    public function getMagentoEdition(): string
    {
        return $this->productMetadata->getEdition();
    }

    /**
     * Return current inRiver Adapter module version
     *
     * @return string
     */
    public function getAdapterVersion(): string
    {
        return $this->moduleList->getOne(self::MODULE_NAME)['setup_version'];
    }
}
