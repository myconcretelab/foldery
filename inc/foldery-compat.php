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

if ( ! function_exists( 'cms_allowed_html' ) ) {
    function cms_allowed_html( $html, $echo = true ) {
        if ( $echo ) {
            echo $html;
            return null;
        }

        return $html;
    }
}

if ( ! function_exists( 'cmsGetCategoriesByPostID' ) ) {
    function cmsGetCategoriesByPostID( $post_ID = null, $taxo = 'category' ) {
        $term_cats = array();
        $categories = get_the_terms( $post_ID, $taxo );

        if ( $categories ) {
            foreach ( $categories as $category ) {
                $term_cats[] = get_term( $category, $taxo );
            }
        }

        return $term_cats;
    }
}

if ( ! function_exists( 'cmsHtmlID' ) ) {
    function cmsHtmlID( $id ) {
        static $ids = array();

        if ( empty( $ids[ $id ] ) ) {
            $ids[ $id ] = 0;
        }

        $ids[ $id ]++;

        return $id . '-' . $ids[ $id ];
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

function foldery_parse_vc_source( $source ) {
    if ( function_exists( 'vc_build_loop_query' ) ) {
        list( $args, $query ) = vc_build_loop_query( $source );
        return array( $args, $query );
    }

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 10,
        'orderby' => 'date',
        'order' => 'DESC',
    );

    foreach ( explode( '|', (string) $source ) as $part ) {
        $pair = explode( ':', $part, 2 );
        if ( count( $pair ) !== 2 ) {
            continue;
        }

        list( $key, $value ) = $pair;
        if ( $key === 'size' ) {
            $args['posts_per_page'] = (int) $value;
        } elseif ( $key === 'order_by' ) {
            $args['orderby'] = sanitize_key( $value );
        } elseif ( $key === 'post_type' ) {
            $args['post_type'] = sanitize_key( $value );
        } elseif ( $key === 'tax_query' ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'portfolio_cat',
                    'field' => 'term_id',
                    'terms' => array_map( 'absint', explode( ',', $value ) ),
                ),
            );
            $args['post_type'] = array( 'portfolio', 'page', 'post', 'attachment' );
        }
    }

    return array( $args, new WP_Query( $args ) );
}

function foldery_shortcode_cms_grid( $atts = array(), $content = null ) {
    $atts = shortcode_atts(
        array(
            'source' => '',
            'col_lg' => 4,
            'col_md' => 3,
            'col_sm' => 2,
            'col_xs' => 1,
            'layout' => 'basic',
            'filter' => 'true',
            'cms_template' => 'cms_grid.php',
            'class' => '',
        ),
        $atts,
        'cms_grid'
    );

    list( $args, $query ) = foldery_parse_vc_source( $atts['source'] );
    if ( strstr( $atts['source'], 'tax_query' ) ) {
        foreach ( explode( '|', $atts['source'] ) as $part ) {
            $pair = explode( ':', $part, 2 );
            if ( count( $pair ) === 2 && $pair[0] === 'tax_query' ) {
                $atts['cat'] = $pair[1];
            }
        }
    } else {
        $atts['cat'] = isset( $args['cat'] ) ? $args['cat'] : '';
    }

    $col_lg = 12 / max( 1, (int) $atts['col_lg'] );
    $col_md = 12 / max( 1, (int) $atts['col_md'] );
    $col_sm = 12 / max( 1, (int) $atts['col_sm'] );
    $col_xs = 12 / max( 1, (int) $atts['col_xs'] );
    $atts['posts'] = $query;
    $atts['item_class'] = "cms-grid-item col-lg-{$col_lg} col-md-{$col_md} col-sm-{$col_sm} col-xs-{$col_xs}";
    $atts['grid_class'] = 'cms-grid' . ( $atts['layout'] === 'masonry' ? ' cms-grid-masonry' : '' );
    $atts['template'] = 'template-' . str_replace( '.php', '', $atts['cms_template'] ) . ' ' . $atts['class'];
    $atts['html_id'] = cmsHtmlID( 'cms-grid' );

    $template = get_template_directory() . '/vc_templates/' . basename( $atts['cms_template'] );
    if ( ! file_exists( $template ) ) {
        $template = get_template_directory() . '/vc_templates/cms_grid.php';
    }

    ob_start();
    include $template;
    return ob_get_clean();
}

function foldery_shortcode_cms_fancybox_single( $atts = array(), $content = null ) {
    $atts = shortcode_atts(
        array(
            'icon_pe7stroke' => '',
            'title_item' => '',
            'description_item' => '',
            'image_align' => '',
            'cms_template' => 'cms_fancybox_single.php',
        ),
        $atts,
        'cms_fancybox_single'
    );

    $icon = $atts['icon_pe7stroke'] ? '<i class="' . esc_attr( $atts['icon_pe7stroke'] ) . '"></i>' : '';

    return '<div class="cms-fancyboxes-wraper cms-fancy-box-single template-cms_fancybox_single clearfix">' .
        '<div class="cms-fancyboxes-body"><div class="cms-fancybox-item">' .
        ( $icon ? '<div class="fancy-box-icon ' . esc_attr( $atts['image_align'] ) . '"><div class="fancy-box-icon-inner">' . $icon . '</div></div>' : '' ) .
        '<div class="fancy-box-content-wrap ' . ( $icon ? 'has-icon-image' : '' ) . '">' .
        ( $atts['title_item'] ? '<h4>' . esc_html( $atts['title_item'] ) . '</h4>' : '' ) .
        '<div class="fancy-box-content">' . wp_kses_post( wpautop( $atts['description_item'] ) ) . '</div>' .
        '</div></div></div></div>';
}

function foldery_shortcode_cms_googlemap( $atts = array(), $content = null ) {
    $atts = shortcode_atts(
        array(
            'address' => '',
            'markercoordinate' => '',
            'height' => '450px',
        ),
        $atts,
        'cms_googlemap'
    );

    $query = $atts['markercoordinate'] ? $atts['markercoordinate'] : $atts['address'];
    if ( empty( $query ) ) {
        return '';
    }

    return '<div class="cms-googlemap" style="height:' . esc_attr( $atts['height'] ) . '">' .
        '<iframe loading="lazy" style="border:0;width:100%;height:100%" src="https://maps.google.com/maps?q=' . rawurlencode( $query ) . '&amp;output=embed"></iframe>' .
        '</div>';
}

function foldery_register_legacy_shortcodes() {
    add_shortcode( 'cms_grid', 'foldery_shortcode_cms_grid' );
    add_shortcode( 'cms_fancybox_single', 'foldery_shortcode_cms_fancybox_single' );
    add_shortcode( 'cms_googlemap', 'foldery_shortcode_cms_googlemap' );
}
add_action( 'init', 'foldery_register_legacy_shortcodes', 20 );
