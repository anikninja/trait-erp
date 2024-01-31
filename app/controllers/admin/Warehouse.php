<?php defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

/**
 * Class Warehouse
 *
 * * @property Warehouse_model $warehouse_model
 */
class Warehouse extends MY_Controller {
	public $upload_path;

	public $thumbs_path;

	public $image_types;

	public $allowed_file_size;

	public $digital_file_types;

	public $import_config;

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
		$this->load->admin_model( 'delivery_schedule_model' );
		$this->load->admin_model( 'sales_model' );
		$this->load->admin_model( 'products_model' );
		$this->load->admin_model( 'delivery_model' );
		$this->load->admin_model( 'warehouse_model' );

		$this->load->model( 'Erp_Package' );
		$this->load->model( 'Erp_Package_Items' );
		$this->load->model( 'Erp_Shipment' );


		$this->upload_path        = 'assets/uploads/';
		$this->thumbs_path        = 'assets/uploads/thumbs/';
		$this->image_types        = 'gif|jpg|jpeg|png|tif';
		$this->allowed_file_size  = '1024';
		$this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
		$this->import_config      = [
			'upload_path'   => 'files/',
			'allowed_types' => 'csv',
			'max_size'      => $this->allowed_file_size,
			'overwrite'     => TRUE,
		];

		$this->data['logo'] = TRUE;

	}

	public function index() {

	}

	public function list() {

		$this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );

		$bc   = [
			[ 'link' => base_url(), 'page' => lang( 'home' ) ],
			[ 'link' => admin_url( 'warehouse' ), 'page' => lang( 'warehouse' ) ],
			[ 'link' => '#', 'page' => lang( 'warehouse_list' ) ]
		];
		$meta = [ 'page_title' => lang( 'warehouse_list' ), 'bc' => $bc ];
		$this->page_construct( 'warehouse/warehouse_list', $meta, $this->data );
	}

	public function getWarehouses() {
		$delivery_area_link = anchor( 'admin/warehouse/edit_delivery_area/$1', '<i class="fa fa-file-text-o"></i> ' . lang( 'delivery_area' ) );
		$pickup_area_link = anchor( 'admin/warehouse/edit_pickup_area/$1', '<i class="fa fa-file-text-o"></i> ' . lang( 'pickup_area' ) );
		$edit_link            = anchor( 'admin/warehouse/edit_warehouse/$1', '<i class="fa fa-edit"></i> ' . lang( 'edit_warehouse' ), 'data-toggle="modal" data-target="#myModal"' );
		$delete_link          = "<a href='#' class='po' title='<b>" . lang( 'delete_warehouse' ) . "</b>' data-content=\"<p>"
		                        . lang( 'r_u_sure' ) . "</p><a class='btn btn-danger po-delete' href='" . admin_url( 'warehouse/delete_warehouse/$1' ) . "'>"
		                        . lang( 'i_m_sure' ) . "</a> <button class='btn po-close'>" . lang( 'no' ) . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
		                        . lang( 'delete_warehouse' ) . '</a>';
		$action               = '<div class="text-center"><div class="btn-group text-left">'
		                        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
		                        . lang( 'actions' ) . ' <span class="caret"></span></button>
						    <ul class="dropdown-menu pull-right" role="menu">
						        <li>' . $delivery_area_link . '</li>
						        <li>' . $pickup_area_link . '</li>
						        <li class="divider"></li>
						        <li>' . $edit_link . '</li>
						        <li class="divider"></li>
						        <li>' . $delete_link . '</li>
						    </ul>
						</div></div>';

		$this->load->library( 'datatables' );
		$this->datatables
			->select( "{$this->db->dbprefix('warehouses')}.id as id, map, code, {$this->db->dbprefix('warehouses')}.name as name, {$this->db->dbprefix('price_groups')}.name as price_group, phone, email" )
			->from( 'warehouses' )
			->join( 'price_groups', 'price_groups.id = warehouses.price_group_id', 'left' )
			->where( 'delete_flag', 0 )
			->add_column( 'Actions', $action, 'id' );

		echo $this->datatables->generate();
	}

	public function add_warehouse() {
		$this->load->helper( 'security' );
		$this->form_validation->set_rules( 'code', lang( 'code' ), 'trim|is_unique[warehouses.code]|required' );
		$this->form_validation->set_rules( 'name', lang( 'name' ), 'required' );
		$this->form_validation->set_rules( 'address', lang( 'address' ), 'required' );
		$this->form_validation->set_rules( 'userfile', lang( 'map_image' ), 'xss_clean' );

		if ( $this->form_validation->run() == TRUE ) {
			if ( $_FILES['userfile']['size'] > 0 ) {
				$this->load->library( 'upload' );

				$config['upload_path']   = 'assets/uploads/';
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['max_size']      = $this->allowed_file_size;
				$config['max_width']     = '2000';
				$config['max_height']    = '2000';
				$config['overwrite']     = FALSE;
				$config['encrypt_name']  = TRUE;
				$config['max_filename']  = 25;
				$this->upload->initialize( $config );

				if ( ! $this->upload->do_upload() ) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata( 'message', $error );
					admin_redirect( 'warehouse/list' );
				}

				$map = $this->upload->file_name;

				$this->load->helper( 'file' );
				$this->load->library( 'image_lib' );
				$config['image_library']  = 'gd2';
				$config['source_image']   = 'assets/uploads/' . $map;
				$config['new_image']      = 'assets/uploads/thumbs/' . $map;
				$config['maintain_ratio'] = TRUE;
				$config['width']          = 76;
				$config['height']         = 76;

				$this->image_lib->clear();
				$this->image_lib->initialize( $config );

				if ( ! $this->image_lib->resize() ) {
					echo $this->image_lib->display_errors();
				}
			} else {
				$map = NULL;
			}
			$data = [
				'code'           => $this->input->post( 'code' ),
				'name'           => $this->input->post( 'name' ),
				'phone'          => $this->input->post( 'phone' ),
				'email'          => $this->input->post( 'email' ),
				'address'        => $this->input->post( 'address' ),
				'price_group_id' => $this->input->post( 'price_group' ),
				'map'            => $map,
			];
		} elseif ( $this->input->post( 'add_warehouse' ) ) {
			$this->session->set_flashdata( 'error', validation_errors() );
			admin_redirect( 'warehouse/list' );
		}

		if ( $this->form_validation->run() == TRUE && $this->warehouse_model->addWarehouse( $data ) ) {
			$this->session->set_flashdata( 'message', lang( 'warehouse_added' ) );
			admin_redirect( 'warehouse/list' );
		} else {

			$this->data['zone'] = $this->warehouse_model->getShippingZonesList();
			$this->data['area'] = $this->warehouse_model->getShippingAreaList();

			$this->data['error']        = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
			$this->data['price_groups'] = $this->warehouse_model->getAllPriceGroups();
			$this->data['modal_js']     = $this->site->modal_js();
			$this->load->view( $this->theme . 'warehouse/add_warehouse', $this->data );
		}
	}

	public function edit_warehouse( $id = NULL ) {
		$this->load->helper( 'security' );
		$this->form_validation->set_rules( 'code', lang( 'code' ), 'trim|required' );
		$wh_details = $this->warehouse_model->getWarehouseByID( $id );
		if ( $this->input->post( 'code' ) != $wh_details->code ) {
			$this->form_validation->set_rules( 'code', lang( 'code' ), 'required|is_unique[warehouses.code]' );
		}
		$this->form_validation->set_rules( 'address', lang( 'address' ), 'required' );
		$this->form_validation->set_rules( 'map', lang( 'map_image' ), 'xss_clean' );

		if ( $this->form_validation->run() == TRUE ) {
			$data = [
				'code'           => $this->input->post( 'code' ),
				'name'           => $this->input->post( 'name' ),
				'phone'          => $this->input->post( 'phone' ),
				'email'          => $this->input->post( 'email' ),
				'address'        => $this->input->post( 'address' ),
				'price_group_id' => $this->input->post( 'price_group' ),
			];

			if ( $_FILES['userfile']['size'] > 0 ) {
				$this->load->library( 'upload' );

				$config['upload_path']   = 'assets/uploads/';
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['max_size']      = $this->allowed_file_size;
				$config['max_width']     = '2000';
				$config['max_height']    = '2000';
				$config['overwrite']     = FALSE;
				$config['encrypt_name']  = TRUE;
				$config['max_filename']  = 25;
				$this->upload->initialize( $config );

				if ( ! $this->upload->do_upload() ) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata( 'message', $error );
					admin_redirect( 'warehouse/list' );
				}

				$data['map'] = $this->upload->file_name;

				$this->load->helper( 'file' );
				$this->load->library( 'image_lib' );
				$config['image_library']  = 'gd2';
				$config['source_image']   = 'assets/uploads/' . $data['map'];
				$config['new_image']      = 'assets/uploads/thumbs/' . $data['map'];
				$config['maintain_ratio'] = TRUE;
				$config['width']          = 76;
				$config['height']         = 76;

				$this->image_lib->clear();
				$this->image_lib->initialize( $config );

				if ( ! $this->image_lib->resize() ) {
					echo $this->image_lib->display_errors();
				}
			}
		} elseif ( $this->input->post( 'edit_warehouse' ) ) {
			$this->session->set_flashdata( 'error', validation_errors() );
			admin_redirect( 'warehouse/list' );
		}

		if ( $this->form_validation->run() == TRUE && $this->warehouse_model->updateWarehouse( $id, $data ) ) { //check to see if we are updateing the customer
			$this->session->set_flashdata( 'message', lang( 'warehouse_updated' ) );
			admin_redirect( 'warehouse/list' );
		} else {

			$this->data['zone'] = $this->warehouse_model->getShippingZonesList();
			$this->data['area'] = $this->warehouse_model->getShippingAreaList();

			$this->data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );

			$this->data['warehouse']    = $this->warehouse_model->getWarehouseByID( $id );
			$this->data['price_groups'] = $this->warehouse_model->getAllPriceGroups();
			$this->data['id']           = $id;
			$this->data['modal_js']     = $this->site->modal_js();
			$this->load->view( $this->theme . 'warehouse/edit_warehouse', $this->data );
		}
	}

	public function delete_warehouse( $id = NULL ) {
		if ( $this->warehouse_model->deleteWarehouse( $id ) ) {
			$this->rerp->send_json( [ 'error' => 0, 'msg' => lang( 'warehouse_deleted' ) ] );
		}
	}

	public function edit_delivery_area( $id = null ) {
		if ( $id == NULL ) {
			$this->session->set_flashdata( 'error', lang( 'nothing_found' ) );
			admin_redirect( 'warehouse/list' );
		}

		$this->form_validation->set_rules( 'area', lang( 'area' ), 'required|greater_than[0]' );

		if ( $this->form_validation->run() == TRUE ) {
			$delivery_area = new Erp_Delivery_Area();
			$delivery_area->setAreaId($this->input->post('area'));
			$delivery_area->setWarehouseId($id);
			if ( $delivery_area->save() ) {
				$this->session->set_flashdata( 'message', 'Delivery Area Added Successfully' );
				redirect( $_SERVER['HTTP_REFERER'] );
			}
		} elseif ( $this->input->post( 'add_area' ) ) {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$warehouse = new Warehouse_model();
		$this->data['area_list'] = $warehouse->getUnassignedDeliveryAreas();
		$this->data['warehouse'] = $warehouse->getWarehouseByID($id);

		$bc   = [
			[ 'link' => base_url(), 'page' => lang( 'home' ) ],
			[ 'link' => admin_url( 'warehouse' ), 'page' => lang( 'warehouse' ) ],
			[ 'link' => admin_url( 'warehouse/list' ), 'page' => lang( 'warehouse_list' ) ],
			[ 'link' => '#', 'page' => lang( 'edit_delivery_area' ) ],
		];
		$meta = [ 'page_title' => lang( 'edit_delivery_area' ), 'bc' => $bc ];
		$this->page_construct( 'warehouse/edit_delivery_area', $meta, $this->data );
	}

	public function getDeliveryAreaList( $id ) {
		$this->load->library( 'datatables' );
		$this->datatables
			->select( "{$this->db->dbprefix('delivery_area')}.id as id, {$this->db->dbprefix('shipping_zone_areas')}.name as area_name, {$this->db->dbprefix('shipping_zones')}.name as zone_name" )
			->from( 'delivery_area' )
			->join( 'shipping_zone_areas', 'shipping_zone_areas.id = delivery_area.area_id', 'left' )
			->join( 'shipping_zones', 'shipping_zones.id = shipping_zone_areas.zone_id', 'left' )
			->where( 'delivery_area.warehouse_id', $id )
			->add_column( 'Actions', "<div class=\"text-center\"><a href='#' class='tip po' title='<b>" . lang( 'delete' ) . ' ' . lang('pickup') . ' ' . lang('area') . "</b>' data-content=\"<p>" . lang( 'r_u_sure' ) . "</p><a class='btn btn-danger po-delete' href='" . admin_url( 'warehouse/delete_delivery_area/$1' ) . "'>" . lang( 'i_m_sure' ) . "</a> <button class='btn po-close'>" . lang( 'no' ) . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id' );

		echo $this->datatables->generate();
	}

	public function delete_delivery_area( $id = NULL ) {

		$area = new Erp_Delivery_Area( $id );

		if ( $area->delete() ) {
			$this->rerp->send_json( [ 'error' => 0, 'msg' => lang( 'delivery_area_deleted' ) ] );
		}
	}

	public function edit_pickup_area( $id = null ) {
		if ( $id == NULL ) {
			$this->session->set_flashdata( 'error', lang( 'nothing_found' ) );
			admin_redirect( 'warehouse/list' );
		}

		$this->form_validation->set_rules( 'area', lang( 'area' ), 'required|greater_than[0]' );

		if ( $this->form_validation->run() == TRUE ) {
			$pickuparea = new Erp_Pickup_Area();
			$pickuparea->setAreaId($this->input->post('area'));
			$pickuparea->setWarehouseId($id);
			if ( $pickuparea->save() ) {
				$this->session->set_flashdata( 'message', 'Pickup Area Added Successfully' );
				redirect( $_SERVER['HTTP_REFERER'] );
			}
		} elseif ( $this->input->post( 'add_area' ) ) {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$warehouse = new Warehouse_model();
		$this->data['area_list'] = $warehouse->getUnassignedPickupAreas();
		$this->data['warehouse'] = $warehouse->getWarehouseByID($id);

		$bc   = [
			[ 'link' => base_url(), 'page' => lang( 'home' ) ],
			[ 'link' => admin_url( 'warehouse' ), 'page' => lang( 'warehouse' ) ],
			[ 'link' => admin_url( 'warehouse/list' ), 'page' => lang( 'warehouse_list' ) ],
			[ 'link' => '#', 'page' => lang( 'edit_pickup_area' ) ],
		];
		$meta = [ 'page_title' => lang( 'edit_pickup_area' ), 'bc' => $bc ];
		$this->page_construct( 'warehouse/edit_pickup_area', $meta, $this->data );
	}

	public function getPickupAreaList( $id ) {
		$this->load->library( 'datatables' );
		$this->datatables
			->select( "{$this->db->dbprefix('pickup_area')}.id as id, {$this->db->dbprefix('shipping_zone_areas')}.name as area_name, {$this->db->dbprefix('shipping_zones')}.name as zone_name" )
			->from( 'pickup_area' )
			->join( 'shipping_zone_areas', 'shipping_zone_areas.id = pickup_area.area_id', 'left' )
			->join( 'shipping_zones', 'shipping_zones.id = shipping_zone_areas.zone_id', 'left' )
			->where( 'pickup_area.warehouse_id', $id )
			->add_column( 'Actions', "<div class=\"text-center\"><a href='#' class='tip po' title='<b>" . lang( 'delete' ) . ' ' . lang('pickup') . ' ' . lang('area') . "</b>' data-content=\"<p>" . lang( 'r_u_sure' ) . "</p><a class='btn btn-danger po-delete' href='" . admin_url( 'warehouse/delete_pickup_area/$1' ) . "'>" . lang( 'i_m_sure' ) . "</a> <button class='btn po-close'>" . lang( 'no' ) . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id' );

		echo $this->datatables->generate();
	}

	public function delete_pickup_area( $id = NULL ) {

		$area = new Erp_Pickup_Area( $id );

		if ( $area->delete() ) {
			$this->rerp->send_json( [ 'error' => 0, 'msg' => lang( 'pickup_area_deleted' ) ] );
		}
	}

	public function stock() {

		$this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );

		$bc   = [
			[ 'link' => base_url(), 'page' => lang( 'home' ) ],
			[ 'link' => admin_url( 'warehouse' ), 'page' => lang( 'warehouse' ) ],
			[ 'link' => '#', 'page' => lang( 'product_stock' ) ]
		];
		$meta = [ 'page_title' => lang( 'product_stock' ), 'bc' => $bc ];
		$this->page_construct( 'warehouse/product_stock_list', $meta, $this->data );
	}

	public function getProductStock() {
		$this->load->library( 'datatables' );
		$this->datatables
			->select( "{$this->db->dbprefix('warehouses_products')}.id as id, COALESCE( {$this->db->dbprefix('products')}.code, 'None' ) as product_code, COALESCE( {$this->db->dbprefix('products')}.name, 'None' ) as product_name, {$this->db->dbprefix('warehouses')}.code as warehouse_code, {$this->db->dbprefix('warehouses')}.name as warehouse_name, {$this->db->dbprefix('warehouses_products')}.quantity as product_quantity" )
			->from( 'warehouses_products' )
			->join( 'products', 'products.id = warehouses_products.product_id', 'left' )
			->join( 'warehouses', 'warehouses.id = warehouses_products.warehouse_id', 'left' );

		echo $this->datatables->generate();
	}

	public function stock_adjustments( $warehouse_id = NULL ) {
		$this->rerp->checkPermissions( 'adjustments' );

		if ( $this->Owner || $this->Admin || ! $this->session->userdata( 'warehouse_id' ) ) {
			$this->data['warehouses'] = $this->site->getAllWarehouses();
			$this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID( $warehouse_id ) : NULL;
		} else {
			$this->data['warehouses'] = NULL;
			$this->data['warehouse']  = $this->session->userdata( 'warehouse_id' ) ? $this->site->getWarehouseByID( $this->session->userdata( 'warehouse_id' ) ) : NULL;
		}

		$this->data['error'] = ( validation_errors() ) ? validation_errors() : $this->session->flashdata( 'error' );
		$bc                  = [
			[ 'link' => base_url(), 'page' => lang( 'home' ) ],
			[ 'link' => admin_url( 'warehouse' ), 'page' => lang( 'warehouse' ) ],
			[ 'link' => '#', 'page' => lang( 'stock_adjustments' ) ]
		];
		$meta                = [ 'page_title' => lang( 'quantity_adjustments' ), 'bc' => $bc ];
		$this->page_construct( 'warehouse/stock_quantity_adjustments', $meta, $this->data );
	}

	public function getStockAdjustments( $warehouse_id = NULL ) {
		$this->rerp->checkPermissions( 'adjustments' );

		$delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line( 'delete_adjustment' ) . "</b>' data-content=\"<p>"
		               . lang( 'r_u_sure' ) . "</p><a class='btn btn-danger po-delete' href='" . admin_url( 'warehouse/delete_adjustment/$1' ) . "'>"
		               . lang( 'i_m_sure' ) . "</a> <button class='btn po-close'>" . lang( 'no' ) . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";

		$this->load->library( 'datatables' );
		$this->datatables
			->select( "{$this->db->dbprefix('adjustments')}.id as id, date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, attachment" )
			->from( 'adjustments' )
			->join( 'warehouses', 'warehouses.id=adjustments.warehouse_id', 'left' )
			->join( 'users', 'users.id=adjustments.created_by', 'left' )
			->group_by( 'adjustments.id' );
		if ( $warehouse_id ) {
			$this->datatables->where( 'adjustments.warehouse_id', $warehouse_id );
		}
		$this->datatables->add_column( 'Actions', "<div class='text-center'><a href='" . admin_url( 'warehouse/edit_adjustment/$1' ) . "' class='tip' title='" . lang( 'warehouse' ) . "'><i class='fa fa-edit'></i></a> " . $delete_link . '</div>', 'id' );

		echo $this->datatables->generate();
	}

	public function qa_suggestions() {
		$term = $this->input->get( 'term', TRUE );

		if ( strlen( $term ) < 1 || ! $term ) {
			die( "<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url( 'welcome' ) . "'; }, 10);</script>" );
		}

		$analyzed  = $this->rerp->analyze_term( $term );
		$sr        = $analyzed['term'];
		$option_id = $analyzed['option_id'];
		$sr        = addslashes( $sr );

		$rows = $this->products_model->getQASuggestions( $sr );
		if ( $rows ) {
			foreach ( $rows as $row ) {
				$row->qty    = 1;
				$options     = $this->products_model->getProductOptions( $row->id );
				$row->option = $option_id;
				$row->serial = '';
				$c           = sha1( uniqid( mt_rand(), TRUE ) );
				$pr[]        = [
					'id'      => $c,
					'item_id' => $row->id,
					'label'   => $row->name . ' (' . $row->code . ')',
					'row'     => $row,
					'options' => $options,
				];
			}
			$this->rerp->send_json( $pr );
		} else {
			$this->rerp->send_json( [ [ 'id' => 0, 'label' => lang( 'no_match_found' ), 'value' => $term ] ] );
		}
	}

	public function add_adjustment( $count_id = NULL ) {
		$this->rerp->checkPermissions( 'adjustments', TRUE );
		$this->form_validation->set_rules( 'warehouse', lang( 'warehouse' ), 'required' );

		if ( $this->form_validation->run() == TRUE ) {
			if ( $this->Owner || $this->Admin ) {
				$date = $this->rerp->fld( $this->input->post( 'date' ) );
			} else {
				$date = date( 'Y-m-d H:s:i' );
			}

			$reference_no = $this->input->post( 'reference_no' ) ? $this->input->post( 'reference_no' ) : $this->site->getReference( 'qa' );
			$warehouse_id = $this->input->post( 'warehouse' );
			$note         = $this->rerp->clear_tags( $this->input->post( 'note' ) );

			$i = isset( $_POST['product_id'] ) ? sizeof( $_POST['product_id'] ) : 0;
			for ( $r = 0; $r < $i; $r ++ ) {
				$product_id = $_POST['product_id'][ $r ];
				$type       = $_POST['type'][ $r ];
				$quantity   = $_POST['quantity'][ $r ];
				$serial     = $_POST['serial'][ $r ];
				$variant    = isset( $_POST['variant'][ $r ] ) && ! empty( $_POST['variant'][ $r ] ) ? $_POST['variant'][ $r ] : NULL;

				if ( ! $this->Settings->overselling && $type == 'subtraction' && ! $count_id ) {
					if ( $variant ) {
						if ( $op_wh_qty = $this->products_model->getProductWarehouseOptionQty( $variant, $warehouse_id ) ) {
							if ( $op_wh_qty->quantity < $quantity ) {
								$this->session->set_flashdata( 'error', lang( 'warehouse_option_qty_is_less_than_damage' ) );
								redirect( $_SERVER['HTTP_REFERER'] );
							}
						} else {
							$this->session->set_flashdata( 'error', lang( 'warehouse_option_qty_is_less_than_damage' ) );
							redirect( $_SERVER['HTTP_REFERER'] );
						}
					}
					if ( $wh_qty = $this->products_model->getProductQuantity( $product_id, $warehouse_id ) ) {
						if ( $wh_qty['quantity'] < $quantity ) {
							$this->session->set_flashdata( 'error', lang( 'warehouse_qty_is_less_than_damage' ) );
							redirect( $_SERVER['HTTP_REFERER'] );
						}
					} else {
						$this->session->set_flashdata( 'error', lang( 'warehouse_qty_is_less_than_damage' ) );
						redirect( $_SERVER['HTTP_REFERER'] );
					}
				}

				$products[] = [
					'product_id'   => $product_id,
					'type'         => $type,
					'quantity'     => $quantity,
					'warehouse_id' => $warehouse_id,
					'option_id'    => $variant,
					'serial_no'    => $serial,
				];
			}

			if ( empty( $products ) ) {
				$this->form_validation->set_rules( 'product', lang( 'products' ), 'required' );
			} else {
				krsort( $products );
			}

			$data = [
				'date'         => $date,
				'reference_no' => $reference_no,
				'warehouse_id' => $warehouse_id,
				'note'         => $note,
				'created_by'   => $this->session->userdata( 'user_id' ),
				'count_id'     => $this->input->post( 'count_id' ) ? $this->input->post( 'count_id' ) : NULL,
			];

			if ( $_FILES['document']['size'] > 0 ) {
				$this->load->library( 'upload' );
				$this->upload->initialize( [
					'upload_path'   => $this->digital_upload_path,
					'allowed_types' => $this->digital_file_types,
					'max_size'      => $this->allowed_file_size,
					'overwrite'     => FALSE,
					'encrypt_name'  => TRUE,
				] );
				if ( ! $this->upload->do_upload( 'document' ) ) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata( 'error', $error );
					redirect( $_SERVER['HTTP_REFERER'] );
				}
				$photo              = $this->upload->file_name;
				$data['attachment'] = $photo;
			}

			// $this->rerp->print_arrays($data, $products);
		}

		if ( $this->form_validation->run() == TRUE && $this->products_model->addAdjustment( $data, $products ) ) {
			$this->session->set_userdata( 'remove_qals', 1 );
			$this->session->set_flashdata( 'message', lang( 'quantity_adjusted' ) );
			admin_redirect( 'warehouse/stock_adjustments' );
		} else {
			if ( $count_id ) {
				$stock_count = $this->products_model->getStouckCountByID( $count_id );
				$items       = $this->products_model->getStockCountItems( $count_id );
				foreach ( $items as $item ) {
					$c = sha1( uniqid( mt_rand(), TRUE ) );
					if ( $item->counted != $item->expected ) {
						$product     = $this->site->getProductByID( $item->product_id );
						$row         = json_decode( '{}' );
						$row->id     = $item->product_id;
						$row->code   = $product->code;
						$row->name   = $product->name;
						$row->qty    = $item->counted - $item->expected;
						$row->type   = $row->qty > 0 ? 'addition' : 'subtraction';
						$row->qty    = $row->qty > 0 ? $row->qty : ( 0 - $row->qty );
						$options     = $this->products_model->getProductOptions( $product->id );
						$row->option = $item->product_variant_id ? $item->product_variant_id : 0;
						$row->serial = '';
						$ri          = $this->Settings->item_addition ? $product->id : $c;

						$pr[ $ri ] = [
							'id'      => $c,
							'item_id' => $row->id,
							'label'   => $row->name . ' (' . $row->code . ')',
							'row'     => $row,
							'options' => $options,
						];
						$c ++;
					}
				}
			}
			$this->data['adjustment_items'] = $count_id ? json_encode( $pr ) : FALSE;
			$this->data['warehouse_id']     = $count_id ? $stock_count->warehouse_id : FALSE;
			$this->data['count_id']         = $count_id;
			$this->data['error']            = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
			$this->data['warehouses']       = $this->site->getAllWarehouses();
			$bc                             = [
				[ 'link' => base_url(), 'page' => lang( 'home' ) ],
				[ 'link' => admin_url( 'warehouse' ), 'page' => lang( 'warehouse' ) ],
				[ 'link' => '#', 'page' => lang( 'add_adjustment' ) ]
			];
			$meta                           = [ 'page_title' => lang( 'add_adjustment' ), 'bc' => $bc ];
			$this->page_construct( 'warehouse/add_adjustment', $meta, $this->data );
		}
	}

	public function add_adjustment_by_csv() {
		$this->rerp->checkPermissions( 'adjustments', TRUE );
		$this->form_validation->set_rules( 'warehouse', lang( 'warehouse' ), 'required' );

		if ( $this->form_validation->run() == TRUE ) {
			if ( $this->Owner || $this->Admin ) {
				$date = $this->rerp->fld( $this->input->post( 'date' ) );
			} else {
				$date = date( 'Y-m-d H:s:i' );
			}

			$reference_no = $this->input->post( 'reference_no' ) ? $this->input->post( 'reference_no' ) : $this->site->getReference( 'qa' );
			$warehouse_id = $this->input->post( 'warehouse' );
			$note         = $this->rerp->clear_tags( $this->input->post( 'note' ) );
			$data         = [
				'date'         => $date,
				'reference_no' => $reference_no,
				'warehouse_id' => $warehouse_id,
				'note'         => $note,
				'created_by'   => $this->session->userdata( 'user_id' ),
				'count_id'     => NULL,
			];

			if ( $_FILES['csv_file']['size'] > 0 ) {
				$this->load->library( 'upload' );

				$this->upload->initialize( [
					'upload_path'   => $this->digital_upload_path,
					'allowed_types' => 'csv',
					'max_size'      => $this->allowed_file_size,
					'overwrite'     => FALSE,
					'encrypt_name'  => TRUE,
				] );
				if ( ! $this->upload->do_upload( 'csv_file' ) ) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata( 'error', $error );
					redirect( $_SERVER['HTTP_REFERER'] );
				}

				$csv                = $this->upload->file_name;
				$data['attachment'] = $csv;

				$arrResult = [];
				$handle    = fopen( $this->digital_upload_path . $csv, 'r' );
				if ( $handle ) {
					while ( ( $row = fgetcsv( $handle, 5000, ',' ) ) !== FALSE ) {
						$arrResult[] = $row;
					}
					fclose( $handle );
				}
				$titles = array_shift( $arrResult );
				$keys   = [ 'code', 'quantity', 'variant' ];
				$final  = [];
				foreach ( $arrResult as $key => $value ) {
					$final[] = array_combine( $keys, $value );
				}
				// $this->rerp->print_arrays($final);
				$rw = 2;
				foreach ( $final as $pr ) {
					if ( $product = $this->products_model->getProductByCode( trim( $pr['code'] ) ) ) {
						$csv_variant = trim( $pr['variant'] );
						$variant     = ! empty( $csv_variant ) ? $this->products_model->getProductVariantID( $product->id, $csv_variant ) : FALSE;

						$csv_quantity = trim( $pr['quantity'] );
						$type         = $csv_quantity > 0 ? 'addition' : 'subtraction';
						$quantity     = $csv_quantity > 0 ? $csv_quantity : ( 0 - $csv_quantity );

						if ( ! $this->Settings->overselling && $type == 'subtraction' ) {
							if ( $variant ) {
								if ( $op_wh_qty = $this->products_model->getProductWarehouseOptionQty( $variant, $warehouse_id ) ) {
									if ( $op_wh_qty->quantity < $quantity ) {
										$this->session->set_flashdata( 'error', lang( 'warehouse_option_qty_is_less_than_damage' ) . ' - ' . lang( 'line_no' ) . ' ' . $rw );
										redirect( $_SERVER['HTTP_REFERER'] );
									}
								} else {
									$this->session->set_flashdata( 'error', lang( 'warehouse_option_qty_is_less_than_damage' ) . ' - ' . lang( 'line_no' ) . ' ' . $rw );
									redirect( $_SERVER['HTTP_REFERER'] );
								}
							}
							if ( $wh_qty = $this->products_model->getProductQuantity( $product->id, $warehouse_id ) ) {
								if ( $wh_qty['quantity'] < $quantity ) {
									$this->session->set_flashdata( 'error', lang( 'warehouse_qty_is_less_than_damage' ) . ' - ' . lang( 'line_no' ) . ' ' . $rw );
									redirect( $_SERVER['HTTP_REFERER'] );
								}
							} else {
								$this->session->set_flashdata( 'error', lang( 'warehouse_qty_is_less_than_damage' ) . ' - ' . lang( 'line_no' ) . ' ' . $rw );
								redirect( $_SERVER['HTTP_REFERER'] );
							}
						}

						$products[] = [
							'product_id'   => $product->id,
							'type'         => $type,
							'quantity'     => $quantity,
							'warehouse_id' => $warehouse_id,
							'option_id'    => $variant,
						];
					} else {
						$this->session->set_flashdata( 'error', lang( 'check_product_code' ) . ' (' . $pr['code'] . '). ' . lang( 'product_code_x_exist' ) . ' ' . lang( 'line_no' ) . ' ' . $rw );
						redirect( $_SERVER['HTTP_REFERER'] );
					}
					$rw ++;
				}
			} else {
				$this->form_validation->set_rules( 'csv_file', lang( 'upload_file' ), 'required' );
			}

			// $this->rerp->print_arrays($data, $products);
		}

		if ( $this->form_validation->run() == TRUE && $this->products_model->addAdjustment( $data, $products ) ) {
			$this->session->set_flashdata( 'message', lang( 'quantity_adjusted' ) );
			admin_redirect( 'warehouse/quantity_adjustments' );
		} else {
			$this->data['error']      = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
			$this->data['warehouses'] = $this->site->getAllWarehouses();
			$bc                       = [
				[ 'link' => base_url(), 'page' => lang( 'home' ) ],
				[ 'link' => admin_url( 'products' ), 'page' => lang( 'products' ) ],
				[ 'link' => '#', 'page' => lang( 'add_adjustment' ) ]
			];
			$meta                     = [ 'page_title' => lang( 'add_adjustment_by_csv' ), 'bc' => $bc ];
			$this->page_construct( 'warehouse/add_adjustment_by_csv', $meta, $this->data );
		}
	}

	public function addByAjax() {
		if ( ! $this->mPermissions( 'add' ) ) {
			exit( json_encode( [ 'msg' => lang( 'access_denied' ) ] ) );
		}
		if ( $this->input->get( 'token' ) && $this->input->get( 'token' ) == $this->session->userdata( 'user_csrf' ) && $this->input->is_ajax_request() ) {
			$product = $this->input->get( 'product' );
			if ( ! isset( $product['code'] ) || empty( $product['code'] ) ) {
				exit( json_encode( [ 'msg' => lang( 'product_code_is_required' ) ] ) );
			}
			if ( ! isset( $product['name'] ) || empty( $product['name'] ) ) {
				exit( json_encode( [ 'msg' => lang( 'product_name_is_required' ) ] ) );
			}
			if ( ! isset( $product['category_id'] ) || empty( $product['category_id'] ) ) {
				exit( json_encode( [ 'msg' => lang( 'product_category_is_required' ) ] ) );
			}
			if ( ! isset( $product['unit'] ) || empty( $product['unit'] ) ) {
				exit( json_encode( [ 'msg' => lang( 'product_unit_is_required' ) ] ) );
			}
			if ( ! isset( $product['price'] ) || empty( $product['price'] ) ) {
				exit( json_encode( [ 'msg' => lang( 'product_price_is_required' ) ] ) );
			}
			if ( ! isset( $product['cost'] ) || empty( $product['cost'] ) ) {
				exit( json_encode( [ 'msg' => lang( 'product_cost_is_required' ) ] ) );
			}
			if ( $this->products_model->getProductByCode( $product['code'] ) ) {
				exit( json_encode( [ 'msg' => lang( 'product_code_already_exist' ) ] ) );
			}
			if ( $row = $this->products_model->addAjaxProduct( $product ) ) {
				$tax_rate = $this->site->getTaxRateByID( $row->tax_rate );
				$pr       = [
					'id'         => $row->id,
					'label'      => $row->name . ' (' . $row->code . ')',
					'code'       => $row->code,
					'qty'        => 1,
					'cost'       => $row->cost,
					'name'       => $row->name,
					'tax_method' => $row->tax_method,
					'tax_rate'   => $tax_rate,
					'discount'   => '0'
				];
				$this->rerp->send_json( [ 'msg' => 'success', 'result' => $pr ] );
			} else {
				exit( json_encode( [ 'msg' => lang( 'failed_to_add_product' ) ] ) );
			}
		} else {
			json_encode( [ 'msg' => 'Invalid token' ] );
		}
	}

	public function adjustment_actions() {
		if ( ! $this->Owner && ! $this->GP['bulk_actions'] ) {
			$this->session->set_flashdata( 'warning', lang( 'access_denied' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$this->form_validation->set_rules( 'form_action', lang( 'form_action' ), 'required' );

		if ( $this->form_validation->run() == TRUE ) {
			if ( ! empty( $_POST['val'] ) ) {
				if ( $this->input->post( 'form_action' ) == 'delete' ) {
					$this->rerp->checkPermissions( 'delete' );
					foreach ( $_POST['val'] as $id ) {
						$this->products_model->deleteAdjustment( $id );
					}
					$this->session->set_flashdata( 'message', $this->lang->line( 'adjustment_deleted' ) );
					redirect( $_SERVER['HTTP_REFERER'] );
				} elseif ( $this->input->post( 'form_action' ) == 'export_excel' ) {
					$this->load->library( 'excel' );
					$this->excel->setActiveSheetIndex( 0 );
					$this->excel->getActiveSheet()->setTitle( 'quantity_adjustments' );
					$this->excel->getActiveSheet()->SetCellValue( 'A1', lang( 'date' ) );
					$this->excel->getActiveSheet()->SetCellValue( 'B1', lang( 'reference_no' ) );
					$this->excel->getActiveSheet()->SetCellValue( 'C1', lang( 'warehouse' ) );
					$this->excel->getActiveSheet()->SetCellValue( 'D1', lang( 'created_by' ) );
					$this->excel->getActiveSheet()->SetCellValue( 'E1', lang( 'note' ) );
					$this->excel->getActiveSheet()->SetCellValue( 'F1', lang( 'items' ) );

					$row = 2;
					foreach ( $_POST['val'] as $id ) {
						$adjustment = $this->products_model->getAdjustmentByID( $id );
						$created_by = $this->site->getUser( $adjustment->created_by );
						$warehouse  = $this->site->getWarehouseByID( $adjustment->warehouse_id );
						$items      = $this->products_model->getAdjustmentItems( $id );
						$products   = '';
						if ( $items ) {
							foreach ( $items as $item ) {
								$products .= $item->product_name . '(' . $this->rerp->formatQuantity( $item->type == 'subtraction' ? - $item->quantity : $item->quantity ) . ')' . "\n";
							}
						}

						$this->excel->getActiveSheet()->SetCellValue( 'A' . $row, $this->rerp->hrld( $adjustment->date ) );
						$this->excel->getActiveSheet()->SetCellValue( 'B' . $row, $adjustment->reference_no );
						$this->excel->getActiveSheet()->SetCellValue( 'C' . $row, $warehouse->name );
						$this->excel->getActiveSheet()->SetCellValue( 'D' . $row, $created_by->first_name . ' ' . $created_by->last_name );
						$this->excel->getActiveSheet()->SetCellValue( 'E' . $row, $this->rerp->decode_html( $adjustment->note ) );
						$this->excel->getActiveSheet()->SetCellValue( 'F' . $row, $products );
						$row ++;
					}

					$this->excel->getActiveSheet()->getColumnDimension( 'A' )->setWidth( 20 );
					$this->excel->getActiveSheet()->getColumnDimension( 'B' )->setWidth( 20 );
					$this->excel->getActiveSheet()->getColumnDimension( 'C' )->setWidth( 15 );
					$this->excel->getActiveSheet()->getColumnDimension( 'D' )->setWidth( 20 );
					$this->excel->getActiveSheet()->getColumnDimension( 'E' )->setWidth( 40 );
					$this->excel->getActiveSheet()->getColumnDimension( 'F' )->setWidth( 30 );
					$this->excel->getDefaultStyle()->getAlignment()->setVertical( 'center' );
					$filename = 'quantity_adjustments_' . date( 'Y_m_d_H_i_s' );
					$this->load->helper( 'excel' );
					create_excel( $this->excel, $filename );
				}
			} else {
				$this->session->set_flashdata( 'error', $this->lang->line( 'no_record_selected' ) );
				redirect( $_SERVER['HTTP_REFERER'] );
			}
		} else {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
	}

	public function edit_adjustment( $id ) {
		$this->rerp->checkPermissions( 'adjustments', TRUE );
		$adjustment = $this->products_model->getAdjustmentByID( $id );
		if ( ! $id || ! $adjustment ) {
			$this->session->set_flashdata( 'error', lang( 'adjustment_not_found' ) );
			$this->rerp->md();
		}
		$this->form_validation->set_rules( 'warehouse', lang( 'warehouse' ), 'required' );

		if ( $this->form_validation->run() == TRUE ) {
			if ( $this->Owner || $this->Admin ) {
				$date = $this->rerp->fld( $this->input->post( 'date' ) );
			} else {
				$date = $adjustment->date;
			}

			$reference_no = $this->input->post( 'reference_no' );
			$warehouse_id = $this->input->post( 'warehouse' );
			$note         = $this->rerp->clear_tags( $this->input->post( 'note' ) );

			$i = isset( $_POST['product_id'] ) ? sizeof( $_POST['product_id'] ) : 0;
			for ( $r = 0; $r < $i; $r ++ ) {
				$product_id = $_POST['product_id'][ $r ];
				$type       = $_POST['type'][ $r ];
				$quantity   = $_POST['quantity'][ $r ];
				$serial     = $_POST['serial'][ $r ];
				$variant    = isset( $_POST['variant'][ $r ] ) && ! empty( $_POST['variant'][ $r ] ) ? $_POST['variant'][ $r ] : NULL;

				if ( ! $this->Settings->overselling && $type == 'subtraction' ) {
					if ( $variant ) {
						if ( $op_wh_qty = $this->products_model->getProductWarehouseOptionQty( $variant, $warehouse_id ) ) {
							if ( $op_wh_qty->quantity < $quantity ) {
								$this->session->set_flashdata( 'error', lang( 'warehouse_option_qty_is_less_than_damage' ) );
								redirect( $_SERVER['HTTP_REFERER'] );
							}
						} else {
							$this->session->set_flashdata( 'error', lang( 'warehouse_option_qty_is_less_than_damage' ) );
							redirect( $_SERVER['HTTP_REFERER'] );
						}
					}
					if ( $wh_qty = $this->products_model->getProductQuantity( $product_id, $warehouse_id ) ) {
						if ( $wh_qty['quantity'] < $quantity ) {
							$this->session->set_flashdata( 'error', lang( 'warehouse_qty_is_less_than_damage' ) );
							redirect( $_SERVER['HTTP_REFERER'] );
						}
					} else {
						$this->session->set_flashdata( 'error', lang( 'warehouse_qty_is_less_than_damage' ) );
						redirect( $_SERVER['HTTP_REFERER'] );
					}
				}

				$products[] = [
					'product_id'   => $product_id,
					'type'         => $type,
					'quantity'     => $quantity,
					'warehouse_id' => $warehouse_id,
					'option_id'    => $variant,
					'serial_no'    => $serial,
				];
			}

			if ( empty( $products ) ) {
				$this->form_validation->set_rules( 'product', lang( 'products' ), 'required' );
			} else {
				krsort( $products );
			}

			$data = [
				'date'         => $date,
				'reference_no' => $reference_no,
				'warehouse_id' => $warehouse_id,
				'note'         => $note,
				'created_by'   => $this->session->userdata( 'user_id' ),
			];

			if ( $_FILES['document']['size'] > 0 ) {
				$this->load->library( 'upload' );
				$this->upload->initialize( [
					'upload_path'   => $this->digital_upload_path,
					'allowed_types' => $this->digital_file_types,
					'max_size'      => $this->allowed_file_size,
					'overwrite'     => FALSE,
					'encrypt_name'  => TRUE,
				] );
				if ( ! $this->upload->do_upload( 'document' ) ) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata( 'error', $error );
					redirect( $_SERVER['HTTP_REFERER'] );
				}
				$photo              = $this->upload->file_name;
				$data['attachment'] = $photo;
			}

			// $this->rerp->print_arrays($data, $products);
		}

		if ( $this->form_validation->run() == TRUE && $this->products_model->updateAdjustment( $id, $data, $products ) ) {
			$this->session->set_userdata( 'remove_qals', 1 );
			$this->session->set_flashdata( 'message', lang( 'quantity_adjusted' ) );
			admin_redirect( 'warehouse/stock_quantity_adjustments' );
		} else {
			$inv_items = $this->products_model->getAdjustmentItems( $id );
			// krsort($inv_items);
			foreach ( $inv_items as $item ) {
				$c           = sha1( uniqid( mt_rand(), TRUE ) );
				$product     = $this->site->getProductByID( $item->product_id );
				$row         = json_decode( '{}' );
				$row->id     = $item->product_id;
				$row->code   = $product->code;
				$row->name   = $product->name;
				$row->qty    = $item->quantity;
				$row->type   = $item->type;
				$options     = $this->products_model->getProductOptions( $product->id );
				$row->option = $item->option_id ? $item->option_id : 0;
				$row->serial = $item->serial_no ? $item->serial_no : '';
				$ri          = $this->Settings->item_addition ? $product->id : $c;

				$pr[ $ri ] = [
					'id'      => $c,
					'item_id' => $row->id,
					'label'   => $row->name . ' (' . $row->code . ')',
					'row'     => $row,
					'options' => $options,
				];
				$c ++;
			}

			$this->data['adjustment']       = $adjustment;
			$this->data['adjustment_items'] = json_encode( $pr );
			$this->data['error']            = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
			$this->data['warehouses']       = $this->site->getAllWarehouses();
			$bc                             = [
				[ 'link' => base_url(), 'page' => lang( 'home' ) ],
				[ 'link' => admin_url( 'warehouse' ), 'page' => lang( 'warehouse' ) ],
				[ 'link' => '#', 'page' => lang( 'edit_adjustment' ) ]
			];
			$meta                           = [ 'page_title' => lang( 'edit_adjustment' ), 'bc' => $bc ];
			$this->page_construct( 'warehouse/edit_adjustment', $meta, $this->data );
		}
	}

	public function barcode( $product_code = NULL, $bcs = 'code128', $height = 40 ) {
		if ( $this->Settings->barcode_img ) {
			header( 'Content-Type: image/png' );
		} else {
			header( 'Content-type: image/svg+xml' );
		}
		echo $this->rerp->barcode( $product_code, $bcs, $height, TRUE, FALSE, TRUE );
	}

	public function stock_counts($warehouse_id = null)
	{
		$this->rerp->checkPermissions('stock_count');

		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
			$this->data['warehouses']   = $this->site->getAllWarehouses();
			$this->data['warehouse_id'] = $warehouse_id;
			$this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
		} else {
			$this->data['warehouses']   = null;
			$this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
			$this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
		}

		$bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('warehouse'), 'page' => lang('warehouse')], ['link' => '#', 'page' => lang('stock_counts')]];
		$meta = ['page_title' => lang('stock_counts'), 'bc' => $bc];
		$this->page_construct('warehouse/stock_counts', $meta, $this->data);
	}

	public function count_stock( $page = NULL ) {
		$this->rerp->checkPermissions( 'stock_count' );
		$this->form_validation->set_rules( 'warehouse', lang( 'warehouse' ), 'required' );
		$this->form_validation->set_rules( 'type', lang( 'type' ), 'required' );

		if ( $this->form_validation->run() == TRUE ) {
			$warehouse_id = $this->input->post( 'warehouse' );
			$type         = $this->input->post( 'type' );
			$categories   = $this->input->post( 'category' ) ? $this->input->post( 'category' ) : NULL;
			$brands       = $this->input->post( 'brand' ) ? $this->input->post( 'brand' ) : NULL;
			$this->load->helper( 'string' );
			$name     = random_string( 'md5' ) . '.csv';
			$products = $this->products_model->getStockCountProducts( $warehouse_id, $type, $categories, $brands );
			$pr       = 0;
			$rw       = 0;
			foreach ( $products as $product ) {
				if ( $variants = $this->products_model->getStockCountProductVariants( $warehouse_id, $product->id ) ) {
					foreach ( $variants as $variant ) {
						$items[] = [
							'product_code' => $product->code,
							'product_name' => $product->name,
							'variant'      => $variant->name,
							'expected'     => $variant->quantity,
							'counted'      => '',
						];
						$rw ++;
					}
				} else {
					$items[] = [
						'product_code' => $product->code,
						'product_name' => $product->name,
						'variant'      => '',
						'expected'     => $product->quantity,
						'counted'      => '',
					];
					$rw ++;
				}
				$pr ++;
			}
			if ( ! empty( $items ) ) {
				$csv_file = fopen( './files/' . $name, 'w' );
				fprintf( $csv_file, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
				fputcsv( $csv_file, [
					lang( 'product_code' ),
					lang( 'product_name' ),
					lang( 'variant' ),
					lang( 'expected' ),
					lang( 'counted' )
				] );
				foreach ( $items as $item ) {
					fputcsv( $csv_file, $item );
				}
				// file_put_contents('./files/'.$name, $csv_file);
				// fwrite($csv_file, $txt);
				fclose( $csv_file );
			} else {
				$this->session->set_flashdata( 'error', lang( 'no_product_found' ) );
				redirect( $_SERVER['HTTP_REFERER'] );
			}

			if ( $this->Owner || $this->Admin ) {
				$date = $this->rerp->fld( $this->input->post( 'date' ) );
			} else {
				$date = date( 'Y-m-d H:s:i' );
			}
			$category_ids   = '';
			$brand_ids      = '';
			$category_names = '';
			$brand_names    = '';
			if ( $categories ) {
				$r = 1;
				$s = sizeof( $categories );
				foreach ( $categories as $category_id ) {
					$category = $this->site->getCategoryByID( $category_id );
					if ( $r == $s ) {
						$category_names .= $category->name;
						$category_ids   .= $category->id;
					} else {
						$category_names .= $category->name . ', ';
						$category_ids   .= $category->id . ', ';
					}
					$r ++;
				}
			}
			if ( $brands ) {
				$r = 1;
				$s = sizeof( $brands );
				foreach ( $brands as $brand_id ) {
					$brand = $this->site->getBrandByID( $brand_id );
					if ( $r == $s ) {
						$brand_names .= $brand->name;
						$brand_ids   .= $brand->id;
					} else {
						$brand_names .= $brand->name . ', ';
						$brand_ids   .= $brand->id . ', ';
					}
					$r ++;
				}
			}
			$data = [
				'date'           => $date,
				'warehouse_id'   => $warehouse_id,
				'reference_no'   => $this->input->post( 'reference_no' ),
				'type'           => $type,
				'categories'     => $category_ids,
				'category_names' => $category_names,
				'brands'         => $brand_ids,
				'brand_names'    => $brand_names,
				'initial_file'   => $name,
				'products'       => $pr,
				'rows'           => $rw,
				'created_by'     => $this->session->userdata( 'user_id' ),
			];
		}

		if ( $this->form_validation->run() == TRUE && $this->products_model->addStockCount( $data ) ) {
			$this->session->set_flashdata( 'message', lang( 'stock_count_intiated' ) );
			admin_redirect( 'warehouse/stock_counts' );
		} else {
			$this->data['error']      = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
			$this->data['warehouses'] = $this->site->getAllWarehouses();
			$this->data['categories'] = $this->site->getAllCategories();
			$this->data['brands']     = $this->site->getAllBrands();
			$bc                       = [
				[ 'link' => base_url(), 'page' => lang( 'home' ) ],
				[ 'link' => admin_url( 'warehouse' ), 'page' => lang( 'warehouse' ) ],
				[ 'link' => '#', 'page' => lang( 'count_stock' ) ]
			];
			$meta                     = [ 'page_title' => lang( 'count_stock' ), 'bc' => $bc ];
			$this->page_construct( 'warehouse/count_stock', $meta, $this->data );
		}
	}

	public function getCounts($warehouse_id = null)
	{
		$this->rerp->checkPermissions('stock_count', true);

		if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
			$user         = $this->site->getUser();
			$warehouse_id = $user->warehouse_id;
		}
		$detail_link = anchor('admin/warehouse/view_count/$1', '<label class="label label-primary pointer">' . lang('details') . '</label>', 'class="tip" title="' . lang('details') . '" data-toggle="modal" data-target="#myModal"');

		$this->load->library('datatables');
		$this->datatables
			->select("{$this->db->dbprefix('stock_counts')}.id as id, date, reference_no, {$this->db->dbprefix('warehouses')}.name as wh_name, type, brand_names, category_names, initial_file, final_file")
			->from('stock_counts')
			->join('warehouses', 'warehouses.id=stock_counts.warehouse_id', 'left');
		if ($warehouse_id) {
			$this->datatables->where('warehouse_id', $warehouse_id);
		}

		$this->datatables->add_column('Actions', '<div class="text-center">' . $detail_link . '</div>', 'id');
		echo $this->datatables->generate();
	}

	public function view_count($id)
	{
		$this->rerp->checkPermissions('stock_count', true);
		$stock_count = $this->products_model->getStouckCountByID($id);
		if (!$stock_count->finalized) {
			$this->rerp->md('admin/warehouse/finalize_count/' . $id);
		}

		$this->data['stock_count']       = $stock_count;
		$this->data['stock_count_items'] = $this->products_model->getStockCountItems($id);
		$this->data['warehouse']         = $this->site->getWarehouseByID($stock_count->warehouse_id);
		$this->data['adjustment']        = $this->products_model->getAdjustmentByCountID($id);
		$this->load->view($this->theme . 'warehouse/view_count', $this->data);
	}

	public function finalize_count($id)
	{
		$this->rerp->checkPermissions('stock_count');
		$stock_count = $this->products_model->getStouckCountByID($id);
		if (!$stock_count || $stock_count->finalized) {
			$this->session->set_flashdata('error', lang('stock_count_finalized'));
			admin_redirect('warehouse/stock_counts');
		}

		$this->form_validation->set_rules('count_id', lang('count_stock'), 'required');

		if ($this->form_validation->run() == true) {
			if ($_FILES['csv_file']['size'] > 0) {
				$note = $this->rerp->clear_tags($this->input->post('note'));
				$data = [
					'updated_by' => $this->session->userdata('user_id'),
					'updated_at' => date('Y-m-d H:s:i'),
					'note'       => $note,
				];

				$this->load->library('upload');

				$this->upload->initialize( [
					'upload_path'   => $this->digital_upload_path,
					'allowed_types' => 'csv',
					'max_size'      => $this->allowed_file_size,
					'overwrite'     => false,
					'encrypt_name'  => true,
				] );
				if (!$this->upload->do_upload('csv_file')) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata('error', $error);
					redirect($_SERVER['HTTP_REFERER']);
				}

				$csv = $this->upload->file_name;

				$arrResult = [];
				$handle    = fopen($this->digital_upload_path . $csv, 'r');
				if ($handle) {
					while (($row = fgetcsv($handle, 5000, ',')) !== false) {
						$arrResult[] = $row;
					}
					fclose($handle);
				}
				$titles = array_shift($arrResult);
				$keys   = ['product_code', 'product_name', 'product_variant', 'expected', 'counted'];
				$final  = [];
				foreach ($arrResult as $key => $value) {
					$final[] = array_combine($keys, $value);
				}
				// $this->rerp->print_arrays($final);
				$rw          = 2;
				$differences = 0;
				$matches     = 0;
				foreach ($final as $pr) {
					if ($product = $this->products_model->getProductByCode(trim($pr['product_code']))) {
						$pr['counted'] = !empty($pr['counted']) ? $pr['counted'] : 0;
						if ($pr['expected'] == $pr['counted']) {
							$matches++;
						} else {
							$pr['stock_count_id']     = $id;
							$pr['product_id']         = $product->id;
							$pr['cost']               = $product->cost;
							$pr['product_variant_id'] = empty($pr['product_variant']) ? null : $this->products_model->getProductVariantID($pr['product_id'], $pr['product_variant']);
							$products[]               = $pr;
							$differences++;
						}
					} else {
						$this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['product_code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
						admin_redirect('warehouse/finalize_count/' . $id);
					}
					$rw++;
				}

				$data['final_file']  = $csv;
				$data['differences'] = $differences;
				$data['matches']     = $matches;
				$data['missing']     = $stock_count->rows - ($rw - 2);
				$data['finalized']   = 1;
			}

			// $this->rerp->print_arrays($data, $products);
		}

		if ($this->form_validation->run() == true && $this->products_model->finalizeStockCount($id, $data, $products)) {
			$this->session->set_flashdata('message', lang('stock_count_finalized'));
			admin_redirect('warehouse/stock_counts');
		} else {
			$this->data['error']       = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$this->data['stock_count'] = $stock_count;
			$this->data['warehouse']   = $this->site->getWarehouseByID($stock_count->warehouse_id);
			$bc                        = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('warehouse'), 'page' => lang('warehouse')], ['link' => admin_url('warehouse/stock_counts'), 'page' => lang('stock_counts')], ['link' => '#', 'page' => lang('finalize_count')]];
			$meta                      = ['page_title' => lang('finalize_count'), 'bc' => $bc];
			$this->page_construct('warehouse/finalize_count', $meta, $this->data);
		}
	}

}
