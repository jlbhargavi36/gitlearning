<?php
/**
 * Copyright © 2015 Aceturtle. All rights reserved.
 */

namespace Aceturtle\Employeediscount\Model\Resource\Items;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aceturtle\Employeediscount\Model\Items', 'Aceturtle\Employeediscount\Model\Resource\Items');
    }
}
