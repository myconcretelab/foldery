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
	<div class="entry-header">
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<div class="entry-meta cms-blog-meta cms-meta"><?php cms_standard_blog_detail(); ?></div>
	</div>
	<!-- .entry-header -->

	<div class="entry-content">
		<?php if(has_post_thumbnail()) {?>
		<div class="entry-media cms-blog-media cms-media"><?php the_post_thumbnail('large'); ?></div>
		<?php cms_archive_introtext() ?>
		<?php } else {
			the_excerpt();
			cms_archive_readmore(); 
		 } ?>
		<?php 
    		wp_link_pages( array(
        		'before'      => '<div class="pagination loop-pagination"><span class="page-links-title">' . esc_html__( 'Pages:', 'foldery' ) . '</span>',
        		'after'       => '</div>',
        		'link_before' => '<span class="page-numbers">',
        		'link_after'  => '</span>',
    		) );
		?>
	</div>
</article>
<!-- #post -->
