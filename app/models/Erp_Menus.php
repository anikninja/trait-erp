<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Erp_Menu_Item
 * Utility class.
 */
class Erp_Menu_Item {
	public $id;
	public $label;
	public $slug;
	public $target;
	public $class;
	public $order;
	public $tip;
	public $parent;
	
	public function __construct( array $menu = [] ) {
		if ( ! empty( $menu ) ) {
			if ( isset( $menu['id'] ) ) {
				$this->id = $menu['id'];
			}
			if ( isset( $menu['label'] ) ) {
				$this->label = $menu['label'];
			}
			if ( isset( $menu['controller'] ) ) {
				$this->controller = $menu['controller'];
			}
			if ( isset( $menu['method'] ) ) {
				$this->method = $menu['method'];
			}
			if ( isset( $menu['slug'] ) ) {
				$this->slug = $menu['slug'];
			}
			if ( isset( $menu['target'] ) ) {
				$this->target = $menu['target'];
			}
			if ( isset( $menu['class'] ) ) {
				$this->class = $menu['class'];
			}
			if ( isset( $menu['order'] ) ) {
				$this->order = $menu['order'];
			}
			if ( isset( $menu['tip'] ) ) {
				$this->tip = $menu['tip'];
			}
			if ( isset( $menu['parent'] ) ) {
				$this->parent = $menu['parent'];
			}
		}
	}
	
	public function to_array() {
		return get_object_vars( $this );
	}
}

class Erp_Menus extends MY_RetailErp_Model {
	
	protected $table = 'menus';
	
	
	/**
	 * Menu Store.
	 * @var Erp_Menu_Item[]
	 */
	protected $_menus = [];
	protected $_children = [];
	
	protected $count = 0;
	
	/**
	 * @var object[]
	 */
	protected $_settings;
	
	/**
	 * Erp_Menu_Model constructor.
	 *
	 * @param null $id
	 */
	public function __construct( $id = null ) {}
	
	/**
	 * @param bool $subNav
	 * @param bool $parentLabel
	 *
	 * @return Erp_Menu_Item[]
	 */
	public function getMenus( $subNav = false, $parentLabel = false ) {
		if ( ! $subNav ) {
			$this->db->where( 'menus.parent', 0 );
		}
		if ( $parentLabel ) {
			$this->db->select( 'menus.*, m.label as parent' );
			$this->db->join( $this->table . ' m', 'menus.parent = m.id', 'left' );
		}
		$this->db->order_by( 'order', 'ASC' );
		return $this->db->get( $this->table )->result( 'Erp_Menu_Item' );
	}
	
	/**
	 * @param $id
	 *
	 * @return bool|Erp_Menu_Item|mixed
	 */
	public function getMenu( $id ) {
		return $this->db->where( 'id', $id )->limit( 1 )->get( $this->table )->row( 0, 'Erp_Menu_Item' );
	}
	
	public function addMenu( $menu ) {
		$menu = $this->setDefaults( $menu );
		if ( empty( $menu['label'] ) || empty( $menu['slug'] ) ) {
			return false;
		}
		$this->db->insert( $this->table, $menu );
		return $this->db->insert_id();
	}
	
	public function getChildren( $id ) {
		$this->db->where( 'parent', $id );
		$this->db->order_by( 'order', 'ASC' );
		return $this->db->get( $this->table )->result( 'Erp_Menu_Item' );
	}
	
	public function getAllChildrenIds( $id ) {
		$ids = [];
		$this->db->select( 'id' )
	         ->where( 'parent', $id );
		$this->db->order_by( 'order', 'ASC' );
		$ids = $this->db->get( $this->table )->result();
		if ( ! empty( $ids ) ) {
			$ids = array_map( function( $id ) {
				return $id->id;
			}, $ids );
			foreach( $ids as $cid ) {
				$ids = array_merge( $ids, $this->getAllChildrenIds( $cid ) );
			}
		}
		$ids = array_unique( $ids );
		return $ids;
	}
	
	public function updateMenu( $menu, $id ) {
		$menu = $this->setDefaults( $menu );
		if ( empty( $menu['label'] ) || empty( $menu['slug'] ) ) {
			return false;
		}
		return $this->db->update( $this->table, $menu, [ 'id' => $id ] );
	}
	
	public function deleteMenu( $id ) {
		// delete children
		$children = $this->getAllChildrenIds( $id );
		if ( ! empty( $children ) ) {
			$this->db->where_in( 'id', $children );
			$this->db->delete( $this->table );
		}
		$this->db->delete( $this->table, [ 'id' => $id ] );
	}
	
	public function to_array() {}
	public function save() {}
	
	protected function setDefaults( $menu ) {
		return ci_parse_args( $menu, [
			'label'      => '',
			'slug'       => '',
			'target'     => '',
			'class'      => '',
			'order'       => '',
			'tip'        => '',
			'parent'     => '',
		] );
	}
}

// End of file Erp_Product.php.
