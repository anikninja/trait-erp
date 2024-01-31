<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

/**
 * Class Coupons
 * @property Products_model $products_model
 */
class Coupons extends MY_Controller {
	public function __construct() {
		parent::__construct();

		if ( ! $this->loggedIn ) {
			$this->session->set_userdata( 'requested_page', $this->uri->uri_string() );
			$this->rerp->md( 'login' );
		}
		if ( $this->Customer || $this->Supplier ) {
			$this->session->set_flashdata( 'warning', lang( 'access_denied' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$this->load->library( 'form_validation' );
		$this->lang->admin_load( 'sales', $this->Settings->user_language );
		$this->load->admin_model( 'sales_model' );
		$this->load->admin_model( 'products_model' );
		$this->load->model( 'shop_model' );
		$this->load->admin_model('companies_model');

		$this->data['logo'] = TRUE;
	}

	public function index() {

	}

	public function list() {
		$this->rerp->checkPermissions();

		$data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$bc = [
			[ 'link' => base_url(), 'page' => lang( 'home' ) ],
			[ 'link' => admin_url( 'coupons' ), 'page' => lang( 'coupons' ) ],
			[ 'link' => '#', 'page' => lang( 'coupon_list' ) ]
		];
		$meta = [ 'page_title' => lang( 'coupon_list' ), 'bc' => $bc ];
		$this->page_construct( 'coupons/coupon_list', $meta, $this->data );
	}

	public function getCouponList() {
		$this->rerp->checkPermissions( 'deliveries' );

		$edit_link   = anchor( 'admin/coupons/edit_coupon/$1', '<i class="fa fa-edit"></i> ' . lang( 'edit_coupon' ), 'data-toggle="modal" data-target="#myModal"' );
		$usage_link   = anchor( 'admin/coupons/edit_coupon_usage/$1', '<i class="fa fa-edit"></i> ' . lang( 'coupon_usage_settings' ), 'data-toggle="modal" data-target="#myModal"' );
		$delete_link = "<a href='#' class='po' title='<b>" . lang( 'delete_coupon' ) . "</b>' data-content=\"<p>"
		               . lang( 'r_u_sure' ) . "</p><a class='btn btn-danger po-delete' href='" . admin_url( 'coupons/delete_coupon/$1' ) . "'>"
		               . lang( 'i_m_sure' ) . "</a> <button class='btn po-close'>" . lang( 'no' ) . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
		               . sprintf( lang( 'delete_x' ), lang( 'coupon' ) ) . '</a>';
		$action      = '<div class="text-center"><div class="btn-group text-left">'
		               . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
		               . lang( 'actions' ) . ' <span class="caret"></span></button>
						    <ul class="dropdown-menu pull-right" role="menu">
						        <li>' . $edit_link . '</li>
						        <li>' . $usage_link . '</li>
						        <li class="divider"></li>
						        <li>' . $delete_link . '</li>
						    </ul>
						</div></div>';

		$this->load->library( 'datatables' );
		$this->datatables
			->select( 'id, coupon_code, coupon_type, coupon_amount, description, start_date, end_date, status', FALSE )
			->from( 'coupons' )
			->where( 'delete_flag', 0 )
			->add_column( 'Actions', $action, 'id' );


		echo $this->datatables->generate();
	}

	public function add_coupon() {
		if ( $this->input->get( 'id' ) ) {
			$this->data['id'] = $this->input->get( 'id' );
		}

		$my_retail_model = new MY_RetailErp_Model();

		$date = NULL;
		if ( $this->Owner || $this->Admin ) {
			$date = $this->rerp->fld( date( 'Y-m-d H:i:s' ) );
		} else {
			$date = date( 'Y-m-d H:i:s' );
		}

		$this->form_validation->set_rules( 'coupon_code', lang( 'coupon_code' ), 'required|is_unique[coupons.coupon_code]' );
		$this->form_validation->set_rules( 'coupon_type', lang( 'coupon_type' ), 'required' );
		$this->form_validation->set_rules( 'coupon_amount', lang( 'coupon_amount' ), 'required|greater_than[0]' );
		$this->form_validation->set_rules( 'start_date', lang( 'start_date' ), 'required' );
		$this->form_validation->set_rules( 'end_date', lang( 'end_date' ), 'required' );
		$this->form_validation->set_rules( 'status', lang( 'status' ), 'required' );


		if ( $this->form_validation->run() == TRUE ) {

			$coupon = new Erp_Coupon();
			$coupon->setCouponCode( $this->input->post( 'coupon_code' ) );
			$coupon->setCouponType( $this->input->post( 'coupon_type' ) );
			$coupon->setCouponAmount( $this->input->post( 'coupon_amount' ) );
			$coupon->setDescription( $this->input->post( 'description' ) );
			$coupon->setStartDate( $this->rerp->fld( trim( $this->input->post( 'start_date' ) ) ) );
			$coupon->setEndDate( $this->rerp->fld( trim( $this->input->post( 'end_date' ) ) ) );
			$coupon->setStatus( $this->input->post( 'status' ) );

			if ( $coupon->save() ) {
				$this->session->set_flashdata( 'message', 'Coupon Created Successfully' );
				admin_redirect( 'coupons/list' );
			}
		} elseif ( $this->input->post( 'create_coupon' ) ) {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$this->data['date']        = $date;
		$this->data['coupon_code'] = $my_retail_model->get_sequential_reference( 'COUPON' );

		$this->data['error']    = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$this->data['modal_js'] = $this->site->modal_js();
		$this->load->view( $this->theme . 'coupons/add_coupon', $this->data );

	}

	public function productSuggestions($term = null, $limit = null)
	{
		if ($this->input->get('term')) {
			$term = $this->input->get('term', true);
		}
		$term            = addslashes($term);
		$limit           = $this->input->get('limit', true);
		$rows = $this->products_model->getQASuggestions($term, $limit);

		$results = [];

		if ( $rows ) {
			foreach ( $rows as $row ){
				$results[] = [
					'id' => $row->id,
					'text' => $row->name . ' (' . $row->code . ')',
				];
			}
		}

		$this->rerp->send_json($results);
	}

	public function categorySuggestions($term = null, $limit = null)
	{
		if ($this->input->get('term')) {
			$term = $this->input->get('term', true);
		}
		$term            = addslashes($term);
		$limit           = $this->input->get('limit', true);
		$rows = $this->products_model->getCategorySuggestions($term, $limit);

		$results = [];
		foreach ( $rows as $row ){
			$results[] = [
				'id' => $row->id,
				'text' => $row->name . ' (' . $row->code . ')',
			];
		}

		$this->rerp->send_json($results);
	}

	public function emailSuggestions( $term = null, $limit = null ) {
		// $this->rerp->checkPermissions('index');
		if ($this->input->get('term')) {
			$term = $this->input->get('term', true);
		}

		$term  = addslashes( $term );
		$limit = $this->input->get( 'limit', true );
		$rows  = $this->companies_model->getCustomerEmailSuggestions( $term, $limit );

		$results = [];
		foreach ( $rows as $row ){
			$results[] = [
				'id' => $row->email,
				'text' => $row->name . ' (' . $row->email . ')',
			];
		}

		$this->rerp->send_json($results);
	}

	public function edit_coupon( $id ) {
		if ( ! $id ) {
			$this->session->set_flashdata( 'error', 'Nothing Selected' );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		if ( $this->input->get( 'id' ) ) {
			$this->data['id'] = $this->input->get( 'id' );
		}

		$date = NULL;
		if ( $this->Owner || $this->Admin ) {
			$date = $this->rerp->fld( date( 'Y-m-d H:i:s' ) );
		} else {
			$date = date( 'Y-m-d H:i:s' );
		}

		$coupon               = new Erp_Coupon( $id );
		$this->data['coupon'] = $coupon;

		if ( $coupon->coupon_code != $this->input->post( 'coupon_code' ) ) {
			$this->form_validation->set_rules( 'coupon_code', lang( 'coupon_code' ), 'required|is_unique[coupons.coupon_code]' );
		}
		$this->form_validation->set_rules( 'coupon_type', lang( 'coupon_type' ), 'required' );
		$this->form_validation->set_rules( 'coupon_amount', lang( 'coupon_amount' ), 'required|greater_than[0]' );
		$this->form_validation->set_rules( 'start_date', lang( 'start_date' ), 'required' );
		$this->form_validation->set_rules( 'end_date', lang( 'end_date' ), 'required' );
		$this->form_validation->set_rules( 'status', lang( 'status' ), 'required' );

		if ( $this->form_validation->run() == TRUE ) {
			$coupon->setCouponCode( $this->input->post( 'coupon_code' ) );
			$coupon->setCouponType( $this->input->post( 'coupon_type' ) );
			$coupon->setCouponAmount( $this->input->post( 'coupon_amount' ) );
			$coupon->setDescription( $this->input->post( 'description' ) );
			$coupon->setStartDate( $this->rerp->fld( trim( $this->input->post( 'start_date' ) ) ) );
			$coupon->setEndDate( $this->rerp->fld( trim( $this->input->post( 'end_date' ) ) ) );
			$coupon->setStatus( $this->input->post( 'status' ) );

			if ( $coupon->save() ) {
				$this->session->set_flashdata( 'message', 'Coupon Updated Successfully' );
				admin_redirect( 'coupons/list' );
			}
		} elseif ( $this->input->post( 'update_coupon' ) ) {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$this->data['error']    = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$this->data['modal_js'] = $this->site->modal_js();
		$this->load->view( $this->theme . 'coupons/edit_coupon', $this->data );

	}

	public function edit_coupon_usage( $id ) {
		if ( ! $id ) {
			$this->session->set_flashdata( 'error', 'Nothing Selected' );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		if ( $this->input->get( 'id' ) ) {
			$this->data['id'] = $this->input->get( 'id' );
		}

		$date = NULL;
		if ( $this->Owner || $this->Admin ) {
			$date = $this->rerp->fld( date( 'Y-m-d H:i:s' ) );
		} else {
			$date = date( 'Y-m-d H:i:s' );
		}

		$coupon               = new Erp_Coupon( $id );
		$this->data['coupon'] = $coupon;

		$this->form_validation->set_rules( 'usage_limit_per_coupon', lang( 'usage_limit_per_coupon' ), 'required|greater_than_equal_to[0]' );

		if ( $this->form_validation->run() == TRUE ) {
			$coupon->setMinimumSpend( $this->input->post( 'minimum_spend' ) );
			$coupon->setMaximumSpend( $this->input->post( 'maximum_spend' ) );
			$coupon->setExcludeSaleItems( $this->input->post( 'exclude_sale_items' ) ? 1 : 0 );
			$coupon->setProducts( $this->input->post( 'products' ) );
			$coupon->setExcludeProducts( $this->input->post( 'exclude_products' ) );
			$coupon->setProductCategories( $this->input->post( 'product_categories' ) );
			$coupon->setExcludeCategories( $this->input->post( 'exclude_categories' ) );
			$coupon->setAllowedEmails( $this->input->post( 'allowed_emails' ) );
			$coupon->setUsageLimitPerCoupon( $this->input->post( 'usage_limit_per_coupon' ) );
			$coupon->setLimitUsageItems( $this->input->post( 'limit_usage_items' ) );
			$coupon->setUsageLimitPerUser( $this->input->post( 'usage_limit_per_user' ) );

			if ( $coupon->save() ) {
				$this->session->set_flashdata( 'message', 'Coupon Updated Successfully' );
				admin_redirect( 'coupons/list' );
			}
		} elseif ( $this->input->post( 'update_coupon' ) ) {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$this->data['error']    = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$this->data['modal_js'] = $this->site->modal_js();
		$this->load->view( $this->theme . 'coupons/coupon_usage_settings', $this->data );
	}

	public function delete_coupon( $id ) {
		$this->rerp->checkPermissions( NULL, TRUE );
		if ( ! $id ) {
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		$coupon = new Erp_Coupon( $id );
		if ( $coupon->getId() ) {
			$coupon->setDeleteFlag( 1 );
			if ( $coupon->save() ) {
				if ( $this->input->is_ajax_request() ) {
					$this->rerp->send_json( [ 'error' => 0, 'msg' => lang( 'coupon_deleted' ) ] );
				}
				$this->session->set_flashdata( 'message', lang( 'coupon_deleted' ) );
				admin_redirect( 'coupons/list' );
			}
		} else {
			$this->session->set_flashdata( 'error', 'Sorry! Invalid Coupon' );
			admin_redirect( 'coupons/list' );
		}
	}

}
