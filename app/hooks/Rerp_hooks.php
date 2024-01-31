<?php

class Rerp_hooks {
    protected $CI;
    
    public function __construct() {
        $this->CI =& get_instance();
    }
    
    public function check() {
        if(! ($this->CI->db->conn_id)) {
            header("Location: install/index.php");
            die();
        }
    }
    
    public function setTimeZone() {
	    $settings = $this->CI->db
		    ->select( 'timezone' )
		    ->where( 'setting_id', 1 )
		    ->get( 'settings' )->row_array();
	    if ( isset( $settings['timezone'] ) && ! empty( $settings['timezone'] ) ) {
		    if ( function_exists( 'date_default_timezone_set' ) ) {
			    date_default_timezone_set( $settings['timezone'] );
		    }
		    if ( ! defined( 'TIMEZONE' ) ) {
			    define( 'TIMEZONE', $settings['timezone'] );
		    }
	    }
    }
}
