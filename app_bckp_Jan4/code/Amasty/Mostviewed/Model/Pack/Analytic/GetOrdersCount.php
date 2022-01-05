<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\Analytic;

use Amasty\Mostviewed\Model\ResourceModel\Pack\Analytic\Sales\LoadOrdersCount;

class GetOrdersCount
{
    /**
     * @var array
     */
    private $cache = [];

    /**
     * @var LoadOrdersCount
     */
    private $loadOrdersCount;

    public function __construct(LoadOrdersCount $loadOrdersCount)
    {
        $this->loadOrdersCount = $loadOrdersCount;
    }

    public function execute(int $packId): int
    {
        if (!isset($this->cache[$packId])) {
            $this->cache[$packId] = $this->loadOrdersCount->execute($packId);
        }

        return $this->cache[$packId];
    }
}
