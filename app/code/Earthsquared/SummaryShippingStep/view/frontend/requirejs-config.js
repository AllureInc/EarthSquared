var config = {
    config: {
        mixins: {
            /**
             * Mixins for rendering order summary in the shipping step of checkout.
             */
            'Magento_Checkout/js/view/summary/abstract-total': {
                'Earthsquared_SummaryShippingStep/js/view/summary/abstract-total-mixins': true
            }
        }
    }
};