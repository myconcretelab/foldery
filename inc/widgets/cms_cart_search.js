(function ($) {
    "use strict";
    jQuery(document).ready(function ($) {
        var display;
        var no_display;
        $('body').click(function (e) {
            var target = $(e.target);
            if (target.parents('.shopping_cart_dropdown').length == 0 && !target.hasClass('shopping_cart_dropdown')) {
                $('.widget_searchform_content,.shopping_cart_dropdown').removeClass('active');
            }
        });
        $('.widget_searchform_content,.shopping_cart_dropdown').click(function (e) {
            e.stopPropagation();
        });
        $('.widget_cart_search_wrap [data-display]').click(function (e) {
            var container = $(this).parents('.widget_cart_search_wrap');
            e.stopPropagation();
            var _this = $(this);
            display = _this.attr('data-display');
            no_display = _this.attr('data-no_display');
			$(display, container).css({
				left: 0
			});
            if ($(display, container).hasClass('active')) {
                $(display, container).removeClass('active');
            } else {
                $(display, container).addClass('active');
                $(no_display, container).removeClass('active');
				var offset = $(display, container).offset().left + $(display).outerWidth() - $(window).width()
				if (offset > 0) {
					$(display, container).css({
						left: 0 - offset
					});
				} else {
					$(display, container).css({
						left: 0
					});

				}
            }
        });
    })
})(jQuery);
