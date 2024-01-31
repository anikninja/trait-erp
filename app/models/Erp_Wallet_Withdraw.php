<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Wallet_Withdraw extends MY_RetailErp_Model {
	
	protected $table = 'wallet_withdraw';
	
	/**
	 * @var int
	 */
	protected $user_id;
	
	/**
	 * @var string
	 */
	protected $reference_no;
	
	/**
	 * @see Erp_Wallet_Withdraw::_types()
	 * @var string
	 */
	protected $type;
	
	/**
	 * applied
	 * approved
	 * reject
	 *
	 * @var string
	 */
	protected $status;
	
	/**
	 * @var float
	 */
	protected $amount;
	
	/**
	 * @var string
	 */
	protected $description;
	
	/**
	 * @var string
	 */
	protected $payment_detail;
	
	/**
	 * @var int
	 */
	protected $transaction_id;
	
	/**
	 * @var string
	 */
	protected $attachment;
	
	/**
	 * @var int
	 */
	protected $request_by;
	
	/**
	 * Datetime
	 * @var string
	 */
	protected $request_date;
	
	/**
	 * @var int
	 */
	protected $approved_by;
	
	/**
	 * Datetime
	 * @var string
	 */
	protected $approved_date;
	
	/**
	 * @var int
	 */
	protected $modified_by;
	
	/**
	 * Datetime
	 * @var string
	 */
	protected $modified_date;
	
	/**
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}
	
	/**
	 * @param int $user_id
	 */
	public function setUserId( $user_id ) {
		$this->user_id = $user_id;
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
	 * @return float
	 */
	public function getAmount() {
		return $this->amount;
	}
	
	/**
	 * @param float $amount
	 */
	public function setAmount( $amount ) {
		$this->amount = $amount;
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
	 * @return string
	 */
	public function getPaymentDetail() {
		return $this->payment_detail;
	}
	
	/**
	 * @param string $payment_detail
	 */
	public function setPaymentDetail( $payment_detail ) {
		$this->payment_detail = $payment_detail;
	}
	
	/**
	 * @return int
	 */
	public function getTransactionId() {
		return $this->transaction_id;
	}
	
	/**
	 * @param int $transaction_id
	 */
	public function setTransactionId( $transaction_id ) {
		$this->transaction_id = $this->absint( $transaction_id );
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
	 * @return int
	 */
	public function getRequestBy() {
		return $this->request_by;
	}
	
	/**
	 * @param int $request_by
	 */
	public function setRequestBy( $request_by ) {
		$this->request_by = $request_by;
	}
	
	/**
	 * @return string
	 */
	public function getRequestDate() {
		return $this->request_date;
	}
	
	/**
	 * @param string $request_date
	 */
	public function setRequestDate( $request_date ) {
		$this->request_date = $request_date;
	}
	
	/**
	 * @return int
	 */
	public function getApprovedBy() {
		return $this->approved_by;
	}
	
	/**
	 * @param int $approved_by
	 */
	public function setApprovedBy( $approved_by ) {
		$this->approved_by = $approved_by;
	}
	
	/**
	 * @return string
	 */
	public function getApprovedDate() {
		return $this->approved_date;
	}
	
	/**
	 * @param string $approved_date
	 */
	public function setApprovedDate( $approved_date ) {
		$this->approved_date = $approved_date;
	}
	
	/**
	 * @return int
	 */
	public function getModifiedBy() {
		return $this->modified_by;
	}
	
	/**
	 * @param int $modified_by
	 */
	public function setModifiedBy( $modified_by ) {
		$this->modified_by = $modified_by;
	}
	
	/**
	 * @return string
	 */
	public function getModifiedDate() {
		return $this->modified_date;
	}
	
	/**
	 * @param string $modified_date
	 */
	public function setModifiedDate( $modified_date ) {
		$this->modified_date = $modified_date;
	}
	
	public function setPurchaseWithdrawal() {
	    $this->type = 'purchase';
	}
	
	public function setBankWithdrawal() {
	    $this->type = 'bank';
	}
	
	public function setCashWithdrawal() {
	    $this->type = 'cash';
	}
	
	public function setCheckWithdrawal() {
	    $this->type = 'check';
	}
	
	public function setOtherWithdrawal() {
	    $this->type = 'other';
	}
	
	private function _types() {
	    return [ 'purchase', 'bank', 'check', 'cash', 'other' ];
	}
	
	/**
	 *
	 * @return bool
	 */
	public function save() {
	    if ( ! in_array( $this->type, $this->_types() ) ) {
	        return false;
	    }
		if( ! $this->request_date ) {
			$this->request_date = $this->format_date();
		}
		
		if( 'approved' === $this->status && ! $this->approved_date ) {
			$this->approved_date = $this->format_date();
		}
		
		if ( ! $this->reference_no ) {
			$this->reference_no = $this->get_sequential_reference( 'WDL' );
		}
		
		if ( null === $this->modified_by ) {
			$this->modified_by = $this->session->userdata( 'user_id' );
		}
		$this->modified_date = $this->format_date();
		
		$save = parent::save();
		
		if ( $save ) {
			$this->update_sequential_reference( 'WDL' );
		}
		
		// current status is approved & data saved & doesn't have transaction.
		if ( 'approved' === $this->getStatus() && $save && ! $this->getTransactionId() ) {
			if ( ! class_exists( 'Erp_Transaction', false ) ) {
				$this->load->model( 'Erp_Transaction' );
			}
			
			$tnx = new Erp_Transaction();
			$tnx->setUserId( $this->getUserId() );
			$tnx->setType( 'withdraw' );
			$tnx->setStatus( 'approved' );
			$tnx->setCredit( $this->getAmount() );
			$tnx->setDescription( sprintf( 'Withdrawal Request: %s', $this->getDescription() ) );
			
			if ( $tnx->save() ) {
				$this->setTransactionId( $tnx->getId() );
				parent::save();
			}
		}
		
		return $save;
	}
}
