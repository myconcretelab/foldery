<?php
/**
 * Foldery lightbox.
 */

function foldery_lightbox_options() {
    global $smof_data;

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

    $settings = is_array( $smof_data ) ? $smof_data : array();

    $options = array(
        'enabled' => ! empty( $settings['lightbox_enabled'] ),
        'groupLinks' => ! empty( $settings['lightbox_group_links'] ),
        'groupByPost' => ! empty( $settings['lightbox_group_post'] ),
        'groupGallery' => ! empty( $settings['lightbox_group_gallery'] ),
        'loop' => ! empty( $settings['lightbox_group_loop'] ),
        'autofit' => ! empty( $settings['lightbox_ui_autofit'] ),
        'animate' => ! empty( $settings['lightbox_ui_animate'] ),
        'overlayOpacity' => isset( $settings['lightbox_ui_overlay_opacity'] ) ? (float) $settings['lightbox_ui_overlay_opacity'] : $defaults['overlayOpacity'],
        'titleDefault' => ! empty( $settings['lightbox_ui_title_default'] ),
        'slideshowAutostart' => ! empty( $settings['lightbox_slideshow_autostart'] ),
        'slideshowDuration' => isset( $settings['lightbox_slideshow_duration'] ) ? max( 1, (int) $settings['lightbox_slideshow_duration'] ) : $defaults['slideshowDuration'],
        'labels' => array(
            'close' => isset( $settings['lightbox_label_close'] ) ? $settings['lightbox_label_close'] : $defaults['labels']['close'],
            'loading' => isset( $settings['lightbox_label_loading'] ) ? $settings['lightbox_label_loading'] : $defaults['labels']['loading'],
            'next' => isset( $settings['lightbox_label_next'] ) ? $settings['lightbox_label_next'] : $defaults['labels']['next'],
            'prev' => isset( $settings['lightbox_label_prev'] ) ? $settings['lightbox_label_prev'] : $defaults['labels']['prev'],
            'slideshowStart' => isset( $settings['lightbox_label_slideshow_start'] ) ? $settings['lightbox_label_slideshow_start'] : $defaults['labels']['slideshowStart'],
            'slideshowStop' => isset( $settings['lightbox_label_slideshow_stop'] ) ? $settings['lightbox_label_slideshow_stop'] : $defaults['labels']['slideshowStop'],
            'groupStatus' => isset( $settings['lightbox_label_group_status'] ) ? $settings['lightbox_label_group_status'] : $defaults['labels']['groupStatus'],
        ),
    );

    $options['overlayOpacity'] = min( 1, max( 0, $options['overlayOpacity'] ) );

    return apply_filters( 'foldery_lightbox_options', wp_parse_args( $options, $defaults ) );
}

function foldery_lightbox_is_enabled() {
    global $smof_data;

    $options = foldery_lightbox_options();
    if ( empty( $options['enabled'] ) || is_admin() || is_feed() ) {
        return false;
    }

    $settings = is_array( $smof_data ) ? $smof_data : array();

    if ( is_front_page() || is_home() ) {
        return ! empty( $settings['lightbox_enabled_home'] );
    }

    if ( is_page() ) {
        return ! empty( $settings['lightbox_enabled_page'] );
    }

    if ( is_singular() ) {
        return ! empty( $settings['lightbox_enabled_post'] );
    }

    if ( is_archive() || is_search() ) {
        return ! empty( $settings['lightbox_enabled_archive'] );
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
    global $smof_data;

    return ! empty( $smof_data['lightbox_enabled_widget'] ) ? foldery_lightbox_activate( $content, 'widget' ) : $content;
}
add_filter( 'widget_text', 'foldery_lightbox_filter_widget_content', 99 );
add_filter( 'widget_text_content', 'foldery_lightbox_filter_widget_content', 99 );

function foldery_lightbox_filter_menu_content( $content ) {
    global $smof_data;

    return ! empty( $smof_data['lightbox_enabled_menu'] ) ? foldery_lightbox_activate( $content, 'menu' ) : $content;
}
add_filter( 'wp_nav_menu', 'foldery_lightbox_filter_menu_content', 99 );

function foldery_lightbox_enqueue_assets() {
    if ( ! foldery_lightbox_is_enabled() ) {
        return;
    }

    $options = foldery_lightbox_options();

    wp_enqueue_style( 'foldery-lightbox', get_template_directory_uri() . '/assets/css/foldery-lightbox.css', array(), '1.0.0' );
    wp_enqueue_script( 'foldery-lightbox', get_template_directory_uri() . '/assets/js/foldery-lightbox.js', array(), '1.0.0', true );
    wp_localize_script( 'foldery-lightbox', 'FolderyLightboxOptions', $options );
}
add_action( 'wp_enqueue_scripts', 'foldery_lightbox_enqueue_assets', 20 );
