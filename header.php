<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'ltr cms-header-v1 cms-custom-vc-row-stretch-content' ); ?>>
<?php wp_body_open(); ?>
<?php
$foldery_logo_url = '';
$custom_logo_id   = get_theme_mod( 'custom_logo' );

if ( $custom_logo_id ) {
    $foldery_logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
}

if ( ! $foldery_logo_url ) {
    $legacy_options = get_option( 'smof_data' );
    $foldery_logo_url = $legacy_options['main_logo']['url'] ?? '';
}

if ( ! $foldery_logo_url ) {
    $foldery_logo_url = content_url( 'uploads/logo-pt.png' );
}

$foldery_menu_args = array(
    'menu_class'  => 'nav-menu menu-main-menu',
    'container'   => false,
    'fallback_cb' => false,
);

$foldery_menu_locations = get_nav_menu_locations();

if ( ! empty( $foldery_menu_locations['primary'] ) ) {
    $foldery_menu_args['theme_location'] = 'primary';
} else {
    $foldery_menu_args['menu'] = 'Main menu';
}
?>

<div id="cms-page" class="cs-wide header-v1 header-left clearfix">
    <section id="cms-header-wrapper" class="clearfix">
        <header id="masthead" class="site-header header-v1 header-left clearfix" role="banner">
            <div id="cms-header" class="cms-header header-v1 clearfix">
                <div id="cms-header-inner">
                    <div id="cms-header-logo">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <img alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" src="<?php echo esc_url( $foldery_logo_url ); ?>">
                        </a>
                    </div>
                    <div id="cms-header-navigation">
                        <nav id="site-navigation" class="main-navigation" role="navigation">
                            <?php
                            wp_nav_menu( $foldery_menu_args );
                            ?>
                        </nav>
                    </div>
                    <div id="cms-nav-extra" class="cms-nav-extra main-navigation">
                        <div id="cms-menu-mobile" class="pull-left"><ul><li><a><i class="fa fa-bars"></i></a></li></ul></div>
                    </div>
                </div>
            </div>
        </header>
    </section>
    <section id="cms-content-wrapper" class="<?php echo is_front_page() ? 'home' : ''; ?> clearfix">
