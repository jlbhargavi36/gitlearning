<?php

namespace Aceturtle\Quickview\Controller\Catalog\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Add extends \Magento\Catalog\Controller\Product\Compare\Add
{
    /**
     * @var ObjectManagerInterface
     */
    protected $myObjectManager;

	public function execute()
    {
        $this->myObjectManager = $this->_objectManager;
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setRefererUrl();
        }

        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId && ($this->_customerVisitor->getId() || $this->_customerSession->isLoggedIn())) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productId, false, $storeId);
            } catch (\Exception $e) {
                $product = null;
            }

            if ($product) {
                $this->_catalogProductCompareList->addProduct($product);
                $productName = $this->myObjectManager->get('Magento\Framework\Escaper')->escapeHtml($product->getName());
                $this->messageManager->addSuccess(__('You added product %1 to the comparison list.', $productName));
                $this->_eventManager->dispatch('catalog_product_compare_add_product', ['product' => $product]);
            }

            $this->myObjectManager->get('Magento\Catalog\Helper\Product\Compare')->calculate();
        }
        $params = $this->getRequest()->getParams();
	  	if (isset($params['aceturtlequickview']) && $params['aceturtlequickview'] == 1) {
            return $resultRedirect->setPath($product->getUrlModel()->getUrl($product));
	  	}
        return $resultRedirect->setRefererOrBaseUrl();
    }

}
