<?php

namespace Amasty\Storelocator\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Schedule extends AbstractDb
{
    const TABLE_NAME = 'amasty_amlocator_schedule';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'id');
    }
}
