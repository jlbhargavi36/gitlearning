<?php

namespace Amasty\Mostviewed\Model\ResourceModel\Analytics;

use Amasty\Mostviewed\Api\Data\ClickInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Click extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ClickInterface::MAIN_TABLE, ClickInterface::ID);
    }
}
