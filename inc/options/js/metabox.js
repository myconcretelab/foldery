jQuery(document).ready(function($) {
	"use strict";
	
	/* meta tabs */
	var meta_tabs = $('.tab-container');
	if(meta_tabs.length > 0){
		meta_tabs.easytabs();
	}

	zk_monaco_load_tab($('#page_template').val());
	
	/* select page templates */
	$('#page_template').on('change', function() {
		zk_monaco_load_tab($(this).val());
	});

	function zk_monaco_load_tab(value) {
		
		$('#cs_metabox_field__cms_show_sidebar, #show_sidebar_page_left').css('display', 'block');
		
		switch (value) {

			case 'page-templates/blog-grid.php':
				zk_monaco_show_tab([ 'tabs-blog-option' ]);
				zk_monaco_show_tab([ 'tabs-page-option' ]);
				zk_monaco_hide_tab([ 'tabs-portfolio-option' ]);
				break;
			case 'page-templates/blog-masonry.php':
				zk_monaco_show_tab([ 'tabs-blog-option' ]);
				zk_monaco_show_tab([ 'tabs-page-option' ]);
				zk_monaco_hide_tab([ 'tabs-portfolio-option' ]);
				break;
			case 'page-templates/blog-masonry2.php':
				zk_monaco_show_tab([ 'tabs-blog-option' ]);
				zk_monaco_show_tab([ 'tabs-page-option' ]);
				zk_monaco_hide_tab([ 'tabs-portfolio-option' ]);
				break;
			case 'page-templates/blog-standard.php':
				zk_monaco_show_tab([ 'tabs-blog-option' ]);
				zk_monaco_show_tab([ 'tabs-page-option' ]);
				zk_monaco_hide_tab([ 'tabs-portfolio-option' ]);
				break;
			case 'page-templates/portfolio-grid.php':
				zk_monaco_show_tab([ 'tabs-page-option' ]);
				zk_monaco_show_tab([ 'tabs-portfolio-option' ]);
				zk_monaco_hide_tab([ 'tabs-blog-option' ]);
				break;
			case 'page-templates/portfolio-grid2.php':
				zk_monaco_show_tab([ 'tabs-page-option' ]);
				zk_monaco_show_tab([ 'tabs-portfolio-option' ]);
				zk_monaco_hide_tab([ 'tabs-blog-option' ]);
				break;
			case 'page-templates/portfolio-masonry.php':
				zk_monaco_show_tab([ 'tabs-page-option' ]);
				zk_monaco_show_tab([ 'tabs-portfolio-option' ]);
				zk_monaco_hide_tab([ 'tabs-blog-option' ]);
				break;	
			default:
				zk_monaco_hide_tab([ 'tabs-page-option' ]);
				zk_monaco_hide_tab([ 'tabs-blog-option' ]);
				zk_monaco_hide_tab([ 'tabs-portfolio-option' ]);
				break;
		}
	}

	function zk_monaco_show_tab(tab) {

		$.each(tab, function(i, val) {
			$('.' + val).attr('style', '');
		});
	}

	function zk_monaco_hide_tab(tab) {

		$.each(tab, function(i, val) {
			$('.' + val).css('display', 'none');
		});
	}
});