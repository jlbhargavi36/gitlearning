<?php

namespace Amasty\StoreCredit\Block\Adminhtml;

class Grid extends \Magento\Backend\Block\Template
{
    public function toHtml()
    {
        return $this->getChildHtml('amstorecredit-history');
    }
}
