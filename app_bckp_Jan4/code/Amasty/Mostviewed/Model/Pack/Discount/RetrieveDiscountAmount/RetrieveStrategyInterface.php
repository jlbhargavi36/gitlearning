<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\Discount\RetrieveDiscountAmount;

use Amasty\Mostviewed\Api\Data\PackInterface;
use Amasty\Mostviewed\Model\Pack\Finder\ItemPool;
use Amasty\Mostviewed\Model\Pack\Finder\Result\SimplePack;
use Magento\Quote\Model\Quote\Item\AbstractItem;

interface RetrieveStrategyInterface
{
    public function execute(AbstractItem $item, SimplePack $simplePack): float;
}
