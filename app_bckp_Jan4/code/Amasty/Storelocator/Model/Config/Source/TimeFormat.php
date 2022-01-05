<?php

namespace Amasty\Storelocator\Model\Config\Source;

/**
 * Class TimeFormat
 */
class TimeFormat implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => '0',
                'label' => __('24h'),
            ],
            [
                'value' => '1',
                'label' => __('12h'),
            ]
        ];
    }
}
