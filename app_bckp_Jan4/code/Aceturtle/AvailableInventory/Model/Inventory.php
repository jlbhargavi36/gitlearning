<?php

namespace Aceturtle\AvailableInventory\Model;

use Magento\Framework\Model\AbstractModel;

class Inventory extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Aceturtle\AvailableInventory\Model\ResourceModel\Inventory');
    }
}
