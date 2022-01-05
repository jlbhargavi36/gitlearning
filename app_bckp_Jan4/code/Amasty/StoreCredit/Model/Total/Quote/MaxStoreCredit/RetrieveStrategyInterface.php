<?php

declare(strict_types=1);

namespace Amasty\StoreCredit\Model\Total\Quote\MaxStoreCredit;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;

interface RetrieveStrategyInterface
{
    /**
     * Retrieve max store credit value which can be applied for given quote.
     *
     * @param Quote $quote
     * @param Total $total
     * @return float
     */
    public function execute(Quote $quote, Total $total): float;
}
