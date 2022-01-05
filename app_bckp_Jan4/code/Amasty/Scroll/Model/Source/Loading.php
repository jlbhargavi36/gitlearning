<?php

namespace Amasty\Scroll\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Loading implements OptionSourceInterface
{
    const NONE = 'none';

    const AUTO = 'auto';

    const BUTTON = 'button';

    const COMBINED = 'combined';

    const DEFAULT_COMBINED_VALUE = 3;

    const COMBINED_BUTTON_AUTO = 'combined_button_auto';

    const DEFAULT_COMBINED_BUTTON_AUTO_VALUE = 0;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::NONE,
                'label' => __('None - module is disabled')
            ],
            [
                'value' => self::AUTO,
                'label' => __('Automatic - on page scroll')
            ],
            [
                'value' => self::BUTTON,
                'label' => __('Button - on button click')
            ],
            [
                'value' => self::COMBINED,
                'label' => __('Combined - automatic + button')
            ],
            [
                'value' => self::COMBINED_BUTTON_AUTO,
                'label' => __('Combined - button + automatic')
            ]
        ];
    }
}
