<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Erp_Shipment extends MY_RetailErp_Model {
	protected $table = 'shipment';

	/**
	 * @var string
	 */
	public $shipment_date;

	/**
	 * @var string
	 */
	public $shipment_no;

	/**
	 * @var int
	 */
	public $sale_id;

	/**
	 * @var int
	 */
	public $delivery_id;

	/**
	 * @var float
	 */
	public $cost_adjustment = 0;

	/**
	 * @var string
	 */
	public $status = 'pending';

	/**
	 * @var int
	 */
	public $created_by;

	/**
	 * @var string
	 */
	public $created_at;

	/**
	 * @var int
	 */
	public $updated_by;

	/**
	 * @var string
	 */
	public $updated_at;

	/**
	 * @var int
	 */
	public $delete_flag = 0;

	/**
	 * @return string
	 */
	public function getShipmentDate() {
		return $this->shipment_date;
	}

	/**
	 * @param string $shipment_date
	 */
	public function setShipmentDate( string $shipment_date ) {
		$this->shipment_date = $shipment_date;
	}

	/**
	 * @return string
	 */
	public function getShipmentNo() {
		return $this->shipment_no;
	}

	/**
	 * @param string $shipment_no
	 */
	public function setShipmentNo( string $shipment_no ) {
		$this->shipment_no = $shipment_no;
	}

	/**
	 * @return int
	 */
	public function getSaleId() {
		return $this->sale_id;
	}

	/**
	 * @param int $sale_id
	 */
	public function setSaleId( int $sale_id ) {
		$this->sale_id = $sale_id;
	}

	/**
	 * @return int
	 */
	public function getDeliveryId() {
		return $this->delivery_id;
	}

	/**
	 * @param int $delivery_id
	 */
	public function setDeliveryId( int $delivery_id ) {
		$this->delivery_id = $delivery_id;
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

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string $status
	 */
	public function setStatus( string $status ) {
		$this->status = $status;
	}

	/**
	 * @return int
	 */
	public function getCreatedBy() {
		return $this->created_by;
	}

	/**
	 * @param int $created_by
	 */
	public function setCreatedBy( int $created_by ) {
		$this->created_by = $created_by;
	}

	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->created_at;
	}

	/**
	 * @param string $created_at
	 */
	public function setCreatedAt( string $created_at ) {
		$this->created_at = $created_at;
	}

	/**
	 * @return int
	 */
	public function getUpdatedBy() {
		return $this->updated_by;
	}

	/**
	 * @param int $updated_by
	 */
	public function setUpdatedBy( int $updated_by ) {
		$this->updated_by = $updated_by;
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->updated_at;
	}

	/**
	 * @param string $updated_at
	 */
	public function setUpdatedAt( string $updated_at ) {
		$this->updated_at = $updated_at;
	}

	/**
	 * @return int
	 */
	public function getDeleteFlag() {
		return $this->delete_flag;
	}

	/**
	 * @param int $delete_flag
	 */
	public function setDeleteFlag( int $delete_flag ) {
		$this->delete_flag = $delete_flag;
	}


	public function save() {
		$update_sequence = false;
		if ( ! $this->shipment_no ) {
			$update_sequence = true;
			$this->shipment_no = $this->get_sequential_reference( 'SH' );
		}


		if ( NULL === $this->created_by ) {
			$this->created_by = $this->session->userdata( 'user_id' );
		}
		if ( ! $this->created_at ) {
			$this->created_at = $this->format_date();
		}
		if ( NULL === $this->updated_by ) {
			$this->updated_by = $this->session->userdata( 'user_id' );
		}

		$this->updated_at = $this->format_date();

		if ( parent::save() ) {
			if ( $update_sequence ) {
				$this->update_sequential_reference( 'SH' );
			}
			return true;
		}

		return false;
	}

}
// End of file Erp_Shipment.php.
