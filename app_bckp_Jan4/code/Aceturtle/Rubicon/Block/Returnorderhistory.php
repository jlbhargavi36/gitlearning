<?php

namespace Aceturtle\Rubicon\Block;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

class Returnorderhistory extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        TemplateContext $context,
        \Aceturtle\Rubicon\Helper\Order $orderHelper,
        array $data = []
    ) {
        $this->orderHelper = $orderHelper;
        parent::__construct($context, $data);
    }

    public function getOrderData() {

        $order = $this->getOrder();
        $rubiconItemData = $this->orderHelper->getOrderData($order);

        return $rubiconItemData;
    }

    public function getProductImageUrl($sku) {
        return $this->orderHelper->getProductImageUrl($sku);
    }
}
