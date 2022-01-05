<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Cart;

use Amasty\Mostviewed\Model\ConfigProvider;
use Amasty\Mostviewed\Model\Pack\Cart\ProductRegistry;
use Exception;
use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Downloadable\Model\Product\Type as Downloadable;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Store\Model\StoreManagerInterface;

class AddProductsByIds
{
    const BUNDLE_PACK_ID_OPTION = 'amasty_bundle_pack_id';
    const BUNDLE_PACK_OPTION_CODE = 'amasty_bundle_pack';

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var BundleResultFactory
     */
    private $bundleResultFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var ProductRegistry
     */
    private $productRegistry;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductAddingProgressFlag
     */
    private $productAddingProgressFlag;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CheckoutSession $checkoutSession,
        ConfigProvider $configProvider,
        BundleResultFactory $bundleResultFactory,
        ProductRegistry $productRegistry,
        MessageManagerInterface $messageManager,
        StoreManagerInterface $storeManager,
        ProductAddingProgressFlag $productAddingProgressFlag
    ) {
        $this->productRepository = $productRepository;
        $this->bundleResultFactory = $bundleResultFactory;
        $this->configProvider = $configProvider;
        $this->checkoutSession = $checkoutSession;
        $this->messageManager = $messageManager;
        $this->productRegistry = $productRegistry;
        $this->storeManager = $storeManager;
        $this->productAddingProgressFlag = $productAddingProgressFlag;
    }

    public function execute(int $packId, array $productIds): BundleResult
    {
        /** @var BundleResult $bundleResult */
        $bundleResult = $this->bundleResultFactory->create();

        $hasRequiredOptions = false;
        foreach ($productIds as $productId => $qty) {
            $productId = (int) $productId;
            if (!$productId) {
                continue;
            }

            try {
                $product = $this->productRepository->getById(
                    $productId,
                    false,
                    $this->storeManager->getStore()->getId()
                );
                if (!$product->isSalable() || !$product->isVisibleInCatalog()) {
                    $this->messageManager->addErrorMessage(__(
                        'We can\'t add %1 to your shopping cart right now.',
                        $product->getName()
                    ));
                    continue;
                }
            } catch (NoSuchEntityException $e) {
                continue;
            }

            $productHasRequiredOptions = $this->isProductHasRequiredOptions($product);
            $hasRequiredOptions = $hasRequiredOptions || $productHasRequiredOptions;
            $shopPopupOptions = $productHasRequiredOptions
                || ($this->configProvider->isShowAllOptions() && $product->getOptions());

            if ($shopPopupOptions) {
                $bundleResult->addSkippedProduct($product);
            } else {
                try {
                    if (!$this->configProvider->isProductsCanBeAddedSeparately()) {
                        $product->addCustomOption(AddProductsByIds::BUNDLE_PACK_ID_OPTION, $packId);
                        $product->addCustomOption(AddProductsByIds::BUNDLE_PACK_OPTION_CODE, true);
                    }
                    $result = $this->checkoutSession->getQuote()->addProduct($product, $qty);
                    if (is_string($result)) {
                        $bundleResult->addSkippedProduct($product, $result);
                    } else {
                        $this->productRegistry->addProduct((int) $product->getId(), [
                            'qty' => (float) $qty
                        ]);
                    }
                } catch (Exception $e) {
                    $bundleResult->addSkippedProduct($product, $e->getMessage());
                }
            }
        }
        $bundleResult->setHasRequiredOptions($hasRequiredOptions);
        $bundleResult->setPackId($packId);

        if ($bundleResult->getSkippedProducts()) {
            $this->productAddingProgressFlag->set(true);
        }

        return $bundleResult;
    }

    private function isProductHasRequiredOptions(ProductInterface $product): bool
    {
        switch ($product->getTypeId()) {
            case Type::TYPE_SIMPLE:
            case Type::TYPE_VIRTUAL:
                $result = $product->getTypeInstance()->hasRequiredOptions($product);
                break;
            case Configurable::TYPE_CODE:
            case Grouped::TYPE_CODE:
            case Bundle::TYPE_CODE:
            case Downloadable::TYPE_DOWNLOADABLE:
            case 'amgiftcard':
            default:
                $result = true;
        }

        return $result;
    }
}
