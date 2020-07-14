define([
    'jquery',
    'Magento_Checkout/js/model/cart/cache',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Magento_Checkout/js/model/quote'
], function ($, cartCache, totalsProcessor, quote) {
    'use strict';

    console.log('Submit coupon loaded');       
    //console.log(quote.totals.subscribe(data => {data._latestValue}));
    var form = $('#discount-coupon-form');

    $('.submit-coupon').on('click', function () {
        $.ajax(
            {
                type: "POST",
                url: form.attr('action'),
                data: form.serialize(),
                success: function (response) {
                    if(response.errors){
                        $('.success.loaded').hide();
                        $('#ajax_message').removeClass('message-success success');
                        $('#ajax_message').addClass('message-error error');
                        $('#ajax_message div').html(response.message);
                    }else{
                        var valuediscount1 = $('td.amount[data-th="Discount"] span span.price').text();
                        $('.success.loaded').hide();
                        $('#ajax_message').removeClass('message-error error');
                        $('#ajax_message').addClass('message-success success');
                        $('#ajax_message div').html(response.message + "<a href='JavaScript:void(0);' class='change-coupon-ajax'>change</a>");                        
                        $('.fieldset.coupon').hide();                        
                        $('.change-coupon-ajax').on('click', function () {
                            $('.fieldset.coupon').show();
                        });
                    }
                    cartCache.clear('cartVersion');                                     
                    totalsProcessor.estimateTotals(quote.shippingAddress());
                }
            }
        );
    });

    $('.change-coupon').on('click', function () {
        $('.fieldset.coupon').show();
    });
});