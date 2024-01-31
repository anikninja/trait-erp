<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Delivery extends MY_RetailErp_Model {
	
	protected $table = 'deliveries';
	
	/**
	 * @var string
	 */
	protected $date;
	
	/**
	 * @var int
	 */
	protected $sale_id;
	
	/**
	 * @var string
	 */
	protected $do_reference_no;
	
	/**
	 * @var string
	 */
	protected $sale_reference_no;
	
	/**
	 * @var string
	 */
	protected $customer;
	
	/**
	 * @var string
	 */
	protected $address;
	
	/**
	 * @var string
	 */
	protected $note;
	
	/**
	 * @var string
	 */
	protected $status;
	
	/**
	 * @var string
	 */
	protected $attachment;
	
	/**
	 * @var string
	 */
	protected $delivered_by;
	
	/**
	 * @var string
	 */
	protected $received_by;
	
	/**
	 * @var int
	 */
	protected $created_by;
	
	/**
	 * @var int
	 */
	protected $updated_by;
	
	/**
	 * @var string
	 */
	protected $updated_at;

	/**
	 * @return string
	 */
	public function getDate() {
		return $this->date;
	}
	
	/**
	 * @param string $date
	 */
	public function setDate( $date ) {
		$this->date = $this->format_date( $date );
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
	 * @return string
	 */
	public function getDoReferenceNo() {
		return $this->do_reference_no;
	}
	
	/**
	 * @param string $do_reference_no
	 */
	public function setDoReferenceNo( $do_reference_no ) {
		$this->do_reference_no = $do_reference_no;
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
	 * @return string
	 */
	public function getCustomer() {
		return $this->customer;
	}
	
	/**
	 * @param string $customer
	 */
	public function setCustomer( $customer ) {
		$this->customer = $customer;
	}
	
	/**
	 * @return string
	 */
	public function getAddress() {
		return $this->address;
	}
	
	/**
	 * @param string $address
	 */
	public function setAddress( $address ) {
		$this->address = $address;
	}
	
	/**
	 * @return string
	 */
	public function getNote() {
		return $this->note;
	}
	
	/**
	 * @param string $note
	 */
	public function setNote( $note ) {
		$this->note = $note;
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
	 * @return string
	 */
	public function getAttachment() {
		return $this->attachment;
	}
	
	/**
	 * @param string $attachment
	 */
	public function setAttachment( $attachment ) {
		$this->attachment = $attachment;
	}
	
	/**
	 * @return string
	 */
	public function getDeliveredBy() {
		return $this->delivered_by;
	}
	
	/**
	 * @param string $delivered_by
	 */
	public function setDeliveredBy( $delivered_by ) {
		$this->delivered_by = $delivered_by;
	}
	
	/**
	 * @return string
	 */
	public function getReceivedBy() {
		return $this->received_by;
	}
	
	/**
	 * @param string $received_by
	 */
	public function setReceivedBy( $received_by ) {
		$this->received_by = $received_by;
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
}
// End of file Erp_Address.php.
