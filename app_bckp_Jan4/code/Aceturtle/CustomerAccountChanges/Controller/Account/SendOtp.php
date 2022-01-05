<?php

namespace Aceturtle\CustomerAccountChanges\Controller\Account;

use Magento\Framework\App\Action\Context;

class SendOtp extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
        \Aceturtle\General\Helper\Data $generalHelper,
        \Magento\Customer\Model\Session $session
    ) {
        $this->generalHelper = $generalHelper;
        $this->session = $session;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $isModuleEnabled = $this->generalHelper->isModuleEnabled();
        if (!$isModuleEnabled) {
            return $this;
        }
        $mobileNo = $this->getRequest()->getParam('mobile_no');
        $containsCountryCode = substr($mobileNo, 0, 3) === "+91";
        if (!$containsCountryCode) {
            $mobileNo = '+91' . $mobileNo;
        }
	$reqmobileNo = $this->getRequest()->getParam('mobile_no');
        if ($mobileNo) {
            $otp = rand(100000, 999999);
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/otp.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($otp);
            $message = $this->generalHelper->getOtpMessage($otp);
            $this->generalHelper->sendSms($message, $mobileNo,$reqmobileNo);
            $this->session->setMobileOtp($otp);
        }
    }
}
