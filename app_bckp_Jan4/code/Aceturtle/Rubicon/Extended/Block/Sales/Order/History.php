<?php

namespace Aceturtle\Rubicon\Extended\Block\Sales\Order;


class History extends \Magento\Sales\Block\Order\History
{

    /*public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        array $data = [],
        \Magento\Sales\Model\Order $order,
        \Magento\Catalog\Model\ProductRepository $productRepository
    )
    {
        $this->order = $order;
        $this->productRepository = $productRepository;
        parent::__construct($context, $orderCollectionFactory, $customerSession, $orderConfig, $data);
    }

    public function getOrderData($orderId) {
        return $this->order->load($orderId);
    }

    public function getAllItems($orderId) {
        $orderData = $this->getOrderData($orderId);
        $items = $orderData->getAllItems();
        return $items;
    }

    public function getProduct($id) {
        return $this->productRepository->getById($id);
    }*/
}
