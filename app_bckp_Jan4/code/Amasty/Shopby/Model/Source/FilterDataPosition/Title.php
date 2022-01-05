<?php

namespace Amasty\Shopby\Model\Source\FilterDataPosition;

use Amasty\Shopby\Model\Source;

/**
 * Class Title
 * @package Amasty\Shopby\Model\Source\FilterDataPosition
 */
class Title extends Source\AbstractFilterDataPosition implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return mixed|void
     */
    protected function _setLabel()
    {
        $this->_label = __('Category Name');
    }
}
