<?php
namespace Aceturtle\Rubicon\Controller\Index;

class Changepassword extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_session;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
        $this->_session = $session;
        return parent::__construct($context);
    }

        public function execute()
        {
         
            if ($this->_session->isLoggedIn()) {
            return $this->_pageFactory->create();
        } else {
            $result = $this->resultRedirectFactory->create();
            $result->setPath('customer/account/login');
            return $result;
        }
    }
}
