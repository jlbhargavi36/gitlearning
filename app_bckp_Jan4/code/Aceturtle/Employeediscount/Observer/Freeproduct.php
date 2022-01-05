<?php
namespace Aceturtle\Employeediscount\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class Freeproduct implements ObserverInterface
{
    protected $_productRepository;
    protected $_cart;
    protected $formKey;

    public function __construct(\Magento\Catalog\Model\ProductRepository $productRepository, \Magento\Checkout\Model\Cart $cart,
    \Magento\Checkout\Model\Session $checkoutSession,
    \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    \Magento\Framework\Message\ManagerInterface $messageManager,
    \Magento\Framework\ObjectManagerInterface $objectmanager,
    \Aceturtle\Employeediscount\Helper\Data $dataHelper,
    \Magento\Customer\Model\Session $customerSession,
     \Magento\Framework\Data\Form\FormKey $formKey){
        $this->_productRepository = $productRepository;
        $this->_cart = $cart;
        $this->quoteRepository = $quoteRepository;
        $this->_objectManager = $objectmanager;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
        $this->_checkoutSession = $checkoutSession;
	$this->dataHelper = $dataHelper;
	$this->customerSession = $customerSession;
        $this->formKey = $formKey;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/coupon_cart.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $quote = $this->_checkoutSession->getQuote();
        $salesruleIds = explode(',', $quote->getAppliedRuleIds());
        	$quote->getGrandTotal();
	$getLimitSetForEmp = $this->dataHelper->getEmpCouponId();
	$logger->info("salesruleIds : ". json_encode($salesruleIds));
	$couponId = $this->dataHelper->getEmpCouponId();
	if(in_array($couponId,$salesruleIds) && $this->customerSession->isLoggedIn()){

			$PreTotalAmount = $this->dataHelper->getCustomerOrderTotal();
			$logger->info("total : ". json_encode($PreTotalAmount));
			$totalAmount = $this->_cart->getQuote()->getGrandTotal() + $PreTotalAmount;
			$logger->info("totalAmount : ". json_encode($totalAmount));
			$getLimitSetForEmp = $this->dataHelper->getLimitSetForEmp();
			if($totalAmount>$getLimitSetForEmp){
				//$quote->setCouponCode('');
				$logger->info("execute now : ". json_encode($totalAmount));
				$this->_cart->getQuote()->setCouponCode('')->collectTotals()->save();
				$this->messageManager->addErrorMessage(__('Coupon code will be revoke, you have cross the '.$getLimitSetForEmp.' limit'));
			}
		}

	
   }
}
