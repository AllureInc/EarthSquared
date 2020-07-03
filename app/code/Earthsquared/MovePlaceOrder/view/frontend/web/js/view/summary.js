define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/view/summary',
        'Magento_Checkout/js/model/step-navigator',
    ],
    function(
        $,
        ko,
        Component,
        stepNavigator
    ) {
        'use strict';

        return Component.extend({
            isVisibleShippingButton: function () {
                return !stepNavigator.getActiveItemIndex();
             },
        
            isVisible: function () {
                return stepNavigator.isProcessed('shipping');
            },
            initialize: function () {
                $(function() {
                    $('body').on('click', '#continue-to-payment-trigger', function () {
                        $('#shipping-method-buttons-container').find('.action.continue.primary').trigger('click');
                    });                     
                    $('body').on("click", '#place-order-trigger', function () {
                        $(".payment-method._active").find('.action.primary.checkout').trigger( 'click' );
                    });
                });
                var self = this;
                this._super();
            }

        });
    }
);