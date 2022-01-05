<?php

namespace Amasty\CPS\Plugin\Framework\Search\Request;

use Magento\Framework\Search\Request\Builder as MagentoRequestBuilder;
use Magento\Store\Model\StoreManager;

class Builder
{
    const COLLECTION_PARAM_NAME = 'ambrand_id';

    /**
     * @var \Amasty\ShopbyBrand\Helper\Content
     */
    private $contentHelper;

    /**
     * @var \Magento\Framework\EntityManager\EntityMetadataInterface
     */
    private $metadata;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Api\SortOrderFactory
     */
    private $sortOrderFactory;

    public function __construct(
        \Amasty\ShopbyBrand\Helper\Content $contentHelper,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        StoreManager $storeManager,
        \Magento\Framework\Api\SortOrderFactory $sortOrderFactory
    ) {
        $this->contentHelper = $contentHelper;
        $this->metadata = $metadataPool->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);
        $this->storeManager = $storeManager;
        $this->sortOrderFactory = $sortOrderFactory;
    }

    /**
     * @param MagentoRequestBuilder $subject
     * @return array
     */
    public function beforeCreate(MagentoRequestBuilder $subject)
    {
        if ($brand = $this->contentHelper->getCurrentBranding()) {
            $subject->bind(self::COLLECTION_PARAM_NAME, $brand->getValue());
            if (method_exists($subject, 'setSort')) {
                $subject->setSort(['position' => 'asc']);
            }
        }

        return [];
    }
}
