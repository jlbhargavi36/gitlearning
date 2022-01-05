<?php

declare(strict_types=1);

namespace Amasty\Xsearch\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class BestsellersBlockPosition implements OptionSourceInterface
{
    const BEFORE_RECENTLY_VIEWED = 1;
    const AFTER_RECENTLY_VIEWES = 2;

    public function toOptionArray()
    {
        return [
            [
                'label' => __('Show Before Recently Viewed'),
                'value' => self::BEFORE_RECENTLY_VIEWED
            ],
            [
                'label' => __('Show After Recently Viewed'),
                'value' => self::AFTER_RECENTLY_VIEWES
            ]
        ];
    }
}
