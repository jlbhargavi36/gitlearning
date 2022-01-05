<?php
/**
 * Copyright © 2015 Aceturtle. All rights reserved.
 */

namespace Aceturtle\Employeediscount\Model\Resource;

class Items extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aceturtle_emp_coupon', 'id');
    }
}
