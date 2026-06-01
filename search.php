<?php
/**
 * The template for displaying Search Results pages
 *
 * @package ZookaStudio
 * @subpackage Foldery
 * @since 1.0.0
 */

get_header(); ?>
<div class="container">
    <div class="row">
        <section id="primary" class="cms-blog cms-blog-standard col-xs-12 col-sm-9 col-md-9 col-lg-9">
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
                                'perpage' => get_option('post_per_page'),
                                'nextLink' => next_posts($max, false),
                                'ajaxType' => 'Button',
                                'masonry' => 'basic'
                            )
                        );
                        wp_enqueue_script( 'cms-loadmore-js' );
                    }
                ?>
                <div class="cms-isotope-masonry-post cms-grid-masonry-isotope clearfix">

                <?php /* Start the Loop */ ?>
                <?php while ( have_posts() ) : the_post(); 
                    //if(get_post_type() != 'page'){
                        echo '<div class="cms-grid-item">';
                        get_template_part( 'single-templates/standard/content', get_post_format() );
                        echo '</div>';
                    //}
                 endwhile; ?>

                <?php
                    if($smof_data['blog_nav']){
                        echo '<div class="cs_pagination"></div>';
                    }
                    else{
                        cms_paging_nav();    
                    }
                ?>
                </div>

            <?php else : ?>

                <article id="post-0" class="post no-results not-found">
                    <header class="entry-header">
                        <h1 class="entry-title"><?php esc_html_e( 'Nothing Found', 'foldery' ); ?></h1>
                    </header>

                    <div class="entry-content">
                        <p><?php esc_html_e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'foldery' ); ?></p>
                        <?php get_search_form(); ?>
                    </div><!-- .entry-content -->
                </article><!-- #post-0 -->

            <?php endif; ?>

            </div><!-- #content -->
        </section><!-- #primary -->
        <div id="page-sidebar" class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>