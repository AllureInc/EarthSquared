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

namespace Dolphin\CustomerAddress\Model\Customer\Attribute\Source;

class NatureOfBusiness extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
				['value' => '0', 'label' => __('')],
                ['value' => '1', 'label' => __('Shop')],
                ['value' => '2', 'label' => __('eCommerce')],
                ['value' => '3', 'label' => __('Other')]
            ];
        }
        return $this->_options;
    }
}
