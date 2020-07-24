/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_SalesRule/js/action/set-coupon-code',
    'Magento_SalesRule/js/action/cancel-coupon',
    'Magento_SalesRule/js/model/coupon',
    'mage/translate'
], function ($, ko, Component, quote, setCouponCodeAction, cancelCouponAction, coupon, $t) {
    'use strict';

    var totals = quote.getTotals(),
        couponCode = coupon.getCouponCode(),
        isApplied = coupon.getIsApplied();

    if (totals()) {
        couponCode(totals()['coupon_code']);
    }
    isApplied(couponCode() != null);

    return Component.extend({
        defaults: {
            template: 'Magento_SalesRule/payment/discount'
        },
        couponCode: couponCode,

        /**
         * Applied flag
         */
        isApplied: isApplied,

        /**
         * Coupon code application procedure
         */
        apply: function () {
            if (this.validate()) {
                $('.trade#discount-form').removeClass('removedCoupon');
                setCouponCodeAction(couponCode(), isApplied);
                $('.message.message-success.success.loaded').show();
            }
        },

        /**
         * Cancel using coupon
         */
        cancel: function () {
            if (this.validate()) {
                couponCode('');
                cancelCouponAction(isApplied);
                $('.trade#discount-form').addClass('removedCoupon');                
            }

        },

        showApplyMessage: function(){            
            $('.trade#discount-form .payment-option-inner').hide();
            $('.trade#discount-form .actions-toolbar').hide();
            var discount = parseFloat(quote.totals().subtotal - quote.totals().subtotal_with_discount);           
            return $t('Promotional Code '+quote.totals().coupon_code+' successfully applied. You received a discount of £'+discount.toFixed(2));
        },

        /**
         * Coupon form validation
         *
         * @returns {Boolean}
         */
        validate: function () {
            var form = '#discount-form';

            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
