<?php
defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Erp_Commission_Group extends MY_RetailErp_Model {
	
	protected $table = 'commission_groups';
	
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var int
	 */
	protected $category_id;
	
	/**
	 * @var float
	 */
	protected $rate;
	
	/**
	 * @var string
	 */
	protected $description;
	
	/**
	 * @var int
	 */
	protected $is_enabled = 0;
	
	/**
	 * @var int
	 */
	protected $created_by;
	
	/**
	 * @var DateTime
	 */
	protected $created;
	
	/**
	 * @var int
	 */
	protected $modified_by;
	
	/**
	 * @var DateTime
	 */
	protected $modified;
	
	public static function getByCategoryId( $categoryId ) {
		$group = self::get_ci_instance()->db
			->get_where(
				'commission_groups',
				[
					'category_id' => $categoryId,
					'is_enabled' => 1
				]
			)->row();
		if ( $group ) {
			return new self( $group->id );
		}
		
		return false;
	}
	
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @return number
	 */
	public function getCategoryId() {
		return $this->category_id;
	}
	
	/**
	 * @return number
	 */
	public function getRate() {
		return (float) $this->rate;
	}
	
	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	public function getIsEnabled() {
		return (bool) $this->is_enabled;
	}
	
	/**
	 * @return number
	 */
	public function getCreatedBy() {
		return $this->created_by;
	}
	
	/**
	 * @return number
	 */
	public function getModifiedBy() {
		return $this->modified_by;
	}
	
	/**
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}
	
	/**
	 * @param number $category_id
	 */
	public function setCategoryId( $category_id ) {
		$this->category_id = $category_id;
	}
	
	/**
	 * @param number $rate
	 */
	public function setRate( $rate ) {
		$this->rate = $rate;
	}
	
	/**
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = $description;
	}
	
	public function setIsEnabled( $is_enabled ) {
		$this->is_enabled = $is_enabled ? 1 : 0;
	}
	
	/**
	 * Get Group Users.
	 *
	 * @param bool $active_only
	 *
	 * @return Erp_Commission_User[]
	 */
	public function getUsers( $active_only = false ) {
		
		if ( ! $this->getId() ) {
			return [];
		}
		if ( $active_only ) {
			$this->db->where( 'is_enabled', '1' );
		}
		$this->db
			->where( 'group_id', $this->getId() )
			->order_by( 'created', 'DESC' );
		
		return $this->db->get( 'commission_users' )->result( 'Erp_Commission_User' );
	}
	
	/**
	 *
	 * @return bool|Erp_Referral_Commission
	 */
	public function getReferralCommission() {
		return new Erp_Referral_Commission( $this->getId() );
	}
	
	/**
	 *
	 * @return bool|Erp_Shopper_Commission
	 */
	public function getShopperCommission() {
		return new Erp_Shopper_Commission( $this->getId() );
	}
	
	public function save() {
		
		if ( ! $this->getId() ) {
			$this->created_by = $this->session->userdata( 'user_id' );
			$this->created    = $this->format_date();
		}
		
		$this->modified_by = $this->session->userdata( 'user_id' );
		$this->modified    = $this->format_date();
		
		return parent::save();
	}
	
	public function delete() {
		// delete all users associated with this commission group.
		$this->db->delete( 'commission_users', [ 'group_id' => $this->getId() ] );
		$this->getReferralCommission()->delete();
		return parent::delete();
		
//		if ( $this->db->affected_rows() > 0 ) {
//		}
	}
}
