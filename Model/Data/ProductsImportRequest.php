<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Inriver\Adapter\Api\Data\ProductsImportRequestInterface;
use Magento\Framework\Exception\LocalizedException;

use function __;
use function filter_var;

use const FILTER_VALIDATE_URL;

class ProductsImportRequest implements ProductsImportRequestInterface
{
    /** @var string */
    protected $url;

    /**
     * Url of the CSV file
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set URL of the CSV file
     *
     * @param string $url
     *
     * @return \Inriver\Adapter\Api\Data\ProductsImportRequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setUrl(string $url): ProductsImportRequestInterface
    {
        $this->url = $this->validateUrl($url);

        return $this;
    }

    /**
     * Validate url
     *
     * @param string $url
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateUrl(string $url): string
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new LocalizedException(__('Provided URL is invalid'));
        }

        return $url;
    }
}
