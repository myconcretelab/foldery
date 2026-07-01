<?php
/**
 * Dynamic global header.
 */

function foldery_header_social_links( $raw = null ) {
    if ( null === $raw ) {
        $settings = function_exists( 'foldery_theme_settings' ) ? foldery_theme_settings() : array();
        $raw      = isset( $settings['social_links'] ) ? (string) $settings['social_links'] : '';
    }

    $links = array();

    foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
        $line = trim( $line );
        if ( '' === $line ) {
            continue;
        }

        $parts   = array_map( 'trim', explode( '|', $line, 2 ) );
        $links[] = array(
            'label' => $parts[0],
            'url'   => isset( $parts[1] ) ? $parts[1] : '',
        );
    }

    return $links;
}

function foldery_site_logo_id() {
    $site_logo_id = get_option( 'site_logo', false );

    if ( false !== $site_logo_id ) {
        return absint( $site_logo_id );
    }

    return absint( get_theme_mod( 'custom_logo' ) );
}

function foldery_header_logo_html() {
    $logo_id = foldery_site_logo_id();
    $image   = $logo_id ? wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => 'foldery-paper-header__logo-image' ) ) : '';

    if ( '' === $image ) {
        $fallback = get_template_directory_uri() . '/assets/images/logo.png';
        $image    = '<img class="foldery-paper-header__logo-image" src="' . esc_url( $fallback ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
    }

    return '<a class="foldery-paper-header__logo" href="' . esc_url( home_url( '/' ) ) . '" aria-label="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . $image . '</a>';
}

function foldery_header_menu_html( $attributes ) {
    if ( function_exists( 'foldery_explorer_render_menu_block' ) ) {
        $settings   = function_exists( 'foldery_theme_settings' ) ? foldery_theme_settings() : array();
        $folder_ids = isset( $attributes['menuFolderIds'] ) ? trim( (string) $attributes['menuFolderIds'] ) : '';
        if ( '' === $folder_ids && ! empty( $settings['header_menu_folder_ids'] ) ) {
            $folder_ids = (string) $settings['header_menu_folder_ids'];
        }

        return foldery_explorer_render_menu_block(
            array(
                'folderIds'    => $folder_ids,
                'showSubmenus' => ! empty( $attributes['showSubmenus'] ),
                'ariaLabel'    => isset( $attributes['ariaLabel'] ) ? $attributes['ariaLabel'] : __( 'Menu principal', 'foldery' ),
                'scrollToExplorer' => ! empty( $attributes['scrollToExplorer'] ),
                'className'    => 'foldery-paper-header__menu',
            )
        );
    }

    if ( has_nav_menu( 'primary' ) ) {
        return wp_nav_menu(
            array(
                'theme_location' => 'primary',
                'container'      => 'nav',
                'container_class'=> 'foldery-paper-header__menu',
                'echo'           => false,
                'fallback_cb'    => false,
            )
        );
    }

    return '';
}

function foldery_header_attribute_or_setting( $attributes, $attribute_key, $settings, $settings_key, $fallback = '' ) {
    if ( array_key_exists( $attribute_key, $attributes ) ) {
        return (string) $attributes[ $attribute_key ];
    }

    return isset( $settings[ $settings_key ] ) ? (string) $settings[ $settings_key ] : $fallback;
}

function foldery_header_render_link_or_text( $label, $url, $class_name = '' ) {
    if ( '' === $label ) {
        return '';
    }

    if ( '' !== $url ) {
        return '<a class="' . esc_attr( $class_name ) . '" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
    }

    return '<span class="' . esc_attr( $class_name ) . '">' . esc_html( $label ) . '</span>';
}

function foldery_header_render_paragraph_lines( $lines ) {
    $lines = array_values( array_filter( $lines ) );

    if ( empty( $lines ) ) {
        return '';
    }

    return '<p>' . implode( '<br>', $lines ) . '</p>';
}

function foldery_render_site_header_block( $attributes ) {
    $settings = function_exists( 'foldery_theme_settings' ) ? foldery_theme_settings() : array();
    $classes  = 'foldery-paper-header';
    if ( ! empty( $attributes['className'] ) ) {
        $extra_classes = array_filter( array_map( 'sanitize_html_class', preg_split( '/\s+/', $attributes['className'] ) ) );
        $classes      .= ' ' . implode( ' ', $extra_classes );
    }

    $artist_name     = foldery_header_attribute_or_setting( $attributes, 'artistName', $settings, 'artist_name', get_bloginfo( 'name' ) );
    $artist_baseline = foldery_header_attribute_or_setting( $attributes, 'artistBaseline', $settings, 'artist_baseline', get_bloginfo( 'description' ) );
    $phone           = foldery_header_attribute_or_setting( $attributes, 'phone', $settings, 'phone' );
    $email           = foldery_header_attribute_or_setting( $attributes, 'email', $settings, 'email', get_option( 'admin_email' ) );
    $social_links    = foldery_header_attribute_or_setting( $attributes, 'socialLinks', $settings, 'social_links' );
    $action_label    = foldery_header_attribute_or_setting( $attributes, 'actionLabel', $settings, 'header_link_label' );
    $action_url      = foldery_header_attribute_or_setting( $attributes, 'actionUrl', $settings, 'header_link_url' );
    $menu            = foldery_header_menu_html( $attributes );

    ob_start();
    ?>
    <header class="<?php echo esc_attr( $classes ); ?>" data-foldery-paper-header>
        <div class="foldery-paper-header__paper">
            <?php echo foldery_header_logo_html(); ?>
            <div class="foldery-paper-header__content">
                <section class="foldery-paper-header__column foldery-paper-header__column--artist" aria-label="<?php esc_attr_e( 'Artiste', 'foldery' ); ?>">
                    <?php if ( $artist_name ) : ?>
                        <h2><?php echo esc_html( $artist_name ); ?></h2>
                    <?php endif; ?>
                    <?php
                    $artist_lines = array();
                    if ( $artist_baseline ) {
                        $artist_lines[] = esc_html( $artist_baseline );
                    }
                    foreach ( foldery_header_social_links( $social_links ) as $link ) {
                        $artist_lines[] = foldery_header_render_link_or_text( $link['label'], $link['url'], 'foldery-paper-header__social-link' );
                    }
                    echo foldery_header_render_paragraph_lines( $artist_lines );
                    ?>
                </section>

                <section class="foldery-paper-header__column foldery-paper-header__column--contact" aria-label="<?php esc_attr_e( 'Contact', 'foldery' ); ?>">
                    <h2><?php esc_html_e( 'CONTACT', 'foldery' ); ?></h2>
                    <?php
                    echo foldery_header_render_paragraph_lines(
                        array(
                            $phone ? '<a href="' . esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a>' : '',
                            $email ? '<a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a>' : '',
                        )
                    );
                    ?>
                </section>

                <section class="foldery-paper-header__column foldery-paper-header__column--action" aria-label="<?php esc_attr_e( 'Lien principal', 'foldery' ); ?>">
                    <?php if ( $action_label ) : ?>
                        <?php echo foldery_header_render_link_or_text( $action_label, $action_url, 'foldery-paper-header__action-link' ); ?>
                    <?php endif; ?>
                </section>
            </div>
        </div>
        <?php echo $menu; ?>
    </header>
    <?php

    return ob_get_clean();
}

function foldery_register_site_header_block() {
    register_block_type(
        'foldery/site-header',
        array(
            'api_version'     => 3,
            'editor_script'   => 'foldery-blocks-editor',
            'editor_style'    => 'foldery-explorer-editor-style',
            'render_callback' => 'foldery_render_site_header_block',
            'attributes'      => array(
                'menuFolderIds'     => array( 'type' => 'string', 'default' => '' ),
                'showSubmenus'      => array( 'type' => 'boolean', 'default' => true ),
                'ariaLabel'         => array( 'type' => 'string', 'default' => 'Menu principal' ),
                'scrollToExplorer'  => array( 'type' => 'boolean', 'default' => false ),
                'className'         => array( 'type' => 'string' ),
                'artistName'     => array( 'type' => 'string' ),
                'artistBaseline' => array( 'type' => 'string' ),
                'phone'          => array( 'type' => 'string' ),
                'email'          => array( 'type' => 'string' ),
                'socialLinks'    => array( 'type' => 'string' ),
                'actionLabel'    => array( 'type' => 'string' ),
                'actionUrl'      => array( 'type' => 'string' ),
            ),
        )
    );
}
add_action( 'init', 'foldery_register_site_header_block' );
