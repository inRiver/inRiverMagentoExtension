<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data\OptionsByAttributeInterface;

/**
 * Interface AttributeOptionInterface
 */
interface OptionInterface
{
    /**
     * Get admin value
     *
     * @return string
     */
    public function getAdminValue(): string;

    /**
     * Set admin value
     *
     * @param string $adminValue
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface
     */
    public function setAdminValue(string $adminValue): OptionInterface;

    /**
     * Get values
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface\ValuesInterface[]
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getValues(): array;

    /**
     * Set values
     *
     * @param \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface\ValuesInterface[] $values
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function setValues(array $values): OptionInterface;
}
