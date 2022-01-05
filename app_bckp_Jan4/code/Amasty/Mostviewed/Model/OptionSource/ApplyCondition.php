<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ApplyCondition implements OptionSourceInterface
{
    const ANY_PRODUCTS = 0;
    const ALL_PRODUCTS = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ANY_PRODUCTS, 'label' => __('Any Bundle Product is Chosen')],
            ['value' => self::ALL_PRODUCTS, 'label' => __('All Bundle Products are Chosen')]
        ];
    }
}
