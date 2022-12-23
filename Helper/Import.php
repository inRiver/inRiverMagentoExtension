<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Helper;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Url;
use Magento\CatalogImportExport\Model\Import\Product;
use Magento\CatalogImportExport\Model\Import\Product\SkuProcessor;

use function array_key_exists;
use function str_replace;
use function strtolower;

class Import
{
    public const INRIVER_ENTITY = 'inriver_product';
    public const IS_INRIVER_IMPORT = 'is_inriver_import';

    public const LIST_OF_CHARACTER_TO_DECODE_AND_ENCODE_FOR_IMPORT = [
        'search' => ['%3D', '%2C', '%22', '%7C', '%25', '%0A', '%0D'],
        'replace' => ['=', ',', '"', '|', '%', "\n", "\r"],
    ];
    public const ATTRIBUTES_TYPE_NOT_TO_DECODE_FOR_INSERT = [
        'datetime',
        'multiselect',
        'decimal',
    ];
    public const ATTRIBUTES_NOT_TO_DECODE = [
        'sku',
        Product::COL_VISIBILITY,
        Product::COL_ATTR_SET,
        'status',
        'url_key',
        'name',
        'description',
        'short_description',
        'image',
        'small_image',
        'thumbnail',
        'swatch_image',
    ];

    public const COL_STATUS = 'status';

    /** @var \Magento\CatalogImportExport\Model\Import\Product\SkuProcessor */
    private $skuProcessor;

    /** @var \Magento\Catalog\Model\Product\Url */
    private $productUrl;

    /**
     * @param \Magento\CatalogImportExport\Model\Import\Product\SkuProcessor $skuProcessor
     * @param \Magento\Catalog\Model\Product\Url $productUrl
     */
    public function __construct(
        SkuProcessor $skuProcessor,
        Url $productUrl
    ) {
        $this->skuProcessor = $skuProcessor;
        $this->productUrl = $productUrl;
    }

    /**
     * Check if current import row contains a simple product
     *
     * @param string[] $rowData
     *
     * @return bool
     */
    public function isNewSimpleProductRowWithNoPrice(array $rowData): bool
    {
        return isset($rowData[Product::COL_SKU], $rowData[Product::COL_TYPE])
            && !isset($rowData['price'])
            && array_key_exists(Product::COL_TYPE, $rowData)
            && ($rowData[Product::COL_TYPE] === Type::TYPE_SIMPLE || $rowData[Product::COL_TYPE] === Type::TYPE_VIRTUAL)
            && $this->isNewSku($rowData[Product::COL_SKU]);
    }

    /**
     * @param string[] $rowData
     *
     * @return bool
     */
    public function isNewBundleProductRowWithNoPrice(array $rowData): bool
    {
        return isset($rowData[Product::COL_SKU], $rowData[Product::COL_TYPE])
            && !isset($rowData['price'])
            && (isset($rowData['bundle_price_type']) && $rowData['bundle_price_type'] === 'fixed')
            && array_key_exists(Product::COL_TYPE, $rowData)
            && $rowData[Product::COL_TYPE] === Type::TYPE_BUNDLE
            && $this->isNewSku($rowData[Product::COL_SKU]);
    }

    /**
     * @param array $rowData
     * @return bool
     */
    public function isNewProductRowWithNoPrice(array $rowData): bool
    {
        return $this->isNewSimpleProductRowWithNoPrice($rowData) || $this->isNewBundleProductRowWithNoPrice($rowData);
    }

    /**
     * Check if given sku already exists in the database
     *
     * @param string $sku
     *
     * @return bool
     */
    public function isNewSku(string $sku): bool
    {
        return !isset($this->skuProcessor->getOldSkus()[strtolower($sku)]);
    }

    /**
     * @param string[] $rowData
     *
     * @return bool
     */
    public function isImportTypeDisable(array $rowData): bool
    {
        return !isset($rowData[Product::COL_NAME]) &&
            isset($rowData['product_online']) &&
            (int) $rowData['product_online'] === Status::STATUS_DISABLED;
    }

    /**
     * @param string[] $rowData
     *
     * @return string
     */
    public function getUniqueProductUrl(array $rowData): string
    {
        if (
            isset($rowData[Product::COL_NAME]) &&
            isset($rowData[Product::COL_SKU]) &&
            $rowData[Product::COL_NAME] !== '' &&
            $rowData[Product::COL_SKU] !== ''
        ) {
            return $this->productUrl->formatUrlKey($rowData[Product::COL_NAME] . '-' . $rowData[Product::COL_SKU]);
        }

        return '';
    }

    /**
     * @param string $attributeValue
     *
     * @return string
     */
    public function decodeImportAttributeValue(string $attributeValue): string
    {
        return str_replace(
            self::LIST_OF_CHARACTER_TO_DECODE_AND_ENCODE_FOR_IMPORT['search'],
            self::LIST_OF_CHARACTER_TO_DECODE_AND_ENCODE_FOR_IMPORT['replace'],
            $attributeValue
        );
    }
}
