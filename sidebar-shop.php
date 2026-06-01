<?php
/**
 * The sidebar containing the main widget area
 *
 * If no active widgets are in the sidebar, hide it completely.
 *
 * @package ZookaStudio
 * @subpackage Foldery
 * @since 1.6.0
 */
?>

<?php if (foldery_wc_sidebar()) : ?>
	<div id="sidebar-area" class="widget-area sidebar-area sidebar-shop col-md-3">
		<?php dynamic_sidebar( 'sidebar-9' ); ?>
	</div>
<?php endif; ?>