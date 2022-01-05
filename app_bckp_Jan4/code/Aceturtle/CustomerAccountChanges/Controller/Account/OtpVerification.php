<?php

namespace Aceturtle\CustomerAccountChanges\Controller\Account;

use Magento\Framework\App\Action\Context;

class OtpVerification extends \Magento\Framework\App\Action\Action
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

        if ($this->getRequest()->getParam('otp')) {
            $mobileNo = $this->getRequest()->getParam('mobile_no');
            $mobileOtp = (int) $this->session->getMobileOtp();
            $postOtp = (int) $this->getRequest()->getParam('otp');
            if ($mobileOtp != $postOtp) {
                $data = ['code' => 400, 'msg' => "Invalid OTP"];
                $result->setData($data);
                return $result;
            }

            if ($mobileOtp == $postOtp) {
		if ($this->getRequest()->getParam('page')!='signin'){
                $websiteId = $this->storeManager->getStore()->getWebsiteId();
                $customerEmail = $this->session->getCustomer()->getEmail();
                $loadedCustomer = $this->customer->setWebsiteId($websiteId)->loadByEmail($customerEmail);
                $loadedCustomer->setmobileverified(1);		
		$loadedCustomer->save();
		}
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
