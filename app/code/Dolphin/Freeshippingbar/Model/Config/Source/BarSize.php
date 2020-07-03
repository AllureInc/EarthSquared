<?php

declare (strict_types = 1);

namespace Dolphin\Freeshippingbar\Model\Config\Source;

class BarSize implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => 'Small', 'label' => __('Small')],
            ['value' => 'Medium', 'label' => __('Medium')],
            ['value' => 'Large', 'label' => __('Large')],
        ];
    }

    public function toArray()
    {
        return [
            'Small' => __('Small'),
            'Medium' => __('Medium'),
            'Large' => __('Large'),
        ];
    }
}
