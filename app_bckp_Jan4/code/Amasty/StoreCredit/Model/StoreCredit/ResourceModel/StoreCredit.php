<?php

namespace Amasty\StoreCredit\Model\StoreCredit\ResourceModel;

use Amasty\StoreCredit\Api\Data\StoreCreditInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StoreCredit extends AbstractDb
{
    const TABLE_NAME = 'amasty_store_credit';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, StoreCreditInterface::STORE_CREDIT_ID);
    }
}
