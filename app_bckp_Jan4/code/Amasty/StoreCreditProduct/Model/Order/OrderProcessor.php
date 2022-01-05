<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Model\Order;

use Amasty\StoreCredit\Model\History\MessageProcessor;
use Amasty\StoreCredit\Model\StoreCredit\ManageCustomerStoreCredit;
use Amasty\StoreCreditProduct\Model\Order\OrderValidator;
use Amasty\StoreCreditProduct\Model\Product\Type\StoreCreditProductType;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;

class OrderProcessor
{
    /**
     * @var ManageCustomerStoreCredit
     */
    private $manageCustomerStoreCredit;

    /**
     * @var OrderValidator
     */
    private $orderValidator;

    public function __construct(
        ManageCustomerStoreCredit $manageCustomerStoreCredit,
        OrderValidator $orderValidator
    ) {
        $this->manageCustomerStoreCredit = $manageCustomerStoreCredit;
        $this->orderValidator = $orderValidator;
    }

    /**
     * @param Order $order
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function process(Order $order): void
    {
        $amount = $this->getStoreCreditAmount($order->getAllItems());

        if ($amount > 0 && !$this->orderValidator->isOrderProcessedBefore($order)) {
            $this->manageCustomerStoreCredit->addOrSubtractStoreCredit(
                $order->getCustomerId(),
                $amount,
                MessageProcessor::BUY_STORE_CREDIT_PRODUCT,
                [$order->getIncrementId()],
                $order->getStoreId(),
                '',
                false
            );
        }
    }

    /**
     * @param Item[] $items
     * @return float
     */
    private function getStoreCreditAmount(array $items): float
    {
        $amount = 0;
        foreach ($items as $item) {
            if ($item->getProductType() === StoreCreditProductType::PRODUCT_TYPE) {
                $amount += ($item->getBasePrice() * $item->getQtyOrdered());
            }
        }

        return (float)$amount;
    }
}
