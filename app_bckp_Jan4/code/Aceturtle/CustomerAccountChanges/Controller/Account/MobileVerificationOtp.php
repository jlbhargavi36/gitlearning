<?php

namespace Aceturtle\CustomerAccountChanges\Controller\Account;

use Magento\Framework\App\Action\Context;

class MobileVerificationOtp extends \Magento\Framework\App\Action\Action
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

	$mobileNo = $this->session->getCustomer()->getMobileNo();
        $otp = $this->getRequest()->getParam('otp');
        $containsCountryCode = substr($mobileNo, 0, 3) === "+91";
        if (!$containsCountryCode) {
            $mobileNo = '+91' . $mobileNo;
        }
	$reqmobileNo = $this->session->getCustomer()->getMobileNo();
        if ($mobileNo) {
            $otp = rand(100000, 999999);
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/otp.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($otp);
	    $message = "You Verfication code is ".$otp;
            //$message = $this->generalHelper->getOtpMessage($otp);
            $this->generalHelper->sendSms($message, $mobileNo, $reqmobileNo);
            $this->session->setMobileOtp($otp);
        }
    }
}
