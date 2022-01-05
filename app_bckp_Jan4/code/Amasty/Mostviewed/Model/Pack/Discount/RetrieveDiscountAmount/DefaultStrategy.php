<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\Discount\RetrieveDiscountAmount;

use Amasty\Mostviewed\Api\PackRepositoryInterface;
use Amasty\Mostviewed\Model\Pack\Finder\Result\SimplePack;
use Magento\Quote\Model\Quote\Item\AbstractItem;

class DefaultStrategy implements RetrieveStrategyInterface
{
    /**
     * @var PackRepositoryInterface
     */
    private $packRepository;

    public function __construct(PackRepositoryInterface $packRepository)
    {
        $this->packRepository = $packRepository;
    }

    public function execute(AbstractItem $item, SimplePack $simplePack): float
    {
        $pack = $this->packRepository->getById($simplePack->getComplexPack()->getPackId());
        return $pack->getChildProductDiscount((int) $item->getProduct()->getId())
            ?? (float) $pack->getDiscountAmount();
    }
}
