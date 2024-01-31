<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Product_Category extends MY_RetailErp_Model {
	protected $table = 'categories';
	
	/**
	 * @var string
	 */
	protected $code;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $image;
	/**
	 * @var int
	 */
	protected $parent_id;
	/**
	 * @var Erp_Product_Category
	 */
	protected $parent;
	/**
	 * @var string
	 */
	protected $slug;
	/**
	 * @var string
	 */
	protected $description;
	/**
	 * @var Erp_Product_Category[]
	 */
	protected $children = [];
	
	/**
	 * @return Erp_Product_Category[]
	 */
	public function getChildren() {
		if ( $this->getId() && empty( $this->children ) ) {
			$this->db->select( 'id' );
			$this->db->where( [ 'parent_id' => $this->getId() ] );
			$this->children = $this->idsToObject( $this->db->get( $this->table )->result_array(), 'Erp_Product_Category' );
		}
		
		return $this->children;
	}
	
	/**
	 * @return bool
	 */
	public function hasChildren() {
		$this->getChildren();
		return ! empty( $this->children );
	}
	
	/**
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}
	
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @param bool $url
	 * @param bool $thumb
	 * @param bool $check
	 *
	 * @return bool|string
	 */
	public function getImage( $url = true, $thumb = true, $check = true ) {
		if ( $url ) {
			return get_image_url( $this->image, $thumb, $check );
		}
		return $this->image;
	}
	
	/**
	 * @return int
	 */
	public function getParentId() {
		return $this->parent_id;
	}
	
	public function getParent() {
		if ( $this->parent ) {
			return $this->parent;
		}
		if ( $this->getParentId() ) {
			$this->parent = new Erp_Product_Category( $this->getParentId() );
		}
		
		return $this->parent;
	}
	
	/**
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}
	
	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * @param string $code
	 */
	public function setCode( $code ) {
		$this->code = $code;
	}
	
	/**
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}
	
	/**
	 * @param string $image
	 */
	public function setImage( $image ) {
		$this->image = $image;
	}
	
	/**
	 * @param int $parent_id
	 */
	public function setParentId( $parent_id ) {
		$this->parent_id = $parent_id;
	}
	
	/**
	 * @param string $slug
	 */
	public function setSlug( $slug ) {
		$this->slug = $slug;
	}
	
	/**
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = $description;
	}
}
// End of file Erp_Product_Category.php.
