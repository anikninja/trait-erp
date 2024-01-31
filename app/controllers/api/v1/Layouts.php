<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

class Layouts extends MY_REST_Controller {
	
	public function __construct() {
		parent::__construct();

		$this->methods['index_get']['limit'] = 500;
		$this->load->api_model('products_api');
	}

	public function index_get() {
		$sliders = json_decode( $this->shop_settings->slider );
		$sliders = array_map( 'prepare_slide_data', $sliders );
		$data = [
			'sliders' => array_filter( $sliders ),
		];
		
		$this->set_response( $data, REST_Controller::HTTP_OK );
	}
}
