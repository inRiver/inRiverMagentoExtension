<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class CategoryPimUniqueId
 * Data patch for category so we can more easily track between inriver and magento
 */
class CategoryPimUniqueId implements DataPatchInterface
{
    public const CATEGORY_PIM_UNIQUE_ID = 'pim_unique_id';

    /** @var \Magento\Eav\Setup\EavSetupFactory */
    private $eavSetupFactory;

    /** @var \Magento\Framework\Setup\ModuleDataSetupInterface */
    private $moduleDataSetup;

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Get aliases
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Apply patch
     *
     * @return \Magento\Framework\Setup\Patch\DataPatchInterface|void
     * @throws \Inriver\Adapter\Setup\Patch\Data\LocalizedException
     * @throws \Inriver\Adapter\Setup\Patch\Data\Zend_Validate_Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(Category::ENTITY, self::CATEGORY_PIM_UNIQUE_ID, [
            'type' => 'varchar',
            'label' => 'PIM Unique ID',
            'input' => 'text',
            'visible' => false,
            'visible_on_front' => false,
            'default' => '',
            'required' => false,
            'unique' => false,
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'group' => 'General Information',
        ]);
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Get dependencies
     *
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
