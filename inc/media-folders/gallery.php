<?php
function foldery_media_gallery_atts( $out, $pairs, $atts ) {
	$atts = shortcode_atts(
		array(
			'folder_id'      => -2,
			'fid'            => -2,
			'order'          => 'ASC',
			'orderby'        => 'folder_order',
			'posts_per_page' => -1,
		),
		$atts
	);

	if ( isset( $out['folder_id'] ) && (int) $out['folder_id'] > -2 ) {
		$atts['folder_id'] = (int) $out['folder_id'];
	} elseif ( isset( $out['fid'] ) && (int) $out['fid'] > -2 ) {
		$atts['folder_id'] = (int) $out['fid'];
	} elseif ( (int) $atts['folder_id'] <= -2 && (int) $atts['fid'] > -2 ) {
		$atts['folder_id'] = (int) $atts['fid'];
	}

	if ( (int) $atts['folder_id'] > -2 ) {
		$ids = foldery_media_get_attachments( (int) $atts['folder_id'], $atts['order'], $atts['orderby'] );
		$out['include'] = $ids ? implode( ',', $ids ) : '0';
		$out['orderby'] = 'post__in';
		unset( $out['folder_id'] );
		unset( $out['fid'] );
	}

	return $out;
}
add_filter( 'shortcode_atts_gallery', 'foldery_media_gallery_atts', 10, 3 );

function foldery_media_register_gallery_shortcode() {
	global $shortcode_tags;

	if ( ! shortcode_exists( 'folder-gallery' ) && isset( $shortcode_tags['gallery'] ) ) {
		add_shortcode( 'folder-gallery', $shortcode_tags['gallery'] );
	}
}
add_action( 'init', 'foldery_media_register_gallery_shortcode', 20 );
