<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package ZookaStudio
 * @subpackage Foldery
 * @since 1.0.0
 */
?>

<?php global $cms_meta; ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content<?php //echo !empty($cms_meta->_cms_one_page_full) ? ' full-page' : ''; ?>">
			<?php the_content(); ?>
	</div><!-- .entry-content -->
	<footer class="entry-meta">
			<?php edit_post_link( esc_html__( 'Edit', 'foldery' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->
</article><!-- #post -->
