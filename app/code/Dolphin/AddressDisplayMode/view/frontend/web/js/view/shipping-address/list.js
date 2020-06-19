define([
    'jquery',
    'underscore',
    'ko',
    'mageUtils',
    'uiComponent',
    'uiLayout',
    'mage/translate',
    'Magento_Customer/js/model/address-list',
    'Magento_Customer/js/customer-data'
], function ($, _, ko, utils, Component, layout, $t, addressList, customerData) {
    'use strict';

    var defaultRendererTemplate = {
        parent: '${ $.$data.parentName }',
        name: '${ $.$data.name }',
        component: 'Dolphin_AddressDisplayMode/js/view/shipping-address/address-renderer/default',
        provider: 'checkoutProvider'
    };

    var countryData = customerData.get('directory-data');

    return Component.extend({
        defaults: {
            template: 'Dolphin_AddressDisplayMode/shipping-address/list',
            visible: addressList().length > 0,
            rendererTemplates: [],
            isNewAddressAdded: ko.observable(false)
        },

        addressOptionsText: function (address) {
            if (!address.isEditable())
                return address.getAddressInline();
            else {
                let inlineAddress = address.firstname + ' ' + address.lastname
                    + ' ' + address.street.join(', ')
                    + ', ' + address.city + ', ' + address.region + ' ' + address.postcode
                    + ', ' + this.getCountryName(address.countryId);
                return $t('New Address') + ' - ' + inlineAddress;
            }
        },

        selectAddress: function () {
            let selectKey = $('select.select-shipping-address-dd').val();
            if (selectKey == 'new-address') {
                $('#checkout-step-shipping .new-address-popup').find('button').trigger('click');
            } else {
                $('.shipping-address-item.address-key-' + selectKey).find('button.action-select-shipping-item').trigger('click');
            }
        },

        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
        },

        /** @inheritdoc */
        initialize: function () {
            this._super()
                .initChildren();

            addressList.subscribe(function (changes) {
                    var self = this;

                    changes.forEach(function (change) {
                        if (change.status === 'added') {
                            self.createRendererComponent(change.value, change.index);
                        }
                    });
                },
                this,
                'arrayChange'
            );


            return this;
        },

        /** @inheritdoc */
        initConfig: function () {
            this._super();
            // the list of child components that are responsible for address rendering
            this.rendererComponents = [];

            return this;
        },

        /** @inheritdoc */
        initChildren: function () {
            _.each(addressList(), this.createRendererComponent, this);

            let hasNewAddress = addressList.some(function (address) {
                return address.getType() == 'new-customer-address'; //eslint-disable-line eqeqeq
            });
            this.isNewAddressAdded(hasNewAddress);

            return this;
        },

        /**
         * Create new component that will render given address in the address list
         *
         * @param {Object} address
         * @param {*} index
         */
        createRendererComponent: function (address, index) {

            var rendererTemplate, templateData, rendererComponent;

            if (index in this.rendererComponents) {
                this.rendererComponents[index].address(address);
            } else {
                // rendererTemplates are provided via layout
                rendererTemplate = address.getType() != undefined && this.rendererTemplates[address.getType()] != undefined ? //eslint-disable-line
                    utils.extend({}, defaultRendererTemplate, this.rendererTemplates[address.getType()]) :
                    defaultRendererTemplate;
                templateData = {
                    parentName: this.name,
                    name: index
                };
                rendererComponent = utils.template(rendererTemplate, templateData);
                utils.extend(rendererComponent, {
                    address: ko.observable(address)
                });
                layout([rendererComponent]);
                this.rendererComponents[index] = rendererComponent;
            }

            let hasNewAddress = addressList.some(function (address) {
                return address.getType() == 'new-customer-address'; //eslint-disable-line eqeqeq
            });
            this.isNewAddressAdded(hasNewAddress);
        }
    });
});
