<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Observer;

use Amasty\StoreCreditProduct\Model\Product\Type\StoreCreditProductType;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class DisableGuestCheckout implements ObserverInterface
{
    /**
     * Event name 'checkout_allow_guest'
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $result = $observer->getEvent()->getResult();
        $quote = $observer->getEvent()->getQuote();

        foreach ($quote->getAllItems() as $item) {
            $product = $item->getProduct();
            if ((string)$product->getTypeId() === StoreCreditProductType::PRODUCT_TYPE) {
                $result->setIsAllowed(false);
                break;
            }
        }
    }
}
