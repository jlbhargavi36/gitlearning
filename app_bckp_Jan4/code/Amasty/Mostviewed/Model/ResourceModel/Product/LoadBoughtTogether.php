<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\ResourceModel\Product;

use Magento\Framework\App\ResourceConnection;

class LoadBoughtTogether
{
    public const QUERY_LIMIT = 1000;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(array $productIds, array $storeIds, int $period, array $orderStatuses): array
    {
        $tableName = $this->resourceConnection->getTableName('sales_order_item', 'sales');
        $salesConnection = $this->resourceConnection->getConnection('sales');

        $productIdField = $salesConnection->getIfNullSql(
            'parent_item.product_id',
            'order_item.product_id'
        );

        $orderItemsSelect = $salesConnection->select()
            ->from(
                ['order_item' => $tableName],
                ['id' => $productIdField, 'cnt' => new \Zend_Db_Expr('COUNT(*)')]
            )
            ->join(
                ['order' => $this->resourceConnection->getTableName('sales_order', 'sales')],
                'order_item.order_id = order.entity_id',
                []
            )
            ->join(
                ['main_item' => $tableName],
                'main_item.order_id = order.entity_id',
                []
            )
            ->joinLeft(
                ['parent_item' => $tableName],
                'parent_item.item_id = order_item.parent_item_id',
                []
            )
            ->where('order_item.product_id NOT IN(?)', $productIds)
            ->where('main_item.product_id IN(?)', $productIds)
            ->where('order.store_id IN (?)', $storeIds)
            ->where('(NOW() - INTERVAL ? DAY) <= order.created_at', $period)
            ->group($productIdField)
            ->order('cnt DESC')
            ->limit(self::QUERY_LIMIT);

        if ($orderStatuses) {
            $orderItemsSelect->where('order.status IN (?)', $orderStatuses);
        }

        return $salesConnection->fetchAll($orderItemsSelect);
    }
}
