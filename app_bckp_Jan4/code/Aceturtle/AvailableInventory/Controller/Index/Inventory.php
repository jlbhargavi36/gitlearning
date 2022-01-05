<?php

namespace Aceturtle\AvailableInventory\Controller\Index;

use Aceturtle\AvailableInventory\Model\InventoryFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;

class Inventory extends Action
{
    /**
     * @var InventoryFactory
     */
    private $inventoryFactory;

    public function __construct(
        Context $context,
        InventoryFactory $inventoryFactory
    ) {
        parent::__construct($context);
        $this->inventoryFactory = $inventoryFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $inventoryModel = $this->inventoryFactory->create();
        $collection = $inventoryModel->getCollection()
        ->addFieldToFilter('row_id', 1);
        //var_dump($collection->getData());
        //exit;

    }
}
