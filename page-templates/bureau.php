<?php
/**
 * Template Name: Bureau
 * Template Post Type: page
 */

$bureau_background_url = foldery_make_relative_dev_url( content_url( 'uploads/IMG_7945-scaled.jpg' ) );
$bureau_logo_url       = foldery_get_site_logo_url();
$bureau_contact        = get_theme_mod( 'foldery_bureau_contact', get_option( 'admin_email' ) );
$bureau_address        = get_theme_mod( 'foldery_bureau_address', '' );
$bureau_header_block   = foldery_render_bureau_header_block();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'bureau-template ltr' ); ?>>
<?php wp_body_open(); ?>

<div id="cms-page" class="bureau-page" style="--bureau-bg: url('<?php echo esc_url( $bureau_background_url ); ?>');">
    <div class="bureau-stage">
        <header class="bureau-header <?php echo $bureau_header_block ? 'bureau-header--shared' : 'bureau-header--fallback'; ?>" role="banner">
            <?php if ( $bureau_header_block ) : ?>
                <div class="bureau-header-content entry-content">
                    <?php echo $bureau_header_block; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            <?php else : ?>
                <div class="bureau-logo-paper" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                    <a href="<?php echo esc_url( foldery_make_relative_dev_url( home_url( '/' ) ) ); ?>">
                        <img src="<?php echo esc_url( $bureau_logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                    </a>
                </div>

                <div class="bureau-info-paper">
                    <p class="bureau-site-name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
                    <div class="bureau-site-meta">
                        <?php if ( get_bloginfo( 'description' ) ) : ?>
                            <span><?php echo esc_html( get_bloginfo( 'description' ) ); ?></span>
                        <?php endif; ?>
                        <?php if ( $bureau_address ) : ?>
                            <span><?php echo esc_html( $bureau_address ); ?></span>
                        <?php endif; ?>
                        <?php if ( $bureau_contact ) : ?>
                            <span><?php echo esc_html( $bureau_contact ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </header>

        <nav class="bureau-navigation main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Navigation principale', 'foldery' ); ?>">
            <?php wp_nav_menu( foldery_get_primary_menu_args( 'bureau-menu nav-menu' ) ); ?>
        </nav>

        <?php while ( have_posts() ) : the_post(); ?>
            <?php $bureau_content = foldery_bureau_split_content( get_the_content( null, false, get_the_ID() ) ); ?>
            <main id="main" class="bureau-board" role="main">
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'bureau-sheet bureau-sheet--main' ); ?>>
                    <span class="bureau-sheet-tab"><?php the_title(); ?></span>
                    <div class="bureau-sheet-content entry-content">
                        <?php echo $bureau_content['main']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                </article>

                <aside class="bureau-sheet bureau-sheet--side" aria-label="<?php esc_attr_e( 'Contenu lateral', 'foldery' ); ?>">
                    <span class="bureau-sheet-tab"><?php esc_html_e( 'Notes', 'foldery' ); ?></span>
                    <div class="bureau-sheet-content entry-content">
                        <?php echo $bureau_content['sidebar']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                </aside>
            </main>
        <?php endwhile; ?>
    </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
