<?php

namespace Aceturtle\CustomerAccountChanges\Controller\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class LoginOverwritten extends \Magento\Customer\Controller\Account\Login
{
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->customer = $customer;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        parent::__construct($context, $customerSession, $resultPageFactory);
    }

    public function execute()
    {
        /*if ($this->getRequest()->getParam('mobile_no')) {
            $mobileNo = $this->getRequest()->getParam('mobile_no');
            $mobileOtp = (int) $this->session->getMobileOtp();
            $postOtp = (int) $this->getRequest()->getParam('post_otp');
            
            if ($mobileOtp !== $postOtp){
                $this->messageManager->addErrorMessage("OTP didn't match");
                $this->_redirect('customer/account/login');
                return;
            }
            if ($mobileOtp) {
                $customerCollection = $this->collectionFactory->create();
                $customer = $customerCollection->addAttributeToSelect('*')
                    ->addAttributeToFilter('mobile_no', $mobileNo)
                    ->load();
                $websiteId = $this->storeManager->getStore()->getWebsiteId();
                if (!$customer->getData()) {
                    $this->messageManager->addErrorMessage('This mobile number is not registered');
                    $this->_redirect('customer/account/login');
                    return;
                }
                $customerEmail = $customer->getData()[0]['email'];
                $loadedCustomer = $this->customer->setWebsiteId($websiteId)->loadByEmail($customerEmail);
                $loadedCustomer->setWebsiteId($websiteId);
                $this->session->setCustomerAsLoggedIn($loadedCustomer);
                $this->_redirect('customer/account');
            }
        } else {
            return parent::execute();
        }*/
    }
}
