jQuery(document).ready(function($) {
	"use strict";
	
	/* meta tabs */
	var meta_tabs = $('.tab-container');
	if(meta_tabs.length > 0){
		meta_tabs.easytabs();
	}

	foldery_load_tab($('#page_template').val());
	
	/* select page templates */
	$('#page_template').on('change', function() {
		foldery_load_tab($(this).val());
	});

	function foldery_load_tab(value) {
		
		$('#cs_metabox_field__cms_show_sidebar, #show_sidebar_page_left').css('display', 'block');
		
		switch (value) {

			case 'page-templates/blog-grid.php':
				foldery_show_tab([ 'tabs-blog-option' ]);
				foldery_show_tab([ 'tabs-page-option' ]);
				foldery_hide_tab([ 'tabs-portfolio-option' ]);
				break;
			case 'page-templates/blog-masonry.php':
				foldery_show_tab([ 'tabs-blog-option' ]);
				foldery_show_tab([ 'tabs-page-option' ]);
				foldery_hide_tab([ 'tabs-portfolio-option' ]);
				break;
			case 'page-templates/blog-masonry2.php':
				foldery_show_tab([ 'tabs-blog-option' ]);
				foldery_show_tab([ 'tabs-page-option' ]);
				foldery_hide_tab([ 'tabs-portfolio-option' ]);
				break;
			case 'page-templates/blog-standard.php':
				foldery_show_tab([ 'tabs-blog-option' ]);
				foldery_show_tab([ 'tabs-page-option' ]);
				foldery_hide_tab([ 'tabs-portfolio-option' ]);
				break;
			case 'page-templates/portfolio-grid.php':
				foldery_show_tab([ 'tabs-page-option' ]);
				foldery_show_tab([ 'tabs-portfolio-option' ]);
				foldery_hide_tab([ 'tabs-blog-option' ]);
				break;
			case 'page-templates/portfolio-grid2.php':
				foldery_show_tab([ 'tabs-page-option' ]);
				foldery_show_tab([ 'tabs-portfolio-option' ]);
				foldery_hide_tab([ 'tabs-blog-option' ]);
				break;
			case 'page-templates/portfolio-masonry.php':
				foldery_show_tab([ 'tabs-page-option' ]);
				foldery_show_tab([ 'tabs-portfolio-option' ]);
				foldery_hide_tab([ 'tabs-blog-option' ]);
				break;	
			default:
				foldery_hide_tab([ 'tabs-page-option' ]);
				foldery_hide_tab([ 'tabs-blog-option' ]);
				foldery_hide_tab([ 'tabs-portfolio-option' ]);
				break;
		}
	}

	function foldery_show_tab(tab) {

		$.each(tab, function(i, val) {
			$('.' + val).attr('style', '');
		});
	}

	function foldery_hide_tab(tab) {

		$.each(tab, function(i, val) {
			$('.' + val).css('display', 'none');
		});
	}
});