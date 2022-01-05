<?php

namespace Amasty\StoreCredit\Observer;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;

class ConvertQuoteToOrder implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData('order');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getData('quote');
        if ($quote->getData(SalesFieldInterface::AMSC_USE)) {
            $order->setAmstorecreditBaseAmount($quote->getAmstorecreditBaseAmount());
            $order->setAmstorecreditAmount($quote->getAmstorecreditAmount());
            $order->setData(
                SalesFieldInterface::AMSC_SHIPPING_AMOUNT,
                $quote->getData(SalesFieldInterface::AMSC_SHIPPING_AMOUNT)
            );
        }
    }
}
