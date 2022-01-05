<?php

namespace Amasty\Xsearch\Model;

class UserSearch extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Amasty\Xsearch\Model\ResourceModel\UserSearch::class);
    }
}
