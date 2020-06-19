<?php

namespace Dolphin\AddressDisplayMode\Plugin\Block;

use Dolphin\AddressDisplayMode\Helper\Data as DisplayModeHelper;

class LayoutProcessor
{
    /*
    * @var DisplayModeHelper
    */
    protected $displayModeHelper;

    public function __construct(
        DisplayModeHelper $displayModeHelper
    )
    {
        $this->displayModeHelper = $displayModeHelper;
    }

    public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $subject, array $jsLayout)
    {
        if ($this->displayModeHelper->getDisplayMode() == DisplayModeHelper::ADDRESS_DISPLAY_MODE_LIST) {
            // for beforeMethods
            if (
                count($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
                ['children']['beforeMethods']['children']) > 0 &&
                isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
                    ['children']['beforeMethods']['children']['billing-address-form'])
            ) {
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
                ['children']['beforeMethods']['children']['billing-address-form']['children']['billingAddressList']['component'] = 'Dolphin_AddressDisplayMode/js/view/billing-address/list';

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
                ['children']['beforeMethods']['children']['billing-address-form']['children']['billingAddressList']['template'] = 'Dolphin_AddressDisplayMode/billing-address/list';
            }
            // for afterMethods
            if (
                count($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
                ['children']['afterMethods']['children']) > 0 &&
                isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
                    ['children']['afterMethods']['children']['billing-address-form'])
            ) {
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
                ['children']['afterMethods']['children']['billing-address-form']['children']['billingAddressList']['component'] = 'Dolphin_AddressDisplayMode/js/view/billing-address/list';

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
                ['children']['afterMethods']['children']['billing-address-form']['children']['billingAddressList']['template'] = 'Dolphin_AddressDisplayMode/billing-address/list';
            }
            // for all payment-method forms
            if (
                isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children']) &&
                count($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children']) > 1
            ) {

                $paymentForms = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'];
                foreach ($paymentForms as $key => $paymentForm) {
                    if ($paymentForm['component'] == 'Magento_Checkout/js/view/billing-address') {

                        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children']
                        [$key]['children']['billingAddressList']['component'] = 'Dolphin_AddressDisplayMode/js/view/billing-address/list';

                        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children']
                        [$key]['children']['billingAddressList']['template'] = 'Dolphin_AddressDisplayMode/billing-address/list';
                    }
                }
            }
        } else {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['address-list']['component'] = 'Dolphin_AddressDisplayMode/js/view/shipping-address/list';
        }

        /*echo '<pre>';
        print_r($jsLayout);
        exit;*/

        return $jsLayout;
    }
}
