<?php
/**
 * Created by PhpStorm.
 * User: 
 * Date: 29/7/20
 * Time: 2:02 AM
 */

namespace Aceturtle\CustomerAccountChanges\Plugin\Customer\Account;


use Aceturtle\CustomerAccountChanges\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\RequestInterface;

class EditPost
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

    public function beforeExecute(\Magento\Customer\Controller\Account\EditPost $subject) {
       
            $createUrl = $this->url->getUrl('customer/account/edit/');
            $mobileNo = $this->request->getParam('mobile_no');
		$currentLoginId = $this->customerSession->getCustomer()->getId();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerObj = $objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection'); 
		$customerfetch = $customerObj->addAttributeToSelect('mobile_no')
        		->addAttributeToFilter('mobile_no', $mobileNo)
			->addAttributeToFilter('entity_id', array('neq' => $currentLoginId))
        		->load();
            if ($customerfetch->count()>0) {
                $this->messageManager->addErrorMessage("There is already an account with same Mobile number.");
                $this->responseHttp->setRedirect($createUrl)->sendResponse();
                //return false;
		die();
            }
	return;
    }

}
