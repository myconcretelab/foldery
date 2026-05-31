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
			static $cache = array();
			if ( ! array_key_exists( $id, $cache ) ) {
				$cache[ $id ] = foldery_rml_folder_from_row( foldery_rml_get_folder_row( $id ) );
			}
			$folder = $cache[ $id ];
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
