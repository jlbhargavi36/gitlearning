<?php

namespace Aceturtle\AvailableInventory\Observer;

use Aceturtle\AvailableInventory\Model\InventoryFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class UpdateAvailableInventory implements ObserverInterface
{
    protected $orderRepository;
    /**
     * @var InventoryFactory
     */
    private $inventoryFactory;

    public function __construct(
        OrderRepositoryInterface $OrderRepositoryInterface,
        InventoryFactory $inventoryFactory
    )
    {
        $this->orderRepository = $OrderRepositoryInterface;
        $this->inventoryFactory = $inventoryFactory;
    }

    public function execute(Observer $observer)
    {
       /* $order_ids = $observer->getEvent()->getOrderIds()[0];
        $order = $this->orderRepository->get($order_ids);
        $deliveryType = $order->getDeliveryType();
        $availableInventoryCollection = [];
        foreach ($order->getAllVisibleItems() as $item) {
            $sku = $item->getSku();
            $qtyOrdered = (int)$item->getQtyOrdered();
            $orderQty = $item->getQty();
            if ($deliveryType === 'dutyfree') {
                $availableInventoryCollection = $this->inventoryFactory->create()->getCollection()
                    ->addFieldToFilter('sku', $sku)
                    ->addFieldToFilter('delivery_type', $deliveryType);
            }
            if ($deliveryType === 'home_delivery' || $deliveryType === 'dutyfree' || $deliveryType === 'click_and_collect') {
                $availableInventoryCollection = $this->inventoryFactory->create()->getCollection()
                    ->addFieldToFilter('sku', $sku)
                    ->addFieldToFilter('delivery_type', ['in' => ['home_delivery', 'click_and_collect', 'dutyfree']]);
            }

            if ($availableInventoryCollection && $availableInventoryCollection->getData()) {
                foreach ($availableInventoryCollection as $rowData) {
                    $rowId = $rowData->getRowId();
                    $inventory = $this->inventoryFactory->create()->load($rowId);
                    $availableQty = (int)$inventory->getAvailableQty();
                    $inventory->setAvailableQty($availableQty - $qtyOrdered);
                    $inventory->save();
                }
            }
	
        }
	*/
    }
}
