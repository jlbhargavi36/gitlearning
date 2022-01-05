<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\ConditionalDiscount\Query;

use Amasty\Mostviewed\Api\Data\ConditionalDiscountInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface GetListInterface
{
    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ConditionalDiscountInterface[]
     */
    public function execute(SearchCriteriaInterface $searchCriteria): array;
}
