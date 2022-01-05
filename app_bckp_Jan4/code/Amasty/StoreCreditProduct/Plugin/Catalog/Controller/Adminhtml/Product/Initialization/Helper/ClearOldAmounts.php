<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Plugin\Catalog\Controller\Adminhtml\Product\Initialization\Helper;

use Amasty\StoreCreditProduct\Model\Product\Attributes;
use Amasty\StoreCreditProduct\Model\Product\Type\StoreCreditProductType;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper as InitializationHelper;
use Magento\Catalog\Model\Product;

class ClearOldAmounts
{
    /**
     * @param InitializationHelper $subject
     * @param Product $product
     * @param array $productData
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeInitializeFromData(InitializationHelper $subject, Product $product, array $productData): array
    {
        if (($product->getTypeId() === StoreCreditProductType::PRODUCT_TYPE)
            && !isset($productData[Attributes::PRODUCT_PRICES])
        ) {
            $productData[Attributes::PRODUCT_PRICES] = [];
        }

        return [$product, $productData];
    }
}
