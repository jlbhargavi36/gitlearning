<?php
namespace Aceturtle\RegenProductPos\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Store\Model\Store;
use Magento\Framework\App\State;

use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\Category;

class RegenerateProductPositionCommand extends Command
{
    /**
     * @var ProductUrlRewriteGenerator
     */
    protected $productUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @var ProductRepositoryInterface
     */
    protected $collection;
    
    protected $_categoryRepository;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    public function __construct(
        State $state,
        Collection $collection,
        ProductUrlRewriteGenerator $productUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
        CollectionFactory $productCollectionFactory,
    	StockStateInterface $StockStateInterface,
    	ResourceConnection $resourceConnection, 
    	Category $categoryRepository
        
    ) {
        $this->state = $state;
        $this->collection = $collection;
        $this->productUrlRewriteGenerator = $productUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->stockStateInterface = $StockStateInterface;
        $this->_getConnection = $resourceConnection->getConnection();
        $this->_categoryRepository = $categoryRepository;
        $this->_categoryProductTable = $resourceConnection->getTableName('catalog_category_product');
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('ace:regenpos')
            ->setDescription('Regenerate position base on products size and qty')
            ->addArgument(
                'pids',
                InputArgument::IS_ARRAY,
                'Products to regenerate'
            )
            ->addOption(
                'store', 's',
                InputOption::VALUE_REQUIRED,
                'Use the specific Store View',
                Store::DEFAULT_STORE_ID
            )
            ;
        return parent::configure();
    }

    public function execute(InputInterface $inp, OutputInterface $out)
    {
     

      //  $store_id = $inp->getOption('store');
      $this->state->setAreaCode('adminhtml');
      $collection = $this->_productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addFilter('type_id', 'configurable', 'eq');
            $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
             $out->writeln('Update postion');
            
            foreach($collection as $product){
		    try {
		         $totalStock = $this->getTotalStock($product, $out);
		         $out->writeln("Total Stock: ".$totalStock['totalStocks']);
		         $out->writeln("Total items: ".$totalStock['itemsCount']);
		         $out->writeln("Total Salable items: ".$totalStock['salableitem']);
		         $position = $totalStock['totalStocks'] + ($totalStock['salableitem'] * 10);
		         $out->writeln("Position: ".$position);
		         $categories = $product->getCategoryIds();
		         $productid = $product->getId();
		    	 $this->updatePosition($productid, $position);
		    }
		    catch(\Exception $e) {
		        $out->writeln('<error> Exception '. $e .'</error>');
		    }
            }
       
    }
    
    public function updatePosition($productId, $position)
    {
    	$adapter = $this->_getConnection;
    	if ($productId) {
                    $where = array(
                       // 'category_id = ?' => (int) $categoryId,
                        'product_id = ?' => (int) $productId
                    );
                    $bind = array('position' => (int) $position);
                    $adapter->update($this->_categoryProductTable, $bind, $where);
                    return "Success";
          } else {
                    return "Error";
          }
    	
    	
    	/*  foreach ($categoryIds as $row) {
    		if ($position) {
                    $where = array(
                        'category_id = ?' => (int) $categoryId,
                        'product_id = ?' => (int) $productId
                    );
                    $bind = array('position' => (int) $position);
                    $adapter->update($this->_categoryProductTable, $bind, $where);
                    $this->_messageManager->addSuccess(__(' %1  Position Updated Successfully.', $categoryId));
                } else {
                    $this->_messageManager->addError(__("%1 is not available", $categoryId));
                }
    	}*/
    
    }
    
    
    
    public function getTotalStock($_product, $out){
	$total_stock = 0;
	if($_product->getTypeID() == 'configurable'){
	    $productTypeInstance = $_product->getTypeInstance();
	    $usedProducts = $productTypeInstance->getSalableUsedProducts($_product, null);
	    $i = 0;
	    foreach ($usedProducts as $simple) {
		 $qty = $this->stockStateInterface->getStockQty($simple->getId(), $simple->getStore()->getWebsiteId());
		 $total_stock += $qty;
		if($qty > 1)
		    $i++;
		$out->writeln($simple->getName()." with size ".$simple->getSize()." have a stock of " . $qty);
	    }
	}
	return array('totalStocks' => $total_stock, 'itemsCount' => count($usedProducts), 'salableitem'=> $i);
    }
	
}
