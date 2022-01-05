<?php

namespace Aceturtle\Swatchselect\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $request;
	protected $_productRepository;
	protected $resultJsonFactory;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Catalog\Model\ProductRepository $productRepository,
       \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
       \Magento\Framework\App\Action\Context $context
    ) {
       $this->request = $request;
       $this->_productRepository = $productRepository;
       $this->resultJsonFactory = $resultJsonFactory;
       parent::__construct($context);
    }

    public function execute()
    {
 	$resultJson = $this->resultJsonFactory->create();
        $selectedProduct = $this->request->getPostValue('CProductID'); //modification
      

	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	$product = $objectManager->get('Magento\Catalog\Model\Product')->load($selectedProduct); 
	$percentage = "";
	 if($product->getPrice() > 0 && $product->getFinalPrice() > 0){
	    $_savePercent = 100 - round(((float)$product->getFinalPrice() / (float)$product->getPrice()) * 100);
	 
	    if($_savePercent > 0){
		 $percentage = '<div class="price-off"> -'.$_savePercent . '% OFF</div>';
	    }
	 }else {
		$percentage = "";
	}
        /***mod end*/
        $response = ["video" =>$prodCusomVideo,"title" => $product->getName(), "desc" => $product->getDescription(), "percentage" => $percentage];



        return $resultJson->setData($response);
        
    }
}
