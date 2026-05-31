<?php
/**
 * The default template for displaying single portfolio
 *
 *
 * @package ZookaStudio
 * @subpackage Monaco
 * @since 1.0.0
 */
$portfolio_meta = cms_post_meta_data();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('container portfolio-fullwidth'); ?>>
	<?php  
		cms_portfolio_media();
	?>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
			<h2 class="entry-header">
				<?php echo __('About the project', 'foldery' ); ?>
			</h2>
			<div class="entry-description">
				<?php 
					
					the_content();
				?>
			</div>
			<div class="entry-content">
				<footer class="entry-footer clearfix">
				    <?php cms_portfolio_get_socials_share(); ?>
				</footer>
			</div>
		</div>
		<div class="entry-content col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div class="portfolio-meta">
			<?php 
				if(!empty($portfolio_meta->_cms_date)) echo  '<div class="entry-date"><h5 class="details-title">'.__('Date', 'foldery' ).'</h5><div class="playfairdisplay">'.esc_attr($portfolio_meta->_cms_date).'</div></div>'; 
	    		if(!empty($portfolio_meta->_cms_client)) echo  '<div class="entry-client"><h5 class="details-title">'.__('Client', 'foldery' ).'</h5><div class="playfairdisplay">'.esc_attr($portfolio_meta->_cms_client).'</div></div>';
	    		echo  '<div class="entry-category"><h5 class="details-title">'.__('Category', 'foldery' ).'</h5><div class="playfairdisplay">';
	    		cms_portfolio_detail();
	    		echo '</div></div>';
			?>
			</div>
		</div>
		<?php 
			wp_link_pages( array(
        		'before'      => '<div class="pagination loop-pagination"><span class="page-links-title">' . esc_html__( 'Pages:', 'foldery' ) . '</span>',
        		'after'       => '</div>',
        		'link_before' => '<span class="page-numbers">',
        		'link_after'  => '</span>',
    		) );
		?>
	</div>
	<!-- .entry-content -->
</article>
<!-- #post -->
