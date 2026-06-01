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

<article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

	<?php cms_archive_quote(false); ?>

	<div class="entry-header">
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<div class="entry-meta cms-blog-meta cms-meta"><?php cms_archive_detail(); ?></div>
	</div>
	<!-- .entry-header -->

	<div class="entry-content">
		<?php 
		$quote = preg_match('/\<blockquote\>(.*)\<\/blockquote\>/', get_the_content(), $blockquote);
		if($quote){ echo apply_filters('the_content', preg_replace('/<blockquote>(.*)<\/blockquote>/', '', get_the_content(), 1));} else { the_content(); }
    		wp_link_pages( array(
        		'before'      => '<div class="pagination loop-pagination"><span class="page-links-title">' . esc_html__( 'Pages:', 'foldery' ) . '</span>',
        		'after'       => '</div>',
        		'link_before' => '<span class="page-numbers">',
        		'link_after'  => '</span>',
    		) );
		?>
	</div>
	<!-- .entry-content -->

	<footer class="entry-footer clearfix">
	    <?php cms_get_socials_share(); ?>
	    <?php cms_single_tag(); ?>
	</footer>
	<!-- .entry-meta -->
</article>
<!-- #post -->
