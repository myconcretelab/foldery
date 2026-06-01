<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
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
    <div class="<?php cms_main_class(); ?>">
        <div class="row">
            <div id="primary" class="index cms-blog blog-archive cms-blog-standard col-xs-12 col-sm-9 col-md-8 col-lg-8">
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

                        <?php /* Start the Loop */ ?>
                        <div class="cms-isotope-masonry-post cms-grid-masonry-isotope clearfix">
                        <?php while ( have_posts() ) : the_post(); ?>
                            <div class="cms-grid-item">
                            <?php get_template_part( 'single-templates/standard/content', get_post_format() ); ?>
                            </div>
                        <?php endwhile; ?>
                        
                        <?php
                            cms_paging_nav_layout();
                        ?>
                        </div>
                    <?php else : ?>

                        <article id="post-0" class="post no-results not-found">

                            <?php if ( current_user_can( 'edit_posts' ) ) :
                                // Show a different message to a logged-in user who can add posts.
                                ?>
                                <header class="entry-header">
                                    <h1 class="entry-title"><?php esc_html_e( 'No posts to display', 'foldery' ); ?></h1>
                                </header>

                                <div class="entry-content">
                                    <p><?php printf( esc_html__( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'foldery' ), admin_url( 'post-new.php' ) ); ?></p>
                                </div><!-- .entry-content -->

                            <?php else :
                                // Show the default message to everyone else.
                                ?>
                                <header class="entry-header">
                                    <h1 class="entry-title"><?php esc_html_e( 'Nothing Found', 'foldery' ); ?></h1>
                                </header>

                                <div class="entry-content">
                                    <p><?php esc_html_e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', 'foldery' ); ?></p>
                                    <?php get_search_form(); ?>
                                </div><!-- .entry-content -->
                            <?php endif; // end current_user_can() check ?>

                        </article><!-- #post-0 -->

                    <?php endif; // end have_posts() check ?>

                </div><!-- #content -->
            </div><!-- #primary -->
            <div id="page-sidebar" class="col-xs-12 col-sm-3 col-md-4 col-lg-4">
                <?php get_sidebar(); ?>
            </div>
        </div>
    </div>
<?php get_footer(); ?>