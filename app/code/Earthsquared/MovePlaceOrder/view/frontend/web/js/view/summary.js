define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/view/summary',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/quote',
        'Magento_Ui/js/model/messageList'
    ],
    function(
        $,
        ko,
        Component,
        stepNavigator,
        quote,
        messageList
    ) {
        'use strict';

        return Component.extend({
			errorValidationMessage: ko.observable(false),
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
						if (!quote.paymentMethod()) {
							messageList.addErrorMessage({ message: $.mage.__('Please specify a payment method.') });
							return false;
						}
                        $(".payment-method._active").find('.action.primary.checkout').trigger( 'click' );
                    });
                });
                var self = this;
                this._super();
            }

        });
    }
);
