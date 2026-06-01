<?php
function foldery_media_count_attachments_in_folder( $fid ) {
	global $wpdb;

	if ( ! foldery_media_has_tables() ) {
		return 0;
	}

	$table_posts = foldery_media_table_name( 'posts' );
	if ( foldery_media_root_id() === (int) $fid ) {
		return (int) $wpdb->get_var(
			"SELECT COUNT(DISTINCT p.ID)
			FROM {$wpdb->posts} p
			LEFT JOIN {$table_posts} media_rel ON media_rel.attachment = p.ID AND media_rel.isShortcut = 0
			WHERE p.post_type = 'attachment'
				AND p.post_status = 'inherit'
				AND (media_rel.fid IS NULL OR media_rel.fid = -1)"
		);
	}

	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(DISTINCT p.ID)
			FROM {$table_posts} media_rel
			INNER JOIN {$wpdb->posts} p ON p.ID = media_rel.attachment
			WHERE media_rel.fid = %d
				AND media_rel.isShortcut = 0
				AND p.post_type = 'attachment'
				AND p.post_status = 'inherit'",
			(int) $fid
		)
	);
}

function foldery_media_attachment_ids_for_folder( $fid ) {
	global $wpdb;

	$fid = (int) $fid;
	if ( 0 === $fid ) {
		return null;
	}

	if ( foldery_media_root_id() !== $fid ) {
		return foldery_media_get_attachments( $fid, 'ASC', 'folder_order' );
	}

	if ( ! foldery_media_has_tables() ) {
		return array();
	}

	$table_posts = foldery_media_table_name( 'posts' );
	return array_map(
		'intval',
		$wpdb->get_col(
			"SELECT DISTINCT p.ID
			FROM {$wpdb->posts} p
			LEFT JOIN {$table_posts} media_rel ON media_rel.attachment = p.ID AND media_rel.isShortcut = 0
			WHERE p.post_type = 'attachment'
				AND p.post_status = 'inherit'
				AND (media_rel.fid IS NULL OR media_rel.fid = -1)
			ORDER BY p.post_date DESC, p.ID DESC"
		)
	);
}

function foldery_media_update_count( $folders = null ) {
	global $wpdb;

	if ( ! foldery_media_has_tables() ) {
		return false;
	}

	if ( null === $folders ) {
		$table   = foldery_media_table_name();
		$folders = array_map( 'intval', $wpdb->get_col( "SELECT id FROM {$table}" ) );
	} else {
		$folders = array_unique( array_filter( array_map( 'intval', (array) $folders ), 'is_numeric' ) );
	}

	$table = foldery_media_table_name();
	foreach ( $folders as $fid ) {
		if ( $fid > 0 ) {
			$wpdb->update( $table, array( 'cnt' => foldery_media_count_attachments_in_folder( $fid ) ), array( 'id' => $fid ), array( '%d' ), array( '%d' ) );
		}
	}

	foldery_media_clear_runtime_cache();
	return true;
}

function foldery_media_unique_slug( $name, $parent, $exclude_id = 0 ) {
	global $wpdb;

	$slug  = sanitize_title( $name );
	$slug  = '' === $slug ? 'folder' : $slug;
	$base  = $slug;
	$count = 2;
	$table = foldery_media_table_name();

	while (
		$wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$table} WHERE parent = %d AND slug = %s AND id <> %d LIMIT 1",
				(int) $parent,
				$slug,
				(int) $exclude_id
			)
		)
	) {
		$slug = $base . '-' . $count;
		$count++;
	}

	return $slug;
}

function foldery_media_absolute_path_for( $parent, $slug ) {
	$parent = (int) $parent;
	if ( $parent <= 0 ) {
		return $slug;
	}

	$parent_folder = foldery_media_get_folder( $parent );
	if ( ! foldery_is_media_folder( $parent_folder ) ) {
		return $slug;
	}

	return trim( $parent_folder->getAbsolutePath() . '/' . $slug, '/' );
}

function foldery_media_refresh_child_absolute_paths( $parent ) {
	global $wpdb;

	$table    = foldery_media_table_name();
	$children = $wpdb->get_results( $wpdb->prepare( "SELECT id, slug FROM {$table} WHERE parent = %d", (int) $parent ) );
	foreach ( $children as $child ) {
		$absolute = foldery_media_absolute_path_for( $parent, $child->slug );
		$wpdb->update( $table, array( 'absolute' => $absolute ), array( 'id' => (int) $child->id ), array( '%s' ), array( '%d' ) );
		foldery_media_refresh_child_absolute_paths( (int) $child->id );
	}
}

function foldery_media_create_folder( $name, $parent = -1 ) {
	global $wpdb;

	$name   = trim( wp_strip_all_tags( (string) $name ) );
	$parent = is_numeric( $parent ) ? (int) $parent : foldery_media_root_id();
	if ( '' === $name ) {
		return array( __( 'Folder name is required.', 'foldery' ) );
	}
	if ( foldery_media_root_id() !== $parent && ! foldery_is_media_folder( foldery_media_get_folder( $parent ) ) ) {
		return array( __( 'Parent folder does not exist.', 'foldery' ) );
	}

	$table = foldery_media_table_name();
	$dupe  = $wpdb->get_var(
		$wpdb->prepare( "SELECT id FROM {$table} WHERE parent = %d AND name = %s LIMIT 1", $parent, $name )
	);
	if ( $dupe ) {
		return array( __( 'A folder with this name already exists here.', 'foldery' ) );
	}

	$slug     = foldery_media_unique_slug( $name, $parent );
	$absolute = foldery_media_absolute_path_for( $parent, $slug );
	$ord      = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(MAX(ord), 0) + 1 FROM {$table} WHERE parent = %d", $parent ) );
	$inserted = $wpdb->insert(
		$table,
		array(
			'parent'             => $parent,
			'name'               => $name,
			'slug'               => $slug,
			'absolute'           => $absolute,
			'owner'              => get_current_user_id(),
			'ord'                => $ord,
			'type'               => (string) FOLDERY_MEDIA_FOLDER_TYPE_FOLDER,
			'restrictions'       => '',
			'cnt'                => 0,
			'contentCustomOrder' => 1,
		),
		array( '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d' )
	);

	if ( ! $inserted ) {
		return array( __( 'Folder could not be created.', 'foldery' ) );
	}

	foldery_media_clear_runtime_cache();
	return (int) $wpdb->insert_id;
}

function foldery_media_rename_folder( $name, $id ) {
	global $wpdb;

	$name   = trim( wp_strip_all_tags( (string) $name ) );
	$id     = (int) $id;
	$folder = foldery_media_get_folder( $id );
	if ( '' === $name ) {
		return array( __( 'Folder name is required.', 'foldery' ) );
	}
	if ( ! foldery_is_media_folder( $folder ) || $id <= 0 ) {
		return array( __( 'Folder does not exist.', 'foldery' ) );
	}

	$table = foldery_media_table_name();
	$dupe  = $wpdb->get_var(
		$wpdb->prepare( "SELECT id FROM {$table} WHERE parent = %d AND name = %s AND id <> %d LIMIT 1", $folder->getParent(), $name, $id )
	);
	if ( $dupe ) {
		return array( __( 'A folder with this name already exists here.', 'foldery' ) );
	}

	$slug     = foldery_media_unique_slug( $name, $folder->getParent(), $id );
	$absolute = foldery_media_absolute_path_for( $folder->getParent(), $slug );
	$updated  = $wpdb->update(
		$table,
		array(
			'name'     => $name,
			'slug'     => $slug,
			'absolute' => $absolute,
		),
		array( 'id' => $id ),
		array( '%s', '%s', '%s' ),
		array( '%d' )
	);

	if ( false === $updated ) {
		return array( __( 'Folder could not be renamed.', 'foldery' ) );
	}

	foldery_media_clear_runtime_cache();
	foldery_media_refresh_child_absolute_paths( $id );
	foldery_media_clear_runtime_cache();
	return true;
}

function foldery_media_reorder_folders( $tree ) {
	global $wpdb;

	if ( ! foldery_media_has_tables() || ! is_array( $tree ) ) {
		return array( __( 'Folder order could not be saved.', 'foldery' ) );
	}

	$folders = foldery_media_folders();
	$known   = array();
	foreach ( $folders as $folder ) {
		if ( foldery_is_media_folder( $folder ) && $folder->getId() > 0 ) {
			$known[ $folder->getId() ] = $folder;
		}
	}

	$updates = array();
	foreach ( $tree as $row ) {
		if ( ! is_array( $row ) || ! isset( $row['id'], $row['parent'] ) ) {
			continue;
		}

		$id     = (int) $row['id'];
		$parent = (int) $row['parent'];
		$ord    = isset( $row['ord'] ) ? (int) $row['ord'] : count( $updates ) + 1;
		if ( ! isset( $known[ $id ] ) ) {
			return array( __( 'Folder does not exist.', 'foldery' ) );
		}
		if ( foldery_media_root_id() !== $parent && ! isset( $known[ $parent ] ) ) {
			return array( __( 'Parent folder does not exist.', 'foldery' ) );
		}

		$updates[ $id ] = array(
			'parent' => $parent,
			'ord'    => max( 1, $ord ),
		);
	}

	if ( empty( $updates ) ) {
		return array( __( 'Folder order could not be saved.', 'foldery' ) );
	}

	foreach ( $updates as $id => $update ) {
		$parent = $update['parent'];
		while ( foldery_media_root_id() !== $parent ) {
			if ( $parent === $id ) {
				return array( __( 'A folder cannot be moved into itself.', 'foldery' ) );
			}
			if ( isset( $updates[ $parent ] ) ) {
				$parent = $updates[ $parent ]['parent'];
			} elseif ( isset( $known[ $parent ] ) ) {
				$parent = $known[ $parent ]->getParent();
			} else {
				return array( __( 'Parent folder does not exist.', 'foldery' ) );
			}
		}
	}

	$names_by_parent = array();
	foreach ( $known as $id => $folder ) {
		$parent = isset( $updates[ $id ] ) ? $updates[ $id ]['parent'] : $folder->getParent();
		$name   = function_exists( 'mb_strtolower' ) ? mb_strtolower( $folder->getName() ) : strtolower( $folder->getName() );
		$key    = $parent . '|' . $name;
		if ( isset( $names_by_parent[ $key ] ) ) {
			return array( __( 'A folder with this name already exists here.', 'foldery' ) );
		}
		$names_by_parent[ $key ] = true;
	}

	$table = foldery_media_table_name();
	foreach ( $updates as $id => $update ) {
		$folder   = $known[ $id ];
		$slug     = foldery_media_unique_slug( $folder->getName(), $update['parent'], $id );
		$wpdb->update(
			$table,
			array(
				'parent' => $update['parent'],
				'ord'    => $update['ord'],
				'slug'   => $slug,
			),
			array( 'id' => $id ),
			array( '%d', '%d', '%s' ),
			array( '%d' )
		);
	}

	foldery_media_clear_runtime_cache();
	foldery_media_refresh_child_absolute_paths( foldery_media_root_id() );
	foldery_media_clear_runtime_cache();
	return true;
}

function foldery_media_delete_folder( $id ) {
	global $wpdb;

	$id     = (int) $id;
	$folder = foldery_media_get_folder( $id );
	if ( ! foldery_is_media_folder( $folder ) || $id <= 0 ) {
		return array( __( 'Folder does not exist.', 'foldery' ) );
	}
	if ( count( $folder->getChildren() ) > 0 ) {
		return array( __( 'The folder has subfolders.', 'foldery' ) );
	}

	$table       = foldery_media_table_name();
	$table_posts = foldery_media_table_name( 'posts' );
	$table_meta  = foldery_media_table_name( 'meta' );
	$folder_id_column = foldery_media_folder_meta_id_column();
	$wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );
	$wpdb->delete( $table_posts, array( 'fid' => $id ), array( '%d' ) );
	$wpdb->delete( $table_meta, array( $folder_id_column => $id ), array( '%d' ) );

	foldery_media_clear_runtime_cache();
	return true;
}

function foldery_media_move_attachments( $to, $ids ) {
	global $wpdb;

	$to  = is_numeric( $to ) ? (int) $to : foldery_media_root_id();
	$ids = array_values( array_unique( array_filter( array_map( 'intval', (array) $ids ) ) ) );
	if ( empty( $ids ) ) {
		return array( __( 'No media selected.', 'foldery' ) );
	}
	if ( foldery_media_root_id() !== $to && ! foldery_is_media_folder( foldery_media_get_folder( $to ) ) ) {
		return array( __( 'Destination folder does not exist.', 'foldery' ) );
	}

	$table_posts = foldery_media_table_name( 'posts' );
	$sources     = foldery_media_get_attachment_folder( $ids );
	foreach ( $ids as $attachment_id ) {
		$wpdb->delete( $table_posts, array( 'attachment' => $attachment_id, 'isShortcut' => 0 ), array( '%d', '%d' ) );
	}

	if ( foldery_media_root_id() !== $to ) {
		$next = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(MAX(nr), 0) + 1 FROM {$table_posts} WHERE fid = %d", $to ) );
		foreach ( $ids as $attachment_id ) {
			$wpdb->insert(
				$table_posts,
				array(
					'attachment' => $attachment_id,
					'fid'        => $to,
					'isShortcut' => 0,
					'nr'         => $next,
				),
				array( '%d', '%d', '%d', '%d' )
			);
			$next++;
		}
	}

	foldery_media_update_count( array_merge( (array) $sources, array( $to ) ) );
	return true;
}

function foldery_media_reorder_attachments( $fid, $ids ) {
	global $wpdb;

	$fid = is_numeric( $fid ) ? (int) $fid : 0;
	$ids = array_values( array_unique( array_filter( array_map( 'intval', (array) $ids ) ) ) );
	if ( empty( $ids ) ) {
		return array( __( 'No media selected.', 'foldery' ) );
	}
	if ( $fid <= 0 || foldery_media_root_id() === $fid || ! foldery_is_media_folder( foldery_media_get_folder( $fid ) ) ) {
		return array( __( 'Select a folder before reordering media.', 'foldery' ) );
	}

	$current = foldery_media_get_attachments( $fid, 'ASC', 'folder_order' );
	$current = array_values( array_map( 'intval', $current ) );
	$ordered = array_values( array_intersect( $ids, $current ) );
	$ordered = array_merge( $ordered, array_values( array_diff( $current, $ordered ) ) );

	$table_posts = foldery_media_table_name( 'posts' );
	foreach ( $ordered as $index => $attachment_id ) {
		$wpdb->update(
			$table_posts,
			array( 'nr' => $index + 1 ),
			array(
				'fid'        => $fid,
				'attachment' => $attachment_id,
				'isShortcut' => 0,
			),
			array( '%d' ),
			array( '%d', '%d', '%d' )
		);
	}

	return true;
}

if ( ! function_exists( 'foldery_media_create' ) ) {
	function foldery_media_create( $name, $parent, $type = FOLDERY_MEDIA_FOLDER_TYPE_FOLDER, $restrictions = array(), $supress_validation = false, $return_existing_id = false ) {
		return foldery_media_create_folder( $name, $parent );
	}
}

if ( ! function_exists( 'foldery_media_rename' ) ) {
	function foldery_media_rename( $name, $id, $supress_validation = false ) {
		return foldery_media_rename_folder( $name, $id );
	}
}

if ( ! function_exists( 'foldery_media_delete' ) ) {
	function foldery_media_delete( $id, $supress_validation = false ) {
		return foldery_media_delete_folder( $id );
	}
}

if ( ! function_exists( 'foldery_media_move' ) ) {
	function foldery_media_move( $to, $ids, $supress_validation = false, $isShortcut = false ) {
		return foldery_media_move_attachments( $to, $ids );
	}
}
