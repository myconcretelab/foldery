<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package ZookaStudio
 * @subpackage Foldery
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-blog">
		<div class="entry-header">
			<div class="entry-date">
				<div class="arow-date"></div>
				<?php cms_archive_post_icon(); ?>
				<span><?php echo get_the_date("F d,Y"); ?></span>
			</div>
		    <h2 class="entry-title">
		    	<a href="<?php the_permalink(); ?>">
		    		<?php
			    		if(is_sticky()){
			                echo "<i class='fa fa-thumb-tack'></i>";
			            }
			    	?>
		    		<?php the_title(); ?>
		    	</a>
		    </h2>
		    <div class="entry-feature entry-video"><?php cms_archive_video(); ?></div>
			<div class="entry-meta"><?php cms_archive_detail(); ?></div>
		</div>
		<!-- .entry-header -->

		<div class="entry-content">
			<?php the_excerpt();
	    		wp_link_pages( array(
	        		'before'      => '<div class="pagination loop-pagination"><span class="page-links-title">' . esc_html__( 'Pages:', 'foldery' ) . '</span>',
	        		'after'       => '</div>',
	        		'link_before' => '<span class="page-numbers">',
	        		'link_after'  => '</span>',
	    		) );
			?>
		</div>
		<!-- .entry-content -->

		<footer class="entry-footer">
		    <?php cms_archive_readmore(); ?>
		    <!-- .readmore link -->
		</footer>
		<!-- .entry-footer -->
	</div>
	<!-- .entry-blog -->
</article>
<!-- #post -->
