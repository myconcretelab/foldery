<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Twelve already
 * has tag.php for Tag archives, category.php for Category archives, and
 * author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package ZookaStudio
 * @subpackage Foldery
 * @since 1.0.0
 */
global $smof_data, $paged, $wp_query;
$limit = get_option('posts_per_page','10');
get_header(); ?>
<div class="container">
    <div class="row">
        <section id="primary" class="archive cms-blog cms-blog-standard col-xs-12 col-sm-9 col-md-8 col-lg-8">
            <div id="content" role="main">

            <?php if ( have_posts() ) : ?>
                <?php
                    if($smof_data['blog_nav']){
                        /*ajax media*/
                        wp_enqueue_style( 'wp-mediaelement' );
                        wp_enqueue_script( 'wp-mediaelement' );
                        /* js, css for load more */
                        wp_register_script( 'cms-loadmore-js', get_template_directory_uri().'/assets/js/cms_loadmore.js', array('jquery') ,'1.0',true);
                        // What page are we on? And what is the pages limit?
                        $max = $wp_query->max_num_pages;
                        $paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;

                        // Add some parameters for the JS.
                        wp_localize_script(
                            'cms-loadmore-js',
                            'cs_more_obj',
                            array(
                                'startPage' => $paged,
                                'maxPages' => $max,
                                'total' => $wp_query->found_posts,
                                'perpage' => $limit,
                                'nextLink' => next_posts($max, false),
                                'ajaxType' => 'Button',
                                'masonry' => 'basic'
                            )
                        );
                        wp_enqueue_script( 'cms-loadmore-js' );
                    }
                ?>
                <div class="cms-isotope-masonry-post cms-grid-masonry-isotope clearfix">
                <?php
                /* Start the Loop */
                while ( have_posts() ) : the_post();

                    /* Include the post format-specific template for the content. If you want to
                     * this in a child theme then include a file called called content-___.php
                     * (where ___ is the post format) and that will be used instead.
                     */
                    echo '<div class="cms-grid-item">';
                    get_template_part( 'single-templates/standard/content', get_post_format() );
                    echo '</div>';
                endwhile;
                ?>
                <?php
                    cms_paging_nav_layout();
                ?>
                </div>
            <?php else : ?>
                <?php get_template_part( 'single-templates/content', 'none' ); ?>
            <?php endif; ?>

            </div><!-- #content -->
        </section><!-- #primary -->
        <div id="page-sidebar" class="col-xs-12 col-sm-3 col-md-4 col-lg-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>