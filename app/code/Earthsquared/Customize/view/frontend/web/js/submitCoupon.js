define([
    'jquery',
    'Magento_Checkout/js/model/cart/cache',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Magento_Checkout/js/model/quote'
], function ($, cartCache, totalsProcessor, quote) {
    'use strict';

    console.log('Submit coupon loaded');

    var form = $('#discount-coupon-form');

    $('#submit_coupon').on('click', function () {
        $.ajax(
            {
                type: "POST",
                url: form.attr('action'),
                data: form.serialize(),
                success: function (response) {
                    //console.log(response);
                    cartCache.clear('cartVersion');
                    totalsProcessor.estimateTotals(quote.shippingAddress());
                }
            }
        );
    });
});