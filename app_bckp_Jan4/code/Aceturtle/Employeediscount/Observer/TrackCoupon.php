<?php

namespace Aceturtle\Employeediscount\Observer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class TrackCoupon implements ObserverInterface
{
    protected $_responseFactory;
    protected $_request;
    protected $_url;
    private $registry;
    protected $_objectManager;
    protected $couponModel;
    protected $customerSession;
    protected $ruleRepository;
    protected $messageManager; 

    public function __construct(
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\UrlInterface $url,
	\Magento\Framework\ObjectManagerInterface $objectManager,
	\Magento\Checkout\Model\Session $checkoutSession,
	\Magento\SalesRule\Model\Coupon $couponModel,
        \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository,
	\Aceturtle\Employeediscount\Helper\Data $dataHelper,
	\Magento\Customer\Model\Session $customerSession,
	\Magento\Framework\Message\ManagerInterface $messageManager,
	\Magento\Checkout\Model\Cart $cart,
        Registry $registry
    ) {
        $this->_responseFactory = $responseFactory;
        $this->_request = $request;
        $this->_url = $url;
	$this->_objectManager = $objectManager;
        $this->registry = $registry;
	$this->_checkoutSession = $checkoutSession;
	$this->couponModel = $couponModel;
        $this->ruleRepository = $ruleRepository;
	$this->dataHelper = $dataHelper;
	$this->customerSession = $customerSession;
	 $this->messageManager = $messageManager;
	$this->cart = $cart;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/coupon_validate.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
	$rule = $observer;
	$logger->info("rule 13: ". json_encode($rule));
	
	$quote = $this->_checkoutSession->getQuote();
        $salesruleIds = explode(',', $quote->getAppliedRuleIds());
	$logger->info("Coupon 1: ". json_encode($salesruleIds));
        $salesruleIds = explode(',', $this->cart->getQuote()->getAppliedRuleIds());
	$logger->info("Coupon 2: ". json_encode($this->cart->getQuote()->getAppliedRuleIds()));
        	//$quote->getGrandTotal();

	/*$logger->info("quote 2: ". json_encode($this->cart->getQuote()->getGrandTotal()));
	$controller = $observer->getControllerAction();
	    	$logger->info("salesruleIds : ". json_encode($salesruleIds));
		if(in_array('5',$salesruleIds) && $this->customerSession->isLoggedIn()){

			$PreTotalAmount = $this->dataHelper->getCustomerOrderTotal();
			$logger->info("total : ". json_encode($PreTotalAmount));
			$totalAmount = $this->cart->getQuote()->getGrandTotal() + $PreTotalAmount;
			$logger->info("totalAmount : ". json_encode($totalAmount));
			if($totalAmount>15000){
				//$quote->setCouponCode('');
				$logger->info("execute now : ". json_encode($totalAmount));
				$this->cart->getQuote()->setCouponCode('')->collectTotals()->save();
				$this->messageManager->addErrorMessage(__('We cannot apply the coupon code.'));
				return false;
			}
		}
	*/
	/*if(in_array('5',$salesruleIds) ) {
	//$data = $this->dataHelper->getCustomerOrder();
	
	$quote = $this->_checkoutSession->getQuote();
        $salesruleIds = explode(',', $quote->getAppliedRuleIds());
	$logger->info("Coupon : ". json_encode($salesruleIds));
		
	} */
	
	
	$logger->info("end now : ");
       //$couponCode = $observer->getEvent();
	//$logger->info("Coupon : ". json_encode($couponCode));
    }


 

}
