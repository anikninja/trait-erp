<?php
defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Erp_Referral extends MY_RetailErp_Model {
	
	protected $table = 'referral';
	
	/**
	 * @var int
	 */
	protected $user_id;
	
	/**
	 * @var int
	 */
	protected $referral_id;
	
	/**
	 * @var string
	 */
	protected $description;
	
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
	
	public static function get_by( $id, $field = 'id' ) {
		if ( in_array( $field, [ 'customer', 'company' ], true ) ) {
			$user = get_instance()->db
				->select( 'id' )->where(
					[
						'company_id' => (int) $id,
						'active'     => 1,
					]
				)
                ->get( 'users' )->row();
		} else {
			$user = get_instance()->db
				->select( 'id' )
				->where(
					[
						'id' => (int) $id,
						'active'  => 1,
					]
				)
				->get( 'users' )->row(); // just validate the user exists...
		}
		
		if ( ! $user ) {
			return false;
		}
		$ref = get_instance()->db->select( 'id' )->where( 'user_id', $user->id )->get( 'referral' )->row();
		if ( ! $ref ) {
			return false;
		}
		return new self( $ref->id );
	}
	
	/**
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}
	
	/**
	 * @return int
	 */
	public function getReferralId() {
		return (int) $this->referral_id;
	}
	
	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * @return int
	 */
	public function getCreatedBy() {
		return (int) $this->created_by;
	}
	
	/**
	 * @return int
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
	 * @param int $user_id
	 */
	public function setUserId( $user_id ) {
		$this->user_id = $user_id;
	}
	
	/**
	 * @param int $referral_id
	 */
	public function setReferralId( $referral_id ) {
		$this->referral_id = $referral_id;
	}
	
	/**
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = $description;
	}
	
	/**
	 * @param int $created_by
	 */
	public function setCreatedBy( $created_by ) {
		$this->created_by = $created_by;
	}
	
	/**
	 * @param int $modified_by
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
			if ( null === $this->created_by ) {
				$this->created_by = $this->session->userdata( 'user_id' );
			}
			$this->created = $this->format_date();
		}
		if ( null === $this->modified_by ) {
			$this->modified_by = $this->session->userdata( 'user_id' );
		}
		$this->modified = $this->format_date();
		
		return parent::save();
	}
}
