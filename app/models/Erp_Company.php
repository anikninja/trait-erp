<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Erp_Company
 * Company Describes a user's company information or primary address
 * this also describes a customer of invoice.
 */
class Erp_Company extends MY_RetailErp_Model {
	protected $table = 'companies';

	/**
	 * Group ID
	 *
	 * @var int
	 */
	protected $group_id = 3;
	/**
	 * Group Name
	 *
	 * @var string
	 */
	protected $group_name = 'customer';
	/**
	 * Customer Group ID
	 *
	 * @var int
	 */
	protected $customer_group_id = 1;
	/**
	 * Customer Group Name
	 *
	 * @var string
	 */
	protected $customer_group_name = 'General';
	/**
	 * Customer Full Name
	 *
	 * @var string
	 */
	protected $name = '';
	/**
	 * Company Name.
	 *
	 * @var string
	 */
	protected $company = '';
	/**
	 * Vat No.
	 *
	 * @var string
	 */
	protected $vat_no = '';
	/**
	 * Address Line 1 & 2
	 *
	 * @var string
	 */
	protected $address = '';
	/**
	 * City
	 *
	 * @var string
	 */
	protected $city = '';
	/**
	 * State
	 *
	 * @var string
	 */
	protected $state = '';
	/**
	 * ZIP/Post Code
	 *
	 * @var string
	 */
	protected $postal_code = '';
	/**
	 * Country Name.
	 *
	 * @var string
	 */
	protected $country = '';
	/**
	 * Phone Number (including dialing code)
	 *
	 * @var string
	 */
	protected $phone = '';
	/**
	 * Email Address
	 *
	 * @var string
	 */
	protected $email = '';
	/**
	 * @var string
	 */
	protected $cf1 = '';
	/**
	 * @var string
	 */
	protected $cf2 = '';
	/**
	 * @var string
	 */
	protected $cf3 = '';
	/**
	 * @var string
	 */
	protected $cf4 = '';
	/**
	 * @var string
	 */
	protected $cf5 = '';
	/**
	 * @var string
	 */
	protected $cf6 = '';
	/**
	 * @var string
	 */
	protected $invoice_footer = '';
	/**
	 * @var int
	 */
	protected $payment_term = 0;
	/**
	 * @var string
	 */
	protected $logo = '';
	/**
	 * @var int
	 */
	protected $award_points = 0;
	/**
	 * @var float
	 */
	protected $deposit_amount = '';
	/**
	 * @var int
	 */
	protected $price_group_id = 1;
	/**
	 * @var string
	 */
	protected $price_group_name = 'Default';
	/**
	 * @var string
	 */
	protected $gst_no = '';
	
	/**
	 * @return int
	 */
	public function getGroupId() {
		return (int) $this->group_id;
	}
	
	/**
	 * @param int $group_id
	 * @return bool
	 */
	public function setGroupId( $group_id ) {
		$group_id = $this->absint( $group_id );
		if ( $group_id ) {
			$group = $this->db->select( 'name' )->where( 'id', $group_id )->get( 'groups' )->row();
			if ( $group ) {
				$this->group_id = $this->absint( $group_id );
				// @TODO group object.
				$this->group_name = $group->name;
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @param int $group_id
	 *
	 * @return bool
	 */
	public function setGroupName( $group_id ) {
		return $this->setGroupId( $group_id );
	}
	
	/**
	 * @return string
	 */
	public function getGroupName() {
		return $this->group_name;
	}
	
	/**
	 * @return int
	 */
	public function getCustomerGroupId() {
		return $this->customer_group_id;
	}
	
	/**
	 * @param int $customer_group_id
	 *
	 * @return bool
	 */
	public function setCustomerGroupId( $customer_group_id ) {
		$customer_group_id = $this->absint( $customer_group_id );
		if ( $customer_group_id ) {
			$customer_group = $this->db->get_where('customer_groups', ['id' => $customer_group_id ])->row();
			if ( $customer_group ) {
				$this->customer_group_id   = $customer_group->id;
				$this->customer_group_name = $customer_group->name;
				return true;
			}
		}
			return false;
	}
	
	/**
	 * @param int $customer_group_id
	 *
	 * @return bool
	 */
	public function setCustomerGroupName( $customer_group_id ) {
		return $this->setCustomerGroupId( $customer_group_id );
	}
	
	/**
	 * @return string
	 */
	public function getCustomerGroupName() {
		return $this->customer_group_name;
	}
	
	
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}
	
	/**
	 * @param bool $fallback_name
	 * @return string
	 */
	public function getCompany( $fallback_name = false ) {
		if ( $fallback_name ) {
			if ( empty( $this->company ) || $this->company == '-' ) {
				return $this->getName();
			}
		}
		return $this->company;
	}
	
	/**
	 * @param string $company
	 */
	public function setCompany( $company ) {
		$this->company = $company;
	}
	
	/**
	 * @return string
	 */
	public function getVatNo() {
		return $this->vat_no;
	}
	
	/**
	 * @param string $vat_no
	 */
	public function setVatNo( $vat_no ) {
		$this->vat_no = $vat_no;
	}
	
	/**
	 * @param bool $replace_br
	 * @param string $replacement
	 * @return string
	 */
	public function getAddress( $replace_br = false, $replacement = ' ' ) {
		if ( $replace_br ) {
			$address = nl2br( $this->address, false );
			$address = str_replace( [ '<br>', '<br />', '<BR>', '<BR />' ], $replacement, $address );
			$address = str_replace( [ ' ' ], ' ', $address );
			return trim( $address, " \t\n\r\0\x0B" . $replacement );
		}
		return $this->address;
	}
	
	/**
	 * @param string $address
	 */
	public function setAddress( $address ) {
		$this->address = $address;
	}
	
	/**
	 * @return string
	 */
	public function getCity() {
		return $this->city;
	}
	
	/**
	 * @param string $city
	 */
	public function setCity( $city ) {
		$this->city = $city;
	}
	
	/**
	 * @return string
	 */
	public function getState() {
		return $this->state;
	}
	
	/**
	 * @param string $state
	 */
	public function setState( $state ) {
		$this->state = $state;
	}
	
	/**
	 * @return string
	 */
	public function getPostalCode() {
		return $this->postal_code;
	}
	
	/**
	 * @param string $postal_code
	 */
	public function setPostalCode( $postal_code ) {
		$this->postal_code = $postal_code;
	}
	
	/**
	 * @return string
	 */
	public function getCountry() {
		return $this->country;
	}
	
	/**
	 * @param string $country
	 */
	public function setCountry( $country ) {
		$this->country = $country;
	}
	
	/**
	 * @return string
	 */
	public function getPhone() {
		return $this->phone;
	}
	
	/**
	 * @param string $phone
	 */
	public function setPhone( $phone ) {
		$this->phone = $phone;
	}
	
	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}
	
	/**
	 * @param string $email
	 */
	public function setEmail( $email ) {
		if ( (bool) filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$this->email = $email;
		}
	}
	
	/**
	 * @return string
	 */
	public function getCf1() {
		return $this->cf1;
	}
	
	/**
	 * @return string
	 */
	public function getCf2() {
		return $this->cf2;
	}
	
	/**
	 * @return string
	 */
	public function getCf3() {
		return $this->cf3;
	}
	
	/**
	 * @return string
	 */
	public function getCf4() {
		return $this->cf4;
	}
	
	/**
	 * @return string
	 */
	public function getCf5() {
		return $this->cf5;
	}
	
	/**
	 * @return string
	 */
	public function getCf6() {
		return $this->cf6;
	}
	
	/**
	 * @param string $cf1
	 */
	public function setCf1( $cf1 ) {
		$this->cf1 = $cf1;
	}
	
	/**
	 * @param string $cf2
	 */
	public function setCf2( $cf2 ) {
		$this->cf2 = $cf2;
	}
	
	/**
	 * @param string $cf3
	 */
	public function setCf3( $cf3 ) {
		$this->cf3 = $cf3;
	}
	
	/**
	 * @param string $cf4
	 */
	public function setCf4( $cf4 ) {
		$this->cf4 = $cf4;
	}
	
	/**
	 * @param string $cf5
	 */
	public function setCf5( $cf5 ) {
		$this->cf5 = $cf5;
	}
	
	/**
	 * @param string $cf6
	 */
	public function setCf6( $cf6 ) {
		$this->cf6 = $cf6;
	}
	
	/**
	 * @return string
	 */
	public function getInvoiceFooter() {
		return $this->invoice_footer;
	}
	
	/**
	 * @param string $invoice_footer
	 */
	public function setInvoiceFooter( $invoice_footer ) {
		$this->invoice_footer = $invoice_footer;
	}
	
	/**
	 * @return int
	 */
	public function getPaymentTerm() {
		return $this->payment_term;
	}
	
	/**
	 * @param int $payment_term
	 */
	public function setPaymentTerm( $payment_term ) {
		$this->payment_term = (int) $payment_term;
	}
	
	/**
	 * @return string
	 */
	public function getLogo() {
		return $this->logo;
	}
	
	/**
	 * @param string $logo
	 */
	public function setLogo( string $logo ) {
		$this->logo = $logo;
	}
	
	/**
	 * @return int
	 */
	public function getAwardPoints() {
		return $this->award_points;
	}
	
	/**
	 * @param int $award_points
	 */
	public function setAwardPoints( $award_points ) {
		$this->award_points = (int) $award_points;
	}
	
	/**
	 * @return float
	 */
	public function getDepositAmount() {
		return $this->deposit_amount;
	}
	
	/**
	 * @param float $deposit_amount
	 */
	public function setDepositAmount( $deposit_amount ) {
		$this->deposit_amount = (float) $deposit_amount;
	}
	
	/**
	 * @return int
	 */
	public function getPriceGroupId() {
		return $this->price_group_id;
	}
	
	/**
	 * @param int $price_group_id
	 * @return bool
	 */
	public function setPriceGroupId( $price_group_id ) {
		$price_group_id = $this->absint( $price_group_id );
		if ( $price_group_id ) {
			$price_group = $this->db->get_where( 'price_groups', [ 'id' => $price_group_id ] )->row();
			if ( $price_group ) {
				$this->price_group_id   = $price_group->id;
				$this->price_group_name = $price_group->name;
				return true;
			}
			return true;
		}
		return false;
	}
	
	/**
	 * @return string
	 */
	public function getPriceGroupName() {
		return $this->price_group_name;
	}
	
	/**
	 * @param string $price_group_id
	 * @return bool
	 */
	public function setPriceGroupName( $price_group_id ) {
		return $this->setPriceGroupId( $price_group_id );
	}
	
	/**
	 * @return string
	 */
	public function getGstNo() {
		return $this->gst_no;
	}
	
	/**
	 * @param string $gst_no
	 */
	public function setGstNo( $gst_no ) {
		$this->gst_no = $gst_no;
	}
}
// End of file Erp_Company.php.
