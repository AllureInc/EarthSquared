require([
    'jquery',
    'mage/cookies',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'jquery/jquery-storageapi',
    'mage/storage'
], function($,modal,storage){
	var cookieData = new Array();
var updatedcookieData = new Array();
	$( document ).ready(function() {
		if(!!$.cookie('rowcollection') != "")
		{
			var loadcookieCollection = $.parseJSON($.cookie('rowcollection'));		
			var total_qty = 0;
			$.each(loadcookieCollection,function(index,value){			
				total_qty = parseInt(total_qty) + parseInt(value.qty);
				console.log(value.qty);				
				$('#left'+index).children('.product-item').children('.quickproduct').children('.product-item-details').children('.productprice-qty').children('.quickqty').children('#qty').attr('value',value.qty);				
			});	
			var total_items = $('.quicksidebar .subrowitems .rowdata').length;
			$('.quicksidebar .subtotal').html('Selected Items<span class="total_products">(<span class="countqty">'+total_qty+'</span> Products</span> , <span class="total_items">'+total_items+' Items)</span>');				
		}
	});
	var click_qty = 0;
	$(document).on('click','.qty-inc', function(){
			var dataString = new Array();
			var selectedOptionsData = [];
			var type_id = $(this).closest('.subcategoryproduct-collection').find('.type_id').val();       
				if(type_id == 'configurable'){
					if($(this).closest('.subcategoryproduct-collection').find('.swatch-option').hasClass('selected')){
						var selectedOptionId = $(this).closest('.subcategoryproduct-collection').find('.swatch-option.selected').attr('option-id');			
						var selectedOptionLabel = $(this).closest('.subcategoryproduct-collection').find('.swatch-option.selected').attr('option-label');
						var selectedOptionProductId = $(this).closest('.subcategoryproduct-collection').find('.swatch-option.selected').attr('associated-id');
						selectedOptionsData = {'option_id': selectedOptionId,'option_label':selectedOptionLabel, 'associated_id':selectedOptionProductId};					
						$(this).closest('.subcategoryproduct-collection').find('.swatch-error').hide();
					} else {
						$(this).closest('.subcategoryproduct-collection').find('.swatch-error').html('<span style="color:red">Please select colour</span>');
						return false;
					}
				}
	            var inputqty = $(this).parents('.field.quickqty').find("input.input-text.qty").val((+$(this).parents('.field.quickqty').find("input.input-text.qty").val() + 1) || 0);
	            $(this).focus();

	            var product_id = $(this).closest('.subcategoryproduct-collection').find('.productid').val();
				var name =  $(this).closest('.subcategoryproduct-collection').find('.product-name .subpname').text();                        
				var sku = $(this).closest('.subcategoryproduct-collection').find('.product-ref').text();       

				var qty = $(inputqty).val();

				var image = $(this).closest('.subcategoryproduct-collection').find('.product-image-photo').attr('src');       	            
				var finalPrice = $(this).closest('.subcategoryproduct-collection').find('.price-box .price').text();

				var rowcollection = {
	            	'product_id':product_id,
	            	'name':name,
	            	'sku':sku,
	            	'type_id':type_id,
	            	'qty':qty,
	            	'selectedOptionsData':selectedOptionsData,
	            	'image':image,
	            	'finalPrice': finalPrice
	            };

				dataString.push(rowcollection);

				var sidedata = $.parseJSON(JSON.stringify(dataString));
				console.log(sidedata);
				
				cookieData.push(rowcollection);

				var newCookieData = {}
				
				//old cookie data push to new 
				var total_qty = 0;
				if(!!$.cookie('rowcollection') != "")
				{
					newCookieData = $.parseJSON($.cookie('rowcollection'));					
					// $.each(newCookieData,function(index,value){			
					// 	total_qty = parseInt(total_qty) + parseInt(value.qty);						
					// });						
				}

				for(var i=0; i<cookieData.length; i++) {
					newCookieData[cookieData[i]['product_id']] = cookieData[i];							
				}
				
				// `newCookieData` is now:				
				$.cookie("rowcollection", JSON.stringify(newCookieData));								
					var flag = 0;
				//click_qty = parseInt(sidedata[0].qty) + parseInt(click_qty);
				//console.log(click_qty);
						$( ".subrowitems .rowdata" ).each(function( index ) {	
							if($(this).attr('id') == '_'+sidedata[0].product_id){															
								flag = 1;									
									if(sidedata[0].selectedOptionsData.length === 0)
									{
										var html = '<div class="rowdata row'+sidedata[0].product_id+'" id="_'+sidedata[0].product_id+'"><div class="rowqty"><span>'+sidedata[0].qty+'</span></div><div class="rownameswatches"><div class="rowname">'+sidedata[0].name+'</div></div><div class="editrow"><a href="#left'+sidedata[0].product_id+'"><img src="http://staging-trade.earthsquared.com/pub/media/pen.png"></a></div><div class="removerow"><img src="http://staging-trade.earthsquared.com/pub/media/garbage.png"></div></div>';	                 																		
										$('.quicksidebar .subrowitems .rowdata#_'+sidedata[0].product_id+'').replaceWith(html);											
										//var total_items = $('.quicksidebar .subrowitems .rowdata').length;
										//$('.quicksidebar .subtotal').html('Selected Items <span class="total_products">('+click_qty+' Products</span> , <span class="total_items">'+total_items+' Items)</span>');				
										return false;	
									} else {
										if($(this).children().find('.swatches').attr('id') == 'a'+sidedata[0].selectedOptionsData.associated_id)
										{										
											var html = '<div class="rowdata row'+sidedata[0].product_id+'" id="_'+sidedata[0].product_id+'"><div class="rowqty"><span>'+sidedata[0].qty+'</span></div><div class="rownameswatches"><div class="rowname">'+sidedata[0].name+'</div><div class="swatches" id="a'+sidedata[0].selectedOptionsData.associated_id+'">Colour: '+sidedata[0].selectedOptionsData.option_label+'</div></div><div class="editrow"><a href="#left'+sidedata[0].product_id+'"><img src="http://staging-trade.earthsquared.com/pub/media/pen.png"></a></div><div class="removerow"><img src="http://staging-trade.earthsquared.com/pub/media/garbage.png"></div></div>';	                 																		
											$('.quicksidebar .subrowitems .rowdata#_'+sidedata[0].product_id+'').replaceWith(html);									
											//var total_items = $('.quicksidebar .subrowitems .rowdata').length;
											//$('.quicksidebar .subtotal').html('Selected Items <span class="total_products">('+click_qty+' Products</span> , <span class="total_items">'+total_items+' Items)</span>');				
											return false;
										} else {										
											var html = '<div class="rowdata row'+sidedata[0].product_id+'" id="_'+sidedata[0].product_id+'"><div class="rowqty"><span>'+sidedata[0].qty+'</span></div><div class="rownameswatches"><div class="rowname">'+sidedata[0].name+'</div><div class="swatches" id="a'+sidedata[0].selectedOptionsData.associated_id+'">Colour: '+sidedata[0].selectedOptionsData.option_label+'</div></div><div class="editrow"><a href="#left'+sidedata[0].product_id+'"><img src="http://staging-trade.earthsquared.com/pub/media/pen.png"></a></div><div class="removerow"><img src="http://staging-trade.earthsquared.com/pub/media/garbage.png"></div></div>';	                 																		
											$('.quicksidebar .subrowitems').append(html);	
											//var total_items = $('.quicksidebar .subrowitems .rowdata').length;
											//$('.quicksidebar .subtotal').html('Selected Items <span class="total_products">('+click_qty+' Products</span> , <span class="total_items">'+total_items+' Items)</span>');				
											return false;
										}		
									}																							
								return false;							
							}
						});		                
		                if(flag == 0){		
		                	    if(sidedata[0].selectedOptionsData.length === 0)
								{									
									var html = '<div class="rowdata row'+sidedata[0].product_id+'" id="_'+sidedata[0].product_id+'"><div class="rowqty"><span>'+sidedata[0].qty+'</span></div><div class="rownameswatches"><div class="rowname">'+sidedata[0].name+'</div></div><div class="editrow"><a href="#left'+sidedata[0].product_id+'"><img src="http://staging-trade.earthsquared.com/pub/media/pen.png"></a></div><div class="removerow"><img src="http://staging-trade.earthsquared.com/pub/media/garbage.png"></div></div>';	                 																		
									$('.quicksidebar .subrowitems').append(html);								
									//var total_items = $('.quicksidebar .subrowitems .rowdata').length;
									//$('.quicksidebar .subtotal').html('Selected Items <span class="total_products">('+click_qty+' Products</span> , <span class="total_items">'+total_items+' Items)</span>');				
								} else {
									var html = '<div class="rowdata row'+sidedata[0].product_id+'" id="_'+sidedata[0].product_id+'"><div class="rowqty"><span>'+sidedata[0].qty+'</span></div><div class="rownameswatches"><div class="rowname">'+sidedata[0].name+'</div><div class="swatches" id="a'+sidedata[0].selectedOptionsData.associated_id+'">Colour: '+sidedata[0].selectedOptionsData.option_label+'</div></div><div class="editrow"><a href="#left'+sidedata[0].product_id+'"><img src="http://staging-trade.earthsquared.com/pub/media/pen.png"></a></div><div class="removerow"><img src="http://staging-trade.earthsquared.com/pub/media/garbage.png"></div></div>';	                 																		
									$('.quicksidebar .subrowitems').append(html);								
									//var total_items = $('.quicksidebar .subrowitems .rowdata').length;
									//$('.quicksidebar .subtotal').html('Selected Items <span class="total_products">('+click_qty+' Products</span> , <span class="total_items">'+total_items+' Items)</span>');				
								}            	
								
							
                            //var html = '<div class="rowdata row'+sidedata[0].product_id+'" id="_'+sidedata[0].product_id+'"><div class="rowqty"><span>'+sidedata[0].qty+'</span></div><div class="rowname">'+sidedata[0].name+'</div><div class="rownameswatches"><div class="swatches">Colour: '+sidedata[0].selectedOptionsData.option_label+'</div></div><div class="editrow"><img src="http://staging-trade.earthsquared.com/pub/media/pen.png"></div><div class="removerow"><img src="http://staging-trade.earthsquared.com/pub/media/garbage.png"></div></div>';		                 				     
					        //$('.quicksidebar .subrowitems').append(html);					       	
		                }
		                 
			
	});
	$(document).on('click','.qty-dec', function(){                  
		if($(this).parents('.field.quickqty').find("input.input-text.qty").is(':enabled')){
	    	$(this).parents('.field.quickqty').find("input.input-text.qty").val(($(this).parents('.field.quickqty').find("input.input-text.qty").val() - 1 > 0) ? ($(this).parents('.field.quickqty').find("input.input-text.qty").val() - 1) : 0);                          
	        $(this).focus();
	    }
	}); 	
	//For edit click focus
	$(document).on('click','.editrow a', function(event){ 	
	    var target = $(this.getAttribute('href'));
	    var focusTarget = this.getAttribute('href');
	    console.log(focusTarget);
	    if( target.length ) {
	        event.preventDefault();
	        $('html, body').stop().animate({
	            scrollTop: target.offset().top - 100
	        }, 1000);
	    }
	    $(focusTarget).children('.product-item').children('.quickproduct').children('.product-item-details').children('.productprice-qty').children('.quickqty').children('#qty').focus();
	});	
	$(document).on('click','.removerow', function(){
		var delRowFromHtml = $(this).closest('.rowdata').attr('id');
		$('#'+delRowFromHtml+'').remove();
		var delRowFromCookie = delRowFromHtml.replace("_", ""); 

			if(!!$.cookie('rowcollection') != "")
			{
				var cookieCollection = $.parseJSON($.cookie('rowcollection'));	
				delete cookieCollection[delRowFromCookie];	
				$.cookie('rowcollection',JSON.stringify(cookieCollection));			
			}
	});	
	var $rows = $('.quickorder-product-collection .subcategoryproduct-collection');
	$('#quicksearch').keyup(function() {
	    var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
	    
	    $rows.show().filter(function() {
	        var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
	        return !~text.indexOf(val);
	    }).hide();
	});
});