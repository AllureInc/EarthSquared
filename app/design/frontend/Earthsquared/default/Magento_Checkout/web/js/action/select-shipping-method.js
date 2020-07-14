/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    '../model/quote',
	'jquery',
    'Magento_Checkout/js/action/get-totals',
	'Magento_Checkout/js/model/cart/totals-processor/default'
], function (quote, $, getTotalsAction, totalsDefaultProvider) {
    'use strict';
    return function (shippingMethod) {
        quote.shippingMethod(shippingMethod);		
		var deferred = $.Deferred();
		//getTotalsAction([], deferred);
		totalsDefaultProvider.estimateTotals(quote.shippingAddress());		
    };
});
