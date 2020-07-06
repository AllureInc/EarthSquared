<?php

declare (strict_types = 1);

namespace Dolphin\Freeshippingbar\Model\Config\Source;

class DisplayOn implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => 'Home Page', 'label' => __('Home Page')],
            ['value' => 'Category Page', 'label' => __('Category Page')],
            ['value' => 'Search Result Page', 'label' => __('Search Result Page')],
            ['value' => 'Product Page', 'label' => __('Product Page')],
            ['value' => 'Cart Page', 'label' => __('Cart Page')],
            ['value' => 'Checkout Page', 'label' => __('Checkout Page')],
        ];
    }

    public function toArray()
    {
        return [
            'Home Page' => __('Home Page'),
            'Category Page' => __('Category Page'),
            'Search Result Page' => __('Search Result Page'),
            'Product Page' => __('Product Page'),
            'Cart Page' => __('Cart Page'),
            'Checkout Page' => __('Checkout Page'),
        ];
    }
}
