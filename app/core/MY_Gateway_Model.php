<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Gateway_Model extends MY_RetailErp_Model {
	protected $table = '';
	protected $gateway = '';
	protected $active;
	protected $account_email;
	protected $fixed_charges;
	protected $extra_charges_my;
	protected $extra_charges_other;
}
// End of the file MY_RetailErp_Model.php
