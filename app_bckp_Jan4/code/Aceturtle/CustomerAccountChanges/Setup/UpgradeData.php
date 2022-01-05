<?php

namespace Aceturtle\CustomerAccountChanges\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        if (version_compare($context->getVersion(), '2.0.0') < 0){

				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$customerSetup = $objectManager->create('Aceturtle\CustomerAccountChanges\Setup\CustomerSetup');
				$customerSetup->installAttributes($customerSetup);

				

		 }

	 if (version_compare($context->getVersion(), '2.0.3', '<')) {
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	    $customerSetup = $objectManager->create('Aceturtle\CustomerAccountChanges\Setup\CustomerSetup');
            $customerSetup->updateAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'custom_dob',
                [
                    'unique' => false,
		    'required' => false,
                ]
            );
	    $customerSetup->updateAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'anniversary_date',
                [
                    'unique' => false,
		    'required' => false,
                ]
            );
        }

	if (version_compare($context->getVersion(), '2.0.4', '<')) {
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	    $customerSetup = $objectManager->create('Aceturtle\CustomerAccountChanges\Setup\CustomerSetup');
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
        }
    }
}
