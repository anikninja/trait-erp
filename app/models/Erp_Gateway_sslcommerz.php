<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Gateway_sslcommerz extends MY_Gateway_Model {
	protected $table   = 'sslcommerz';
	protected $gateway = 'sslcommerz';
	protected $active;
	protected $store_id;
	protected $store_password;
	protected $merchant_id;
	protected $sslcommerz_currency;
	protected $account_email;
	protected $fixed_charges;
	protected $extra_charges_my;
	protected $extra_charges_other;
	
	public function __construct() {
		parent::__construct( 1 );
	}
}
// End of file Erp_Gateway_sslcommerz.php.
