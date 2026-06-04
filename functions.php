<?php
/**
 * Foldery: lightweight theme bootstrap for sebastienj.com.
 */

if ( ! defined( 'FOLDERY_VERSION' ) ) {
    define( 'FOLDERY_VERSION', '3.0.31' );
}

if ( ! isset( $content_width ) ) {
    $content_width = 1024;
}

function foldery_upload_size_limit( $size ) {
    return min( $size, 10 * MB_IN_BYTES );
}
add_filter( 'upload_size_limit', 'foldery_upload_size_limit' );

function foldery_register_shared_styles() {
    wp_register_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), '3.3.4' );
    wp_register_style( 'foldery-style', get_stylesheet_uri(), array( 'bootstrap' ), FOLDERY_VERSION );
    wp_register_style( 'foldery-static', get_template_directory_uri() . '/assets/css/static.css', array( 'foldery-style' ), FOLDERY_VERSION );
    wp_register_style( 'foldery-explorer', get_template_directory_uri() . '/assets/css/foldery-explorer.css', array( 'foldery-static' ), FOLDERY_VERSION );
    wp_register_style( 'foldery-site', get_template_directory_uri() . '/assets/css/foldery-child.css', array( 'foldery-explorer' ), FOLDERY_VERSION );
    wp_register_style( 'foldery-bureau', get_template_directory_uri() . '/assets/css/bureau.css', array( 'foldery-site' ), FOLDERY_VERSION );
    wp_register_style( 'foldery-explorer-editor-style', get_template_directory_uri() . '/assets/css/foldery-explorer-editor.css', array(), FOLDERY_VERSION );
}
add_action( 'init', 'foldery_register_shared_styles', 5 );

require get_template_directory() . '/inc/media-folders/bootstrap.php';
require get_template_directory() . '/inc/theme-settings.php';
require get_template_directory() . '/inc/lightbox/bootstrap.php';
require get_template_directory() . '/inc/site-header-block.php';
require get_template_directory() . '/inc/foldery-explorer-block.php';
require get_template_directory() . '/inc/atelier.php';
require get_template_directory() . '/inc/block-editor.php';

function foldery_setup() {
    load_theme_textdomain( 'foldery', get_template_directory() . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
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
            'assets/css/bureau.css',
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

    if ( foldery_is_bureau_template() ) {
        $classes[] = 'bureau-template';
    }

    if ( foldery_is_atelier_template() ) {
        $classes[] = 'atelier-template';
    }

    return $classes;
}
add_filter( 'body_class', 'foldery_body_classes' );

function foldery_is_bureau_template() {
    if ( ! is_singular( 'page' ) ) {
        return false;
    }

    $template_slug = (string) get_page_template_slug( get_queried_object_id() );

    return false !== strpos( $template_slug, 'bureau' );
}

function foldery_is_atelier_template() {
    if ( ! is_singular( 'page' ) ) {
        return false;
    }

    $template_slug = (string) get_page_template_slug( get_queried_object_id() );

    return false !== strpos( $template_slug, 'atelier' );
}

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

    wp_enqueue_style( 'foldery-site' );
    wp_enqueue_script( 'foldery-header', get_template_directory_uri() . '/assets/js/foldery-header.js', array(), FOLDERY_VERSION, true );

    if ( foldery_is_bureau_template() || foldery_is_atelier_template() ) {
        wp_enqueue_style( 'foldery-bureau' );
    }

    wp_enqueue_script( 'foldery-masonry', get_template_directory_uri() . '/js/masonry.pkgd.min.js', array(), '4.2.2', true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'foldery_scripts_styles' );

function foldery_register_bureau_block_styles() {
    if ( ! function_exists( 'register_block_style' ) ) {
        return;
    }

    register_block_style(
        'core/group',
        array(
            'name'  => 'bureau-paper',
            'label' => __( 'Papier scotche', 'foldery' ),
        )
    );

    register_block_style(
        'core/group',
        array(
            'name'  => 'bureau-note',
            'label' => __( 'Petite note', 'foldery' ),
        )
    );

    register_block_style(
        'core/heading',
        array(
            'name'  => 'bureau-label',
            'label' => __( 'Etiquette scotchee', 'foldery' ),
        )
    );

    register_block_style(
        'core/paragraph',
        array(
            'name'  => 'bureau-typewritten',
            'label' => __( 'Texte de papier', 'foldery' ),
        )
    );

    register_block_style(
        'core/image',
        array(
            'name'  => 'bureau-taped',
            'label' => __( 'Photo scotchee', 'foldery' ),
        )
    );

    register_block_style(
        'core/columns',
        array(
            'name'  => 'bureau-layout',
            'label' => __( 'Bureau deux feuilles', 'foldery' ),
        )
    );
}
add_action( 'init', 'foldery_register_bureau_block_styles' );

function foldery_register_bureau_block_pattern() {
    if ( ! function_exists( 'register_block_pattern' ) || ! function_exists( 'register_block_pattern_category' ) ) {
        return;
    }

    register_block_pattern_category(
        'foldery',
        array(
            'label' => __( 'Foldery', 'foldery' ),
        )
    );

    register_block_pattern(
        'foldery/bureau-page',
        array(
            'title'       => __( 'Bureau - deux feuilles', 'foldery' ),
            'description' => __( 'Structure editable pour le modele Bureau : contenu principal a gauche, contenu lateral a droite.', 'foldery' ),
            'categories'  => array( 'foldery' ),
            'content'     => '<!-- wp:columns {"className":"is-style-bureau-layout"} --><div class="wp-block-columns is-style-bureau-layout"><!-- wp:column {"width":"68%"} --><div class="wp-block-column" style="flex-basis:68%"><!-- wp:heading {"className":"is-style-bureau-label"} --><h2 class="wp-block-heading is-style-bureau-label">Titre principal</h2><!-- /wp:heading --><!-- wp:paragraph {"className":"is-style-bureau-typewritten"} --><p class="is-style-bureau-typewritten">Ajoutez ici le contenu principal de la page.</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column {"width":"32%"} --><div class="wp-block-column" style="flex-basis:32%"><!-- wp:heading {"level":3,"className":"is-style-bureau-label"} --><h3 class="wp-block-heading is-style-bureau-label">Note</h3><!-- /wp:heading --><!-- wp:paragraph {"className":"is-style-bureau-typewritten"} --><p class="is-style-bureau-typewritten">Ajoutez ici la colonne laterale.</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns -->',
        )
    );
}
add_action( 'init', 'foldery_register_bureau_block_pattern' );

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
}
add_action( 'init', 'foldery_register_content_types' );
