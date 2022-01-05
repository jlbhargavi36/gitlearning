<?php
namespace Aceturtle\Redirectcolor\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper 
{
    protected $_registry;

    public function __construct(
        \Magento\Framework\Registry $registry
    )
    {
        $this->_registry = $registry;
    }

    public function getRedirectColor() {
        if($this->_registry->registry('redirectcolor') != "NA") {
            $return = $this->_registry->registry('redirectcolor');
        } else {
            $return = "NA";
        }
        return $return;
    }
}
