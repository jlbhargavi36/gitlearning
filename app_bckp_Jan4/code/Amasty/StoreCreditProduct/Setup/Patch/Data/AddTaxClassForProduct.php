<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Setup\Patch\Data;

use Amasty\StoreCreditProduct\Model\Product\Type\StoreCreditProductType;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddTaxClassForProduct implements DataPatchInterface
{
    const TAX_CLASS_ATTRIBUTE = 'tax_class_id';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply(): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $productEntityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);

        $applyTo = explode(
            ',',
            $eavSetup->getAttribute($productEntityTypeId, self::TAX_CLASS_ATTRIBUTE, 'apply_to')
        );
        if (!in_array(StoreCreditProductType::PRODUCT_TYPE, $applyTo)) {
            $applyTo[] = StoreCreditProductType::PRODUCT_TYPE;
            $eavSetup->updateAttribute(
                $productEntityTypeId,
                self::TAX_CLASS_ATTRIBUTE,
                'apply_to',
                implode(',', $applyTo)
            );
        }
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
