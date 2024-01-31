<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Erp_package extends MY_RetailErp_Model {
	protected $table = 'package';

	/**
	 * @var string
	 */
	public $package_no;

	/**
	 * @var string
	 */
	public $sale_reference_no;

	/**
	 * @var int
	 */
	public $sale_id;

	/**
	 * @var int
	 */
	public $package_items_count;

	/**
	 * @var int
	 */
	public $delivery_id;

	/**
	 * @var int
	 */
	public $shipment_id;

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
	public function getPackageNo() {
		return $this->package_no;
	}

	/**
	 * @param string $package_no
	 */
	public function setPackageNo( $package_no ) {
		$this->package_no = $package_no;
	}

	/**
	 * @return string
	 */
	public function getSaleReferenceNo() {
		return $this->sale_reference_no;
	}

	/**
	 * @param string $sale_reference_no
	 */
	public function setSaleReferenceNo( $sale_reference_no ) {
		$this->sale_reference_no = $sale_reference_no;
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
	public function setSaleId( $sale_id ) {
		$this->sale_id = $sale_id;
	}

	/**
	 * @return int
	 */
	public function getPackageItemsCount() {
		return $this->package_items_count;
	}

	/**
	 * @param int $package_items_count
	 */
	public function setPackageItemsCount( $package_items_count ) {
		$this->package_items_count = $package_items_count;
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
	public function setDeliveryId( $delivery_id ) {
		$this->delivery_id = $delivery_id;
	}

	/**
	 * @return int
	 */
	public function getShipmentId() {
		return $this->shipment_id;
	}

	/**
	 * @param int $shipment_id
	 */
	public function setShipmentId( $shipment_id ) {
		$this->shipment_id = $shipment_id;
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
	public function setStatus( $status ) {
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
	public function setCreatedBy( $created_by ) {
		if ( $created_by instanceof Erp_User ) {
			$created_by = $created_by->getId();
		}
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
	public function setCreatedAt( $created_at ) {
		$this->created_at = $this->format_date( $created_at );
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
	public function setUpdatedBy( $updated_by ) {
		if ( $updated_by instanceof Erp_User ) {
			$updated_by = $updated_by->getId();
		}
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
	public function setUpdatedAt( $updated_at ) {
		$this->updated_at = $this->format_date( $updated_at );
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

	/**
	 * @param int $sale_id
	 * @param int[] $item_id
	 *
	 * @return bool
	 */
	public function add_package( $sale_id, $item_id ) {
		$sales_model = new Sales_model();

		$inv      = $sales_model->getInvoiceByID( $sale_id );
		$delivery = $sales_model->getDeliveryBySaleID( $sale_id );

		$this->setSaleReferenceNo( $inv->reference_no );
		$this->setSaleId( $inv->id );
		$this->setPackageItemsCount( count( $item_id ) );
		$this->setDeliveryId( $delivery ? $delivery->id : NULL );
		$this->setShipmentId( NULL );

		if ( $this->save() ) {
			$data['package_id'] = $this->getId();
			$data['sale_id']    = $inv->id;
			foreach ( $item_id as $val ) {
				$data['sales_item_id'] = $val;
				$this->db->insert( 'package_items', $data );
			}
			return $this->getId();
		}

		return false;
	}

	public function add_package_item( $pack_id, $sale_id, $item_id ) {
		if ( !empty($pack_id) || !empty($sale_id) ) {
			$data['package_id'] = $pack_id;
			$data['sale_id']    = $sale_id;
			foreach ( $item_id as $val ) {
				$data['sales_item_id'] = $val;
				$this->db->insert( 'package_items', $data );
			}
			return $this->getId();
		}

		return false;
	}

	public function save() {
		$update_sequence = false;
		if ( ! $this->package_no ) {
			$update_sequence = true;
			$this->package_no = $this->get_sequential_reference( 'PACK' );
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
				$this->update_sequential_reference( 'PACK' );
			}
			return true;
		}

		return false;
	}
}
// End of file Erp_package.php.
