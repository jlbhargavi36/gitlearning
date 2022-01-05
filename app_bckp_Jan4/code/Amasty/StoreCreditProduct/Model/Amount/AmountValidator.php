<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Model\Amount;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Exception\LocalizedException;

class AmountValidator
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param Product $product
     * @param float $customAmount
     * @throws LocalizedException
     */
    public function validateCustomAmount(Product $product, float $customAmount): void
    {
        if ($customAmount <= 0) {
            throw new LocalizedException(__('Please specify a store credit amount.'));
        }

        $min = $product->getAmstoreCreditOpenAmountMin();
        $max = $product->getAmstoreCreditOpenAmountMax();

        if ($min && $customAmount < $min) {
            throw new LocalizedException(
                __('Store Credit min amount is %1', $this->priceCurrency->convertAndFormat($min, false))
            );
        }
        if ($max && $customAmount > $max) {
            throw new LocalizedException(
                __('Store Credit max amount is %1', $this->priceCurrency->convertAndFormat($max, false))
            );
        }
    }
}
