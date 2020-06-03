<?php

namespace Dolphin\CustomerAccountLinksManager\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Action implements ArrayInterface
{
    const EXCLUDE_SELECTED = 1;
    const INCLUDE_SELECTED = 2;

    public function toOptionArray()
    {
        return [
            [
                'value' => self::EXCLUDE_SELECTED,
                'label' => __('Hide all selected links')
            ],
            [
                'value' => self::INCLUDE_SELECTED,
                'label' => __('Display only links selected')
            ],
        ];
    }
}
