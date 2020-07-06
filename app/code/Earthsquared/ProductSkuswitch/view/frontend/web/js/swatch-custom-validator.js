define([
    'jquery',
    'jquery/validate'
], function ($) {
	'use strict';

	$.each({
		"custom-required-rule":[
			function(value){
				return !$.mage.isEmpty(value);
			},
			function(value, element){
				console.log(element);
				var label = $(element).parent().find('.swatch-attribute-label').text();
				if(typeof label === 'string'){
					return $.mage.__('Please select a %1.').replace('%1',label);
				}
				return $.mage.__('This is a required field')
			}
		]
	},function(i,rule){
		rule.unshift(i);
		$.validator.addMethod.apply($.validator,rule);
	});
	$.validator.addClassRules({
		"custom-required-rule":{
			required: true
		}
	});
});