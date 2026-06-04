<?php
/**
 * Atelier page template helpers.
 */

if ( ! defined( 'FOLDERY_ATELIER_HERO_IMAGE_META' ) ) {
    define( 'FOLDERY_ATELIER_HERO_IMAGE_META', '_foldery_atelier_hero_image_id' );
}

if ( ! defined( 'FOLDERY_ATELIER_TITLE_META' ) ) {
    define( 'FOLDERY_ATELIER_TITLE_META', '_foldery_atelier_title' );
}

if ( ! defined( 'FOLDERY_ATELIER_SUBTITLE_META' ) ) {
    define( 'FOLDERY_ATELIER_SUBTITLE_META', '_foldery_atelier_subtitle' );
}

if ( ! defined( 'FOLDERY_ATELIER_ARTWORKS_META' ) ) {
    define( 'FOLDERY_ATELIER_ARTWORKS_META', '_foldery_atelier_artwork_ids' );
}

if ( ! defined( 'FOLDERY_ATELIER_OVERLAY_COLOR_META' ) ) {
    define( 'FOLDERY_ATELIER_OVERLAY_COLOR_META', '_foldery_atelier_overlay_color' );
}

if ( ! defined( 'FOLDERY_ATELIER_OVERLAY_OPACITY_META' ) ) {
    define( 'FOLDERY_ATELIER_OVERLAY_OPACITY_META', '_foldery_atelier_overlay_opacity' );
}

if ( ! defined( 'FOLDERY_ATELIER_VIGNETTE_META' ) ) {
    define( 'FOLDERY_ATELIER_VIGNETTE_META', '_foldery_atelier_vignette' );
}

function foldery_atelier_meta_keys() {
    return array(
        FOLDERY_ATELIER_HERO_IMAGE_META,
        FOLDERY_ATELIER_TITLE_META,
        FOLDERY_ATELIER_SUBTITLE_META,
        FOLDERY_ATELIER_ARTWORKS_META,
        FOLDERY_ATELIER_OVERLAY_COLOR_META,
        FOLDERY_ATELIER_OVERLAY_OPACITY_META,
        FOLDERY_ATELIER_VIGNETTE_META,
    );
}

function foldery_atelier_parse_ids( $ids ) {
    if ( is_array( $ids ) ) {
        $raw = $ids;
    } else {
        $value   = trim( (string) $ids );
        $decoded = '[' === substr( $value, 0, 1 ) ? json_decode( $value, true ) : null;
        $raw     = is_array( $decoded ) ? array_map(
            function( $item ) {
                return is_array( $item ) && isset( $item['id'] ) ? $item['id'] : $item;
            },
            $decoded
        ) : explode( ',', $value );
    }

    return array_values( array_unique( array_filter( array_map( 'absint', $raw ) ) ) );
}

function foldery_atelier_clamp_int( $value, $min, $max ) {
    return min( $max, max( $min, absint( $value ) ) );
}

function foldery_atelier_default_overlay_settings() {
    return array(
        'color'    => '#15100c',
        'opacity'  => 34,
        'vignette' => 46,
    );
}

function foldery_atelier_sanitize_overlay_color( $value ) {
    $defaults = foldery_atelier_default_overlay_settings();
    $color = sanitize_hex_color( $value );

    return $color ? $color : $defaults['color'];
}

function foldery_atelier_sanitize_percent_meta( $value ) {
    return (string) foldery_atelier_clamp_int( $value, 0, 100 );
}

function foldery_atelier_default_artwork( $attachment_id ) {
    return array(
        'id'    => absint( $attachment_id ),
        'scale' => 100,
        'tapes' => 1,
    );
}

function foldery_atelier_normalize_artwork( $artwork ) {
    if ( is_numeric( $artwork ) ) {
        return foldery_atelier_default_artwork( $artwork );
    }

    if ( ! is_array( $artwork ) ) {
        return null;
    }

    $attachment_id = isset( $artwork['id'] ) ? absint( $artwork['id'] ) : 0;
    if ( ! $attachment_id ) {
        return null;
    }

    return array(
        'id'    => $attachment_id,
        'scale' => isset( $artwork['scale'] ) ? foldery_atelier_clamp_int( $artwork['scale'], 55, 145 ) : 100,
        'tapes' => isset( $artwork['tapes'] ) ? foldery_atelier_clamp_int( $artwork['tapes'], 0, 5 ) : 1,
    );
}

function foldery_atelier_parse_artworks( $value ) {
    if ( is_array( $value ) ) {
        $raw = $value;
    } else {
        $value   = trim( (string) $value );
        $decoded = '[' === substr( $value, 0, 1 ) ? json_decode( $value, true ) : null;
        $raw     = is_array( $decoded ) ? $decoded : foldery_atelier_parse_ids( $value );
    }

    $artworks = array();
    $seen     = array();

    foreach ( $raw as $item ) {
        $artwork = foldery_atelier_normalize_artwork( $item );
        if ( ! $artwork || isset( $seen[ $artwork['id'] ] ) ) {
            continue;
        }

        $seen[ $artwork['id'] ] = true;
        $artworks[]             = $artwork;
    }

    return $artworks;
}

function foldery_atelier_current_page_id() {
    if ( is_singular( 'page' ) ) {
        return get_queried_object_id();
    }

    return get_the_ID();
}

function foldery_atelier_get_meta( $post_id, $key ) {
    return get_post_meta( $post_id, $key, true );
}

function foldery_atelier_register_meta() {
    foreach ( foldery_atelier_meta_keys() as $key ) {
        $sanitize_callback = 'sanitize_text_field';
        if ( FOLDERY_ATELIER_HERO_IMAGE_META === $key ) {
            $sanitize_callback = 'foldery_atelier_sanitize_ids_meta';
        } elseif ( FOLDERY_ATELIER_ARTWORKS_META === $key ) {
            $sanitize_callback = 'foldery_atelier_sanitize_artworks_meta';
        } elseif ( FOLDERY_ATELIER_OVERLAY_COLOR_META === $key ) {
            $sanitize_callback = 'foldery_atelier_sanitize_overlay_color';
        } elseif ( in_array( $key, array( FOLDERY_ATELIER_OVERLAY_OPACITY_META, FOLDERY_ATELIER_VIGNETTE_META ), true ) ) {
            $sanitize_callback = 'foldery_atelier_sanitize_percent_meta';
        }

        register_post_meta(
            'page',
            $key,
            array(
                'single'            => true,
                'show_in_rest'      => true,
                'auth_callback'     => function() {
                    return current_user_can( 'edit_pages' );
                },
                'sanitize_callback' => $sanitize_callback,
                'type'              => 'string',
            )
        );
    }
}
add_action( 'init', 'foldery_atelier_register_meta' );

function foldery_atelier_sanitize_ids_meta( $value ) {
    return implode( ',', foldery_atelier_parse_ids( $value ) );
}

function foldery_atelier_sanitize_artworks_meta( $value ) {
    $artworks = foldery_atelier_parse_artworks( $value );

    return $artworks ? wp_json_encode( $artworks ) : '';
}

function foldery_atelier_hex_to_rgb( $color ) {
    $color = ltrim( foldery_atelier_sanitize_overlay_color( $color ), '#' );

    return array(
        hexdec( substr( $color, 0, 2 ) ),
        hexdec( substr( $color, 2, 2 ) ),
        hexdec( substr( $color, 4, 2 ) ),
    );
}

function foldery_atelier_rgba( $color, $opacity ) {
    $rgb     = foldery_atelier_hex_to_rgb( $color );
    $opacity = foldery_atelier_clamp_int( $opacity, 0, 100 ) / 100;

    return sprintf( 'rgba(%1$d, %2$d, %3$d, %.2F)', $rgb[0], $rgb[1], $rgb[2], $opacity );
}

function foldery_atelier_overlay_settings( $post_id ) {
    $defaults = foldery_atelier_default_overlay_settings();
    $color    = foldery_atelier_get_meta( $post_id, FOLDERY_ATELIER_OVERLAY_COLOR_META );
    $opacity  = foldery_atelier_get_meta( $post_id, FOLDERY_ATELIER_OVERLAY_OPACITY_META );
    $vignette = foldery_atelier_get_meta( $post_id, FOLDERY_ATELIER_VIGNETTE_META );

    return array(
        'color'    => '' !== $color ? foldery_atelier_sanitize_overlay_color( $color ) : $defaults['color'],
        'opacity'  => '' !== $opacity ? foldery_atelier_clamp_int( $opacity, 0, 100 ) : $defaults['opacity'],
        'vignette' => '' !== $vignette ? foldery_atelier_clamp_int( $vignette, 0, 100 ) : $defaults['vignette'],
    );
}

function foldery_atelier_hero_style( $hero_url, $overlay ) {
    return sprintf(
        '--atelier-hero-image: url("%1$s"); --atelier-hero-overlay: %2$s; --atelier-hero-vignette: %3$s;',
        esc_url( $hero_url ),
        foldery_atelier_rgba( $overlay['color'], $overlay['opacity'] ),
        foldery_atelier_rgba( $overlay['color'], $overlay['vignette'] )
    );
}

function foldery_atelier_tape_style( $attachment_id, $index, $tape_index, $count ) {
    $positions = array(
        array( 'left' => 18, 'top' => -14, 'rotate' => -8, 'axis' => 'x' ),
        array( 'left' => 50, 'top' => -14, 'rotate' => 3, 'axis' => 'x' ),
        array( 'left' => 82, 'top' => -14, 'rotate' => 8, 'axis' => 'x' ),
        array( 'right' => -34, 'top' => 22, 'rotate' => 84, 'axis' => 'y' ),
        array( 'right' => -34, 'top' => 50, 'rotate' => 92, 'axis' => 'y' ),
        array( 'right' => -34, 'top' => 78, 'rotate' => 99, 'axis' => 'y' ),
        array( 'left' => 82, 'bottom' => -14, 'rotate' => -6, 'axis' => 'x' ),
        array( 'left' => 50, 'bottom' => -14, 'rotate' => 3, 'axis' => 'x' ),
        array( 'left' => 18, 'bottom' => -14, 'rotate' => 7, 'axis' => 'x' ),
        array( 'left' => -34, 'top' => 78, 'rotate' => -96, 'axis' => 'y' ),
        array( 'left' => -34, 'top' => 50, 'rotate' => -88, 'axis' => 'y' ),
        array( 'left' => -34, 'top' => 22, 'rotate' => -82, 'axis' => 'y' ),
    );
    $seed      = absint( sprintf( '%u', crc32( $attachment_id . ':' . $index ) ) );
    $count     = max( 1, foldery_atelier_clamp_int( $count, 1, 5 ) );
    $step      = count( $positions ) / $count;
    $position  = $positions[ ( (int) floor( $seed % count( $positions ) ) + (int) round( $tape_index * $step ) ) % count( $positions ) ];
    $jitter    = -4 + ( ( absint( sprintf( '%u', crc32( $attachment_id . ':' . $index . ':' . $tape_index ) ) ) % 9 ) );
    $rotation  = $position['rotate'] + $jitter;
    $transform = 'x' === $position['axis'] ? 'translateX(-50%)' : 'translateY(-50%)';
    $style     = '';

    foreach ( array( 'left', 'right', 'top', 'bottom' ) as $key ) {
        if ( isset( $position[ $key ] ) ) {
            $unit   = in_array( $key, array( 'left', 'top' ), true ) && $position[ $key ] >= 0 ? '%' : 'px';
            $style .= $key . ':' . (int) $position[ $key ] . $unit . ';';
        }
    }

    return $style . sprintf( 'transform:%1$s rotate(%2$ddeg);', $transform, $rotation );
}

function foldery_atelier_render_tapes( $attachment_id, $index, $count ) {
    $count = foldery_atelier_clamp_int( $count, 0, 5 );
    if ( ! $count ) {
        return '';
    }

    $html = '';
    for ( $tape_index = 0; $tape_index < $count; $tape_index++ ) {
        $html .= sprintf(
            '<span class="atelier-hero-artwork__tape" style="%s" aria-hidden="true"></span>',
            esc_attr( foldery_atelier_tape_style( $attachment_id, $index, $tape_index, $count ) )
        );
    }

    return $html;
}

function foldery_atelier_render_linked_image( $artwork, $index ) {
    $artwork       = foldery_atelier_normalize_artwork( $artwork );
    $attachment_id = $artwork ? absint( $artwork['id'] ) : 0;
    if ( ! $attachment_id ) {
        return '';
    }

    $image = wp_get_attachment_image(
        $attachment_id,
        'medium_large',
        false,
        array(
            'class'   => 'atelier-hero-artwork__image',
            'loading' => $index < 2 ? 'eager' : 'lazy',
        )
    );

    if ( '' === $image ) {
        return '';
    }

    $scale = foldery_atelier_clamp_int( $artwork['scale'], 55, 145 ) / 100;
    $style = '--atelier-artwork-scale: ' . number_format( $scale, 2, '.', '' ) . ';';
    $tapes = foldery_atelier_render_tapes( $attachment_id, $index, $artwork['tapes'] );
    $full  = wp_get_attachment_image_url( $attachment_id, 'full' );
    if ( ! $full ) {
        return sprintf(
            '<figure class="atelier-hero-artwork atelier-hero-artwork--%1$d" style="%2$s">%3$s%4$s</figure>',
            (int) ( $index + 1 ),
            esc_attr( $style ),
            $tapes,
            $image
        );
    }

    $title = get_the_title( $attachment_id );
    $html  = sprintf(
        '<figure class="atelier-hero-artwork atelier-hero-artwork--%1$d" style="%2$s">%3$s<a href="%4$s" title="%5$s">%6$s</a></figure>',
        (int) ( $index + 1 ),
        esc_attr( $style ),
        $tapes,
        esc_url( $full ),
        esc_attr( $title ),
        $image
    );

    return function_exists( 'foldery_lightbox_activate' ) ? foldery_lightbox_activate( $html, 'atelier-hero' ) : $html;
}

function foldery_render_atelier_hero_block() {
    $post_id = foldery_atelier_current_page_id();
    if ( ! $post_id ) {
        return '';
    }

    $hero_image_id = absint( foldery_atelier_get_meta( $post_id, FOLDERY_ATELIER_HERO_IMAGE_META ) );
    $hero_url      = $hero_image_id ? wp_get_attachment_image_url( $hero_image_id, 'full' ) : '';
    if ( ! $hero_url ) {
        $hero_url = get_template_directory_uri() . '/assets/images/bureau-bg.jpg';
    }

    $title    = trim( (string) foldery_atelier_get_meta( $post_id, FOLDERY_ATELIER_TITLE_META ) );
    $subtitle = trim( (string) foldery_atelier_get_meta( $post_id, FOLDERY_ATELIER_SUBTITLE_META ) );
    $artworks = foldery_atelier_parse_artworks( foldery_atelier_get_meta( $post_id, FOLDERY_ATELIER_ARTWORKS_META ) );
    $overlay  = foldery_atelier_overlay_settings( $post_id );

    ob_start();
    ?>
    <section class="atelier-hero" style="<?php echo esc_attr( foldery_atelier_hero_style( $hero_url, $overlay ) ); ?>" aria-label="<?php esc_attr_e( 'Atelier', 'foldery' ); ?>">
        <div class="atelier-hero__shade"></div>
        <div class="atelier-hero__inner">
            <div class="atelier-hero__copy">
                <?php if ( $title ) : ?>
                    <h1><?php echo esc_html( $title ); ?></h1>
                <?php endif; ?>
                <?php if ( $subtitle ) : ?>
                    <p><?php echo esc_html( $subtitle ); ?></p>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $artworks ) ) : ?>
                <div class="atelier-hero__artworks" data-foldery-lightbox-gallery>
                    <?php
                    foreach ( array_slice( $artworks, 0, 6 ) as $index => $artwork ) {
                        echo foldery_atelier_render_linked_image( $artwork, $index );
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php

    return ob_get_clean();
}

function foldery_register_atelier_hero_block() {
    register_block_type(
        'foldery/atelier-hero',
        array(
            'api_version'     => 3,
            'editor_script'   => 'foldery-blocks-editor',
            'editor_style'    => 'foldery-explorer-editor-style',
            'render_callback' => 'foldery_render_atelier_hero_block',
        )
    );
}
add_action( 'init', 'foldery_register_atelier_hero_block' );

function foldery_atelier_add_meta_box() {
    add_meta_box(
        'foldery_atelier_hero',
        __( 'Heros atelier', 'foldery' ),
        'foldery_atelier_render_meta_box',
        'page',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes_page', 'foldery_atelier_add_meta_box' );

function foldery_atelier_media_preview( $attachment_id, $size = 'thumbnail' ) {
    $attachment_id = absint( $attachment_id );
    if ( ! $attachment_id ) {
        return '';
    }

    return wp_get_attachment_image( $attachment_id, $size );
}

function foldery_atelier_render_artwork_control( $artwork ) {
    $artwork = foldery_atelier_normalize_artwork( $artwork );
    if ( ! $artwork ) {
        return '';
    }

    $attachment_id = absint( $artwork['id'] );
    $title         = get_the_title( $attachment_id );
    $title         = $title ? $title : sprintf( __( 'Image #%d', 'foldery' ), $attachment_id );

    ob_start();
    ?>
    <details class="foldery-atelier-artwork-item" data-artwork-id="<?php echo esc_attr( $attachment_id ); ?>">
        <summary class="foldery-atelier-artwork-summary">
            <span class="foldery-atelier-artwork-tools">
                <span class="foldery-atelier-artwork-handle" aria-label="<?php esc_attr_e( 'Reordonner', 'foldery' ); ?>">
                    <span class="dashicons dashicons-menu" aria-hidden="true"></span>
                </span>
                <button type="button" class="button-link foldery-atelier-artwork-remove" aria-label="<?php esc_attr_e( 'Retirer cette image', 'foldery' ); ?>">
                    <span class="dashicons dashicons-trash" aria-hidden="true"></span>
                </button>
            </span>
            <span class="foldery-atelier-artwork-thumb">
                <?php echo foldery_atelier_media_preview( $attachment_id ); ?>
            </span>
            <span class="foldery-atelier-artwork-title"><?php echo esc_html( $title ); ?></span>
        </summary>
        <div class="foldery-atelier-artwork-main">
            <label class="foldery-atelier-artwork-range">
                <span><?php esc_html_e( 'Proportion', 'foldery' ); ?> <output><?php echo esc_html( $artwork['scale'] ); ?>%</output></span>
                <input type="range" class="foldery-atelier-artwork-scale" min="55" max="145" step="5" value="<?php echo esc_attr( $artwork['scale'] ); ?>">
            </label>
            <label class="foldery-atelier-artwork-range">
                <span><?php esc_html_e( 'Scotch', 'foldery' ); ?> <output><?php echo esc_html( $artwork['tapes'] ); ?></output></span>
                <input type="range" class="foldery-atelier-artwork-tapes" min="0" max="5" step="1" value="<?php echo esc_attr( $artwork['tapes'] ); ?>">
            </label>
        </div>
    </details>
    <?php

    return ob_get_clean();
}

function foldery_atelier_render_meta_box( $post ) {
    $hero_image_id = absint( foldery_atelier_get_meta( $post->ID, FOLDERY_ATELIER_HERO_IMAGE_META ) );
    $title         = (string) foldery_atelier_get_meta( $post->ID, FOLDERY_ATELIER_TITLE_META );
    $subtitle      = (string) foldery_atelier_get_meta( $post->ID, FOLDERY_ATELIER_SUBTITLE_META );
    $artworks      = foldery_atelier_parse_artworks( foldery_atelier_get_meta( $post->ID, FOLDERY_ATELIER_ARTWORKS_META ) );
    $overlay       = foldery_atelier_overlay_settings( $post->ID );

    wp_nonce_field( 'foldery_atelier_save_meta', 'foldery_atelier_nonce' );
    ?>
    <div class="foldery-atelier-fields">
        <p>
            <label for="foldery-atelier-title"><strong><?php esc_html_e( 'Titre du heros', 'foldery' ); ?></strong></label>
            <input type="text" id="foldery-atelier-title" name="foldery_atelier_title" value="<?php echo esc_attr( $title ); ?>" class="widefat" placeholder="<?php echo esc_attr( get_the_title( $post ) ); ?>">
        </p>
        <p>
            <label for="foldery-atelier-subtitle"><strong><?php esc_html_e( 'Sous-titre', 'foldery' ); ?></strong></label>
            <textarea id="foldery-atelier-subtitle" name="foldery_atelier_subtitle" rows="3" class="widefat"><?php echo esc_textarea( $subtitle ); ?></textarea>
        </p>

        <div class="foldery-atelier-overlay-controls">
            <strong><?php esc_html_e( 'Lisibilite du heros', 'foldery' ); ?></strong>
            <label class="foldery-atelier-control-row" for="foldery-atelier-overlay-color">
                <span><?php esc_html_e( 'Couleur', 'foldery' ); ?></span>
                <input type="color" id="foldery-atelier-overlay-color" name="foldery_atelier_overlay_color" value="<?php echo esc_attr( $overlay['color'] ); ?>">
            </label>
            <label class="foldery-atelier-control-row">
                <span><?php esc_html_e( 'Opacite', 'foldery' ); ?> <output><?php echo esc_html( $overlay['opacity'] ); ?>%</output></span>
                <input type="range" class="foldery-atelier-percent-field" name="foldery_atelier_overlay_opacity" min="0" max="100" step="1" value="<?php echo esc_attr( $overlay['opacity'] ); ?>">
            </label>
            <label class="foldery-atelier-control-row">
                <span><?php esc_html_e( 'Vignettage', 'foldery' ); ?> <output><?php echo esc_html( $overlay['vignette'] ); ?>%</output></span>
                <input type="range" class="foldery-atelier-percent-field" name="foldery_atelier_vignette" min="0" max="100" step="1" value="<?php echo esc_attr( $overlay['vignette'] ); ?>">
            </label>
        </div>

        <div class="foldery-atelier-media-field" data-foldery-media-field="single">
            <strong><?php esc_html_e( 'Image du bureau', 'foldery' ); ?></strong>
            <input type="hidden" name="foldery_atelier_hero_image_id" value="<?php echo esc_attr( $hero_image_id ); ?>">
            <div class="foldery-atelier-media-preview">
                <?php echo foldery_atelier_media_preview( $hero_image_id ); ?>
            </div>
            <p class="foldery-atelier-media-actions">
                <button type="button" class="button foldery-atelier-choose-media" data-title="<?php esc_attr_e( "Choisir l'image du bureau", 'foldery' ); ?>" data-button="<?php esc_attr_e( 'Utiliser cette image', 'foldery' ); ?>"><?php esc_html_e( 'Choisir', 'foldery' ); ?></button>
                <button type="button" class="button-link-delete foldery-atelier-clear-media"><?php esc_html_e( 'Retirer', 'foldery' ); ?></button>
            </p>
        </div>

        <div class="foldery-atelier-media-field" data-foldery-media-field="multiple">
            <strong><?php esc_html_e( "Oeuvres posees sur l'image", 'foldery' ); ?></strong>
            <input type="hidden" name="foldery_atelier_artwork_ids" value="<?php echo esc_attr( foldery_atelier_sanitize_artworks_meta( $artworks ) ); ?>">
            <div class="foldery-atelier-artwork-list" data-foldery-atelier-artworks>
                <?php foreach ( $artworks as $artwork ) : ?>
                    <?php echo foldery_atelier_render_artwork_control( $artwork ); ?>
                <?php endforeach; ?>
            </div>
            <p class="foldery-atelier-media-actions">
                <button type="button" class="button foldery-atelier-choose-media" data-title="<?php esc_attr_e( 'Choisir les oeuvres', 'foldery' ); ?>" data-button="<?php esc_attr_e( 'Utiliser ces oeuvres', 'foldery' ); ?>"><?php esc_html_e( 'Choisir les oeuvres', 'foldery' ); ?></button>
                <button type="button" class="button-link-delete foldery-atelier-clear-media"><?php esc_html_e( 'Retirer', 'foldery' ); ?></button>
            </p>
            <p class="description"><?php esc_html_e( 'Les six premieres images seront affichees dans le heros.', 'foldery' ); ?></p>
        </div>
    </div>
    <?php
}

function foldery_atelier_save_meta( $post_id ) {
    if ( ! isset( $_POST['foldery_atelier_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['foldery_atelier_nonce'] ) ), 'foldery_atelier_save_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
    }

    $title = isset( $_POST['foldery_atelier_title'] ) ? sanitize_text_field( wp_unslash( $_POST['foldery_atelier_title'] ) ) : '';
    update_post_meta( $post_id, FOLDERY_ATELIER_TITLE_META, $title );

    $subtitle = isset( $_POST['foldery_atelier_subtitle'] ) ? sanitize_textarea_field( wp_unslash( $_POST['foldery_atelier_subtitle'] ) ) : '';
    update_post_meta( $post_id, FOLDERY_ATELIER_SUBTITLE_META, $subtitle );

    $overlay_defaults = foldery_atelier_default_overlay_settings();

    $overlay_color = isset( $_POST['foldery_atelier_overlay_color'] ) ? foldery_atelier_sanitize_overlay_color( wp_unslash( $_POST['foldery_atelier_overlay_color'] ) ) : $overlay_defaults['color'];
    update_post_meta( $post_id, FOLDERY_ATELIER_OVERLAY_COLOR_META, $overlay_color );

    $overlay_opacity = isset( $_POST['foldery_atelier_overlay_opacity'] ) ? foldery_atelier_sanitize_percent_meta( wp_unslash( $_POST['foldery_atelier_overlay_opacity'] ) ) : (string) $overlay_defaults['opacity'];
    update_post_meta( $post_id, FOLDERY_ATELIER_OVERLAY_OPACITY_META, $overlay_opacity );

    $vignette = isset( $_POST['foldery_atelier_vignette'] ) ? foldery_atelier_sanitize_percent_meta( wp_unslash( $_POST['foldery_atelier_vignette'] ) ) : (string) $overlay_defaults['vignette'];
    update_post_meta( $post_id, FOLDERY_ATELIER_VIGNETTE_META, $vignette );

    $hero_image_id = isset( $_POST['foldery_atelier_hero_image_id'] ) ? absint( $_POST['foldery_atelier_hero_image_id'] ) : 0;
    update_post_meta( $post_id, FOLDERY_ATELIER_HERO_IMAGE_META, $hero_image_id ? (string) $hero_image_id : '' );

    $artworks = isset( $_POST['foldery_atelier_artwork_ids'] ) ? foldery_atelier_sanitize_artworks_meta( wp_unslash( $_POST['foldery_atelier_artwork_ids'] ) ) : '';
    update_post_meta( $post_id, FOLDERY_ATELIER_ARTWORKS_META, $artworks );
}
add_action( 'save_post_page', 'foldery_atelier_save_meta' );

function foldery_atelier_admin_assets( $hook ) {
    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen || 'page' !== $screen->post_type ) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script( 'foldery-atelier-admin', get_template_directory_uri() . '/assets/admin/atelier-hero.js', array( 'jquery', 'jquery-ui-sortable' ), FOLDERY_VERSION, true );
    wp_localize_script(
        'foldery-atelier-admin',
        'FolderyAtelierAdmin',
        array(
            'metaKeys' => array(
                'foldery_atelier_hero_image_id' => FOLDERY_ATELIER_HERO_IMAGE_META,
                'foldery_atelier_title'         => FOLDERY_ATELIER_TITLE_META,
                'foldery_atelier_subtitle'      => FOLDERY_ATELIER_SUBTITLE_META,
                'foldery_atelier_artwork_ids'   => FOLDERY_ATELIER_ARTWORKS_META,
                'foldery_atelier_overlay_color' => FOLDERY_ATELIER_OVERLAY_COLOR_META,
                'foldery_atelier_overlay_opacity' => FOLDERY_ATELIER_OVERLAY_OPACITY_META,
                'foldery_atelier_vignette'      => FOLDERY_ATELIER_VIGNETTE_META,
            ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'foldery_atelier_admin_assets' );
