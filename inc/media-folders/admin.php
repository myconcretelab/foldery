<?php
function foldery_media_plain_folder( $folder ) {
	$linked_page_id = function_exists( 'foldery_explorer_folder_linked_page_id' ) ? foldery_explorer_folder_linked_page_id( $folder->getId() ) : 0;

	return array(
		'id'              => $folder->getId(),
		'parent'          => $folder->getParent(),
		'name'            => $folder->getName(),
		'path'            => $folder->getAbsolutePath(),
		'count'           => foldery_media_count_attachments_in_folder( $folder->getId() ),
		'linkedPageId'    => $linked_page_id,
		'linkedPageTitle' => $linked_page_id ? get_the_title( $linked_page_id ) : '',
		'children'        => array_map( 'foldery_media_plain_folder', $folder->getChildren() ),
	);
}

function foldery_media_admin_page_data() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return array();
	}

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'private', 'draft', 'pending' ),
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'order'          => 'ASC',
		)
	);

	return array_map(
		function ( $page ) {
			return array(
				'id'     => (int) $page->ID,
				'title'  => get_the_title( $page ),
				'status' => get_post_status( $page ),
			);
		},
		$pages
	);
}

function foldery_media_admin_tree_data() {
	return array(
		'all'          => array(
			'id'    => 0,
			'name'  => __( 'All files', 'foldery' ),
			'count' => (int) wp_count_posts( 'attachment' )->inherit,
		),
		'unorganized'  => array(
			'id'    => foldery_media_root_id(),
			'name'  => __( 'Unorganized', 'foldery' ),
			'count' => foldery_media_count_attachments_in_folder( foldery_media_root_id() ),
		),
		'folders'      => array_map( 'foldery_media_plain_folder', foldery_media_root_children() ),
		'selected'     => foldery_media_current_request_folder_id(),
		'dropdownHtml' => foldery_media_dropdown( foldery_media_current_request_folder_id(), array(), true ),
	);
}

function foldery_media_current_request_folder_id() {
	$value = null;
	if ( isset( $_REQUEST['foldery_media_folder'] ) ) {
		$value = wp_unslash( $_REQUEST['foldery_media_folder'] );
	} elseif ( isset( $_REQUEST['query'] ) && is_array( $_REQUEST['query'] ) && isset( $_REQUEST['query']['foldery_media_folder'] ) ) {
		$value = wp_unslash( $_REQUEST['query']['foldery_media_folder'] );
	} elseif ( isset( $_REQUEST['folderyMediaFolder'] ) ) {
		$value = wp_unslash( $_REQUEST['folderyMediaFolder'] );
	}

	if ( is_array( $value ) ) {
		return 0;
	}

	if ( null === $value || '' === $value ) {
		return 0;
	}

	return (int) $value;
}

function foldery_media_apply_attachment_query_filter( $query, $fid ) {
	$ids = foldery_media_attachment_ids_for_folder( $fid );
	if ( null === $ids ) {
		return $query;
	}

	$query['post__in'] = empty( $ids ) ? array( 0 ) : $ids;
	$query['orderby']  = 'post__in';
	return $query;
}

function foldery_media_ajax_query_attachments_args( $query ) {
	if ( ! current_user_can( 'upload_files' ) ) {
		return $query;
	}

	return foldery_media_apply_attachment_query_filter( $query, foldery_media_current_request_folder_id() );
}
add_filter( 'ajax_query_attachments_args', 'foldery_media_ajax_query_attachments_args' );

function foldery_media_pre_get_posts( $query ) {
	global $pagenow;

	if ( ! is_admin() || 'upload.php' !== $pagenow || ! $query->is_main_query() || 'attachment' !== $query->get( 'post_type' ) ) {
		return;
	}

	$args = foldery_media_apply_attachment_query_filter( array(), foldery_media_current_request_folder_id() );
	foreach ( $args as $key => $value ) {
		$query->set( $key, $value );
	}
}
add_action( 'pre_get_posts', 'foldery_media_pre_get_posts' );

function foldery_media_restrict_manage_posts() {
	global $typenow;

	if ( 'attachment' !== $typenow || ! current_user_can( 'upload_files' ) ) {
		return;
	}

	printf(
		'<label class="screen-reader-text" for="filter-by-foldery-media-folder">%s</label><select name="foldery_media_folder" id="filter-by-foldery-media-folder" class="attachment-filters attachment-filters-foldery">%s</select>',
		esc_html__( 'Filter by media folder', 'foldery' ),
		foldery_media_dropdown( foldery_media_current_request_folder_id(), array(), true )
	);
}
add_action( 'restrict_manage_posts', 'foldery_media_restrict_manage_posts' );

function foldery_media_prepare_attachment_for_js( $response, $attachment, $meta ) {
	$response['folderyMediaFolder'] = foldery_media_get_attachment_folder( $attachment->ID, foldery_media_root_id() );
	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'foldery_media_prepare_attachment_for_js', 10, 3 );

function foldery_media_attachment_fields_to_edit( $fields, $post ) {
	if ( ! current_user_can( 'upload_files' ) ) {
		return $fields;
	}

	$fields['folderyMediaFolder'] = array(
		'label' => __( 'Media folder', 'foldery' ),
		'input' => 'html',
		'html'  => '<select name="attachments[' . (int) $post->ID . '][foldery_media_folder]">' . foldery_media_dropdown( foldery_media_get_attachment_folder( $post->ID, foldery_media_root_id() ), array(), false ) . '</select>',
		'helps' => __( 'Move this file to a media folder.', 'foldery' ),
	);

	return $fields;
}
add_filter( 'attachment_fields_to_edit', 'foldery_media_attachment_fields_to_edit', 10, 2 );

function foldery_media_attachment_fields_to_save( $post, $attachment ) {
	if ( isset( $attachment['foldery_media_folder'] ) && current_user_can( 'upload_files' ) ) {
		foldery_media_move_attachments( (int) $attachment['foldery_media_folder'], array( (int) $post['ID'] ) );
	}

	return $post;
}
add_filter( 'attachment_fields_to_save', 'foldery_media_attachment_fields_to_save', 10, 2 );

function foldery_media_handle_uploaded_attachment( $attachment_id ) {
	$fid = foldery_media_current_request_folder_id();
	if ( 0 !== $fid ) {
		foldery_media_move_attachments( $fid, array( (int) $attachment_id ) );
	}
}
add_action( 'add_attachment', 'foldery_media_handle_uploaded_attachment' );

function foldery_media_admin_enqueue( $hook ) {
	if ( ! current_user_can( 'upload_files' ) || ! in_array( $hook, array( 'upload.php', 'media-new.php' ), true ) ) {
		return;
	}

	$script = get_template_directory() . '/assets/admin/foldery-media-library.js';
	$style  = get_template_directory() . '/assets/admin/foldery-media-library.css';
	wp_enqueue_style( 'foldery-media-library', get_template_directory_uri() . '/assets/admin/foldery-media-library.css', array(), file_exists( $style ) ? filemtime( $style ) : null );
	wp_enqueue_script( 'foldery-media-library', get_template_directory_uri() . '/assets/admin/foldery-media-library.js', array( 'jquery', 'jquery-ui-sortable', 'media-views', 'wp-util' ), file_exists( $script ) ? filemtime( $script ) : null, true );
	wp_localize_script(
		'foldery-media-library',
		'folderyMediaLibrary',
		array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'foldery-media-admin' ),
			'rootId'   => foldery_media_root_id(),
			'tree'     => foldery_media_admin_tree_data(),
			'pages'    => foldery_media_admin_page_data(),
			'labels'   => array(
				'title'          => __( 'Folders', 'foldery' ),
				'newFolder'      => __( 'New folder', 'foldery' ),
				'rename'         => __( 'Rename', 'foldery' ),
				'delete'         => __( 'Delete', 'foldery' ),
				'moveSelected'   => __( 'Move selection here', 'foldery' ),
				'linkedPage'     => __( 'Linked page', 'foldery' ),
				'noLinkedPage'   => __( 'No linked page', 'foldery' ),
				'savePageLink'   => __( 'Save page link', 'foldery' ),
				'pageLinkSaved'  => __( 'Page link saved.', 'foldery' ),
				'folderName'     => __( 'Folder name', 'foldery' ),
				'confirmDelete'  => __( 'Delete this folder? Files will become unorganized.', 'foldery' ),
				'selectFiles'    => __( 'Select media first.', 'foldery' ),
				'allFiles'       => __( 'All files', 'foldery' ),
				'unorganized'    => __( 'Unorganized', 'foldery' ),
				'dropToMove'     => __( 'Drop to move', 'foldery' ),
				'moved'          => __( 'Media moved.', 'foldery' ),
				'orderSaved'     => __( 'Media order saved.', 'foldery' ),
				'orderDisabled'  => __( 'Select a folder to reorder media.', 'foldery' ),
				'folderOrderSaved' => __( 'Folder order saved.', 'foldery' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'foldery_media_admin_enqueue' );

function foldery_media_admin_ajax() {
	check_ajax_referer( 'foldery-media-admin', 'nonce' );
	if ( ! current_user_can( 'upload_files' ) ) {
		wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'foldery' ) ), 403 );
	}

	$folder_action = isset( $_POST['folder_action'] ) ? sanitize_key( wp_unslash( $_POST['folder_action'] ) ) : '';
	$result        = true;
	$selected      = foldery_media_current_request_folder_id();

	switch ( $folder_action ) {
		case 'create':
			$parent = isset( $_POST['parent'] ) ? (int) $_POST['parent'] : foldery_media_root_id();
			$name   = isset( $_POST['name'] ) ? wp_unslash( $_POST['name'] ) : '';
			$result = foldery_media_create_folder( $name, $parent );
			if ( is_int( $result ) ) {
				$selected = $result;
			}
			break;
		case 'rename':
			$id       = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
			$name     = isset( $_POST['name'] ) ? wp_unslash( $_POST['name'] ) : '';
			$result   = foldery_media_rename_folder( $name, $id );
			$selected = $id;
			break;
		case 'delete':
			$id       = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
			$result   = foldery_media_delete_folder( $id );
			$selected = 0;
			break;
		case 'move':
			$to       = isset( $_POST['to'] ) ? (int) $_POST['to'] : foldery_media_root_id();
			$ids      = isset( $_POST['ids'] ) ? (array) $_POST['ids'] : array();
			$result   = foldery_media_move_attachments( $to, $ids );
			$selected = $to;
			break;
		case 'reorder':
			$id       = isset( $_POST['id'] ) ? (int) $_POST['id'] : $selected;
			$ids      = isset( $_POST['ids'] ) ? (array) $_POST['ids'] : array();
			$result   = foldery_media_reorder_attachments( $id, $ids );
			$selected = $id;
			break;
		case 'reorder_folders':
			$tree = array();
			if ( isset( $_POST['tree'] ) ) {
				$decoded = json_decode( wp_unslash( $_POST['tree'] ), true );
				$tree    = is_array( $decoded ) ? $decoded : array();
			}
			$result = foldery_media_reorder_folders( $tree );
			break;
		case 'link_page':
			if ( ! current_user_can( 'edit_pages' ) ) {
				$result = array( __( 'Insufficient permissions.', 'foldery' ) );
				break;
			}

			$id      = isset( $_POST['id'] ) ? (int) $_POST['id'] : $selected;
			$page_id = isset( $_POST['page_id'] ) ? (int) $_POST['page_id'] : 0;
			$result  = function_exists( 'foldery_explorer_link_folder_to_page' )
				? foldery_explorer_link_folder_to_page( $id, $page_id )
				: array( __( 'Explorer is not available.', 'foldery' ) );
			$selected = $id;
			break;
		default:
			wp_send_json_error( array( 'message' => __( 'Unknown action.', 'foldery' ) ), 400 );
	}

	if ( true !== $result && ! is_int( $result ) ) {
		wp_send_json_error( array( 'message' => implode( ' ', (array) $result ) ), 400 );
	}

	wp_send_json_success(
		array(
			'tree'     => foldery_media_admin_tree_data(),
			'selected' => $selected,
			'message'  => __( 'Media folders updated.', 'foldery' ),
		)
	);
}
add_action( 'wp_ajax_foldery_media_admin', 'foldery_media_admin_ajax' );
