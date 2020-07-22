define([
    'jquery',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Customer/js/customer-data'
], function ($, getTotalsAction, customerData) {
 
    $(document).ready(function(){
        $(document).on('change', '.input-text.qty', function(){
            var form = $('form#form-validate');
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                showLoader: true,
                success: function (res) {
                    var parsedResponse = $.parseHTML(res);
                    var result = $(parsedResponse).find("#form-validate");
                    var sections = ['cart'];
 
                    $("#form-validate").replaceWith(result);
 
                    /* Minicart reloading */
                    customerData.reload(sections, true);
 
                    /* Totals summary reloading */
                    var deferred = $.Deferred();
                    getTotalsAction([], deferred);
                    setTimeout(function(){
                        var discount = $('#cart-totals .totals td.amount[data-th="Discount"] span span').text();                        
                        $('#discount-coupon-form .message.message-success.success div span').text(discount.replace(/-/g, ""));
                        $('#ajax_message div span').text(discount.replace(/-/g, ""));
                    }, 3000);                    
                },
                error: function (xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });            
        });

    });
});