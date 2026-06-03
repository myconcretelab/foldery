<?php
/**
 * Foldery: lightweight theme bootstrap for sebastienj.com.
 */

if ( ! defined( 'FOLDERY_VERSION' ) ) {
    define( 'FOLDERY_VERSION', '2.0.13' );
}

if ( ! isset( $content_width ) ) {
    $content_width = 1170;
}

function foldery_upload_size_limit( $size ) {
    return min( $size, 10 * MB_IN_BYTES );
}
add_filter( 'upload_size_limit', 'foldery_upload_size_limit' );

function foldery_register_shared_styles() {
    wp_register_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), '3.3.4' );
    wp_register_style( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', array(), '4.7.0' );
    wp_register_style( 'foldery-style', get_stylesheet_uri(), array( 'bootstrap' ), FOLDERY_VERSION );
    wp_register_style( 'foldery-static', get_template_directory_uri() . '/assets/css/static.css', array( 'foldery-style' ), FOLDERY_VERSION );
    wp_register_style( 'foldery-explorer', get_template_directory_uri() . '/assets/css/foldery-explorer.css', array( 'foldery-static' ), FOLDERY_VERSION );
    wp_register_style( 'foldery-site', get_template_directory_uri() . '/assets/css/foldery-child.css', array( 'foldery-explorer' ), FOLDERY_VERSION );
    wp_register_style( 'foldery-bureau', get_template_directory_uri() . '/assets/css/bureau.css', array( 'foldery-site' ), FOLDERY_VERSION );
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

    return $classes;
}
add_filter( 'body_class', 'foldery_body_classes' );

function foldery_get_site_logo_url() {
    $foldery_logo_url = '';
    $custom_logo_id   = get_theme_mod( 'custom_logo' );

    if ( $custom_logo_id ) {
        $foldery_logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
    }

    if ( ! $foldery_logo_url ) {
        $legacy_options   = get_option( 'smof_data' );
        $foldery_logo_url = $legacy_options['main_logo']['url'] ?? '';
    }

    if ( ! $foldery_logo_url ) {
        $foldery_logo_url = content_url( 'uploads/logo-pt.png' );
    }

    return foldery_make_relative_dev_url( $foldery_logo_url );
}

function foldery_get_primary_menu_args( $menu_class = 'nav-menu menu-main-menu' ) {
    $foldery_menu_args = array(
        'menu_class'  => $menu_class,
        'container'   => false,
        'fallback_cb' => false,
    );

    $foldery_menu_locations = get_nav_menu_locations();

    if ( ! empty( $foldery_menu_locations['primary'] ) ) {
        $foldery_menu_args['theme_location'] = 'primary';
    } else {
        $foldery_menu_args['menu'] = 'Main menu';
    }

    return $foldery_menu_args;
}

function foldery_render_block_list_content( $blocks ) {
    if ( empty( $blocks ) || ! function_exists( 'serialize_blocks' ) ) {
        return '';
    }

    return apply_filters( 'the_content', serialize_blocks( $blocks ) );
}

function foldery_bureau_split_content( $content ) {
    $fallback = array(
        'main'    => apply_filters( 'the_content', $content ),
        'sidebar' => '',
    );

    if ( ! function_exists( 'parse_blocks' ) || ! function_exists( 'serialize_blocks' ) ) {
        return $fallback;
    }

    $blocks = parse_blocks( $content );

    foreach ( $blocks as $index => $block ) {
        if ( 'core/columns' !== ( $block['blockName'] ?? '' ) || empty( $block['innerBlocks'] ) ) {
            continue;
        }

        $columns = array_values(
            array_filter(
                $block['innerBlocks'],
                function ( $inner_block ) {
                    return 'core/column' === ( $inner_block['blockName'] ?? '' );
                }
            )
        );

        if ( count( $columns ) < 2 ) {
            continue;
        }

        $before_blocks  = array_slice( $blocks, 0, $index );
        $after_blocks   = array_slice( $blocks, $index + 1 );
        $main_blocks    = array_merge( $before_blocks, $columns[0]['innerBlocks'], $after_blocks );
        $sidebar_blocks = array();

        foreach ( array_slice( $columns, 1 ) as $sidebar_column ) {
            $sidebar_blocks = array_merge( $sidebar_blocks, $sidebar_column['innerBlocks'] );
        }

        return array(
            'main'    => foldery_render_block_list_content( $main_blocks ),
            'sidebar' => foldery_render_block_list_content( $sidebar_blocks ),
        );
    }

    return $fallback;
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

    wp_enqueue_style( 'font-awesome' );
    wp_enqueue_style( 'foldery-site' );

    if ( is_page_template( 'page-templates/bureau.php' ) ) {
        wp_enqueue_style( 'foldery-bureau' );
    }

    wp_enqueue_script( 'foldery-menu', get_template_directory_uri() . '/assets/js/menu.js', array( 'jquery' ), FOLDERY_VERSION, true );
    wp_enqueue_script( 'foldery-masonry', get_template_directory_uri() . '/js/masonry.pkgd.min.js', array(), '4.2.2', true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'foldery_scripts_styles' );

function foldery_customize_bureau_options( $wp_customize ) {
    $wp_customize->add_section(
        'foldery_bureau',
        array(
            'title'    => __( 'Bureau', 'foldery' ),
            'priority' => 35,
        )
    );

    $wp_customize->add_setting(
        'foldery_bureau_address',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'foldery_bureau_address',
        array(
            'label'   => __( 'Adresse / atelier', 'foldery' ),
            'section' => 'foldery_bureau',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'foldery_bureau_contact',
        array(
            'default'           => get_option( 'admin_email' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'foldery_bureau_contact',
        array(
            'label'   => __( 'Contact affiche', 'foldery' ),
            'section' => 'foldery_bureau',
            'type'    => 'text',
        )
    );
}
add_action( 'customize_register', 'foldery_customize_bureau_options' );

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
