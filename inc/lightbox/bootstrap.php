<?php
/**
 * Foldery lightbox.
 */

function foldery_lightbox_options() {
    $defaults = array(
        'enabled' => true,
        'groupLinks' => true,
        'groupByPost' => true,
        'groupGallery' => false,
        'loop' => true,
        'autofit' => true,
        'animate' => true,
        'overlayOpacity' => 0.8,
        'titleDefault' => false,
        'slideshowAutostart' => false,
        'slideshowDuration' => 6,
        'labels' => array(
            'close' => __( 'Close', 'foldery' ),
            'loading' => __( 'Loading', 'foldery' ),
            'next' => __( 'Next', 'foldery' ),
            'prev' => __( 'Previous', 'foldery' ),
            'slideshowStart' => __( 'Start slideshow', 'foldery' ),
            'slideshowStop' => __( 'Stop slideshow', 'foldery' ),
            'groupStatus' => __( 'Image %current% of %total%', 'foldery' ),
        ),
    );

    if ( ! function_exists( 'foldery_theme_settings' ) ) {
        return $defaults;
    }

    $settings = foldery_theme_settings();
    if ( empty( $settings['lightbox'] ) || ! is_array( $settings['lightbox'] ) ) {
        return $defaults;
    }

    return array_replace(
        $defaults,
        array_intersect_key( $settings['lightbox'], $defaults )
    );
}

function foldery_lightbox_is_enabled() {
    $options = foldery_lightbox_options();
    if ( empty( $options['enabled'] ) || is_feed() ) {
        return false;
    }

    if ( is_admin() ) {
        if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'foldery_explorer' === sanitize_key( wp_unslash( $_REQUEST['action'] ) ) ) {
            return true;
        }

        return false;
    }

    return true;
}

function foldery_lightbox_is_image_url( $url ) {
    if ( ! is_string( $url ) || $url === '' ) {
        return false;
    }

    $path = wp_parse_url( html_entity_decode( $url ), PHP_URL_PATH );
    if ( ! is_string( $path ) || $path === '' ) {
        return false;
    }

    return (bool) preg_match( '/\.(?:jpe?g|png|gif|webp|avif|bmp)(?:$|\?)/i', $path );
}

function foldery_lightbox_group_id( $group = null ) {
    $options = foldery_lightbox_options();

    if ( is_string( $group ) && $group !== '' ) {
        return sanitize_title( $group );
    }

    if ( ! empty( $options['groupByPost'] ) && in_the_loop() ) {
        $post_id = get_the_ID();
        if ( $post_id ) {
            return 'post-' . absint( $post_id );
        }
    }

    return ! empty( $options['groupLinks'] ) ? 'page' : wp_unique_id( 'item-' );
}

function foldery_lightbox_activate( $content, $group = null ) {
    if ( empty( $content ) || ! foldery_lightbox_is_enabled() ) {
        return $content;
    }

    $group_id = foldery_lightbox_group_id( $group );

    if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
        $tags = new WP_HTML_Tag_Processor( $content );
        $has_lightbox = false;

        while ( $tags->next_tag( 'A' ) ) {
            $href = $tags->get_attribute( 'href' );
            if ( ! foldery_lightbox_is_image_url( $href ) || null !== $tags->get_attribute( 'data-foldery-lightbox-exclude' ) ) {
                continue;
            }

            $has_lightbox = true;
            $tags->add_class( 'foldery-lightbox-link' );
            $tags->set_attribute( 'data-foldery-lightbox', 'image' );
            $tags->set_attribute( 'data-foldery-lightbox-group', $group_id );

            $title = $tags->get_attribute( 'title' );
            if ( is_string( $title ) && $title !== '' && null === $tags->get_attribute( 'data-foldery-lightbox-title' ) ) {
                $tags->set_attribute( 'data-foldery-lightbox-title', $title );
            }
        }

        return $has_lightbox ? $tags->get_updated_html() : $content;
    }

    return preg_replace_callback(
        '/<a\b([^>]*\bhref=(["\'])([^"\']+)\2[^>]*)>/i',
        function ( $matches ) use ( $group_id ) {
            if ( ! foldery_lightbox_is_image_url( $matches[3] ) || false !== strpos( $matches[1], 'data-foldery-lightbox-exclude' ) ) {
                return $matches[0];
            }

            $attrs = $matches[1];
            if ( false === strpos( $attrs, 'class=' ) ) {
                $attrs .= ' class="foldery-lightbox-link"';
            }

            return '<a ' . $attrs . ' data-foldery-lightbox="image" data-foldery-lightbox-group="' . esc_attr( $group_id ) . '">';
        },
        $content
    );
}

function foldery_lightbox_filter_content( $content ) {
    return foldery_lightbox_activate( $content );
}
add_filter( 'the_content', 'foldery_lightbox_filter_content', 99 );

function foldery_lightbox_filter_widget_content( $content ) {
    return foldery_lightbox_activate( $content, 'widget' );
}
add_filter( 'widget_text', 'foldery_lightbox_filter_widget_content', 99 );
add_filter( 'widget_text_content', 'foldery_lightbox_filter_widget_content', 99 );

function foldery_lightbox_filter_menu_content( $content ) {
    return foldery_lightbox_activate( $content, 'menu' );
}
add_filter( 'wp_nav_menu', 'foldery_lightbox_filter_menu_content', 99 );

function foldery_lightbox_enqueue_assets() {
    if ( ! foldery_lightbox_is_enabled() ) {
        return;
    }

    $options = foldery_lightbox_options();

    foreach ( array( 'enabled', 'groupLinks', 'groupByPost', 'groupGallery', 'loop', 'autofit', 'animate', 'titleDefault', 'slideshowAutostart' ) as $key ) {
        $options[ $key ] = ! empty( $options[ $key ] );
    }

    wp_enqueue_style( 'foldery-lightbox', get_template_directory_uri() . '/assets/css/foldery-lightbox.css', array(), '1.0.0' );
    wp_enqueue_script( 'foldery-lightbox', get_template_directory_uri() . '/assets/js/foldery-lightbox.js', array(), '1.0.0', true );
    wp_add_inline_script(
        'foldery-lightbox',
        'window.FolderyLightboxOptions = ' . wp_json_encode( $options ) . ';',
        'before'
    );
}
add_action( 'wp_enqueue_scripts', 'foldery_lightbox_enqueue_assets', 20 );
