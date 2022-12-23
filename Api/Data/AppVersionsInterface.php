<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

/**
 * Interface AppVersionsInterface
 */
interface AppVersionsInterface
{
    /**
     * Return current Magento version
     *
     * @return string
     */
    public function getMagentoVersion(): string;

    /**
     * Return current Magento edition
     *
     * @return string
     */
    public function getMagentoEdition(): string;

    /**
     * Return current inRiver Adapter module version
     *
     * @return string
     */
    public function getAdapterVersion(): string;
}
