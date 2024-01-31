<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Delivery_Schedule extends MY_RetailErp_Model {
	protected $table = 'delivery_schedules';
    /**
     * @var string
     */
    public $scheduled_on;
    /**
     * @var string
     */
    public $start;
	/**
	 * @var string
	 */
	public $end;
    /**
     * @var int
     */
    public $slot_id;
    /**
     * @var int
     */
    public $zone_id;
    /**
     * @var int
     */
    public $area_id;
	/**
	 * @var int
	 */
	public $sales_id;
    /**
     * @var int
     */
    public $delivery_id;

    /**
     * @return string
     */
    public function getScheduledOn(){
        return $this->scheduled_on;
    }
    /**
     * @return string
     */
    public function getStart(){
        return $this->start;
    }
	/**
	 * @return string
	 */
	public function getEnd(){
		return $this->end;
	}
    /**
     * @return int
     */
    public function getSlotId(){
        return $this->slot_id;
    }
    /**
     * @return int
     */
    public function getZoneId(){
        return $this->zone_id;
    }
    /**
     * @return int
     */
    public function getAreaId(){
        return $this->area_id;
    }
    /**
     * @return int
     */
    public function getDeliveryId(){
        return $this->delivery_id;
    }
    /**
     * @return int
     */
    public function getSalesId(){
        return $this->sales_id;
    }

    /**
     * @param string $start
     */
    public function setStart( $start ){
        $this->start = $start;
    }
	/**
	 * @param string $end
	 */
	public function setEnd( $end ){
		$this->end = $end;
	}
    /**
     * @param int $slot_id
     */
    public function setSlotId( $slot_id ){
        $this->slot_id = $slot_id;
    }
    /**
     * @param int $zone_id
     */
    public function setZoneId( $zone_id ){
        $this->zone_id = $zone_id;
    }
    /**
     * @param int $area_id
     */
    public function setAreaId( $area_id ){
        $this->area_id = $area_id;
    }
    /**
     * @param int $delivery_id
     */
    public function setDeliveryId( $delivery_id ){
        $this->delivery_id = $delivery_id;
    }
    /**
     * @param int $sales_id
     */
    public function setSalesId( $sales_id ){
        $this->sales_id = $sales_id;
    }

    public static function get_delivery_schedule_by_sales_id ( $id = null ){
    	if ( $id == null ){
    		return FALSE;
	    }
    	else{
		    $schedule = self::get_ci_instance()->db->select( '*' )->where( 'sales_id', $id )->get( 'delivery_schedules' )->row();
		    if ($schedule){
			    return  $schedule;
		    }
		    else{
		    	return FALSE;
		    }
	    }
    }

	public function save() {
		return parent::save();
	}
}
// End of file Erp_Delivery_Schedule.php.
