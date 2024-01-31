<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Shipping_Zone extends MY_RetailErp_Model {
	protected $table = 'shipping_zones';
	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var string
	 */
	public $continent;
	/**
	 * @var string
	 */
	public $country;
	/**
	 * @var string
	 */
	public $state;
	/**
	 * @var string
	 */
	public $city;
	/**
	 * @var string
	 */
	public $zip;
	/**
	 * @var bool|int
	 */
	public $is_enabled = 1;
	/**
	 * @var int
	 */
	public $order = 0;
	
	/**
	 * @var Erp_Shipping_Zone_Area[]
	 */
	private $areas;
	
	/**
	 * @var Erp_Shipping_Method[]
	 */
	private $methods;

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @return string
	 */
	public function getContinent() {
		return $this->continent;
	}
	
	/**
	 * @return string
	 */
	public function getCountry() {
		return $this->country;
	}
	
	/**
	 * @return string
	 */
	public function getState() {
		return $this->state;
	}
	
	/**
	 * @return string
	 */
	public function getCity() {
		return $this->city;
	}
	
	/**
	 * @return string
	 */
	public function getZip() {
		return $this->zip;
	}
	
	/**
	 * @return int
	 */
	public function getOrder() {
		return $this->order;
	}
	
	/**
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}
	
	/**
	 * @param string $continent
	 */
	public function setContinent( $continent ) {
		$this->continent = $continent;
	}
	
	/**
	 * @param string $country
	 */
	public function setCountry( $country ) {
		$this->country = $country;
	}
	
	/**
	 * @param string $state
	 */
	public function setState( $state ) {
		$this->state = $state;
	}
	
	/**
	 * @param string $city
	 */
	public function setCity( $city ) {
		$this->city = $city;
	}
	
	/**
	 * @param string $zip
	 */
	public function setZip( $zip ) {
		$this->zip = $zip;
	}
	
	/**
	 * @param int $order
	 */
	public function setOrder( $order ) {
		$this->order = $order;
	}
	
	public function getIsEnabled() {
		return $this->is_enabled;
	}
	
	public function setIsEnabled( $enabled ) {
		$this->is_enabled = (bool) $enabled;
	}
	
	public function getAreas( $is_enabled = true ) {
		if ( ! $this->areas ) {
			$this->areas = $this->db->select( '*' )
				->where( 'zone_id', (string) $this->getId() )
				->where( 'is_enabled', $is_enabled ? 1 : 0 )
				->get( 'shipping_zone_areas' )->result( 'Erp_Shipping_Zone_Area' );
		}
		
		return $this->areas;
	}
	
	public function has_areas( $is_enabled = true ) {
		return ! ! $this->getAreas( $is_enabled );
	}
	
	public function has_area( $area, $is_enabled = true ) {
		$area = $this->absint( $area );
		if ( ! $area ) {
			return false;
		}
		
		$area = $this->db->select( '*' )
            ->where( 'id', $area )
            ->where( 'zone_id', $this->getId() )
            ->where( 'is_enabled', $is_enabled ? 1 : 0 )
            ->get( 'shipping_zone_areas' )->row( 0, 'Erp_Shipping_Zone_Area' );
		
		return $area ? $area : false;
	}
	
	public function getShippingMethods( $is_enabled = true ) {
		if ( ! $this->methods ) {
			$this->methods = $this->db->select( '*' )
                ->where( 'zone_id', (string) $this->getId() )
				->where( 'is_enabled', $is_enabled ? 1 : 0 )
                ->get( 'shipping_zone_methods' )->result( 'Erp_Shipping_Method' );
		}
		
		return $this->methods;
	}
	
	public function has_shipping_methods( $is_enabled = true ) {
		return ! ! $this->getShippingMethods( $is_enabled );
	}
	
	public function has_shipping_method( $method, $is_enabled = true ) {
		$default = absfloat( $this->shop_settings->shipping );
		if ( 'default' === $method && $default > 0 ) {
			return true;
		}
		$method = $this->absint( $method );
		if ( ! $method ) {
			return false;
		}
		
		$method = $this->db->select( '*' )
		                 ->where( 'id', $method )
		                 ->where( 'zone_id', $this->getId() )
		                 ->where( 'is_enabled', $is_enabled ? 1 : 0 )
		                 ->get( 'shipping_zone_methods' )->row( 0, 'Erp_Shipping_Method' );
		
		return $method ? $method : false;
	}
	
	public function delete() {
		$areas = $this->db->select( 'id' )
		         ->where( 'zone_id', (string) $this->getId() )
		         ->get( 'shipping_zone_areas' )->result();
		if ( ! empty( $areas ) ) {
			$areas = array_map( function( $area ) { return $area->id; }, $areas );
			$this->db->where_in('area_id', $areas );
			$this->db->delete('shipping_area_slots');
		}
		
		$this->db->delete( 'shipping_zone_areas', [ 'zone_id' => $this->getId() ] );
		// remove methods.
		$this->db->delete( 'shipping_zone_methods', [ 'zone_id' => $this->getId() ] );
		return parent::delete();
	}
}
// End of file Erp_Shipping_Zone.php.
