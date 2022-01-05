<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\ViewModel\Adminhtml\Catalog\Product\Composite\Fieldset;

use Amasty\StoreCreditProduct\Model\Amount\AmountFilter;
use Amasty\StoreCreditProduct\Model\Product\Amounts;
use Amasty\StoreCreditProduct\ViewModel\StoreCreditProduct;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;

class Product extends StoreCreditProduct
{
    /**
     * @var ProductHelper
     */
    private $productHelper;

    /**
     * @var Data
     */
    private $pricingHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        Json $jsonSerializer,
        ProductHelper $productHelper,
        Data $pricingHelper,
        StoreManagerInterface $storeManager,
        AmountFilter $amountFilter
    ) {
        parent::__construct(
            $productRepository,
            $jsonSerializer,
            $amountFilter
        );
        $this->productHelper = $productHelper;
        $this->pricingHelper = $pricingHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $productId
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isLastFieldset(int $productId): bool
    {
        return !$this->getProduct($productId)->getOptions();
    }

    /**
     * @param int $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCurrentCurrencyCode(int $storeId): string
    {
        return $this->storeManager->getStore($storeId)->getCurrentCurrencyCode();
    }

    /**
     * @return bool
     */
    public function isSkipSaleableCheck(): bool
    {
        return $this->productHelper->getSkipSaleableCheck();
    }

    /**
     * @param ProductInterface $product
     * @return float[]
     * @throws NoSuchEntityException
     */
    public function getAmounts(ProductInterface $product): array
    {
        $result = [];
        $amounts = $this->amountFilter->filterByWebsite(
            $product->getAmstoreCreditPrices(),
            $product->getStore()->getWebsiteId()
        );
        foreach ($amounts as $amount) {
            $result[] = round($amount['value'], 2);
        }
        sort($result);

        return $result;
    }

    /**
     * @param float $amount
     * @param int $storeId
     * @return float|string
     */
    public function getCurrencyByStore(float $amount, int $storeId)
    {
        return $this->pricingHelper->currencyByStore($amount, $storeId, true, false);
    }

    /**
     * @param string $key
     * @return string
     * @throws NoSuchEntityException
     */
    public function getDefaultValue(string $key, int $productId): string
    {
        return (string)$this->getProduct($productId)->getPreconfiguredValues()->getData($key);
    }

    /**
     * @param string $configValue
     * @param ProductInterface $product
     * @return bool
     */
    public function isCustomAmount(string $configValue, ProductInterface $product): bool
    {
        if ($configValue === Amounts::CUSTOM_AMOUNT_PARAM || !$this->getAmounts($product)) {
            return true;
        }

        return false;
    }
}
