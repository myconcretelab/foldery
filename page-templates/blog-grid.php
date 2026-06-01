<?php
/**
 * Template Name: Blog Grid
 *
 * @package ZookaStudio
 * @subpackage Foldery
 * @since 1.0.0
 * @author Luan Nguyen
 */

get_header();
global $post,$cms_meta,$paged,$cs_span,$cs_cat_class,$masonry_filter;
global $myglobal_page_id;
$myglobal_page_id = get_the_ID();
?>

<?php
    if($cms_meta->_cms_masonry_limit!='') $limit = $cms_meta->_cms_masonry_limit; else $limit = '8';
    if($cms_meta->_cms_masonry_columns!='') $masonry_columns = $cms_meta->_cms_masonry_columns; else $masonry_columns = '2';
    $column = 12/$masonry_columns;

    $masonry_loadmore = (isset($cms_meta->_cms_masonry_loadmore)&& $cms_meta->_cms_masonry_loadmore!='')?true:false;
    $masonry_filter = (isset($cms_meta->_cms_masonry_filter)&& $cms_meta->_cms_masonry_filter!='')?true:false;

    $categories = isset($cms_meta->_cms_post_categories)?$cms_meta->_cms_post_categories:'';
    if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
    elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
    else { $paged = 1; }
    global $cs_span;
    $cs_span = "cols-".$masonry_columns;
    /*script*/
    wp_enqueue_script('imagesloaded', get_template_directory_uri(). '/assets/js/jquery.imagesloaded.js', array('jquery'));
    wp_enqueue_script('jquery-isotope', get_template_directory_uri(). '/assets/js/jquery.isotope.min.js', array('jquery','imagesloaded'));
    wp_enqueue_script('cms-jquery-isotope', get_template_directory_uri(). '/assets/js/jquery.isotope.cms.js', array('jquery-isotope'));

    /* Get thumbmail size for multiple columns */
    $thumbmail_size = 'large';

    if(is_active_sidebar('sidebar-1')){
        $cls = 'col-xs-12 col-sm-9 col-md-8 col-lg-8';
    } else {
        $cls = 'col-xs-12 col-sm-12 col-md-12 col-lg-12';
    }
     
?>

<div class="<?php cms_main_class(); ?>">
    <div class="row">
        <div id="primary" class="<?php echo esc_attr($cls); ?>">
            <div id="content" class="cms-blog cms-blog-grid grid-<?php echo esc_attr($masonry_columns);?>" role="main">
            <?php
                if($masonry_filter){
                    get_template_part( 'single-templates/filter');
                }
                 
                $params = array(
                    'post_type'     => 'post',
                    'showposts'     => $limit,
                    'paged'         => $paged,
                );
                $cat_id = explode(',', $categories);
                if(is_numeric($cat_id[0]))
                    $params['cat'] = $categories;
                else
                    $params['category_name'] = $categories;
            ?>
            <?php $wp_query = new WP_Query($params); ?>
            <?php if ( $wp_query->have_posts() ) : ?>
                <?php
                    if($masonry_loadmore){
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
                                'masonry' => 'masonry'
                            )
                        );
                        wp_enqueue_script( 'cms-loadmore-js' );
                    }
                ?>
                <div class="cms-loadmore-post cms-isotope-grid-post row clearfix">
                <?php while ( $wp_query->have_posts() ) : $wp_query->the_post();
                        $categories = get_the_category($post->ID);
                        $cms_cat_class = '';
                        if(is_array($categories)){
                            foreach($categories as $category){
                                $cms_cat_class .= 'category-'.$category->slug.' ';
                            }
                        }
                    /* Include the post format-specific template for the content. If you want to
                     * this in a child theme then include a file called called content-___.php
                     * (where ___ is the post format) and that will be used instead.
                     */

                    echo '<div class="cms-grid-item text-center col-xs-12 col-sm-12 col-md-'.$column.' col-lg-'.$column.' '.$cms_cat_class.'">';
                        get_template_part( 'single-templates/grid/content', get_post_format() );
                    echo '</div>';

                endwhile; // end of the loop.?>
                </div>
            <?php else : ?>
                <?php get_template_part( 'single-templates/content', 'none' ); ?>
            <?php endif; ?> 
                
            <?php
                if($masonry_loadmore){
                    echo '<div class="cs_pagination"></div>';
                }
                else{
                    cms_paging_nav();    
                }
                wp_reset_postdata()
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
<?php get_footer(); ?>