require([
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/cookies',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'jquery/jquery-storageapi',
    'mage/storage',
    'mage/url'    
], function($,customerData,modal,storage,url){
	var cookieData = new Array();
	var updatedcookieData = new Array();
	var linkUrl = $('#baseurl').val();	
	$( document ).ready(function() {		
		loadSubtotalSection();
		//sideBar();
	});
	$( window ).resize(function() {
		//sideBar();
	});
	var click_qty = 0;
	$(document).on('click','.qty-inc', function(){
		//console.log("test--");
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
				//console.log("QTY --"+$(this).parents('.field.quickqty').find("input.input-text.qty").val());
	            var inputqty = $(this).parents('.field.quickqty').find("input.input-text.qty").val((+$(this).parents('.field.quickqty').find("input.input-text.qty").val() + 1) || 0);
	            $(this).focus();

	            var product_id = $(this).closest('.subcategoryproduct-collection').find('.productid').val();
				var name =  $(this).closest('.subcategoryproduct-collection').find('.product-name .subpname').text();
				var sku = $(this).closest('.subcategoryproduct-collection').find('#product-ref').attr('value');

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
				//console.log(sidedata);

				cookieData.push(rowcollection);				
				var newCookieData = {}

				//old cookie data push to new
				var total_qty = 0;
				if(!!localStorage.getItem('rowcollection') != "")
				{
					newCookieData = $.parseJSON(localStorage.getItem('rowcollection'));
					// $.each(newCookieData,function(index,value){
					// 	total_qty = parseInt(total_qty) + parseInt(value.qty);
					// });
				}
				console.log('cookieLength-->'+cookieData.length);
				for(var i=0; i<cookieData.length; i++) {
					newCookieData[cookieData[i]['product_id']] = cookieData[i];
				}
				console.log(newCookieData);
				// `newCookieData` is now:
				localStorage.setItem("rowcollection", JSON.stringify(newCookieData));
				//$.cookie("rowcollection", JSON.stringify(newCookieData));
					var flag = 0;
				//click_qty = parseInt(sidedata[0].qty) + parseInt(click_qty);
				//console.log(click_qty);
						$( ".subrowitems .rowdata" ).each(function( index ) {
							if($(this).attr('id') == '_'+sidedata[0].product_id){
								flag = 1;

								//console.log("flag 1");
									if(sidedata[0].selectedOptionsData.length === 0)
									{
										var html = '<div class="rowdata row'+sidedata[0].product_id+'" id="_'+sidedata[0].product_id+'"><div class="rowqty"><span>'+sidedata[0].qty+'</span></div><div class="rownameswatches"><div class="rowname">'+sidedata[0].name+'</div></div><div class="editrow"><a href="#left'+sidedata[0].product_id+'"><img src="'+linkUrl+'pub/media/pen.png"></a><input type="hidden" id="sidebarsku" name="sidebarsku" value="'+sidedata[0].sku+'"/></div><div class="removerow"><img src="'+linkUrl+'pub/media/garbage.png"></div></div>';
										$('.quicksidebar .subrowitems .rowdata#_'+sidedata[0].product_id+'').replaceWith(html);
										//var total_items = $('.quicksidebar .subrowitems .rowdata').length;
										//$('.quicksidebar .subtotal').html('Selected Items <span class="total_products">('+click_qty+' Products</span> , <span class="total_items">'+total_items+' Items)</span>');
										return false;
									} else {
										if($(this).children().find('.swatches').attr('id') == 'a'+sidedata[0].selectedOptionsData.associated_id)
										{
											var html = '<div class="rowdata row'+sidedata[0].product_id+'" id="_'+sidedata[0].product_id+'"><div class="rowqty"><span>'+sidedata[0].qty+'</span></div><div class="rownameswatches"><div class="rowname">'+sidedata[0].name+'</div><div class="swatches" id="a'+sidedata[0].selectedOptionsData.associated_id+'">Colour: '+sidedata[0].selectedOptionsData.option_label+'</div></div><div class="editrow"><a href="#left'+sidedata[0].product_id+'"><img src="'+linkUrl+'pub/media/pen.png"></a><input type="hidden" id="sidebarsku" name="sidebarsku" value="'+sidedata[0].sku+'"/></div><div class="removerow"><img src="'+linkUrl+'pub/media/garbage.png"></div></div>';
											$('.quicksidebar .subrowitems .rowdata#_'+sidedata[0].product_id+'').replaceWith(html);
											//var total_items = $('.quicksidebar .subrowitems .rowdata').length;
											//$('.quicksidebar .subtotal').html('Selected Items <span class="total_products">('+click_qty+' Products</span> , <span class="total_items">'+total_items+' Items)</span>');
											return false;
										} else {
											var html = '<div class="rowdata row'+sidedata[0].product_id+'" id="_'+sidedata[0].product_id+'"><div class="rowqty"><span>'+sidedata[0].qty+'</span></div><div class="rownameswatches"><div class="rowname">'+sidedata[0].name+'</div><div class="swatches" id="a'+sidedata[0].selectedOptionsData.associated_id+'">Colour: '+sidedata[0].selectedOptionsData.option_label+'</div></div><div class="editrow"><a href="#left'+sidedata[0].product_id+'"><img src="'+linkUrl+'pub/media/pen.png"></a><input type="hidden" id="sidebarsku" name="sidebarsku" value="'+sidedata[0].sku+'"/></div><div class="removerow"><img src="'+linkUrl+'pub/media/garbage.png"></div></div>';
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
								//console.log("flag 0");
		                	    if(sidedata[0].selectedOptionsData.length === 0)
								{
									var html = '<div class="rowdata selected row'+sidedata[0].product_id+'" id="_'+sidedata[0].product_id+'"><div class="rowqty"><span>'+sidedata[0].qty+'</span></div><div class="rownameswatches"><div class="rowname">'+sidedata[0].name+'</div></div><div class="editrow"><a href="#left'+sidedata[0].product_id+'"><img src="'+linkUrl+'pub/media/pen.png"></a><input type="hidden" id="sidebarsku" name="sidebarsku" value="'+sidedata[0].sku+'"/></div><div class="removerow"><img src="'+linkUrl+'pub/media/garbage.png"></div></div>';
									$('.quicksidebar .subrowitems').append(html);
									//var total_items = $('.quicksidebar .subrowitems .rowdata').length;
									//$('.quicksidebar .subtotal').html('Selected Items <span class="total_products">('+click_qty+' Products</span> , <span class="total_items">'+total_items+' Items)</span>');
								} else {
									var html = '<div class="rowdata not-sel row'+sidedata[0].product_id+'" id="_'+sidedata[0].product_id+'"><div class="rowqty"><span>'+sidedata[0].qty+'</span></div><div class="rownameswatches"><div class="rowname">'+sidedata[0].name+'</div><div class="swatches" id="a'+sidedata[0].selectedOptionsData.associated_id+'">Colour: '+sidedata[0].selectedOptionsData.option_label+'</div></div><div class="editrow"><a href="#left'+sidedata[0].product_id+'"><img src="'+linkUrl+'pub/media/pen.png"></a><input type="hidden" id="sidebarsku" name="sidebarsku" value="'+sidedata[0].sku+'"/></div><div class="removerow"><img src="'+linkUrl+'pub/media/garbage.png"></div></div>';
									$('.quicksidebar .subrowitems').append(html);
									//var total_items = $('.quicksidebar .subrowitems .rowdata').length;
									//$('.quicksidebar .subtotal').html('Selected Items <span class="total_products">('+click_qty+' Products</span> , <span class="total_items">'+total_items+' Items)</span>');
								}


                            //var html = '<div class="rowdata row'+sidedata[0].product_id+'" id="_'+sidedata[0].product_id+'"><div class="rowqty"><span>'+sidedata[0].qty+'</span></div><div class="rowname">'+sidedata[0].name+'</div><div class="rownameswatches"><div class="swatches">Colour: '+sidedata[0].selectedOptionsData.option_label+'</div></div><div class="editrow"><img src="http://staging-trade.earthsquared.com/pub/media/pen.png"></div><div class="removerow"><img src="http://staging-trade.earthsquared.com/pub/media/garbage.png"></div></div>';
					        //$('.quicksidebar .subrowitems').append(html);
		                }
			loadSubtotalSection();
		sideBar();

	});
	$(document).on('click','.qty-dec', function(){
		if($(this).parents('.field.quickqty').find("input.input-text.qty").is(':enabled')){
	    	$(this).parents('.field.quickqty').find("input.input-text.qty").val(($(this).parents('.field.quickqty').find("input.input-text.qty").val() - 1 > 0) ? ($(this).parents('.field.quickqty').find("input.input-text.qty").val() - 1) : 0);
	        $(this).focus();
	    }
	    var mainId = $(this).attr('data-id');
	    $('#_'+mainId+' .rowqty span').text($(this).next().val());
	    if($(this).next().val() == 0){
	    	$('#_'+mainId+' .removerow').trigger('click');
	    }
	    var sumall = 0;
		$(".subrowitems .rowdata" ).each(function( index ) {
			sumall += parseInt($(this).find('.rowqty span').text());
		});
		$('.quicksidebar .subtotal .total_products .countqty').text(sumall);

		if(!!localStorage.getItem('rowcollection') != "")
		{
			var cookieCollection = $.parseJSON(localStorage.getItem('rowcollection'));
			if(cookieCollection.hasOwnProperty(mainId)){
				cookieCollection[mainId]['qty'] = $(this).next().val();
			}
			localStorage.setItem('rowcollection',JSON.stringify(cookieCollection));
		}

		decreaseSideBar();
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
	    } else {
	        var sideskunotFound = $(this).next('#sidebarsku').val();
	        $('#quicksearch').val(sideskunotFound);
			$('#quicksearch').trigger('keyup');		        
	    }
	    if(focusTarget){
	    	$(focusTarget).children('.product-item').children('.quickproduct').children('.product-item-details').children('.productprice-qty').children('.quickqty').children('#qty').focus();
	    } 
		if($(window).width() < 580)	{
			$('.subrowitems.inneractive').hide();	    	
		}		
	});
	$(document).on('click','.removerow', function(){
		var delRowFromHtml = $(this).closest('.rowdata').attr('id');
		$('#'+delRowFromHtml+'').remove();
		var delRowFromCookie = delRowFromHtml.replace("_", "");

			if(!!localStorage.getItem('rowcollection') != "")
			{
				var cookieCollection = $.parseJSON(localStorage.getItem("rowcollection"));
				delete cookieCollection[delRowFromCookie];
				localStorage.setItem('rowcollection',JSON.stringify(cookieCollection));
			}
			loadSubtotalSection();
		decreaseSideBar();
	});
	var $rows = $('.quickorder-product-collection .subcategoryproduct-collection');	
	var total_qty_new1 = 0;	
	$('#quicksearch').keyup(function(e) {				
		if ($(this).val().length > 3) {		
			var querysearch = $(this).val();							
			$.ajax({
				showLoader: true,
				dataType: 'json',
				type: 'GET',
				//url: 'http://staging-trade.earthsquared.com/quickorder/index/search',
				url: linkUrl+'quickorder/index/search',
				data: {'querysearch':querysearch},
				success: function(responce)
				{	
					$('.quickorder-product-collection.products.list.items.product-items').empty();
					$.each(responce,function(index,value){						
						var html = '<div class="subcategoryproduct-collection item product product-item" id="left'+value.id+'"><div class="product-item"><div class="product-name mobile-screen"><span class="subpname-mobile">'+value.name+'</span></div><div class="quickproduct product-item-info"><a href="'+value.product_url+'" class="product photo product-item-photo proudctimage">		<img class="product-image-photo" src="http://staging-trade.earthsquared.com/pub/media/bluestagscarf_lifestyle.jpg" alt="'+value.name+'" title="'+value.name+'"></a><div class="product-item-details"><div class="quickproduct-detail"><div class="product-name desk-screen"><span class="subpname">'+value.name+'</span><div class="swatch-error"></div></div><div class="product-ref">Product Reference: '+value.sku+'<input type="hidden" name="product-ref" id="product-ref" value="'+value.sku+'" /></div><input type="hidden" class="productid" name="productid" value="'+value.id+'"></div><div class="productprice-qty"><div class="price"><div class="price-box"><span class="price">'+value.price+'</span></div></div><div class="field quickqty"><div href="javascript:void(0)" class="qty-dec" data-id="'+value.id+'">-</div><input type="number" name="qty" id="qty" min="1" value="0" title="Quantity" class="input-text qty form-control" data-validate="null"><div href="javascript:void(0)" class="qty-inc">+</div></div></div></div></div></div></div>';
						$('.quickorder-product-collection.products.list.items.product-items').append(html); 
					});										
					
				}, 
				complete: function(responce)
				{
					var loadcookieCollection_new = $.parseJSON(localStorage.getItem('rowcollection'));
					console.log(loadcookieCollection_new);				
			 		$.each(loadcookieCollection_new,function(index,value){			
						//console.log(value.qty);
						$('#left'+index).children('.product-item').children('.quickproduct').children('.product-item-details').children('.productprice-qty').children('.quickqty').children('#qty').attr('value',value.qty);
					});
				}
			});		
		} 
		// var $rows = $('.quickorder-product-collection .subcategoryproduct-collection');
		// var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

	 //    $rows.show().filter(function() {
	 //        var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
	 //        return !~text.indexOf(val);
	 //    }).hide();    
	});	
	$(document).on('click','#addtobagall', function(event){
	//$("#addtobagall").click(function(){
		if(!$('.subrowitems').children().length > 0){
			$('.allproductmsg').remove();
			$('.page.messages').before('<div class="message error allproductmsg">Please Select Product</div>');
			return false;
		}
		$('#addtobagall span').html('Adding..');
		$.ajax({
			showLoader: true,
			dataType: 'json',
			method: 'POST',
			url: window.location.addToProductUrl,
			data: {rowcollection: localStorage.getItem('rowcollection')},
			success: function(responce)
			{
				$('.listitemerror').remove();
				$('.allproductmsg').remove();
				if(responce.errors == true && responce.productId != ''){
					var proId = responce.productId,
						leftitem = $('#left'+proId);
					leftitem.find('.product-item-info').after('<div class="message error listitemerror">'+responce.message+'</div>');
					$('html, body').animate({
				        scrollTop: leftitem.offset().top - 60
				    }, 500);
				}else if(responce.errors == true){
					$('.page.messages').before('<div class="message error allproductmsg">'+responce.message+'</div>');
		            $('html, body').animate({
				        scrollTop: $('body').offset().top
				    }, 500);
				}else{
					// Remove all row
					//$.cookie("rowcollection", null, { path: '/' });										
					$('.subrowitems').html('');
					localStorage.removeItem('rowcollection');
					$('.quicksidebar .subtotal').html('Selected Items<span class="total_products"><span class="left_br">(</span><span class="countqty">0</span> <span class="prod">Products</span></span> <span class="comma">,</span> <span class="total_items">0<span class="itm"> Items</span><span class="right_br">)</span></span><span class="close-all">close</span>');		
					$('.page.messages').before('<div class="message success allproductmsg">'+responce.message+'</div>');
					var sections = ['cart'];
		            customerData.invalidate(sections);
		            customerData.reload(sections, true);
		            $('#addtobagall span').html('Added');
		            $('html, body').animate({
				        scrollTop: $('body').offset().top
				    }, 500);
				}
	            setTimeout(function(){
	            	$('#addtobagall span').html("Add to my Bag");
	           	}, 800);
	           	$('body').loader().loader('hide');
			}
		});
	});

	function loadSubtotalSection(){
		if(!!localStorage.getItem('rowcollection') != "")
		{
			var loadcookieCollection_new = $.parseJSON(localStorage.getItem('rowcollection'));
			console.log(loadcookieCollection_new);			
			var total_qty_new = 0;			
			var htmll = '';
			$.each(loadcookieCollection_new,function(index,value){				
				total_qty_new = parseInt(total_qty_new) + parseInt(value.qty);				
				$('#left'+index).children('.product-item').children('.quickproduct').children('.product-item-details').children('.productprice-qty').children('.quickqty').children('#qty').attr('value',value.qty);
				htmll += '<div class="rowdata selected row'+value.product_id+'" id="_'+value.product_id+'"><div class="rowqty"><span>'+value.qty+'</span></div><div class="rownameswatches"><div class="rowname">'+value.name+'</div></div><div class="editrow"><a href="#left'+value.product_id+'"><img src="'+linkUrl+'pub/media/pen.png"></a><input type="hidden" id="sidebarsku" name="sidebarsku" value="'+value.sku+'"/></div><div class="removerow"><img src="'+linkUrl+'pub/media/garbage.png"></div></div>';				
			});
			$('.quicksidebar .subrowitems').html(htmll);				
			var total_items = $('.quicksidebar .subrowitems .rowdata').length;
			//$('.quicksidebar .subtotal').html('Selected Items<span class="total_products">(<span class="countqty">'+total_qty_new+'</span> Products</span> , <span class="total_items">'+total_items+' Items)</span>');
			if($('.quicksidebar .subrowitems').length > 0){
				if($('.quicksidebar .subrowitems').height() > 618){
					$('.subrowitems').addClass('inneractive');		        	
		        	$('.subrowitems').addClass('moreitems');
		        	$('.quicksidebar').addClass('active');
				}
			}
		$('.quicksidebar .subtotal').html('Selected Items<span class="total_products"><span class="left_br">(</span><span class="countqty">'+total_qty_new+'</span> <span class="prod">Products</span></span> <span class="comma">,</span> <span class="total_items">'+total_items+' <span class="itm">Items</span><span class="right_br">)</span></span><span class="close-all">close</span>');		
		}
	}

	function sideBar(){

   		var windowHeight = window.innerHeight;
		var headerHeight = $('.page-header').height();
		var menuHeight = $('.sections.nav-sections').height();
		var totaldecHeight = headerHeight - menuHeight;
		var btnHeight = $('.action.alladdtocart').height();
		var totalHeight = windowHeight - totaldecHeight;
		var withoutBtn = totalHeight - btnHeight;

		// $('.quicksidebar').css('height',withoutBtn+'px');
		// console.log(windowHeight);
		// console.log($('.quicksidebar').outerHeight());
		// console.log(totaldecHeight);
		// console.log($(".quicksidebar").scrollTop());

        var newWindowWidth = $(window).width();
        if (newWindowWidth >= 767) {
			if($(window).scrollTop() <= $('.quicksidebar').offset().top + $('.quicksidebar').outerHeight() - window.innerHeight) {
				console.log("end reached");
				var rowheight = $('.subrowitems .rowdata:last-child').height();
		        $('.subrowitems').addClass('inneractive');
		        $('.quicksidebar').addClass('active');

    			var divLength = $('.subrowitems').children().length;
    			    console.log(divLength);
    			    if(divLength > 6){
		        		$('.subrowitems').addClass('moreitems');
    			    }else{    			    	
		        		$('.subrowitems').removeClass('moreitems');
    			    }
		        //$('.subrowitems').css('height',withoutBtn-rowheight+'px');

				//$('.subrowitems').animate({height: '+='+rowheight}, 500);
				//$('.subrowitems').css('padding-bottom',rowheight+menuHeight+'px');
		    }
		}else{
		
			if($(window).scrollTop() >= $('.quicksidebar').offset().top + $('.quicksidebar').outerHeight() - window.innerHeight) {

				//console.log("end reached");
				var rowheight = $('.subrowitems .rowdata:last-child').height();

		        $('.subrowitems').addClass('inneractive');
		        $('.quicksidebar').addClass('active');
    			var divLength = $('.subrowitems').children().length;
    			    console.log(divLength);
    			    if(divLength > 6){
		        		$('.subrowitems').addClass('moreitems');
    			    }else{    			    	
		        		$('.subrowitems').removeClass('moreitems');
    			    }


				//$('.subrowitems').animate({height: '+='+rowheight}, 500);
				//$('.subrowitems').css('padding-bottom',rowheight+'px');
		    }
		}
   	}
   	function decreaseSideBar(){
        var newWindowWidth = $(window).width();
        if (newWindowWidth >= 767) {
	   		var windowHeight = window.innerHeight;
			var headerHeight = $('.page-header').height();
			var menuHeight = $('.sections.nav-sections').height();
			var totaldecHeight = headerHeight + menuHeight;
			var btnHeight = $('.action.alladdtocart').height();
			var totalHeight = windowHeight - totaldecHeight;
			var withoutBtn = totalHeight - btnHeight;
			var rowheight = $('.subrowitems .rowdata:last-child').height();

			//$('.subrowitems').removeClass('inneractive');
		 	//$('.quicksidebar').removeClass('active');

			var divLength = $('.subrowitems').children().length;
			    console.log(divLength);
			    if(divLength > 6){
		 			$('.quicksidebar').addClass('active');
					$('.subrowitems').addClass('inneractive');
					$('.subrowitems').css('padding-bottom','0px');
			    }else if(divLength == 0){    			    	
					$('.subrowitems').css('padding-bottom','0px');
					$('.subrowitems').removeClass('moreitems');
			    }else{    			    	
	        		$('.quicksidebar').removeClass('active');
					$('.subrowitems').removeClass('inneractive');
			    }
		}else{
			var divLength = $('.subrowitems').children().length;
			    console.log(divLength);
			    if(divLength < 4){
		 			
	        		$('.subrowitems').removeClass('moreitems');
			    }
		}
   	}
	$(window).on("resize", function (e) {
        checkScreenSize();
    });

    checkScreenSize();

    function checkScreenSize(){
        var newWindowWidth = $(window).width();
        if (newWindowWidth <= 767) {

            //alert("Page loaded.");
            $(".subrowitems").hide();
            $(document).on('touchstart','.quicksidebar .subtotal.outer', function(){
            //$(".quicksidebar .subtotal.outer").click(function(){
                $(this).parent().toggleClass("active");
                $(".subrowitems").toggle();
            });
            $(document).on('touchstart','.subtotal.inner span.close-all', function(event){
			//$(".subtotal.inner span.close-all").click(function(event){
	        event.preventDefault();
				console.log("click");
		        $(this).parent().parent().parent(".quicksidebar").toggleClass("active");
		        $(".subrowitems").toggle();
		        if($(".subrowitems").hasClass("inneractive")){
					$(".subrowitems").removeClass("inneractive");
		        	$(".subrowitems").hide();
		        }
			});
           
        }else{

        }

    }

	// $(".subtotal.inner span.close-all").click(function(e){
	// 	console.log("click");
	//         e.preventDefault();
 //        $(this).parent().parent().parent(".quicksidebar").toggleClass("active");
 //        $(".quicksidebar").removeClass("active");
 //        $(".subrowitems").hide();
	// });

		$(".page-title-wrapper .need_morehelp.mobileview span").click(function(event){
		    event.preventDefault();        
		    $(this).parent().toggleClass("active");
	        $(".page-title-wrapper .need_morehelp .tooltip_text").toggle();
		});
		$(".page-title-wrapper .need_morehelp.desktopview span").hover(function(event){
		        event.preventDefault();
	        $(this).parent().toggleClass("active");
	        $(".page-title-wrapper .need_morehelp .tooltip_text").toggle();
		});

});