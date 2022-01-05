<?php

namespace Amasty\Mostviewed\Model\Rule\Condition;

class ProductFactory extends \Magento\CatalogRule\Model\Rule\Condition\ProductFactory
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = \Amasty\Mostviewed\Model\Rule\Condition\Product::class
    ) {
        parent::__construct($objectManager, $instanceName);
        null;//fix coding standard. owerride to change instance
    }
}
