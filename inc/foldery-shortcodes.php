<?php
/**
 * Current site shortcodes.
 */

function foldery_shortcode_atts( $defaults, $atts, $tag ) {
    return shortcode_atts( $defaults, array_change_key_case( (array) $atts, CASE_LOWER ), $tag );
}

function foldery_resolve_folder_from_value( $value ) {
    if ( foldery_is_media_folder( $value ) ) {
        return $value;
    }

    if ( is_numeric( $value ) ) {
        return foldery_media_get_folder( (int) $value );
    }

    return null;
}

function foldery_folder_description( $folder_id ) {
    $meta = foldery_media_get_folder_meta( $folder_id );
    if ( empty( $meta['description'][0] ) ) {
        return '';
    }

    return wpautop( esc_html( $meta['description'][0] ) );
}

function foldery_attachment_ids_from_folder( $folder ) {
    if ( ! foldery_is_media_folder( $folder ) ) {
        return array();
    }

    $ids = $folder->read();
    if ( count( $ids ) ) {
        return $ids;
    }

    foreach ( $folder->getChildren() as $child ) {
        if ( $child->getCnt() ) {
            $ids = array_merge( $ids, $child->read() );
        }
    }

    return $ids;
}

function foldery_shortcode_masonry( $atts = array(), $content = null, $tag = 'masonry' ) {
    $atts = foldery_shortcode_atts(
        array(
            'folder_id' => 0,
            'fid'       => 0,
            'ids'       => '',
            'thumbsize' => 'medium',
        ),
        $atts,
        $tag
    );

    $ids = array_filter( array_map( 'absint', explode( ',', $atts['ids'] ) ) );
    $folder_id = absint( $atts['folder_id'] ? $atts['folder_id'] : $atts['fid'] );
    if ( $folder_id ) {
        $ids = foldery_attachment_ids_from_folder( foldery_media_get_folder( $folder_id ) );
    }

    if ( ! count( $ids ) ) {
        return '';
    }

    $html = '<div class="grid foldery-masonry" data-masonry=\'{ "itemSelector": ".grid-item", "columnWidth": ".grid-item", "gutter": 20, "isFitWidth": true }\'>';
    foreach ( $ids as $id ) {
        $image = wp_get_attachment_image_src( $id, $atts['thumbsize'] );
        if ( ! $image ) {
            continue;
        }

        $shape = $image[1] === $image[2] ? 'img-sq' : ( $image[1] > $image[2] ? 'img-lg' : 'img-ht' );
        $html .= sprintf(
            '<div class="grid-item %1$s w%2$d h%3$d">%4$s</div>',
            esc_attr( $shape ),
            (int) $image[1],
            (int) $image[2],
            wp_get_attachment_link( $id, $atts['thumbsize'] )
        );
    }
    $html .= '</div><div class="clear"></div>';

    return function_exists( 'foldery_lightbox_activate' ) ? foldery_lightbox_activate( $html ) : $html;
}

function foldery_series_link( $folder ) {
    return home_url( '/serie/' . sanitize_title( $folder->getName() ) . '/' . $folder->getId() . '/' );
}

function foldery_render_series_stack( $folders ) {
    $folders = array_filter( (array) $folders, 'foldery_is_media_folder' );
    if ( ! count( $folders ) ) {
        return '';
    }

    $html = '<div class="stack-wrapper foldery-series-stack" data-masonry=\'{ "itemSelector": ".stack-item", "columnWidth": ".stack-item", "gutter": 30 }\'>';
    foreach ( $folders as $folder ) {
        $ids = foldery_attachment_ids_from_folder( $folder );
        if ( ! count( $ids ) ) {
            continue;
        }

        $image = wp_get_attachment_image_src( $ids[0], 'medium' );
        if ( ! $image ) {
            continue;
        }

        $width = $image[1] / 2;
        $height = $image[2] / 2;
        $html .= sprintf(
            '<div class="stack-item"><a href="%1$s" class="stack-link"><h5>%2$s</h5><figure class="img-area" id="img-area-%3$d" style="width:%4$dpx;height:%5$dpx">%6$s</figure><style>#img-area-%3$d:after,#img-area-%3$d:before{width:%4$dpx;height:%5$dpx}</style></a></div>',
            esc_url( foldery_series_link( $folder ) ),
            esc_html( $folder->getName() ),
            (int) $ids[0],
            (int) $width,
            (int) $height,
            wp_get_attachment_image( $ids[0], 'medium' )
        );
    }
    $html .= '</div>';

    return $html;
}

function foldery_shortcode_series( $atts = array(), $content = null, $tag = 'foldery_series' ) {
    $atts = foldery_shortcode_atts(
        array(
            'folder_id'  => 0,
            'folder_ids' => '',
            'fid'        => 0,
            'fids'       => '',
        ),
        $atts,
        $tag
    );

    $folder_ids = $atts['folder_ids'] ? $atts['folder_ids'] : $atts['fids'];
    $single_id = $atts['folder_id'] ? $atts['folder_id'] : $atts['fid'];
    $folders = array();

    if ( $folder_ids ) {
        foreach ( explode( ',', $folder_ids ) as $folder_id ) {
            $folder = foldery_resolve_folder_from_value( trim( $folder_id ) );
            if ( $folder ) {
                $folders[] = $folder;
            }
        }
    } else {
        $folder = $single_id ? foldery_resolve_folder_from_value( $single_id ) : foldery_resolve_folder_from_value( foldery_get_field( 'folder' ) );
        if ( foldery_is_media_folder( $folder ) ) {
            $folders = $folder->getChildren();
        }
    }

    return foldery_render_series_stack( $folders );
}

function foldery_shortcode_serie_detail( $atts = array(), $content = null, $tag = 'foldery_serie_detail' ) {
    $atts = foldery_shortcode_atts(
        array(
            'folder_id' => 0,
            'fid'       => 0,
        ),
        $atts,
        $tag
    );

    $folder_id = absint( $atts['folder_id'] ? $atts['folder_id'] : $atts['fid'] );
    if ( ! $folder_id ) {
        $folder_id = absint( get_query_var( 'serie_id' ) );
    }
    if ( ! $folder_id ) {
        $folder = foldery_resolve_folder_from_value( foldery_get_field( 'folder' ) );
        $folder_id = foldery_is_media_folder( $folder ) ? $folder->getId() : 0;
    }

    $folder = foldery_media_get_folder( $folder_id );
    if ( ! foldery_is_media_folder( $folder ) ) {
        return '';
    }

    $html = '<article class="foldery-serie-detail"><div class="entry-content"><div class="row"><div class="col-md-3"><h2>' . esc_html( $folder->getName() ) . '</h2></div><div class="col-md-9">' . foldery_folder_description( $folder_id ) . '</div></div></div></article>';
    $html .= '<div class="vc-zigzag-inner foldery-separator"></div>';

    if ( $folder->getCnt() ) {
        $html .= do_shortcode( '[masonry folder_id="' . (int) $folder_id . '"]' );
    }

    foreach ( $folder->getChildren() as $child ) {
        if ( ! $child->getCnt() ) {
            continue;
        }

        $html .= '<div class="vc-zigzag-inner foldery-separator"></div>';
        $html .= '<h2 class="padding">' . esc_html( $child->getName() ) . '</h2>';
        $html .= foldery_folder_description( $child->getId() );
        $html .= do_shortcode( '[masonry folder_id="' . (int) $child->getId() . '"]' );
    }

    return $html;
}

function foldery_frame_classes( $attachment_id ) {
    $frame = foldery_attachment_field( 'cadre_presentation', $attachment_id, false );
    $classes = in_array( (string) $frame, array( '15', '25', '35' ), true ) ? 'frame' : '';
    if ( '25' === (string) $frame ) {
        $classes .= ' borderless';
    }

    return trim( $classes );
}

function foldery_render_framed_attachment( $attachment_id, $ratio = 0.5 ) {
    $image = wp_get_attachment_image_src( $attachment_id, 'medium' );
    if ( ! $image ) {
        return '';
    }

    $link = wp_get_attachment_link( $attachment_id, 'medium' );
    if ( function_exists( 'foldery_lightbox_activate' ) ) {
        $link = foldery_lightbox_activate( $link );
    }

    $width = $image[1] * $ratio;
    $height = $image[2] * $ratio;
    $style_id = 'frame_' . (int) $attachment_id;

    return sprintf(
        '<figure class="%1$s" id="%2$s" style="width:%3$dpx">%4$s</figure><style>#%2$s:before{width:%5$dpx;height:%6$dpx}#%2$s:after{width:%7$dpx;height:%8$dpx}#%2$s.borderless:before{width:%3$dpx;height:%9$dpx}#%2$s.borderless:after{width:%10$dpx;height:%11$dpx}</style>',
        esc_attr( foldery_frame_classes( $attachment_id ) ),
        esc_attr( $style_id ),
        (int) $width,
        $link,
        (int) ( $width + 40 ),
        (int) ( $height + 40 ),
        (int) ( $width + 60 ),
        (int) ( $height + 60 ),
        (int) $height,
        (int) ( $width + 20 ),
        (int) ( $height + 20 )
    );
}

function foldery_shortcode_reproductions() {
    $attachments = get_posts(
        array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'   => 'reproductions_disponible',
                    'value' => '1',
                ),
            ),
        )
    );

    if ( ! count( $attachments ) ) {
        return '';
    }

    $html = '<table class="expo foldery-reproductions">';
    foreach ( $attachments as $attachment ) {
        $price = (float) foldery_attachment_field( 'prix', $attachment->ID, false );
        $frame = (float) foldery_attachment_field( 'cadre_presentation', $attachment->ID, false );
        $image = wp_get_attachment_image_src( $attachment->ID, 'medium' );
        $html .= '<tr><td>' . foldery_render_framed_attachment( $attachment->ID, 0.5 ) . '</td><td>';
        $html .= '<form id="form_' . (int) $attachment->ID . '" class="form foldery-interest-form" action="" method="post">';
        $html .= '<h4>' . esc_html( get_the_title( $attachment ) ) . '</h4>';
        $html .= wpautop( esc_html( $attachment->post_excerpt ) );

        foreach ( array( 'dimension' => __( 'Dimensions', 'foldery' ), 'papier' => __( 'Papier', 'foldery' ) ) as $key => $label ) {
            $value = foldery_attachment_field( $key, $attachment->ID, true );
            if ( $value ) {
                $html .= '<div class="entry-date"><strong>' . esc_html( $label ) . ' : </strong><em>' . esc_html( $value ) . '</em></div>';
            }
        }

        $frames = foldery_attachment_field( 'cadre_en_option', $attachment->ID, true );
        if ( is_array( $frames ) && count( $frames ) ) {
            $html .= '<div class="entry-date"><strong>' . esc_html__( 'Cadre (en option)', 'foldery' ) . ' : </strong><br>';
            foreach ( $frames as $option ) {
                $option_value = isset( $option['value'] ) ? (float) $option['value'] : 0;
                $option_label = isset( $option['label'] ) ? $option['label'] : $option_value;
                $checked = (string) $option_value === (string) $frame ? ' checked' : '';
                $html .= '<label><input type="radio" name="encadrement" value="' . esc_attr( $option_label ) . '" data-price="' . esc_attr( $option_value ) . '"' . $checked . '> ' . esc_html( $option_label ) . '</label><br>';
            }
            $html .= '</div>';
        }

        if ( $price ) {
            $html .= '<h3><strong>' . esc_html__( 'Prix', 'foldery' ) . ' : </strong><em class="prix" data-base="' . esc_attr( $price ) . '">' . esc_html( $price + $frame ) . '</em> EUR</h3>';
        }

        $html .= '<input class="form-control bccolor" placeholder="Votre Email" required value="" size="40" type="email">';
        $html .= '<input class="submit" value="Je suis interesse(e)" name="submit" type="submit">';
        $html .= '<input type="hidden" name="oeuvre" value="' . esc_attr( get_the_title( $attachment ) ) . '">';
        $html .= '<input type="hidden" name="lien" value="' . esc_url( $image ? $image[0] : '' ) . '">';
        $html .= '</form></td></tr>';
    }
    $html .= '</table>';
    $html .= foldery_reproductions_script();

    return $html;
}

function foldery_reproductions_script() {
    ob_start();
    ?>
    <script>
    jQuery(function($) {
        $('.foldery-interest-form').on('change', 'input[name="encadrement"]', function() {
            var $form = $(this).closest('form');
            var base = parseFloat($form.find('.prix').data('base') || $form.find('.prix').text()) || 0;
            var extra = parseFloat($(this).data('price')) || 0;
            $form.find('.prix').text(base + extra);
        });

        $('.foldery-interest-form').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            $.post(
                <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>,
                {
                    action: 'foldery_send_reproduction_email',
                    encadrement: $form.find('input[name="encadrement"]:checked').val(),
                    email: $form.find('input[type="email"]').val(),
                    prix: $form.find('.prix').text(),
                    oeuvre: $form.find('input[name="oeuvre"]').val(),
                    lien: $form.find('input[name="lien"]').val()
                },
                function() {
                    alert('Merci pour votre interet, je vous recontacte rapidement.');
                }
            );
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

function foldery_send_reproduction_email() {
    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    if ( ! $email ) {
        wp_send_json_error();
    }

    $message = sprintf(
        "Quelqu'un aimerait acheter une oeuvre.\n\nEmail: %s\nOeuvre: %s\nPrix: %s\nEncadrement: %s\nLien: %s",
        $email,
        isset( $_POST['oeuvre'] ) ? sanitize_text_field( wp_unslash( $_POST['oeuvre'] ) ) : '',
        isset( $_POST['prix'] ) ? sanitize_text_field( wp_unslash( $_POST['prix'] ) ) : '',
        isset( $_POST['encadrement'] ) ? sanitize_text_field( wp_unslash( $_POST['encadrement'] ) ) : '',
        isset( $_POST['lien'] ) ? esc_url_raw( wp_unslash( $_POST['lien'] ) ) : ''
    );

    wp_mail( 'contact@sebastienj.com', 'Demande reproduction', $message, array( 'Reply-To: ' . $email ) );
    wp_send_json_success();
}
add_action( 'wp_ajax_foldery_send_reproduction_email', 'foldery_send_reproduction_email' );
add_action( 'wp_ajax_nopriv_foldery_send_reproduction_email', 'foldery_send_reproduction_email' );

function foldery_shortcode_menu( $atts = array(), $content = null, $tag = 'foldery_menu' ) {
    $atts = foldery_shortcode_atts(
        array(
            'location' => 'primary',
            'class'    => 'nav-menu',
        ),
        $atts,
        $tag
    );

    return wp_nav_menu(
        array(
            'theme_location' => sanitize_key( $atts['location'] ),
            'menu_class'     => sanitize_html_class( $atts['class'] ),
            'container'      => false,
            'echo'           => false,
            'fallback_cb'    => false,
        )
    );
}

function foldery_passthrough_shortcode( $atts = array(), $content = null ) {
    return do_shortcode( shortcode_unautop( (string) $content ) );
}

function foldery_button_shortcode( $atts = array() ) {
    $atts = foldery_shortcode_atts(
        array(
            'title'        => '',
            'link'         => '',
            'button_block' => '',
            'align'        => '',
        ),
        $atts,
        'vc_btn'
    );

    $parts = array();
    foreach ( explode( '|', $atts['link'] ) as $part ) {
        $pair = explode( ':', $part, 2 );
        if ( 2 === count( $pair ) ) {
            $parts[ $pair[0] ] = rawurldecode( $pair[1] );
        }
    }

    $url = isset( $parts['url'] ) ? $parts['url'] : '#';
    $title = $atts['title'] ? $atts['title'] : ( isset( $parts['title'] ) ? $parts['title'] : '' );
    $class = 'vc_general vc_btn3';
    if ( $atts['button_block'] ) {
        $class .= ' vc_btn3-block btn-block';
    }

    $html = '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a>';
    return $atts['align'] ? '<div class="vc_btn3-container vc_btn3-' . esc_attr( $atts['align'] ) . '">' . $html . '</div>' : $html;
}

function foldery_register_shortcodes() {
    add_shortcode( 'foldery_menu', 'foldery_shortcode_menu' );
    add_shortcode( 'foldery_series', 'foldery_shortcode_series' );
    add_shortcode( 'foldery_serie_detail', 'foldery_shortcode_serie_detail' );
    add_shortcode( 'foldery_reproductions', 'foldery_shortcode_reproductions' );
    add_shortcode( 'serie', 'foldery_shortcode_series' );
    add_shortcode( 'masonry', 'foldery_shortcode_masonry' );

    add_shortcode( 'vc_row', 'foldery_passthrough_shortcode' );
    add_shortcode( 'vc_column', 'foldery_passthrough_shortcode' );
    add_shortcode( 'vc_column_text', 'foldery_passthrough_shortcode' );
    add_shortcode( 'vc_btn', 'foldery_button_shortcode' );
    add_shortcode( 'vc_zigzag', function() {
        return '<div class="vc-zigzag-inner foldery-separator"></div>';
    } );
    add_shortcode( 'cms_grid', '__return_empty_string' );
    add_shortcode( 'cms_fancybox_single', '__return_empty_string' );
    add_shortcode( 'cms_googlemap', '__return_empty_string' );
}
add_action( 'init', 'foldery_register_shortcodes' );
