<?php
/**
 * Created by PhpStorm.
 * User: Varun Verma
 * Date: 29/7/20
 * Time: 2:51 AM
 */

namespace Aceturtle\CustomerAccountChanges\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\ResourceModel\Customer\Collection;

class Data extends AbstractHelper
{
    public function __construct(Context $context,
                                Collection $customerCollection)
    {
        $this->customerCollection = $customerCollection;
        parent::__construct($context);
    }

    public function getConfigValues($field, $storeId) {

        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }
    public function getGeneralConfig($code, $storeId = null) {
        return $this->getConfigValues(self::XML_PATH_MOBILE_OTP . 'general/' . $code, $storeId);
    }

    public function searchCustomersByAttributeValue($attributeCode, $value)
    {
        $collection = $this->customerCollection->addAttributeToSelect('*')
            ->addAttributeToFilter($attributeCode,$value)
            ->load();
        $customers = $collection->getData();
        return $customers;
    }
}