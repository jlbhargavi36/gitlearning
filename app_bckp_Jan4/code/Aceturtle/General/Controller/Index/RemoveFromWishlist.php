<?php

namespace Aceturtle\General\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class RemoveFromWishlist extends Action {
    protected $wishlist;

    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\Wishlist $wishlist,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        $this->customerSession = $customerSession;
        $this->wishlist = $wishlist;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        if(!$customerId) {
           $jsonData = ['result' => ['status' => 200, 'message' => 'Customer not logged in.']]; 
            $result = $this->jsonFactory->create()->setData($jsonData);
            return $result;
        }

        $productId = $this->getRequest()->getParam('productId');

        $wish = $this->wishlist->loadByCustomerId($customerId);
        $items = $wish->getItemCollection();

        /** @var \Magento\Wishlist\Model\Item $item */
        foreach ($items as $item) {
            if ($item->getProductId() == $productId) {
                
                $item->delete();
                $wish->save();
            }
        }
        $jsonData = ['result' => ['status' => 200, 'message' => 'Success: You have modified your wish list!']];
        $result = $this->jsonFactory->create()->setData($jsonData);
        return $result;
    }
}