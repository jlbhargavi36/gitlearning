<?php
/**
 * Copyright © 2015 Aceturtle. All rights reserved.
 */

namespace Aceturtle\Employeediscount\Model;

class Items extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Aceturtle\Employeediscount\Model\Resource\Items');
    }
}
