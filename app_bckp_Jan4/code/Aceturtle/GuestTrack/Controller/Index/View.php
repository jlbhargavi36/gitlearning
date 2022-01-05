<?php
/**
 *
 * Copyright © 2015 Aceturtlecommerce. All rights reserved.
 */
namespace Aceturtle\GuestTrack\Controller\Index;

class View extends \Magento\Framework\App\Action\Action
{

	/**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
	    \Magento\Catalog\Model\Session $catalogSession
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->eventManager = $eventManager;
	   $this->catalogSession = $catalogSession;
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
	$order_id = $this->getRequest()->getParam('order_id'); 
	$_SESSION['oar_order_id'] = $order_id;
	$this->catalogSession->setData('oar_order_id', $order_id);
    $this->eventManager->dispatch(
        'controller_action_predispatch_guest_view_post',
                ['order_id' => $this->getRequest()->getParam('order_id')]
        );
	$resultRedirect = $this->resultRedirectFactory->create();
	$resultRedirect->setPath('sales/guest/view/');
	return $resultRedirect;
     
    }
}
