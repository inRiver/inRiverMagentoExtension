<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

/**
 * Interface ProductsImportRequestInterface
 */
interface ProductsImportRequestInterface
{
    /**
     * Url of the CSV file
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Set URL of the CSV file
     *
     * @param string $url
     *
     * @return \Inriver\Adapter\Api\Data\ProductsImportRequestInterface
     */
    public function setUrl(string $url): ProductsImportRequestInterface;
}
