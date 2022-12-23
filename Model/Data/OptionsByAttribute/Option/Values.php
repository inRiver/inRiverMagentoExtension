<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data\OptionsByAttribute\Option;

use Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface\ValuesInterface;
use Magento\Framework\Exception\LocalizedException;

use function __;

/**
 * Class Values
 * Save Option value class that helps with the creation of options through the new API call created for inriver
 */
class Values implements ValuesInterface
{
    /** @var string */
    protected $storeViewCode;

    /** @var string */
    protected $value;

    /** @var string */
    protected $description;

    /**
     * Get store view code
     *
     * @return string
     */
    public function getStoreViewCode(): string
    {
        return $this->storeViewCode;
    }

    /**
     * Set store view code
     *
     * @param string $storeViewCode
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface\ValuesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setStoreViewCode(string $storeViewCode): ValuesInterface
    {
        if ($storeViewCode === '') {
            throw new LocalizedException(__('store_view_code should not be empty'));
        }

        $this->storeViewCode = $storeViewCode;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface\ValuesInterface
     */
    public function setValue(string $value): ValuesInterface
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set value
     *
     * @param string|null $description
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface\ValuesInterface
     */
    public function setDescription(?string $description): ValuesInterface
    {
        $this->description = $description;

        return $this;
    }
}
