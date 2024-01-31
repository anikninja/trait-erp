<?php
defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Erp_Transaction_History extends MY_RetailErp_Model {
	
	/**
	 * @var int
	 */
	public $user_id;
	
	/**
	 * @var int
	 */
	public $item_per_page = 10;
	
	/**
	 * @var Erp_Wallet
	 */
	public $wallet; // this wallet
	
	/**
	 * Erp_Transaction_History constructor.
	 *
	 * @param int $user_id
	 */
	public function __construct( $user_id = null ) {
		if ( $user_id ) {
			$this->user_id = $user_id;
		}
	}
	
	public function setItemPerPage( $per_page ) {
		$per_page = $this->absint( $per_page );
		if ( $per_page ) {
			$this->item_per_page = $this->absint( $per_page );
		}
	}
	
	public function getItemPerPage() {
		return $this->item_per_page;
	}
	
	/**
	 * @param bool $update update/reload wallet data from db.
	 * @return bool|Erp_Wallet
	 */
	public function getWallet( $update = false ) {
		if ( ! $this->wallet || $update ) {
			if ( $this->user_id ) {
				$wallet = Erp_Wallet::get_user_wallet( $this->user_id );
				if ( $wallet ) {
					$this->wallet = $wallet;
				}
			}
		}
		
		return $this->wallet;
	}
	
	/**
	 *
	 * @param int $page
	 * @return array|Erp_Transaction[]
	 */
	public function getTransactions( $page = 1 ) {
	    if ( ! $this->user_id ) {
			return [];
		}
		$page = max( $this->absint( $page ), 1 );
		return $this->db
			->where( 'user_id', $this->user_id )
			->order_by('id', 'DESC')
			->get( 'transactions', $this->item_per_page, absint( $this->item_per_page - ( $page * $this->item_per_page ) ) )
		    ->result( 'Erp_Transaction' );
	}
	
	public function getTransactionCount() {
		if ( ! $this->user_id ) {
			return 0;
		}
		$this->db->where('user_id', $this->user_id);
		return $this->db->count_all_results('transactions');
	}
	
	public function delete() {
		return false;
	}
	
	public function save() {
		return false;
	}
}
