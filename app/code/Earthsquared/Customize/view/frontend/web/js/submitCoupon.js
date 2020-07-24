define([
    'jquery',
    'Magento_Checkout/js/model/cart/cache',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Magento_Checkout/js/model/quote'
], function ($, cartCache, totalsProcessor, quote) {
    'use strict';

    //console.log('Submit coupon loaded');       
    //console.log(quote.totals.subscribe(data => {data._latestValue}));
    
    var form = $('#discount-coupon-form');

    $('.submit-coupon').on('click', function () {
        $('#remove-coupon').attr('value',0);
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
                        
                        $('.success.loaded').hide();
                        $('#ajax_message').removeClass('message-error error');                        
                        $('#ajax_message').addClass('message-success success');                        
                        $('#ajax_message div').html(response.message + "<a href='JavaScript:void(0);' class='change-coupon-ajax'>remove</a>");                        
                        $('#ajax_message').show();
                        $('.fieldset.coupon').hide();                        
                        $('.change-coupon-ajax').on('click', function () {
                            removeCoupon();
                        });                        
                    }
                    cartCache.clear('cartVersion');                                     
                    totalsProcessor.estimateTotals(quote.shippingAddress());
                }
            }
        );
    });    
    function removeCoupon(){
        $('.fieldset.coupon').show();
        $('#remove-coupon').attr('value',1);
        $.ajax(
            {
                type: "POST",
                url: form.attr('action'),
                data: form.serialize(),
                success: function (response) {
                    $('.message.message.success.loaded').hide();
                    $('#ajax_message').hide();                                        
                    $('#discount-coupon-form .fieldset.coupon').removeClass('applied');
                    $('#discount-coupon-form #coupon_code').attr('value','');
                    cartCache.clear('cartVersion');                                     
                    totalsProcessor.estimateTotals(quote.shippingAddress());
                }
            }
        );          
    }
    $('.change-coupon').on('click', function () {
        removeCoupon();      
    });
});