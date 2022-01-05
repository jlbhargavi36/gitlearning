<?php

namespace Amasty\Storelocator\Model\Config\Source;

/**
 * Class RadiusType
 */
class RadiusType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'select',
                'label' => __('Dropdown'),
            ],
            [
                'value' => 'range',
                'label' => __('Slider'),
            ]
        ];
    }
}
