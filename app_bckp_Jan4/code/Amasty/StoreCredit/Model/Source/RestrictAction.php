<?php

declare(strict_types=1);

namespace Amasty\StoreCredit\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RestrictAction implements OptionSourceInterface
{
    const INCLUDE = 0;
    const EXCLUDE = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::INCLUDE, 'label' => __('Include')],
            ['value' => self::EXCLUDE, 'label' => __('Exclude')]
        ];
    }
}
