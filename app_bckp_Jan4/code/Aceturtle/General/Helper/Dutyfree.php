<?php
namespace Aceturtle\General\Helper;


use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;

class Dutyfree extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager

        )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
    }

    public function isDutyfreeStore() {
        $storeCode = $this->storeManager->getStore()->getCode();
        if ($storeCode === 'dutyfree') {
            return true;
        }
        return false;
    }
}