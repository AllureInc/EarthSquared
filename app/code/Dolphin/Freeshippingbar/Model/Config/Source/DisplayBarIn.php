<?php

declare (strict_types = 1);

namespace Dolphin\Freeshippingbar\Model\Config\Source;

class DisplayBarIn implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => 'Both', 'label' => __('Both')],
            ['value' => 'Desktop', 'label' => __('Desktop')],
            ['value' => 'Mobile', 'label' => __('Mobile')],
        ];
    }

    public function toArray()
    {
        return [
            'Both' => __('Both'),
            'Desktop' => __('Desktop'),
            'Mobile' => __('Mobile'),
        ];
    }
}
