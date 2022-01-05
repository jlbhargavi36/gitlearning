<?php

declare(strict_types=1);

namespace Amasty\Xsearch\Model\ResourceModel;

class UserSearch extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'amasty_xsearch_users_search';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, 'id');
    }
}
