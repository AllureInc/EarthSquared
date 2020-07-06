<?php

declare (strict_types = 1);

namespace Dolphin\Freeshippingbar\Model\Config\Source;

class FontFamily implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => 'Website Default Font', 'label' => __('Website Default Font')],
            ['value' => 'Open Sans', 'label' => __('Open Sans')],
            ['value' => 'Lato', 'label' => __('Lato')],
            ['value' => 'Roboto', 'label' => __('Roboto')],
            ['value' => 'Poppins', 'label' => __('Poppins')],
            ['value' => 'Oswald', 'label' => __('Oswald')],
            ['value' => 'Raleway', 'label' => __('Raleway')],
            ['value' => 'PT Sans', 'label' => __('PT Sans')],
            ['value' => 'Ubuntu', 'label' => __('Ubuntu')],
        ];
    }

    public function toArray()
    {
        return [
            'Website Default Font' => __('Website Default Font'),
            'Open Sans' => __('Open Sans'),
            'Lato' => __('Lato'),
            'Roboto' => __('Roboto'),
            'Poppins' => __('Poppins'),
            'Oswald' => __('Oswald'),
            'Raleway' => __('Raleway'),
            'PT Sans' => __('PT Sans'),
            'Ubuntu' => __('Ubuntu'),
        ];
    }
}
