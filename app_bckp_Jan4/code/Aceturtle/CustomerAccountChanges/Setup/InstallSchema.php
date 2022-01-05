<?php

/**
 * Created by PhpStorm.
 * User: Amit Thakur
 */

namespace Aceturtle\CustomerAccountChanges\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;

class InstallData implements InstallDataInterface
{

    private $customerSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'mobile_no', [
            'type' => 'varchar',
            'label' => 'Mobile Number',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'unique' => false,
            'position' => 333,
            'system' => false,
            'backend' => ''
        ]);

        $mobileNo = $customerSetup->getEavConfig()->getAttribute('customer', 'mobile_no')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $mobileNo->save();

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'anniversary_date', [
            'type' => 'varchar',
            'label' => 'Date of anniversary',
            'input' => 'date',
            'source' => '',
            'required' => false,
            'visible' => true,
            'unique' => false,
            'position' => 333,
            'system' => false,
            'backend' => ''
        ]);

        $anniversaryDate = $customerSetup->getEavConfig()->getAttribute('customer', 'anniversary_date')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $anniversaryDate->save();

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'custom_dob', [
            'type' => 'varchar',
            'label' => 'Dob',
            'input' => 'date',
            'source' => '',
            'required' => false,
            'visible' => true,
            'unique' => false,
            'position' => 333,
            'system' => false,
            'backend' => ''
        ]);

        

        $dob = $customerSetup->getEavConfig()->getAttribute('customer', 'custom_dob')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $dob->save();
    }
}