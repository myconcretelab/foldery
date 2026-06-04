<?php
/**
 * Shared block editor assets.
 */

function foldery_register_blocks_editor_assets() {
    $custom_logo_id = absint( get_theme_mod( 'custom_logo' ) );
    $logo_url       = $custom_logo_id ? wp_get_attachment_image_url( $custom_logo_id, 'medium' ) : '';
    if ( ! $logo_url ) {
        $logo_url = get_template_directory_uri() . '/assets/images/logo.png';
    }

    wp_register_script(
        'foldery-blocks-editor',
        get_template_directory_uri() . '/assets/js/foldery-blocks-editor.js',
        array(
            'wp-blocks',
            'wp-block-editor',
            'wp-components',
            'wp-core-data',
            'wp-data',
            'wp-element',
            'wp-i18n',
        ),
        FOLDERY_VERSION,
        true
    );

    wp_localize_script(
        'foldery-blocks-editor',
        'FolderyBlocksEditor',
        array(
            'atelierMetaKeys' => array(
                'heroImage' => defined( 'FOLDERY_ATELIER_HERO_IMAGE_META' ) ? FOLDERY_ATELIER_HERO_IMAGE_META : '_foldery_atelier_hero_image_id',
                'title'     => defined( 'FOLDERY_ATELIER_TITLE_META' ) ? FOLDERY_ATELIER_TITLE_META : '_foldery_atelier_title',
                'subtitle'  => defined( 'FOLDERY_ATELIER_SUBTITLE_META' ) ? FOLDERY_ATELIER_SUBTITLE_META : '_foldery_atelier_subtitle',
                'artworks'  => defined( 'FOLDERY_ATELIER_ARTWORKS_META' ) ? FOLDERY_ATELIER_ARTWORKS_META : '_foldery_atelier_artwork_ids',
            ),
            'fallbackHeroImageUrl' => get_template_directory_uri() . '/assets/images/bureau-bg.jpg',
            'siteHeader'           => array(
                'settings'    => function_exists( 'foldery_theme_settings' ) ? foldery_theme_settings() : array(),
                'logoUrl'     => $logo_url,
                'siteName'    => get_bloginfo( 'name' ),
                'homeUrl'     => home_url( '/' ),
                'settingsUrl' => admin_url( 'themes.php?page=foldery-theme-settings&tab=artist' ),
                'folders'     => function_exists( 'foldery_explorer_editor_folder_tree' ) && function_exists( 'foldery_media_active' ) && foldery_media_active()
                    ? foldery_explorer_editor_folder_tree()
                    : array(),
            ),
        )
    );
}
add_action( 'init', 'foldery_register_blocks_editor_assets', 5 );
