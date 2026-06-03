<?php
/**
 * Foldery: lightweight theme bootstrap for sebastienj.com.
 */

if ( ! defined( 'FOLDERY_VERSION' ) ) {
    define( 'FOLDERY_VERSION', '2.0.12' );
}

if ( ! isset( $content_width ) ) {
    $content_width = 1170;
}

function foldery_register_shared_styles() {
    wp_register_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), '3.3.4' );
    wp_register_style( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', array(), '4.7.0' );
    wp_register_style( 'foldery-style', get_stylesheet_uri(), array( 'bootstrap' ), FOLDERY_VERSION );
    wp_register_style( 'foldery-static', get_template_directory_uri() . '/assets/css/static.css', array( 'foldery-style' ), FOLDERY_VERSION );
    wp_register_style( 'foldery-explorer', get_template_directory_uri() . '/assets/css/foldery-explorer.css', array( 'foldery-static' ), FOLDERY_VERSION );
    wp_register_style( 'foldery-site', get_template_directory_uri() . '/assets/css/foldery-child.css', array( 'foldery-explorer' ), FOLDERY_VERSION );
    wp_register_style( 'foldery-explorer-editor-style', get_template_directory_uri() . '/assets/css/foldery-explorer-editor.css', array(), FOLDERY_VERSION );
}
add_action( 'init', 'foldery_register_shared_styles', 5 );

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
    add_editor_style(
        array(
            'assets/css/bootstrap.min.css',
            'assets/css/static.css',
            'assets/css/foldery-explorer.css',
            'assets/css/foldery-child.css',
            'assets/css/foldery-explorer-editor.css',
        )
    );

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

function foldery_should_use_relative_dev_urls() {
    if ( ! is_admin() ) {
        return true;
    }

    if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'foldery_explorer' === $_REQUEST['action'] ) {
        return true;
    }

    return false;
}

function foldery_make_relative_dev_url( $url ) {
    if ( ! foldery_should_use_relative_dev_urls() || ! is_string( $url ) || '' === $url ) {
        return $url;
    }

    $parts = wp_parse_url( $url );
    if ( empty( $parts['host'] ) ) {
        return $url;
    }

    $home = wp_parse_url( get_option( 'home' ) );
    if ( empty( $home['host'] ) || $parts['host'] !== $home['host'] ) {
        return $url;
    }

    $url_port  = isset( $parts['port'] ) ? (int) $parts['port'] : null;
    $home_port = isset( $home['port'] ) ? (int) $home['port'] : null;
    if ( $url_port !== $home_port ) {
        return $url;
    }

    $relative = isset( $parts['path'] ) ? $parts['path'] : '/';
    if ( isset( $parts['query'] ) ) {
        $relative .= '?' . $parts['query'];
    }
    if ( isset( $parts['fragment'] ) ) {
        $relative .= '#' . $parts['fragment'];
    }

    return $relative;
}

function foldery_make_relative_dev_srcset( $sources ) {
    if ( ! is_array( $sources ) ) {
        return $sources;
    }

    foreach ( $sources as $width => $source ) {
        if ( isset( $source['url'] ) ) {
            $sources[ $width ]['url'] = foldery_make_relative_dev_url( $source['url'] );
        }
    }

    return $sources;
}

function foldery_make_relative_dev_image_attrs( $attr ) {
    foreach ( array( 'src', 'data-src' ) as $key ) {
        if ( isset( $attr[ $key ] ) ) {
            $attr[ $key ] = foldery_make_relative_dev_url( $attr[ $key ] );
        }
    }

    return $attr;
}

function foldery_make_relative_dev_menu_items( $items ) {
    foreach ( $items as $item ) {
        if ( isset( $item->url ) ) {
            $item->url = foldery_make_relative_dev_url( $item->url );
        }
    }

    return $items;
}

add_filter( 'template_directory_uri', 'foldery_make_relative_dev_url' );
add_filter( 'stylesheet_directory_uri', 'foldery_make_relative_dev_url' );
add_filter( 'theme_file_uri', 'foldery_make_relative_dev_url' );
add_filter( 'style_loader_src', 'foldery_make_relative_dev_url' );
add_filter( 'script_loader_src', 'foldery_make_relative_dev_url' );
add_filter( 'wp_get_attachment_url', 'foldery_make_relative_dev_url' );
add_filter( 'content_url', 'foldery_make_relative_dev_url' );
add_filter( 'wp_calculate_image_srcset', 'foldery_make_relative_dev_srcset' );
add_filter( 'wp_get_attachment_image_attributes', 'foldery_make_relative_dev_image_attrs' );
add_filter( 'wp_nav_menu_objects', 'foldery_make_relative_dev_menu_items' );

function foldery_is_local_dev_host() {
    $home = wp_parse_url( get_option( 'home' ) );
    return ! empty( $home['host'] ) && in_array( $home['host'], array( '127.0.0.1', 'localhost' ), true );
}

function foldery_force_local_admin_http( $url ) {
    if ( ! foldery_is_local_dev_host() || ! is_string( $url ) || '' === $url ) {
        return $url;
    }

    $parts = wp_parse_url( $url );
    if ( empty( $parts['host'] ) || ! in_array( $parts['host'], array( '127.0.0.1', 'localhost' ), true ) ) {
        return $url;
    }

    $path = isset( $parts['path'] ) ? $parts['path'] : '/';
    if ( isset( $parts['query'] ) ) {
        $path .= '?' . $parts['query'];
    }
    if ( isset( $parts['fragment'] ) ) {
        $path .= '#' . $parts['fragment'];
    }

    $port = isset( $parts['port'] ) ? ':' . (int) $parts['port'] : '';
    return 'http://' . $parts['host'] . $port . $path;
}
add_filter( 'admin_url', 'foldery_force_local_admin_http' );
add_filter( 'login_url', 'foldery_force_local_admin_http' );

function foldery_redirect_local_https_admin_to_http() {
    if ( ! foldery_is_local_dev_host() || ! is_ssl() || headers_sent() ) {
        return;
    }

    $host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
    $uri  = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';

    if ( $host && 0 === strpos( $uri, '/wp-admin/' ) ) {
        wp_safe_redirect( 'http://' . $host . $uri );
        exit;
    }
}
add_action( 'admin_init', 'foldery_redirect_local_https_admin_to_http', 1 );

remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

function foldery_scripts_styles() {
    foldery_register_shared_styles();

    wp_enqueue_style( 'font-awesome' );
    wp_enqueue_style( 'foldery-site' );

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
