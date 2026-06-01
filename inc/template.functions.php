<?php

/**
 * add theme class to body tag
 * @since 1.4.0
 * @author Chinh Duong Manh
 */
add_filter( 'body_class', 'foldery_body_class' ); 
function foldery_body_class($class=''){
    global $smof_data, $cms_meta, $foldery_base;
    $class[] = is_rtl() ? 'rtl' : 'ltr';
    if(is_front_page()) $class[] = 'monaco-home';

    if(!isset($smof_data)) {
        $class[] = 'cms-header-default';
    }
    /* header for page */
    if(isset($cms_meta->_cms_header) && $cms_meta->_cms_header){
        if(isset($cms_meta->_cms_header_layout)){
            $smof_data['header_layout'] = $cms_meta->_cms_header_layout;
        }
    }
    /* add header layout class. */
    if($smof_data['header_layout'])   $class[] = 'cms-header-'.$smof_data['header_layout'];

    /* Header v1 */
    if( $smof_data['header_layout'] === 'v1' && $smof_data['header_fixed'] === ''){
        $class[] = 'cms-custom-vc-row-stretch-content';
    }

    return $class;
}

/**
 * Main Logo 
 * @since 2.2
 * @author Chinh Duong Manh
 * @return Main Logo
*/
function foldery_main_logo(){
    global $smof_data;
    $logo_url = isset($smof_data['main_logo']['url']) && !empty($smof_data['main_logo']['url']) ? $smof_data['main_logo']['url'] : get_template_directory_uri().'/assets/images/logo.png';
    ?>
    <a href="<?php echo home_url(); ?>"><img alt="<?php echo get_bloginfo('name');?>" src="<?php echo esc_url($logo_url); ?>"></a>
    <?php
}


/**
 * Page title template
 * @since 1.0.0
 * @author Fox
 */
function cms_page_title(){
    global $smof_data, $cms_meta, $foldery_base, $woocommerce;
    
    /* page options */
    if(is_page() && isset($cms_meta->_cms_page_title) && $cms_meta->_cms_page_title){
        if(isset($cms_meta->_cms_page_title_type)){
            $smof_data['page_title_layout'] = $cms_meta->_cms_page_title_type;
        }
    }
    if (class_exists('woocommerce') && is_woocommerce()) return;
    if($smof_data['page_title_layout']){
        ?>
        <div class="<?php echo esc_attr($smof_data['page_title_fullwidth'])?'no-container':'container';?>">
        <div id="page-title" class="page-title">
            <div class="container">
            <div class="row">
            <?php switch ($smof_data['page_title_layout']){
                case '1':
                    ?>
                    <div id="page-title-text" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><h1><?php $foldery_base->getPageTitle(); ?></h1><?php cms_page_sub_title(); ?></div>
                    <div id="breadcrumb-text" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><?php $foldery_base->getBreadCrumb(); ?></div>
                    <?php
                    break;
                case '2':
                    ?>
                    <div id="breadcrumb-text" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><?php $foldery_base->getBreadCrumb(); ?></div>
                    <div id="page-title-text" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><h1><?php $foldery_base->getPageTitle(); ?></h1><?php cms_page_sub_title(); ?></div>
                    <?php          
                    break;
                case '3':
                    ?>
                    <div id="page-title-text" class="col-xs-12 col-sm-6 col-md-6 col-lg-6"><h1><?php $foldery_base->getPageTitle(); ?></h1><?php cms_page_sub_title(); ?></div>
                    <div id="breadcrumb-text" class="col-xs-12 col-sm-6 col-md-6 col-lg-6"><?php $foldery_base->getBreadCrumb(); ?></div>
                    <?php
                    break;
                case '4':
                    ?>
                    <div id="breadcrumb-text" class="col-xs-12 col-sm-6 col-md-6 col-lg-6"><?php $foldery_base->getBreadCrumb(); ?></div>
                    <div id="page-title-text" class="col-xs-12 col-sm-6 col-md-6 col-lg-6"><h1><?php $foldery_base->getPageTitle(); ?></h1><?php cms_page_sub_title(); ?></div>
                    <?php
                    break;
                case '5':
                    ?>
                    <div id="page-title-text" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center"><h1><?php $foldery_base->getPageTitle(); ?></h1><?php cms_page_sub_title(); ?></div>
                    <?php
                    break;
                case '6':
                    ?>
                    <div id="breadcrumb-text" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><?php $foldery_base->getBreadCrumb(); ?></div>
                    <?php
                    break;
                default :
                    ?>
                    <div id="page-title-text" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><h1><?php $foldery_base->getPageTitle(); ?></h1><?php cms_page_sub_title(); ?></div>
                    <div id="breadcrumb-text" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><?php $foldery_base->getBreadCrumb(); ?></div>
                    <?php
                    break;
            } ?>
            </div>
            </div>
        </div><!-- #page-title -->
        </div>
        <?php
    }
}

/**
 * Get sub page title.
 *
 * @author Fox
 */
function cms_page_sub_title(){
    global $cms_meta, $post;

    if(!empty($cms_meta->_cms_page_title_sub_text)){
        echo '<div class="page-sub-title">'.esc_attr($cms_meta->_cms_page_title_sub_text).'</div>';
    } elseif (!empty($post->ID) && get_post_meta($post->ID, 'post_subtitle', true)){
        echo '<div class="page-sub-title">'.esc_attr(get_post_meta($post->ID, 'post_subtitle', true)).'</div>';
    }
}

/**
 * Shop Page title template
 * @since 1.0.0
 * @author Chinh Duong Manh
 */
function cms_shop_page_sub_title(){
    global $smof_data;
    if($smof_data['shop_page_sub_title']!=''){
        echo '<div class="shop-page-sub-title page-sub-title">'.esc_attr($smof_data['shop_page_sub_title']).'</div>';
    }
}
function cms_shop_page_main_title(){
    global $smof_data;
    if(is_archive()){
        if($smof_data['shop_page_title']!=''){
            echo '<h1 class="custom">'.esc_attr($smof_data['shop_page_title']).'</h1>';
            cms_shop_page_sub_title();
        } else {
            echo '<h1 class="woo-default">';
            woocommerce_page_title();
            echo '</h1>';
            cms_shop_page_sub_title();
        }
    } else {  /* get single product title */
        echo '<h1 class="woo-default product-title">';
            the_title();
        echo '</h1>';
    }
}

function cms_shop_page_title(){
    global $smof_data;
        
    if($smof_data['enable_shop_page_title']){
        ?>
        <div class="<?php echo esc_attr($smof_data['shop_page_title_fullwidth'])?'no-container':'container';?>">
        <div id="shop-page-title" class="shop-page-title page-title">
            <div class="container">
            <div class="row">
            <?php switch ($smof_data['shop_page_title_layout']){
                case '1':
                    ?>
                    <div id="shop-page-title-text" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center"><?php cms_shop_page_main_title(); ?></div>
                    <div id="shop-breadcrumb-text" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center"><?php woocommerce_breadcrumb(); ?></div>
                    <?php
                    break;
                case '2':
                    ?>
                    <div id="shop-breadcrumb-text" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center"><?php woocommerce_breadcrumb(); ?></div>
                    <div id="shop-page-title-text" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center"><?php cms_shop_page_main_title(); ?></div>
                    <?php          
                    break;
                case '3':
                    ?>
                    <div id="shop-page-title-text" class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-center"><?php cms_shop_page_main_title(); ?></div>
                    <div id="breadcrumb-text" class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-center"><?php woocommerce_breadcrumb(); ?></div>
                    <?php
                    break;
                case '4':
                    ?>
                    <div id="shop-breadcrumb-text" class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-center"><?php woocommerce_breadcrumb(); ?></div>
                    <div id="shop-page-title-text" class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-center"><?php cms_shop_page_main_title(); ?></div>
                    <?php
                    break;
                case '5':
                    ?>
                    <div id="shop-page-title-text" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center"><?php cms_shop_page_main_title(); ?></div>
                    <?php
                    break;
                case '6':
                    ?>
                    <div id="shop-breadcrumb-text" class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-center"><?php woocommerce_breadcrumb(); ?></div>
                    <?php
                    break;
            } ?>
            </div>
            </div>
        </div><!-- #shop-page-title -->
        </div>
        <?php
    }
}

/**
 * Get Header Layout.
 * 
 * @author Fox
 */
function cms_header(){
    global $smof_data, $cms_meta;
    /* header for page */
    if(isset($cms_meta->_cms_header) && $cms_meta->_cms_header){
        if(isset($cms_meta->_cms_header_layout)){
            $smof_data['header_layout'] = $cms_meta->_cms_header_layout;
        }
    }
    /* load template. */
    get_template_part('inc/header/header', $smof_data['header_layout']);
}

/**
 * Get Header Class Style.
 * 
 * @author Chinh Duong Manh
 */
 function cms_header_class(){
    global $smof_data, $cms_meta;
    $cms_header_class = '';
    /* header for page */
    if(isset($cms_meta->_cms_header) && $cms_meta->_cms_header){
        if(isset($cms_meta->_cms_header_layout)){
            $smof_data['header_layout'] = $cms_meta->_cms_header_layout;
        }
        if(isset($cms_meta->_cms_header_fixed)){
            $smof_data['header_fixed'] = $cms_meta->_cms_header_fixed;
        }
    }
    /* Header layout */
    if($smof_data['header_layout']){
        $cms_header_class .= 'header-'.$smof_data['header_layout'];
    } else {
        $cms_header_class .= 'header-default';
    }
    /* Header Type */
    switch ($smof_data['header_fixed']) {
        case 'ontop':
            $cms_header_class .=' header-ontop';
            break;
        case 'fixed':
            $cms_header_class .=' header-fixed';
            break;
        default:
            $cms_header_class .='';
            break;
    }
    /* Sticky Header */
    if (!$smof_data['menu_sticky']) {
        $cms_header_class .= ' no-sticky';
    } else {
        $cms_header_class .= ' has-sticky';
        if ($smof_data['menu_sticky_tablets']) {
            $cms_header_class .= ' sticky-tablets';
        }
        if ($smof_data['menu_sticky_mobile']){
            $cms_header_class .= ' sticky-mobile';
        }
    }
    
    $cms_header_class .=' clearfix';
    echo apply_filters('cms_header_class', $cms_header_class);
}
function cms_header_wrap_class(){
    global $smof_data, $cms_meta;
    $cms_header_wrap_class = '';
    /* header for page */
    if(isset($cms_meta->_cms_header) && $cms_meta->_cms_header){
        if(isset($cms_meta->_cms_header_layout)){
            $smof_data['header_layout'] = $cms_meta->_cms_header_layout;
        }
    }
    /* Header layout */
    if($smof_data['header_layout']){
        $cms_header_wrap_class .= 'header-'.$smof_data['header_layout'];
        if($smof_data['header_layout'] =='v1'){
            $cms_header_wrap_class .= ' header-'.$smof_data['header_position'];
        }
    } else {
        $cms_header_wrap_class .= 'header-default';
    }

    /* Header Type */
    switch ($smof_data['header_fixed']) {
        case 'ontop':
            $cms_header_wrap_class .=' header-ontop';
            break;
        case 'fixed':
            $cms_header_wrap_class .=' header-fixed';
            break;
        default:
            $cms_header_wrap_class .='';
            break;
    }

    /* Header Border Bottom */
    switch ($smof_data['header_border_on_home']) {
        case '1':
            $cms_header_wrap_class .='';
            break;
        case '0':
            if(is_front_page()){
                $cms_header_wrap_class .=' no-border-home';
            }
            break;
        default:
            $cms_header_wrap_class .=' no-border-home';
            break;
    }

    $cms_header_wrap_class .=' clearfix';
    echo apply_filters('cms_header_wrap_class', $cms_header_wrap_class);
}
/**
 * Get header menu position
 * @author Chinh Duong Manh
 */
function cms_mainmenu_position(){
    global $smof_data;
    echo 'pull-'.$smof_data['header_menu_position'];
}
/**
 * Get menu location ID.
 * 
 * @param string $option
 * @return NULL
 */
function cms_menu_location($option = '_cms_primary'){
    global $cms_meta;
    /* get menu id from page setting */
    return (isset($cms_meta->$option) && $cms_meta->$option) ? $cms_meta->$option : null ;
}

/**
 * Get menu left location ID.
 * 
 * @param string $option
 * @return NULL
 */
function cms_menu_left_location(){
    global $cms_meta, $smof_data;

    if(!empty($cms_meta->_cms_leftmenu)) $smof_data['header_left_menu'] = $cms_meta->_cms_leftmenu;

    return $smof_data['header_left_menu'];
}

/**
 * Get menu right location ID.
 * 
 * @param string $option
 * @return NULL
 */
function cms_menu_right_location(){
    global $cms_meta, $smof_data;

    if(!empty($cms_meta->_cms_rightmenu)) $smof_data['header_right_menu'] = $cms_meta->_cms_rightmenu;

    return $smof_data['header_right_menu'];
}

/**
 * Get menu onepage nav location ID.
 * 
 * @param string $option
 * @return NULL
 */
function cms_menu_onepage_nav_location(){
    global $cms_meta, $smof_data;

    if(!empty($cms_meta->_cms_onepage_nav)) $smof_data['onepage_nav_menu'] = $cms_meta->_cms_onepage_nav;

    return $smof_data['onepage_nav_menu'];
}

/**
 * Add page class
 * 
 * @author Fox
 * @since 1.0.0
 */
function cms_page_class(){
    global $smof_data;
    
    $page_class = '';
    /* check boxed layout */
    if($smof_data['body_layout']){
        $page_class = 'cs-boxed';
    } else {
        $page_class = 'cs-wide';
    }
    
    echo apply_filters('cms_page_class', $page_class);
}

/**
 * Add main class
 * 
 * @author Fox
 * @since 1.0.0
 */
function cms_main_class(){
    global $cms_meta;
    
    $main_class = '';
    /* chect content full width */
    $page_template = array(
        'page-templates/blog-grid.php',
        'page-templates/blog-masonry.php',
        'page-templates/blog-masonry2.php',
        'page-templates/blog-standard.php',
        'page-templates/portfolio-grid.php',
        'page-templates/portfolio-grid2.php',
        'page-templates/portfolio-masonry.php',
    );
    if(is_page() && isset($cms_meta->_cms_full_width) && $cms_meta->_cms_full_width && is_page_template($page_template)) {
        /* full width */
        $main_class = "no-container";
    } else {
        /* boxed */
        $main_class = "container";
    }
    
    echo apply_filters('cms_main_class', $main_class);
}

/**
 * Page Navigation
 * 
 * @author Chinh Duong Manh
 * @since 1.0.0
 */
function cms_paging_nav_layout(){
    global $smof_data, $cms_meta;
    $masonry_loadmore = (isset($cms_meta->_cms_masonry_loadmore) && $cms_meta->_cms_masonry_loadmore!='')?1:'';

    if($masonry_loadmore) $smof_data['blog_nav'] = $cms_meta->_cms_masonry_loadmore;

    switch ($smof_data['blog_nav']) {
        case '':
            break;
        case '1':   
            echo '<div class="cs_pagination"></div>';
            break;
        case '2':
            cms_paging_nav2();
            break;    
        default:
            cms_paging_nav();
            break;
    }
}

/**
 * Archive show/hide introtext
 * 
 * @author Chinh Duong Manh
 * @since 1.0.0
 */
function cms_archive_introtext(){
    global $smof_data;  
    if($smof_data['blog_introtext']){
        echo '<div class="entry-introtext">';
        $content = get_the_excerpt();
        echo strip_shortcodes( $content );
        cms_archive_readmore();
        echo '</div>';
    }
}
/**
 * Archive detail
 * 
 * @author Fox
 * @since 1.0.0
 */
function cms_archive_detail(){
    global $smof_data; 
    $show_date = isset($smof_data['meta_post_date']) ? $smof_data['meta_post_date'] : true ;
    $show_author = isset($smof_data['meta_post_author']) ? $smof_data['meta_post_author'] : true ;
    $show_category = isset($smof_data['meta_post_category']) ? $smof_data['meta_post_category'] : true ;
    $show_comment = isset($smof_data['meta_post_comment']) ? $smof_data['meta_post_comment'] : true ;
    $show_like = isset($smof_data['meta_post_like']) ? $smof_data['meta_post_like'] : true ;
    ?>
    <ul class="list-unstyled list-inline cms_archive_detail">
        <?php if($show_date) : ?><li class="detail-date"><a href="<?php echo get_day_link(get_the_time('Y'),get_the_time('m'),get_the_time('d'));?>"><?php echo get_the_date(get_option('date_format', 'Y/m/d'));?></a></li><?php endif; ?>
        <?php if($show_date) : ?><li class="detail-author"><?php the_author_posts_link(); ?></li><?php endif; ?>
        <?php if($show_category && has_category()): ?>
        <li class="detail-terms"><?php the_terms( get_the_ID(), 'category', '' ); ?></li>
        <?php endif; ?>
        <?php if($show_comment) : ?><li class="detail-comment"><a href="<?php the_permalink(); ?>"><?php echo comments_number('0','1','%'); ?> <?php esc_html_e('Comments', 'foldery'); ?></a></li><?php endif; ?>
        <?php if($show_like && function_exists('post_favorite')){ ?> <li class="entry-like"><?php post_favorite('', 'likes', false);?></li><?php } ?>
    </ul>
    <?php
}
function cms_standard_blog_detail(){
    global $smof_data; 
    $show_date = isset($smof_data['meta_post_date']) ? $smof_data['meta_post_date'] : true ;
    $show_author = isset($smof_data['meta_post_author']) ? $smof_data['meta_post_author'] : true ;
    $show_category = isset($smof_data['meta_post_category']) ? $smof_data['meta_post_category'] : true ;
    $show_comment = isset($smof_data['meta_post_comment']) ? $smof_data['meta_post_comment'] : true ;
    $show_like = isset($smof_data['meta_post_like']) ? $smof_data['meta_post_like'] : true ;
    ?>
    <ul class="list-unstyled list-inline cms_standard_blog_detail">
        <?php if($show_date) : ?><li class="detail-date"><a href="<?php echo get_day_link(get_the_time('Y'),get_the_time('m'),get_the_time('d'));?>"><?php echo get_the_date(get_option('date_format', 'Y/m/d'));?></a></li><?php endif; ?>
        <?php if($show_author) : ?><li class="detail-author"><?php the_author_posts_link(); ?></li><?php endif; ?>
        <?php if($show_category && has_category()): ?>
        <li class="detail-terms"><?php the_terms( get_the_ID(), 'category', '' ); ?></li>
        <?php endif; ?>
        <?php if($show_comment) : ?><li class="detail-comment"><a href="<?php the_permalink(); ?>"><?php echo comments_number('0','1','%'); ?> <?php esc_html_e('Comments', 'foldery'); ?></a></li><?php endif; ?>
        <?php if($show_like && function_exists('post_favorite')){ ?> <li class="entry-like"><?php post_favorite('', 'likes', false);?></li><?php } 
            edit_post_link();

        ?>
    </ul>
    <?php
}

/**
 * Archive readmore
 * 
 * @author Fox
 * @since 1.0.0
 */
function cms_archive_readmore(){
    echo '<a class="more-link" href="'.get_the_permalink().'" title="'.get_the_title().'" >'.__('View post &rarr;', 'foldery').'</a>';
}

/**
 * Media Audio.
 * 
 * @param string $before
 * @param string $after
 */
function cms_archive_audio() {
   global $wp_embed, $foldery_base;
    /* get shortcode audio. */
    $shortcode = $foldery_base->getShortcodeFromContent('audio', get_the_content());
    /* Get remote audio */
    $remote_audio = $foldery_base->getShortcodeFromContent('embed', get_the_content());

     /* Get soundcloud audio */
    $remote_soundcloud = $foldery_base->getShortcodeFromContent('soundcloud', get_the_content());

    if($remote_soundcloud != ''){
        echo '<div class="entry-media entry-audio entry-remote-soundcloud">'.do_shortcode($remote_soundcloud).'</div>';
        cms_archive_introtext();
        return true;
    } elseif ($remote_audio) {
        /* view remote audio. */
        echo '<div class="entry-media entry-audito entry-remote-audio">'.$wp_embed->run_shortcode($remote_audio).'</div>';
        cms_archive_introtext();
        return true;    
    } elseif($shortcode != ''){
        echo '<div class="entry-media entry-audio">'.do_shortcode($shortcode).'</div>';
        cms_archive_introtext();
        return true;
    }  elseif(has_post_thumbnail()){
        echo '<div class="entry-media entry-feature-image">';
        the_post_thumbnail('blog-grid');
        cms_archive_introtext();
        echo '</div>';
    } else {
        the_excerpt();
    }  
}

/**
 * Media Video.
 *
 * @param string $before
 * @param string $after
 */
function cms_archive_video() {
    
    global $wp_embed, $foldery_base;
    /* Get Local Video */
    $local_video = $foldery_base->getShortcodeFromContent('video', get_the_content());
    
    /* Get Youtube or Vimeo */
    $remote_video = $foldery_base->getShortcodeFromContent('embed', get_the_content());
    
    if($local_video){
        /* view local. */
        echo '<div class="entry-media entry-video entry-local-video">'.do_shortcode($local_video).'</div>';
        cms_archive_introtext();
        return true;
    } elseif ($remote_video) {
        /* view youtube or vimeo. */
        echo '<div class="entry-media entry-video entry-remote-video">'.$wp_embed->run_shortcode($remote_video).'</div>';
        cms_archive_introtext();
        return true;
    } elseif (has_post_thumbnail()) {
        /* view thumbnail. */
        echo '<div class="entry-media entry-feature-image">';
        the_post_thumbnail('blog-grid');
        cms_archive_introtext();
        echo '</div>';
    } else {
        the_excerpt();
    } 
}
/**
 * Gallerry Images
 * 
 * @author Fox
 * @since 1.0.0
 */
function cms_archive_gallery(){
    global $foldery_base;
    /* get shortcode gallery. */
    $shortcode = $foldery_base->getShortcodeFromContent('gallery', get_the_content());
    
    if($shortcode != ''){
        preg_match('/\[gallery.*ids=.(.*).\]/', $shortcode, $ids);
        
        if(!empty($ids)){
        
            $array_id = explode(",", $ids[1]);
            ?>
            <div id="carousel-<?php the_ID();?>" class="carousel slide entry-media entry-gallery" data-ride="carousel">
                <div class="carousel-inner">
                <?php $i = 0; ?>
                <?php foreach ($array_id as $image_id): ?>
                    <?php
                    $attachment_image = wp_get_attachment_image_src($image_id, 'full', false);
                    if($attachment_image[0] != ''):?>
                        <div class="item <?php if( $i == 0 ){ echo 'active'; } ?>">
                            <img style="width:100%;" data-src="holder.js" src="<?php echo esc_url($attachment_image[0]);?>" alt="<?php echo get_the_title();?>" />
                        </div>
                    <?php $i++; endif; ?>
                <?php endforeach; ?>
                </div>
                <a class="left carousel-control" href="#carousel-<?php the_ID();?>" role="button" data-slide="prev">
                    <span class="pe-7s-angle-left"></span>
                </a>
                <a class="right carousel-control" href="#carousel-<?php the_ID();?>" role="button" data-slide="next">
                    <span class="pe-7s-angle-right"></span>
                </a>
            </div>
            <?php
            cms_archive_introtext();
            return true;
            
        } else {
            return false;
        }
        
    } else {
        if(has_post_thumbnail()){
            echo '<div class="entry-media entry-feature-image">';
            the_post_thumbnail();
            cms_archive_introtext();
            echo '</div>';
        } else {
            the_excerpt();
        }
    }
}

/**
 * Quote Text.
 * 
 * @author Fox
 * @since 1.0.0
 */

function cms_archive_quote($introtext = true) {
    /* get text. */
    preg_match('/\<blockquote\>(.*)\<\/blockquote\>/', get_the_content(), $blockquote);
    if(has_post_thumbnail()){
        echo '<div class="quote-content has-thumbnail">';
        the_post_thumbnail('blog-grid');
        echo '<div class="overlay"><div class="overlay-content">';
            if(!empty($blockquote[0])){
                foldery_allowed_html($blockquote[0]);
            } else {
                '<blockquote>'.the_excerpt().'</blockquote>';
            }
        echo '</div></div>';
        echo '</div>';
        if($introtext) cms_archive_introtext();
    } else {
        echo '<div class="quote-content">';
        if(!empty($blockquote[0])){
            foldery_allowed_html($blockquote[0]);
        } else {
            '<blockquote>'.the_excerpt().'</blockquote>';
        }
        echo '</div>'; 
    }
}

/**
 * Get icon from post format.
 *
 * @return multitype:string Ambigous <string, mixed>
 * @author Fox
 * @since 1.0.0
 */
function cms_archive_post_icon() {
    $post_icon = array('icon'=>'fa fa-file-text-o','text'=>__('STANDARD', 'foldery'));
    switch (get_post_format()) {
        case 'gallery':
            $post_icon['icon'] = 'fa fa-file-image-o';
            $post_icon['text'] = __('GALLERY', 'foldery');
            break;
        case 'link':
            $post_icon['icon'] = 'fa fa-external-link';
            $post_icon['text'] = __('LINK', 'foldery');
            break;
        case 'quote':
            $post_icon['icon'] = 'fa fa-quote-left';
            $post_icon['text'] = __('QUOTE', 'foldery');
            break;
        case 'video':
            $post_icon['icon'] = 'fa fa-film';
            $post_icon['text'] = __('VIDEO', 'foldery');
            break;
        case 'audio':
            $post_icon['icon'] = 'fa fa-bullhorn';
            $post_icon['text'] = __('AUDIO', 'foldery');
            break;
        default:
            if(is_sticky()){
                $post_icon['icon'] = 'fa fa-thumbs-o-up';
                $post_icon['text'] = __('STICKY', 'foldery');
            } else {
                $post_icon['icon'] = 'fa fa-file-text-o';
                $post_icon['text'] = __('STANDARD', 'foldery');
            }
            break;
    }
    echo '<i class="'.$post_icon['icon'].'"></i>';
}

/**
 * List socials share for post.
 * 
 * @since 1.0.0
 */
function cms_get_socials_share(){
    ?>
    <span class="post-share-title left">
        <span class="h6"><i class="pe-7s-share"></i> <?php echo esc_html_e('Share','foldery');?></span>
        <span class="post-share">
            <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink();?>"><i class="fa fa-facebook"></i></a>
            <a target="_blank" href="https://twitter.com/home?status=<?php esc_html_e('Check out this article', 'foldery');?>:%20<?php the_title();?>%20-%20<?php the_permalink();?>"><i class="fa fa-twitter"></i></a>
            <a target="_blank" href="https://pinterest.com/pin/create/button/?url=<?php echo the_permalink();?>&media=&description=<?php the_title();?>"><i class="fa fa-pinterest"></i></a>
            <a target="_blank" href="https://plus.google.com/share?url=<?php the_permalink();?>"><i class="fa fa-google-plus"></i></a>
            <a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo the_permalink();?>&title=<?php the_title();?>"><i class="fa fa-linkedin"></i></a>
        </span>
    </span>
    <?php
}

/**
 * Show post like.
 * 
 * @since 1.0.0
 */
function cms_get_post_like(){
    
    global $smof_data; 

    $show_like = isset($smof_data['meta_post_like']) ? $smof_data['meta_post_like'] : true ;
    
    ?>
    <?php if($show_like && function_exists('post_favorite')){ ?> <li class="entry-like"><?php post_favorite('', 'likes', false);?></li><?php } ?>
    
    <?php
}

/**
 * Show post view.
 * 
 * @since 1.0.0
 */
 function cms_get_post_view(){
     $views = get_post_meta(get_the_ID() , '_cms_post_views', true);
     
     if(!$views) $views = 0;
     
     ?>
     <div class="cms-post-view"><i class="fa fa-eye"></i><span><?php echo esc_attr($views); ?></span></div>
     <?php
 }

 /**
 * Single detail
 *
 * @author Fox
 * @since 1.0.0
 */
function cms_single_detail(){
    
}

function cms_single_tag(){
    if(has_tag()){
        echo '<span class="single-tags"><span class="h6">'.__('Tags','foldery').'</span><span class="tags-list list-tags">';
        the_tags('','');
        echo '</span></span>';
    }
}

function cms_single_comment(){
    echo '<span class="single-comment playfairdisplay">';
    comments_number('0','1','%');_e(' Comments', 'foldery'); 
    echo '</span>';
}

function cms_single_like(){
    
    global $smof_data; 

    $show_like = isset($smof_data['meta_post_like']) ? $smof_data['meta_post_like'] : true ;
    
    ?>
    <?php if($show_like  && function_exists('post_favorite')){ ?> <span class="cms-post-like playfairdisplay"><?php post_favorite('', 'likes', false);?></span><?php } ?>
    
    <?php
}

/**
 * Single Media Audio.
 * 
 * @param string $before
 * @param string $after
 */
function cms_single_audio() {
    global $wp_embed, $foldery_base;
    /* get shortcode audio. */
    $shortcode = $foldery_base->getShortcodeFromContent('audio', get_the_content());
    /* Get remote audio */
    $remote_audio = $foldery_base->getShortcodeFromContent('embed', get_the_content());

     /* Get soundcloud audio */
    $remote_soundcloud = $foldery_base->getShortcodeFromContent('soundcloud', get_the_content());

    if($remote_soundcloud != ''){
        echo '<div class="entry-media entry-audio entry-remote-soundcloud">'.do_shortcode($remote_soundcloud).'</div>';
        return true;
    } elseif ($remote_audio) {
        /* view remote audio. */
        echo '<div class="entry-media entry-audito entry-remote-audio">'.$wp_embed->run_shortcode($remote_audio).'</div>';
        return true;    
    } elseif($shortcode != ''){
        echo '<div class="entry-media entry-audio">'.do_shortcode($shortcode).'</div>';
        return true;
    }  elseif(has_post_thumbnail()){
        echo '<div class="entry-media entry-feature-image">';
        the_post_thumbnail();
        echo '</div>';
    } else {
        the_excerpt();
    }  
}

/**
 * Single Media Video.
 *
 * @param string $before
 * @param string $after
 */
function cms_single_video() {
    
    global $wp_embed, $foldery_base;
    /* Get Local Video */
    $local_video = $foldery_base->getShortcodeFromContent('video', get_the_content());
    
    /* Get Youtube or Vimeo */
    $remote_video = $foldery_base->getShortcodeFromContent('embed', get_the_content());
    
    if($local_video){
        /* view local. */
        echo '<div class="entry-media entry-video entry-local-video">'.do_shortcode($local_video).'</div>';
        return true;
    } elseif ($remote_video) {
        /* view youtube or vimeo. */
        echo '<div class="entry-media entry-video entry-remote-video">'.$wp_embed->run_shortcode($remote_video).'</div>';
        return true;
    } elseif (has_post_thumbnail()) {
        /* view thumbnail. */
        echo '<div class="entry-media entry-feature-image">';
        the_post_thumbnail('blog-grid');
        echo '</div>';
    } else {
    } 
}
/**
 * Single Gallery Images
 * 
 * @author Fox
 * @since 1.0.0
 */
function cms_single_gallery(){
    global $foldery_base;
    /* get shortcode gallery. */
    $shortcode = $foldery_base->getShortcodeFromContent('gallery', get_the_content());
    
    if($shortcode != ''){
        preg_match('/\[gallery.*ids=.(.*).\]/', $shortcode, $ids);
        
        if(!empty($ids)){
        
            $array_id = explode(",", $ids[1]);
            ?>
            <div id="carousel-<?php the_ID();?>" class="carousel slide entry-media entry-gallery" data-ride="carousel">
                <div class="carousel-inner">
                <?php $i = 0; ?>
                <?php foreach ($array_id as $image_id): ?>
                    <?php
                    $attachment_image = wp_get_attachment_image_src($image_id, 'full', false);
                    if($attachment_image[0] != ''):?>
                        <div class="item <?php if( $i == 0 ){ echo 'active'; } ?>">
                            <img style="width:100%;" data-src="holder.js" src="<?php echo esc_url($attachment_image[0]);?>" alt="<?php echo get_the_title();?>" />
                        </div>
                    <?php $i++; endif; ?>
                <?php endforeach; ?>
                </div>
                <a class="left carousel-control" href="#carousel-<?php the_ID();?>" role="button" data-slide="prev">
                    <span class="pe-7s-angle-left"></span>
                </a>
                <a class="right carousel-control" href="#carousel-<?php the_ID();?>" role="button" data-slide="next">
                    <span class="pe-7s-angle-right"></span>
                </a>
            </div>
            <?php
            
            return true;
        
        } else {
            return false;
        }
    } else {
        if(has_post_thumbnail()){
            echo '<div class="entry-media entry-feature-image">';
            the_post_thumbnail();
            echo '</div>';
        } else {
        }
    }
}

/**
 * Portfolio Archive
 * 
 * @author Chinh Duong Manh
 * @since 1.0.0
 */
function cms_portfolio_detail(){
    ?>
    <ul class="list-unstyled list-inline">      
        <li class="detail-terms"><?php the_terms( get_the_ID(), 'portfolio_cat', '' ); ?></li>
    </ul>
    <?php
}
/**
 * Portfolio readmore
 * 
 * @author Chinh Duong Manh
 * @since 1.0.0
 */
function cms_portfolio_readmore(){
    echo '<div class="readmore pull-right"><a class="more-link" href="'.get_the_permalink().'" title="'.get_the_title().'" >'.__('View work  &rarr;', 'foldery').'</a></div>';
}

function cms_portfolio_readmore_overlay(){
    global $myglobal_page_id;
    $pretty_rel_random = ' rel="prettyPhoto[rel-'.$myglobal_page_id.']"';
    echo '<a class="icon circle fa fa-link" href="'.get_the_permalink().'" title="'.get_the_title().'" ></a>';
    if(has_post_thumbnail()){
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
        $image_url = $image[0];
        echo '<a class="icon circle prettyphoto" '.$pretty_rel_random.'  href="'.$image_url.'"><i class="fa fa-search"></i></a>';
    }
}

/**
 * Portfolio Media
 * 
 * @author Chinh Duong Manh
 * @since 1.0.0
 */

function cms_get_shortcode($code,$content) {
    $pattern = get_shortcode_regex();
    preg_match_all('/'.$pattern.'/s',$content,$matches);
    if(is_array($matches) && isset($matches[2]) && in_array($code,$matches[2])) {
		if (count($matches[2]) > 1) {
			// More than one Gallery 
			$indexes = array_keys($matches[2], $code);
			foreach($indexes as $index) {
				$shortcode = $matches[0][$index] . "<hr/>";
				$shortcodes .= (do_shortcode($shortcode));							
			}
			return $shortcodes;
		} else {
			$index = array_search($code,$matches[2]);
			$shortcode = $matches[0][$index];;
			return do_shortcode($shortcode);			
		}
    } else {
        return false;
    }
}

function cms_portfolio_media(){
    global $post;
	//$content = preg_replace('/<img[^>]+./','',$post->post_content,-1,$count);
	$content = $post->post_content;
    $gallery = cms_get_shortcode('gallery',$content); /* Get gallery */
    $playlist  = cms_get_shortcode('playlist',$content); /* Get audio and video playlist */
    $embed  = cms_get_shortcode('embed',$content); /* Get video from URL */
	
   
//	if (has_post_thumbnail()) echo '<div class="entry-media entry-gallery">'.get_the_post_thumbnail(get_the_ID(),'large').'</div>';
//	If they are both img and Gallery
//	if ($count && $gallery) echo "<h3 style='margin:30px 0'>Etapes de travail</h3>";
    if($gallery) echo '<div class="entry-media entry-gallery">'.$gallery.'</div>'; 
    if($playlist) echo '<div class="entry-media entry-playlist">'.$playlist.'</div>';
    if($embed) echo '<div class="entry-media entry-embed">'.$embed.'</div>';
}

/**
 * List socials share for portfolio.
 * 
 * @since 1.0.0
 */
function cms_portfolio_get_socials_share(){
    ?>
    <span class="portfolio-share">
        <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink();?>"><i class="fa fa-facebook"></i></a>
        <a target="_blank" href="https://twitter.com/home?status=<?php esc_html_e('Check out this article', 'foldery');?>:%20<?php the_title();?>%20-%20<?php the_permalink();?>"><i class="fa fa-twitter"></i></a>
        <a target="_blank" href="https://pinterest.com/pin/create/button/?url=<?php echo the_permalink();?>&media=&description=<?php the_title();?>"><i class="fa fa-pinterest"></i></a>
        <a target="_blank" href="https://plus.google.com/share?url=<?php the_permalink();?>"><i class="fa fa-google-plus"></i></a>
        <a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo the_permalink();?>&title=<?php the_title();?>"><i class="fa fa-linkedin"></i></a>
    </span>
    <?php
}

/**
* Display navigation to next/previous portfolio when applicable.
*
* @since 1.0.0
*/
function cms_portfolio_nav() {
	return; // Supprimer la fonction de "Précédent - Suivant"
    global $post;
    // Don't print empty markup if there's nowhere to navigate.
    $previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
    $next     = get_adjacent_post( false, '', false );
    if ( ! $next && ! $previous )
        return;
    ?>
    <nav class="navigation post-navigation portfolio-navigation clearfix" role="navigation">
        <div class="nav-links container clearfix">
            <?php 
                if($previous)
                previous_post_link('<div class="nav-previous">%link</div>', _x( '<i class="pe-7s-angle-left"></i>'.get_the_post_thumbnail($previous->ID, 'thumbnail').'<span class="nav-label-wrap"><span class="nav-label">Preview</span><span class="nav-title h4">'.$previous->post_title, 'foldery' ).'</span></span>' );
                if($next)
                next_post_link('<div class="nav-next">%link</div>', _x( '<span class="nav-label-wrap"><span class="nav-label">Next</span><span class="nav-title h4">'.$next->post_title.'</span></span>', 'foldery').get_the_post_thumbnail($next->ID, 'thumbnail').'<i class="pe-7s-angle-right"></i>');
            ?>
        </div><!-- .nav-links -->
    </nav><!-- .navigation -->
    <?php
}


