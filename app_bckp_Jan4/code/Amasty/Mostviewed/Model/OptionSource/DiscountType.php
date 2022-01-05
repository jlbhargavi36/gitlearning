<?php

namespace Amasty\Mostviewed\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class DiscountType implements OptionSourceInterface
{
    const FIXED = 0;
    const PERCENTAGE = 1;
    const CONDITIONAL = 2;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::FIXED, 'label' => __('Fixed')],
            ['value' => self::PERCENTAGE, 'label' => __('Percentage')],
            ['value' => self::CONDITIONAL, 'label' => __('Conditional Discounts')]
        ];
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getLabelByValue($value)
    {
        foreach ($this->toOptionArray() as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }

        return '';
    }
}
