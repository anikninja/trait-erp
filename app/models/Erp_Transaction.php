<?php
defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Erp_Transaction extends MY_RetailErp_Model {
	
	protected $table = 'transactions';
	
	/**
	 *
	 * @var int
	 */
	protected $user_id;
	
	/**
	 *
	 * @var string
	 */
	protected $reference_no;
	
	/**
	 *
	 * @var string
	 */
	protected $type;
	
	/**
	 *
	 * @var string
	 */
	protected $description;
	
	/**
	 *
	 * @var float
	 */
	protected $debit = 0;
	
	/**
	 *
	 * @var float
	 */
	protected $credit = 0;
	
	/**
	 *
	 * @var string
	 */
	protected $status;
	
	/**
	 *
	 * @var string
	 */
	protected $transaction_date;
	
	/**
	 *
	 * @var string
	 */
	protected $balance_date;
	
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
		if ( $this->getId() ) {
			log_message( 'error', 'cannot update transaction user id after created.' );
			return;
		}
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
		if ( $this->getId() ) {
			log_message( 'error', 'cannot update reference number after created.' );
			return;
		}
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
		if ( $this->getId() ) {
			log_message( 'error', 'cannot update transaction type after created.' );
			return;
		}
		$this->type = $type;
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
	 * @return float
	 */
	public function getDebit() {
		return $this->debit;
	}
	
	/**
	 * @param float $debit
	 */
	public function setDebit( $debit ) {
		if ( $this->getId() ) {
			log_message( 'error', 'cannot update transaction amounts after created.' );
			return;
		}
		$this->debit = $debit;
	}
	
	/**
	 * 
	 * @param int $user_id
	 * @return array|object[]|object|boolean
	 */
	
	public function getTotalBalanceByUserId( $user_id ){
		if ( $user_id ) {
            $this->db->select('rerp_transactions.user_id as id, rerp_users.username as username, sum(rerp_transactions.debit) as balance, max(rerp_transactions.transaction_date) as date')
	            ->from( 'transactions' )
            ->join('rerp_users', 'rerp_users.id = rerp_transactions.user_id')
            ->group_by('rerp_transactions.user_id')
	        ->where('rerp_transactions.user_id', $user_id);
	        $q = $this->db->get();
	        if ($q->num_rows() > 0) {
	            foreach (($q->result()) as $row) {
	                $data[] = $row;
	            }
	            return $data[0];
	        }
	    }
	    else {
	        return FALSE;
	    }   
	}
	
	/**
	 * @param int $user_id
	 * @return array|object[]|object|boolean
	 */
	
	public function getBalanceHistoryByUserId( $user_id ){
	    if($user_id){
	        $this->db->select('rerp_transactions.id as id, rerp_transactions.transaction_date as transaction_date, rerp_transactions.debit as amount, rerp_transactions.description as description')
	        ->from('rerp_transactions')
	        ->where('rerp_transactions.user_id', $user_id)
	        ->order_by('rerp_transactions.transaction_date', 'desc');
	        $q = $this->db->get();
	        if ($q->num_rows() > 0) {
	            foreach (($q->result()) as $row) {
	                $data[] = $row;
	            }
	            return $data;
	        }
	    }
	    else {
	        return FALSE;
	    }
	}
	
	/**
	 * @return float
	 */
	public function getCredit() {
		return $this->credit;
	}
	
	/**
	 * @param float $credit
	 */
	public function setCredit( $credit ) {
		if ( $this->getId() ) {
			log_message( 'error', 'cannot update transaction amounts after created.' );
			return;
		}
		$this->credit = $credit;
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
	
	
	public function setApproved() {
		$this->status = 'approved';
	}
	
	public function setUnapproved() {
		$this->status = 'unapproved';
	}
	
	/**
	 * @return string
	 */
	public function getTransactionDate() {
		return $this->transaction_date;
	}
	
	/**
	 * @param string $transaction_date
	 */
	public function setTransactionDate( $transaction_date ) {
		if ( $this->getId() ) {
			log_message( 'error', 'cannot update transaction dates after created.' );
			return;
		}
		$this->transaction_date = $transaction_date;
	}
	
	/**
	 * @return string
	 */
	public function getBalanceDate() {
		return $this->balance_date;
	}
	
	/**
	 * @param string $balance_date
	 */
	public function setBalanceDate( $balance_date ) {
		if ( $this->getId() ) {
			log_message( 'error', 'cannot update transaction dates after created.' );
			return;
		}
		$this->balance_date = $balance_date;
	}
	
	public function save() {
		
		if ( ! $this->getTransactionDate() ) {
			$this->transaction_date = $this->format_date();
		}
		
		if ( 'approved' === $this->getStatus() && ! $this->getBalanceDate() ) {
			$this->balance_date = $this->format_date();
		}
		
		if ( null === $this->reference_no ) {
			$this->reference_no = $this->get_sequential_reference( 'TRnX' );
		}
		
		$save = parent::save();
		if ( $save ) {
			$this->update_sequential_reference( 'TRnX' );
		}
		if ( $save && 'approved' === $this->getStatus() ) {
			$wallet = Erp_Wallet::get_user_wallet( $this->getUserId(), false );
			if ( $wallet ) {
				if ( $this->debit ) {
					$wallet->updateAmount( $this->debit );
				}
				if ( $this->credit ) {
					$wallet->updateAmount( '-' . $this->credit );
				}
				$wallet->save();
			}
		}
		
		return $save;
	}
}
