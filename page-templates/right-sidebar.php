<?php
/**
 * Template Name: Right Sidebar
 *
 * @package ZookaStudio
 * @subpackage Monaco
 * @since 1.0.0
 * @author Fox
 */
if(is_active_sidebar('sidebar-1')){
    $cls = 'col-xs-12 col-sm-9 col-md-8 col-lg-8';
} else {
    $cls = 'col-xs-12 col-sm-12 col-md-12 col-lg-12';
}
get_header(); ?>
<div id="page-right-sidebar">
    <div class="container">
        <div class="row">
            <div id="primary" class="<?php echo esc_attr($cls); ?>">
                <div id="content" role="main">

                   <?php
                        // Start the loop.
                        while ( have_posts() ) : the_post();

                            // Include the page content template.
                            get_template_part( 'page-templates/content', 'page' );

                            // If comments are open or we have at least one comment, load up the comment template.
                            if ( comments_open() || get_comments_number() ) :
                                comments_template();
                            endif;

                        // End the loop.
                        endwhile;
                        ?>

                </div><!-- #content -->
            </div><!-- #primary -->
            <?php if(is_active_sidebar('sidebar-1')){ ?>
            <div id="page-sidebar" class="col-xs-12 col-sm-3 col-md-4 col-lg-4">
                <?php get_sidebar(); ?>
            </div><!-- #page-sidebar -->
            <?php } ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>