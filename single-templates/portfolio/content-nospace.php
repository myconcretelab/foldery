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

<article id="post-<?php the_ID(); ?>" <?php post_class('nospace text-center'); ?>>
	<div class="entry-content">
		<div class="entry-media cms-blog-media cms-media overlay-wrap">
			<?php if(has_post_thumbnail() && !post_password_required() && !is_attachment() &&  wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $thumbmail_size, false)):
				the_post_thumbnail($thumbmail_size); 
			else:
				$thumbnail = '<img src="'.CMS_IMAGES.'no-image.jpg" alt="'.get_the_title().'" />';
				echo cms_allowed_html($thumbnail);
			endif; ?>
			<div class="overlay">
				<div class="overlay-content">
					<div class="entry-header">
						<h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
						<div class="entry-meta cms-blog-meta cms-meta"><?php cms_portfolio_detail(); ?></div>	
					</div>
				</div>
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
