<?php

namespace Aceturtle\Employeediscount\Observer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class BehaviorCustomerBeforeSave implements ObserverInterface
{
    protected $_responseFactory;
    protected $_request;
    protected $_url;
    private $registry;

    public function __construct(
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\UrlInterface $url,
        Registry $registry
    ) {
        $this->_responseFactory = $responseFactory;
        $this->_request = $request;
        $this->_url = $url;
        $this->registry = $registry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
 	$customerEmail = $customer->getEmail();
	$extractdomain=explode("@",$customerEmail);
        $domainval=$extractdomain[1];
        if($domainval!="aceturtle.com") {
		$customer->setConfirmation(null);

	    	if ($this->registry->registry('skip_confirmation_if_email')) {
			$this->registry->unregister('skip_confirmation_if_email');
	    	}

	    	$this->registry->register('skip_confirmation_if_email', $customer->getEmail());
	}
    }

}
