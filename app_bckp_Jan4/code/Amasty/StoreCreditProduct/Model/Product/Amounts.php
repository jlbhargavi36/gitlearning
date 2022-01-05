<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Model\Product;

use Amasty\StoreCreditProduct\Model\Amount\AmountFilter;
use Amasty\StoreCreditProduct\Model\Amount\AmountValidator;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\FormatInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class Amounts
{
    const CUSTOM_AMOUNT_PARAM = 'custom';
    const PRODUCT_AMOUNT = 'amstore_credit_amount';
    const PRODUCT_CUSTOM_AMOUNT = 'amstore_credit_amount_custom';
    const BASE_CURRENCY_RATE = 1;

    /**
     * @var AmountValidator
     */
    private $amountValidator;

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @var FormatInterface
     */
    private $localeFormat;

    /**
     * @var BuyRequest
     */
    private $buyRequest;

    /**
     * @var AmountFilter
     */
    private $amountFilter;

    public function __construct(
        AmountValidator $amountValidator,
        StoreManagerInterface $storeManager,
        FormatInterface $localeFormat,
        BuyRequest $buyRequest,
        AmountFilter $amountFilter
    ) {
        $this->amountValidator = $amountValidator;
        $this->store = $storeManager->getStore();
        $this->localeFormat = $localeFormat;
        $this->buyRequest = $buyRequest;
        $this->amountFilter = $amountFilter;
    }

    /**
     * @param DataObject $buyRequest
     * @param Product $product
     * @param bool $isStrictProcessMode
     * @return float
     * @throws LocalizedException
     */
    public function checkAmount(DataObject $buyRequest, Product $product, bool $isStrictProcessMode): float
    {
        if (!$product->getCustomOption(self::PRODUCT_AMOUNT)) {
            $amount = $this->getFinalAmount(
                $buyRequest,
                $product,
                $isStrictProcessMode
            );
        } else {
            $amount = (float)$product->getCustomOption(self::PRODUCT_AMOUNT)->getValue();
        }

        return $amount;
    }

    /**
     * @param DataObject $buyRequest
     * @param Product $product
     * @param bool $isStrictProcessMode
     * @return float
     * @throws LocalizedException
     */
    public function getFinalAmount(
        DataObject $buyRequest,
        Product $product,
        bool $isStrictProcessMode
    ): float {
        $allowedOpenAmount = $product->getAmstoreCreditOpenAmount();
        $selectedAmount = $buyRequest->getAmstoreCreditAmount();
        $allowedAmounts = $this->getAllowedAmounts($product);
        $customAmount = $this->getCustomStoreCreditAmount($buyRequest);

        $amount = null;
        if ((!$selectedAmount || $selectedAmount === Amounts::CUSTOM_AMOUNT_PARAM) && $allowedOpenAmount) {
            if ($isStrictProcessMode) {
                $this->amountValidator->validateCustomAmount($product, $customAmount);
            }
            $amount = $customAmount;
            $this->buyRequest->setCustomAmount($buyRequest, $amount);
        } elseif (is_numeric($selectedAmount) && in_array($selectedAmount, $allowedAmounts)) {
            $amount = $selectedAmount;
        }

        if ($amount === null && count($allowedAmounts) === 1) {
            $amount = array_shift($allowedAmounts);
        }

        if ($amount === null) {
            throw new LocalizedException(__('Please specify a store credit amount.'));
        }

        return (float)$amount;
    }

    /**
     * @param DataObject $buyRequest
     * @return float
     * @throws LocalizedException
     */
    private function getCustomStoreCreditAmount(DataObject $buyRequest): float
    {
        $customAmount = $buyRequest->getAmstoreCreditAmountCustom();

        if ($buyRequest->getAmstoreCreditAmount() === self::CUSTOM_AMOUNT_PARAM
            && !$buyRequest->getAmstoreCreditAmountCustomOrder()
        ) {
            return (float)$customAmount;
        }

        $rate = $this->store->getCurrentCurrencyRate();

        if ($rate !== self::BASE_CURRENCY_RATE && $customAmount) {
            $customAmount = $this->localeFormat->getNumber($customAmount);

            if (is_numeric($customAmount) && $customAmount) {
                $customAmount = round($customAmount / $rate, 2);
            }
        }

        return (float)$customAmount;
    }

    /**
     * @param Product $product
     * @return float[]
     * @throws NoSuchEntityException
     */
    private function getAllowedAmounts(Product $product): array
    {
        $allowedAmounts = [];

        if ($product->getAmstoreCreditPrices()) {
            $amounts = $this->amountFilter->filterByWebsite(
                $product->getAmstoreCreditPrices(),
                $product->getStore()->getWebsiteId()
            );
            foreach ($amounts as $amount) {
                $allowedAmounts[] = round($amount['value'], 2);
            }
        }

        return $allowedAmounts;
    }
}
