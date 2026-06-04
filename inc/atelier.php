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

function foldery_atelier_meta_keys() {
    return array(
        FOLDERY_ATELIER_HERO_IMAGE_META,
        FOLDERY_ATELIER_TITLE_META,
        FOLDERY_ATELIER_SUBTITLE_META,
        FOLDERY_ATELIER_ARTWORKS_META,
    );
}

function foldery_atelier_parse_ids( $ids ) {
    if ( is_array( $ids ) ) {
        $raw = $ids;
    } else {
        $raw = explode( ',', (string) $ids );
    }

    return array_values( array_unique( array_filter( array_map( 'absint', $raw ) ) ) );
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
        register_post_meta(
            'page',
            $key,
            array(
                'single'            => true,
                'show_in_rest'      => true,
                'auth_callback'     => function() {
                    return current_user_can( 'edit_pages' );
                },
                'sanitize_callback' => in_array( $key, array( FOLDERY_ATELIER_HERO_IMAGE_META, FOLDERY_ATELIER_ARTWORKS_META ), true )
                    ? 'foldery_atelier_sanitize_ids_meta'
                    : 'sanitize_text_field',
                'type'              => 'string',
            )
        );
    }
}
add_action( 'init', 'foldery_atelier_register_meta' );

function foldery_atelier_sanitize_ids_meta( $value ) {
    return implode( ',', foldery_atelier_parse_ids( $value ) );
}

function foldery_atelier_render_linked_image( $attachment_id, $index ) {
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

    $full = wp_get_attachment_image_url( $attachment_id, 'full' );
    if ( ! $full ) {
        return '<figure class="atelier-hero-artwork atelier-hero-artwork--' . (int) ( $index + 1 ) . '">' . $image . '</figure>';
    }

    $title = get_the_title( $attachment_id );
    $html  = sprintf(
        '<figure class="atelier-hero-artwork atelier-hero-artwork--%1$d"><a href="%2$s" title="%3$s">%4$s</a></figure>',
        (int) ( $index + 1 ),
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
    $artworks = foldery_atelier_parse_ids( foldery_atelier_get_meta( $post_id, FOLDERY_ATELIER_ARTWORKS_META ) );

    if ( '' === $title ) {
        $title = get_the_title( $post_id );
    }

    ob_start();
    ?>
    <section class="atelier-hero" style="<?php echo esc_attr( '--atelier-hero-image: url("' . esc_url( $hero_url ) . '");' ); ?>" aria-label="<?php esc_attr_e( 'Atelier', 'foldery' ); ?>">
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
                    foreach ( array_slice( $artworks, 0, 6 ) as $index => $artwork_id ) {
                        echo foldery_atelier_render_linked_image( $artwork_id, $index );
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

function foldery_atelier_render_meta_box( $post ) {
    $hero_image_id = absint( foldery_atelier_get_meta( $post->ID, FOLDERY_ATELIER_HERO_IMAGE_META ) );
    $title         = (string) foldery_atelier_get_meta( $post->ID, FOLDERY_ATELIER_TITLE_META );
    $subtitle      = (string) foldery_atelier_get_meta( $post->ID, FOLDERY_ATELIER_SUBTITLE_META );
    $artwork_ids   = foldery_atelier_parse_ids( foldery_atelier_get_meta( $post->ID, FOLDERY_ATELIER_ARTWORKS_META ) );

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
            <input type="hidden" name="foldery_atelier_artwork_ids" value="<?php echo esc_attr( implode( ',', $artwork_ids ) ); ?>">
            <div class="foldery-atelier-media-preview foldery-atelier-media-preview--grid">
                <?php foreach ( $artwork_ids as $artwork_id ) : ?>
                    <?php echo foldery_atelier_media_preview( $artwork_id ); ?>
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

    $hero_image_id = isset( $_POST['foldery_atelier_hero_image_id'] ) ? absint( $_POST['foldery_atelier_hero_image_id'] ) : 0;
    update_post_meta( $post_id, FOLDERY_ATELIER_HERO_IMAGE_META, $hero_image_id ? (string) $hero_image_id : '' );

    $artwork_ids = isset( $_POST['foldery_atelier_artwork_ids'] ) ? foldery_atelier_sanitize_ids_meta( wp_unslash( $_POST['foldery_atelier_artwork_ids'] ) ) : '';
    update_post_meta( $post_id, FOLDERY_ATELIER_ARTWORKS_META, $artwork_ids );
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
    wp_enqueue_script( 'foldery-atelier-admin', get_template_directory_uri() . '/assets/admin/atelier-hero.js', array( 'jquery' ), FOLDERY_VERSION, true );
    wp_localize_script(
        'foldery-atelier-admin',
        'FolderyAtelierAdmin',
        array(
            'metaKeys' => array(
                'foldery_atelier_hero_image_id' => FOLDERY_ATELIER_HERO_IMAGE_META,
                'foldery_atelier_title'         => FOLDERY_ATELIER_TITLE_META,
                'foldery_atelier_subtitle'      => FOLDERY_ATELIER_SUBTITLE_META,
                'foldery_atelier_artwork_ids'   => FOLDERY_ATELIER_ARTWORKS_META,
            ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'foldery_atelier_admin_assets' );
