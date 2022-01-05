<?php
declare(strict_types=1);

namespace Amasty\StoreCredit\Model\Total\Quote\Collectors;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;

interface QuoteCollectorInterface
{
    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @param float $availableCredit
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total,
        float $availableCredit
    ): void;
}
