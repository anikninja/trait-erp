<?php
defined('BASEPATH') or exit('No direct script access allowed');

if ( ! trait_exists( 'MY_Model_Trait' ) ) {
	require 'MY_Model_Trait.php';
}

class MY_Model extends CI_Model {
	
	use MY_Model_Trait;
	
	public function __construct() {
		parent::__construct();
	}
}
// End of the file MY_Model.php
