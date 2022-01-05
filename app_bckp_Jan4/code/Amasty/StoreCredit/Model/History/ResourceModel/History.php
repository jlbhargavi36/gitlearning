<?php

namespace Amasty\StoreCredit\Model\History\ResourceModel;

use Amasty\StoreCredit\Api\Data\HistoryInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class History extends AbstractDb
{
    const TABLE_NAME = 'amasty_store_credit_history';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, HistoryInterface::HISTORY_ID);
    }

    public function getNextCustomerHistoryId($customerId)
    {
        $select = $this->getConnection()->select()
            ->from(['history' => $this->getMainTable()])
            ->where('history.' . HistoryInterface::CUSTOMER_ID . ' = ?', (int)$customerId)
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns('history.' . HistoryInterface::CUSTOMER_HISTORY_ID)
            ->order('history.' . HistoryInterface::CUSTOMER_HISTORY_ID . ' DESC')
            ->limit(1);

        if ($row = $this->getConnection()->fetchRow($select)) {
            return ++$row[HistoryInterface::CUSTOMER_HISTORY_ID];
        }

        return 1;
    }
}
