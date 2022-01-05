<?php

namespace Amasty\Mostviewed\Plugin\Community;

use Amasty\Mostviewed\Model\OptionSource\BlockPosition;

class Upsell extends AbstractProduct
{
    /**
     * @param $items
     *
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Framework\Data\Collection
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetItemCollection($object, $items)
    {
        return $this->prepareCollection(BlockPosition::PRODUCT_INTO_UPSELL, $items, $object);
    }
}
