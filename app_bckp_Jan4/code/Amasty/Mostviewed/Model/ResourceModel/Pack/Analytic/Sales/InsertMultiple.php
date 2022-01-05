<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\ResourceModel\Pack\Analytic\Sales;

use Magento\Framework\App\ResourceConnection;
use Zend_Db_Exception;

class InsertMultiple
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param array $data
     * @return void
     * @throws Zend_Db_Exception
     */
    public function execute(array $data): void
    {
        $this->resourceConnection->getConnection()->insertMultiple(
            $this->resourceConnection->getTableName(PackHistoryTable::TABLE_NAME),
            $data
        );
    }
}
