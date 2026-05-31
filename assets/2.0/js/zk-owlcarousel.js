/**
 * Custom OWL in theme
 */
(function ($) {
    "use strict";
    $(window).on('load',function () {
        /* add first/last/center class */
        function themeframe_owl_flc(e) {
            var idx = $(e.target).find('.owl-item');
            idx.removeClass('first last'), 
            idx.eq(e.item.index).addClass('first'), 
            idx.eq(e.item.index + e.page.size - 1).addClass('last')
        }
        $(".zk-carousel").each(function () {
            var $this = $(this),
                slide_id = $this.attr('id'),
                slider_settings = cmscarousel[slide_id];
            $this.on("initialized.owl.vccarousel", function(e) {
               themeframe_owl_flc(e);
            }),
            $this.vcOwlCarousel(slider_settings),
            $this.on("changed.owl.vccarousel", function(e) {
                themeframe_owl_flc(e)
            })
        });
    });
})(jQuery)