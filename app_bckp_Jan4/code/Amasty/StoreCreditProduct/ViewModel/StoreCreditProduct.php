<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\ViewModel;

use Amasty\StoreCreditProduct\Model\Amount\AmountFilter;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class StoreCreditProduct implements ArgumentInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var AmountFilter
     */
    public $amountFilter;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        Json $jsonSerializer,
        AmountFilter $amountFilter
    ) {
        $this->productRepository = $productRepository;
        $this->jsonSerializer = $jsonSerializer;
        $this->amountFilter = $amountFilter;
    }

    /**
     * @param int $productId
     * @return Product
     * @throws NoSuchEntityException
     */
    public function getProduct(int $productId): Product
    {
        return $this->productRepository->getById($productId);
    }

    /**
     * @param Product $product
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isConfigured(Product $product): bool
    {
        $amounts = $product->getAmstoreCreditPrices();
        if ($amounts) {
            $amounts = $this->amountFilter->filterByWebsite($amounts, $product->getStore()->getWebsiteId());
        }
        if (!$product->getAmstoreCreditOpenAmount() && !$amounts) {
            return false;
        }

        return true;
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getDefaultValues(Product $product): string
    {
        return $this->jsonSerializer->serialize($product->getPreconfiguredValues()->getData());
    }
}
