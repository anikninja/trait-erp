<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

/**
 * Class Products
 *
 * @property Products_api $products_api
 */
class Categories extends MY_REST_Controller {
    public function __construct() {
	    parent::__construct();
        $this->methods['index_get']['limit'] = 500;
	    $this->load->api_model( 'products_api' );
    }
    
	public function index_get() {
		$parentOnly   = absint( $this->get( 'parent' ) ) > 0;
		$featuredOnly = absint( $this->get( 'featured' ) ) > 0;
		$includeSubs  = absint( $this->get( 'include_sub' ) ) > 0;
		$id           = $this->get( 'id' ) ? absint( $this->get( 'id' ) ) : false;
		$data         = false;
		if ( $id ) {
			$data = $this->products_api->get_category( $id, $includeSubs, $featuredOnly );
		} else {
			$data = $this->products_api->get_category_tree( $featuredOnly, $parentOnly );
		}
		
		if ( $data ) {
			$this->set_response( $data, REST_Controller::HTTP_OK );
		} else {
			$this->response_404( lang(  $id ? 'requested_404' : 'nothing_found' ), 'NOT_FOUND' );
		}
	}
	
	public function promo_get() {
		$today = date( 'Y-m-d' );
		$this->db
			->select( 'category_id' )
			->where( 'promotion', '1' )
			->where( 'start_date <=', $today )
			->where( 'end_date >=', $today );
		$prods = $this->db->get( 'products' )->result();
		$cats = [];
		if ( ! empty( $prods ) ) {
			$catIds = array_column( $prods, 'category_id' );
			if ( ! empty( $catIds ) ) {
				$this->db->where_in( 'id', $catIds );
				$cats = $this->db->get( 'categories' )->result();
				if ( $this->get('include_products' ) ) {
					foreach ( $cats as &$cat ) {
						$products = $this->shop_model->getProducts( [ 'category' => [ 'id' => $cat->id ], 'promo' => true ] );
						if ( ! empty( $products ) ) {
							$products = array_map( [ $this, 'setup_product_data' ], $products );
						}
						$cat->products = $products;
					}
				}
			}
		}
		$this->set_response( $cats, REST_Controller::HTTP_OK );
	}
}
