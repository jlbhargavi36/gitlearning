<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Model\Product;

use Amasty\StoreCreditProduct\Model\Amount\AmountFilter;
use Amasty\StoreCreditProduct\Model\Product\Amounts;
use Amasty\StoreCreditProduct\Model\Product\Attributes;
use Magento\Catalog\Api\Data\ProductTierPriceExtensionFactory;
use Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\Price as TypePrice;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\CatalogRule\Model\ResourceModel\RuleFactory;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Price extends TypePrice
{
    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var AmountFilter
     */
    private $amountFilter;

    public function __construct(
        RuleFactory $ruleFactory,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        Session $customerSession,
        ManagerInterface $eventManager,
        PriceCurrencyInterface $priceCurrency,
        GroupManagementInterface $groupManagement,
        ProductTierPriceInterfaceFactory $tierPriceFactory,
        ScopeConfigInterface $config,
        ProductTierPriceExtensionFactory $tierPriceExtensionFactory,
        ProductResource $productResource,
        Json $jsonSerializer,
        AmountFilter $amountFilter
    ) {
        parent::__construct(
            $ruleFactory,
            $storeManager,
            $localeDate,
            $customerSession,
            $eventManager,
            $priceCurrency,
            $groupManagement,
            $tierPriceFactory,
            $config,
            $tierPriceExtensionFactory
        );
        $this->productResource = $productResource;
        $this->jsonSerializer = $jsonSerializer;
        $this->amountFilter = $amountFilter;
    }

    /**
     * @param Product $product
     * @return array
     * @throws LocalizedException
     */
    public function getAmounts(Product $product): array
    {
        $prices = $product->getAmstoreCreditPrices();

        if (($prices === null) && $this->productResource->getAttribute(Attributes::PRODUCT_PRICES)) {
            $attribute = $this->productResource->getAttribute(Attributes::PRODUCT_PRICES);
            $attribute->getBackend()->afterLoad($product);
            $prices = $product->getAmstoreCreditPrices();
        }

        if (is_string($prices)) {
            $prices = $this->jsonSerializer->unserialize($prices);
        }

        $prices = $this->amountFilter->filterByWebsite((array)$prices, $product->getStore()->getWebsiteId());

        return $prices;
    }

    public function getPrice($product)
    {
        $price = (float)$product->getData('price');
        //if there is one amount and no open amount, set product price = this amount
        if (!$product->getAmstoreCreditOpenAmount()
            && (count($this->getAmounts($product)) === 1)
            && !$product->hasCustomOptions()
        ) {
            $amounts = $this->getAmounts($product);
            $amount = array_shift($amounts);
            $price = (float)$amount['value'];
        }

        return $price;
    }

    public function getFinalPrice($qty, $product)
    {
        $finalPrice = $product->getPrice();

        if ($product->hasCustomOptions() && $product->getCustomOption(Amounts::PRODUCT_AMOUNT)) {
            $customValue = (float)$product->getCustomOption(Amounts::PRODUCT_AMOUNT)->getValue();

            $finalPrice += $customValue;
        }
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        $product->setData(FinalPrice::PRICE_CODE, $finalPrice);

        return max(0, $product->getData(FinalPrice::PRICE_CODE));
    }
}
