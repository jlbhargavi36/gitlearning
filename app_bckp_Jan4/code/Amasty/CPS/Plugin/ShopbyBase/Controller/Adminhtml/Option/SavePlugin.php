<?php

namespace Amasty\CPS\Plugin\ShopbyBase\Controller\Adminhtml\Option;

class SavePlugin
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Amasty\CPS\Model\Product\AdminhtmlDataProvider
     */
    private $dataProvider;

    /**
     * @var \Amasty\CPS\Model\BrandProduct
     */
    private $brandProduct;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Amasty\CPS\Model\Product\AdminhtmlDataProvider $dataProvider,
        \Amasty\CPS\Model\BrandProduct $brandProduct
    ) {
        $this->request = $request;
        $this->dataProvider = $dataProvider;
        $this->brandProduct = $brandProduct;
    }

    /**
     * @param \Amasty\ShopbyBase\Controller\Adminhtml\Option\Save $subject
     * @return array
     */
    public function beforeExecute(
        \Amasty\ShopbyBase\Controller\Adminhtml\Option\Save $subject
    ) {
        $subject->getRequest()->setPostValue('sorting', $this->dataProvider->getSortOrder());
        $this->brandProduct->pinProduct(
            $this->dataProvider->getBrandId(),
            $this->dataProvider->getStoreId(),
            $this->dataProvider->getProductPositionData()
        );

        return [];
    }
}
