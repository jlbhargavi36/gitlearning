<?php

namespace Aceturtle\Employeediscount\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper 
{
   
 
    /**
     * @var CollectionFactory
     */
    private $orderCollectionFactory;
 
    public function __construct(
        Context $context,
        CollectionFactory $orderCollectionFactory,
	\Magento\Customer\Model\Session $customerSession
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
	$this->customerSession = $customerSession;
        parent::__construct($context);
    }
 
    /**
     * @return array
     */
    public function getCustomerOrder()
    {
        $customerId = $this->customerSession->getCustomer()->getId(); // pass customer id
        $customerOrder = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
	    ->addAttributeToFilter('status', array(
            	'eq' => 'processing'
            ));
        return $customerOrder->getData();
    }

    public function getCustomerOrderTotal()
    {
	  $orders = $this->getCustomerOrder();
	  $total = 0;
	  foreach($orders as $order){
		$total += $order['grand_total'];
	  }
	return $total;
   }

    public function getLimitSetForEmp()
    {
	return 30000;
     }

    public function getEmpCouponId()
    {

	return 18;
    }
}

?>
