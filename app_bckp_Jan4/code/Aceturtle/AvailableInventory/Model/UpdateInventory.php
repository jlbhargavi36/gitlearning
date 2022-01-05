<?php

namespace Aceturtle\AvailableInventory\Model;

use Aceturtle\AvailableInventory\Api\InventoryInterface;

class UpdateInventory implements InventoryInterface
{
    /**
     * @var InventoryFactory
     */
    private $inventoryFactory;

    public function __construct(
        \Aceturtle\AvailableInventory\Model\InventoryFactory $inventoryFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->inventoryFactory = $inventoryFactory;
        $this->configurable = $configurable;
        $this->stockRegistry = $stockRegistry;
        $this->productRepository = $productRepository;
    }

    /**
     * @param array[] $data
     * @return array[]|\Magento\Framework\Controller\Result\Json
     */
    public function updateInventory($data)
    {
        $result = [];

        foreach ($data as $skuData) {

        $requiredKeys = ['sku', 'main_inventory', 'is_in_stock'];
        $result = [];

        if (count(array_intersect_key(array_flip($requiredKeys), $skuData)) === count($requiredKeys)) {
            if (isset($skuData['sku'])) {
                $sku = $skuData['sku'];
            }
            if (isset($skuData['main_inventory'])) {
                $mainInventory = $skuData['main_inventory'];
            }
            if (isset($skuData['is_in_stock'])) {
                $iSInStock = $skuData['is_in_stock'];
            }
                    if (isset($skuData['delivery_methods_inventory'])) {

                        $this->updateMainInventory($sku, $mainInventory, $iSInStock);

                        $deliveryMethodsInventory = $skuData['delivery_methods_inventory'];
                        foreach ($deliveryMethodsInventory as $key => $delValue) {
                            $deliveryType = $delValue['delivery_type'];
                            $availableQty = $delValue['available_qty'];
                            $inventoryModel = $this->inventoryFactory->create();
                            $collection = $inventoryModel->getCollection()
                                ->addFieldToFilter('sku', $sku)
                                ->addFieldToFilter('delivery_type', $deliveryType);

                            if (!empty($collection->getData())) {
                                $id = $collection->getData()[0]['row_id'];
                                $inventoryModel->load($id);
                                $inventoryModel->setAvailableQty($availableQty);
                            } else {
                                $inventoryModel->setData(
                                    [
                                        'sku' => $sku,
                                        'delivery_type' => $deliveryType,
                                        'available_qty' => $availableQty
                                    ]
                                );
                            }
                            try {
                                $inventoryModel->save();

                                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inventory.log');
                                $logger = new \Zend\Log\Logger();
                                $logger->addWriter($writer);
                                $logger->info('sku ' .$sku . ' Main inventory: '. $mainInventory .'  deliveryType ' . $deliveryType . ' availableQty ' .$availableQty);

                                $result['status'] = true;
                                $result['msg'] = 'Updated successfully';
                                if ($availableQty > 0) {
                                    $this->updateParentStockStatus($sku);
                                }
                            } catch (\Exception $exception) {
                                echo $exception->getMessage();
                            }
                        }
                    }   
        }
        else {
            $result['status'] = false;
            $result['msg'] = 'Required key(s) missing.';
        }

    
        return json_encode($result, true);
    }
}

    public function updateMainInventory($sku, $qty, $iSInStock) {
        $stockItem = $this->stockRegistry->getStockItemBySku($sku);
        $stockItem->setQty($qty);
        $stockItem->setIsInStock($iSInStock); // this line
        $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
    }

    public function getInventory($sku)
    {
        $collection = $this->inventoryFactory->create()
            ->getCollection()
            ->addFieldToFilter('sku', $sku);
        return json_encode(['result' => $collection->getData()], true);
    }

    public function getProductIdBySku($sku) {
        $product = $this->productRepository->get($sku);
        $productId = $product->getId();
        return $productId;
    }

    public function updateParentStockStatus($sku) {
        $childId = $this->getProductIdBySku($sku);
        if ($childId) {
            $parentIds = $this->configurable->getParentIdsByChild($childId);
            foreach ($parentIds as $parentId) { 
                $stockItem = $this->stockRegistry->getStockItem($parentId);
                $stockItem->setData('is_in_stock',1);
                $cSku = $this->productRepository->getById($parentId)->getSku();
                $this->stockRegistry->updateStockItemBySku($cSku, $stockItem);
            }
        }
        return $this;
    }
}
