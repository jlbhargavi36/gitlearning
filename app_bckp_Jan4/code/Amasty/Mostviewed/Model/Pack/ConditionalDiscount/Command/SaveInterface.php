<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\ConditionalDiscount\Command;

use Amasty\Mostviewed\Api\Data\ConditionalDiscountInterface;
use Magento\Framework\Exception\CouldNotSaveException;

interface SaveInterface
{
    /**
     * @param ConditionalDiscountInterface $conditionalDiscount
     * @return void
     * @throws CouldNotSaveException
     */
    public function execute(ConditionalDiscountInterface $conditionalDiscount): void;
}
