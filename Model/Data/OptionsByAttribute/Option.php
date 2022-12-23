<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data\OptionsByAttribute;

use Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface;

/**
 * Class Option
 * Attribute option value class for saves
 */
class Option implements OptionInterface
{
    /** @var string */
    protected $adminValue;

    /** @var \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface\ValuesInterface[] */
    protected $values;

    /**
     * Get admin value
     *
     * @return string
     */
    public function getAdminValue(): string
    {
        return $this->adminValue;
    }

    /**
     * Set admin value
     *
     * @param string $adminValue
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface
     */
    public function setAdminValue(string $adminValue): OptionInterface
    {
        $this->adminValue = $adminValue;

        return $this;
    }

    /**
     * Get values
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface\ValuesInterface[]
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Set values
     *
     * @param \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface\ValuesInterface[] $values
     *
     * @return \Inriver\Adapter\Api\Data\OptionsByAttributeInterface\OptionInterface
     *
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function setValues(array $values): OptionInterface
    {
        $this->values = $values;

        return $this;
    }
}
