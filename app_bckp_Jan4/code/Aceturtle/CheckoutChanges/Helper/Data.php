<?php

namespace Aceturtle\CheckoutChanges\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
        ) {
        $this->order = $order;
        $this->orderRepository = $orderRepository;
    }

    public function getOrderData($incrementId)
    {
        $entityId = $this->order->loadByIncrementId($incrementId)->getEntityId();
        $order = $this->orderRepository->get($entityId);
        $response = [];
        $response['created_at'] = $order->getCreatedAt();
        $response['payment_method'] = $order->getPayment()->getAdditionalInformation()['method_title'];
        $response['shipping_method'] = $order->getShippingDescription();
        $response['total'] = $order->getGrandTotal();
        return $response;
    }
}
