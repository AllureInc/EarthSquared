<?php
/**
 * A Magento 2 module named Dolphin/CustomerAddress
 * Copyright (C) 2019 
 * 
 * This file included in Dolphin/CustomerAddress is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Dolphin\CustomerAddress\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
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

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'nature_of_business', [
            'type' => 'int',
            'label' => 'Nature of Business',
            'input' => 'select',
            'source' => 'Dolphin\CustomerAddress\Model\Customer\Attribute\Source\NatureOfBusiness',
            'required' => false,
            'visible' => true,
            'position' => 333,
            'system' => false,
            'backend' => ''
        ]);
        
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'nature_of_business')
        ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'customer_account_create',
                'customer_account_edit'
            ]
        ]);
        $attribute->save();

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'nature_of_business_other', [
            'type' => 'varchar',
            'label' => 'Nature of Business Other',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 333,
            'system' => false,
            'backend' => ''
        ]);
        
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'nature_of_business_other')
        ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'customer_account_create',
                'customer_account_edit'
            ]
        ]);
        $attribute->save();

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'additional_information', [
            'type' => 'varchar',
            'label' => 'Additional Information',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 333,
            'system' => false,
            'backend' => ''
        ]);
        
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'additional_information')
        ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'customer_account_create',
                'customer_account_edit'
            ]
        ]);
        $attribute->save();
    }
}
