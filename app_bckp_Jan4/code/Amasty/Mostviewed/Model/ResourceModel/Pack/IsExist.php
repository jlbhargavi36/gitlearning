<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\ResourceModel\Pack;

use Amasty\Mostviewed\Api\Data\PackInterface;
use Amasty\Mostviewed\Model\ResourceModel\Pack as PackResource;
use Magento\Framework\App\ResourceConnection;
use Zend_Db_Exception;

class IsExist
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
     * @param int $id
     * @return bool
     * @throws Zend_Db_Exception
     */
    public function execute(int $id): bool
    {
        $select = $this->resourceConnection->getConnection()->select()->from(
            $this->resourceConnection->getTableName(PackResource::PACK_TABLE),
            [PackInterface::PACK_ID]
        )->where(sprintf('%s = ?', PackInterface::PACK_ID), $id);

        return (bool)$this->resourceConnection->getConnection()->fetchOne($select);
    }
}
