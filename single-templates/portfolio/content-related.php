<?php 
		// Annulation de la fonction des travaux relatifs 
	/* 
	$portfolio_categories = '';
	$terms = get_the_terms(get_the_ID(), 'portfolio_cat');
	if(!empty($terms)) {
	    
	    $portfolio_categories = array();
	    
	    foreach ($terms as $term){
	         $portfolio_categories[] = $term->term_id;
	    }   
	    
	    $portfolio_categories = '|tax_query:'.implode(',', $portfolio_categories);
	}

<div class="container cms-portfolio-related cshero-shortcode text-center">
	<h2 class="cms-portfolio-related-title">
		<span><?php esc_html_e('Related Projects', 'foldery' ); ?></span>
	</h2>   
	<?php echo do_shortcode('[cms_grid source="size:3|order_by:date|post_type:portfolio'.$portfolio_categories.'" layout="basic" col_xs="1" col_sm="2" col_md="3" col_lg="3" filter="false" cms_template="cms_grid--porelate.php"]'); ?>
	
</div>
*/ ?>