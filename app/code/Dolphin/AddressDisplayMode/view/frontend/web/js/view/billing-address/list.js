/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/model/address-list',
    'mage/translate',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/customer-data'
], function ($, ko, Component, addressList, $t, customer, quote, customerData) {
    'use strict';

    var countryData = customerData.get('directory-data');

    var newAddressOption = {
            /**
             * Get new address label
             * @returns {String}
             */
            getAddressInline: function () {
                return $t('New Address');
            },
            customerAddressId: null
        },
        addressOptions = addressList().filter(function (address) {
            return address.getType() === 'customer-address';
        });

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/billing-address',
            selectedAddress: null,
            isNewAddressSelected: false,
            addressOptions: addressOptions,
            exports: {
                selectedAddress: '${ $.parentName }:selectedAddress'
            }
        },

        /**
         * @returns {Object} Chainable.
         */
        initConfig: function () {
            this._super();
            this.addressOptions.push(newAddressOption);


            return this;
        },

        /**
         * @return {exports.initObservable}
         */
        initObservable: function () {
            this._super()
                .observe('selectedAddress isNewAddressSelected')
                .observe({
                    isNewAddressSelected: !customer.isLoggedIn() || !addressOptions.length
                });

            /*this.isSelected = ko.computed(function () {
                var isSelected = false,
                    billingAddress = checkoutData.getSelectedShippingAddress();
                if (billingAddress) {
                    // isSelected = billingAddress.getKey() == this.address().getKey(); //eslint-disable-line eqeqeq
                    isSelected = false;
                }
                return isSelected;
            }, this);*/

            return this;
        },

        /**
         * @param {Object} address
         * @return {*}
         */
        addressOptionsText: function (address) {
            return address.getAddressInline();
        },

        /**
         * @param {Object} address
         */
        onAddressChange: function (address) {
            this.selectedAddress(address);
            console.log('onAddressChange');
            // $('.checkout-billing-address .fieldset').find('button.action-update').trigger('click');
            //console.log('isDefaultShipping : ' + address.isDefaultShipping());

            this.isNewAddressSelected(address === newAddressOption);

        },

        addNewBillingAddress: function () {
            // this.onAddressChange(newAddressOption);
            this.isNewAddressSelected(true);
        },

        /**
         * @param {String} countryId
         * @return {String}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
        },

        /**
         * Get customer attribute label
         *
         * @param {*} attribute
         * @returns {*}
         */
        getCustomAttributeLabel: function (attribute) {
            var resultAttribute;

            if (typeof attribute === 'string') {
                return attribute;
            }

            if (attribute.label) {
                return attribute.label;
            }

            resultAttribute = _.findWhere(this.source.get('customAttributes')[attribute['attribute_code']], {
                value: attribute.value
            });

            return resultAttribute && resultAttribute.label || attribute.value;
        },

        /** Set selected customer billing address  */
        selectAddress: function () {

            selectShippingAddressAction(this.address());
            checkoutData.setSelectedShippingAddress(this.address().getKey());
        },

    });
});
