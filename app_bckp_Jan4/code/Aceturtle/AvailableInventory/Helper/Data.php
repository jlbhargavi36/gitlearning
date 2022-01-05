<?php

namespace Aceturtle\AvailableInventory\Helper;

use Aceturtle\AvailableInventory\Model\Inventory;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var Inventory
     */
    private $inventory;

    public function __construct(
        Context $context,
        Session $session,
        Inventory $inventory
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->inventory = $inventory;
    }

    public function getAvailability($sku, $deliveryType)
    {
        $collection = $this->getAvailabilityCollection($sku, $deliveryType);
        if ($collection) {
            $qty = (int) $this->getCartQty($sku);
            if ($qty <= $collection['available_qty']) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getAvailableQty($sku, $deliveryType)
    {
        $collection = $this->getAvailabilityCollection($sku, $deliveryType);
        $qty = 0;
        if ($collection) {
            $qty = (int)$collection['available_qty'];
        }
        return $qty;
    }

    public function getAvailabilitySkuCollection($sku)
    {
        $collection = $this->inventory->getCollection()
            ->addFieldToFilter('sku', $sku)
            ->addFieldToFilter('available_qty', ['neq' => 0])
            ->getFirstItem();
        if ($collection->getData()) {
            return $collection->getData();
        }
        return [];
    }

    public function getAvailabilityCollection($sku, $deliveryType)
    {
        $collection = $this->inventory->getCollection()
            ->addFieldToFilter('sku', $sku)
            ->addFieldToFilter('delivery_type', $deliveryType)
            ->getFirstItem();
        if ($collection->getData() && (int)$collection->getAvailableQty() !== 0) {
            return $collection->getData();
        }
        return [];
    }

    public function getCartQty($sku)
    {
        $quote = $this->getQuote();
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            while ($quoteItem->getSku() === $sku) {
                return $quoteItem->getQty();
            }
        }
    }

    public function getQuote()
    {
        return $this->session->getQuote();
    }

    public function getDeliveryType()
    {
        if ($this->getQuote()) {
            return $this->session->getQuote()->getDeliveryType();
        }
    }
}
