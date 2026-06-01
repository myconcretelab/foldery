<?php
/**
 * Foldery media folder model.
 */

if ( ! defined( 'FOLDERY_MEDIA_FOLDER_TYPE_FOLDER' ) ) {
	define( 'FOLDERY_MEDIA_FOLDER_TYPE_FOLDER', 0 );
}
if ( ! defined( 'FOLDERY_MEDIA_FOLDER_TYPE_COLLECTION' ) ) {
	define( 'FOLDERY_MEDIA_FOLDER_TYPE_COLLECTION', 1 );
}
if ( ! defined( 'FOLDERY_MEDIA_FOLDER_TYPE_GALLERY' ) ) {
	define( 'FOLDERY_MEDIA_FOLDER_TYPE_GALLERY', 2 );
}
if ( ! defined( 'FOLDERY_MEDIA_FOLDER_TYPE_ALL' ) ) {
	define( 'FOLDERY_MEDIA_FOLDER_TYPE_ALL', 3 );
}
if ( ! defined( 'FOLDERY_MEDIA_FOLDER_TYPE_ROOT' ) ) {
	define( 'FOLDERY_MEDIA_FOLDER_TYPE_ROOT', 4 );
}

if ( ! class_exists( 'Foldery_Media_Folder' ) ) {
	class Foldery_Media_Folder {
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
					'type'               => FOLDERY_MEDIA_FOLDER_TYPE_ROOT,
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
			if ( FOLDERY_MEDIA_FOLDER_TYPE_ROOT === $this->getType() ) {
				return $this->getName( true );
			}

			$path   = array();
			$folder = $this;
			while ( foldery_is_media_folder( $folder ) && FOLDERY_MEDIA_FOLDER_TYPE_ROOT !== $folder->getType() ) {
				if ( ! isset( $filter ) || call_user_func( $filter, $folder ) ) {
					$name = $folder->getName();
					if ( 'htmlentities' === $map ) {
						$name = htmlentities( $name );
					} elseif ( null !== $map ) {
						$name = call_user_func( $map, $name, $folder );
					}
					array_unshift( $path, $name );
				}
				$folder = foldery_media_get_folder( $folder->getParent() );
			}

			return implode( $implode, $path );
		}

		public function getCnt( $forceReload = false ) {
			if ( null !== $this->row->cnt && ! $forceReload ) {
				return max( 0, (int) $this->row->cnt );
			}

			global $wpdb;
			$table_posts = foldery_media_table_name( 'posts' );
			$count       = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(DISTINCT p.ID)
					FROM {$table_posts} media_rel
					INNER JOIN {$wpdb->posts} p ON p.ID = media_rel.attachment
					WHERE media_rel.fid = %d
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

			$this->children = foldery_media_get_children( $this->getId() );
			return $this->children;
		}

		public function read( $order = null, $orderby = null ) {
			return foldery_media_read_attachments( $this->getId(), $order, $orderby, $this );
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

