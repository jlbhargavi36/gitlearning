<?php
namespace Aceturtle\Dashboardtracking\Block;
class Dashproductdetail extends \Magento\Framework\View\Element\Template
{
  
     /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    
    protected $_storeManager;
    
    protected $stockFilter;

    protected $productRepository; 

    
  
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogInventory\Helper\Stock $stockFilter,
            \Magento\Catalog\Api\ProductRepositoryInterface $productRepository

    )
    {    
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->stockFilter=$stockFilter;   
        $this->productRepository = $productRepository;
  
        parent::__construct($context);
    }
    
    
    public function getProductsku($ids)
    { 
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	    $stockregistry=$objectManager->create('\Magento\CatalogInventory\Model\StockRegistry'); 
	    $productskuoptionconfig=$this->getconfigProductByCategory($ids);
              $inStock = [];
              $cntsku=0;
       foreach($productskuoptionconfig as $skuconfig) {
             $_children = $skuconfig->getTypeInstance()->getUsedProducts($skuconfig);

           foreach($_children as $_child) {
             $stockItem = $stockregistry->getStockItem($_child->getId(), 1);

             if($stockItem->getIsInStock()) {
              $inStock[] = $_child->getId(); 
              $cntsku=$cntsku+1;
             }
             }
       }
       
        return $cntsku;
    }
    
    
    
     public function getProductInventory($ids)
      { 
      	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	    $stockregistry=$objectManager->create('\Magento\CatalogInventory\Model\StockRegistry'); 
	    $productinventoryoptionconfig=$this->getconfigProductByCategory($ids);
              $inStock = [];
              $cntInventory=0;
       foreach($productinventoryoptionconfig as $inventoryconfig) {
             $_children = $inventoryconfig->getTypeInstance()->getUsedProducts($inventoryconfig);

           foreach($_children as $_child) {
             $stockItem = $stockregistry->getStockItem($_child->getId(), 1);
            
            if($stockItem->getIsInStock()) {
              $inStock[] = $_child->getId(); 
              $stockinv = $stockItem->getQty();  
              $cntInventory=$cntInventory+$stockinv;  
             }


             }
       }
       

        return $cntInventory;
    } 
    
   
    public function getProductoption($ids)
    { 
    $product = $this->productRepository->getById($ids); //Configurable Product Id

    $colorAttributeId = $product->getResource()->getAttribute('color')->getId(); // Get Color Attribute Id
    $configurableAttrs = $product->getTypeInstance()->getConfigurableAttributesAsArray($product); // Get Used Attributes with its values

    if(isset($configurableAttrs[$colorAttributeId])){ 
          // Gives you the count
      //  echo "<pre>";print_r($configurableAttrs[$colorAttributeId]['values']); // Give you values used
      return count($configurableAttrs[$colorAttributeId]['values']); 
    } else {
       
       return 0;
    
    }
       
    }

    public function getconfigProductByCategory($ids){
         $_collectionconfig = $this->_productCollectionFactory->create();
         if($ids!="") { 
        $_collectionconfig->addCategoriesFilter(['in' => $ids]);
         }

           $_collectionconfig->addAttributeToFilter('visibility', [2,4]);
        $_collectionconfig->addStoreFilter($this->_storeManager->getStore()->getId());
        $_collectionconfig->addAttributeToFilter('type_id', ['eq' => 'configurable']);
         $_collectionconfig->addAttributeToFilter(
                            'status', array('eq' => '1')
                        );
    $this->stockFilter->addInStockFilterToCollection($_collectionconfig);
     $_collectionconfig
                    ->joinField('qty',
                        'cataloginventory_stock_item',
                        'qty',
                        'product_id=entity_id',
                        '{{table}}.is_in_stock=1',
                        'left'
                    )->addAttributeToSelect('stock_status')                 
                        ->addAttributeToSort ( 'entity_id', 'DESC' );                            
                       // ->addAttributeToFilter('qty',['gt'=>0]);
    //echo $_collectionconfig->getselect()->__tostring();
        return $_collectionconfig;
      } 



    
    
     public function getProductCollectionByCategories($ids)
      {
        $collectionProd = $this->_productCollectionFactory->create();
        $collectionProd->addAttributeToSelect('*');
        $collectionProd->addCategoriesFilter(['in' => $ids]);
         $collectionProd->addAttributeToFilter('visibility', [2,4]);
        $collectionProd->addStoreFilter($this->_storeManager->getStore()->getId());
       $collectionProd->addAttributeToFilter('type_id', ['eq' => 'configurable']);

       // echo $collectionProd->getselect()->__tostring();
        return $collectionProd;
      } 
    
   public function getProductBySky($sku)
    {
        return $this->productRepository->get($sku);
    }
    
    
    
}



