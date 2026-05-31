<?php
/**
 * Twenty Twelve functions and definitions
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, @link http://codex.wordpress.org/Plugin_API
 *
 * @package ZookaStudio
 * @subpackage Monaco
 * @since 1.0.0
 */

/**
 * Add global values.
 */
global $smof_data, $cms_meta, $cms_base;

define('THEMENAME', 'foldery');
if ( ! isset( $content_width ) ) $content_width = 1170;
/* Add base functions */
require( get_template_directory() . '/inc/base.class.php' );
require( get_template_directory() . '/inc/foldery-compat.php' );
foldery_load_theme_options();
/* Install Sample Data */
//require( get_template_directory() . '/inc/demo-data.php' );

if(class_exists("CMS_Base")){
    $cms_base = new CMS_Base();
}

/* Add theme options when Redux is available; front-end options are loaded locally above. */
if ( class_exists( 'ReduxFramework' ) ) {
    require( get_template_directory() . '/inc/options/functions.php' );
}

    
/* Add theme elements */
add_action('vc_before_init', 'cms_vc_elements');
function cms_vc_elements(){
    if(class_exists('CmsShortCode')){
        $element = get_template_directory() . '/inc/elements/googlemap';
        require( $element . '/cms_googlemap.php' );
    }
}

add_action('vc_before_init', 'cms_vc_params');
function cms_vc_params() {
    require( get_template_directory() . '/vc_params/vc_customs.php' );
    require( get_template_directory() . '/vc_params/vc_btn.php' );
    require( get_template_directory() . '/vc_params/vc_icon.php' );
    require( get_template_directory() . '/vc_params/vc_gallery.php' );
}

/* Add Meta Core Options */
if(is_admin()){
    
    if(!class_exists('CsCoreControl')){
        /* add mete core */
        require( get_template_directory() . '/inc/metacore/core.options.php' );
        /* add meta options */
        require( get_template_directory() . '/inc/options/meta.options.php' );
    }
    
}

/* Add Template functions */
require( get_template_directory() . '/inc/template.functions.php' );

/* Static css. */
require( get_template_directory() . '/inc/dynamic/static.css.php' );

/* Dynamic css*/
require( get_template_directory() . '/inc/dynamic/dynamic.css.php' );

/* Add mega menu */
if(!class_exists('HeroMenuWalker')){
    // require( get_template_directory() . '/inc/megamenu/mega-menu.php' );
}

/* Add widgets */
// require( get_template_directory() . '/inc/widgets/cms_social.php' );
// require( get_template_directory() . '/inc/widgets/cms_instagram.php' );

/* Add tinymce */
// require( get_template_directory() . '/inc/tinymce/button.php' );

/* load template functions : Post Favorite */
// require_once( get_template_directory() . '/inc/post_favorite.php' );

/* Woo commerce function */
if(class_exists('WooCommerce')){
	/* Add widgets */
//	require( get_template_directory() . '/inc/widgets/cart_search.php' );
	/* Custom WooCommerce Hook  */
 //   require get_template_directory() . '/woocommerce/wc-template-hooks.php';
}
/**
 * Change default woocommerce thumbnails size
 * This action need to do when active Woo, so it can not add in if(class_exists('WooCommerce'))
 * @since 1.0.3
 * @author Chinh Duong Manh
 */

//add_action('init', 'cms_change_default_woo_thumb_size');
function cms_change_default_woo_thumb_size(){
	register_activation_hook('woocommerce/woocommerce.php', 'cms_woocommerce_image_dimensions');
}
/*
function cms_woocommerce_image_dimensions() {
    global $pagenow;
 
    $catalog = array(
        'width'     => '270',   // px
        'height'    => '320',   // px
        'crop'      => 1        // true
    );
    $single = array(
        'width'     => '450',   // px
        'height'    => '533',   // px
        'crop'      => 1        // true
    );
    $thumbnail = array(
        'width'     => '100',   // px
        'height'    => '120',   // px
        'crop'      => 1        // true
    );

    update_option( 'shop_catalog_image_size', $catalog );       
    update_option( 'shop_single_image_size', $single );       
    update_option( 'shop_thumbnail_image_size', $thumbnail );  
}
*/
/**
 * CMS Theme setup.
 *
 * Sets up theme defaults and registers the various WordPress features that
 * CMS Theme supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add a Visual Editor stylesheet.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
 * 	custom background, and post formats.
 * @uses register_nav_menu() To add support for navigation menus.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since 1.0.0
 */
function cms_setup() {
	/*
	 * Makes Twenty Twelve available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Twenty Twelve, use a find and replace
	 * to change 'foldery' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'foldery' , get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	//add_editor_style();

	// Adds title tag
	//add_theme_support( "title-tag" );
	
	// Add woocommerce
	//add_theme_support('woocommerce');
	
	// Adds custom header
	add_theme_support( 'custom-header' );
	
	// Adds RSS feed links to <head> for posts and comments.
	//add_theme_support( 'automatic-feed-links' );

	// This theme supports a variety of post formats.
	//add_theme_support( 'post-formats', array( 'video', 'audio' , 'gallery', 'quote',) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', esc_html__( 'Primary Menu', 'foldery' ) );
	register_nav_menu( 'leftmenu', esc_html__( 'Left Menu', 'foldery' ) );
	register_nav_menu( 'rightmenu', esc_html__( 'Right Menu', 'foldery' ) );

	/*
	 * This theme supports custom background color and image,
	 * and here we also set up the default background color.
	 */
	add_theme_support( 'custom-background', array(
		'default-color' => 'ffffff',
	) );

	/* Change default image thumbnail sizes in wordpress */
	/* Disable by SebastienJ
    update_option('large_size_w', 770);
    update_option('large_size_h', 458);
    update_option('large_crop', 1);
    update_option('medium_large_size_w', 570);
    update_option('medium_large_size_h', 385);
    update_option('medium_large_crop', 1); 
    update_option('medium_size_w', 370);
    update_option('medium_size_h', 250);
    update_option('medium_crop', 1); 
    update_option('thumbnail_size_w', 140);
    update_option('thumbnail_size_h', 180);
    update_option('thumbnail_crop', 1); 
	
	
    update_option('medium_large_crop', 0); 
    update_option('large_crop', 0);
    update_option('medium_crop', 0); 
    
	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'blog-grid', 1170, 790, true );
	add_image_size( 'blog-masonry', 835 );
	add_image_size( 'blog-masonry4', 390, 528, true );

	add_image_size( 'monaco-team', 300, 300, true );
	*/
}

add_action( 'after_setup_theme', 'cms_setup' );

/**
 * Get meta data.
 * @author Fox
 * @return mixed|NULL
 */
function cms_meta_data(){
    global $post, $cms_meta;
    if(isset($post->ID)){
        $cms_meta = json_decode(get_post_meta($post->ID, '_cms_meta_data', true));
        if(!empty($cms_meta)){
		    foreach ($cms_meta as $key => $meta){
		        $cms_meta->$key = rawurldecode($meta);
		    }
		}
    } else {
        $cms_meta = null;
    }

}
//add_action('wp', 'cms_meta_data');

/**
 * Get post meta data.
 * @author Fox
 * @return mixed|NULL
 */
function cms_post_meta_data(){
    global $post;
    if(isset($post->ID)){
		$cms_meta = json_decode(get_post_meta($post->ID, '_cms_meta_data', true));
        if(!empty($cms_meta)){
		    foreach ($cms_meta as $key => $meta){
		        $cms_meta->$key = rawurldecode($meta);
		    }
		}
		return $cms_meta;
    } else {
        return null;
    }
}

/**
 * Enqueue scripts and styles for front-end.
 * @author Fox
 * @since Monaco 1.0
 */
function cms_scripts_styles() {
    
	global $smof_data, $wp_styles, $cms_meta;
	
	/** theme options. */
	$script_options = array(
/*		'header_type'=> $smof_data['header_fixed'],
	    'menu_sticky'=> $smof_data['menu_sticky'],
	    'menu_sticky_tablets'=> $smof_data['menu_sticky_tablets'],
	    'menu_sticky_mobile'=> $smof_data['menu_sticky_mobile'],
	    'paralax' => 1,
	    'back_to_top'=> $smof_data['footer_botton_back_to_top']*/
	);

	/*------------------------------------- JavaScript ---------------------------------------*/
	
	
	/** --------------------------libs--------------------------------- */
	
	
	/* Adds JavaScript Bootstrap. */
	wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array( 'jquery' ), '3.3.2');
	
	
	/* Add smoothscroll plugin */
	if($smof_data['smoothscroll']){
	  // wp_enqueue_script('smoothscroll', get_template_directory_uri() . '/assets/js/smoothscroll.min.js', array( 'jquery' ), '1.0.0', true);
	}
	
	
	/** --------------------------custom------------------------------- */
	
	/* Add main.js */
	wp_register_script('cmssuperheroes-main', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), '1.0.0', true);
	wp_localize_script('cmssuperheroes-main', 'CMSOptions', $script_options);
	wp_enqueue_script('cmssuperheroes-main');
	/* Add menu.js */
    wp_enqueue_script('cmssuperheroes-menu', get_template_directory_uri() . '/assets/js/menu.js', array( 'jquery' ), '1.0.0', true);
	/*
	 * Adds JavaScript to pages with the comment form to support
	 * sites with threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

    /*------------------------------------- Stylesheet ---------------------------------------*/
	
	/** --------------------------libs--------------------------------- */
	
	/* Loads Bootstrap stylesheet. */
	wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), '3.3.4');
	
	/* Loads Bootstrap stylesheet. */
	wp_deregister_style( 'font-awesome' ); /* Remove font-awesome from 3rd extension */
	wp_enqueue_style('font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', array(), '4.7.0');

	/* Loads Font Ionicons. */
	wp_enqueue_style('font-ionicons', get_template_directory_uri() . '/assets/css/ionicons.min.css', array(), '2.0.1');

	/* Loads Pe Icon. */
	wp_enqueue_style('cms-icon-pe7stroke', get_template_directory_uri() . '/assets/css/pe-icon-7-stroke.css', array(), '1.0.1');
	
	/** --------------------------custom------------------------------- */
	
	/* Loads our main stylesheet. */
	wp_enqueue_style( 'monaco-style', get_stylesheet_uri(), array( 'bootstrap' ));

	/* Loads the Internet Explorer specific stylesheet. */
	wp_enqueue_style( 'foldery-ie', get_template_directory_uri() . '/assets/css/ie.css', array( 'monaco-style' ), '1.6.0' );
	$wp_styles->add_data( 'foldery-ie', 'conditional', 'lt IE 11' );
	
	/* WooCommerce */
	if(class_exists('WooCommerce')){
	    wp_enqueue_style( 'monaco-woo', get_template_directory_uri() . "/assets/css/woocommerce.css", array(), '1.6.0');
	}
	
	/* Load static css*/
	wp_enqueue_style('monaco-static', get_template_directory_uri() . '/assets/css/static.css', array( 'monaco-style' ), '2.0.0');

	/* Load PrettyPhoto*/
/*	wp_enqueue_script('prettyphoto');
    wp_enqueue_style('prettyphoto');
*/}

add_action( 'wp_enqueue_scripts', 'cms_scripts_styles' );

/**
 * Register sidebars.
 *
 * Registers our main widget area and the front page widget areas.
 *
 * @since Fox
 */
function cms_widgets_init() {
	register_sidebar( array(
		'name' => esc_html__( 'Main Sidebar', 'foldery' ),
		'id' => 'sidebar-1',
		'description' => esc_html__( 'Appears on posts and pages except the optional Front Page template, which has its own widgets', 'foldery' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="wg-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
    	'name' => esc_html__( 'Slider before Header', 'foldery' ),
    	'id' => 'sidebar-11',
    	'description' => esc_html__( 'You can use this are to add revelution slider or anything else. It will appears at the top of page', 'foldery' ),
    	'before_widget' => '<section id="cms-showcase" class="cms-showcase" role="complementary">',
    	'after_widget' => '</section>',
    	'before_title' => '<h3 class="wg-title">',
    	'after_title' => '</h3>',
	) );
	register_sidebar( array(
    	'name' => esc_html__( 'Top Header', 'foldery' ),
    	'id' => 'sidebar-12',
    	'description' => esc_html__( 'It will appears at the top of Header', 'foldery' ),
    	'before_widget' => '<div id="%1$s" class="cms-topheader">',
    	'after_widget' => '</div><!-- .cms-topheader -->',
    	'before_title' => '<h3 class="wg-title">',
    	'after_title' => '</h3>',
	) );
	register_sidebar( array(
    	'name' => esc_html__( 'Header Widget', 'foldery' ),
    	'id' => 'sidebar-8',
    	'description' => esc_html__( 'Appears in header, beside menu', 'foldery' ),
    	'before_widget' => '',
    	'after_widget' => '',
    	'before_title' => '',
    	'after_title' => '',
	) );

	register_sidebar( array(
    	'name' => esc_html__( 'Footer Top 1', 'foldery' ),
    	'id' => 'sidebar-2',
    	'description' => esc_html__( 'Appears when using the optional Footer with a page set as Footer Top 1', 'foldery' ),
    	'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    	'after_widget' => '</aside>',
    	'before_title' => '<h3 class="wg-title">',
    	'after_title' => '</h3>',
	) );
	
	register_sidebar( array(
    	'name' => esc_html__( 'Footer Top 2', 'foldery' ),
    	'id' => 'sidebar-3',
    	'description' => esc_html__( 'Appears when using the optional Footer with a page set as Footer Top 2', 'foldery' ),
    	'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    	'after_widget' => '</aside>',
    	'before_title' => '<h3 class="wg-title">',
    	'after_title' => '</h3>',
	) );
	
	register_sidebar( array(
    	'name' => esc_html__( 'Footer Top 3', 'foldery' ),
    	'id' => 'sidebar-4',
    	'description' => esc_html__( 'Appears when using the optional Footer with a page set as Footer Top 3', 'foldery' ),
    	'before_widget' => '<aside class="widget %2$s">',
    	'after_widget' => '</aside>',
    	'before_title' => '<h3 class="wg-title">',
    	'after_title' => '</h3>',
	) );
		
	register_sidebar( array(
    	'name' => esc_html__( 'Footer Bottom 1', 'foldery' ),
    	'id' => 'sidebar-5',
    	'description' => esc_html__( 'Appears when using the optional Footer Bottom with a page set as Footer Bottom 1', 'foldery' ),
    	'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    	'after_widget' => '</aside>',
    	'before_title' => '<h3 class="wg-title">',
    	'after_title' => '</h3>',
	) );
	
	register_sidebar( array(
    	'name' => esc_html__( 'Footer Bottom 2', 'foldery' ),
    	'id' => 'sidebar-6',
    	'description' => esc_html__( 'Appears when using the optional Footer Bottom with a page set as Footer Bottom 2', 'foldery' ),
    	'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    	'after_widget' => '</aside>',
    	'before_title' => '<h3 class="wg-title">',
    	'after_title' => '</h3>',
	) );
	register_sidebar( array(
    	'name' => esc_html__( 'Footer Bottom 3', 'foldery' ),
    	'id' => 'sidebar-7',
    	'description' => esc_html__( 'Appears when using the optional Footer Bottom with a page set as Footer Bottom 3', 'foldery' ),
    	'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    	'after_widget' => '</aside>',
    	'before_title' => '<h3 class="wg-title">',
    	'after_title' => '</h3>',
	) );
	if(class_exists('woocommerce')){
		register_sidebar( array(
	    	'name' => esc_html__( 'WooCommerce Sidebar', 'foldery' ),
	    	'id' => 'sidebar-9',
	    	'description' => esc_html__( 'Appears on WooCommerce page', 'foldery' ),
	    	'before_widget' => '<aside id="%1$s" class="widget %2$s">',
	    	'after_widget' => '</aside>',
	    	'before_title' => '<h3 class="wg-title">',
	    	'after_title' => '</h3>',
		) );
	}
	if(class_exists('newsletter')){
		register_sidebar( array(
			'name' => esc_html__( 'Newsletter in Page', 'foldery' ),
			'id' => 'sidebar-10',
			'description' => esc_html__( 'Appears on Page when use VC Widgetised Sidebar and call to this own', 'foldery' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
	}
}
//add_action( 'widgets_init', 'cms_widgets_init' );

/**
 * Filter the page menu arguments.
 *
 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
 *
 * @since 1.0.0
 */
function cms_page_menu_args( $args ) {
    if ( ! isset( $args['show_home'] ) )
        $args['show_home'] = true;
    return $args;
}
add_filter( 'wp_page_menu_args', 'cms_page_menu_args' );

/**
 * Save custom theme meta. 
 * 
 * @since 1.0.0
 */
function cms_save_meta_boxes($post_id) {
    
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    /* update field subtitle */
    if(isset($_POST['post_subtitle'])){
        update_post_meta($post_id, 'post_subtitle', $_POST['post_subtitle']);
    }
}

add_action('save_post', 'cms_save_meta_boxes');

/**
 * Display navigation to next/previous comments when applicable.
 *
 * @since 1.0.0
 */
function cms_comment_nav() {
    // Are there comments to navigate through?
    if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
    ?>
	<nav class="navigation comment-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'foldery' ); ?></h2>
		<div class="nav-links">
			<?php
				if ( $prev_link = get_previous_comments_link( esc_html__( 'Older Comments', 'foldery' ) ) ) :
					printf( '<div class="nav-previous">%s</div>', $prev_link );
				endif;

				if ( $next_link = get_next_comments_link( esc_html__( 'Newer Comments', 'foldery' ) ) ) :
					printf( '<div class="nav-next">%s</div>', $next_link );
				endif;
			?>
		</div><!-- .nav-links -->
	</nav><!-- .comment-navigation -->
	<?php
	endif;
}

/**
 * Incudes file
 *
 * @package ZookaStudio
 * @subpackage Monaco
 * @since 2.0.0
 * @author Chinh Duong Manh
 *
*/
function monaco_require_folder($foldername,$path)
{
    $dir = $path . DIRECTORY_SEPARATOR . $foldername;
    if (!is_dir($dir)) {
        return;
    }
    $files = array_diff(scandir($dir), array('..', '.'));
    foreach ($files as $file) {
        $patch = $dir . DIRECTORY_SEPARATOR . $file;
        if (file_exists($patch) && strpos($file, ".php") !== false) {
            require_once $patch;
        }
    }
}


/**
 * Enqueue scripts and styles for front-end.
 *
 * @package ZookaStudio
 * @subpackage Monaco
 * @since 2.0
 * @author Chinh Duong Manh
 *
 */
function monaco_front_end_scripts()
{
    global $wp_styles;
    $themeframe_ver = wp_get_theme()->get('Version');
    /* Add main.js */
    $min = '';
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        $min = '.min';
    }
    /* VC Script */
    if (class_exists('VC_Manager')) {
        wp_register_script( 'vc_grid', get_template_directory_uri() . '/assets/2.0/js/vc/vc_grid.min.js', array(
            'jquery',
            'underscore',
            'vc_pageable_owl-carousel',
            'waypoints',
            //'isotope',
            'vc_grid-js-imagesloaded',
        ), WPB_VC_VERSION, true );
        wp_register_script( 'vc_pageable_owl-carousel', vc_asset_url( 'lib/owl-carousel2-dist/owl.carousel.min.js' ), array(
            'jquery',
        ), WPB_VC_VERSION, true );
        wp_register_style( 'vc_pageable_owl-carousel-css', vc_asset_url( 'lib/owl-carousel2-dist/assets/owl.min.css' ), array(), WPB_VC_VERSION );
        wp_register_script('zk-owlcarousel', get_template_directory_uri() . '/assets/2.0/js/zk-owlcarousel.js', array('jquery'), $themeframe_ver, true);
        wp_register_style( 'animate-css', vc_asset_url( 'lib/bower/animate-css/animate.min.css' ), '', WPB_VC_VERSION );
        /* Masonry */
        wp_register_script( 'vc_masonry', vc_asset_url( 'lib/bower/masonry/dist/masonry.pkgd.min.js'), '', WPB_VC_VERSION, true );
        
    } else {
        /* Masonry */
        wp_register_script( 'vc_masonry', get_template_directory_uri() . '/assets/2.0/js/vc/masonry.pkgd.min.js', '', $themeframe_ver, true );
        /* Animate CSS */
        wp_register_style( 'animate-css', get_template_directory_uri() . '/assets/2.0/css/animate.min.css', '', $themeframe_ver, true );
    }
}

//add_action('wp_enqueue_scripts', 'monaco_front_end_scripts');

/**
 * New VC Element
 *
 * @package ZookaStudio
 * @subpackage Monaco
 * @since 2.0
 * @author Chinh Duong Manh
 *
*/
/*
  VC Custom 
*/
monaco_require_folder('vc_customs',get_template_directory());

/**
 * Add new elements for VC
*/
add_action('vc_before_init', 'monaco_new_vc_elements');
function monaco_new_vc_elements()
{
    if (class_exists('CmsShortCode')) {
        monaco_require_folder('vc_elements', get_template_directory());
    }
}


/**
 * Add custom post type and taxonimies
 * Move to theme 
 * remove required CPT UI plugins
*/
/**
 * Add Admin style
*/
add_action('admin_enqueue_scripts',function(){
	wp_enqueue_style('capitol', get_template_directory_uri() . '/assets/admin/admin.css', array(), '2.5');
});
/**
 * Support GutenBerg
 * @since 2.2
*/
add_filter('ef3_support_gtb', function (){ 
	global $smof_data;
	$ef3_support_gtb = isset($smof_data['gutenberg']) ? (bool) $smof_data['gutenberg'] : true;
	if(class_exists('Classic_Editor'))
		return false;
	else
		return $ef3_support_gtb; // theme support or not
} );

require( get_template_directory() . '/inc/foldery-legacy.php' );
