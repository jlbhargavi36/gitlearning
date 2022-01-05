<?php
/**
 * Copyright © 2015 Aceturtle . All rights reserved.
 */
namespace Aceturtle\GuestTrack\Block\Index;
class Index extends \Magento\Framework\View\Element\Template
{
 protected $_orderCollectionFactory;
 protected $_urlInterface;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
       \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
       \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
	\Magento\Framework\UrlInterface $urlInterface, 
        \Magento\Sales\Model\Order $order,
	\Magento\Framework\Controller\ResultFactory $resultFactory,
	\Magento\Framework\Message\ManagerInterface $messageManager,
	\Magento\Framework\App\RequestInterface $request
    ) {
        parent::__construct($context);
        $this->_session = $session;
        $this->_logger = $context->getLogger();
	$this->collectionFactory = $collectionFactory;
	$this->_urlInterface = $urlInterface;
        $this->_orderCollectionFactory = $orderCollectionFactory;
	$this->resultFactory = $resultFactory;
	$this->messageManager = $messageManager;
	$this->request = $request;
    }

	
     public function getOrders() {   

	       $mobileNo = $this->getRequest()->getParam('mobile_no'); 

	       /*$customer = $this->getCustomerEmail($mobileNo);
	       if($customer->count()==0){
			$this->messageManager->addError(__("You entered incorrect data. Please try again."));
			$redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
    			$redirect->setUrl('/sales/guest/form/');
			//$url = $this->_urlInterface->getBaseUrl('');
	        }
		$customerEmail = $customer->getData()[0]['email'];*/
		$this->orders = $this->_orderCollectionFactory->create()
		->addFieldToSelect('*')
		//->addFieldToFilter('customer_email',$customerEmail)
		->join(
                'sales_order_address',
                'sales_order_address.parent_id=main_table.entity_id',
                'sales_order_address.telephone ')
            	->addFieldToFilter('telephone', $mobileNo)
		->addAttributeToSort('entity_id', 'DESC')
		->setPageSize(5);
		$this->orders->getSelect()->group('main_table.entity_id');

            return $this->orders;
	   
     }

    public function getViewUrl($order)
    {
        return $this->getUrl('guesttrack/index/view/', ['order_id' => $order->getRealOrderId()]);
    }

    public function getCustomerEmail($mobileNo)
    {
        $customerCollection = $this->collectionFactory->create();
                $customer = $customerCollection->addAttributeToSelect('*')
                    ->addAttributeToFilter('mobile_no', $mobileNo)
                    ->load();

	return $customer;
    }

   // public function checkBillingData($mobileNo){

	  /* 

	$this->orders = $this->_orderCollectionFactory->create()
		->addFieldToSelect('*')

$this->orders = $this->_orderCollectionFactory->create()
		->addFieldToSelect('*')
		//->addFieldToFilter('customer_mobile');
		/* Joined with `sales_order_address` to get COuntry Id *\/
		
            ->join(
                'sales_order_address',
                'sales_order_address.parent_id=main_table.entity_id',
                'sales_order_address.telephone ')
            	->addFieldToFilter('telephone', $mobileNo);
	    */

	//}
}
