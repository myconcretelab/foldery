<?php
/**
 * The Template for displaying all single posts
 *
 * @package ZookaStudio
 * @subpackage Monaco
 * @since 1.0.0
 */

get_header(); ?>
<div class="<?php cms_main_class(); ?>">
    <div class="row">
        <div id="primary" class="single col-xs-12 col-sm-9 col-md-8 col-lg-8">
            <div id="content" role="main">

                <?php while ( have_posts() ) : the_post(); ?>
                    <?php cms_post_nav(); ?>
                    <?php get_template_part( 'single-templates/single/content', get_post_format() ); ?>

                    <div class="entry-author clearfix">
                        <div class="entry-author-avatar col-xs-6 col-sm-6 col-md-6 col-lg-6 nopaddingleft">
                            <div class="author-avatar vcard">
                                <?php echo get_avatar(get_the_author_meta('ID'), 100); ?>
                            </div>
                            <div class="author-info">
                                <h3><?php echo get_the_author(); ?></h3>
                                <div class="playfairdisplay"><?php echo get_the_author_meta('email'); ?></div>
                            </div>
                        </div>
                        <div class="entry-author-info col-xs-6 col-sm-6 col-md-6 col-lg-6 nopaddingright text-right">
                            <?php cms_single_comment(); ?>
                            <?php cms_single_like(); ?>
                        </div>
                    </div>

                    <?php comments_template( '', true ); ?>

                <?php endwhile; // end of the loop. ?>

            </div><!-- #content -->
        </div><!-- #primary -->
        <div id="page-sidebar" class="col-xs-12 col-sm-3 col-md-4 col-lg-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>