window.vc_prettyPhoto = window.vc_prettyPhoto || function() {};

jQuery(document).ready(function($) {
	"use strict";
	/* window */
	var window_width, window_height, scroll_top;
	
	/* admin bar */
	var adminbar = $('#wpadminbar');
	var adminbar_height = 0;
	
	/* header */
	var header = $('#cms-header-wrapper');
	var header_top = 0,
		masthead = $('#masthead'),
		header_v1_width, 
        header_v1_content_width,
        content_padding_left,
        content_padding_right;
	
	/* scroll status */
	var scroll_status = '';

	/* Show search at top */
    $("#header-widget-search").click(function(){
        $("#cms-search").toggleClass('open');
        $("#cms-search input[type='text']" ).focus();
    });
    $("#header-widget-search-close").click(function(){
        $("#cms-search").removeClass('open');
    });

    /* Show Main Navigation for Header v4*/
    $("#cms-show-mainnav").click(function(){
        $("#cms-mainnav-v4").toggleClass('open');
    });
    $("#cms-hide-mainnav").click(function(){
        $("#cms-mainnav-v4").removeClass('open');
    });


	/**
	 * window load event.
	 * 
	 * Bind an event handler to the "load" JavaScript event.
	 * @author Fox
	 */
	$(window).on('load', function () {	
		/** current scroll */
		scroll_top = $(window).scrollTop();
		
		/** current window width */
		window_width = $(window).width();
		
		/** current window height */
		window_height = $(window).height();
		
		/* get admin bar height */
		adminbar_height = adminbar.length > 0 ? adminbar.outerHeight(true) : 0 ;
		
		/* get top header menu */
		header_top = adminbar_height;
		/* get Header height */
		header_top = header.length > 0 ? header.outerHeight(true) : 0 ;

		/* Header V1 */
        header_v1_width  = masthead.length > 0 ?  masthead.outerWidth() : 0;
        header_v1_content_width = window_width - header_v1_width;
        content_padding_left = parseInt($('#cms-content-wrapper').css('padding-left'));
        content_padding_right = parseInt($('#cms-content-wrapper').css('padding-right'));

		/* check sticky menu. */
		if(FolderyOptions.menu_sticky == '1'){
			foldery_sticky_menu(scroll_top);
		}
		
		/* check mobile menu */
		foldery_mobile_menu();
		foldery_auto_video_width();
		/* check back to top */
		if(FolderyOptions.back_to_top == '1'){
			/* add html. */
			$('body').append('<div id="back_to_top" class="back_to_top"><span class="go_up"><i style="" class="fa fa-arrow-up"></i></span></div><!-- #back-to-top -->');
			foldery_back_to_top();
		}

		/* Custom VC row stretch content */
        foldery_custom_vc_row_stretch_content();

	});

	/**
	 * reload event.
	 * 
	 * Bind an event handler to the "navigate".
	 */
	window.onbeforeunload = function(){}
	
	/**
	 * resize event.
	 * 
	 * Bind an event handler to the "resize" JavaScript event, or trigger that event on an element.
	 * @author Fox
	 */
	$(window).on('resize', function (event, ui) {
		/** current window width */
		window_width = $(event.target).width();
		
		/** current window height */
		window_height = $(window).height();

		/* Header V1 */
        header_v1_width  = masthead.length > 0 ?  masthead.outerWidth() : 0;
        header_v1_content_width = window_width - header_v1_width;
        content_padding_left = parseInt($('#cms-content-wrapper').css('padding-left'));
        content_padding_right = parseInt($('#cms-content-wrapper').css('padding-right'));
		
		/** current scroll */
		scroll_top = $(window).scrollTop();
		
		/* check sticky menu. */
		if(FolderyOptions.menu_sticky == '1'){
			foldery_sticky_menu(scroll_top);
		}
		
		/* check mobile menu */
		foldery_mobile_menu();

		foldery_auto_video_width();

		/* Custom VC row stretch content */
        foldery_custom_vc_row_stretch_content();
		
	});
	
	/**
	 * scroll event.
	 * 
	 * Bind an event handler to the "scroll" JavaScript event, or trigger that event on an element.
	 * @author Fox
	 */
	var lastScrollTop = 0;
	
	$(window).scroll(function() {
		/** current scroll */
		scroll_top = $(window).scrollTop();
		/** check scroll up or down. */
		if(scroll_top < lastScrollTop) {
			/* scroll up. */
			scroll_status = 'up';
		} else {
			/* scroll down. */
			scroll_status = 'down';
		}
		
		lastScrollTop = scroll_top;
		
		/* check sticky menu. */
		if(FolderyOptions.menu_sticky == '1'){
			foldery_sticky_menu();
		}

		/* Header type style */
		if(FolderyOptions.header_type == 'fixed'){
			foldery_menu_fixed_bg();
		}
		
		/* check back to top */
		foldery_back_to_top();
	});

	/**
	 * Sticky menu
	 * 
	 * Show or hide sticky menu.
	 * @author Fox
	 * @since 1.0.0
	 */
	function foldery_sticky_menu() {
		if (header_top < scroll_top) {
			switch (true) {
				case (window_width > 992):
					header.find('#masthead #cms-header').addClass('header-sticky');
					$('body').addClass('sticky-margin-top');
					break;
				case ((window_width <= 992 && window_width >= 768) && (FolderyOptions.menu_sticky_tablets == '1')):
					header.find('#masthead #cms-header').addClass('header-sticky');
					$('body').addClass('sticky-margin-top');
					break;
				case ((window_width <= 768) && (FolderyOptions.menu_sticky_mobile == '1')):
					header.find('#masthead #cms-header').addClass('header-sticky');
					$('body').addClass('sticky-margin-top');
					break;
			}
		} else {
			header.find('#masthead #cms-header').removeClass('header-sticky');
			$('body').removeClass('sticky-margin-top');
		}
	}
	function foldery_menu_fixed_bg() {
		if (header_top < scroll_top) {
			header.addClass('cms-header-fixed-bg');
		} else {
			header.removeClass('cms-header-fixed-bg');
		}
	}
	
	/**
	 * Mobile menu
	 * 
	 * Show or hide mobile menu.
	 * @author Fox
	 * @since 1.0.0
	 */
	
	$('body').on('click', '#cms-menu-mobile', function(){
		var navigation = $(this).parents().find('#cms-header-navigation');
		if(!navigation.hasClass('collapse')){
			navigation.addClass('collapse');
		} else {
			navigation.removeClass('collapse');
		}
	});
	/* check mobile screen. */
	function foldery_mobile_menu() {
		var menu = $('#cms-header-navigation');
		
		/* active mobile menu. */
		switch (true) {
		case (window_width <= 991):
			/* Add mobile menu for Header V2 */
			var $mainmenu_left = $('#cms-header-navigation-left ul.nav-menu');
	        var $mainmenu_right = $('#cms-header-navigation-right ul.nav-menu');
	        var $mobilemenu_1 = $mainmenu_left.clone();
	        var $mobilemenu_2 = $mainmenu_right.clone();
	        if($('#cms-header-navigation .main-navigation').find('ul').length == 0){
	        	$mobilemenu_1.appendTo('#cms-header-navigation .main-navigation');
		        $mobilemenu_2.appendTo('#cms-header-navigation .main-navigation');
	        }
	        menu.addClass('tablets-nav hidden-md hidden-lg');
			/* */
			foldery_mobile_menu_group(menu);
			break;
		case (window_width <= 992 && window_width >= 768):
			menu.removeClass('phones-nav hidden-md hidden-lg').addClass('tablets-nav');
			/* */
			foldery_mobile_menu_group(menu);
			break;
		case (window_width <= 768):
			menu.removeClass('hidden-md hidden-lg')
			break;
		default:
			$('#cms-header-navigation').removeClass('collapse');
			menu.removeClass('mobile-nav tablets-nav hidden-md hidden-lg');
			menu.find('li').removeClass('mobile-group');
			if($('.header-v2 #cms-header-navigation .main-navigation').find('ul').length != 0){ /* fixed menu show when resize screen @since 1.1.8*/
	        	$('.header-v2 #cms-header-navigation .main-navigation').empty();
	        }
			break;
		}

		
	}
	/* group sub menu. */
	function foldery_mobile_menu_group(nav) {
		nav.each(function(){
			$(this).find('li').each(function(){
				if($(this).find('ul:first').length > 0){
					$(this).addClass('mobile-group');
				}
			});
		});
	}

	/* Custom VC row stretch content 
     * This function just applied for header V1
     * @author Chinh Duong Manh
     * @since 1.4.0.1
    */

    function foldery_custom_vc_row_stretch_content() {
        var $elements = $('.cms-header-v1.cms-custom-vc-row-stretch-content [data-vc-full-width="true"]');
        setTimeout(function() {
        	if (window_width > 991) {
        		$('.cms-header-v1.cms-custom-vc-row-stretch-content').find('#main').css({'overflow':'hidden'});
		        $.each($elements, function(key, item) {
		            var $el = $(this);
		            $el.addClass("vc_hidden");
		            var $el_full = $el.next(".vc_row-full-width");
		            if ($el_full.length || ($el_full = $el.parent().next(".vc_row-full-width")), $el_full.length) {
		                var el_margin_left = parseInt($el.css("margin-left"), 10),
		                    el_margin_right = parseInt($el.css("margin-right"), 10),
		                    offset = 0 - $el_full.offset().left - el_margin_left + header_v1_width + content_padding_left,
		                    width = $(window).width();
		                if ($el.css({
		                        position: "relative",
		                        left: offset,
		                        "box-sizing": "border-box",
		                        width: $(window).width() - header_v1_width - content_padding_left - content_padding_right,
		                    }), !$el.data("vcStretchContent")) {
		                    var padding = -1 * offset;
		                    0 > padding && (padding = 0);
		                    var paddingRight = width - padding - $el_full.width() + el_margin_left + el_margin_right;
		                    0 > paddingRight && (paddingRight = 0), $el.css({
		                        "padding-left": padding + "px",
		                        "padding-right": padding + "px"
		                    })
		                }
		                $el.attr("data-vc-full-width-init", "true"), $el.removeClass("vc_hidden")
		            }
		        }), $(document).trigger("vc-full-width-row", $elements)

		        /* Fix width for Rev slider */
		        $elements.find('.rev_slider.fullwidthabanner .tp-revslider-mainul').css({'width': header_v1_content_width - content_padding_right - content_padding_left});
		    } else {
		    	$('.cms-header-v1.cms-custom-vc-row-stretch-content').find('#main').css({'overflow':''});
		    }
	    }, 0 );
    }
	
	/**
     * Auto width video iframe
     * 
     * Youtube, Vimeo.
     * @author Chinh Duong Manh
     */
    function foldery_auto_video_width() {
        $('.entry-media iframe').each(function(){
            var v_width = $(this).width();
            v_width = v_width / (16/9);
            $(this).attr('height',v_width + 35);
        })
    }
	/**
	 * Back To Top
	 * 
	 * @author Fox
	 * @since 1.0.0
	 */
	$('body').on('click', '#back_to_top', function () {
        $("html, body").animate({
            scrollTop: 0
        }, 1500);
    });
	
	/* Show or hide buttom  */
	function foldery_back_to_top(){
		/* back to top */
        if (scroll_top < window_height) {
        	$('#back_to_top').addClass('off').removeClass('on');
        } else {
        	$('#back_to_top').removeClass('off').addClass('on');
        }
	}
	
	/**
	 * One page
	 * 
	 * @author Fox
	 */
	if(FolderyOptions.one_page == true){
		
		$('body').on('click', '.onepage', function () {
			$('#cms-menu-mobile').removeClass('close-open');
			$('#cms-header-navigation').removeClass('open-menu');
			$('.cms-menu-close').removeClass('open');
		});
		
		var one_page_options = {'filter' : '.onepage'};
		
		if(FolderyOptions.one_page_speed != undefined) one_page_options.speed = parseInt(FolderyOptions.one_page_speed);
		if(FolderyOptions.one_page_easing != undefined) one_page_options.easing =  FolderyOptions.one_page_easing;
		$('#cms-header').singlePageNav(one_page_options);
	}
	
	/**
	 * Full page
	 * 
	 * @author Fox
	 */
	if($('.full-page').length > 0){
		
		var anchors = [];
		
		$('.full-page .cms-grid .cms-grid-item-fullpage').each(function(i) {
			if($(this).attr('id') != undefined){
				anchors.push($(this).attr('id'));
			}
		})
		
		$('.full-page').fullpage({
			sectionSelector : '.cms-grid-item-fullpage',
			autoScrolling:true,
			navigation:true,
		});

		$(".cms-open-popup").fullScreenPopup({
			lockDocumentScroll: false,
		}); 
	}
	/* Newsletter form label to placeholder */
    $(".tnp form .tnp-field").each(function() {
        var $this = $(this);
        var label = $this.find('label');
            $this.find('input').attr("placeholder", $(label).html());
            $(label).remove();
    });
	//ajax complete
    jQuery(document).ajaxComplete(function(event, xhr, settings){
		foldery_auto_video_width();
		/* CUSTOM VC */
        foldery_custom_vc_row_stretch_content();

	})
});
