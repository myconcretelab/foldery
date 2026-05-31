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
global $thumbmail_size;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
		Salut !
		<?php if(has_post_thumbnail()) {?>
		<div class="entry-media cms-blog-media cms-media"><?php the_post_thumbnail($thumbmail_size); ?></div>
		<?php } else {
			the_excerpt();
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
	<!-- .entry-content -->
	<div class="entry-header">
		<?php cms_portfolio_readmore(); ?>
		<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<div class="entry-meta cms-blog-meta cms-meta"><?php cms_portfolio_detail(); ?></div>
		
	</div>
</article>
<!-- #post -->
