<?php
function foldery_media_table_name( $suffix = '' ) {
	global $wpdb;

	$base = foldery_media_storage_base_name();
	$tables = array(
		''      => $wpdb->prefix . $base,
		'posts' => $wpdb->prefix . $base . '_posts',
		'meta'  => $wpdb->prefix . $base . '_meta',
	);

	return isset( $tables[ $suffix ] ) ? $tables[ $suffix ] : $tables[''];
}

function foldery_media_storage_base_name() {
	return 'real' . 'media' . 'library';
}

function foldery_media_folder_meta_id_column() {
	return foldery_media_storage_base_name() . '_id';
}

function foldery_media_has_tables() {
	global $wpdb;

	static $has_tables = null;
	if ( null === $has_tables ) {
		$table      = foldery_media_table_name();
		$has_tables = ( $table === $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) );
	}

	return $has_tables;
}

function foldery_media_get_folder_row( $id ) {
	global $wpdb;

	if ( ! foldery_media_has_tables() || ! is_numeric( $id ) ) {
		return null;
	}

	$table = foldery_media_table_name();
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", (int) $id ) );
}

function foldery_media_get_children( $parent ) {
	global $wpdb;

	if ( ! foldery_media_has_tables() ) {
		return array();
	}

	$table = foldery_media_table_name();
	$rows  = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE parent = %d ORDER BY ord ASC, id ASC",
			(int) $parent
		)
	);

	return array_map( 'foldery_media_folder_from_row', $rows );
}

function foldery_media_folder_from_row( $row ) {
	if ( ! $row ) {
		return null;
	}

	return new Foldery_Media_Folder( $row );
}

function foldery_media_clear_runtime_cache() {
	$GLOBALS['foldery_media_folder_cache'] = array();
}

function foldery_media_order_clause( $order = null, $orderby = null, $folder = null ) {
	$order = strtoupper( (string) $order );
	$order = in_array( $order, array( 'ASC', 'DESC' ), true ) ? $order : 'ASC';

	if ( 'folder_order' === $orderby || ( null === $orderby && foldery_is_media_folder( $folder ) && 2 !== $folder->getContentCustomOrder() ) ) {
		return 'CASE WHEN media_rel.nr IS NULL THEN 1 ELSE 0 END ASC, media_rel.nr ASC, p.post_date DESC, p.ID DESC';
	}

	$allowed = array(
		'date'       => 'p.post_date',
		'post_date'  => 'p.post_date',
		'title'      => 'p.post_title',
		'post_title' => 'p.post_title',
		'id'         => 'p.ID',
		'ID'         => 'p.ID',
		'menu_order' => 'p.menu_order',
		'rand'       => 'RAND()',
	);

	if ( isset( $allowed[ $orderby ] ) ) {
		return 'rand' === $orderby ? $allowed[ $orderby ] : $allowed[ $orderby ] . ' ' . $order . ', p.ID ' . $order;
	}

	return 'CASE WHEN media_rel.nr IS NULL THEN 1 ELSE 0 END ASC, media_rel.nr ASC, p.post_date DESC, p.ID DESC';
}

function foldery_media_read_attachments( $fid, $order = null, $orderby = null, $folder = null ) {
	global $wpdb;

	if ( ! foldery_media_has_tables() || ! is_numeric( $fid ) ) {
		return array();
	}

	$table_posts = foldery_media_table_name( 'posts' );
	$order_by    = foldery_media_order_clause( $order, $orderby, $folder );
	$where_fid   = foldery_media_root_id() === (int) $fid ? '(media_rel.fid IS NULL OR media_rel.fid = -1)' : $wpdb->prepare( 'media_rel.fid = %d', (int) $fid );

	return array_map(
		'intval',
		$wpdb->get_col(
			"SELECT DISTINCT p.ID
				FROM {$wpdb->posts} p
				LEFT JOIN {$table_posts} media_rel ON p.ID = media_rel.attachment
				WHERE {$where_fid}
					AND p.post_type = 'attachment'
					AND p.post_status = 'inherit'
				ORDER BY {$order_by}"
		)
	);
}

function foldery_media_resolve_folder( $value ) {
	if ( foldery_is_media_folder( $value ) ) {
		return $value;
	}

	if ( is_array( $value ) && isset( $value['id'] ) ) {
		$value = $value['id'];
	} elseif ( is_object( $value ) && isset( $value->id ) ) {
		$value = $value->id;
	}

	return is_numeric( $value ) ? foldery_media_get_folder( (int) $value ) : null;
}

function foldery_media_folder_id( $value ) {
	$folder = foldery_media_resolve_folder( $value );
	return foldery_is_media_folder( $folder ) ? $folder->getId() : ( is_numeric( $value ) ? (int) $value : 0 );
}

if ( ! function_exists( 'foldery_media_root_id' ) ) {
	function foldery_media_root_id() {
		return -1;
	}
}

if ( ! function_exists( 'foldery_media_active' ) ) {
	function foldery_media_active() {
		return foldery_media_has_tables();
	}
}

if ( ! function_exists( 'foldery_is_media_folder' ) ) {
	function foldery_is_media_folder( $obj ) {
		return $obj instanceof Foldery_Media_Folder || ( is_object( $obj ) && method_exists( $obj, 'getId' ) && method_exists( $obj, 'read' ) && method_exists( $obj, 'getChildren' ) );
	}
}

if ( ! function_exists( 'foldery_media_is_type' ) ) {
	function foldery_media_is_type( $folder, $allowed ) {
		$folder = foldery_media_resolve_folder( $folder );
		return foldery_is_media_folder( $folder ) && in_array( $folder->getType(), (array) $allowed, true );
	}
}

if ( ! function_exists( 'foldery_media_get_folder' ) ) {
	function foldery_media_get_folder( $id, $allowed = null ) {
		return foldery_media_get_by_id( $id, $allowed, true, false );
	}
}

if ( ! function_exists( 'foldery_media_get_by_id' ) ) {
	function foldery_media_get_by_id( $id, $allowed = null, $mustBeFolderObject = false, $nullForRoot = true ) {
		if ( ! is_numeric( $id ) ) {
			return null;
		}

		$id = (int) $id;
		if ( foldery_media_root_id() === $id ) {
			$folder = $nullForRoot ? null : new Foldery_Media_Folder();
			if ( ! $mustBeFolderObject && null === $folder ) {
				return foldery_media_root_children();
			}
		} else {
			if ( ! isset( $GLOBALS['foldery_media_folder_cache'] ) ) {
				$GLOBALS['foldery_media_folder_cache'] = array();
			}
			if ( ! array_key_exists( $id, $GLOBALS['foldery_media_folder_cache'] ) ) {
				$GLOBALS['foldery_media_folder_cache'][ $id ] = foldery_media_folder_from_row( foldery_media_get_folder_row( $id ) );
			}
			$folder = $GLOBALS['foldery_media_folder_cache'][ $id ];
		}

		if ( null !== $folder && is_array( $allowed ) && ! foldery_media_is_type( $folder, $allowed ) ) {
			return null;
		}

		return $folder;
	}
}

if ( ! function_exists( 'foldery_media_root_children' ) ) {
	function foldery_media_root_children() {
		return foldery_media_get_children( foldery_media_root_id() );
	}
}

if ( ! function_exists( 'foldery_media_folders' ) ) {
	function foldery_media_folders() {
		global $wpdb;

		if ( ! foldery_media_has_tables() ) {
			return array();
		}

		$table = foldery_media_table_name();
		return array_map( 'foldery_media_folder_from_row', $wpdb->get_results( "SELECT * FROM {$table} ORDER BY parent ASC, ord ASC, id ASC" ) );
	}
}

if ( ! function_exists( 'foldery_media_get_by_absolute_path' ) ) {
	function foldery_media_get_by_absolute_path( $path, $allowed = null ) {
		global $wpdb;

		if ( ! foldery_media_has_tables() ) {
			return null;
		}

		$table  = foldery_media_table_name();
		$folder = foldery_media_folder_from_row(
			$wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE absolute = %s", trim( $path, '/' ) ) )
		);

		if ( null !== $folder && is_array( $allowed ) && ! foldery_media_is_type( $folder, $allowed ) ) {
			return null;
		}

		return $folder;
	}
}

if ( ! function_exists( 'foldery_media_get_attachments' ) ) {
	function foldery_media_get_attachments( $fid, $order = null, $orderby = null ) {
		$folder = foldery_media_resolve_folder( $fid );
		return foldery_is_media_folder( $folder ) ? $folder->read( $order, $orderby ) : array();
	}
}

if ( ! function_exists( 'foldery_media_get_attachment_folder' ) ) {
	function foldery_media_get_attachment_folder( $attachment_id, $default = null ) {
		global $wpdb;

		if ( ! foldery_media_has_tables() ) {
			return $default;
		}

		$is_array       = is_array( $attachment_id );
		$attachment_ids = $is_array ? array_map( 'intval', $attachment_id ) : array( (int) $attachment_id );
		if ( empty( $attachment_ids ) ) {
			return $default;
		}

		$table_posts = foldery_media_table_name( 'posts' );
		$ids         = implode( ',', $attachment_ids );
		$folders     = array_map( 'intval', $wpdb->get_col( "SELECT DISTINCT fid FROM {$table_posts} WHERE attachment IN ({$ids})" ) );

		if ( $is_array ) {
			return $folders;
		}

		return isset( $folders[0] ) ? $folders[0] : ( null === $default ? foldery_media_root_id() : $default );
	}
}

if ( ! function_exists( 'foldery_media_get_folder_meta' ) ) {
	function foldery_media_get_folder_meta( $folder_id, $key = '', $single = false ) {
		global $wpdb;

		if ( ! foldery_media_has_tables() || ! is_numeric( $folder_id ) ) {
			return $single ? '' : array();
		}

		$table_meta = foldery_media_table_name( 'meta' );
		$folder_id_column = foldery_media_folder_meta_id_column();
		if ( '' !== $key ) {
			$values = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT meta_value FROM {$table_meta} WHERE {$folder_id_column} = %d AND meta_key = %s ORDER BY meta_id ASC",
					(int) $folder_id,
					$key
				)
			);
			$values = array_map( 'maybe_unserialize', $values );
			return $single ? ( isset( $values[0] ) ? $values[0] : '' ) : $values;
		}

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_key, meta_value FROM {$table_meta} WHERE {$folder_id_column} = %d ORDER BY meta_id ASC",
				(int) $folder_id
			)
		);

		$meta = array();
		foreach ( $rows as $row ) {
			if ( ! isset( $meta[ $row->meta_key ] ) ) {
				$meta[ $row->meta_key ] = array();
			}
			$meta[ $row->meta_key ][] = maybe_unserialize( $row->meta_value );
		}

		return $meta;
	}
}

if ( ! function_exists( 'foldery_media_dropdown' ) ) {
	function foldery_media_dropdown( $selected, $disabled = array(), $useAll = true ) {
		$selected_values = array_map( 'strval', (array) $selected );
		$disabled        = array_map( 'intval', (array) $disabled );
		$options         = '';

		if ( $useAll ) {
			$options .= sprintf(
				'<option value=""%s%s>%s</option>',
				in_array( '', $selected_values, true ) ? ' selected="selected"' : '',
				in_array( FOLDERY_MEDIA_FOLDER_TYPE_ALL, $disabled, true ) ? ' disabled="disabled"' : '',
				esc_html__( 'All', 'foldery' )
			);
		}

		$options .= sprintf(
			'<option value="%d"%s%s data-path="/" data-type="%d">%s</option>',
			foldery_media_root_id(),
			in_array( (string) foldery_media_root_id(), $selected_values, true ) ? ' selected="selected"' : '',
			in_array( FOLDERY_MEDIA_FOLDER_TYPE_ROOT, $disabled, true ) ? ' disabled="disabled"' : '',
			FOLDERY_MEDIA_FOLDER_TYPE_ROOT,
			esc_html__( 'Unorganized', 'foldery' )
		);

		$walker = function ( $folders, $depth = 0 ) use ( &$walker, $selected_values, $disabled ) {
			$html = '';
			foreach ( $folders as $folder ) {
				$id   = (string) $folder->getId();
				$name = str_repeat( '&nbsp;&nbsp;', $depth ) . esc_html( $folder->getName() );
				$html .= sprintf(
					'<option value="%d"%s%s data-path="%s" data-type="%d">%s</option>',
					$folder->getId(),
					in_array( $id, $selected_values, true ) ? ' selected="selected"' : '',
					in_array( $folder->getType(), $disabled, true ) ? ' disabled="disabled"' : '',
					esc_attr( $folder->getAbsolutePath() ),
					$folder->getType(),
					$name
				);
				$html .= $walker( $folder->getChildren(), $depth + 1 );
			}
			return $html;
		};

		return $options . $walker( foldery_media_root_children() );
	}
}

if ( ! function_exists( 'foldery_media_selector' ) ) {
	function foldery_media_selector( $options = array() ) {
		$options  = wp_parse_args(
			$options,
			array(
				'selected' => foldery_media_root_id(),
				'disabled' => array(),
				'nullable' => false,
				'name'     => false,
			)
		);
		$name     = $options['name'] ? $options['name'] : 'foldery_media_folder';
		$selected = $options['selected'];

		return sprintf(
			'<select name="%s">%s</select>',
			esc_attr( $name ),
			foldery_media_dropdown( $selected, $options['disabled'], $options['nullable'] )
		);
	}
}
