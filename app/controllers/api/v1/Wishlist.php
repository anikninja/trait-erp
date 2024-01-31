<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

/**
 * Class Products
 *
 */
class Wishlist extends MY_REST_Controller {
    public function __construct() {
	    parent::__construct();
        $this->methods['index_get']['limit'] = 500;
    }
    
    public function index_get() {
	    if ( ! $this->isCustomer() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
	    $limit = $this->get( 'limit' ) ? absint( $this->get( 'limit' ) ) : 10;
	    $start = $this->get( 'start' ) ? absint( $this->get( 'start' ) ) : 0;
	    $items = $this->shop_model->getWishlist( false, $limit, $start );
	    $products = [];
	    
	    foreach ( $items as $item ) {
	    	$product = $this->shop_model->getProductByID( $item->product_id );
		    if ( $product ) {
			    $this->setup_product_data( $product );
			    $products[] = $product;
		    } else {
		    	$products[] = (object) [
		    		'id'              => $item->product_id, // wishlist item id so user can remove this.
				    'name'            => lang( 'product_not_found' ),
				    'image'           => $this->Settings->noImage,
				    'saved'           => '',
				    'sale_price'      => '',
				    'regular_price'   => '',
				    'onSale'          => false,
				    'promo'           => false,
				    'details'         => '',
				    'product_details' => '',
				    'stock_status'    => '',
			    ];
		    }
	    }
	    
	    $data = [
		    'status' => true,
		    'data'   => $products,
		    'limit'  => $limit,
		    'start'  => $start,
		    'total'  => $this->shop_model->getWishlist( true ),
	    ];
	    $this->set_response( $data, REST_Controller::HTTP_OK );
    }
    
    public function index_post() {
	    if ( ! $this->isCustomer() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
	    $this->form_validation->set_data( $this->post() );
	
	    $this->form_validation->set_rules( 'id', lang( 'product_id' ), 'required|integer|greater_than[0]' );
	
	    if ( $this->form_validation->run() ) {
		    $product = absint( $this->post( 'id' ) );
		    $product = $product ? $this->shop_model->getProductByID( $product ) : false;
		    if ( ! $product ) {
			    $this->set_response(
				    [
					    'status' => false,
					    'error' => lang( 'product_not_found' ),
				    ],
				    REST_Controller::HTTP_BAD_REQUEST
			    );
			    return;
		    }
		    if ( $this->shop_model->addWishlist( $product->id ) ) {
			    $data = [
				    'status'  => true,
				    'message' => lang( 'added_wishlist' ),
			    ];
		    } else {
			    $data = [
				    'status'  => false,
				    'message' => lang( 'product_exists_in_wishlist' ),
			    ];
		    }
		    $this->set_response( $data, REST_Controller::HTTP_OK );
	    } else {
		    $this->response_invalid_form();
	    }
    }
    
    public function index_delete() {
    	$this->remove_post();
    }
    
    public function remove_post() {
	    if ( ! $this->isCustomer() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
	    $this->form_validation->set_data( $this->post() );
	    
	    $this->form_validation->set_rules( 'id', lang( 'product_id' ), 'required|integer|greater_than[0]' );
	    
	    if ( $this->form_validation->run() ) {
		    $product = absint( $this->post( 'id' ) );
		    if ( $this->shop_model->removeWishlist( $product ) ) {
			    $data = [
				    'status'  => true,
				    'message' => lang( 'removed_wishlist' ),
			    ];
		    } else {
			    $data = [
				    'status'  => false,
				    'message' => lang( 'error_occurred' ),
				    'level'   => 'error',
			    ];
		    }
		    $this->set_response( $data, REST_Controller::HTTP_OK );
	    } else {
		    $this->response_invalid_form();
	    }
    }
}
