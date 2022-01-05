<?php
/**
 * Created by PhpStorm.
 * User: amit
 * Date: 22/3/19
 * Time: 10:48 AM
 */

namespace Aceturtle\General\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Checkout\Model\Cart;

class MoveCartToWishlist implements ObserverInterface
{

    /**
     * @var Session
     */
    private $session;
    /**
     * @var \Magento\Wishlist\Model\Wishlist
     */
    private $wishlist;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Session $session,
        WishlistFactory $wishlist,
        Cart $cart,
        StoreManagerInterface $storeManager
    )
    {
        $this->session = $session;
        $this->wishlist = $wishlist;
        $this->cart = $cart;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $params = $observer->getRequest()->getParams();
        $storeParam = $observer->getRequest()->getParam('___store');
        if (!$storeParam )
        {
            return $this;
        }
        $quote = $this->session->getQuote();
        $quoteCount = $quote->getItemsCount();
        $customerID = $quote->getCustomerId();
        $storeId = $this->getStoreId($params);
        if ($storeParam && $quoteCount && $customerID) {
           foreach ($this->session->getQuote()->getAllVisibleItems() as $item) {
               $wishlist =  $this->wishlist->create()->loadByCustomerId($customerID);
               if($wishlist) {
                   $product = $item->getProduct();
                   $product->setStoreId($storeId);
                   $wishlist->addNewItem($product);
                   $wishlist->save();
               }
           }
           foreach ($quote->getAllVisibleItems() as $cartItem) {
               $itemId = $cartItem->getItemId();
               $this->cart->removeItem($itemId)->save();
           }
        }
        if ($storeParam && $quoteCount && !$customerID) {
            foreach ($quote->getAllVisibleItems() as $cartItem) {
                $itemId = $cartItem->getItemId();
                $this->cart->removeItem($itemId)->save();
            }
        }
    }

    public function getStoreId($params) {
        $storeParam = $params['___store'];
        if (isset($params['___from_store'])) {
            $fromStore = $params['___from_store'];
        }
        if (isset($fromStore)) {
            $fromStoreCode = $fromStore;
        }
        if (!isset($fromStore)) {
            switch ($storeParam) {
                case 'default':
                    $fromStoreCode = 'dutyfree';
                    break;
                case 'dutyfree':
                    $fromStoreCode = 'default';
                    break;
                default:
                    $fromStoreCode = 'default';
            }
         }
        $stores = $this->storeManager->getStores(true, false);
        foreach($stores as $store){
            if($store->getCode() === $fromStoreCode){
                $storeId = $store->getId();
            }
        }
        return $storeId;
    }
}