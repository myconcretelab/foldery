<?php
/**
 * Foldery core helpers and local replacements for features formerly supplied
 * by the parent theme and companion plugins.
 */

if ( ! defined( 'FOLDERY_IMAGES' ) ) {
    define( 'FOLDERY_IMAGES', get_template_directory_uri() . '/assets/images/' );
}
if ( ! defined( 'FOLDERY_JS' ) ) {
    define( 'FOLDERY_JS', get_template_directory_uri() . '/assets/js/' );
}
if ( ! defined( 'FOLDERY_CSS' ) ) {
    define( 'FOLDERY_CSS', get_template_directory_uri() . '/assets/css/' );
}

if ( ! function_exists( 'foldery_remove_filter' ) ) {
    function foldery_remove_filter( $tag, $function_to_remove, $priority = 10 ) {
        return remove_filter( $tag, $function_to_remove, $priority );
    }
}

if ( ! function_exists( 'foldery_register_widget' ) ) {
    function foldery_register_widget( $widget_class ) {
        return register_widget( $widget_class );
    }
}

if ( ! function_exists( 'foldery_base64_encode' ) ) {
    function foldery_base64_encode( $data ) {
        return base64_encode( $data );
    }
}

if ( ! function_exists( 'foldery_base64_decode' ) ) {
    function foldery_base64_decode( $data ) {
        return base64_decode( $data );
    }
}

if ( ! function_exists( 'foldery_allowed_html' ) ) {
    function foldery_allowed_html( $html, $echo = true ) {
        if ( $echo ) {
            echo $html;
            return null;
        }

        return $html;
    }
}

if ( ! function_exists( 'foldery_get_categories_by_post_id' ) ) {
    function foldery_get_categories_by_post_id( $post_ID = null, $taxo = 'category' ) {
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

if ( ! function_exists( 'foldery_html_id' ) ) {
    function foldery_html_id( $id ) {
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
        'foldery_wc_archive_per_page' => '12',
        'foldery_wc_archive_sidebar' => '',
        'foldery_wc_single_sidebar' => '',
        'foldery_wc_single_gallery' => '',
        'lightbox_enabled' => '1',
        'lightbox_enabled_home' => '1',
        'lightbox_enabled_post' => '1',
        'lightbox_enabled_page' => '1',
        'lightbox_enabled_archive' => '1',
        'lightbox_enabled_widget' => '',
        'lightbox_enabled_menu' => '',
        'lightbox_group_links' => '1',
        'lightbox_group_post' => '1',
        'lightbox_group_gallery' => '',
        'lightbox_group_loop' => '1',
        'lightbox_ui_autofit' => '1',
        'lightbox_ui_animate' => '1',
        'lightbox_ui_overlay_opacity' => '0.8',
        'lightbox_ui_title_default' => '',
        'lightbox_slideshow_autostart' => '',
        'lightbox_slideshow_duration' => '6',
        'lightbox_label_close' => 'Close',
        'lightbox_label_loading' => 'Loading',
        'lightbox_label_next' => 'Next',
        'lightbox_label_prev' => 'Previous',
        'lightbox_label_slideshow_start' => 'Start slideshow',
        'lightbox_label_slideshow_stop' => 'Stop slideshow',
        'lightbox_label_group_status' => 'Image %current% of %total%',
        'gutenberg' => '0',
    );
}

function foldery_load_theme_options() {
    global $smof_data;

    $saved = get_option( 'smof_data', array() );
    if ( ! is_array( $saved ) ) {
        $saved = array();
    }

    $legacy_prefix = 'zk_' . 'mon' . 'aco_wc_';
    $option_aliases = array(
        'foldery_wc_archive_per_page' => $legacy_prefix . 'archive_per_page',
        'foldery_wc_archive_sidebar'  => $legacy_prefix . 'archive_sidebar',
        'foldery_wc_single_sidebar'   => $legacy_prefix . 'single_sidebar',
        'foldery_wc_single_gallery'   => $legacy_prefix . 'single_gallery',
    );

    foreach ( $option_aliases as $current_key => $legacy_key ) {
        if ( empty( $saved[ $current_key ] ) && ! empty( $saved[ $legacy_key ] ) ) {
            $saved[ $current_key ] = $saved[ $legacy_key ];
        }
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
    $atts['html_id'] = foldery_html_id( 'cms-grid' );

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

function foldery_vc_shortcode_css_style( $css ) {
    if ( ! is_string( $css ) || $css === '' ) {
        return '';
    }

    if ( preg_match( '/\{([^}]+)\}/', $css, $matches ) ) {
        $css = $matches[1];
    }

    return safecss_filter_attr( str_replace( '!important', '', $css ) );
}

function foldery_do_shortcode_capture( $content ) {
    ob_start();
    $html = do_shortcode( shortcode_unautop( $content ) );
    $printed = ob_get_clean();

    return $printed . $html;
}

function foldery_vc_column_width_class( $width ) {
    $map = array(
        '1/1'  => 12,
        '1/2'  => 6,
        '1/3'  => 4,
        '2/3'  => 8,
        '1/4'  => 3,
        '3/4'  => 9,
        '1/6'  => 2,
        '5/6'  => 10,
        '5/12' => 5,
        '7/12' => 7,
    );

    $span = isset( $map[ $width ] ) ? $map[ $width ] : 12;

    return 'vc_col-sm-' . $span . ' col-sm-' . $span;
}

function foldery_shortcode_vc_row( $atts = array(), $content = null ) {
    $atts = shortcode_atts(
        array(
            'css' => '',
            'el_class' => '',
        ),
        $atts,
        'vc_row'
    );

    $style = foldery_vc_shortcode_css_style( $atts['css'] );
    $classes = trim( 'vc_row wpb_row vc_row-fluid clearfix ' . $atts['el_class'] );
    $html = foldery_do_shortcode_capture( $content );

    return '<div class="' . esc_attr( $classes ) . '"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>' . $html . '</div>';
}

function foldery_shortcode_vc_column( $atts = array(), $content = null ) {
    $atts = shortcode_atts(
        array(
            'width' => '1/1',
            'offset' => '',
            'css' => '',
            'el_class' => '',
        ),
        $atts,
        'vc_column'
    );

    $offset = trim( str_replace( 'vc_hidden-xs', 'vc_hidden-xs hidden-xs', $atts['offset'] ) );
    $style = foldery_vc_shortcode_css_style( $atts['css'] );
    $classes = trim( 'wpb_column vc_column_container ' . foldery_vc_column_width_class( $atts['width'] ) . ' ' . $offset . ' ' . $atts['el_class'] );
    $html = foldery_do_shortcode_capture( $content );

    return '<div class="' . esc_attr( $classes ) . '"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '><div class="vc_column-inner"><div class="wpb_wrapper">' . $html . '</div></div></div>';
}

function foldery_shortcode_vc_column_text( $atts = array(), $content = null ) {
    $atts = shortcode_atts(
        array(
            'css' => '',
            'el_class' => '',
        ),
        $atts,
        'vc_column_text'
    );

    $style = foldery_vc_shortcode_css_style( $atts['css'] );
    $html = foldery_do_shortcode_capture( $content );
    if ( false === strpos( $html, '<div' ) && false === strpos( $html, '<blockquote' ) ) {
        $html = wpautop( $html );
    }

    return '<div class="wpb_text_column wpb_content_element ' . esc_attr( $atts['el_class'] ) . '"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '><div class="wpb_wrapper">' . $html . '</div></div>';
}

function foldery_shortcode_vc_btn( $atts = array() ) {
    $atts = shortcode_atts(
        array(
            'title' => '',
            'link' => '',
            'size' => 'md',
            'align' => '',
            'button_block' => '',
            'add_icon' => '',
            'i_icon_fontawesome' => '',
            'el_class' => '',
        ),
        $atts,
        'vc_btn'
    );

    $link = array();
    foreach ( explode( '|', $atts['link'] ) as $part ) {
        $pair = explode( ':', $part, 2 );
        if ( count( $pair ) === 2 ) {
            $link[ $pair[0] ] = rawurldecode( $pair[1] );
        }
    }

    $url = isset( $link['url'] ) ? $link['url'] : '#';
    $title = $atts['title'] !== '' ? $atts['title'] : ( isset( $link['title'] ) ? $link['title'] : '' );
    $icon_class = trim( str_replace( 'fas ', 'fa ', $atts['i_icon_fontawesome'] ) );
    $classes = trim( 'vc_general vc_btn3 vc_btn3-size-' . sanitize_html_class( $atts['size'] ) . ( $atts['button_block'] ? ' vc_btn3-block btn-block' : '' ) . ' ' . $atts['el_class'] );
    $button = '<a class="' . esc_attr( $classes ) . '" href="' . esc_url( $url ) . '">' .
        ( $atts['add_icon'] && $icon_class ? '<i class="' . esc_attr( $icon_class ) . '"></i> ' : '' ) .
        esc_html( $title ) .
        '</a>';

    return $atts['align'] ? '<div class="vc_btn3-container vc_btn3-' . esc_attr( $atts['align'] ) . '">' . $button . '</div>' : $button;
}

function foldery_shortcode_vc_custom_heading( $atts = array() ) {
    $atts = shortcode_atts(
        array(
            'text' => '',
            'font_container' => '',
            'el_class' => '',
        ),
        $atts,
        'vc_custom_heading'
    );

    $tag = 'div';
    $style = '';
    foreach ( explode( '|', $atts['font_container'] ) as $part ) {
        $pair = explode( ':', $part, 2 );
        if ( count( $pair ) !== 2 ) {
            continue;
        }
        if ( $pair[0] === 'tag' && preg_match( '/^(h[1-6]|div|p)$/', $pair[1] ) ) {
            $tag = $pair[1];
        } elseif ( $pair[0] === 'font_size' ) {
            $style .= 'font-size:' . sanitize_text_field( $pair[1] ) . ';';
        } elseif ( $pair[0] === 'text_align' ) {
            $style .= 'text-align:' . sanitize_text_field( $pair[1] ) . ';';
        } elseif ( $pair[0] === 'color' ) {
            $style .= 'color:' . sanitize_hex_color( rawurldecode( $pair[1] ) ) . ';';
        } elseif ( $pair[0] === 'line_height' ) {
            $style .= 'line-height:' . sanitize_text_field( $pair[1] ) . ';';
        }
    }

    return '<' . $tag . ' class="vc_custom_heading ' . esc_attr( $atts['el_class'] ) . '"' . ( $style ? ' style="' . esc_attr( safecss_filter_attr( $style ) ) . '"' : '' ) . '>' . esc_html( $atts['text'] ) . '</' . $tag . '>';
}

function foldery_shortcode_vc_zigzag( $atts = array() ) {
    $atts = shortcode_atts(
        array(
            'el_class' => '',
        ),
        $atts,
        'vc_zigzag'
    );

    return '<div class="vc_zigzag ' . esc_attr( $atts['el_class'] ) . '"></div>';
}

function foldery_register_legacy_shortcodes() {
    if ( ! shortcode_exists( 'vc_row' ) ) {
        add_shortcode( 'vc_row', 'foldery_shortcode_vc_row' );
    }
    if ( ! shortcode_exists( 'vc_column' ) ) {
        add_shortcode( 'vc_column', 'foldery_shortcode_vc_column' );
    }
    if ( ! shortcode_exists( 'vc_column_text' ) ) {
        add_shortcode( 'vc_column_text', 'foldery_shortcode_vc_column_text' );
    }
    if ( ! shortcode_exists( 'vc_btn' ) ) {
        add_shortcode( 'vc_btn', 'foldery_shortcode_vc_btn' );
    }
    if ( ! shortcode_exists( 'vc_custom_heading' ) ) {
        add_shortcode( 'vc_custom_heading', 'foldery_shortcode_vc_custom_heading' );
    }
    if ( ! shortcode_exists( 'vc_zigzag' ) ) {
        add_shortcode( 'vc_zigzag', 'foldery_shortcode_vc_zigzag' );
    }

    add_shortcode( 'cms_grid', 'foldery_shortcode_cms_grid' );
    add_shortcode( 'cms_fancybox_single', 'foldery_shortcode_cms_fancybox_single' );
    add_shortcode( 'cms_googlemap', 'foldery_shortcode_cms_googlemap' );
}
add_action( 'init', 'foldery_register_legacy_shortcodes', 20 );
