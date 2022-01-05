<?php

namespace Aceturtle\CustomerAccountChanges\Controller\Account;

use Magento\Framework\App\Action\Context;

class MobileLogin extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $session
    ) {
        $this->customer = $customer;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->session = $session;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        if ($this->getRequest()->getParam('post_otp')) {
            $mobileNo = $this->getRequest()->getParam('mobile_no');
            $mobileOtp = (int) $this->session->getMobileOtp();
            $postOtp = (int) $this->getRequest()->getParam('post_otp');

            if ($mobileOtp !== $postOtp) {
                $data = ['code' => 400, 'msg' => "Invalid OTP.Please try again"];
                $result->setData($data);
                return $result;
            }
            if ($mobileOtp) {
                $customerCollection = $this->collectionFactory->create();
                $customer = $customerCollection->addAttributeToSelect('*')
                    ->addAttributeToFilter('mobile_no', $mobileNo)
                    ->load();
                $websiteId = $this->storeManager->getStore()->getWebsiteId();
                if (!$customer->getData()) {
                    $data = ['code' => 400, 'msg' => "You are not registered with us. Please Sign Up"];
                    $result->setData($data);
                    return $result;
                }
                $customerEmail = $customer->getData()[0]['email'];
                $loadedCustomer = $this->customer->setWebsiteId($websiteId)->loadByEmail($customerEmail);
                $loadedCustomer->setWebsiteId($websiteId);
                $this->session->setCustomerAsLoggedIn($loadedCustomer);
                $data = ['code' => 200, 'msg' => 'Success'];
                $result->setData($data);
                return $result;
            }
        } else {
            $data = ['code' => 400, 'msg' => 'Please enter OTP'];
            $result->setData($data);
            return $result;
        }
    }
}
