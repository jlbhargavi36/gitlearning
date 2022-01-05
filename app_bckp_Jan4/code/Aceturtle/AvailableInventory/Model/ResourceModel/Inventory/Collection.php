<?php

namespace Aceturtle\AvailableInventory\Model\ResourceModel\Inventory;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Aceturtle\AvailableInventory\Model\Inventory',
            'Aceturtle\AvailableInventory\Model\ResourceModel\Inventory'
        );
    }
}
