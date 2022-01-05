<?php

declare(strict_types=1);

namespace Amasty\Xsearch\Model\Indexer;

class ElasticSearchStockStatusStructureMapper
{
    const STOCK_STATUS = 'stock_status';
    const TYPE_INTEGER = 'integer';

    /**
     * @return array
     */
    public function buildEntityFields(): array
    {
        return [self::STOCK_STATUS => ['type' => self::TYPE_INTEGER]];
    }
}
