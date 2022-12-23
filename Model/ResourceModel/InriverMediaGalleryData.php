<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class InriverMediaGalleryData extends AbstractDb
{
    /** @var bool */
    protected $_isPkAutoIncrement = false;

    //phpcs:ignore SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
    public function _construct(): void
    {
        $this->_init('catalog_product_entity_media_gallery_value_inriver', 'value_id');
    }
}
