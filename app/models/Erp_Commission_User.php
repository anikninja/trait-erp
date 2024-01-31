<?php
defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Erp_Commission_User extends MY_RetailErp_Model {
	
	protected $table = 'commission_users';
	
	/**
	 * @var int
	 */
	protected $group_id;
	
	/**
	 * @var int
	 */
	protected $user_id;
	
	/**
	 * @var Erp_User
	 */
	protected $user;
	
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
	public $created;
	
	/**
	 * @var int
	 */
	protected $modified_by;
	
	/**
	 * @var DateTime
	 */
	public $modified;
	
	/**
	 * @return number
	 */
	public function getGroupId() {
		return $this->group_id;
	}
	
	/**
	 * @return number
	 */
	public function getUserId() {
		return $this->user_id;
	}
	
	/**
	 * @return null|Erp_User
	 */
	public function getUser() {
		if ( ! $this->user ) {
			$this->user = new Erp_User( $this->getUserId() );
		}
		
		return $this->user;
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
		return $this->is_enabled;
	}
	
	/**
	 * @return number
	 */
	public function getCreated_by() {
		return $this->created_by;
	}
	
	/**
	 * @return number
	 */
	public function getModified_by() {
		return $this->modified_by;
	}
	
	public function getCreated() {
		return $this->created;
	}
	
	public function getModified() {
		return $this->modified;
	}
	
	/**
	 * @param number $group_id
	 */
	public function setGroupId( $group_id ) {
		$this->group_id = $group_id;
	}
	
	/**
	 * @param number $user_id
	 */
	public function setUserId( $user_id ) {
		$this->user_id = $user_id;
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
	 * @param number $created_by
	 */
	public function setCreatedBy( $created_by ) {
		$this->created_by = $created_by;
	}
	
	/**
	 * @param number $modified_by
	 */
	public function setModifiedBy( $modified_by ) {
		$this->modified_by = $modified_by;
	}
	
	
	public function setCreated( $created ) {
		$this->created = $created;
	}
	
	public function setModified( $modified ) {
		$this->modified = $modified;
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
	
	
}
