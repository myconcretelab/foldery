<?php
/**
 * Gutenberg explorer block for media-folder navigation.
 */

if ( ! defined( 'FOLDERY_EXPLORER_LINKED_PAGE_META' ) ) {
    define( 'FOLDERY_EXPLORER_LINKED_PAGE_META', 'foldery_linked_page_id' );
}

function foldery_explorer_default_attributes() {
    return array(
        'showHomeSelection' => true,
        'homeFolderIds'     => '45,49,56,38,2,12,1',
        'homeTitle'         => 'Series en cours...',
        'showRecent'        => true,
        'recentTitle'       => "Recemment cree a l'atelier (ou dehors !)",
        'recentImageIds'    => '',
        'includePageContent'=> true,
        'animate'           => true,
    );
}

function foldery_explorer_parse_ids( $ids ) {
    return array_filter( array_map( 'absint', explode( ',', (string) $ids ) ) );
}

function foldery_explorer_folder_linked_page_id( $folder_id ) {
    $folder_id = absint( $folder_id );
    if ( ! $folder_id ) {
        return 0;
    }

    $page_id = absint( foldery_media_get_folder_meta( $folder_id, FOLDERY_EXPLORER_LINKED_PAGE_META, true ) );
    if ( $page_id && 'page' === get_post_type( $page_id ) && 'trash' !== get_post_status( $page_id ) ) {
        return $page_id;
    }

    return 0;
}

function foldery_explorer_folder_page( $folder_id ) {
    $page_id = foldery_explorer_folder_linked_page_id( $folder_id );
    if ( $page_id ) {
        $page = get_post( $page_id );
        if ( $page instanceof WP_Post ) {
            return $page;
        }
    }

    return null;
}

function foldery_explorer_page_folder_id( $page_id ) {
    global $wpdb;

    $page_id = absint( $page_id );
    if ( ! $page_id || ! foldery_media_has_tables() ) {
        return 0;
    }

    $table_meta       = foldery_media_table_name( 'meta' );
    $folder_id_column = foldery_media_folder_meta_id_column();
    $folder_id        = absint(
        $wpdb->get_var(
            $wpdb->prepare(
                "SELECT {$folder_id_column} FROM {$table_meta} WHERE meta_key = %s AND meta_value = %s ORDER BY meta_id ASC LIMIT 1",
                FOLDERY_EXPLORER_LINKED_PAGE_META,
                (string) $page_id
            )
        )
    );

    if ( $folder_id && foldery_is_media_folder( foldery_media_get_folder( $folder_id ) ) ) {
        return $folder_id;
    }

    return 0;
}

function foldery_explorer_link_folder_to_page( $folder_id, $page_id ) {
    global $wpdb;

    $folder_id = absint( $folder_id );
    $page_id   = absint( $page_id );
    if ( ! $folder_id || ! foldery_is_media_folder( foldery_media_get_folder( $folder_id ) ) ) {
        return array( __( 'Folder does not exist.', 'foldery' ) );
    }

    if ( $page_id && ( 'page' !== get_post_type( $page_id ) || 'trash' === get_post_status( $page_id ) ) ) {
        return array( __( 'Selected page does not exist.', 'foldery' ) );
    }

    $table_meta = foldery_media_table_name( 'meta' );
    $wpdb->delete(
        $table_meta,
        array(
            'meta_key'   => FOLDERY_EXPLORER_LINKED_PAGE_META,
            'meta_value' => (string) $page_id,
        ),
        array( '%s', '%s' )
    );

    if ( ! $page_id ) {
        foldery_media_delete_folder_meta( $folder_id, FOLDERY_EXPLORER_LINKED_PAGE_META );
        return true;
    }

    return foldery_media_update_folder_meta( $folder_id, FOLDERY_EXPLORER_LINKED_PAGE_META, (string) $page_id )
        ? true
        : array( __( 'Page link could not be saved.', 'foldery' ) );
}

function foldery_explorer_folder_url( $folder ) {
    if ( ! foldery_is_media_folder( $folder ) ) {
        return foldery_make_relative_dev_url( home_url( '/' ) );
    }

    $page = foldery_explorer_folder_page( $folder->getId() );
    if ( $page ) {
        return foldery_make_relative_dev_url( get_permalink( $page ) );
    }

    return foldery_make_relative_dev_url( home_url( '/explorer/' . trim( $folder->getAbsolutePath(), '/' ) . '/' ) );
}

function foldery_explorer_clean_page_content( $content ) {
    $content = preg_replace( '/<!-- wp:foldery\/explorer\b[^>]*(?:\/-->|-->.*?<!-- \/wp:foldery\/explorer -->)/is', '', $content );
    $content = preg_replace( '/<!-- wp:shortcode -->\s*\[(foldery_series|serie|foldery_serie_detail|masonry|last_pics)[^\]]*\]\s*<!-- \/wp:shortcode -->/i', '', $content );
    $content = preg_replace( '/\[(foldery_series|serie|foldery_serie_detail|masonry|last_pics)[^\]]*\]/i', '', $content );

    return trim( $content );
}

function foldery_explorer_page_content( $folder_id ) {
    global $foldery_explorer_rendering_page_content;

    $page = foldery_explorer_folder_page( $folder_id );
    if ( ! $page ) {
        return '';
    }

    $content = foldery_explorer_clean_page_content( $page->post_content );
    if ( '' === $content ) {
        return '';
    }

    $GLOBALS['post'] = $page;
    setup_postdata( $page );
    $foldery_explorer_rendering_page_content = true;
    $html = apply_filters( 'the_content', $content );
    $foldery_explorer_rendering_page_content = false;
    wp_reset_postdata();

    return '<div class="foldery-explorer-page-content">' . $html . '</div>';
}

function foldery_explorer_attachment_ids_from_folder( $folder ) {
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

function foldery_explorer_attachment_field( $key, $post_id = null, $format_value = true ) {
    if ( function_exists( 'get_field' ) ) {
        return get_field( $key, $post_id, $format_value );
    }

    return get_post_meta( $post_id ? $post_id : get_the_ID(), $key, true );
}

function foldery_explorer_frame_classes( $attachment_id ) {
    $frame   = foldery_explorer_attachment_field( 'cadre_presentation', $attachment_id, false );
    $classes = in_array( (string) $frame, array( '15', '25', '35' ), true ) ? 'frame' : '';
    if ( '25' === (string) $frame ) {
        $classes .= ' borderless';
    }

    return trim( $classes );
}

function foldery_explorer_render_framed_attachment( $attachment_id, $ratio = 0.5 ) {
    $image = wp_get_attachment_image_src( $attachment_id, 'medium' );
    if ( ! $image ) {
        return '';
    }

    $link = wp_get_attachment_link( $attachment_id, 'medium' );
    if ( function_exists( 'foldery_lightbox_activate' ) ) {
        $link = foldery_lightbox_activate( $link );
    }

    $width    = $image[1] * $ratio;
    $height   = $image[2] * $ratio;
    $style_id = 'frame_' . (int) $attachment_id;

    return sprintf(
        '<figure class="%1$s" id="%2$s" style="width:%3$dpx">%4$s</figure><style>#%2$s:before{width:%5$dpx;height:%6$dpx}#%2$s:after{width:%7$dpx;height:%8$dpx}#%2$s.borderless:before{width:%3$dpx;height:%9$dpx}#%2$s.borderless:after{width:%10$dpx;height:%11$dpx}</style>',
        esc_attr( foldery_explorer_frame_classes( $attachment_id ) ),
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

function foldery_explorer_render_masonry( $folder_id, $thumbsize = 'medium' ) {
    $ids = foldery_explorer_attachment_ids_from_folder( foldery_media_get_folder( $folder_id ) );
    if ( ! count( $ids ) ) {
        return '';
    }

    $html = '<div class="grid foldery-masonry" data-masonry=\'{ "itemSelector": ".grid-item", "columnWidth": ".grid-item", "gutter": 20, "isFitWidth": true }\'>';
    foreach ( $ids as $id ) {
        $image = wp_get_attachment_image_src( $id, $thumbsize );
        if ( ! $image ) {
            continue;
        }

        $shape = $image[1] === $image[2] ? 'img-sq' : ( $image[1] > $image[2] ? 'img-lg' : 'img-ht' );
        $html .= sprintf(
            '<div class="grid-item %1$s w%2$d h%3$d">%4$s</div>',
            esc_attr( $shape ),
            (int) $image[1],
            (int) $image[2],
            wp_get_attachment_link( $id, $thumbsize )
        );
    }
    $html .= '</div><div class="clear"></div>';

    return function_exists( 'foldery_lightbox_activate' ) ? foldery_lightbox_activate( $html ) : $html;
}

function foldery_explorer_stack_image_id( $folder ) {
    $ids = foldery_explorer_attachment_ids_from_folder( $folder );
    return count( $ids ) ? (int) $ids[0] : 0;
}

function foldery_explorer_render_stack( $folders ) {
    $folders = array_filter( (array) $folders, 'foldery_is_media_folder' );
    if ( ! count( $folders ) ) {
        return '';
    }

    $html = '<div class="stack-wrapper foldery-series-stack foldery-explorer-stack" data-masonry=\'{ "itemSelector": ".stack-item", "columnWidth": ".stack-item", "gutter": 30 }\'>';
    foreach ( $folders as $folder ) {
        $image_id = foldery_explorer_stack_image_id( $folder );
        if ( ! $image_id ) {
            continue;
        }

        $image = wp_get_attachment_image_src( $image_id, 'medium' );
        if ( ! $image ) {
            continue;
        }

        $width  = $image[1] / 2;
        $height = $image[2] / 2;
        $html  .= sprintf(
            '<div class="stack-item foldery-explorer-item"><a href="%1$s" class="stack-link foldery-explorer-link" data-folder-id="%2$d"><h5>%3$s</h5><figure class="img-area" id="explorer-img-area-%4$d" style="width:%5$dpx;height:%6$dpx">%7$s</figure><style>#explorer-img-area-%4$d:after,#explorer-img-area-%4$d:before{width:%5$dpx;height:%6$dpx}</style></a></div>',
            esc_url( foldery_explorer_folder_url( $folder ) ),
            (int) $folder->getId(),
            esc_html( $folder->getName() ),
            (int) $image_id,
            (int) $width,
            (int) $height,
            wp_get_attachment_image( $image_id, 'medium' )
        );
    }
    $html .= '</div>';

    return $html;
}

function foldery_explorer_render_parent_link( $folder ) {
    if ( ! foldery_is_media_folder( $folder ) ) {
        return '';
    }

    $parent_id = $folder->getParent();
    if ( ! $parent_id || foldery_media_root_id() === (int) $parent_id ) {
        return '';
    }

    $parent = foldery_media_get_folder( $parent_id );
    if ( ! foldery_is_media_folder( $parent ) ) {
        return '';
    }

    $name  = $parent->getName();
    $label = sprintf( 'Revenir a %s', $name );

    return sprintf(
        '<a href="%1$s" class="foldery-explorer-back foldery-explorer-link" data-folder-id="%2$d" data-foldery-back="1" aria-label="%3$s" title="%3$s"><span class="foldery-explorer-back-icon" aria-hidden="true"></span><span class="foldery-explorer-back-label">%4$s</span></a>',
        esc_url( foldery_explorer_folder_url( $parent ) ),
        (int) $parent->getId(),
        esc_attr( $label ),
        esc_html( $name )
    );
}

function foldery_explorer_render_folder( $folder_id, $include_page_content = true ) {
    $folder = foldery_media_get_folder( $folder_id );
    if ( ! foldery_is_media_folder( $folder ) ) {
        return '';
    }

    $children     = $folder->getChildren();
    $page_content = $include_page_content ? foldery_explorer_page_content( $folder->getId() ) : '';
    $parent_link  = foldery_explorer_render_parent_link( $folder );
    $html         = '<section class="foldery-explorer-view foldery-explorer-folder' . ( $parent_link ? ' has-parent-link' : '' ) . '" data-folder-id="' . (int) $folder->getId() . '">';

    $html .= $parent_link;

    if ( '' === $page_content ) {
        $html .= '<header class="foldery-explorer-heading"><h3>' . esc_html( $folder->getName() ) . '</h3></header>';
    } else {
        $html .= $page_content;
    }

    if ( count( $children ) ) {
        $html .= foldery_explorer_render_stack( $children );
    } elseif ( $folder->getCnt() ) {
        $html .= foldery_explorer_render_masonry( $folder->getId() );
    }

    $html .= '</section>';

    return $html;
}

function foldery_explorer_render_selected_images( $ids, $columns = 3 ) {
    $ids = foldery_explorer_parse_ids( $ids );
    if ( ! count( $ids ) ) {
        return '';
    }

    $has_cells = false;
    $html      = '<table class="expo foldery-selected-pics">';
    foreach ( array_chunk( $ids, max( 1, (int) $columns ) ) as $row ) {
        $cells = '';
        foreach ( $row as $attachment_id ) {
            if ( 'attachment' !== get_post_type( $attachment_id ) ) {
                continue;
            }

            $attachment_html = foldery_explorer_render_framed_attachment( $attachment_id );
            if ( '' === $attachment_html ) {
                continue;
            }

            $cells .= '<td>' . $attachment_html;
            $display_dimension = foldery_explorer_attachment_field( 'dimension', $attachment_id, true );
            if ( $display_dimension ) {
                $cells .= '<h5>' . esc_html( $display_dimension ) . '</h5>';
            }
            $cells .= '</td>';
        }

        if ( '' !== $cells ) {
            $has_cells = true;
            $html .= '<tr>' . $cells . '</tr>';
        }
    }
    $html .= '</table>';

    return $has_cells ? $html : '';
}

function foldery_explorer_render_home( $attributes ) {
    $attributes = wp_parse_args( $attributes, foldery_explorer_default_attributes() );
    $html       = '<section class="foldery-explorer-view foldery-explorer-home">';

    if ( $attributes['showHomeSelection'] ) {
        $folders = array();
        foreach ( foldery_explorer_parse_ids( $attributes['homeFolderIds'] ) as $folder_id ) {
            $folder = foldery_media_get_folder( $folder_id );
            if ( foldery_is_media_folder( $folder ) ) {
                $folders[] = $folder;
            }
        }

        if ( count( $folders ) ) {
            $html .= '<h3 class="wp-block-heading">' . esc_html( $attributes['homeTitle'] ) . '</h3>';
            $html .= foldery_explorer_render_stack( $folders );
        }
    }

    if ( $attributes['showRecent'] ) {
        $recent_html = foldery_explorer_render_selected_images( $attributes['recentImageIds'] );
        if ( '' !== $recent_html ) {
            $html .= '<h3 class="wp-block-heading">' . esc_html( $attributes['recentTitle'] ) . '</h3>';
            $html .= $recent_html;
        }
    }

    $html .= '</section>';

    return $html;
}

function foldery_explorer_menu_map() {
    $locations = get_nav_menu_locations();
    $menu_id   = $locations['primary'] ?? 0;
    $map       = array();

    if ( ! $menu_id ) {
        $menu = wp_get_nav_menu_object( 'Main menu' );
        $menu_id = $menu ? $menu->term_id : 0;
    }

    if ( ! $menu_id ) {
        return $map;
    }

    foreach ( wp_get_nav_menu_items( $menu_id ) as $item ) {
        $page_id   = isset( $item->object_id ) ? absint( $item->object_id ) : 0;
        $folder_id = $page_id ? foldery_explorer_page_folder_id( $page_id ) : 0;
        if ( ! $folder_id ) {
            continue;
        }

        $url = foldery_make_relative_dev_url( $item->url );

        $map[ untrailingslashit( $url ) ] = array(
            'folderId' => $folder_id,
            'url'      => $url,
            'title'    => $item->title,
        );
    }

    return $map;
}

function foldery_explorer_menu_default_attributes() {
    return array(
        'rootFolderId' => foldery_media_root_id(),
        'folderIds'    => '',
        'maxDepth'     => 0,
        'showSubmenus' => true,
        'includeEmpty' => true,
        'ariaLabel'    => 'Explorer',
    );
}

function foldery_explorer_current_folder_id() {
    $path = get_query_var( 'foldery_path' );
    if ( $path ) {
        $folder = foldery_media_get_by_absolute_path( $path );
        if ( foldery_is_media_folder( $folder ) ) {
            return $folder->getId();
        }
    }

    if ( is_page() ) {
        return foldery_explorer_page_folder_id( get_queried_object_id() );
    }

    return 0;
}

function foldery_explorer_folder_ancestor_ids( $folder_id ) {
    $ids    = array();
    $folder = foldery_media_get_folder( $folder_id );

    while ( foldery_is_media_folder( $folder ) ) {
        $parent_id = $folder->getParent();
        if ( ! $parent_id || foldery_media_root_id() === (int) $parent_id ) {
            break;
        }

        $ids[]  = (int) $parent_id;
        $folder = foldery_media_get_folder( $parent_id );
    }

    return $ids;
}

function foldery_explorer_folder_menu_title( $folder ) {
    if ( ! foldery_is_media_folder( $folder ) ) {
        return '';
    }

    $page = foldery_explorer_folder_page( $folder->getId() );
    return $page ? get_the_title( $page ) : $folder->getName();
}

function foldery_explorer_folder_has_menu_content( $folder ) {
    if ( ! foldery_is_media_folder( $folder ) ) {
        return false;
    }

    if ( $folder->getCnt() ) {
        return true;
    }

    foreach ( $folder->getChildren() as $child ) {
        if ( foldery_explorer_folder_has_menu_content( $child ) ) {
            return true;
        }
    }

    return false;
}

function foldery_explorer_menu_items( $folders, $attributes, $depth = 1, $current_folder_id = 0, $current_ancestor_ids = array() ) {
    $html      = '';
    $max_depth = max( 0, (int) $attributes['maxDepth'] );

    foreach ( array_filter( (array) $folders, 'foldery_is_media_folder' ) as $folder ) {
        if ( empty( $attributes['includeEmpty'] ) && ! foldery_explorer_folder_has_menu_content( $folder ) ) {
            continue;
        }

        $children = ( ! empty( $attributes['showSubmenus'] ) && ( ! $max_depth || $depth < $max_depth ) )
            ? foldery_explorer_menu_items( $folder->getChildren(), $attributes, $depth + 1, $current_folder_id, $current_ancestor_ids )
            : '';
        $classes  = array( 'foldery-explorer-menu-item' );
        if ( $current_folder_id === $folder->getId() ) {
            $classes[] = 'current-menu-item';
        }
        if ( in_array( $folder->getId(), $current_ancestor_ids, true ) ) {
            $classes[] = 'current-menu-ancestor';
        }
        if ( '' !== $children ) {
            $classes[] = 'menu-item-has-children';
        }

        $html .= sprintf(
            '<li class="%1$s"><a href="%2$s" class="foldery-explorer-menu-link foldery-explorer-link" data-folder-id="%3$d">%4$s</a>%5$s</li>',
            esc_attr( implode( ' ', $classes ) ),
            esc_url( foldery_explorer_folder_url( $folder ) ),
            (int) $folder->getId(),
            esc_html( foldery_explorer_folder_menu_title( $folder ) ),
            $children ? '<ul class="sub-menu foldery-explorer-submenu">' . $children . '</ul>' : ''
        );
    }

    return $html;
}

function foldery_explorer_render_menu_block( $attributes ) {
    $attributes = wp_parse_args( $attributes, foldery_explorer_menu_default_attributes() );
    $root_id    = isset( $attributes['rootFolderId'] ) ? (int) $attributes['rootFolderId'] : foldery_media_root_id();
    $folders    = array();
    $folder_ids = foldery_explorer_parse_ids( $attributes['folderIds'] );

    if ( count( $folder_ids ) ) {
        foreach ( $folder_ids as $folder_id ) {
            $folder = foldery_media_get_folder( $folder_id );
            if ( foldery_is_media_folder( $folder ) ) {
                $folders[] = $folder;
            }
        }
    } elseif ( foldery_media_root_id() === $root_id ) {
        $folders = foldery_media_root_children();
    } else {
        $root = foldery_media_get_folder( $root_id );
        if ( foldery_is_media_folder( $root ) ) {
            $folders = $root->getChildren();
        }
    }

    $current_folder_id = foldery_explorer_current_folder_id();
    $items             = foldery_explorer_menu_items( $folders, $attributes, 1, $current_folder_id, foldery_explorer_folder_ancestor_ids( $current_folder_id ) );
    if ( '' === $items ) {
        return '';
    }

    $classes = 'foldery-explorer-menu';
    if ( ! empty( $attributes['className'] ) ) {
        $classes .= ' ' . $attributes['className'];
    }

    return sprintf(
        '<nav class="%1$s" aria-label="%2$s"><ul class="foldery-explorer-menu-list">%3$s</ul></nav>',
        esc_attr( trim( $classes ) ),
        esc_attr( $attributes['ariaLabel'] ),
        $items
    );
}

function foldery_explorer_render_block( $attributes ) {
    foldery_explorer_enqueue_front_assets();

    $attributes = wp_parse_args( $attributes, foldery_explorer_default_attributes() );
    $path       = get_query_var( 'foldery_path' );
    $folder     = $path ? foldery_media_get_by_absolute_path( $path ) : null;
    if ( ! foldery_is_media_folder( $folder ) && is_page() ) {
        $linked_folder_id = foldery_explorer_page_folder_id( get_queried_object_id() );
        $folder           = $linked_folder_id ? foldery_media_get_folder( $linked_folder_id ) : null;
    }
    $html       = foldery_is_media_folder( $folder )
        ? foldery_explorer_render_folder( $folder->getId(), ! empty( $attributes['includePageContent'] ) )
        : foldery_explorer_render_home( $attributes );

    return sprintf(
        '<div class="foldery-explorer" data-api-url="%1$s" data-include-page="%2$d" data-animate="%3$d" data-menu-map="%4$s"><div class="foldery-explorer-stage">%5$s</div></div>',
        esc_url( foldery_make_relative_dev_url( admin_url( 'admin-ajax.php?action=foldery_explorer' ) ) ),
        empty( $attributes['includePageContent'] ) ? 0 : 1,
        empty( $attributes['animate'] ) ? 0 : 1,
        esc_attr( wp_json_encode( foldery_explorer_menu_map() ) ),
        $html
    );
}

function foldery_explorer_filter_linked_page_content( $content ) {
    global $foldery_explorer_rendering_page_content;

    if ( is_admin() || ! is_singular( 'page' ) || ! in_the_loop() || ! is_main_query() || ! empty( $foldery_explorer_rendering_page_content ) ) {
        return $content;
    }

    $folder_id = foldery_explorer_page_folder_id( get_the_ID() );
    if ( ! $folder_id ) {
        return $content;
    }

    return foldery_explorer_render_block( array( 'includePageContent' => true ) );
}
add_filter( 'the_content', 'foldery_explorer_filter_linked_page_content', 11 );

function foldery_explorer_response_data( $folder_id, $include_page ) {
    $folder = foldery_media_get_folder( $folder_id );

    if ( ! foldery_is_media_folder( $folder ) ) {
        return null;
    }

    return array(
        'folderId'    => $folder->getId(),
        'ancestorIds' => foldery_explorer_folder_ancestor_ids( $folder->getId() ),
        'title'       => $folder->getName(),
        'url'         => foldery_explorer_folder_url( $folder ),
        'html'        => foldery_explorer_render_folder( $folder->getId(), (bool) $include_page ),
    );
}

function foldery_explorer_editor_folder_tree( $folders = null ) {
    if ( null === $folders ) {
        $folders = foldery_media_root_children();
    }

    return array_values(
        array_map(
            function ( $folder ) {
                return array(
                    'id'       => $folder->getId(),
                    'parent'   => $folder->getParent(),
                    'name'     => $folder->getName(),
                    'path'     => $folder->getPath( ' / ', null ),
                    'count'    => $folder->getCnt(),
                    'children' => foldery_explorer_editor_folder_tree( $folder->getChildren() ),
                );
            },
            array_filter( (array) $folders, 'foldery_is_media_folder' )
        )
    );
}

function foldery_explorer_register_block() {
    if ( function_exists( 'foldery_register_shared_styles' ) ) {
        foldery_register_shared_styles();
    }

    wp_register_script( 'foldery-explorer-front', get_template_directory_uri() . '/assets/js/foldery-explorer.js', array(), FOLDERY_VERSION, true );
    wp_register_script(
        'foldery-explorer-editor',
        get_template_directory_uri() . '/assets/js/foldery-explorer-editor.js',
        array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n', 'wp-server-side-render' ),
        FOLDERY_VERSION,
        true
    );

    register_block_type(
        'foldery/explorer',
        array(
            'api_version'     => 3,
            'editor_script'   => 'foldery-explorer-editor',
            'editor_style'    => 'foldery-explorer-editor-style',
            'view_script'     => 'foldery-explorer-front',
            'render_callback' => 'foldery_explorer_render_block',
            'attributes'      => array(
                'showHomeSelection' => array( 'type' => 'boolean', 'default' => true ),
                'homeFolderIds'     => array( 'type' => 'string', 'default' => '45,49,56,38,2,12,1' ),
                'homeTitle'         => array( 'type' => 'string', 'default' => 'Series en cours...' ),
                'showRecent'        => array( 'type' => 'boolean', 'default' => true ),
                'recentTitle'       => array( 'type' => 'string', 'default' => "Recemment cree a l'atelier (ou dehors !)" ),
                'recentImageIds'    => array( 'type' => 'string', 'default' => '' ),
                'includePageContent'=> array( 'type' => 'boolean', 'default' => true ),
                'animate'           => array( 'type' => 'boolean', 'default' => true ),
            ),
        )
    );

    register_block_type(
        'foldery/explorer-menu',
        array(
            'api_version'     => 3,
            'editor_script'   => 'foldery-explorer-editor',
            'editor_style'    => 'foldery-explorer-editor-style',
            'render_callback' => 'foldery_explorer_render_menu_block',
            'attributes'      => array(
                'rootFolderId' => array( 'type' => 'number', 'default' => foldery_media_root_id() ),
                'folderIds'    => array( 'type' => 'string', 'default' => '' ),
                'maxDepth'     => array( 'type' => 'number', 'default' => 0 ),
                'showSubmenus' => array( 'type' => 'boolean', 'default' => true ),
                'includeEmpty' => array( 'type' => 'boolean', 'default' => true ),
                'ariaLabel'    => array( 'type' => 'string', 'default' => 'Explorer' ),
                'className'    => array( 'type' => 'string' ),
            ),
        )
    );
}
add_action( 'init', 'foldery_explorer_register_block' );

function foldery_explorer_enqueue_front_assets() {
    wp_enqueue_script( 'foldery-explorer-front' );
}

function foldery_explorer_enqueue_block_editor_assets() {
    if ( function_exists( 'foldery_register_shared_styles' ) ) {
        foldery_register_shared_styles();
    }

    wp_enqueue_style( 'foldery-explorer-editor-style' );
    wp_add_inline_script(
        'foldery-explorer-editor',
        'window.FolderyExplorerEditor = ' . wp_json_encode(
            array(
                'folders' => foldery_media_active() ? foldery_explorer_editor_folder_tree() : array(),
            )
        ) . ';',
        'before'
    );
}
add_action( 'enqueue_block_editor_assets', 'foldery_explorer_enqueue_block_editor_assets' );

function foldery_explorer_register_routes() {
    register_rest_route(
        'foldery/v1',
        '/explorer',
        array(
            'methods'             => WP_REST_Server::READABLE,
            'permission_callback' => '__return_true',
            'args'                => array(
                'folder_id'    => array( 'sanitize_callback' => 'absint' ),
                'include_page' => array( 'sanitize_callback' => 'absint' ),
            ),
            'callback'            => function ( WP_REST_Request $request ) {
                $data = foldery_explorer_response_data( absint( $request->get_param( 'folder_id' ) ), $request->get_param( 'include_page' ) );
                if ( ! $data ) {
                    return new WP_Error( 'foldery_missing_folder', __( 'Folder not found.', 'foldery' ), array( 'status' => 404 ) );
                }

                return rest_ensure_response( $data );
            },
        )
    );
}
add_action( 'rest_api_init', 'foldery_explorer_register_routes' );

function foldery_explorer_ajax() {
    $folder_id = isset( $_GET['folder_id'] ) ? absint( $_GET['folder_id'] ) : 0;
    $include_page = isset( $_GET['include_page'] ) ? absint( $_GET['include_page'] ) : 1;
    $data = foldery_explorer_response_data( $folder_id, $include_page );

    if ( ! $data ) {
        wp_send_json_error( array( 'message' => __( 'Folder not found.', 'foldery' ) ), 404 );
    }

    wp_send_json( $data );
}
add_action( 'wp_ajax_foldery_explorer', 'foldery_explorer_ajax' );
add_action( 'wp_ajax_nopriv_foldery_explorer', 'foldery_explorer_ajax' );

function foldery_explorer_rewrite_rules() {
    add_rewrite_tag( '%foldery_path%', '(.+)' );
    add_rewrite_rule( '^explorer/(.+)/?$', 'index.php?page_id=' . absint( get_option( 'page_on_front' ) ) . '&foldery_path=$matches[1]', 'top' );
}
add_action( 'init', 'foldery_explorer_rewrite_rules' );

function foldery_explorer_query_vars( $vars ) {
    $vars[] = 'foldery_path';
    return $vars;
}
add_filter( 'query_vars', 'foldery_explorer_query_vars' );

function foldery_explorer_disable_canonical_redirect( $redirect_url ) {
    return get_query_var( 'foldery_path' ) ? false : $redirect_url;
}
add_filter( 'redirect_canonical', 'foldery_explorer_disable_canonical_redirect' );
