<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Referral
 * 
 */
class Referral extends MY_Controller {
	
    public function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->rerp->md('login');
        }
	
	    if ( ! $this->Owner ) {
		    $this->session->set_flashdata( 'warning', lang( 'access_denied' ) );
		    redirect( 'admin' );
	    }
	    $this->lang->admin_load( 'settings', $this->Settings->user_language );
	    $this->load->library( 'form_validation' );
	    $this->load->model( 'Erp_Referral' );
	    $this->load->admin_model( 'Referral_model' );
    }
    
    
    public function index() {
    
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [ [ 'link' => base_url(), 'page' => lang('home')], ['link' => admin_url('referral/'), 'page' => lang( 'referral' ) ] ];
        $meta                = [ 'page_title' => lang('referral'), 'bc' => $bc ];
                
        $this->page_construct( 'referral/index', $meta, $this->data );
    }
    
    public function getReferralList() {
        $this->load->library('datatables');
        $this->datatables
        ->select( "CONCAT( a1.first_name, ' ', a1.last_name ) as username, CONCAT( b1.first_name, ' ', b1.last_name ) as referral_user, description, created as joining", false )
        ->from('referral')
        ->join('users as a1', 'a1.id = referral.user_id')
        ->join('users as b1', 'b1.id = referral.referral_id');
        
        echo $this->datatables->generate();
    }
}
