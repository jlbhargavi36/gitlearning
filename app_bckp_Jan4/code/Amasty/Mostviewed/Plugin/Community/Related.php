<?php

namespace Amasty\Mostviewed\Plugin\Community;

use Amasty\Mostviewed\Model\OptionSource\BlockPosition;

class Related extends AbstractProduct
{
    /**
     * @param $items
     *
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Framework\Data\Collection
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetItems($object, $items)
    {
        return $this->prepareCollection(BlockPosition::PRODUCT_INTO_RELATED, $items, $object);
    }
}
