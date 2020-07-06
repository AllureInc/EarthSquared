<?php

declare (strict_types = 1);

namespace Dolphin\Freeshippingbar\Model\Config\Source;

class AnimationEffect implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => 'FadeIn', 'label' => __('FadeIn')],
            ['value' => 'FadeOut', 'label' => __('FadeOut')],
            ['value' => 'BounceInTop', 'label' => __('BounceInTop')],
            ['value' => 'BounceInRight', 'label' => __('BounceInRight')],
            ['value' => 'BounceInBottom', 'label' => __('BounceInBottom')],
            ['value' => 'BounceInLeft', 'label' => __('BounceInLeft')],
            ['value' => 'BounceInForward', 'label' => __('BounceInForward')],
            ['value' => 'BounceInBack', 'label' => __('BounceInBack')],
            ['value' => 'SlideINForward', 'label' => __('SlideINForward')],
            ['value' => 'SlideINBack', 'label' => __('SlideINBack')],
            ['value' => 'PuffIn', 'label' => __('PuffIn')],
            ['value' => 'PuffOut', 'label' => __('PuffOut')],
        ];
    }

    public function toArray()
    {
        return [
            'FadeIn' => __('FadeIn'),
            'FadeOut' => __('FadeOut'),
            'BounceInTop' => __('BounceInTop'),
            'BounceInRight' => __('BounceInRight'),
            'BounceInBottom' => __('BounceInBottom'),
            'BounceInLeft' => __('BounceInLeft'),
            'BounceInForward' => __('BounceInForward'),
            'BounceInBack' => __('BounceInBack'),
            'SlideINForward' => __('SlideINForward'),
            'SlideINBack' => __('SlideINBack'),
            'PuffIn' => __('PuffIn'),
            'PuffOut' => __('PuffOut'),
        ];
    }
}
