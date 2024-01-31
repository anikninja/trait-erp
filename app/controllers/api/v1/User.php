<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

/**
 * Class Products
 *
 * @property Users_api $users_api
 */
class User extends MY_REST_Controller {
    public function __construct() {
	    parent::__construct();
        $this->methods['index_get']['limit'] = 500;
	    $this->load->api_model( 'users_api' );
    }
    
    public function index_get() {
	    if ( ! $this->isCustomer() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
			
	    $this->set_response(
		    [
			    'status' => true,
			    'data'   => $this->getCurrentUser(),
		    ],
		    REST_Controller::HTTP_OK
	    );
    }
    
    public function index_post() {
	    if ( ! $this->isCustomer() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
	    $this->form_validation->set_data( $this->post() );
		
	    $this->form_validation->set_rules( 'id', lang( 'id' ), 'required|integer|greater_than[0]' );
	    $this->form_validation->set_rules( 'first_name', lang( 'first_name' ), 'required' );
	    $this->form_validation->set_rules( 'last_name', lang( 'last_name' ), 'required' );
	    $this->form_validation->set_rules( 'phone', lang( 'phone' ), 'required' );
	    //$this->form_validation->set_rules( 'email', lang( 'email' ), 'required|valid_email' );
	    $this->form_validation->set_rules( 'company', lang( 'company' ), 'trim' );
	    $this->form_validation->set_rules( 'vat_no', lang( 'vat_no' ), 'trim' );
	    $this->form_validation->set_rules( 'address', lang( 'billing_address' ), 'required' );
	    $this->form_validation->set_rules( 'city', lang( 'city' ), 'required' );
	    $this->form_validation->set_rules( 'state', lang( 'state' ), 'required' );
	    $this->form_validation->set_rules( 'postal_code', lang( 'postal_code' ), 'trim' );
	    $this->form_validation->set_rules( 'country', lang( 'country' ), 'required' );
	    
	    if ( $this->form_validation->run() ) {
		    $user    = $this->getCurrentUser();
		    $profile = [
			    'first_name' => $this->post( 'first_name' ),
			    'last_name'  => $this->post( 'last_name' ),
			    'company'    => $this->post( 'company' ),
			    'phone'      => $this->post( 'phone' ),
			    //'email'      => $this->post('email'),
		    ];
		    $billing = [
			    'name'        => $this->post( 'first_name' ) . ' ' . $this->post( 'last_name' ),
			    'phone'       => $this->post( 'phone' ),
			    //'email'       => $this->post('email'),
			    'company'     => $this->post( 'company' ),
			    'vat_no'      => $this->post( 'vat_no' ),
			    'address'     => $this->post( 'address' ),
			    'city'        => $this->post( 'city' ),
			    'state'       => $this->post( 'state' ),
			    'postal_code' => $this->post( 'postal_code' ),
			    'country'     => $this->post( 'country' ),
		    ];
			$profileUpdate = $this->ion_auth->update( $user->id, $profile );
			$billingUpdate = $this->shop_model->updateCompany( $user->company_id, $billing );
		    if ( $profileUpdate && $billingUpdate  ) {
			    $this->set_response( [
				    'status' => true,
				    'data'   => $this->getCurrentUser(),
				    'message' => lang( 'user_updated' ),
			    ] );
		    } else {
		    	$errors = [];
		    	if ( ! $profileUpdate ) {
		    		$errors[] = lang( 'unable_to_update_user' );
			    }
		    	if ( ! $billingUpdate ) {
		    		$errors[] = lang( 'unable_to_update_billing_address' );
			    }
		    	$data = [
				    'status' => false,
				    'error'  => count( $errors ) == 1 ? $errors[0] : $errors,
			    ];
		    	if ( $profileUpdate || $billingUpdate ) {
				    $data['data'] = $this->getCurrentUser();
			    }
			    $this->set_response( $data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR );
		    }
		    
	    } else {
		    $this->response_invalid_form();
	    }
    }
    
    public function password_post() {
	    if ( ! $this->isCustomer() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
	    $this->form_validation->set_data( $this->post() );
	
	    $this->form_validation->set_rules( 'old_password', lang( 'old_password' ), 'required' );
	    $this->form_validation->set_rules( 'new_password', lang( 'new_password' ), 'required|min_length[8]|max_length[25]' );
	    $this->form_validation->set_rules( 'confirm_password', lang( 'confirm_password' ), 'required|matches[new_password]' );
	    
	    if ( $this->form_validation->run() ) {
		    
		    $identity = $this->session->userdata( $this->config->item( 'identity', 'ion_auth' ) );
		    $change   = $this->ion_auth->change_password(
		    	$identity,
			    $this->input->post( 'old_password' ),
			    $this->input->post( 'new_password' )
		    );
		    
		    if ( $change ) {
			    $this->set_response( [
				    'status' => true,
				    'message' => $this->ion_auth_messages( true ),
			    ] );
		    } else {
			    $this->set_response(
			    	[
					    'status' => false,
					    'error'  => $this->ion_auth_errors( true ),
				    ],
				    REST_Controller::HTTP_BAD_REQUEST
			    );
		    }
		    
	    } else {
		    $this->response_invalid_form();
	    }
    }
}
