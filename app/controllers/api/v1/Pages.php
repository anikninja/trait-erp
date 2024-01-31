<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

/**
 * Class Products
 *
 * @property Pages_api $pages_api
 */
class Pages extends MY_REST_Controller {
    public function __construct() {
	    parent::__construct();
        $this->methods['index_get']['limit'] = 500;
	    $this->load->api_model( 'pages_api' );
    }
	
	public function index_get() {
		$slug = $this->get('slug');
    	if ( ! $slug ) {
    		$filters = [
			    'query'    => $this->get( 'query' ) ? $this->get( 'query' ) : null,
			    'start'    => $this->get( 'start' ) && is_numeric( $this->get( 'start' ) ) ? $this->get( 'start' ) : 1,
			    'limit'    => $this->get( 'limit' ) && is_numeric( $this->get( 'limit' ) ) ? $this->get( 'limit' ) : 10,
			    'order_by' => $this->get( 'order_by' ) ? explode( ',', $this->get( 'order_by' ) ) : [ 'order_no', 'acs' ],
		    ];
		    $pages = $this->pages_api->getPages( $filters );
		    $this->response( [
			    'status' => true,
			    'data'   => $pages,
			    'limit'  => $filters['limit'],
			    'start'  => $filters['start'],
			    'total'  => $this->pages_api->countPages( $filters ),
		    ], REST_Controller::HTTP_OK );
	    } else {
    		$page = $this->pages_api->getPageBySlug( $slug );
    		if ( $page ) {
			    $this->set_response(
				    [
					    'status' => true,
					    'data'   => $page,
				    ],
				    REST_Controller::HTTP_OK
			    );
		    } else {
			    $this->set_response(
				    [
					    'status' => false,
					    'error'  => lang( 'requested_404' ),
				    ],
				    REST_Controller::HTTP_NOT_FOUND
			    );
		    }
	    }
	}
}
