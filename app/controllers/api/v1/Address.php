<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

/**
 * Class Products
 *
 * @property Ion_auth|Auth_model $ion_auth
 * @property CI_Session $session
 *
 */
class Address extends MY_REST_Controller {
    public function __construct() {
	    parent::__construct();
	    $this->methods['index_get']['limit'] = 500;
    }
    
    public function index_get() {
    	if ( 'index' !== $this->v ) {
    		$this->response_404();
    		return;
	    }
    	if ( ! $this->isCustomer() ) {
    		$this->response_user_unauthorized();
    		return;
	    }
	
	    $this->set_response(
		    [
			    'status' => true,
			    'count'  => $this->shop_model->countAddresses(),
			    'data'   => $this->shop_model->getAddresses(),
		    ],
		    REST_Controller::HTTP_OK
	    );
    }
    
    public function create_post() {
    	if ( ! $this->isCustomer() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
    	if ( $this->shop_model->countAddresses() >= 6 ) {
    		$this->set_response(
    			[
				    'status' => false,
				    'error'  => lang( 'already_have_max_addresses' ),
			    ],
			    REST_Controller::HTTP_TOO_MANY_REQUESTS
		    );
	    }
	    $this->setup_validator();
    	
    	if ( $this->form_validation->run() ) {
		    $data = $this->addressDbData();
		    $zone = false;
		    $area = false;
		    
		    if ( $data['zone'] ) {
		    	$zone = new Erp_Shipping_Zone( $data['zone'] );
		    }
		
		    if ( $data['area'] ) {
			    $area = new Erp_Shipping_Zone_Area( $data['area'] );
		    }
		    
		    if ( $zone && $area ) {
		    	if ( ! $zone->getId() || ! $area->getId() || $area->getZoneId() != $zone->getId() || ! $zone->getIsEnabled() || ! $area->getIsEnabled() ) {
				    $this->set_response(
					    [
						    'status' => false,
						    'error' => lang( 'invalid_address' ),
					    ],
					    REST_Controller::HTTP_BAD_REQUEST
				    );
				    return;
			    }
		    }
		    
		    $this->db->insert( 'addresses', $data );
		    $data['id'] = $this->db->insert_id();
		    
		    if ( $data['area'] ) {
			    $area = new Erp_Shipping_Zone_Area( $data['area'] );
			    $data['area_name'] = $area->getName();
		    }
		    
		    $this->set_response(
		    	[
				    'status'  => true,
				    'data'    => $data,
				    'message' => lang( 'address_added' ),
			    ]
		    );
	    } else {
    		$this->response_invalid_form();
	    }
    }
    
    public function update_post() {
    	if ( ! $this->isCustomer() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
    	
	    $this->setup_validator( true );
    	
    	if ( $this->form_validation->run() ) {
		    $id      = $this->post( 'id' );
		    $address = $this->shop_model->getAddressByID( $id );
		    if ( ! $address ) {
			    $this->response_404( lang( 'address_not_found' ), lang( 'nothing_found' ) );
			    return;
		    }
		    
		    // check if this address belongs to current user.
		    $cid = $this->session->userdata( 'company_id' );
		    if ( $address->company_id != $cid ) {
		    	$this->response_user_no_permission();
		    	return;
		    }
		    
		    $data = $this->addressDbData();
		    $this->db->update( 'addresses', $data, [ 'id' => $id ] );
		    $data['id'] = $id;
		    
		    if ( $data['area'] ) {
			    $area = new Erp_Shipping_Zone_Area( $data['area'] );
			    $data['area_name'] = $area->getName();
		    }
		    
		    $this->set_response(
		    	[
				    'status'  => true,
				    'data'    => $data,
				    'message' => lang( 'address_updated' ),
			    ]
		    );
	    } else {
    		$this->response_invalid_form();
	    }
    }
    
    public function delete_post() {
	    if ( ! $this->isCustomer() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
	
	    $this->form_validation->set_data( $this->post() );
	    $this->form_validation->set_rules( 'id', lang( 'id' ), 'required|numeric' );
    	
	    if ( $this->form_validation->run() ) {
		    $address = $this->shop_model->getUserAddressByID( $this->post( 'id' ) );
		    if ( ! $address ) {
			    $this->response_404( lang( 'address_not_found' ), lang( 'nothing_found' ) );
			    return;
		    }
		    $deleted = $this->shop_model->deleteAddress( $address->id );
		    $this->set_response(
		    	[
				    'status' => $deleted,
				    'message' => $deleted ? lang( 'address_deleted' ) : lang( 'unable_to_delete_address' )
			    ]
		    );
	    } else {
		    $this->response_invalid_form();
	    }
    }
    
    public function shipping_countries_get() {
	    $countries = $this->shop_model->getShippingCountries(true );
	    if ( empty( $countries ) ) {
		    $countries = ci_get_countries();
	    }
    	$this->set_response( [
    		'status' => true,
		    'data'   => $countries,
	    ] );
    }
    
    public function shipping_states_get() {
	    $cc = $this->get( 'country' );
	    $countries = $this->shop_model->getShippingCountries(true );
	    if ( ! empty( $countries ) ) {
		    $output = $this->shop_model->getShippingStates( $cc, true );
	    } else {
		    $output = ci_get_states( $cc );
	    }
	    $states = [];
	    foreach ($output as $k=>$v) {
		    $states[] = ['id'=>$k, 'name'=>$v];
	    }
	    $this->set_response( [
		    'status' => true,
		    'data'   => $states,
	    ] );
    }
    
    public function shipping_cities_get() {
	    $cc = $this->get( 'country' );
	    $sc = $this->get( 'state' );
	    $this->set_response( [
	    	'status' => true,
		    'data'   => $this->shop_model->getShippingCities( $cc, $sc )
	    ] );
    }
    
    public function search_zone_area_get() {
    	$data = $this->get();
    	if ( empty( $data ) ) {
    		// setting empty data will force form validator to use the $_POST data.
    		$data = [ 'xxx' => 'xxx' ];
	    }
    	
	    $this->form_validation->set_data( $data );
	    
	    $this->form_validation->set_rules( 'country', lang( 'country' ), 'trim|required' );
	    $this->form_validation->set_rules( 'state', lang( 'state' ), 'trim|required' );
	    $this->form_validation->set_rules( 'city', lang( 'city' ), 'trim|required' );
	    $this->form_validation->set_rules( 'postal_code', lang( 'postal_code' ), 'trim' );
	    if ( $this->form_validation->run() ) {
		    $cc   = $this->get( 'country' );
		    $sc   = $this->get( 'state' );
		    $city = $this->get( 'city' );
		    $zip  = $this->get( 'postal_code' );
		    $zone = $this->shop_model->getShippingZones( $cc, $sc, $city, $zip, 'id' );
		    $area = $this->shop_model->getShippingAreas( $cc, $sc, $city, $zip );
		    
		    $this->set_response(
			    [
				    'status' => true,
				    'data'   => [
					    'zone' => $zone ? $zone[0]->id : '',
					    'area' => $area,
				    ],
			    ]
		    );
	    } else {
		    $this->set_response(
			    [
				    'status' => false,
				    'error' => $this->validation_errors(),
			    ],
			    REST_Controller::HTTP_BAD_REQUEST
		    );
	    }
	    return;
    }
    
    protected function addressDbData() {
    	return [
		    'title'       => $this->post( 'title' ),
		    'line1'       => $this->post( 'line1' ),
		    'line2'       => $this->post( 'line2' ),
		    'phone'       => $this->post( 'phone' ),
		    'city'        => $this->post( 'city' ),
		    'state'       => $this->post( 'state' ),
		    'postal_code' => $this->post( 'postal_code' ),
		    'country'     => $this->post( 'country' ),
		    'area'        => $this->post( 'area' ),
		    'zone'        => $this->post( 'zone' ),
		    'company_id'  => $this->session->userdata( 'company_id' ),
	    ];
    }
    
    protected function setup_validator( $update = false ) {
	    $this->form_validation->set_data( $this->post() );
	    if ( $update ) {
		    $this->form_validation->set_rules( 'id', lang( 'id' ), 'required|numeric' );
	    }
	    $this->form_validation->set_rules( 'title', lang( 'title' ), 'trim|required' );
	    $this->form_validation->set_rules( 'line1', lang( 'line1' ), 'trim|required' );
	    $this->form_validation->set_rules( 'line2', lang( "line2" ), 'trim' );
	    $this->form_validation->set_rules( 'country', lang( 'country' ), 'trim|required' );
	    $this->form_validation->set_rules( 'state', lang( 'state' ), 'trim|required' );
	    $this->form_validation->set_rules( 'city', lang( 'city' ), 'trim|required' );
	    $this->form_validation->set_rules( 'postal_code', lang( "postal_code" ), 'trim' );
	    $this->form_validation->set_rules( 'phone', lang( 'phone' ), 'trim|required' );
	    $this->form_validation->set_rules( 'zone', lang( 'zone' ), 'trim|numeric' );
	    $this->form_validation->set_rules( 'area', lang( 'area' ), 'trim|numeric' );
    }
}
