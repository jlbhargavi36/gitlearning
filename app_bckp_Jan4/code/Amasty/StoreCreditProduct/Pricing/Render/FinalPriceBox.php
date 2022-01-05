<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Pricing\Render;

use Amasty\StoreCreditProduct\Model\Amount\AmountFilter;
use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\Catalog\Pricing\Render\FinalPriceBox as RenderPrice;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template\Context;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class FinalPriceBox extends RenderPrice
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var AmountFilter
     */
    private $amountFilter;

    /**
     * @var array
     */
    protected $minMaxPrice = [];

    /**
     * @var array
     */
    protected $amounts = [];

    public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        PriceCurrencyInterface $priceCurrency,
        Json $jsonSerializer,
        AmountFilter $amountFilter,
        array $data = [],
        SalableResolverInterface $salableResolver = null,
        MinimalPriceCalculatorInterface $minPriceCalculator = null
    ) {
        $this->priceCurrency = $priceCurrency;
        parent::__construct(
            $context,
            $saleableItem,
            $price,
            $rendererPool,
            $data,
            $salableResolver,
            $minPriceCalculator
        );
        $this->jsonSerializer = $jsonSerializer;
        $this->amountFilter = $amountFilter;
        $this->initializeMinMaxPrice();
    }

    private function initializeMinMaxPrice(): void
    {
        $min = $max = null;

        if ($this->isOpenAmount()) {
            $min = $this->getOpenAmountMin() ?: 0;
            $max = $this->getOpenAmountMax() ?: 0;
        }

        foreach ($this->getAmounts() as $amount) {
            $min = ($min === null) ? $amount['value'] : min($min, $amount['value']);
            $max = ($max === null) ? $amount['value'] : max($max, $amount['value']);
        }
        $this->minMaxPrice = ['min' => (float)$min, 'max' => (float)$max];
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    private function getAmounts(): array
    {
        $amounts = $this->saleableItem->getAmstoreCreditPrices();
        if (is_string($amounts)) {
            $amounts = $this->jsonSerializer->unserialize($amounts);
        }

        $amounts = $this->amountFilter->filterByWebsite($amounts, $this->saleableItem->getStore()->getWebsiteId());

        return $amounts;
    }

    /**
     * @return bool
     */
    public function isProductForm(): bool
    {
        return (bool)$this->getData('is_product_from');
    }

    /**
     * @return bool
     */
    public function isSinglePrice(): bool
    {
        return ($this->minMaxPrice['min'] && $this->minMaxPrice['max'])
            ? $this->minMaxPrice['min'] === $this->minMaxPrice['max']
            : false;
    }

    /**
     * @return string
     */
    public function getPredefinedAmounts(): string
    {
        if (!empty($this->amounts)) {
            return $this->jsonSerializer->serialize($this->amounts);
        }

        foreach ($this->getAmounts() as $amount) {
            $this->amounts[] = [
                'value' => (float)$amount['value'],
                'convertValue' => $this->convertCurrency((float)$amount['value']),
                'price' => $this->convertAndFormatCurrency((float)$amount['value'], false)
            ];
        }

        return $this->jsonSerializer->serialize($this->amounts);
    }

    /**
     * @return bool
     */
    public function isOpenAmount(): bool
    {
        return (bool)$this->saleableItem->getAmstoreCreditOpenAmount();
    }

    /**
     * @return float
     */
    public function getOpenAmountMin(): float
    {
        return (float)$this->saleableItem->getAmstoreCreditOpenAmountMin();
    }

    /**
     * @return float
     */
    public function getOpenAmountMax(): float
    {
        return (float)$this->saleableItem->getAmstoreCreditOpenAmountMax();
    }

    /**
     * @return float
     */
    public function getMinPrice(): float
    {
        return $this->minMaxPrice['min'];
    }

    /**
     * @return float
     */
    public function getMaxPrice(): float
    {
        return $this->minMaxPrice['max'];
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        $currency = $this->priceCurrency->getCurrency();

        return $currency->getCurrencyCode() ?: $currency->getCurrencySymbol();
    }

    /**
     * @return string
     */
    public function getCurrencySymbol(): string
    {
        return $this->priceCurrency->getCurrencySymbol();
    }

    /**
     * @param float $amount
     * @param bool $includeContainer
     * @return string
     */
    public function convertAndFormatCurrency(float $amount, bool $includeContainer = true): string
    {
        return $this->priceCurrency->convertAndFormat($amount, $includeContainer);
    }

    /**
     * @param float $amount
     * @return float
     */
    public function convertCurrency(float $amount): float
    {
        return $this->priceCurrency->convert($amount);
    }
}
