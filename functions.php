<?php
/**
 * Foldery: lightweight theme bootstrap for sebastienj.com.
 */

if ( ! defined( 'FOLDERY_VERSION' ) ) {
    define( 'FOLDERY_VERSION', '2.0.3' );
}

if ( ! isset( $content_width ) ) {
    $content_width = 1170;
}

require get_template_directory() . '/inc/media-folders/bootstrap.php';
require get_template_directory() . '/inc/lightbox/bootstrap.php';
require get_template_directory() . '/inc/foldery-shortcodes.php';
require get_template_directory() . '/inc/foldery-explorer-block.php';

function foldery_setup() {
    load_theme_textdomain( 'foldery', get_template_directory() . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'html5', array( 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

    register_nav_menus(
        array(
            'primary' => __( 'Primary Menu', 'foldery' ),
        )
    );
}
add_action( 'after_setup_theme', 'foldery_setup' );

function foldery_body_classes( $classes ) {
    if ( is_front_page() ) {
        $classes[] = 'monaco-home';
    }

    return $classes;
}
add_filter( 'body_class', 'foldery_body_classes' );

function foldery_scripts_styles() {
    wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), '3.3.4' );
    wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', array(), '4.7.0' );
    wp_enqueue_style( 'foldery-style', get_stylesheet_uri(), array( 'bootstrap' ), FOLDERY_VERSION );
    wp_enqueue_style( 'foldery-static', get_template_directory_uri() . '/assets/css/static.css', array( 'foldery-style' ), FOLDERY_VERSION );
    wp_enqueue_style( 'foldery-explorer', get_template_directory_uri() . '/assets/css/foldery-explorer.css', array( 'foldery-static' ), FOLDERY_VERSION );
    wp_enqueue_style( 'foldery-site', get_template_directory_uri() . '/assets/css/foldery-child.css', array( 'foldery-explorer' ), FOLDERY_VERSION );

    wp_enqueue_script( 'foldery-menu', get_template_directory_uri() . '/assets/js/menu.js', array( 'jquery' ), FOLDERY_VERSION, true );
    wp_enqueue_script( 'foldery-masonry', get_template_directory_uri() . '/js/masonry.pkgd.min.js', array(), '4.2.2', true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'foldery_scripts_styles' );

function foldery_admin_styles() {
    wp_enqueue_style( 'foldery-admin', get_template_directory_uri() . '/assets/admin/admin.css', array(), FOLDERY_VERSION );
}
add_action( 'admin_enqueue_scripts', 'foldery_admin_styles' );

function foldery_register_content_types() {
    register_taxonomy(
        'portfolio_cat',
        array( 'page' ),
        array(
            'label'        => __( 'Portfolio Categories', 'foldery' ),
            'public'       => true,
            'hierarchical' => true,
            'show_in_rest' => true,
            'rewrite'      => array( 'slug' => 'portfolio_cat' ),
        )
    );

    register_post_type(
        'serie',
        array(
            'labels'       => array(
                'name'          => __( 'Series', 'foldery' ),
                'singular_name' => __( 'Serie', 'foldery' ),
                'add_new_item'  => __( 'Add Serie', 'foldery' ),
                'edit_item'     => __( 'Edit Serie', 'foldery' ),
                'menu_name'     => __( 'Series', 'foldery' ),
            ),
            'public'       => true,
            'show_in_rest' => true,
            'has_archive'  => false,
            'supports'     => array( 'title', 'editor', 'thumbnail' ),
            'menu_icon'    => 'dashicons-format-gallery',
            'rewrite'      => array( 'slug' => 'serie' ),
        )
    );
}
add_action( 'init', 'foldery_register_content_types' );

function foldery_register_serie_routes() {
    add_rewrite_tag( '%serie_id%', '([^/]+)' );
    add_rewrite_rule( '^([^/]+)/serie/([^/]+)/([^/]+)', 'index.php?serie=$matches[2]&post_type=serie&serie_id=$matches[3]', 'top' );
    add_rewrite_rule( '^serie/([^/]+)/([^/]+)', 'index.php?serie=$matches[1]&post_type=serie&serie_id=$matches[2]', 'top' );
}
add_action( 'init', 'foldery_register_serie_routes' );

function foldery_register_query_vars( $vars ) {
    $vars[] = 'serie_id';
    return $vars;
}
add_filter( 'query_vars', 'foldery_register_query_vars' );

function foldery_serie_404_template( $template ) {
    $serie_id = get_query_var( 'serie_id' );
    if ( ! $serie_id ) {
        return $template;
    }

    global $wp_query;
    $wp_query->is_404 = false;
    status_header( 200 );

    $located = locate_template( 'single-serie.php', false );
    return $located ? $located : $template;
}
add_filter( '404_template', 'foldery_serie_404_template' );

function foldery_serie_document_title( $title ) {
    $serie_id = get_query_var( 'serie_id' );
    if ( ! $serie_id ) {
        return $title;
    }

    $folder = foldery_media_get_folder( $serie_id );
    if ( foldery_is_media_folder( $folder ) ) {
        return sprintf( __( 'Serie %s', 'foldery' ), $folder->getName() );
    }

    return $title;
}
add_filter( 'pre_get_document_title', 'foldery_serie_document_title' );

function foldery_get_field( $key, $post_id = null, $format_value = true ) {
    if ( function_exists( 'get_field' ) ) {
        return get_field( $key, $post_id, $format_value );
    }

    return get_post_meta( $post_id ? $post_id : get_the_ID(), $key, true );
}

function foldery_attachment_field( $key, $post_id = null, $format_value = true ) {
    return foldery_get_field( $key, $post_id, $format_value );
}
