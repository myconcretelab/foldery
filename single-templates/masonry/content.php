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
global $thumbmail_size;

if(false !== wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $thumbmail_size, false))
	$size = $thumbmail_size;
else 
	$size = 'full';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
	
	<?php if(has_post_thumbnail()) {?>
		<div class="entry-media cms-blog-media cms-media"><?php the_post_thumbnail($size); ?></div>
	<?php } else {
		the_excerpt();
	 } ?>
	<div class="entry-content">
		<div class="entry-header">
			<h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
			<div class="entry-meta cms-blog-meta cms-meta"><?php cms_standard_blog_detail(); ?></div>
		</div>
		<?php cms_archive_readmore(); ?>
		<!-- .entry-header -->
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
