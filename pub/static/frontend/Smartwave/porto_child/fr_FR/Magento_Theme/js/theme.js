/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
	'owl.carousel/owl.carousel.min',
    'mage/ie-class-fixer',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';
    //Move language switch to right header
    function moveLanguage(){
        if($(window).width() < 768){
            $('.nav-sections .switcher-language').detach().appendTo('.header-language-currency');
            $('.nav-sections .switcher-currency').detach().appendTo('.header-language-currency');
            $('.nav-sections-item-title:nth-of-type(5)').hide();
        }else{
            // $('.nav-sections-item-title:nth-of-type(5)').show();
            $('.header-language-currency .switcher-language').detach().appendTo('.nav-sections-item-title:nth-of-type(5)');
            $('.header-language-currency .switcher-currency').detach().appendTo('.nav-sections-item-title:nth-of-type(5)');
        }  
    }
    
    $(document).ready(function(){
        moveLanguage();
        $(window).resize(function() {
            moveLanguage();
            console.log('1');
        })
        $('.cart-summary').mage('sticky', {
            container: '#maincontent'
        });

        $('.panel.header .header.links').clone().appendTo('#store\\.links');
    });
    keyboardHandler.apply();
	
	   // home brands
	      if($(window).width() < 767) {
			$(".brand-content figure:lt(6)").show();

		    $(".gallery-btn").on('click', function(event) {
				   event.preventDefault();
				   var $hidden =$(".brand-content figure:hidden");
				   $($hidden).slice(0, 6).fadeIn(800);

				   if ($hidden.length == 0) {
					   $(this).fadeOut();
				   }
		   });
	   }else {
		   $(".brand-content figure:lt(10)").show();

		   $(".gallery-btn").on('click', function(event) {
				   event.preventDefault();
				   var $hidden =$(".brand-content figure:hidden");
				   $($hidden).slice(0, 10).fadeIn(800);

				   if ($hidden.length == 0) {
					   $(this).fadeOut();
				   }
		   });
	   }
       


        var owl = $(".brand-products .product-items");
		owl.owlCarousel({
			autoplay: false,
			items: 3,
			loop: false,
			nav: true,
			dots: false,
			center: false,
			rewind: false,
			mouseDrag: true,
			touchDrag: true,
			pullDrag: true,
			freeDrag: false,
			margin: 0,
			stagePadding: 0,
			merge: false,
			mergeFit: true,
			autoWidth: false,
			startPosition: 0,
			rtl: false,
			smartSpeed: 250,
			fluidSpeed: false,
			dragEndSpeed: false,
			responsive: {
				0: {
					items: 2
					// nav: true
				},
				
				768: {
					items: 3,
					// nav: true,
					loop: false
				},
				992: {
					items: 4,
					// nav: true,
					loop: false
				}
			},
			responsiveRefreshRate: 200,
			responsiveBaseElement: window,
			fallbackEasing: "swing",
			info: false,
			nestedItemSelector: false,
			itemElement: "div",
			stageElement: "div",
			refreshClass: "owl-refresh",
			loadedClass: "owl-loaded",
			loadingClass: "owl-loading",
			rtlClass: "owl-rtl",
			responsiveClass: "owl-responsive",
			dragClass: "owl-drag",
			itemClass: "owl-item",
			stageClass: "owl-stage",
			stageOuterClass: "owl-stage-outer",
			grabClass: "owl-grab",
			autoHeight: false,
			lazyLoad: false
		});
		
		// End homepage brands
});
require([
    'jquery'
], function ($) {
    (function() {
        var ev = new $.Event('classadded'),
            orig = $.fn.addClass;
        $.fn.addClass = function() {
            $(this).trigger(ev, arguments);
            return orig.apply(this, arguments);
        }
    })();
    $.fn.extend({
        scrollToMe: function(){
            if($(this).length){
                var top = $(this).offset().top - 100;
                $('html,body').animate({scrollTop: top}, 300);
            }
        },
        scrollToJustMe: function(){
            if($(this).length){
                var top = jQuery(this).offset().top;
                $('html,body').animate({scrollTop: top}, 300);
            }
        }
    });
    $(document).ready(function(){
        var windowScroll_t;
        $(window).scroll(function(){
            clearTimeout(windowScroll_t);
            windowScroll_t = setTimeout(function(){
                if(jQuery(this).scrollTop() > 100){
                    $('#totop').fadeIn();
                }else{
                    $('#totop').fadeOut();
                }
            }, 500);
        });
        $('#totop').off("click").on("click",function(){
            $('html, body').animate({scrollTop: 0}, 600);
        });
        if ($('body').hasClass('checkout-cart-index')) {
            if ($('#co-shipping-method-form .fieldset.rates').length > 0 && $('#co-shipping-method-form .fieldset.rates :checked').length === 0) {
                $('#block-shipping').on('collapsiblecreate', function () {
                    $('#block-shipping').collapsible('forceActivate');
                });
            }
        }
        $(".products-grid .weltpixel-quickview").each(function(){
            $(this).appendTo($(this).parent().parent().children(".product-item-photo"));
        });
        $(".word-rotate").each(function() {

            var $this = $(this),
                itemsWrapper = $(this).find(".word-rotate-items"),
                items = itemsWrapper.find("> span"),
                firstItem = items.eq(0),
                firstItemClone = firstItem.clone(),
                itemHeight = 0,
                currentItem = 1,
                currentTop = 0;

            itemHeight = firstItem.height();

            itemsWrapper.append(firstItemClone);

            $this
                .height(itemHeight)
                .addClass("active");

            setInterval(function() {
                currentTop = (currentItem * itemHeight);

                itemsWrapper.animate({
                    top: -(currentTop) + "px"
                }, 300, function() {
                    currentItem++;
                    if(currentItem > items.length) {
                        itemsWrapper.css("top", 0);
                        currentItem = 1;
                    }
                });

            }, 2000);

        });
        $(".top-links-icon").off("click").on("click", function(e){
            if($(this).parent().children("ul.links").hasClass("show")) {
                $(this).parent().children("ul.links").removeClass("show");
            } else {
                $(this).parent().children("ul.links").addClass("show");
            }
            e.stopPropagation();
        });
        $(".top-links-icon").parent().click(function(e){
            e.stopPropagation();
        });
        $(".search-toggle-icon").click(function(e){
            if($(this).parent().children(".block-search").hasClass("show")) {
                $(this).parent().children(".block-search").removeClass("show");
                $(this).removeClass('open');
            } else {
                $(this).parent().children(".block-search").addClass("show");
                $(this).addClass('open');
            }
            e.stopPropagation();
        });
        $(".search-toggle-icon").parent().click(function(e){
            e.stopPropagation();
        });
        $("html,body").click(function(){
            $(".search-toggle-icon").parent().children(".block-search").removeClass("show");
            $('.autocomplete-suggestions').hide();
            $(".search-toggle-icon").removeClass('open');
            $(".top-links-icon").parent().children("ul.links").removeClass("show");
        });

        /********************* Qty Holder **************************/
        $(document).on("click", ".qtyplus", function(e) {
            // Stop acting like a button
            e.preventDefault();
            // Get its current value
            var currentVal = parseInt($(this).parents('form').find('input[name="qty"]').val());
            // If is not undefined
            if (!isNaN(currentVal)) {
                // Increment
                $(this).parents('form').find('input[name="qty"]').val(currentVal + 1);
            } else {
                // Otherwise put a 0 there
                $(this).parents('form').find('input[name="qty"]').val(0);
            }
        });
        // This button will decrement the value till 0
        $(document).on("click", ".qtyminus", function(e) {
            // Stop acting like a button
            e.preventDefault();
            // Get the field name
            fieldName = $(this).attr('field');
            // Get its current value
            var currentVal = parseInt($(this).parents('form').find('input[name="qty"]').val());
            // If it isn't undefined or its greater than 0
            if (!isNaN(currentVal) && currentVal > 0) {
                // Decrement one
                $(this).parents('form').find('input[name="qty"]').val(currentVal - 1);
            } else {
                // Otherwise put a 0 there
                $(this).parents('form').find('input[name="qty"]').val(0);
            }
        });
        $(".qty-inc").unbind('click').click(function(){
            if($(this).parents('.field.qty,.control.qty').find("input.input-text.qty").is(':enabled')){
                $(this).parents('.field.qty,.control.qty').find("input.input-text.qty").val((+$(this).parents('.field.qty,.control.qty').find("input.input-text.qty").val() + 1) || 0);
                $(this).parents('.field.qty,.control.qty').find("input.input-text.qty").trigger('change');
                $(this).focus();
            }
        });
        $(".qty-dec").unbind('click').click(function(){
            if($(this).parents('.field.qty,.control.qty').find("input.input-text.qty").is(':enabled')){
                $(this).parents('.field.qty,.control.qty').find("input.input-text.qty").val(($(this).parents('.field.qty,.control.qty').find("input.input-text.qty").val() - 1 > 0) ? ($(this).parents('.field.qty,.control.qty').find("input.input-text.qty").val() - 1) : 0);
                $(this).parents('.field.qty,.control.qty').find("input.input-text.qty").trigger('change');
                $(this).focus();
            }
        });
        $('body').on('click', '.selectShipping', function () {
            let classTemp = $(this).attr('class');
            if ($(this).hasClass( "_37WgX" )) {
                $('.selectShipping').removeClass('_37WgX');
                $('.selectShipping').addClass('nsce3');
                $(this).removeClass('_37WgX');
                $(this).addClass('nsce3');
            }
            if ($(this).hasClass( "nsce3" )) {
               $('.selectShipping').removeClass('_37WgX');
               $('.selectShipping').addClass('nsce3');
                $(this).removeClass('nsce3');
                $(this).addClass('_37WgX');
            }
        });
    });
});
require([
    'jquery',
    'js/jquery.lazyload',
    'slick'
], function ($) {
    $(document).ready(function(){
        if($('.catalog-product-view').length > 0) {
            var count = 0,
                timerDown = setInterval(function () {
                    count++;
                    if (count > 30) {
                        clearInterval(timerDown);
                    }
                    if ($('#product-viewed-recent').length > 0) {
                        console.log('dat vao day');
                        $('#product-viewed-recent').not('.slick-initialized').slick({
                            slidesToScroll: 2,
                            slidesToShow: 5,
                            dots: false,
                            infinite: true,
                            responsive: [
                                {
                                    breakpoint: 1024,
                                    settings: {
                                        slidesToShow: 5,
                                        slidesToScroll: 2
                                    }
                                },
                                {
                                    breakpoint: 768,
                                    settings: {
                                        slidesToShow: 2,
                                        slidesToScroll: 1
                                    }
                                },
                                {
                                    breakpoint: 480,
                                    settings: {
                                        slidesToShow: 2,
                                        slidesToScroll: 1
                                    }
                                }
                            ]
                        });
                        clearInterval(timerDown);
                    }
                }, 1000);
        }
        $("img.porto-lazyload:not(.porto-lazyload-loaded)").lazyload({effect:"fadeIn"});
        if ($('.porto-lazyload:not(.porto-lazyload-loaded)').closest('.owl-carousel').length) {
            $('.porto-lazyload:not(.porto-lazyload-loaded)').closest('.owl-carousel').on('changed.owl.carousel', function() {
                $(this).find('.porto-lazyload:not(.porto-lazyload-loaded)').trigger('appear');
            });
            $('.porto-lazyload:not(.porto-lazyload-loaded)').closest('.owl-carousel').on('initialized.owl.carousel', function() {
                $(this).find('.porto-lazyload:not(.porto-lazyload-loaded)').trigger('appear');
            });
        }
        window.setTimeout(function(){
            $('.sidebar-filterproducts').find('.porto-lazyload:not(.porto-lazyload-loaded)').trigger('appear');
        },500);
    });
});
