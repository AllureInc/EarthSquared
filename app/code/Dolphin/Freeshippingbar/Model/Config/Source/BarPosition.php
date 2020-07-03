<?php

declare (strict_types = 1);

namespace Dolphin\Freeshippingbar\Model\Config\Source;

class BarPosition implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => 'Page Top', 'label' => __('Page Top')],
            ['value' => 'Content Top', 'label' => __('Content Top')],
            ['value' => 'Content Bottom', 'label' => __('Content Bottom')],
        ];
    }

    public function toArray()
    {
        return [
            'Page Top' => __('Page Top'),
            'Content Top' => __('Content Top'),
            'Content Bottom' => __('Content Bottom'),
        ];
    }
}
