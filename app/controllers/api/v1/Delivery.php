<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

/**
 * Class Products
 *
 * @property Delivery_api $delivery_api
 * @property CI_Session $session
 *
 */
class Delivery extends MY_REST_Controller {
	/**
	 * @var int
	 */
	protected $dm;
    public function __construct() {
	    parent::__construct();
	    $this->methods['index_get']['limit'] = 500;
	    $this->load->api_model('delivery_api');
	    if( $this->isUserLoggedIn() ){
		    $this->dm = $this->loggedInUser->id;
	    }

    }
    
    public function index_get() {
    	if ( 'index' !== $this->v ) {
    		$this->response_404();
    		return;
	    }
    	if ( ! $this->isStaff() ) {
		    $this->response_user_unauthorized();
    		return;
	    }
    	if ( ! $this->checkPermission( 'deliveries', 'sales' ) ){
    	    $this->response_permission_denied();
    	    return;
	    }
	    $filters = [
		    'delivered_by'      => $this->dm,
		    'status'            => $this->get( 'status', true ) ? $this->get( 'status', true ) : '',
		    'do_reference_no'   => $this->get( 'code', true ) ? $this->get( 'code', true ) : '',
		    'sale_id'           => $this->get( 'sale_id', true ) ? $this->get( 'sale_id', true ) : '',
		    'sale_reference_no' => $this->get( 'sale_ref', true ) ? $this->get( 'sale_ref', true ) : '',
		    'date'              => $this->get( 'date', true ) ? $this->get( 'date', true ) : '',
		    'limit'             => $this->get( 'limit' ) ? absint( $this->get( 'limit' ) ) : 10,
		    'start'             => $this->get( 'start' ) ? absint( $this->get( 'start' ) ) : 0,
	    ];
	    $deliveries = $this->delivery_api->getDeliveries( $filters );
	    $this->set_response(
		    [
			    'status' => true,
			    'data'   => $deliveries,
			    'limit'  => $filters['limit'],
			    'start'  => $filters['start'],
			    'count'  => $this->delivery_api->getDeliveryCount( $filters ),
		    ],
		    REST_Controller::HTTP_OK
	    );
    }
    
    public function add_post() {
	    if ( ! $this->isStaff() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
	    if ( ! $this->checkPermission( 'add_delivery',  'sales' ) ){
		    $this->response_permission_denied();
		    return;
	    }
	    $this->form_validation->set_data( $this->post() );
	    $this->form_validation->set_rules( 'code', lang( 'code' ), 'trim|required' );
	    if ( $this->form_validation->run() ) {
	    	$delivery = $this->delivery_api->getDeliveries( [
			    'status'          => 'pending',
			    'do_reference_no' => $this->post( 'code', true ),
		    ], true );
	    	if ( $delivery ) {
			    $this->set_response(
				    [
					    'status'  => true,
					    'data'    => $delivery,
				    ]
			    );
		    } else {
	    		$this->response_404( sprintf( lang( 'x_not_found' ), lang( 'delivery' ) ), 'NOT_FOUND' );
		    }
	    } else {
		    $this->response_invalid_form();
	    }
    }
    
    public function accept_post() {
	    if ( ! $this->isStaff() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
	    if ( ! $this->checkPermission( 'edit_delivery', 'sales' ) ){
		    $this->response_permission_denied();
		    return;
	    }
	    $this->form_validation->set_data( $this->post() );
	    $this->form_validation->set_rules( 'code', lang( 'code' ), 'trim|required' );
	    if ( $this->form_validation->run() ) {
		    $delivery = $this->delivery_api->getDeliveries( [
			    'status'          => 'pending',
			    'do_reference_no' => $this->post( 'code', true ),
		    ], true );
		    if ( $delivery ) {
			    $update = $this->delivery_api->update_delivery( $delivery->id,
				    [
					    'status'       => 'delivering',
					    'delivered_by' => $this->dm,
				    ]
			    );
			    if ( $update ){
				    $this->set_response(
					    [
						    'status'  => $update,
						    'data'    => $delivery,
					    ]
				    );
			    }
			    else {
				    $this->response_404( sprintf( lang( 'x_not_updated' ), lang( 'delivery' ) ), 'NOT_UPDATED' );
			    }
		    } else {
			    $this->response_404( sprintf( lang( 'x_not_found' ), lang( 'delivery' ) ), 'NOT_FOUND' );
		    }
	    } else {
		    $this->response_invalid_form();
	    }
    }
    
    public function cancel_post() {
	    if ( ! $this->isStaff() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
	    if ( ! $this->checkPermission( 'edit_delivery', 'sales' ) ){
	    	$this->response_permission_denied();
		    return;
	    }
	    $this->form_validation->set_data( $this->post() );
	    $this->form_validation->set_rules( 'code', lang( 'code' ), 'trim|required' );
	    $this->form_validation->set_rules( 'attachment', lang( 'attachment' ), 'xss_clean' );
	    $this->form_validation->set_rules( 'note', lang( 'note' ), 'trim' );
	    if ( $this->form_validation->run() ) {
		    $delivery = $this->delivery_api->getDeliveries( [
			    'do_reference_no' => $this->post( 'code', true ),
			    'delivered_by'    => $this->dm,
		    ], true );
		    $update = $this->delivery_api->update_delivery( $delivery->id, [
		    	'status' => 'canceled',
			    'note'       => $this->post( 'note', true ),
			    //'attachment' => '',
		    ] );

		    $this->set_response(
			    [
				    'status'  => $update,
				    'message' => $update ? sprintf( lang( 'x_canceled'), lang( 'delivery' ) ) : '' ,
				    'error'   => ! $update ? sprintf( lang( 'x_not_updated' ), lang( 'delivery' ) ) : '',
			    ]
		    );
	    } else {
		    $this->response_invalid_form();
	    }
    }
    
	public function update_status_post() {
		if ( ! $this->isStaff() ) {
			$this->response_user_unauthorized();
			return;
		}
		if ( ! $this->checkPermission( 'edit_delivery', 'sales' ) ){
			$this->response_permission_denied();
			return;
		}
		$this->form_validation->set_data( $this->post() );
		$this->form_validation->set_rules( 'code', lang( 'code' ), 'trim|required' );
		$this->form_validation->set_rules( 'status', lang( 'status' ), 'trim|required' );
		$this->form_validation->set_rules( 'attachment', lang( 'attachment' ), 'xss_clean' );
		$this->form_validation->set_rules( 'note', lang( 'note' ), 'trim' );
		if ( $this->form_validation->run() ) {
			$status = $this->post( 'status', true );
			$delivery = $this->delivery_api->getDeliveries( [
				'do_reference_no' => $this->post( 'code', true ),
				'delivered_by'    => $this->dm,
			], true );
			
			if ( ! in_array( $status, [ 'delivering', 'delivered' ] ) || 'delivered' === $delivery->status ) {
				$this->set_response(
					[
						'status' => false,
						'error' => lang( 'invalid_request' ),
					],
					REST_Controller::HTTP_BAD_REQUEST
				);
			}
			$data = [
				'status'     => $status,
				'note'       => $this->post( 'note', true )
			];
			// Upload attachment
			if ($_FILES['attachment']['size'] > 0) {
				$this->load->library('upload');
				$config['upload_path']   = $this->digital_upload_path;
				$config['allowed_types'] = $this->digital_file_types;
				$config['max_size']      = $this->allowed_file_size;
				$config['overwrite']     = false;
				$config['encrypt_name']  = true;
				$this->upload->initialize($config);
				if (!$this->upload->do_upload('attachment')) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					redirect($_SERVER['HTTP_REFERER']);
				}
				$data['attachment'] = $this->upload->file_name;
			}
			$update = $this->delivery_api->update_delivery( $delivery->id, $data );

			$this->set_response(
				[
					'status'  => $update,
					'message' => $update ? sprintf( lang( 'x_updated' ), lang( 'delivery' ) ) : '',
					'error'   => ! $update ? sprintf( lang( 'x_not_updated' ), lang( 'delivery' ) ) : '',
				]
			);

		} else {
			$this->response_invalid_form();
		}
	}
    
    public function delete_post() {
	    $this->not_implemented();
    }
	
	/**
	 * Prepare data.
	 * @param object $delivery
	 *
	 * @return object
	 */
    protected function prepare_data( $delivery ) {
	
	    if ( ! isset( $delivery->qr_code ) ) {
		    $delivery->qr_code = '';
	    }
    	
    	return $delivery;
    }
}
