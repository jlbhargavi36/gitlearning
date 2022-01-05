<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Model\Product\Type;

use Amasty\StoreCredit\Model\ConfigProvider;
use Amasty\StoreCreditProduct\Model\Product\Amounts;
use Amasty\StoreCreditProduct\Model\Product\BuyRequest;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Type\Virtual;
use Magento\Eav\Model\Config;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class StoreCreditProductType extends Virtual
{
    const PRODUCT_TYPE = 'amstore_credit_product';

    /**
     * @var bool
     */
    protected $_canConfigure = true;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var Amounts
     */
    private $amounts;

    /**
     * @var BuyRequest
     */
    private $buyRequest;

    public function __construct(
        Option $catalogProductOption,
        Config $eavConfig,
        Type $catalogProductType,
        ManagerInterface $eventManager,
        Database $fileStorageDb,
        Filesystem $filesystem,
        Registry $coreRegistry,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        ConfigProvider $configProvider,
        PriceCurrencyInterface $priceCurrency,
        Amounts $amounts,
        BuyRequest $buyRequest
    ) {
        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository
        );
        $this->configProvider = $configProvider;
        $this->priceCurrency = $priceCurrency;
        $this->amounts = $amounts;
        $this->buyRequest = $buyRequest;
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function isSalable($product)
    {
        if (!$this->configProvider->isEnabled()) {
            return false;
        }

        $amounts = $product->getPriceModel()->getAmounts($product);
        $open = $product->getAmstoreCreditOpenAmount();

        if (!$open && !$amounts) {
            return false;
        }

        return parent::isSalable($product);
    }

    /**
     * @param DataObject $buyRequest
     * @param Product $product
     * @param string $processMode
     * @return array|string
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareProduct(DataObject $buyRequest, $product, $processMode)
    {
        if ($buyRequest->getData('info_buyRequest')) {
            $productBuyRequest = $buyRequest->getData('info_buyRequest');
            $buyRequestData = $this->serializer->unserialize($productBuyRequest);
            $buyRequest->addData($buyRequestData);
        }
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);

        if (is_string($result)) {
            return $result;
        }

        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);

        try {
            $amount = $this->amounts->checkAmount($buyRequest, $product, $isStrictProcessMode);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $e->getMessage();
        } catch (\Exception $e) {
            $this->_logger->critical($e);

            return __('An error has occurred while preparing Store Credit Product.');
        }
        $this->buyRequest->updateBuyRequest($product, $buyRequest);

        if ($amount > 0) {
            $product->addCustomOption(Amounts::PRODUCT_AMOUNT, $amount, $product);
        }

        return $result;
    }

    /**
     * @param Product $product
     * @return $this
     * @throws LocalizedException
     */
    public function checkProductBuyState($product)
    {
        parent::checkProductBuyState($product);
        $option = $product->getCustomOption('info_buyRequest');

        if ($option instanceof \Magento\Quote\Model\Quote\Item\Option) {
            $buyRequest = new DataObject($this->serializer->unserialize($option->getValue()));
            $this->amounts->checkAmount($buyRequest, $product, true);
        }

        return $this;
    }

    /**
     * @param Product $product
     * @param DataObject $buyRequest
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function processBuyRequest($product, $buyRequest)
    {
        $amount = $buyRequest->getData(Amounts::PRODUCT_AMOUNT);

        return [
            Amounts::PRODUCT_AMOUNT => is_numeric($amount)
                ? $this->priceCurrency->convertAndRound($amount) : $amount,
            Amounts::PRODUCT_CUSTOM_AMOUNT => $this->priceCurrency->convertAndRound(
                $buyRequest->getData(Amounts::PRODUCT_CUSTOM_AMOUNT) ?: 0
            )
        ];
    }
}
