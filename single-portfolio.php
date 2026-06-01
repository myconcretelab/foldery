<?php
/**
 * The Template for displaying all single posts
 *
 * @package ZookaStudio
 * @subpackage Foldery
 * @since 1.0.0
 */
	global $smof_data;
	$portfolio_meta = foldery_post_meta_data();
	// var_dump($portfolio_meta); exit;
	// Start Hack
	//
	$portfolio_meta->_cms_single_layout = 'standard';
	$smof_data['single_portfolio_related'] = true;
	//
	//
	// END Hack
	$portfolio_layout = $portfolio_meta->_cms_single_layout;
	if($portfolio_layout != '') $smof_data['single_portfolio_layout'] = $portfolio_layout;

	get_header(); 
?>


<div id="primary" class="primary single-portfolio <?php echo esc_attr($smof_data['single_portfolio_layout']);?>">
    <div id="content" role="main">
        <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'single-templates/portfolio/detail', $smof_data['single_portfolio_layout'] ); ?>
            <?php cms_portfolio_nav(); ?>
        <?php endwhile; // end of the loop. ?>
    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>
