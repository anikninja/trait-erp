<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

/**
 * Class Products
 *
 * @property Products_api $products_api
 */
class Products extends MY_REST_Controller {
    public function __construct() {
	    parent::__construct();
        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('products_api');
    }

    protected function __setProduct($product) {
        $product->tax_rate       = $this->products_api->getTaxRateByID($product->tax_rate);
        $product->unit           = $this->products_api->getProductUnit($product->unit);
        $ctax                    = $this->site->calculateTax($product, $product->tax_rate);
        $product->price          = $this->rerp->formatDecimal($product->price);
        $product->net_price      = $this->rerp->formatDecimal($product->tax_method ? $product->price : $product->price - $ctax['amount']);
        $product->unit_price     = $this->rerp->formatDecimal($product->tax_method ? $product->price + $ctax['amount'] : $product->price);
        $product->tax_method     = $product->tax_method ? 'exclusive' : 'inclusive';
        $product->tax_rate->type = $product->tax_rate->type ? 'percentage' : 'fixed';
        $product                 = (array) $product;
        ksort($product);
        return $product;
    }
    
    protected function setProduct( $product, $includes = [] ) {
	    $this->setup_product_data($product );
	    if ( ! empty( $includes ) ) {
		    foreach ( $includes as $include) {
			    if ( $include == 'brand' ) {
				    $product->brand = $this->products_api->getBrandByID( $product->brand );
				    if ( ! empty( $product->brand ) ) {
					    $product->brand->image = $this->getThumb( $product->brand->image );
				    }
			    } elseif ( $include == 'category' ) {
				    $product->category = $this->products_api->getCategoryByID( $product->category );
				    if ( ! empty( $product->category ) ) {
					    $product->category->image = $this->getThumb( $product->category->image );
				    }
			    } elseif ( $include == 'sub_units' ) {
				    $product->sub_units = $this->products_api->getSubUnits( $product->unit );
			    }
		    }
	    }
	    return $product;
    }

    public function index_get() {
        $code = $this->get('code');
        $id   = absint( $this->get('id') );

        $filters = [
            'code'     => $code,
            'id'       => $id,
            'promo'    => $this->get( 'promo' ) ? absint( $this->get( 'promo' ) ) > 1 : false,
            'include'  => $this->get( 'include' ) ? explode( ',', $this->get( 'include' ) ) : [],
            'brand'    => $this->get( 'brand' ) ? $this->get( 'brand' ) : false,
            'category' => $this->get( 'category' ) ? $this->get( 'category' ) : false,
            'query'    => $this->get( 'query' ) ? $this->get( 'query' ) : false,
            'start'    => $this->get( 'start' ) && is_numeric( $this->get( 'start' ) ) ? absint( $this->get( 'start' ) ) : 0,
            'limit'    => $this->get( 'limit' ) && is_numeric( $this->get( 'limit' ) ) ? absint( $this->get( 'limit' ) ) : 10,
            'order_by' => $this->get( 'order_by' ) ? explode( ',', $this->get( 'order_by' ) ) : [ 'id', 'acs' ],
        ];
		
	    if ( $code === null || ! $id ) {
		    if ( $products = $this->products_api->getProducts( $filters ) ) {
                $pr_data = [];
	            foreach ( $products as $product ) {
		            $pr_data[] = $this->setProduct( $product, $filters['include'] );
                }
			
			    $data = [
				    'data'  => $pr_data,
				    'limit' => $filters['limit'],
				    'start' => $filters['start'],
				    'total' => $this->products_api->countProducts( $filters ),
			    ];
			    $this->response( $data, REST_Controller::HTTP_OK );
            } else {
			    $this->response( [
				    'message' => lang( 'no_product_found' ),
				    'status'  => false,
			    ],
				    REST_Controller::HTTP_NOT_FOUND
			    );
            }
        } else {
		    if ( $product = $this->products_api->getProduct( $filters ) ) {
			    $product = $this->setProduct( $product, $filters['include'] );
			    $this->set_response( $product, REST_Controller::HTTP_OK );
            } else {
			    $this->set_response(
				    [
					    'message' => 'Product could not be found for code ' . $code . '.',
					    'status'  => false,
				    ],
				    REST_Controller::HTTP_NOT_FOUND
			    );
            }
        }
    }
    
    public function daily_deal_get() {
    	// get product which will expired today.
    }
    
    public function categories_get( $id = null ) {
	    $parentOnly   = ! ! $this->get( 'parent' );
	    $featuredOnly = ! ! $this->get( 'featured' );
	    $data = false;
	    if ( ! is_null( $id ) ) {
		    $id = absint( $id );
		    if ( $id ) {
		        $data = $this->products_api->get_category( absint( $id ), ! ! $this->get( 'include_sub' ), $featuredOnly );
		    }
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
	
	    $filters = [
		    'date'     => $this->get( 'date', true ) ? $this->get( 'date', true ) : false,
		    'brand'    => $this->get( 'brand' ) ? $this->get( 'brand' ) : false,
		    'category' => $this->get( 'category' ) ? $this->get( 'category' ) : false,
		    'include'  => $this->get( 'include' ) ? explode( ',', $this->get( 'include' ) ) : [],
		    'start'    => $this->get( 'start' ) && is_numeric( $this->get( 'start' ) ) ? absint( $this->get( 'start' ) ) : 0,
		    'limit'    => $this->get( 'limit' ) && is_numeric( $this->get( 'limit' ) ) ? absint( $this->get( 'limit' ) ) : 10,
		    'order_by' => $this->get( 'order_by' ) ? explode( ',', $this->get( 'order_by' ) ) : [ 'id', 'acs' ],
	    ];
	    
	    if ( $products = $this->products_api->getPromoProducts( $filters ) ) {
		    $pr_data = [];
		    foreach ( $products as $product ) {
			    $pr_data[] = $this->setProduct( $product, $filters['include'] );
		    }
		
		    $data = [
			    'data'  => $pr_data,
			    'limit' => $filters['limit'],
			    'start' => $filters['start'],
			    'total' => $this->products_api->getPromoProductCount( $filters ),
		    ];
		    $this->response( $data, REST_Controller::HTTP_OK );
	    } else {
		    $this->response( [
			    'message' => lang( 'no_product_found' ),
			    'status'  => false,
		    ],
			    REST_Controller::HTTP_NOT_FOUND
		    );
	    }
    }
    
    public function trending_get() {
	
	    $filters = [
		    'brand'    => $this->get( 'brand' ) ? $this->get( 'brand' ) : false,
		    'category' => $this->get( 'category' ) ? $this->get( 'category' ) : false,
		    'include'  => $this->get( 'include' ) ? explode( ',', $this->get( 'include' ) ) : [],
		    'start'    => $this->get( 'start' ) && is_numeric( $this->get( 'start' ) ) ? absint( $this->get( 'start' ) ) : 0,
		    'limit'    => $this->get( 'limit' ) && is_numeric( $this->get( 'limit' ) ) ? absint( $this->get( 'limit' ) ) : 10,
		    'order_by' => $this->get( 'order_by' ) ? explode( ',', $this->get( 'order_by' ) ) : [ 'id', 'acs' ],
	    ];
	    
	    if ( $products = $this->products_api->getTrendingProducts( $filters ) ) {
		    $pr_data = [];
		    foreach ( $products as $product ) {
			    $pr_data[] = $this->setProduct( $product, $filters['include'] );
		    }
		
		    $data = [
			    'data'  => $pr_data,
			    'limit' => $filters['limit'],
			    'start' => $filters['start'],
			    'total' => $this->products_api->getTrendingProductCount( $filters ),
		    ];
		    $this->response( $data, REST_Controller::HTTP_OK );
	    } else {
		    $this->response( [
			    'message' => lang( 'no_product_found' ),
			    'status'  => false,
		    ],
			    REST_Controller::HTTP_NOT_FOUND
		    );
	    }
    }
}
