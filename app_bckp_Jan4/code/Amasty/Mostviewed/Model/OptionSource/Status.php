<?php

namespace Amasty\Mostviewed\Model\OptionSource;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => __('Inactive')
            ],
            [
                'value' => 1,
                'label' => __('Active')
            ]
        ];
    }
}
