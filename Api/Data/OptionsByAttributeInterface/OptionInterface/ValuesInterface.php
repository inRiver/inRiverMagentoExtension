<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface;

/**
 * Interface ValuesInterface
 */
interface ValuesInterface
{
    /**
     * Get store view code
     *
     * @return string
     */
    public function getStoreViewCode(): string;

    /**
     * Set store view code
     *
     * @param string $storeViewCode
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface\ValuesInterface
     */
    public function setStoreViewCode(string $storeViewCode): ValuesInterface;

    /**
     * Get value
     *
     * @return string
     */
    public function getValue(): string;

    /**
     * Set value
     *
     * @param string $value
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface\ValuesInterface
     */
    public function setValue(string $value): ValuesInterface;

    /**
     * Get value
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Set value
     *
     * @param string $description
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface\ValuesInterface
     */
    public function setDescription(?string $description): ValuesInterface;
}
