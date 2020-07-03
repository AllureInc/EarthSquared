<?php

declare (strict_types = 1);

namespace Dolphin\Freeshippingbar\Model\Config\Source;

class TextAlign implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => 'Left', 'label' => __('Left')],
            ['value' => 'Center', 'label' => __('Center')],
            ['value' => 'Right', 'label' => __('Right')],
        ];
    }

    public function toArray()
    {
        return [
            'Left' => __('Left'),
            'Center' => __('Center'),
            'Right' => __('Right'),
        ];
    }
}
