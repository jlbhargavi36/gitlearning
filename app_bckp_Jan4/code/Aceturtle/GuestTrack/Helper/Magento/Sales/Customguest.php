<?php

namespace Aceturtle\GuestTrack\Helper\Magento\Sales;

use Magento\Framework\App as App;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Sales\Model\Order;
use Magento\Captcha\Observer\CaptchaStringResolver;


class Customguest extends \Magento\Sales\Helper\Guest
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Cookie key for guest view
     */
    const COOKIE_NAME = 'guest-view';

    /**
     * Cookie path
     */
    const COOKIE_PATH = '/';

    /**
     * Cookie lifetime value
     */
    const COOKIE_LIFETIME = 600;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var string
     */
    private $inputExceptionMessage = 'Please enter valid Order ID to track your order. The shared details doesn’t match.';

    protected $captchaStringResolver;

    protected $_orderCollectionFactory;

    /**
     * @param App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        App\Helper\Context $context,
	\Magento\Captcha\Helper\Data $helper,
	\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
	\Aceturtle\Rubicon\Helper\Order $orderHelper,
	CaptchaStringResolver $captchaStringResolver,
	\Magento\Catalog\Model\Session $catalogSession,
	\Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository = null,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria = null

    ) {
	$this->resultFactory = $resultFactory;
	$this->_helper = $helper;
	$this->_orderCollectionFactory = $orderCollectionFactory;
	$this->orderHelper = $orderHelper;
	$this->captchaStringResolver = $captchaStringResolver;
	$this->catalogSession = $catalogSession;
        $this->coreRegistry = $coreRegistry;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->messageManager = $messageManager;
        $this->orderFactory = $orderFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->orderRepository = $orderRepository ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $this->searchCriteriaBuilder = $searchCriteria?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        parent::__construct(
            $context, $storeManager,  $coreRegistry, $customerSession, $cookieManager, $cookieMetadataFactory, $messageManager, $orderFactory, $resultRedirectFactory, $orderRepository, $searchCriteria
        );
    }
 
    /**
     * Try to load valid order by $_POST or $_COOKIE
     *
     * @param App\RequestInterface $request
     * @return \Magento\Framework\Controller\Result\Redirect|bool
     * @throws \RuntimeException
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    public function loadValidOrder(App\RequestInterface $request)
    {
      /*  if ($this->customerSession->isLoggedIn()) {
            return $this->resultRedirectFactory->create()->setPath('sales/order/history');
        } */
        $post = $request->getPostValue();
	if(isset($post['oar_order_id'])){
		$this->catalogSession->setData('order_id', $post['oar_order_id']);
	}

	
	
	$formId = 'trackorder_form';
        $captcha = $this->_helper->getCaptcha($formId);
        if ($captcha->isRequired() && !empty($post)) {
	 if (!$captcha->isCorrect($this->captchaStringResolver->resolve($request, $formId))) {
		$this->messageManager->addError(__('Incorrect CAPTCHA.'));
		return $this->resultRedirectFactory->create()->setPath('sales/guest/form');
	  }
	}

        $fromCookie = $this->cookieManager->getCookie(self::COOKIE_NAME);
	$order_id = $this->catalogSession->getData('oar_order_id', false);
        if (empty($post) && !$fromCookie && empty($order_id)) {
           // return $this->resultRedirectFactory->create()->setPath('sales/guest/form');
	   return $this->resultRedirectFactory->create()->setPath('sales/guest/form');
        }
        // It is unique place in the class that process exception and only InputException. It is need because by
        // input data we found order and one more InputException could be throws deeper in stack trace
        try {
            $order = $this->loadFromPost($post);
            $this->coreRegistry->register('current_order', $order);
	    if(isset($post['oar_order_id']) && $this->getRubiconcount($post['oar_order_id'])==false){
		$this->messageManager->addErrorMessage($this->inputExceptionMessage);
		return $this->resultRedirectFactory->create()->setPath('sales/guest/form');
	    }
            return true;
        } catch (InputException $e) {
		if(!empty($post) && strlen($post['oar_order_id'])==10){
			$mobile = $post['oar_order_id'];  
			if($this->getOrderCount($mobile) > 0){
			return $this->resultRedirectFactory->create()->setPath('guesttrack',['mobile_no' => $mobile]);
			}else {
			$this->messageManager->addErrorMessage($this->inputExceptionMessage);
			return $this->resultRedirectFactory->create()->setPath('sales/guest/form');
			}
			//$redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
	    		//$redirect->setUrl('/guesttrack?mobile_no=8123262967');
			//die('test34234');
	    		//return $redirect;
			//die('test');
		}else{
            		$this->messageManager->addErrorMessage($e->getMessage());
            		//return $this->resultRedirectFactory->create()->setPath('sales/guest/form');
			return $this->resultRedirectFactory->create()->setPath('sales/guest/form');
	   	}
        }
    }

    /**
     * Get Breadcrumbs for current controller action
     *
     * @param \Magento\Framework\View\Result\Page $resultPage
     * @return void
     */
    public function getBreadcrumbs(\Magento\Framework\View\Result\Page $resultPage)
    {
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        if (!$breadcrumbs) {
            return;
        }
        $breadcrumbs->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->storeManager->getStore()->getBaseUrl()
            ]
        );
        $breadcrumbs->addCrumb(
            'cms_page',
            ['label' => __('Order Information'), 'title' => __('Order Information')]
        );
    }

    /**
     * Set guest-view cookie
     *
     * @param string $cookieValue
     * @return void
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    private function setGuestViewCookie($cookieValue)
    {
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setPath(self::COOKIE_PATH)
            ->setHttpOnly(true);
        $this->cookieManager->setPublicCookie(self::COOKIE_NAME, $cookieValue, $metadata);
    }

    /**
     * Load order from cookie
     *
     * @param string $fromCookie
     * @return Order
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    private function loadFromCookie($fromCookie)
    {
        $cookieData = explode(':', base64_decode($fromCookie));
        $protectCode = isset($cookieData[0]) ? $cookieData[0] : null;
        $incrementId = isset($cookieData[1]) ? $cookieData[1] : null;
        if (!empty($protectCode) && !empty($incrementId)) {
            $order = $this->getOrderRecord($incrementId);
            if (hash_equals((string)$order->getProtectCode(), $protectCode)) {
                $this->setGuestViewCookie($fromCookie);
                return $order;
            }
        }
        throw new InputException(__($this->inputExceptionMessage));
    }

    /**
     * Load order data from post
     *
     * @param array $postData
     * @return Order
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    private function loadFromPost(array $postData)
    {
        /** @var $order \Magento\Sales\Model\Order */  
	
   
        if(isset($postData['oar_order_id'])){
	   $this->catalogSession->setData('oar_order_id', $postData['oar_order_id']);
        }else {
           $postData['oar_order_id'] = $this->catalogSession->getData('oar_order_id', false);
        }
        
        $order = $this->getOrderRecord($postData['oar_order_id']);
        
       /* if (!$this->compareStoredBillingDataWithInput($order, $postData)) {
            throw new InputException(__('You entered incorrect data. Please try again.'));
        } */
        $toCookie = base64_encode($order->getProtectCode() . ':' . $postData['oar_order_id']);
        $this->setGuestViewCookie($toCookie);
        return $order;
    }

    /**
     * Check that billing data from the order and from the input are equal
     *
     * @param Order $order
     * @param array $postData
     * @return bool
     */
    private function compareStoredBillingDataWithInput(Order $order, array $postData)
    {
        $type = $postData['oar_type'];
        $email = $postData['oar_email'];
        $lastName = $postData['oar_billing_lastname'];
        $zip = $postData['oar_zip'];
        $billingAddress = $order->getBillingAddress();
       /* return strtolower($lastName) === strtolower($billingAddress->getLastname()) &&
            ($type === 'email' && strtolower($email) === strtolower($billingAddress->getEmail()) ||
                $type === 'zip' && strtolower($zip) === strtolower($billingAddress->getPostcode()));
	*/
	return true;  
  }

    /**
     * Check post data for empty fields
     *
     * @param array $postData
     * @return bool
     */
    private function hasPostDataEmptyFields(array $postData)
    {
	return empty($postData['oar_order_id']);  
    	/*  return empty($postData['oar_order_id']) || empty($postData['oar_billing_lastname']) ||
            empty($postData['oar_type']) || empty($this->storeManager->getStore()->getId()) ||
            !in_array($postData['oar_type'], ['email', 'zip'], true) ||
            ('email' === $postData['oar_type'] && empty($postData['oar_email'])) ||
            ('zip' === $postData['oar_type'] && empty($postData['oar_zip']));
	*/   
 }

    /**
     * Get order by increment_id and store_id
     *
     * @param string $incrementId
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws InputException
     */
    private function getOrderRecord($incrementId)
    {
        $records = $this->orderRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter('increment_id', $incrementId)
                ->addFilter('store_id', $this->storeManager->getStore()->getId())
                ->create()
        );

        $items = $records->getItems();
        if (empty($items)) {
            throw new InputException(__($this->inputExceptionMessage));
        }

        return array_shift($items);
    }

    public function getOrderCount($mobileNo){
	$order = $this->_orderCollectionFactory->create()
		->addFieldToSelect('*')
		//->addFieldToFilter('customer_email',$customerEmail)
		->join(
                'sales_order_address',
                'sales_order_address.parent_id=main_table.entity_id',
                'sales_order_address.telephone ')
            	->addFieldToFilter('telephone', $mobileNo);
		$order->getSelect()->group('main_table.entity_id');
		return $order->count();
    }

   public function getRubiconcount($orderno){
	$_shipments = $this->orderHelper->getORubiconShipmentData($orderno);
	if(empty($_shipments)){
	return false;
	}else {
	return true;
	}
    }


}

	
	
