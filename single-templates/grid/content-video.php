<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package ZookaStudio
 * @subpackage Monaco
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php cms_archive_video(); ?>
	<div class="entry-content">
		<div class="entry-header">
			<h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
			<div class="entry-meta cms-blog-meta cms-meta"><?php cms_archive_detail(); ?></div>
		</div>
		<!-- .entry-header -->	
		<?php cms_archive_readmore(); ?>
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

	<!-- .entry-meta -->
</article>
<!-- #post -->
