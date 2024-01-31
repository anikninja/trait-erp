<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

/**
 * Class Products
 *
 */
class Brands extends MY_REST_Controller {
    public function __construct() {
	    parent::__construct();
        $this->methods['index_get']['limit'] = 500;
    }
    
    public function index_get() {
	    $start = $this->get( 'start' ) ? absint( $this->get( 'start' ) ) : 0;
	    $limit = $this->get( 'limit' ) ? absint( $this->get( 'limit' ) ) : 0;
	    $slug  = $this->get( 'slug' ) ? $this->get( 'slug' ) : false;
	    if ( $slug ) {
	    	$brand = $this->shop_model->getBrandBySlug( $slug );
	    	if ( $brand ) {
	    		$this->set_response(
	    			[
	    				'status' => true,
					    'data'   => $brand,
				    ]
			    );
		    } else {
	    		$this->response_404( lang( 'brand_not_found' ) );
		    }
	    } else {
	    	$brands = $this->shop_model->getAllBrands( $limit, $start );
	    	if ( ! empty( $brands ) ) {
			    foreach ( $brands as &$brand ) {
				    if ( $brand->image ) {
					    $brand->image = $this->getThumb( $brand->image );
				    }
			    }
		    }
	    	$this->set_response(
	    		[
				    'status' => true,
				    'data'   => $brands,
				    'limit'  => $limit,
				    'start'  => $start,
				    'total'  => $this->shop_model->getAllBrands( true ),
			    ]
		    );
	    }
    }
}
