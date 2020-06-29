define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
 'use strict';
 
    return function(targetModule){
 
        var reloadPrice = targetModule.prototype._reloadPrice;
        var reloadPriceWrapper = wrapper.wrap(reloadPrice, function(original){
        var result = original();
        var simpleSku = this.options.spConfig.skus[this.simpleProduct];
        var simpleName = this.options.spConfig.names[this.simpleProduct];
 
            if(simpleSku != '') {
                $('div.product-info-main .psku span').html(simpleSku);
            }
            if(simpleName != '') {
                $('div.product-info-main .page-title-wrapper.product h1 span').html(simpleName);
            }            
            return result;
        });
        targetModule.prototype._reloadPrice = reloadPriceWrapper;
        return targetModule;
 };
});