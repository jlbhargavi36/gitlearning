<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Setup\Patch\Data;

use Amasty\StoreCreditProduct\Model\Attributes\Backend\Price as StoreCreditPrice;
use Amasty\StoreCreditProduct\Model\Product\Attributes;
use Amasty\StoreCreditProduct\Model\Product\Type\StoreCreditProductType;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Product\Attribute\Backend\Price;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddProductAttributes implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function apply(): void
    {
        $entityType = ProductAttributeInterface::ENTITY_TYPE_CODE;
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
        $categorySetup->addAttribute(
            $entityType,
            Attributes::PRODUCT_PRICES,
            [
                'type' => 'text',
                'label' => 'Amounts',
                'backend' => StoreCreditPrice::class,
                'input' => 'price',
                'source' => '',
                'required' => false,
                'sort_order' => 10,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'group' => Attributes::PRICES_ATTRIBUTE_GROUP,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'apply_to' => StoreCreditProductType::PRODUCT_TYPE,
                'visible' => true
            ]
        );
        $categorySetup->addAttribute(
            $entityType,
            Attributes::ALLOW_OPEN_AMOUNT,
            [
                'backend' => '',
                'frontend' => '',
                'class' => '',
                'source' => '',
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'required' => false,
                'sort_order' => 20,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'group' => Attributes::PRICES_ATTRIBUTE_GROUP,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'input' => 'boolean',
                'label' => 'Open Amount',
                'default' => 0,
                'apply_to' => StoreCreditProductType::PRODUCT_TYPE,
                'visible' => true
            ]
        );
        $categorySetup->addAttribute(
            $entityType,
            Attributes::OPEN_AMOUNT_MIN,
            [
                'type' => 'decimal',
                'label' => 'Open Amount Min Value',
                'backend' => Price::class,
                'input' => 'price',
                'source' => '',
                'required' => false,
                'sort_order' => 30,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'group' => Attributes::PRICES_ATTRIBUTE_GROUP,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'class' => 'validate-number',
                'apply_to' => StoreCreditProductType::PRODUCT_TYPE,
                'visible' => true
            ]
        );
        $categorySetup->addAttribute(
            $entityType,
            Attributes::OPEN_AMOUNT_MAX,
            [
                'type' => 'decimal',
                'label' => 'Open Amount Max Value',
                'backend' => Price::class,
                'input' => 'price',
                'source' => '',
                'required' => false,
                'sort_order' => 40,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'group' => Attributes::PRICES_ATTRIBUTE_GROUP,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'class' => 'validate-number',
                'used_in_product_listing' => true,
                'apply_to' => StoreCreditProductType::PRODUCT_TYPE,
                'visible' => true
            ]
        );
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
