<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Payment extends MY_RetailErp_Model {
	
	protected $table = 'payments';

	public $date;
	public $sale_id;
	public $return_id;
	public $purchase_id;
	public $reference_no;
	public $transaction_id;
	public $paid_by;
	public $cheque_no;
	public $cc_no;
	public $cc_holder;
	public $cc_month;
	public $cc_year;
	public $cc_type;
	public $amount;
	public $currency;
	public $created_by;
	public $attachment;
	public $type;
	public $note;
	public $pos_paid;
	public $pos_balance;
	public $approval_code;
	
	/**
	 * @return string
	 */
	public function getDate() {
		return $this->date;
	}
	
	/**
	 * @param string|Datetime|int $date
	 */
	public function setDate( $date ) {
		if ( $date instanceof DateTime ) {
			$date = $date->format( 'Y-m-d H:i:s' );
		} else if ( is_numeric( $date ) ) {
			$date = date( 'Y-m-d H:i:s', $date );
		}
		$this->date = $date;
	}
	
	/**
	 * @return mixed
	 */
	public function getSaleId() {
		return $this->sale_id;
	}
	
	/**
	 * @param int $sale_id
	 */
	public function setSaleId( $sale_id ) {
		$this->sale_id = (int) $sale_id;
	}
	
	/**
	 * @return int
	 */
	public function getReturnId() {
		return $this->return_id;
	}
	
	/**
	 * @param int $return_id
	 */
	public function setReturnId( $return_id ) {
		$this->return_id = ( int ) $return_id;
	}
	
	/**
	 * @return int
	 */
	public function getPurchaseId() {
		return $this->purchase_id;
	}
	
	/**
	 * @param int $purchase_id
	 */
	public function setPurchaseId( $purchase_id ) {
		$this->purchase_id = (int) $purchase_id;
	}
	
	/**
	 * @return string
	 */
	public function getReferenceNo() {
		return $this->reference_no;
	}
	
	/**
	 * @param string $reference_no
	 */
	public function setReferenceNo( $reference_no ) {
		$this->reference_no = $reference_no;
	}
	
	/**
	 * @return string
	 */
	public function getTransactionId() {
		return $this->transaction_id;
	}
	
	/**
	 * @param string $transaction_id
	 */
	public function setTransactionId( $transaction_id ) {
		$this->transaction_id = $transaction_id;
	}
	
	/**
	 * @return string
	 */
	public function getPaidBy() {
		return $this->paid_by;
	}
	
	/**
	 * @param string $paid_by
	 */
	public function setPaidBy( $paid_by ) {
		$this->paid_by = $paid_by;
	}
	
	/**
	 * @return string
	 */
	public function getChequeNo() {
		return $this->cheque_no;
	}
	
	/**
	 * @param string $cheque_no
	 */
	public function setChequeNo( $cheque_no ) {
		$this->cheque_no = $cheque_no;
	}
	
	/**
	 * @return string
	 */
	public function getCcNo() {
		return $this->cc_no;
	}
	
	/**
	 * @param string $cc_no
	 */
	public function setCcNo( $cc_no ) {
		$this->cc_no = $cc_no;
	}
	
	/**
	 * @return string
	 */
	public function getCcHolder() {
		return $this->cc_holder;
	}
	
	/**
	 * @param string $cc_holder
	 */
	public function setCcHolder( $cc_holder ) {
		$this->cc_holder = $cc_holder;
	}
	
	/**
	 * @return string
	 */
	public function getCcMonth() {
		return $this->cc_month;
	}
	
	/**
	 * @param string $cc_month
	 */
	public function setCcMonth( $cc_month ) {
		$this->cc_month = $cc_month;
	}
	
	/**
	 * @return string
	 */
	public function getCcYear() {
		return $this->cc_year;
	}
	
	/**
	 * @param string $cc_year
	 */
	public function setCcYear( $cc_year ) {
		$this->cc_year = $cc_year;
	}
	
	/**
	 * @return string
	 */
	public function getCcType() {
		return $this->cc_type;
	}
	
	/**
	 * @param string $cc_type
	 */
	public function setCcType( $cc_type ) {
		$this->cc_type = $cc_type;
	}
	
	/**
	 * @return float
	 */
	public function getAmount() {
		return $this->amount;
	}
	
	/**
	 * @param float $amount
	 */
	public function setAmount( $amount ) {
		$this->amount = (float) $amount;
	}
	
	/**
	 * @return string
	 */
	public function getCurrency() {
		return $this->currency;
	}
	
	/**
	 * @param string $currency
	 */
	public function setCurrency( $currency ) {
		$this->currency = $currency;
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
		$this->created_by = (int) $created_by;
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
	public function getType() {
		return $this->type;
	}
	
	/**
	 * @param string $type
	 */
	public function setType( $type ) {
		$this->type = $type;
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
	 * @return float
	 */
	public function getPosPaid() {
		return $this->pos_paid;
	}
	
	/**
	 * @param float $pos_paid
	 */
	public function setPosPaid( $pos_paid ) {
		$this->pos_paid = (float) $pos_paid;
	}
	
	/**
	 * @return float
	 */
	public function getPosBalance() {
		return $this->pos_balance;
	}
	
	/**
	 * @param float $pos_balance
	 */
	public function setPosBalance( $pos_balance ) {
		$this->pos_balance = (float) $pos_balance;
	}
	
	/**
	 * @return string
	 */
	public function getApprovalCode() {
		return $this->approval_code;
	}
	
	/**
	 * @param string $approval_code
	 */
	public function setApprovalCode( $approval_code ) {
		$this->approval_code = $approval_code;
	}
	
	public function save() {
		$new = ! $this->exists();
		if ( parent::save() ) {
			if ( $new ) {
				// update ref only if new payment.
				$this->site->updateReference( 'pay' );
			}
			$this->site->syncSalePayments( $this->getSaleId() );
			return true;
		}
		return false;
	}
}
// End of file Erp_Address.php.
