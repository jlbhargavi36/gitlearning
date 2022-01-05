<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Model\Product;

use Amasty\StoreCreditProduct\Model\Product\Amounts;
use Magento\Framework\DataObject;
use Magento\Catalog\Model\Product;
use Magento\Framework\Serialize\Serializer\Json;

class BuyRequest
{
    const AMOUNT_ORDER_PARAM = 'amstore_credit_amount_custom_order';

    /**
     * @var Json
     */
    private $serializer;

    public function __construct(Json $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Product $product
     * @param DataObject $buyRequest
     */
    public function updateBuyRequest(Product $product, DataObject $buyRequest): void
    {
        $productBuyRequest = $product->getCustomOption('info_buyRequest');
        $buyRequestData = $this->serializer->unserialize($productBuyRequest->getValue());

        if ($buyRequest->getAmstoreCreditAmountCustom()) {
            $buyRequestData[Amounts::PRODUCT_AMOUNT] = Amounts::CUSTOM_AMOUNT_PARAM;
            $buyRequestData[Amounts::PRODUCT_CUSTOM_AMOUNT] = $buyRequest->getAmstoreCreditAmountCustom();
        }
        unset($buyRequestData[self::AMOUNT_ORDER_PARAM]);

        $productBuyRequest->setValue($this->serializer->serialize($buyRequestData));
    }

    /**
     * @param DataObject $buyRequest
     * @param float $amount
     */
    public function setCustomAmount(DataObject $buyRequest, float $amount): void
    {
        $buyRequest->setAmstoreCreditAmountCustom($amount);
    }
}
