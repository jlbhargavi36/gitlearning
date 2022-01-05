<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Model\Order\CreditMemo;

use Amasty\StoreCredit\Model\History\MessageProcessor;
use Amasty\StoreCredit\Model\StoreCredit\ManageCustomerStoreCredit;
use Amasty\StoreCreditProduct\Model\Product\Type\StoreCreditProductType;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Item;

class CreditMemoProcessor
{
    /**
     * @var ManageCustomerStoreCredit
     */
    private $manageCustomerStoreCredit;

    public function __construct(
        ManageCustomerStoreCredit $manageCustomerStoreCredit
    ) {
        $this->manageCustomerStoreCredit = $manageCustomerStoreCredit;
    }

    /**
     * @param Creditmemo $creditMemo
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function process(Creditmemo $creditMemo): void
    {
        $amount = $this->getStoreCreditAmount($creditMemo->getItems());

        if ($amount > 0) {
            $this->manageCustomerStoreCredit->addOrSubtractStoreCredit(
                $creditMemo->getCustomerId(),
                -$amount,
                MessageProcessor::REFUND_STORE_CREDIT_PRODUCT,
                [$creditMemo->getOrder()->getIncrementId()],
                $creditMemo->getStoreId(),
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
            if ($item->getOrderItem()->getProductType() === StoreCreditProductType::PRODUCT_TYPE) {
                $amount += ($item->getBasePrice() * $item->getQty());
            }
        }

        return (float)$amount;
    }
}
