define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
 'use strict';
 
    return function(targetModule){
        var updatePrice = targetModule.prototype._UpdatePrice;
        targetModule.prototype.configurableSku = $('div.product-info-main .sku .value').html();
        targetModule.prototype.configurableName = $('div.product-info-main .page-title-wrapper.product h1 span').html();
        var updatePriceWrapper = wrapper.wrap(updatePrice, function(original){
            var allSelected = true;
            for(var i = 0; i<this.options.jsonConfig.attributes.length;i++){
                if (!$('div.product-info-main .product-options-wrapper .swatch-attribute.' + this.options.jsonConfig.attributes[i].code).attr('option-selected')){
                 allSelected = false;
                }
            }
            var simpleSku = this.configurableSku;
            var simpleName = this.configurableName;            
            if (allSelected){
                var products = this._CalcProducts();
                simpleSku = this.options.jsonConfig.skus[products.slice().shift()];
                simpleName = this.options.jsonConfig.names[products.slice().shift()];
            }
            $('div.product-info-main .psku span').html(simpleSku);
            $('div.product-info-main .psku').html(simpleSku);
            $('div.product-info-main .page-title-wrapper.product h1 span').html(simpleName);
            $('div.product-info-main .am-title').html(simpleName);
              return original();
        });
 
        targetModule.prototype._UpdatePrice = updatePriceWrapper;
        return targetModule;
 };
});