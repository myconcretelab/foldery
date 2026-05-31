<?php
/**
 * Local Real Media Library compatibility.
 *
 * The site only needs the folder tree, folder metadata and ordered attachment
 * reads on the front end. Keep those features in the theme so RML can be
 * disabled without changing existing ACF values or shortcodes.
 */

if ( ! defined( 'RML_TYPE_FOLDER' ) ) {
	define( 'RML_TYPE_FOLDER', 0 );
}
if ( ! defined( 'RML_TYPE_COLLECTION' ) ) {
	define( 'RML_TYPE_COLLECTION', 1 );
}
if ( ! defined( 'RML_TYPE_GALLERY' ) ) {
	define( 'RML_TYPE_GALLERY', 2 );
}
if ( ! defined( 'RML_TYPE_ALL' ) ) {
	define( 'RML_TYPE_ALL', 3 );
}
if ( ! defined( 'RML_TYPE_ROOT' ) ) {
	define( 'RML_TYPE_ROOT', 4 );
}

if ( ! class_exists( 'Foldery_RML_Folder' ) ) {
	class Foldery_RML_Folder {
		protected $row;
		protected $children = null;

		public function __construct( $row = null ) {
			$this->row = (object) wp_parse_args(
				(array) $row,
				array(
					'id'                 => -1,
					'parent'             => null,
					'name'               => '/Unorganized',
					'slug'               => '/',
					'absolute'           => '/',
					'ord'                => 0,
					'cnt'                => null,
					'type'               => RML_TYPE_ROOT,
					'contentCustomOrder' => 2,
					'restrictions'       => '',
					'owner'              => 0,
				)
			);
			$this->row->id     = (int) $this->row->id;
			$this->row->parent = null === $this->row->parent ? null : (int) $this->row->parent;
			$this->row->type   = (int) $this->row->type;
		}

		public function getId() {
			return (int) $this->row->id;
		}

		public function getParent() {
			return null === $this->row->parent ? null : (int) $this->row->parent;
		}

		public function getName( $htmlentities = false ) {
			return $htmlentities ? htmlentities( $this->row->name ) : $this->row->name;
		}

		public function getSlug( $force = false, $fromSetName = false ) {
			return $this->row->slug;
		}

		public function getAbsolutePath( $force = false, $fromSetName = false ) {
			return $this->row->absolute;
		}

		public function getOrder() {
			return (int) $this->row->ord;
		}

		public function getOwner() {
			return (int) $this->row->owner;
		}

		public function getType() {
			return (int) $this->row->type;
		}

		public function getContentCustomOrder() {
			return (int) $this->row->contentCustomOrder;
		}

		public function forceContentCustomOrder() {
			return false;
		}

		public function getRestrictions() {
			return empty( $this->row->restrictions ) ? array() : explode( ',', $this->row->restrictions );
		}

		public function getRestrictionsCount() {
			return count( $this->getRestrictions() );
		}

		public function isRestrictFor( $permission ) {
			return in_array( $permission, $this->getRestrictions(), true );
		}

		public function is( $folder_type ) {
			return (int) $folder_type === $this->getType();
		}

		public function getRowData( $key = null ) {
			if ( null === $key ) {
				return $this->row;
			}

			return property_exists( $this->row, $key ) ? $this->row->{$key} : null;
		}

		public function getPath( $implode = '/', $map = 'htmlentities', $filter = null ) {
			if ( RML_TYPE_ROOT === $this->getType() ) {
				return $this->getName( true );
			}

			$path   = array();
			$folder = $this;
			while ( is_rml_folder( $folder ) && RML_TYPE_ROOT !== $folder->getType() ) {
				if ( ! isset( $filter ) || call_user_func( $filter, $folder ) ) {
					$name = $folder->getName();
					if ( 'htmlentities' === $map ) {
						$name = htmlentities( $name );
					} elseif ( null !== $map ) {
						$name = call_user_func( $map, $name, $folder );
					}
					array_unshift( $path, $name );
				}
				$folder = wp_rml_get_object_by_id( $folder->getParent() );
			}

			return implode( $implode, $path );
		}

		public function getCnt( $forceReload = false ) {
			if ( null !== $this->row->cnt && ! $forceReload ) {
				return max( 0, (int) $this->row->cnt );
			}

			global $wpdb;
			$table_posts = foldery_rml_table_name( 'posts' );
			$count       = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(DISTINCT p.ID)
					FROM {$table_posts} rml
					INNER JOIN {$wpdb->posts} p ON p.ID = rml.attachment
					WHERE rml.fid = %d
						AND p.post_type = 'attachment'
						AND p.post_status = 'inherit'",
					$this->getId()
				)
			);

			$this->row->cnt = (int) $count;
			return $this->row->cnt;
		}

		public function getChildren() {
			if ( null !== $this->children ) {
				return $this->children;
			}

			$this->children = foldery_rml_get_children( $this->getId() );
			return $this->children;
		}

		public function read( $order = null, $orderby = null ) {
			return foldery_rml_read_attachments( $this->getId(), $order, $orderby, $this );
		}

		public function getPlain( $deep = false ) {
			$plain = array(
				'id'                 => $this->getId(),
				'type'               => $this->getType(),
				'parent'             => $this->getParent(),
				'name'               => $this->getName(),
				'order'              => $this->getOrder(),
				'restrictions'       => $this->getRestrictions(),
				'slug'               => $this->getSlug(),
				'absolutePath'       => $this->getAbsolutePath(),
				'cnt'                => $this->getCnt(),
				'contentCustomOrder' => $this->getContentCustomOrder(),
			);

			if ( $deep ) {
				$plain['children'] = array_map(
					function ( $child ) {
						return $child->getPlain( true );
					},
					$this->getChildren()
				);
			}

			return $plain;
		}
	}
}

function foldery_rml_table_name( $suffix = '' ) {
	global $wpdb;

	$tables = array(
		''      => $wpdb->prefix . 'realmedialibrary',
		'posts' => $wpdb->prefix . 'realmedialibrary_posts',
		'meta'  => $wpdb->prefix . 'realmedialibrary_meta',
	);

	return isset( $tables[ $suffix ] ) ? $tables[ $suffix ] : $tables[''];
}

function foldery_rml_has_tables() {
	global $wpdb;

	static $has_tables = null;
	if ( null === $has_tables ) {
		$table      = foldery_rml_table_name();
		$has_tables = ( $table === $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) );
	}

	return $has_tables;
}

function foldery_rml_get_folder_row( $id ) {
	global $wpdb;

	if ( ! foldery_rml_has_tables() || ! is_numeric( $id ) ) {
		return null;
	}

	$table = foldery_rml_table_name();
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", (int) $id ) );
}

function foldery_rml_get_children( $parent ) {
	global $wpdb;

	if ( ! foldery_rml_has_tables() ) {
		return array();
	}

	$table = foldery_rml_table_name();
	$rows  = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE parent = %d ORDER BY ord ASC, id ASC",
			(int) $parent
		)
	);

	return array_map( 'foldery_rml_folder_from_row', $rows );
}

function foldery_rml_folder_from_row( $row ) {
	if ( ! $row ) {
		return null;
	}

	return new Foldery_RML_Folder( $row );
}

function foldery_rml_clear_runtime_cache() {
	$GLOBALS['foldery_rml_folder_cache'] = array();
}

function foldery_rml_order_clause( $order = null, $orderby = null, $folder = null ) {
	$order = strtoupper( (string) $order );
	$order = in_array( $order, array( 'ASC', 'DESC' ), true ) ? $order : 'ASC';

	if ( 'rml' === $orderby || ( null === $orderby && is_rml_folder( $folder ) && 2 !== $folder->getContentCustomOrder() ) ) {
		return 'CASE WHEN rml.nr IS NULL THEN 1 ELSE 0 END ASC, rml.nr ASC, p.post_date DESC, p.ID DESC';
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

	return 'CASE WHEN rml.nr IS NULL THEN 1 ELSE 0 END ASC, rml.nr ASC, p.post_date DESC, p.ID DESC';
}

function foldery_rml_read_attachments( $fid, $order = null, $orderby = null, $folder = null ) {
	global $wpdb;

	if ( ! foldery_rml_has_tables() || ! is_numeric( $fid ) ) {
		return array();
	}

	$table_posts = foldery_rml_table_name( 'posts' );
	$order_by    = foldery_rml_order_clause( $order, $orderby, $folder );
	$where_fid   = _wp_rml_root() === (int) $fid ? '(rml.fid IS NULL OR rml.fid = -1)' : $wpdb->prepare( 'rml.fid = %d', (int) $fid );

	return array_map(
		'intval',
		$wpdb->get_col(
			"SELECT DISTINCT p.ID
				FROM {$wpdb->posts} p
				LEFT JOIN {$table_posts} rml ON p.ID = rml.attachment
				WHERE {$where_fid}
					AND p.post_type = 'attachment'
					AND p.post_status = 'inherit'
				ORDER BY {$order_by}"
		)
	);
}

function foldery_rml_resolve_folder( $value ) {
	if ( is_rml_folder( $value ) ) {
		return $value;
	}

	if ( is_array( $value ) && isset( $value['id'] ) ) {
		$value = $value['id'];
	} elseif ( is_object( $value ) && isset( $value->id ) ) {
		$value = $value->id;
	}

	return is_numeric( $value ) ? wp_rml_get_object_by_id( (int) $value ) : null;
}

function foldery_rml_folder_id( $value ) {
	$folder = foldery_rml_resolve_folder( $value );
	return is_rml_folder( $folder ) ? $folder->getId() : ( is_numeric( $value ) ? (int) $value : 0 );
}

if ( ! function_exists( '_wp_rml_root' ) ) {
	function _wp_rml_root() {
		return -1;
	}
}

if ( ! function_exists( 'wp_rml_active' ) ) {
	function wp_rml_active() {
		return foldery_rml_has_tables();
	}
}

if ( ! function_exists( 'is_rml_folder' ) ) {
	function is_rml_folder( $obj ) {
		return $obj instanceof Foldery_RML_Folder || ( is_object( $obj ) && method_exists( $obj, 'getId' ) && method_exists( $obj, 'read' ) && method_exists( $obj, 'getChildren' ) );
	}
}

if ( ! function_exists( 'wp_rml_is_type' ) ) {
	function wp_rml_is_type( $folder, $allowed ) {
		$folder = foldery_rml_resolve_folder( $folder );
		return is_rml_folder( $folder ) && in_array( $folder->getType(), (array) $allowed, true );
	}
}

if ( ! function_exists( 'wp_rml_get_object_by_id' ) ) {
	function wp_rml_get_object_by_id( $id, $allowed = null ) {
		return wp_rml_get_by_id( $id, $allowed, true, false );
	}
}

if ( ! function_exists( 'wp_rml_get_by_id' ) ) {
	function wp_rml_get_by_id( $id, $allowed = null, $mustBeFolderObject = false, $nullForRoot = true ) {
		if ( ! is_numeric( $id ) ) {
			return null;
		}

		$id = (int) $id;
		if ( _wp_rml_root() === $id ) {
			$folder = $nullForRoot ? null : new Foldery_RML_Folder();
			if ( ! $mustBeFolderObject && null === $folder ) {
				return wp_rml_root_childs();
			}
		} else {
			if ( ! isset( $GLOBALS['foldery_rml_folder_cache'] ) ) {
				$GLOBALS['foldery_rml_folder_cache'] = array();
			}
			if ( ! array_key_exists( $id, $GLOBALS['foldery_rml_folder_cache'] ) ) {
				$GLOBALS['foldery_rml_folder_cache'][ $id ] = foldery_rml_folder_from_row( foldery_rml_get_folder_row( $id ) );
			}
			$folder = $GLOBALS['foldery_rml_folder_cache'][ $id ];
		}

		if ( null !== $folder && is_array( $allowed ) && ! wp_rml_is_type( $folder, $allowed ) ) {
			return null;
		}

		return $folder;
	}
}

if ( ! function_exists( 'wp_rml_root_childs' ) ) {
	function wp_rml_root_childs() {
		return foldery_rml_get_children( _wp_rml_root() );
	}
}

if ( ! function_exists( 'wp_rml_objects' ) ) {
	function wp_rml_objects() {
		global $wpdb;

		if ( ! foldery_rml_has_tables() ) {
			return array();
		}

		$table = foldery_rml_table_name();
		return array_map( 'foldery_rml_folder_from_row', $wpdb->get_results( "SELECT * FROM {$table} ORDER BY parent ASC, ord ASC, id ASC" ) );
	}
}

if ( ! function_exists( 'wp_rml_get_by_absolute_path' ) ) {
	function wp_rml_get_by_absolute_path( $path, $allowed = null ) {
		global $wpdb;

		if ( ! foldery_rml_has_tables() ) {
			return null;
		}

		$table  = foldery_rml_table_name();
		$folder = foldery_rml_folder_from_row(
			$wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE absolute = %s", trim( $path, '/' ) ) )
		);

		if ( null !== $folder && is_array( $allowed ) && ! wp_rml_is_type( $folder, $allowed ) ) {
			return null;
		}

		return $folder;
	}
}

if ( ! function_exists( 'wp_rml_get_attachments' ) ) {
	function wp_rml_get_attachments( $fid, $order = null, $orderby = null ) {
		$folder = foldery_rml_resolve_folder( $fid );
		return is_rml_folder( $folder ) ? $folder->read( $order, $orderby ) : array();
	}
}

if ( ! function_exists( 'wp_attachment_folder' ) ) {
	function wp_attachment_folder( $attachment_id, $default = null ) {
		global $wpdb;

		if ( ! foldery_rml_has_tables() ) {
			return $default;
		}

		$is_array       = is_array( $attachment_id );
		$attachment_ids = $is_array ? array_map( 'intval', $attachment_id ) : array( (int) $attachment_id );
		if ( empty( $attachment_ids ) ) {
			return $default;
		}

		$table_posts = foldery_rml_table_name( 'posts' );
		$ids         = implode( ',', $attachment_ids );
		$folders     = array_map( 'intval', $wpdb->get_col( "SELECT DISTINCT fid FROM {$table_posts} WHERE attachment IN ({$ids})" ) );

		if ( $is_array ) {
			return $folders;
		}

		return isset( $folders[0] ) ? $folders[0] : ( null === $default ? _wp_rml_root() : $default );
	}
}

if ( ! function_exists( 'get_media_folder_meta' ) ) {
	function get_media_folder_meta( $folder_id, $key = '', $single = false ) {
		global $wpdb;

		if ( ! foldery_rml_has_tables() || ! is_numeric( $folder_id ) ) {
			return $single ? '' : array();
		}

		$table_meta = foldery_rml_table_name( 'meta' );
		if ( '' !== $key ) {
			$values = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT meta_value FROM {$table_meta} WHERE realmedialibrary_id = %d AND meta_key = %s ORDER BY meta_id ASC",
					(int) $folder_id,
					$key
				)
			);
			$values = array_map( 'maybe_unserialize', $values );
			return $single ? ( isset( $values[0] ) ? $values[0] : '' ) : $values;
		}

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_key, meta_value FROM {$table_meta} WHERE realmedialibrary_id = %d ORDER BY meta_id ASC",
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

if ( ! function_exists( 'wp_rml_dropdown' ) ) {
	function wp_rml_dropdown( $selected, $disabled = array(), $useAll = true ) {
		$selected_values = array_map( 'strval', (array) $selected );
		$disabled        = array_map( 'intval', (array) $disabled );
		$options         = '';

		if ( $useAll ) {
			$options .= sprintf(
				'<option value=""%s%s>%s</option>',
				in_array( '', $selected_values, true ) ? ' selected="selected"' : '',
				in_array( RML_TYPE_ALL, $disabled, true ) ? ' disabled="disabled"' : '',
				esc_html__( 'All', 'foldery' )
			);
		}

		$options .= sprintf(
			'<option value="%d"%s%s data-path="/" data-type="%d">%s</option>',
			_wp_rml_root(),
			in_array( (string) _wp_rml_root(), $selected_values, true ) ? ' selected="selected"' : '',
			in_array( RML_TYPE_ROOT, $disabled, true ) ? ' disabled="disabled"' : '',
			RML_TYPE_ROOT,
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

		return $options . $walker( wp_rml_root_childs() );
	}
}

if ( ! function_exists( 'wp_rml_selector' ) ) {
	function wp_rml_selector( $options = array() ) {
		$options  = wp_parse_args(
			$options,
			array(
				'selected' => _wp_rml_root(),
				'disabled' => array(),
				'nullable' => false,
				'name'     => false,
			)
		);
		$name     = $options['name'] ? $options['name'] : 'rml_folder';
		$selected = $options['selected'];

		return sprintf(
			'<select name="%s">%s</select>',
			esc_attr( $name ),
			wp_rml_dropdown( $selected, $options['disabled'], $options['nullable'] )
		);
	}
}

function foldery_rml_gallery_atts( $out, $pairs, $atts ) {
	$atts = shortcode_atts(
		array(
			'fid'            => -2,
			'order'          => 'ASC',
			'orderby'        => 'rml',
			'posts_per_page' => -1,
		),
		$atts
	);

	if ( isset( $out['fid'] ) && (int) $out['fid'] > -2 ) {
		$atts['fid'] = (int) $out['fid'];
	}

	if ( (int) $atts['fid'] > -2 ) {
		$ids = wp_rml_get_attachments( (int) $atts['fid'], $atts['order'], $atts['orderby'] );
		$out['include'] = $ids ? implode( ',', $ids ) : '0';
		$out['orderby'] = 'post__in';
		unset( $out['fid'] );
	}

	return $out;
}
add_filter( 'shortcode_atts_gallery', 'foldery_rml_gallery_atts', 10, 3 );

function foldery_rml_register_gallery_shortcode() {
	global $shortcode_tags;

	if ( ! shortcode_exists( 'folder-gallery' ) && isset( $shortcode_tags['gallery'] ) ) {
		add_shortcode( 'folder-gallery', $shortcode_tags['gallery'] );
	}
}
add_action( 'init', 'foldery_rml_register_gallery_shortcode', 20 );

function foldery_rml_count_attachments_in_folder( $fid ) {
	global $wpdb;

	if ( ! foldery_rml_has_tables() ) {
		return 0;
	}

	$table_posts = foldery_rml_table_name( 'posts' );
	if ( _wp_rml_root() === (int) $fid ) {
		return (int) $wpdb->get_var(
			"SELECT COUNT(DISTINCT p.ID)
			FROM {$wpdb->posts} p
			LEFT JOIN {$table_posts} rml ON rml.attachment = p.ID AND rml.isShortcut = 0
			WHERE p.post_type = 'attachment'
				AND p.post_status = 'inherit'
				AND (rml.fid IS NULL OR rml.fid = -1)"
		);
	}

	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(DISTINCT p.ID)
			FROM {$table_posts} rml
			INNER JOIN {$wpdb->posts} p ON p.ID = rml.attachment
			WHERE rml.fid = %d
				AND rml.isShortcut = 0
				AND p.post_type = 'attachment'
				AND p.post_status = 'inherit'",
			(int) $fid
		)
	);
}

function foldery_rml_attachment_ids_for_folder( $fid ) {
	global $wpdb;

	$fid = (int) $fid;
	if ( 0 === $fid ) {
		return null;
	}

	if ( _wp_rml_root() !== $fid ) {
		return wp_rml_get_attachments( $fid, 'ASC', 'rml' );
	}

	if ( ! foldery_rml_has_tables() ) {
		return array();
	}

	$table_posts = foldery_rml_table_name( 'posts' );
	return array_map(
		'intval',
		$wpdb->get_col(
			"SELECT DISTINCT p.ID
			FROM {$wpdb->posts} p
			LEFT JOIN {$table_posts} rml ON rml.attachment = p.ID AND rml.isShortcut = 0
			WHERE p.post_type = 'attachment'
				AND p.post_status = 'inherit'
				AND (rml.fid IS NULL OR rml.fid = -1)
			ORDER BY p.post_date DESC, p.ID DESC"
		)
	);
}

function foldery_rml_update_count( $folders = null ) {
	global $wpdb;

	if ( ! foldery_rml_has_tables() ) {
		return false;
	}

	if ( null === $folders ) {
		$table   = foldery_rml_table_name();
		$folders = array_map( 'intval', $wpdb->get_col( "SELECT id FROM {$table}" ) );
	} else {
		$folders = array_unique( array_filter( array_map( 'intval', (array) $folders ), 'is_numeric' ) );
	}

	$table = foldery_rml_table_name();
	foreach ( $folders as $fid ) {
		if ( $fid > 0 ) {
			$wpdb->update( $table, array( 'cnt' => foldery_rml_count_attachments_in_folder( $fid ) ), array( 'id' => $fid ), array( '%d' ), array( '%d' ) );
		}
	}

	foldery_rml_clear_runtime_cache();
	return true;
}

function foldery_rml_unique_slug( $name, $parent, $exclude_id = 0 ) {
	global $wpdb;

	$slug  = sanitize_title( $name );
	$slug  = '' === $slug ? 'folder' : $slug;
	$base  = $slug;
	$count = 2;
	$table = foldery_rml_table_name();

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

function foldery_rml_absolute_path_for( $parent, $slug ) {
	$parent = (int) $parent;
	if ( $parent <= 0 ) {
		return $slug;
	}

	$parent_folder = wp_rml_get_object_by_id( $parent );
	if ( ! is_rml_folder( $parent_folder ) ) {
		return $slug;
	}

	return trim( $parent_folder->getAbsolutePath() . '/' . $slug, '/' );
}

function foldery_rml_refresh_child_absolute_paths( $parent ) {
	global $wpdb;

	$table    = foldery_rml_table_name();
	$children = $wpdb->get_results( $wpdb->prepare( "SELECT id, slug FROM {$table} WHERE parent = %d", (int) $parent ) );
	foreach ( $children as $child ) {
		$absolute = foldery_rml_absolute_path_for( $parent, $child->slug );
		$wpdb->update( $table, array( 'absolute' => $absolute ), array( 'id' => (int) $child->id ), array( '%s' ), array( '%d' ) );
		foldery_rml_refresh_child_absolute_paths( (int) $child->id );
	}
}

function foldery_rml_create_folder( $name, $parent = -1 ) {
	global $wpdb;

	$name   = trim( wp_strip_all_tags( (string) $name ) );
	$parent = is_numeric( $parent ) ? (int) $parent : _wp_rml_root();
	if ( '' === $name ) {
		return array( __( 'Folder name is required.', 'foldery' ) );
	}
	if ( _wp_rml_root() !== $parent && ! is_rml_folder( wp_rml_get_object_by_id( $parent ) ) ) {
		return array( __( 'Parent folder does not exist.', 'foldery' ) );
	}

	$table = foldery_rml_table_name();
	$dupe  = $wpdb->get_var(
		$wpdb->prepare( "SELECT id FROM {$table} WHERE parent = %d AND name = %s LIMIT 1", $parent, $name )
	);
	if ( $dupe ) {
		return array( __( 'A folder with this name already exists here.', 'foldery' ) );
	}

	$slug     = foldery_rml_unique_slug( $name, $parent );
	$absolute = foldery_rml_absolute_path_for( $parent, $slug );
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
			'type'               => (string) RML_TYPE_FOLDER,
			'restrictions'       => '',
			'cnt'                => 0,
			'contentCustomOrder' => 1,
		),
		array( '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d' )
	);

	if ( ! $inserted ) {
		return array( __( 'Folder could not be created.', 'foldery' ) );
	}

	foldery_rml_clear_runtime_cache();
	return (int) $wpdb->insert_id;
}

function foldery_rml_rename_folder( $name, $id ) {
	global $wpdb;

	$name   = trim( wp_strip_all_tags( (string) $name ) );
	$id     = (int) $id;
	$folder = wp_rml_get_object_by_id( $id );
	if ( '' === $name ) {
		return array( __( 'Folder name is required.', 'foldery' ) );
	}
	if ( ! is_rml_folder( $folder ) || $id <= 0 ) {
		return array( __( 'Folder does not exist.', 'foldery' ) );
	}

	$table = foldery_rml_table_name();
	$dupe  = $wpdb->get_var(
		$wpdb->prepare( "SELECT id FROM {$table} WHERE parent = %d AND name = %s AND id <> %d LIMIT 1", $folder->getParent(), $name, $id )
	);
	if ( $dupe ) {
		return array( __( 'A folder with this name already exists here.', 'foldery' ) );
	}

	$slug     = foldery_rml_unique_slug( $name, $folder->getParent(), $id );
	$absolute = foldery_rml_absolute_path_for( $folder->getParent(), $slug );
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

	foldery_rml_clear_runtime_cache();
	foldery_rml_refresh_child_absolute_paths( $id );
	foldery_rml_clear_runtime_cache();
	return true;
}

function foldery_rml_delete_folder( $id ) {
	global $wpdb;

	$id     = (int) $id;
	$folder = wp_rml_get_object_by_id( $id );
	if ( ! is_rml_folder( $folder ) || $id <= 0 ) {
		return array( __( 'Folder does not exist.', 'foldery' ) );
	}
	if ( count( $folder->getChildren() ) > 0 ) {
		return array( __( 'The folder has subfolders.', 'foldery' ) );
	}

	$table       = foldery_rml_table_name();
	$table_posts = foldery_rml_table_name( 'posts' );
	$table_meta  = foldery_rml_table_name( 'meta' );
	$wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );
	$wpdb->delete( $table_posts, array( 'fid' => $id ), array( '%d' ) );
	$wpdb->delete( $table_meta, array( 'realmedialibrary_id' => $id ), array( '%d' ) );

	foldery_rml_clear_runtime_cache();
	return true;
}

function foldery_rml_move_attachments( $to, $ids ) {
	global $wpdb;

	$to  = is_numeric( $to ) ? (int) $to : _wp_rml_root();
	$ids = array_values( array_unique( array_filter( array_map( 'intval', (array) $ids ) ) ) );
	if ( empty( $ids ) ) {
		return array( __( 'No media selected.', 'foldery' ) );
	}
	if ( _wp_rml_root() !== $to && ! is_rml_folder( wp_rml_get_object_by_id( $to ) ) ) {
		return array( __( 'Destination folder does not exist.', 'foldery' ) );
	}

	$table_posts = foldery_rml_table_name( 'posts' );
	$sources     = wp_attachment_folder( $ids );
	foreach ( $ids as $attachment_id ) {
		$wpdb->delete( $table_posts, array( 'attachment' => $attachment_id, 'isShortcut' => 0 ), array( '%d', '%d' ) );
	}

	if ( _wp_rml_root() !== $to ) {
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

	foldery_rml_update_count( array_merge( (array) $sources, array( $to ) ) );
	return true;
}

if ( ! function_exists( 'wp_rml_create' ) ) {
	function wp_rml_create( $name, $parent, $type = RML_TYPE_FOLDER, $restrictions = array(), $supress_validation = false, $return_existing_id = false ) {
		return foldery_rml_create_folder( $name, $parent );
	}
}

if ( ! function_exists( 'wp_rml_rename' ) ) {
	function wp_rml_rename( $name, $id, $supress_validation = false ) {
		return foldery_rml_rename_folder( $name, $id );
	}
}

if ( ! function_exists( 'wp_rml_delete' ) ) {
	function wp_rml_delete( $id, $supress_validation = false ) {
		return foldery_rml_delete_folder( $id );
	}
}

if ( ! function_exists( 'wp_rml_move' ) ) {
	function wp_rml_move( $to, $ids, $supress_validation = false, $isShortcut = false ) {
		return foldery_rml_move_attachments( $to, $ids );
	}
}

function foldery_rml_plain_folder( $folder ) {
	return array(
		'id'       => $folder->getId(),
		'parent'   => $folder->getParent(),
		'name'     => $folder->getName(),
		'path'     => $folder->getAbsolutePath(),
		'count'    => foldery_rml_count_attachments_in_folder( $folder->getId() ),
		'children' => array_map( 'foldery_rml_plain_folder', $folder->getChildren() ),
	);
}

function foldery_rml_admin_tree_data() {
	return array(
		'all'          => array(
			'id'    => 0,
			'name'  => __( 'All files', 'foldery' ),
			'count' => (int) wp_count_posts( 'attachment' )->inherit,
		),
		'unorganized'  => array(
			'id'    => _wp_rml_root(),
			'name'  => __( 'Unorganized', 'foldery' ),
			'count' => foldery_rml_count_attachments_in_folder( _wp_rml_root() ),
		),
		'folders'      => array_map( 'foldery_rml_plain_folder', wp_rml_root_childs() ),
		'selected'     => foldery_rml_current_request_folder_id(),
		'dropdownHtml' => wp_rml_dropdown( foldery_rml_current_request_folder_id(), array(), true ),
	);
}

function foldery_rml_current_request_folder_id() {
	$value = null;
	if ( isset( $_REQUEST['rml_folder'] ) ) {
		$value = wp_unslash( $_REQUEST['rml_folder'] );
	} elseif ( isset( $_REQUEST['query'] ) && is_array( $_REQUEST['query'] ) && isset( $_REQUEST['query']['rml_folder'] ) ) {
		$value = wp_unslash( $_REQUEST['query']['rml_folder'] );
	} elseif ( isset( $_REQUEST['rmlFolder'] ) ) {
		$value = wp_unslash( $_REQUEST['rmlFolder'] );
	}

	if ( is_array( $value ) ) {
		return 0;
	}

	if ( null === $value || '' === $value ) {
		return 0;
	}

	return (int) $value;
}

function foldery_rml_apply_attachment_query_filter( $query, $fid ) {
	$ids = foldery_rml_attachment_ids_for_folder( $fid );
	if ( null === $ids ) {
		return $query;
	}

	$query['post__in'] = empty( $ids ) ? array( 0 ) : $ids;
	$query['orderby']  = 'post__in';
	return $query;
}

function foldery_rml_ajax_query_attachments_args( $query ) {
	if ( ! current_user_can( 'upload_files' ) ) {
		return $query;
	}

	return foldery_rml_apply_attachment_query_filter( $query, foldery_rml_current_request_folder_id() );
}
add_filter( 'ajax_query_attachments_args', 'foldery_rml_ajax_query_attachments_args' );

function foldery_rml_pre_get_posts( $query ) {
	global $pagenow;

	if ( ! is_admin() || 'upload.php' !== $pagenow || ! $query->is_main_query() || 'attachment' !== $query->get( 'post_type' ) ) {
		return;
	}

	$args = foldery_rml_apply_attachment_query_filter( array(), foldery_rml_current_request_folder_id() );
	foreach ( $args as $key => $value ) {
		$query->set( $key, $value );
	}
}
add_action( 'pre_get_posts', 'foldery_rml_pre_get_posts' );

function foldery_rml_restrict_manage_posts() {
	global $typenow;

	if ( 'attachment' !== $typenow || ! current_user_can( 'upload_files' ) ) {
		return;
	}

	printf(
		'<label class="screen-reader-text" for="filter-by-rml-folder">%s</label><select name="rml_folder" id="filter-by-rml-folder" class="attachment-filters attachment-filters-rml">%s</select>',
		esc_html__( 'Filter by media folder', 'foldery' ),
		wp_rml_dropdown( foldery_rml_current_request_folder_id(), array(), true )
	);
}
add_action( 'restrict_manage_posts', 'foldery_rml_restrict_manage_posts' );

function foldery_rml_prepare_attachment_for_js( $response, $attachment, $meta ) {
	$response['rmlFolder'] = wp_attachment_folder( $attachment->ID, _wp_rml_root() );
	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'foldery_rml_prepare_attachment_for_js', 10, 3 );

function foldery_rml_attachment_fields_to_edit( $fields, $post ) {
	if ( ! current_user_can( 'upload_files' ) ) {
		return $fields;
	}

	$fields['rmlFolder'] = array(
		'label' => __( 'Media folder', 'foldery' ),
		'input' => 'html',
		'html'  => '<select name="attachments[' . (int) $post->ID . '][rml_folder]">' . wp_rml_dropdown( wp_attachment_folder( $post->ID, _wp_rml_root() ), array(), false ) . '</select>',
		'helps' => __( 'Move this file to a media folder.', 'foldery' ),
	);

	return $fields;
}
add_filter( 'attachment_fields_to_edit', 'foldery_rml_attachment_fields_to_edit', 10, 2 );

function foldery_rml_attachment_fields_to_save( $post, $attachment ) {
	if ( isset( $attachment['rml_folder'] ) && current_user_can( 'upload_files' ) ) {
		foldery_rml_move_attachments( (int) $attachment['rml_folder'], array( (int) $post['ID'] ) );
	}

	return $post;
}
add_filter( 'attachment_fields_to_save', 'foldery_rml_attachment_fields_to_save', 10, 2 );

function foldery_rml_handle_uploaded_attachment( $attachment_id ) {
	$fid = foldery_rml_current_request_folder_id();
	if ( 0 !== $fid ) {
		foldery_rml_move_attachments( $fid, array( (int) $attachment_id ) );
	}
}
add_action( 'add_attachment', 'foldery_rml_handle_uploaded_attachment' );

function foldery_rml_admin_enqueue( $hook ) {
	if ( ! current_user_can( 'upload_files' ) || ! in_array( $hook, array( 'upload.php', 'media-new.php' ), true ) ) {
		return;
	}

	$script = get_template_directory() . '/assets/admin/foldery-media-library.js';
	$style  = get_template_directory() . '/assets/admin/foldery-media-library.css';
	wp_enqueue_style( 'foldery-media-library', get_template_directory_uri() . '/assets/admin/foldery-media-library.css', array(), file_exists( $style ) ? filemtime( $style ) : null );
	wp_enqueue_script( 'foldery-media-library', get_template_directory_uri() . '/assets/admin/foldery-media-library.js', array( 'jquery', 'media-views', 'wp-util' ), file_exists( $script ) ? filemtime( $script ) : null, true );
	wp_localize_script(
		'foldery-media-library',
		'folderyMediaLibrary',
		array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'foldery-rml-admin' ),
			'rootId'   => _wp_rml_root(),
			'tree'     => foldery_rml_admin_tree_data(),
			'labels'   => array(
				'title'          => __( 'Folders', 'foldery' ),
				'newFolder'      => __( 'New folder', 'foldery' ),
				'rename'         => __( 'Rename', 'foldery' ),
				'delete'         => __( 'Delete', 'foldery' ),
				'moveSelected'   => __( 'Move selection here', 'foldery' ),
				'folderName'     => __( 'Folder name', 'foldery' ),
				'confirmDelete'  => __( 'Delete this folder? Files will become unorganized.', 'foldery' ),
				'selectFiles'    => __( 'Select media first.', 'foldery' ),
				'allFiles'       => __( 'All files', 'foldery' ),
				'unorganized'    => __( 'Unorganized', 'foldery' ),
				'dropToMove'     => __( 'Drop to move', 'foldery' ),
				'moved'          => __( 'Media moved.', 'foldery' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'foldery_rml_admin_enqueue' );

function foldery_rml_admin_ajax() {
	check_ajax_referer( 'foldery-rml-admin', 'nonce' );
	if ( ! current_user_can( 'upload_files' ) ) {
		wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'foldery' ) ), 403 );
	}

	$folder_action = isset( $_POST['folder_action'] ) ? sanitize_key( wp_unslash( $_POST['folder_action'] ) ) : '';
	$result        = true;
	$selected      = foldery_rml_current_request_folder_id();

	switch ( $folder_action ) {
		case 'create':
			$parent = isset( $_POST['parent'] ) ? (int) $_POST['parent'] : _wp_rml_root();
			$name   = isset( $_POST['name'] ) ? wp_unslash( $_POST['name'] ) : '';
			$result = foldery_rml_create_folder( $name, $parent );
			if ( is_int( $result ) ) {
				$selected = $result;
			}
			break;
		case 'rename':
			$id       = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
			$name     = isset( $_POST['name'] ) ? wp_unslash( $_POST['name'] ) : '';
			$result   = foldery_rml_rename_folder( $name, $id );
			$selected = $id;
			break;
		case 'delete':
			$id       = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
			$result   = foldery_rml_delete_folder( $id );
			$selected = 0;
			break;
		case 'move':
			$to       = isset( $_POST['to'] ) ? (int) $_POST['to'] : _wp_rml_root();
			$ids      = isset( $_POST['ids'] ) ? (array) $_POST['ids'] : array();
			$result   = foldery_rml_move_attachments( $to, $ids );
			$selected = $to;
			break;
		default:
			wp_send_json_error( array( 'message' => __( 'Unknown action.', 'foldery' ) ), 400 );
	}

	if ( true !== $result && ! is_int( $result ) ) {
		wp_send_json_error( array( 'message' => implode( ' ', (array) $result ) ), 400 );
	}

	wp_send_json_success(
		array(
			'tree'     => foldery_rml_admin_tree_data(),
			'selected' => $selected,
			'message'  => __( 'Media folders updated.', 'foldery' ),
		)
	);
}
add_action( 'wp_ajax_foldery_rml_admin', 'foldery_rml_admin_ajax' );
