<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Block\Adminhtml\System\Config\Element;

class AmCartField extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @inheritdoc
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($this->getModuleManager() && $this->getModuleManager()->isEnabled('Amasty_Cart')) {
            $result = parent::render($element);
        } else {
            $result = '';
        }

        return $result;
    }
}
