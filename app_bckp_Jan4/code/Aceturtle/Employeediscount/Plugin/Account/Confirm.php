<?php

namespace Aceturtle\Employeediscount\Plugin\Account;

class Confirm
{

  protected $customerRepository;
  
  protected $session;
  
  protected $resulfactory;
  
  protected $urlModel;
  
  protected $Sendverificationmail;

 public function __construct(
        \Magento\Framework\Controller\ResultFactory $resulfactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\UrlFactory $urlFactory,
        \Aceturtle\Employeediscount\Controller\Account\Sendverificationmail $Sendverificationmail

    ) {
        $this->resulfactory = $resulfactory;
        $this->session = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->urlModel = $urlFactory->create();
        $this->Sendverificationmail = $Sendverificationmail;

    }


     public function afterExecute(\Magento\Customer\Controller\Account\Confirm $subject,$result){
     
        $customerId = $subject->getRequest()->getParam('id', false);
        if($customerId) {
        $key = $subject->getRequest()->getParam('key', false);
        $customerEmail = $this->customerRepository->getById($customerId)->getEmail(); 
        if($customerEmail) {
        $extractdomain=explode("@",$customerEmail);
        $domainval=$extractdomain[1];
        if($domainval=="aceturtle.com") {
        $customer = $this->customerRepository->getById($customerId);
	$name = $customer->getFirstName()." ".$customer->getLastName();
        $customer->setGroupId('4');
        $this->customerRepository->save($customer);
        $this->Sendverificationmail->sendmail($customerEmail,$name);
        }
        }
        }
        return $result;  
    }

}
