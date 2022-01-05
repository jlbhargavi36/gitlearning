<?php

namespace Aceturtle\Employeediscount\Controller\Account;


class Sendverificationmail extends \Magento\Framework\App\Action\Action
{

/**
* @var \Magento\Framework\Mail\Template\TransportBuilder
*/
protected $_transportBuilder;

/**
* @var \Magento\Framework\Translate\Inline\StateInterface
*/
protected $inlineTranslation;

/**
* @var \Magento\Framework\App\Config\ScopeConfigInterface
*/
protected $scopeConfig;

/**
* @var \Magento\Store\Model\StoreManagerInterface
*/
protected $storeManager;
/**
* @var \Magento\Framework\Escaper
*/
protected $_escaper;


protected $_customer;

protected $_coupongenerator;

protected $_couponRepository;


public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
    \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Framework\Escaper $escaper,
    \Magento\Customer\Model\Customer $customer,
    \Magento\SalesRule\Model\Coupon\Codegenerator $coupongenerator,
    \Magento\SalesRule\Api\CouponRepositoryInterface $couponRepository,
    \Magento\Framework\ObjectManagerInterface $objectmanager,
    \Aceturtle\Employeediscount\Helper\Data $dataHelper
) {
    $this->_transportBuilder = $transportBuilder;
    $this->inlineTranslation = $inlineTranslation;
    $this->scopeConfig = $scopeConfig;
    $this->storeManager = $storeManager;
    $this->_escaper = $escaper;
    $this->_customer = $customer;
    $this->_coupongenerator = $coupongenerator;
    $this->_couponRepository = $couponRepository;
    $this->_objectManager = $objectmanager;
    $this->dataHelper = $dataHelper;
}

  public function execute(){ 
    
    }


/**
 * Post user question
 *
 * @return void
 * @throws \Exception
 */
public function sendmail($recipemailid,$customerName)
{ 

    $senderEmail = $this->scopeConfig->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
             
    $senderName = $this->scopeConfig->getValue('trans_email/ident_general/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	$couponId = $this->dataHelper->getEmpCouponId();
            
        $this->_coupongenerator->setRuleId($couponId);
        $this->_coupongenerator->setFormat('alphanum');
        $this->_coupongenerator->setPrefix('emp');
        $this->_coupongenerator->setSuffix('discount');
        $this->_coupongenerator->setLength('12');
        $couponcodes=$this->_coupongenerator->generateCode();
        $expirationDate = strtotime("+90 day");    
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();  
	$coupon = $objectManager->create('Magento\SalesRule\Model\Coupon');        
	$coupon->setId(null)->setRuleId($couponId)->setType(1)->setCode($couponcodes)->save();



  if($recipemailid!="") {
            $sender = [
            'name' => $senderName,
            'email' => $senderEmail,
        ];

	$model = $this->_objectManager->create('Aceturtle\Employeediscount\Model\Items');
	$model->setEmail($recipemailid);
	$model->setCoupon($couponcodes);
	$model->save();

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE; 
        $transport = $this->_transportBuilder
            ->setTemplateIdentifier('Send_emp_verification_email',$storeScope) // this code we have mentioned in the email_templates.xml
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars(['couponcodes' => $couponcodes, 'customerName' => $customerName])
           // ->setTemplateVars(['customer' => $this->_customer])
            ->setFrom($sender)
            ->addTo($recipemailid)
            ->getTransport();

            $transport->sendMessage();
 
            return;
  
   }
}
}
