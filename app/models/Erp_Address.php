<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Address extends MY_RetailErp_Model {
	protected $table = 'addresses';
	
	/**
	 * @var int
	 */
	protected $company_id;
	
	/**
	 * @var string
	 */
	protected $line1;
	
	/**
	 * @var string
	 */
	protected $line2;
	
	/**
	 * @var string
	 */
	protected $city;
	
	/**
	 * @var string
	 */
	protected $postal_code;
	
	/**
	 * @var string
	 */
	protected $state;
	
	/**
	 * @var string
	 */
	protected $country;
	
	/**
	 * @var int
	 */
	protected $area;
	
	/**
	 * @var int
	 */
	protected $zone;
	
	/**
	 * @var string
	 */
	protected $phone;
	
	/**
	 * @var string
	 */
	protected $updated_at;
	
	/**
	 * @return int
	 */
	public function getCompanyId() {
		return $this->company_id;
	}
	
	public function getLine( $sep = ' ' ) {
		if ( ! $sep || ! is_string( $sep ) ) {
			$sep = ' ';
		}
		return trim( implode( $sep, [ $this->getLine1(), $this->getLine2() ] ) );
	}
	
	/**
	 * @return string
	 */
	public function getLine1() {
		return trim( $this->line1 );
	}
	
	/**
	 * @return string
	 */
	public function getLine2() {
		return trim( $this->line2 );
	}
	
	/**
	 * @return string
	 */
	public function getCity() {
		return $this->city;
	}
	
	/**
	 * @return string
	 */
	public function getPostalCode() {
		return $this->postal_code;
	}
	
	/**
	 * @return string
	 */
	public function getState() {
		return $this->state;
	}
	
	/**
	 * @return string
	 */
	public function getCountry() {
		return $this->country;
	}
	
	public function getArea() {
		return $this->area;
	}
	
	public function getZone() {
		return $this->zone;
	}
	
	/**
	 * @return string
	 */
	public function getPhone() {
		return $this->phone;
	}
	
	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->updated_at;
	}
	
	/**
	 * @param int $company_id
	 */
	public function setCompanyId( $company_id ) {
		$this->company_id = (int) $company_id;
	}
	
	/**
	 * @param string $line1
	 */
	public function setLine1( $line1 ) {
		$this->line1 = $line1;
	}
	
	/**
	 * @param string $line2
	 */
	public function setLine2( $line2 ) {
		$this->line2 = $line2;
	}
	
	/**
	 * @param string $city
	 */
	public function setCity( $city ) {
		$this->city = $city;
	}
	
	/**
	 * @param string $postal_code
	 */
	public function setPostalCode( $postal_code ) {
		$this->postal_code = $postal_code;
	}
	
	/**
	 * @param string $state
	 */
	public function setState( $state ) {
		$this->state = $state;
	}
	
	/**
	 * @param string $country
	 */
	public function setCountry( $country ) {
		$this->country = $country;
	}
	
	public function setArea( $area ) {
		$this->area = $this->absint( $area );
	}
	
	public function setZone( $zone ) {
		$this->zone = $this->absint( $zone );
	}
	
	/**
	 * @param string $phone
	 */
	public function setPhone( string $phone ) {
		$this->phone = $phone;
	}
	
	/**
	 * @param string|DateTime|int $updated_at timestamp
	 */
	public function setUpdatedAt( $updated_at ) {
		$this->updated_at = $this->format_date( $updated_at );
	}

	public function get_formatted_address(){
		if ( ! $this->getId() ) {
			return '';
		}
	}

}
// End of file Erp_Address.php.
