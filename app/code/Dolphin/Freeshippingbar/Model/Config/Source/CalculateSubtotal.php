<?php

declare (strict_types = 1);

namespace Dolphin\Freeshippingbar\Model\Config\Source;

class CalculateSubtotal implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => 'Inclusive Of Tax', 'label' => __('Inclusive Of Tax')],
            ['value' => 'Exclusive Of Tax', 'label' => __('Exclusive Of Tax')],
        ];
    }

    public function toArray()
    {
        return [
            'Inclusive Of Tax' => __('Inclusive Of Tax'), '
            Exclusive Of Tax' => __('Exclusive Of Tax'),
        ];
    }
}
