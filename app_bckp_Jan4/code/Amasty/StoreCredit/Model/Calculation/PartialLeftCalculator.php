<?php
declare(strict_types=1);

namespace Amasty\StoreCredit\Model\Calculation;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item;

class PartialLeftCalculator
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(PriceCurrencyInterface $priceCurrency)
    {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param Creditmemo|Invoice $source
     * @return float
     */
    public function calculatePartialStoreCredit($source): float
    {
        $partialLeftStoreCredit = 0.0;
        $order = $source->getOrder();
        /** @var CreditmemoItem|InvoiceItem $sourceItem */
        foreach ($source->getItems() as $sourceItem) {
            $orderItem = $order->getItemById($sourceItem->getOrderItemId());
            $sourceItemQty = (float)$sourceItem->getQty();
            if ($orderItem && $sourceItemQty > 0) {
                $storeCreditOrderItemAmountByQty = $this->calculateCreditByQty($source, $orderItem, $sourceItemQty);

                $sourceItem->setData(SalesFieldInterface::AMSC_AMOUNT, $storeCreditOrderItemAmountByQty);
                $partialLeftStoreCredit += $storeCreditOrderItemAmountByQty;
            }
        }

        return $partialLeftStoreCredit;
    }

    /**
     * @param Creditmemo|Invoice $source
     * @param Item $orderItem
     * @param float $sourceItemQty
     * @return float
     */
    private function calculateCreditByQty($source, Item $orderItem, float $sourceItemQty): float
    {
        $orderItemQty = $orderItem->getQtyOrdered();
        $totalAppliedStoreCreditToOrderItem = (float)$orderItem->getData(SalesFieldInterface::AMSC_AMOUNT);

        $storeCreditOrderItemAmountByQty = $this->priceCurrency->roundPrice(
            $totalAppliedStoreCreditToOrderItem / $orderItemQty * $sourceItemQty
        );

        $currentFinishedStoreCredit = $this->getFinishedStepTotalCredit($source) + $storeCreditOrderItemAmountByQty;
        $totalFoldedStoreCreditByOrderItem = $storeCreditOrderItemAmountByQty * $orderItemQty;
        if ($totalFoldedStoreCreditByOrderItem < $totalAppliedStoreCreditToOrderItem
            && $currentFinishedStoreCredit == $totalFoldedStoreCreditByOrderItem
        ) {
            // add the remainder of division to the last item
            $storeCreditOrderItemAmountByQty += $totalAppliedStoreCreditToOrderItem - $currentFinishedStoreCredit;
        }

        return $storeCreditOrderItemAmountByQty;
    }

    /**
     * @param Creditmemo|Invoice $source
     * @return float
     */
    private function getFinishedStepTotalCredit($source): float
    {
        $order = $source->getOrder();
        if ($source instanceof Creditmemo) {
            $finishedStepTotal = $order->getData(SalesFieldInterface::AMSC_REFUNDED_BASE_AMOUNT);
        } else {
            $finishedStepTotal = $order->getData(SalesFieldInterface::AMSC_INVOICED_BASE_AMOUNT);
        }

        return (float)$finishedStepTotal;
    }
}
