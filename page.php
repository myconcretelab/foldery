<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package ZookaStudio
 * @subpackage Monaco
 * @since 1.0.0
 * @author Fox
 */
global $smof_data, $cms_meta;
get_header(); 
?>
<div id="cms-page-content" class="<?php cms_main_class(); ?>">
	<div id="primary">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php  get_template_part( 'single-templates/content', 'page' ); ?>
				<?php if($smof_data['page_comment']=='1'):?>
					<?php comments_template( '', true ); ?>
				<?php endif; ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->
</div>
<?php get_footer(); ?>