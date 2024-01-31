<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Shipping_Method extends MY_RetailErp_Model {
	protected $table = 'shipping_zone_methods';

	/**
	 * @var int
	 */
	public $zone_id;

	/**
	 * @var string
	 */
	public $method_id;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var float
	 */
	public $cost = 0;

	/**
	 * @var int
	 */
	public $method_order = 0;
	
	/**
	 * @var bool|int
	 */
	public $is_enabled = 1;

	/**
	 * @return int
	 */
	public function getZoneId() {
		return $this->zone_id;
	}

	/**
	 * @param int $zone_id
	 */
	public function setZoneId( $zone_id ) {
		$this->zone_id = $zone_id;
	}

	/**
	 * @return string
	 */
	public function getMethodId() {
		return $this->method_id;
	}

	/**
	 * @param string $method_id
	 */
	public function setMethodId( $method_id ) {
		$this->method_id = $method_id;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = $description;
	}

	/**
	 * @param bool $format
	 * @return float|string
	 */
	public function getCost( $format = false ) {
		if ( $format ) {
			return $this->money_format( $this->cost );
		}
		return $this->cost;
	}

	/**
	 * @param string $cost
	 */
	public function setCost( $cost ) {
		$this->cost = $cost;
	}

	/**
	 * @return int
	 */
	public function getMethodOrder() {
		return $this->method_order;
	}

	/**
	 * @param int $method_order
	 */
	public function setMethodOrder( $method_order ) {
		$this->method_order = $method_order;
	}
	
	/**
	 * @return bool
	 */
	public function getIsEnabled() {
		return (bool) $this->is_enabled;
	}
	
	/**
	 * @param int $is_enabled
	 */
	public function setIsEnabled( $is_enabled ) {
		$this->is_enabled = (int) $is_enabled == 1 ? 1 : 0;
	}
	
}
// End of file Erp_Shipping_Method.php.
