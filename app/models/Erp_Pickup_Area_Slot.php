<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Pickup_Area_Slot extends MY_RetailErp_Model {
	protected $table = 'pickup_area_slots';
    /**
     * @var int
     */
	public $area_id;
    /**
     * @var string
     */
	public $name;
    /**
     * @var string
     */
	public $start_at;
    /**
     * @var string
     */
	public $end_at;
    /**
     * @var int
     */
	public $max_order = 0;
    /**
     * @var float
     */
    public $cost_adjustment = 0;
    /**
     * @var string
     */
    public $close_before;
	/**
	 * @var bool|int
	 */
	public $is_enabled = 1;
	
	public function __construct( $id = null ) {
		parent::__construct( $id );
		if ( is_null( $id ) ) {
			if ( ! $this->getStartAt() ) {
				$this->setStartAt( date( 'H:i' ) . ':00' );
			}
			
			if ( ! $this->getEndAt() ) {
				$this->setEndAt( date( 'H:i', strtotime( '+2 hour', strtotime( $this->getStartAt() ) ) ) );
			}
			
			if ( ! $this->getCloseBefore() ) {
				$this->setCloseBefore( date( 'H:i', strtotime( '-30 minutes', strtotime( $this->getEndAt() ) ) ) );
			}
		}
	}
	
	/**
     * @return int
     */
    public function getAreaId(){
        return $this->area_id;
    }
    /**
     * @return string
     */
    public function getName(){
        return $this->name;
    }
    /**
     * @return string
     */
    public function getStartAt(){
        return $this->start_at;
    }
    /**
     * @return string
     */
    public function getEndAt(){
        return $this->end_at;
    }
    /**
     * @return int
     */
    public function getMaxOrder(){
        return $this->max_order;
    }

    /**
     * @return mixed
     */
    public function getCloseBefore(){
        return $this->close_before;
    }

    /**
     * @param int $area_id
     */
    public function setAreaId( $area_id ){
        $this->area_id = $area_id;
    }
    /**
     * @param string $name
     */
    public function setName( $name ){
        $this->name = $name;
    }
    /**
     * @param string $start_at
     */
    public function setStartAt( $start_at ){
        $this->start_at = $start_at;
    }
    /**
     * @param string $end_at
     */
    public function setEndAt( $end_at ){
        $this->end_at = $end_at;
    }
    /**
     * @param int $max_order
     */
    public function setMaxOrder( $max_order ){
        $this->max_order = $max_order;
    }
    /**
     * @param mixed $close_before
     */
    public function setCloseBefore( $close_before ){
        $this->close_before = $close_before;
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
}
// End of file Erp_Shipping_Zone_Area_Slot.php.
