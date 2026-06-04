<?php
/**
 * Theme settings for editable global content and behavior.
 */

if ( ! defined( 'FOLDERY_THEME_SETTINGS_OPTION' ) ) {
    define( 'FOLDERY_THEME_SETTINGS_OPTION', 'foldery_theme_settings' );
}

function foldery_theme_settings_defaults() {
    return array(
        'artist_name'       => 'JACQMIN SEBASTIEN',
        'artist_baseline'   => 'Artiste travaillant en Bretagne - France',
        'phone'             => '+33.(0)6.20.72.60.10',
        'email'             => 'contact@sebastienj.com',
        'social_links'      => "Instagram : sebastienjacqmin|https://www.instagram.com/sebastienjacqmin/",
        'header_link_label' => 'Mes reproductions...',
        'header_link_url'   => '',
        'lightbox'          => array(
            'enabled'            => 1,
            'groupLinks'         => 1,
            'groupByPost'        => 1,
            'groupGallery'       => 0,
            'loop'               => 1,
            'autofit'            => 1,
            'animate'            => 1,
            'overlayOpacity'     => 0.8,
            'titleDefault'       => 0,
            'slideshowAutostart' => 0,
            'slideshowDuration'  => 6,
        ),
    );
}

function foldery_theme_settings() {
    $settings = get_option( FOLDERY_THEME_SETTINGS_OPTION, array() );
    if ( ! is_array( $settings ) ) {
        $settings = array();
    }

    return array_replace_recursive( foldery_theme_settings_defaults(), $settings );
}

function foldery_theme_sanitize_bool( $value ) {
    return empty( $value ) ? 0 : 1;
}

function foldery_theme_sanitize_settings( $input ) {
    $input    = is_array( $input ) ? $input : array();
    $defaults = foldery_theme_settings_defaults();
    $current  = get_option( FOLDERY_THEME_SETTINGS_OPTION, array() );
    $current  = is_array( $current ) ? $current : array();

    $settings = array_replace_recursive( $defaults, $current );

    foreach ( array( 'artist_name', 'artist_baseline', 'phone', 'header_link_label' ) as $key ) {
        if ( isset( $input[ $key ] ) ) {
            $settings[ $key ] = sanitize_text_field( wp_unslash( $input[ $key ] ) );
        }
    }

    if ( isset( $input['email'] ) ) {
        $settings['email'] = sanitize_email( wp_unslash( $input['email'] ) );
    }
    if ( isset( $input['social_links'] ) ) {
        $settings['social_links'] = sanitize_textarea_field( wp_unslash( $input['social_links'] ) );
    }
    if ( isset( $input['header_link_url'] ) ) {
        $settings['header_link_url'] = esc_url_raw( wp_unslash( $input['header_link_url'] ) );
    }

    if ( isset( $input['lightbox'] ) && is_array( $input['lightbox'] ) ) {
        $lightbox = $input['lightbox'];
        foreach ( array( 'enabled', 'groupLinks', 'groupByPost', 'groupGallery', 'loop', 'autofit', 'animate', 'titleDefault', 'slideshowAutostart' ) as $key ) {
            $settings['lightbox'][ $key ] = foldery_theme_sanitize_bool( $lightbox[ $key ] ?? 0 );
        }

        $settings['lightbox']['overlayOpacity'] = isset( $lightbox['overlayOpacity'] )
            ? min( 1, max( 0, (float) $lightbox['overlayOpacity'] ) )
            : $settings['lightbox']['overlayOpacity'];
        $settings['lightbox']['slideshowDuration'] = isset( $lightbox['slideshowDuration'] )
            ? max( 1, absint( $lightbox['slideshowDuration'] ) )
            : $settings['lightbox']['slideshowDuration'];
    }

    return $settings;
}

function foldery_theme_register_settings() {
    register_setting(
        'foldery_theme_settings',
        FOLDERY_THEME_SETTINGS_OPTION,
        array(
            'type'              => 'array',
            'sanitize_callback' => 'foldery_theme_sanitize_settings',
            'default'           => foldery_theme_settings_defaults(),
        )
    );
}
add_action( 'admin_init', 'foldery_theme_register_settings' );

function foldery_theme_settings_page() {
    add_theme_page(
        __( 'Reglages Foldery', 'foldery' ),
        __( 'Reglages Foldery', 'foldery' ),
        'manage_options',
        'foldery-theme-settings',
        'foldery_theme_render_settings_page'
    );
}
add_action( 'admin_menu', 'foldery_theme_settings_page' );

function foldery_theme_active_settings_tab() {
    $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'artist';
    return in_array( $tab, array( 'artist', 'lightbox' ), true ) ? $tab : 'artist';
}

function foldery_theme_setting_name( $key ) {
    return FOLDERY_THEME_SETTINGS_OPTION . '[' . $key . ']';
}

function foldery_theme_lightbox_setting_name( $key ) {
    return FOLDERY_THEME_SETTINGS_OPTION . '[lightbox][' . $key . ']';
}

function foldery_theme_render_text_field( $settings, $key, $label, $type = 'text', $description = '' ) {
    printf(
        '<tr><th scope="row"><label for="foldery-%1$s">%2$s</label></th><td><input class="regular-text" type="%3$s" id="foldery-%1$s" name="%4$s" value="%5$s">%6$s</td></tr>',
        esc_attr( $key ),
        esc_html( $label ),
        esc_attr( $type ),
        esc_attr( foldery_theme_setting_name( $key ) ),
        esc_attr( $settings[ $key ] ?? '' ),
        $description ? '<p class="description">' . esc_html( $description ) . '</p>' : ''
    );
}

function foldery_theme_render_checkbox_field( $settings, $key, $label ) {
    printf(
        '<tr><th scope="row">%1$s</th><td><label><input type="checkbox" name="%2$s" value="1" %3$s> %4$s</label></td></tr>',
        esc_html( $label ),
        esc_attr( foldery_theme_lightbox_setting_name( $key ) ),
        checked( ! empty( $settings['lightbox'][ $key ] ), true, false ),
        esc_html__( 'Actif', 'foldery' )
    );
}

function foldery_theme_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $settings = foldery_theme_settings();
    $tab      = foldery_theme_active_settings_tab();
    ?>
    <div class="wrap foldery-settings">
        <h1><?php esc_html_e( 'Reglages Foldery', 'foldery' ); ?></h1>

        <h2 class="nav-tab-wrapper">
            <a class="nav-tab <?php echo 'artist' === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'themes.php?page=foldery-theme-settings&tab=artist' ) ); ?>"><?php esc_html_e( 'Artiste', 'foldery' ); ?></a>
            <a class="nav-tab <?php echo 'lightbox' === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'themes.php?page=foldery-theme-settings&tab=lightbox' ) ); ?>"><?php esc_html_e( 'Lightbox', 'foldery' ); ?></a>
        </h2>

        <form method="post" action="options.php">
            <?php settings_fields( 'foldery_theme_settings' ); ?>

            <?php if ( 'artist' === $tab ) : ?>
                <table class="form-table" role="presentation">
                    <?php
                    foldery_theme_render_text_field( $settings, 'artist_name', __( 'Nom de l artiste', 'foldery' ) );
                    foldery_theme_render_text_field( $settings, 'artist_baseline', __( 'Description courte', 'foldery' ) );
                    foldery_theme_render_text_field( $settings, 'phone', __( 'Telephone', 'foldery' ), 'text' );
                    foldery_theme_render_text_field( $settings, 'email', __( 'Email', 'foldery' ), 'email' );
                    foldery_theme_render_text_field( $settings, 'header_link_label', __( 'Libelle du lien principal', 'foldery' ) );
                    foldery_theme_render_text_field( $settings, 'header_link_url', __( 'URL du lien principal', 'foldery' ), 'url' );
                    ?>
                    <tr>
                        <th scope="row"><label for="foldery-social-links"><?php esc_html_e( 'Reseaux sociaux', 'foldery' ); ?></label></th>
                        <td>
                            <textarea class="large-text code" rows="5" id="foldery-social-links" name="<?php echo esc_attr( foldery_theme_setting_name( 'social_links' ) ); ?>"><?php echo esc_textarea( $settings['social_links'] ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'Un lien par ligne, format: Libelle|URL. Le libelle seul fonctionne aussi.', 'foldery' ); ?></p>
                        </td>
                    </tr>
                </table>
            <?php else : ?>
                <table class="form-table" role="presentation">
                    <?php
                    foreach ( array(
                        'enabled'            => __( 'Activer la lightbox', 'foldery' ),
                        'groupLinks'         => __( 'Grouper les liens image', 'foldery' ),
                        'groupByPost'        => __( 'Grouper par contenu', 'foldery' ),
                        'groupGallery'       => __( 'Grouper les galeries', 'foldery' ),
                        'loop'               => __( 'Boucler la navigation', 'foldery' ),
                        'autofit'            => __( 'Adapter l image a l ecran', 'foldery' ),
                        'animate'            => __( 'Animations', 'foldery' ),
                        'titleDefault'       => __( 'Afficher le titre par defaut', 'foldery' ),
                        'slideshowAutostart' => __( 'Lancer le diaporama automatiquement', 'foldery' ),
                    ) as $key => $label ) {
                        foldery_theme_render_checkbox_field( $settings, $key, $label );
                    }
                    ?>
                    <tr>
                        <th scope="row"><label for="foldery-overlay-opacity"><?php esc_html_e( 'Opacite du fond', 'foldery' ); ?></label></th>
                        <td><input type="number" step="0.05" min="0" max="1" id="foldery-overlay-opacity" name="<?php echo esc_attr( foldery_theme_lightbox_setting_name( 'overlayOpacity' ) ); ?>" value="<?php echo esc_attr( $settings['lightbox']['overlayOpacity'] ); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="foldery-slideshow-duration"><?php esc_html_e( 'Duree du diaporama', 'foldery' ); ?></label></th>
                        <td><input type="number" step="1" min="1" id="foldery-slideshow-duration" name="<?php echo esc_attr( foldery_theme_lightbox_setting_name( 'slideshowDuration' ) ); ?>" value="<?php echo esc_attr( $settings['lightbox']['slideshowDuration'] ); ?>"> <?php esc_html_e( 'secondes', 'foldery' ); ?></td>
                    </tr>
                </table>
            <?php endif; ?>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
