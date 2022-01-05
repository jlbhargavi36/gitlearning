<?php

namespace Aceturtle\AvailableInventory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Inventory extends AbstractDb
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
            $this->_init('shipping_method_inventory', 'row_id');
    }
}
