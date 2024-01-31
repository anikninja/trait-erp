<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Shipping_Zone_Area extends MY_RetailErp_Model {
	protected $table = 'shipping_zone_areas';

	/**
	 * @var int
	 */
	public $zone_id;

	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var float
	 */
	public $cost_adjustment = 0;
	
	/**
	 * @var bool|int
	 */
	public $is_enabled = 1;

	/**
	 * @var bool|int
	 */
	public $delivery_enabled = 1;

	/**
	 * @var bool|int
	 */
	public $pickup_enabled = 0;
	
	/**
	 * @var Erp_Shipping_Zone_Area_Slot[]
	 */
	private $slots;
	
	private $countSlotsActive;
	private $countSlots;

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

	/**
	 * @return bool
	 */
	public function getDeliveryEnabled() {
		return (bool) $this->delivery_enabled;
	}

	/**
	 * @param int $delivery_enabled
	 */
	public function setDeliveryEnabled( $delivery_enabled ) {
		$this->delivery_enabled = (int) $delivery_enabled == 1 ? 1 : 0;
	}

	/**
	 * @return bool
	 */
	public function getPickupEnabled() {
		return (bool) $this->pickup_enabled;
	}

	/**
	 * @param int $pickup_enabled
	 */
	public function setPickupEnabled( $pickup_enabled ) {
		$this->pickup_enabled = (int) $pickup_enabled == 1 ? 1 : 0;
	}

	/**
	 * @param bool $format
	 * @return float|string
	 */
	public function getCostAdjustment( $format = false ) {
		if ( $format ) {
			return $this->money_format( $this->cost_adjustment );
		}
		return $this->cost_adjustment;
	}
	
	/**
	 * @param float $cost_adjustment
	 */
	public function setCostAdjustment( $cost_adjustment ) {
		$this->cost_adjustment = (float) $cost_adjustment;
	}
	
	public function getCountSlots() {
		if ( ! $this->countSlots ) {
			$c = $this->db->select( 'COUNT(*) as c', false )
				->where( 'area_id', $this->getId() )
				->get( 'shipping_area_slots' )->row();
			if( $c ) {
				$this->countSlots = $c->c;
			}
		}
		return $this->countSlots;
	}
	
	public function getCountSlotsActive() {
		if ( ! $this->countSlotsActive ) {
			$c = $this->db->select( 'COUNT(*) as c', false )
				->where( [
					'area_id' => $this->getId(),
					'is_enabled' => 1,
				] )
				->get( 'shipping_area_slots' )->row();
			if( $c ) {
				$this->countSlotsActive = $c->c;
			}
		}
		return $this->countSlotsActive;
	}
	
	public function getSlots( $is_enabled = true ) {
		if ( ! $this->slots ) {
			$this->slots = $this->db->select()
				->where( 'area_id', $this->getId() )
				->where( 'is_enabled', $is_enabled ? 1 : 0 )
				->get( 'shipping_area_slots' )->result( 'Erp_Shipping_Zone_Area_Slot' );
		}
		return $this->slots;
	}
	
	public function has_slots( $is_enabled = true ) {
		return ! ! $this->getSlots( $is_enabled );
	}
	
	public function has_slot( $slot, $is_enabled = true ) {
		$slot = $this->absint( $slot );
		if ( ! $slot ) {
			return false;
		}
		$slot = $this->db->select()
	         ->where( 'area_id', $this->getId() )
	         ->where( 'is_enabled', $is_enabled ? 1 : 0 )
	         ->get( 'shipping_area_slots' )->row( 0, 'Erp_Shipping_Zone_Area_Slot' );
		
		return $slot ? $slot : false;
	}
	
	public function delete() {
		$this->db->delete( 'shipping_area_slots', [ 'area_id' => $this->getId() ] );
		return parent::delete();
	}
}
// End of file Erp_Shipping_Zone_Area.php.
