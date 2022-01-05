<?php
/**
 * Created by PhpStorm.
 * User: Varun Verma
 * Date: 29/7/20
 * Time: 2:02 AM
 */

namespace Aceturtle\CustomerAccountChanges\Plugin\Customer\Account;


use Aceturtle\CustomerAccountChanges\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\RequestInterface;

class CreatePost
{
    /**
     * @var ManagerInterface
     */
    private $messageManager;
    /**
     * @var Http
     */
    private $httpResponse;
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var RequestInterface
     */
    private $request;


    public function __construct(
        ManagerInterface $messageManager,
        Http $responseHttp,
        Session $customerSession,
        RequestInterface $request,
        Data $dataHelper,
        \Magento\Framework\UrlInterface $url
    )
    {
        $this->messageManager = $messageManager;
        $this->responseHttp = $responseHttp;
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->dataHelper = $dataHelper;
        $this->url = $url;
    }

    public function aroundExecute(\Magento\Customer\Controller\Account\CreatePost $subject, $proceed) {
       
            $createUrl = $this->url->getUrl('customer/account/create');
            $mobileNo = $this->request->getParam('mobile_no');

            $customers = $this->dataHelper->searchCustomersByAttributeValue('mobile_no', $mobileNo);
            if (!empty($customers)) {
                $this->messageManager->addErrorMessage("There is already an account with same Mobile number. You can login using same mobile number.");
                $this->responseHttp->setRedirect($createUrl);
                return;
            }

        return $proceed();
    }

}