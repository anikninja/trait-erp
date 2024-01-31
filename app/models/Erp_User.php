<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_User extends MY_RetailErp_Model {
	
	protected $table = 'users';
	
	/**
	 * @var string
	 */
	protected $last_ip_address;
	/**
	 * @var string
	 */
	protected $ip_address;
	/**
	 * @var string
	 */
	protected $username;
	/**
	 * @var string
	 */
	protected $password;
	/**
	 * @var string
	 */
	protected $salt;
	/**
	 * @var string
	 */
	protected $email;
	/**
	 * @var string
	 */
	protected $activation_code;
	/**
	 * @var string
	 */
	protected $forgotten_password_code;
	/**
	 * @var string
	 */
	protected $forgotten_password_time;
	/**
	 * @var string
	 */
	protected $remember_code;
	/**
	 * @var string
	 */
	protected $created_on;
	/**
	 * @var string
	 */
	protected $last_login;
	/**
	 * @var string
	 */
	protected $active;
	/**
	 * @var string
	 */
	protected $first_name;
	/**
	 * @var string
	 */
	protected $last_name;
	/**
	 * @var string
	 */
	protected $company;
	/**
	 * @var string
	 */
	protected $phone;
	/**
	 * @var string
	 */
	protected $avatar;
	/**
	 * @var string
	 */
	protected $gender;
	/**
	 * @var int
	 */
	protected $group_id;
	/**
	 * @var int
	 */
	protected $warehouse_id;
	/**
	 * @var int
	 */
	protected $biller_id;
	/**
	 * @var int
	 */
	protected $company_id;
	/**
	 * @var int
	 */
	protected $show_cost;
	/**
	 * @var string
	 */
	protected $show_price;
	/**
	 * @var int
	 */
	protected $award_points;
	/**
	 * @var int
	 */
	protected $view_right;
	/**
	 * @var int
	 */
	protected $edit_right;
	/**
	 * @var int
	 */
	protected $allow_discount;
	
	/**
	 * @return string
	 */
	public function getLastIpAddress() {
		return $this->last_ip_address;
	}
	
	/**
	 * @return string
	 */
	public function getIpAddress() {
		return $this->ip_address;
	}
	
	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}
	
	public function getDisplayName( $use_username = true, $initial = true ) {
	    return ci_get_user_display_name( $this, $use_username, $initial );
	}
	
	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}
	
	/**
	 * @return string
	 */
	public function getSalt() {
		return $this->salt;
	}
	
	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}
	
	/**
	 * @return string
	 */
	public function getActivationCode() {
		return $this->activation_code;
	}
	
	/**
	 * @return string
	 */
	public function getForgottenPasswordCode() {
		return $this->forgotten_password_code;
	}
	
	/**
	 * @return string
	 */
	public function getForgottenPasswordTime() {
		return $this->forgotten_password_time;
	}
	
	/**
	 * @return string
	 */
	public function getRememberCode() {
		return $this->remember_code;
	}
	
	/**
	 * @return string
	 */
	public function getCreatedOn() {
		return $this->created_on;
	}
	
	/**
	 * @return string
	 */
	public function getLastLogin() {
		return $this->last_login;
	}
	
	/**
	 * @return string
	 */
	public function getActive() {
		return $this->active;
	}
	
	/**
	 * @return string
	 */
	public function getFirstName() {
		return $this->first_name;
	}
	
	/**
	 * @return string
	 */
	public function getLastName() {
		return $this->last_name;
	}
	
	/**
	 * @return string
	 */
	public function getCompany() {
		return $this->company;
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
	public function getAvatar() {
		return $this->avatar;
	}
	
	/**
	 * @param bool   $img
	 * @param array  $attrs
	 * @param bool   $gravater
	 * @param int    $gs
	 * @param string $gd
	 * @param string $gr
	 *
	 * @return bool|string
	 */
	public function getAvatarImage( $img = true, $attrs = [], $gravater = true, $gs = 150, $gd = 'mp', $gr = 'g' ) {
		return ci_get_user_avatar( $this, $img, $attrs, $gravater, $gs, $gd, $gr );
	}
	
	/**
	 * @return string
	 */
	public function getGender() {
		return $this->gender;
	}
	
	/**
	 * @return int
	 */
	public function getGroupId() {
		return $this->group_id;
	}
	
	/**
	 * @return int
	 */
	public function getWarehouseId() {
		return $this->warehouse_id;
	}
	
	/**
	 * @return int
	 */
	public function getBillerId() {
		return $this->biller_id;
	}
	
	/**
	 * @return int
	 */
	public function getCompanyId() {
		return $this->company_id;
	}
	
	/**
	 * @return int
	 */
	public function getShowCost() {
		return $this->show_cost;
	}
	
	/**
	 * @return string
	 */
	public function getShowPrice() {
		return $this->show_price;
	}
	
	/**
	 * @return int
	 */
	public function getAwardPoints() {
		return $this->award_points;
	}
	
	/**
	 * @return int
	 */
	public function getViewRight() {
		return $this->view_right;
	}
	
	/**
	 * @return int
	 */
	public function getEditRight() {
		return $this->edit_right;
	}
	
	/**
	 * @return int
	 */
	public function getAllowDiscount() {
		return $this->allow_discount;
	}
	
	/**
	 * @param string $last_ip_address
	 */
	public function setLastIpAddress( $last_ip_address ) {
		$this->last_ip_address = $last_ip_address;
	}
	
	/**
	 * @param string $ip_address
	 */
	public function setIpAddress( $ip_address ) {
		$this->ip_address = $ip_address;
	}
	
	/**
	 * @param string $username
	 */
	public function setUsername( $username ) {
		$this->username = $username;
	}
	
	/**
	 * @param string $password
	 */
	public function setPassword( $password ) {
		$this->password = $password;
	}
	
	/**
	 * @param string $salt
	 */
	public function setSalt( $salt ) {
		$this->salt = $salt;
	}
	
	/**
	 * @param string $email
	 */
	public function setEmail( $email ) {
		$this->email = $email;
	}
	
	/**
	 * @param string $activation_code
	 */
	public function setActivationCode( $activation_code ) {
		$this->activation_code = $activation_code;
	}
	
	/**
	 * @param string $forgotten_password_code
	 */
	public function setForgottenPasswordCode( $forgotten_password_code ) {
		$this->forgotten_password_code = $forgotten_password_code;
	}
	
	/**
	 * @param string $forgotten_password_time
	 */
	public function setForgottenPasswordTime( $forgotten_password_time ) {
		$this->forgotten_password_time = $forgotten_password_time;
	}
	
	/**
	 * @param string $remember_code
	 */
	public function setRememberCode( $remember_code ) {
		$this->remember_code = $remember_code;
	}
	
	/**
	 * @param string $created_on
	 */
	public function setCreatedOn( $created_on ) {
		$this->created_on = $created_on;
	}
	
	/**
	 * @param string $last_login
	 */
	public function setLastLogin( $last_login ) {
		$this->last_login = $last_login;
	}
	
	/**
	 * @param string $active
	 */
	public function setActive( $active ) {
		$this->active = $active;
	}
	
	/**
	 * @param string $first_name
	 */
	public function setFirstName( $first_name ) {
		$this->first_name = $first_name;
	}
	
	/**
	 * @param string $last_name
	 */
	public function setLastName( $last_name ) {
		$this->last_name = $last_name;
	}
	
	/**
	 * @param string $company
	 */
	public function setCompany( $company ) {
		$this->company = $company;
	}
	
	/**
	 * @param string $phone
	 */
	public function setPhone( $phone ) {
		$this->phone = $phone;
	}
	
	/**
	 * @param string $avatar
	 */
	public function setAvatar( $avatar ) {
		$this->avatar = $avatar;
	}
	
	/**
	 * @param string $gender
	 */
	public function setGender( $gender ) {
		$this->gender = $gender;
	}
	
	/**
	 * @param int $group_id
	 */
	public function setGroupId( $group_id ) {
		$this->group_id = $group_id;
	}
	
	/**
	 * @param int $warehouse_id
	 */
	public function setWarehouseId( $warehouse_id ) {
		$this->warehouse_id = $warehouse_id;
	}
	
	/**
	 * @param int $biller_id
	 */
	public function setBillerId( $biller_id ) {
		$this->biller_id = $biller_id;
	}
	
	/**
	 * @param int $company_id
	 */
	public function setCompanyId( $company_id ) {
		$this->company_id = $company_id;
	}
	
	/**
	 *
	 * @param int $company_id
	 *
	 * @return bool|Erp_User
	 */
	public static function getUserBYCompanyId( $company_id ) {
		$user = get_instance()->db->get_where( 'users', [ 'company_id' => $company_id ] )->row();
		if ( $user ) {
			return new self( $user->id );
		}
		
		return false;
	}
	
	/**
	 * @param int $show_cost
	 */
	public function setShowCost( $show_cost ) {
		$this->show_cost = $show_cost;
	}
	
	/**
	 * @param string $show_price
	 */
	public function setShowPrice( $show_price ) {
		$this->show_price = $show_price;
	}
	
	/**
	 * @param int $award_points
	 */
	public function setAwardPoints( $award_points ) {
		$this->award_points = $award_points;
	}
	
	/**
	 * @param int $view_right
	 */
	public function setViewRight( $view_right ) {
		$this->view_right = $view_right;
	}
	
	/**
	 * @param int $edit_right
	 */
	public function setEditRight( $edit_right ) {
		$this->edit_right = $edit_right;
	}
	
	/**
	 * @param int $allow_discount
	 */
	public function setAllowDiscount( $allow_discount ) {
		$this->allow_discount = $allow_discount;
	}
	
	protected function getData() {
		$data = parent::getData();
		
		unset( $data['salt'] ); // only changeable by auth system.
		unset( $data['password'] ); // only changeable by auth system.
		unset( $data['email'] ); // not changeable.
		unset( $data['last_ip_address'] ); // only changeable by auth system.
		unset( $data['ip_address'] ); // only changeable by auth system.
		unset( $data['username'] ); // not changeable.
		
		return $data;
	}
}
// End of file Erp_User.php.
