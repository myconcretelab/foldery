<?php
/**
 * Local compatibility layer for the former Monaco child setup.
 */

if ( ! defined( 'CMS_IMAGES' ) ) {
    define( 'CMS_IMAGES', get_template_directory_uri() . '/assets/images/' );
}
if ( ! defined( 'CMS_JS' ) ) {
    define( 'CMS_JS', get_template_directory_uri() . '/assets/js/' );
}
if ( ! defined( 'CMS_CSS' ) ) {
    define( 'CMS_CSS', get_template_directory_uri() . '/assets/css/' );
}

if ( ! function_exists( 'cms_filter_remove' ) ) {
    function cms_filter_remove( $tag, $function_to_remove, $priority = 10 ) {
        return remove_filter( $tag, $function_to_remove, $priority );
    }
}

if ( ! function_exists( 'cms_widget_register' ) ) {
    function cms_widget_register( $widget_class ) {
        return register_widget( $widget_class );
    }
}

if ( ! function_exists( 'base64_ef3_encode' ) ) {
    function base64_ef3_encode( $data ) {
        return base64_encode( $data );
    }
}

if ( ! function_exists( 'base64_ef3_decode' ) ) {
    function base64_ef3_decode( $data ) {
        return base64_decode( $data );
    }
}

function foldery_theme_defaults() {
    return array(
        'body_layout' => '0',
        'smoothscroll' => '',
        'header_layout' => 'v1',
        'header_fixed' => '',
        'header_fullwidth' => '',
        'header_width' => '260px',
        'header_position' => 'left',
        'header_widget' => '1',
        'header_widget_search' => '1',
        'header_main_logo' => array( 'url' => '' ),
        'main_logo' => array( 'url' => get_template_directory_uri() . '/assets/images/logo.png' ),
        'main_logo_height' => '98px',
        'page_title_layout' => '',
        'page_title_fullwidth' => '1',
        'page_comment' => '',
        'blog_nav' => '1',
        'meta_post_date' => '1',
        'meta_post_author' => '1',
        'meta_post_category' => '1',
        'meta_post_comment' => '1',
        'meta_post_like' => '1',
        'footer_bottom_layout' => '8',
        'footer_copyright' => '',
        'footer_icon' => '0',
        'footer_botton_back_to_top' => '1',
        'enable_shop_page_title' => '',
        'single_portfolio_layout' => 'standard',
        'single_portfolio_related' => false,
        'gutenberg' => '0',
    );
}

function foldery_load_theme_options() {
    global $smof_data;

    $saved = get_option( 'smof_data', array() );
    if ( ! is_array( $saved ) ) {
        $saved = array();
    }

    $smof_data = wp_parse_args( $saved, foldery_theme_defaults() );
}

function foldery_register_content_types() {
    register_taxonomy(
        'portfolio_cat',
        array( 'portfolio', 'page' ),
        array(
            'label' => __( 'Portfolio Categories', 'foldery' ),
            'public' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'portfolio_cat', 'with_front' => true ),
            'show_in_rest' => false,
        )
    );

    register_taxonomy(
        'team_cat',
        array( 'team' ),
        array(
            'label' => __( 'Team Categories', 'foldery' ),
            'public' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'team_cat', 'with_front' => true ),
            'show_in_rest' => false,
        )
    );

    register_post_type(
        'portfolio',
        array(
            'label' => __( 'Portfolio', 'foldery' ),
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => false,
            'has_archive' => false,
            'hierarchical' => true,
            'rewrite' => array( 'slug' => 'portfolio', 'with_front' => true ),
            'supports' => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies' => array( 'portfolio_cat' ),
        )
    );

    register_post_type(
        'team',
        array(
            'label' => __( 'Team', 'foldery' ),
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => false,
            'has_archive' => false,
            'hierarchical' => true,
            'rewrite' => array( 'slug' => 'team', 'with_front' => true ),
            'supports' => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies' => array( 'team_cat' ),
        )
    );

    register_post_type(
        'client',
        array(
            'label' => __( 'Clients', 'foldery' ),
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => false,
            'has_archive' => false,
            'hierarchical' => true,
            'rewrite' => array( 'slug' => 'client', 'with_front' => true ),
            'supports' => array( 'title', 'editor', 'thumbnail' ),
        )
    );

    register_post_type(
        'testimonial',
        array(
            'label' => __( 'Testimonials', 'foldery' ),
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => false,
            'has_archive' => false,
            'hierarchical' => true,
            'rewrite' => array( 'slug' => 'testimonial', 'with_front' => true ),
            'supports' => array( 'title', 'editor', 'thumbnail' ),
        )
    );
}
add_action( 'init', 'foldery_register_content_types', 0 );
