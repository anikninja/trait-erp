<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Erp_Pickup extends MY_RetailErp_Model {
	protected $table = 'pickup';

	/**
	 * @var string
	 */
	public $pickup_date;

	/**
	 * @var string
	 */
	public $pickup_no;

	/**
	 * @var int
	 */
	public $sale_id;

	/**
	 * @var int
	 */
	public $delivery_id;

	/**
	 * @var int
	 */
	public $warehouse_id;

	/**
	 * @var string
	 */
	public $status;

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
	public $delete_flag;

	/**
	 * @return string
	 */
	public function getPickupDate() {
		return $this->pickup_date;
	}

	/**
	 * @param string $pickup_date
	 */
	public function setPickupDate( string $pickup_date ) {
		$this->pickup_date = $pickup_date;
	}

	/**
	 * @return string
	 */
	public function getPickupNo() {
		return $this->pickup_no;
	}

	/**
	 * @param string $pickup_no
	 */
	public function setPickupNo( string $pickup_no ) {
		$this->pickup_no = $pickup_no;
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
	 * @return int
	 */
	public function getWarehouseId() {
		return $this->warehouse_id;
	}

	/**
	 * @param int $warehouse_id
	 */
	public function setWarehouseId( int $warehouse_id ) {
		$this->warehouse_id = $warehouse_id;
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
		if ( ! $this->pickup_no ) {
			$this->pickup_no = $this->get_sequential_reference( 'PICK' );
		}
		if ( ! $this->status ) {
			$this->status = 'pending';
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

		//delete flag
		if ( $this->getDeleteFlag() == NULL ) {
			$this->setDeleteFlag( 0 );
		}

		$save = parent::save();
		if ( $save ) {
			$this->update_sequential_reference( 'PICK' );
		}

		return $save;
	}

}
// End of file Erp_Pickup.php.
