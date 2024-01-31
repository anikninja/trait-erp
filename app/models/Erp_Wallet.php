<?php
/**
 * Wallet (Summery)
 * 
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Wallet extends MY_RetailErp_Model {
	
	protected $table = 'wallet';    
	
	/**
	 * @var int
	 */
	protected $user_id;
	
	/**
	 * @var float
	 */
	protected $amount = 0;
	
	/**
	 * Datetime
	 * @var string
	 */
	protected $created;
	
	/**
	 * Datetime
	 * @var string
	 */
	protected $updated;
	
	/**
	 * @var bool
	 */
	protected $has_pending_withdrawal;
	
	/**
	 * @param int $user_id
	 * @param bool $calculate_balance
	 *
	 * @return bool|Erp_Wallet
	 */
	public static function get_user_wallet( $user_id, $calculate_balance = true ) {
		$user_id = absint( $user_id );
		if ( ! $user_id ) {
			return false;
		}
		$wallet = self::get_ci_instance()->db->select( 'id' )->where( 'user_id', $user_id )->get( 'wallet' )->row();
		if ( $wallet ) {
			return new self( $wallet->id );
		} else {
			$user = self::get_ci_instance()->db
				->select( 'id' )
				->where(
					[
						'id' => (int) $user_id,
						'active'  => 1,
					]
				)
				->get( 'users' )->row();
			if ( $user ) {
				$wallet = new self();
				$wallet->setUserId( $user->id );
				if( $wallet->save() && $calculate_balance ) {
				    $balance = self::get_ci_instance()->db
				    ->select( 'SUM(debit) - SUM(credit) AS balance', FALSE )
				    ->where(
				        [
				            'user_id' => (int) $user_id,
				            // 'status'  => 'approved',
				        ]
				    )
			        ->group_by('user_id')
			        ->get( 'transactions' )->row();
					if ( $balance ) {
				        $wallet->updateAmount($balance->balance);
				        $wallet->save();
				    }
				}
				return $wallet;
			}
			return false;
		}
	}
	
	/**
	 * @return bool
	 */
	public function has_pending_withdrawal() {
		if ( ! $this->getId() ) {
			return false;
		}
		if ( is_null( $this->has_pending_withdrawal ) ) {
			$this->db->where( 'status', 'applied' );
			$this->db->where( 'user_id', $this->getUserId() );
			$this->has_pending_withdrawal = $this->db->count_all_results( 'wallet_withdraw' ) > 0;
		}
		
		return $this->has_pending_withdrawal;
	}
	
	/**
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}
	
	public function getUser() {
		if ( $this->getId() ) {
			return new Erp_User( $this->getId() );
		}
		return false;
	}
	
	/**
	 * @param float $user_id
	 */
	public function setUserId( $user_id ) {
		if ( ! $this->user_id ) {
			$this->user_id = $user_id;
		}
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
	 * Update Wallet Amount.
	 * amount can be negative.
	 * @param float $amount
	 */
	public function updateAmount( $amount ) {
		$this->amount += $amount;
	}
	
	/**
	 * @return string
	 */
	public function getCreated() {
		return $this->created;
	}
	
	/**
	 * @param string $created
	 */
	protected function setCreated( $created ) {
		$this->created = $created;
	}
	
	/**
	 * @return string
	 */
	public function getUpdated() {
		return $this->updated;
	}
	
	/**
	 * @param string $updated
	 */
	protected function setUpdated( $updated ) {
		$this->updated = $updated;
	}
	
	public function save() {
		if ( ! $this->getId() ) {
			$this->created = $this->format_date();
		}
		$this->setUpdated( $this->format_date() );
		
		return parent::save();
	}
}
