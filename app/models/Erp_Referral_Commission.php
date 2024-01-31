<?php
defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Erp_Referral_Commission extends MY_RetailErp_Model {
	
	protected $table = 'referral_commissions';
	
	protected $option = '';
	
	/**
	 * @var int
	 */
	protected $group_id;
	
	/**
	 * @var float
	 */
	protected $rate = 0;
	
	/**
	 * @var string
	 */
	protected $description;
	
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
	
	public function __construct( $group_id = null ) {
		if ( $group_id ) {
			$this->group_id = $group_id;
			$this->option   = $this->table . '_' . $group_id;
			$data           = $this->Erp_Options->getOption( $this->option );
			if ( $data ) {
				$this->setData( $data );
			}
		}
	}
	
	/**
	 * @return number
	 */
	public function getGroupId() {
		return $this->group_id;
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
	public function getCreatedBy() {
		return $this->created_by;
	}
	
	/**
	 * @return number
	 */
	public function getModifiedBy() {
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
		$this->option   = $this->table . '_' . $group_id;
		$this->group_id = $group_id;
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
		if ( ! $this->getGroupId() || ! $this->option ) {
			return false;
		}
		if ( ! $this->created_by ) {
			$this->created_by = $this->session->userdata( 'user_id' );
			$this->created    = $this->format_date();
		}
		
		$this->modified_by = $this->session->userdata( 'user_id' );
		$this->modified    = $this->format_date();
		
		$data = $this->getData();
		
		return $this->Erp_Options->updateOption( $this->option, $data, false );
	}
	
	public function delete() {
		if ( ! $this->getGroupId() || ! $this->option ) {
			return false;
		}
		
		return $this->Erp_Options->deleteOption( $this->option );
	}
}
