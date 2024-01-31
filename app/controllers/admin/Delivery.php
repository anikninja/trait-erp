<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

/**
 * Class Delivery
 *
 * @property Delivery_schedule_model $schedule
 * @property Sales_model $sales_model
 * @property Fc $fc
 */
class Delivery extends MY_Controller {
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
		$this->load->admin_model( 'delivery_schedule_model', 'schedule' );
		$this->load->admin_model( 'sales_model' );
		$this->load->admin_model( 'delivery_model' );
		$this->load->model( 'Erp_Package' );
		$this->load->model( 'Erp_Package_Items' );
		$this->load->model( 'Erp_Shipment' );
		$this->load->model( 'MY_RetailErp_Model' );

		$this->data['logo'] = TRUE;

	}

	public function index() {

	}

	public function add_package_for_shipment( $shipment_id, $package_id ) {
		if ( ! $shipment_id ) {
			$this->session->set_flashdata( 'error', lang( 'shipment_not_found' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		if ( ! $package_id ) {
			$this->session->set_flashdata( 'error', lang( 'package_not_found' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$shipment = new Erp_Shipment( $shipment_id );
		$package  = new Erp_package( $package_id );

		if ( $shipment->getId() !== $shipment_id ) {
			$this->session->set_flashdata( 'error', lang( 'shipment_not_found' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		if ( $package->getId() !== $package_id ) {
			$this->session->set_flashdata( 'error', lang( 'package_not_found' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$delivery_id = ( $package->delivery_id ) ? $package->delivery_id : $shipment->delivery_id;
		$package->setDeliveryId( $delivery_id );
		$package->setShipmentId( $shipment_id );

		if ( $package->save() ) {
			$this->session->set_flashdata( 'message', 'Package Successfully Added.' );
			admin_redirect( 'delivery/edit_shipment/' . $shipment_id );
		}
	}

	public function add_delivery( $id = NULL ) {
		$this->rerp->checkPermissions();

		if ( $this->input->get( 'id' ) ) {
			$id = $this->input->get( 'id' );
		}
		$sale = $this->sales_model->getInvoiceByID( $id );

		if ( $sale->sale_status != 'completed' ) {
			$this->session->set_flashdata( 'error', lang( 'status_is_x_completed' ) );
			$this->rerp->md();
		}

		if ( $delivery = $this->sales_model->getDeliveryBySaleID( $id ) ) {
			$this->edit_delivery( $delivery->id );
		} else {
			$this->form_validation->set_rules( 'sale_reference_no', lang( 'sale_reference_no' ), 'required' );
			$this->form_validation->set_rules( 'customer', lang( 'customer' ), 'required' );
			$this->form_validation->set_rules( 'address', lang( 'address' ), 'required' );
			$schedule = FALSE;
			if ( $this->form_validation->run() == TRUE ) {
				if ( $this->shop_model->hasShippingSlot() && ( $schedule = $this->sales_model->getDeliveryScheduleBySaleId( $id ) ) ) {
					$schedule = new Erp_Delivery_Schedule( $schedule->id );
					$date     = $schedule->getStart();
				} else {
					if ( $this->Owner || $this->Admin ) {
						$date = $this->rerp->fld( trim( $this->input->post( 'date' ) ) );
					} else {
						$date = date( 'Y-m-d H:i:s' );
					}
				}

				$dlDetails = [
					'date'              => $date,
					'sale_id'           => $this->input->post( 'sale_id' ),
					'do_reference_no'   => $this->input->post( 'do_reference_no' ) ? $this->input->post( 'do_reference_no' ) : $this->site->getReference( 'do' ),
					'sale_reference_no' => $this->input->post( 'sale_reference_no' ),
					'customer'          => $this->input->post( 'customer' ),
					'address'           => $this->input->post( 'address' ),
					'status'            => $this->input->post( 'status' ),
					'delivered_by'      => $this->input->post( 'delivered_by' ),
					'received_by'       => $this->input->post( 'received_by' ),
					'note'              => $this->rerp->clear_tags( $this->input->post( 'note' ) ),
					'created_by'        => $this->session->userdata( 'user_id' ),
				];
				if ( $_FILES['document']['size'] > 0 ) {
					$this->load->library( 'upload' );
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = FALSE;
					$config['encrypt_name']  = TRUE;
					$this->upload->initialize( $config );
					if ( ! $this->upload->do_upload( 'document' ) ) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata( 'error', $error );
						redirect( $_SERVER['HTTP_REFERER'] );
					}
					$photo                   = $this->upload->file_name;
					$dlDetails['attachment'] = $photo;
				}
			} elseif ( $this->input->post( 'add_delivery' ) ) {
				if ( $sale->shop ) {
					$this->load->library( 'sms' );
					$this->sms->delivering( $sale->id, $dlDetails['do_reference_no'] );
				}
				$this->session->set_flashdata( 'error', validation_errors() );
				redirect( $_SERVER['HTTP_REFERER'] );
			}

			if ( $this->form_validation->run() == TRUE && $delivery_id = $this->sales_model->addDelivery( $dlDetails ) ) {
				if ( $schedule ) {
					$schedule->setDeliveryId( $delivery_id );
					$schedule->save();
				}
				$this->session->set_flashdata( 'message', lang( 'delivery_added' ) );
				admin_redirect( 'sales/deliveries' );
			} else {
				$scheduled = FALSE;
				if ( $this->shop_model->hasShippingSlot() && ( $schedule = $this->sales_model->getDeliveryScheduleBySaleId( $id ) ) ) {
					$scheduled = TRUE;
					$date      = $schedule->start;
				} else {
					if ( $this->Owner || $this->Admin ) {
						$date = $this->rerp->fld( date( 'Y-m-d H:i:s' ) );
					} else {
						$date = date( 'Y-m-d H:i:s' );
					}
				}

				$this->data['error']           = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
				$this->data['customer']        = $this->site->getCompanyByID( $sale->customer_id );
				$this->data['address']         = $this->site->getAddressByID( $sale->address_id );
				$this->data['inv']             = $sale;
				$this->data['date']            = $date;
				$this->data['scheduled']       = $scheduled;
				$this->data['do_reference_no'] = $this->site->getReference( 'do' );
				$this->data['modal_js']        = $this->site->modal_js();

				$this->load->view( $this->theme . 'sales/add_delivery', $this->data );
			}
		}
	}

	public function edit_delivery( $id = NULL ) {
		$this->rerp->checkPermissions();

		if ( $this->input->get( 'id' ) ) {
			$id = $this->input->get( 'id' );
		}

		$this->form_validation->set_rules( 'do_reference_no', lang( 'do_reference_no' ), 'required' );
		$this->form_validation->set_rules( 'sale_reference_no', lang( 'sale_reference_no' ), 'required' );
		$this->form_validation->set_rules( 'customer', lang( 'customer' ), 'required' );
		$this->form_validation->set_rules( 'address', lang( 'address' ), 'required' );

		if ( $this->form_validation->run() == TRUE ) {
			$dlDetails = [
				'sale_id'           => $this->input->post( 'sale_id' ),
				'do_reference_no'   => $this->input->post( 'do_reference_no' ),
				'sale_reference_no' => $this->input->post( 'sale_reference_no' ),
				'customer'          => $this->input->post( 'customer' ),
				'address'           => $this->input->post( 'address' ),
				'status'            => $this->input->post( 'status' ),
				'delivered_by'      => $this->input->post( 'delivered_by' ),
				'received_by'       => $this->input->post( 'received_by' ),
				'note'              => $this->rerp->clear_tags( $this->input->post( 'note' ) ),
				'created_by'        => $this->session->userdata( 'user_id' ),
			];

			if ( $_FILES['document']['size'] > 0 ) {
				$this->load->library( 'upload' );
				$config['upload_path']   = $this->digital_upload_path;
				$config['allowed_types'] = $this->digital_file_types;
				$config['max_size']      = $this->allowed_file_size;
				$config['overwrite']     = FALSE;
				$config['encrypt_name']  = TRUE;
				$this->upload->initialize( $config );
				if ( ! $this->upload->do_upload( 'document' ) ) {
					$error = $this->upload->display_errors();
					$this->session->set_flashdata( 'error', $error );
					redirect( $_SERVER['HTTP_REFERER'] );
				}
				$photo                   = $this->upload->file_name;
				$dlDetails['attachment'] = $photo;
			}

			if ( $this->Owner || $this->Admin ) {
				$date              = $this->rerp->fld( trim( $this->input->post( 'date' ) ) );
				$dlDetails['date'] = $date;
			}
		} elseif ( $this->input->post( 'edit_delivery' ) ) {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		if ( $this->form_validation->run() == TRUE && $this->sales_model->updateDelivery( $id, $dlDetails ) ) {
			$this->session->set_flashdata( 'message', lang( 'delivery_updated' ) );
			admin_redirect( 'sales/deliveries' );
		} else {
			$this->data['error']    = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
			$this->data['delivery'] = $this->sales_model->getDeliveryByID( $id );
			$this->data['modal_js'] = $this->site->modal_js();
			$this->load->view( $this->theme . 'sales/edit_delivery', $this->data );
		}
	}

	public function delivery_list() {
		$this->rerp->checkPermissions( 'deliveries' );

		$data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$bc            = [
			[ 'link' => base_url(), 'page' => lang( 'home' ) ],
			[ 'link' => admin_url( 'sales' ), 'page' => lang( 'sales' ) ],
			[ 'link' => '#', 'page' => lang( 'deliveries' ) ]
		];
		$meta          = [ 'page_title' => lang( 'deliveries' ), 'bc' => $bc ];
		$this->page_construct( 'delivery/deliveries', $meta, $this->data );
	}

	public function getDeliveries() {
		$this->rerp->checkPermissions( 'deliveries' );

		$detail_link       = anchor( 'admin/delivery/view_delivery/$1', '<i class="fa fa-file-text-o"></i> ' . lang( 'delivery_details' ), 'data-toggle="modal" data-target="#myModal"' );
		$email_link        = anchor( 'admin/sales/email_delivery/$1', '<i class="fa fa-envelope"></i> ' . lang( 'email_delivery' ), 'data-toggle="modal" data-target="#myModal"' );
		$edit_link         = anchor( 'admin/sales/edit_delivery/$1', '<i class="fa fa-edit"></i> ' . lang( 'edit_delivery' ), 'data-toggle="modal" data-target="#myModal"' );
		$add_shipment_link = anchor( 'admin/delivery/add_shipment/?id=$1', '<i class="fa fa-plus"></i> ' . lang( 'add_shipment' ), 'data-toggle="modal" data-target="#myModal"' );
		$add_pickup_link   = anchor( 'admin/delivery/add_pickup/?id=$1', '<i class="fa fa-plus"></i> ' . sprintf( lang( 'add_x' ), lang( 'pickup' ) ), 'data-toggle="modal" data-target="#myModal"' );
		$sales_invoice     = anchor( 'admin/sales/modal_view/$2', '<i class="fa fa-file-text-o"></i> ' . lang( 'sales_invoice' ), 'data-toggle="modal" data-target="#myModal"' );
		$payments_link     = anchor( 'admin/sales/payments/$2', '<i class="fa fa-money"></i> ' . lang( 'view_payments' ), 'data-toggle="modal" data-target="#myModal"' );
		$add_payment_link  = anchor( 'admin/sales/add_payment/$2', '<i class="fa fa-money"></i> ' . lang( 'add_payment' ), 'data-toggle="modal" data-target="#myModal"' );
		$return_link       = anchor( 'admin/sales/return_sale/$2', '<i class="fa fa-angle-double-left"></i> ' . lang( 'add_return' ) );
		$pdf_link          = anchor( 'admin/sales/pdf_delivery/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang( 'download_pdf' ) );
		$delete_link       = "<a href='#' class='po' title='<b>" . lang( 'delete_delivery' ) . "</b>' data-content=\"<p>"
		                     . lang( 'r_u_sure' ) . "</p><a class='btn btn-danger po-delete' href='" . admin_url( 'sales/delete_delivery/$1' ) . "'>"
		                     . lang( 'i_m_sure' ) . "</a> <button class='btn po-close'>" . lang( 'no' ) . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
		                     . lang( 'delete_delivery' ) . '</a>';
		$action            = '<div class="text-center"><div class="btn-group text-left">'
		                     . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
		                     . lang( 'actions' ) . ' <span class="caret"></span></button>
    <ul class="dropdown-menu pull-right" role="menu">
        <li>' . $detail_link . '</li>
        <li>' . $edit_link . '</li>
        <li>' . $add_shipment_link . '</li>       
        <li>' . $add_pickup_link . '</li> 
        <li>' . $sales_invoice . '</li> 
        <li>' . $payments_link . '</li> 
        <li>' . $add_payment_link . '</li>       
        <li>' . $return_link . '</li>
        <li>' . $pdf_link . '</li>
        <li>' . $delete_link . '</li>
    </ul>
</div></div>';

		$this->load->library( 'datatables' );
		//GROUP_CONCAT(CONCAT('Name: ', sale_items.product_name, ' Qty: ', sale_items.quantity ) SEPARATOR '<br>')
		$sh = "(SELECT delivery_id, GROUP_CONCAT(shipment_no SEPARATOR ', \n') AS shipment_no FROM {$this->db->dbprefix('shipment')} WHERE delete_flag = 0 GROUP BY delivery_id ) ship";
		$ph = "(SELECT delivery_id, GROUP_CONCAT(pickup_no SEPARATOR ', \n') AS pickup_no FROM {$this->db->dbprefix('pickup')} WHERE delete_flag = 0 GROUP BY delivery_id ) pick";
		//$pc = "(SELECT delivery_id, GROUP_CONCAT(package_no SEPARATOR ', \n') AS package_no FROM {$this->db->dbprefix('package')} GROUP BY delivery_id ) pack";
		$this->datatables
			->select( 'deliveries.id as id, deliveries.date, do_reference_no, sale_reference_no, COALESCE(ship.shipment_no, \'None\') as shipment_no, COALESCE(pick.pickup_no, \'None\') as pickup_no, deliveries.customer, address, deliveries.status as delivery_status, ' . $this->db->dbprefix( 'sales' ) . '.payment_status, deliveries.attachment, deliveries.sale_id as sale_id' )
			->from( 'deliveries' )
			->join( 'sale_items', 'sale_items.sale_id=deliveries.sale_id', 'left' )
			->join( $sh, 'ship.delivery_id = deliveries.id', 'left' )
			->join( $ph, 'pick.delivery_id = deliveries.id', 'left' )
			->join( 'sales', 'sales.id = deliveries.sale_id', 'left' )
			->group_by( 'deliveries.id' );
		$this->datatables->add_column( 'Actions', $action, 'id, sale_id' );
		$this->datatables->unset_column( 'sale_id' );

		echo $this->datatables->generate();
	}

	public function view_delivery( $id = NULL ) {
		$sales      = new Sales_model();
		$deliveries = new Delivery_model();

		$this->rerp->checkPermissions( 'deliveries' );

		if ( $this->input->get( 'id' ) ) {
			$id = $this->input->get( 'id' );
		}

		$this->data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$deli                = $sales->getDeliveryByID( $id );
		$sale                = $sales->getInvoiceByID( $deli->sale_id );
		if ( ! $sale ) {
			$this->session->set_flashdata( 'error', lang( 'sale_not_found' ) );
			$this->rerp->md();
		}

		$shipments = $deliveries->getAllShipmentByDeliveryID( $deli->id );
		$pickup    = $deliveries->getAllPickupByDeliveryID( $deli->id );

		$_table = NULL;
		if ( $shipments ) {
			foreach ( $shipments as $ship ) {
				$package = $deliveries->getAllPackageByShipmentID( $ship->id );
				$_table  .= '<table class="table table-striped table-bordered">';
				$_table  .= '<thead>';
				$_table  .= '<tr>';
				$_table  .= '<th colspan="2">' . $ship->shipment_no . '</th>';
				$_table  .= '</tr>';
				$_table  .= '</thead>';

				$_table .= '<tbody>';
				$_table .= '<tr>';
				$_table .= '<td>' . lang( 'shipment_date' ) . ': ' . $this->rerp->hrld( $ship->shipment_date ) . '</td>';
				$_table .= '<td>' . lang( 'total_package' ) . ': ' . ( ( $package ) ? count( $package ) : 0 ) . '</td>';
				$_table .= '</tr>';
				$_table .= '<tr>';
				$_table .= '<td colspan="2">';
				if ( $package ) {
					foreach ( $package as $packing ) {
						$i      = 0;
						$_table .= '<table class="table table-striped table-bordered">';
						$_table .= '<tr>';
						$_table .= '<td colspan="3"><strong>' . $packing->package_no . '</strong></td>';
						$_table .= '</tr>';
						$_table .= '<tr>';
						$_table .= '<td colspan="3">' . sprintf( lang( 'total_x_items_of_package' ), $packing->package_items_count ) . '</td>';
						$_table .= '</tr>';

						$_table .= '<tr>';
						$_table .= '<td>' . lang( 'no.' ) . '</td>';
						$_table .= '<td>' . lang( 'name' ) . '</td>';
						$_table .= '<td>' . lang( 'quantity' ) . '</td>';
						$_table .= '</tr>';

						foreach ( $deliveries->getPackedItemsByPackageID( $packing->id ) as $pac_item ) {
							$_table .= '<tr>';
							$_table .= '<td>' . ++ $i . '</td>';
							$_table .= '<td>' . $pac_item[0]->product_code . ' - ' . $pac_item[0]->product_name . '</td>';
							$_table .= '<td>' . $this->rerp->formatQuantity( $pac_item[0]->quantity ) . ' ' . $pac_item[0]->product_unit_code . '</td>';
							$_table .= '</tr>';
						}

						if ( $deliveries->getReturnItemsByPackage_id( $packing->id ) ) {
							$_table .= '<tr class="warning"><td colspan="100%" class="no-border"><strong>' . lang( 'returned_items' ) . '</strong></td></tr>';
							foreach ( $deliveries->getReturnItemsByPackage_id( $packing->id ) as $pac_item ) {
								$_table .= '<tr class="warning">';
								$_table .= '<td>' . ++ $i . '</td>';
								$_table .= '<td>' . $pac_item->product_code . ' - ' . $pac_item->product_name . '</td>';
								$_table .= '<td>' . $this->rerp->formatQuantity( $pac_item->quantity ) . ' ' . $pac_item->product_unit_code . '</td>';
								$_table .= '</tr>';
							}
						}

						$_table .= '</table>';
					}
				} else {
					$_table .= '<div class="alert alert-danger" role="alert">' . lang( 'package_not_found_warning_alert' ) . '</div>';
				}

				$_table .= '</td>';
				$_table .= '</tr>';
				$_table .= '</tbody>';
				$_table .= '</table>';
			}
		} elseif ( $pickup ) {
			foreach ( $pickup as $pick ) {
				$items     = $sales->getAllInvoiceItems( $pick->sale_id );
				$warehouse = $this->site->getWarehouseByID( $sale->warehouse_id );
				$_table    .= '<table class="table table-striped table-bordered">';
				$_table    .= '<thead>';
				$_table    .= '<tr>';
				$_table    .= '<th colspan="2">' . $pick->pickup_no . '</th>';
				$_table    .= '</tr>';
				$_table    .= '</thead>';

				$_table .= '<tbody>';
				$_table .= '<tr>';
				$_table .= '<td>' . sprintf( lang( 'pickup_x' ), lang( 'date' ) ) . ': ' . $this->rerp->hrld( $pick->pickup_date ) . '</td>';
				$_table .= '</tr>';
				$_table .= '<tr>';
				$_table .= '<td >';

				$_table .= '<div class="well well-sm">';
				$_table .= lang( 'warehouse' ) . ': ' . $warehouse->name . ' (' . $warehouse->code . ')';
				$_table .= '</div>
						<div class="table-responsive">
						<table class="table table-striped table-bordered">
						<thead>
		                    <tr>
		                        <th style="text-align: left"> ' . lang( 'no.' ) . '</th>
		                        <th>' . lang( 'name' ) . '</th>
		                        <th>' . lang( 'quantity' ) . '</th>
	                        </tr>
	                    </thead>
	                    <tbody>';
				$i      = 0;
				foreach ( $items as $item ) {
					$_table .= '<tr>';
					$_table .= '<td>' . ++ $i . '</td>';
					$_table .= '<td>' . $item->product_code . ' - ' . $item->product_name . '</td>';
					$_table .= '<td>' . $this->rerp->formatQuantity( $item->quantity ) . ' ' . $item->product_unit_code . '</td>';
					$_table .= '</tr>';
				}
				$_table .= '</tbody>
		                </table>
		            </div>';

				$_table .= '</td>';
				$_table .= '</tr>';
				$_table .= '</tbody>';
				$_table .= '</table>';
			}
		} else {
			$_table .= '<div class="alert alert-danger" role="alert">' . lang( 'shipment_not_found_warning_alert' ) . '</div>';
		}
		$this->data['full_table'] = $_table;

		$this->data['delivery']   = $deli;
		$this->data['biller']     = $this->site->getCompanyByID( $sale->biller_id );
		$this->data['rows']       = $this->sales_model->getAllInvoiceItemsWithDetails( $deli->sale_id );
		$this->data['user']       = $this->site->getUser( $deli->created_by );
		$this->data['page_title'] = lang( 'delivery_order' );

		$this->load->view( $this->theme . 'delivery/view_delivery', $this->data );
	}

	public function sale_suggestions() {
		$term = $this->input->get( 'term', TRUE );

		if ( strlen( $term ) < 1 || ! $term ) {
			die( "<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url( 'welcome' ) . "'; }, 10);</script>" );
		}

		$analyzed  = $this->rerp->analyze_term( $term );
		$sr        = $analyzed['term'];
		$option_id = $analyzed['option_id'];
		$sr        = addslashes( $sr );

		$rows = $this->delivery_model->getSaleSuggestions( $sr );

		if ( $rows ) {
			foreach ( $rows as $row ) {
				$c    = sha1( uniqid( mt_rand(), TRUE ) );
				$pr[] = [
					'id'                => $c,
					'sale_id'           => $row->id,
					'sale_reference_no' => $row->reference_no . ' (' . $row->name . ')',
					'row'               => $row
				];
			}
			$this->rerp->send_json( $pr );
		} else {
			$this->rerp->send_json( [ [ 'id' => 0, 'label' => lang( 'no_match_found' ), 'value' => $term ] ] );
		}
	}

	public function delayed_delivery() {

	}

	public function sales_not_delivered() {

	}

	public function shipment_list() {
		$this->rerp->checkPermissions();

		$data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$bc            = [
			[ 'link' => base_url(), 'page' => lang( 'home' ) ],
			[ 'link' => admin_url( 'delivery' ), 'page' => lang( 'delivery' ) ],
			[ 'link' => '#', 'page' => lang( 'shipment_list' ) ]
		];
		$meta          = [ 'page_title' => lang( 'deliveries' ), 'bc' => $bc ];
		$this->page_construct( 'delivery/shipment_list', $meta, $this->data );
	}

	public function pickup_list() {
		$this->rerp->checkPermissions();

		$data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$bc            = [
			[ 'link' => base_url(), 'page' => lang( 'home' ) ],
			[ 'link' => admin_url( 'delivery' ), 'page' => lang( 'delivery' ) ],
			[ 'link' => '#', 'page' => lang( 'pickup_list' ) ]
		];
		$meta          = [ 'page_title' => lang( 'deliveries' ), 'bc' => $bc ];
		$this->page_construct( 'delivery/pickup_list', $meta, $this->data );
	}

	public function getShipments() {
		$this->rerp->checkPermissions( 'deliveries' );

		$detail_link          = anchor( 'admin/delivery/view_shipment/$1', '<i class="fa fa-file-text-o"></i> ' . lang( 'shipment_details' ), 'data-toggle="modal" data-target="#myModal"' );
		$delivery_detail_link = anchor( 'admin/delivery/view_delivery/$2', '<i class="fa fa-file-text-o"></i> ' . lang( 'delivery_details' ), 'data-toggle="modal" data-target="#myModal"' );
		$sales_invoice        = anchor( 'admin/sales/modal_view/$3', '<i class="fa fa-file-text-o"></i> ' . lang( 'sales_invoice' ), 'data-toggle="modal" data-target="#myModal"' );
		$payments_link        = anchor( 'admin/sales/payments/$3', '<i class="fa fa-money"></i> ' . lang( 'view_payments' ), 'data-toggle="modal" data-target="#myModal"' );
		$add_payment_link     = anchor( 'admin/sales/add_payment/$3?reference_no=$4&amount=$5', '<i class="fa fa-money"></i> ' . lang( 'add_payment' ), 'data-toggle="modal" data-target="#myModal"' );
		$edit_link            = anchor( 'admin/delivery/edit_shipment/$1', '<i class="fa fa-edit"></i> ' . lang( 'edit_shipment' ) );
		$return_link          = anchor( 'admin/sales/return_sale/$3/$1', '<i class="fa fa-angle-double-left"></i> ' . lang( 'add_return' ) );
		$delete_link          = "<a href='#' class='po' title='<b>" . lang( 'delete_shipment' ) . "</b>' data-content=\"<p>"
		                        . lang( 'r_u_sure' ) . "</p><a class='btn btn-danger po-delete' href='" . admin_url( 'delivery/delete_shipment/$1' ) . "'>"
		                        . lang( 'i_m_sure' ) . "</a> <button class='btn po-close'>" . lang( 'no' ) . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
		                        . lang( 'delete_shipment' ) . '</a>';
		$action               = '<div class="text-center"><div class="btn-group text-left">'
		                        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
		                        . lang( 'actions' ) . ' <span class="caret"></span></button>
						    <ul class="dropdown-menu pull-right" role="menu">
						        <li>' . $detail_link . '</li>
						        <li>' . $edit_link . '</li>
						        <li class="divider"></li>
						        <li>' . $delivery_detail_link . '</li>
						        <li class="divider"></li>
						        <li>' . $sales_invoice . '</li>
						        <li class="divider"></li>
						        <li>' . $payments_link . '</li>
						        <li>' . $add_payment_link . '</li>
						        <li>' . $return_link . '</li>
						        <li class="divider"></li>
						        <li>' . $delete_link . '</li>
						    </ul>
						</div></div>';

		$t_shipment      = $this->db->dbprefix( 'shipment' );
		$t_delivery      = $this->db->dbprefix( 'deliveries' );
		$t_sales         = $this->db->dbprefix( 'sales' );
		$t_package       = $this->db->dbprefix( 'package' );
		$t_package_items = $this->db->dbprefix( 'package_items' );
		$t_sale_items    = $this->db->dbprefix( 'sale_items' );
		$t_payments      = $this->db->dbprefix( 'payments' );

		$ship_amount  = "( SELECT {$t_package}.shipment_id, SUM(package_item.subtotal) as total FROM {$t_package}  JOIN (SELECT {$t_package_items}.package_id, {$t_sale_items}.*  FROM {$t_package_items} JOIN {$t_sale_items} ON {$t_sale_items}.id = {$t_package_items}.sales_item_id) package_item ON package_item.package_id = {$t_package}.id GROUP BY {$t_package}.shipment_id ) shipping_cost";
		$shipment_pay = "( SELECT {$t_shipment}.id as shipment_id, SUM({$t_payments}.amount) as amount FROM {$t_shipment} JOIN {$t_payments} ON {$t_payments}.reference_no = {$t_shipment}.shipment_no GROUP BY {$t_shipment}.id ) shipment_pay";

		$this->load->library( 'datatables' );
		$this->datatables
			->select( $t_shipment . '.id as id, 
						       ' . $t_shipment . '.delivery_id as delivery_id, 
						       ' . $t_sales . '.id as sale_id, 
						       ' . $t_shipment . '.shipment_date as shipment_date, 
						       ' . $t_shipment . '.shipment_no as shipment_reference_no, 
						       ' . $t_delivery . '.do_reference_no as delivery_reference_no, 
						       ' . $t_sales . '.reference_no as sale_reference_no, 
						       COALESCE(shipping_cost.total, \'0\') as shipment_cost, 
						       ' . $t_shipment . '.cost_adjustment as cost_adjustment, 
						       (shipping_cost.total + ' . $t_shipment . '.cost_adjustment) as total_amount, 
						       ' . $t_shipment . '.status as shipment_status, 
						       (CASE WHEN shipment_pay.amount = (shipping_cost.total + ' . $t_shipment . '.cost_adjustment) THEN \'paid\' WHEN shipment_pay.amount < (shipping_cost.total + ' . $t_shipment . '.cost_adjustment) THEN \'due\' ELSE \'pending\' END) as payment_status', FALSE )
			->join( 'deliveries', 'deliveries.id = shipment.delivery_id', 'left' )
			->join( 'sales', 'sales.id = shipment.sale_id', 'left' )
			->join( $ship_amount, 'shipping_cost.shipment_id = shipment.id', 'left' )
			->join( $shipment_pay, 'shipment_pay.shipment_id = shipment.id', 'left' )
			->from( 'shipment' )
			->where( $t_shipment . '.delete_flag', 0 )
			->add_column( 'Actions', $action, 'id, delivery_id, sale_id, shipment_reference_no, total_amount' )
			->unset_column( 'delivery_id' )
			->unset_column( 'sale_id' );

		echo $this->datatables->generate();
	}

	public function getPickupList() {
		$this->rerp->checkPermissions( 'deliveries' );

		$detail_link      = anchor( 'admin/delivery/view_pickup/$1', '<i class="fa fa-file-text-o"></i> ' . sprintf( lang( 'pickup_x' ), lang( 'details' ) ), 'data-toggle="modal" data-target="#myModal"' );
		$edit_link        = anchor( 'admin/delivery/edit_pickup/$1', '<i class="fa fa-edit"></i> ' . sprintf( lang( 'edit_x' ), lang( 'pickup' ) ) );
		$sales_invoice    = anchor( 'admin/sales/modal_view/$2', '<i class="fa fa-file-text-o"></i> ' . lang( 'sales_invoice' ), 'data-toggle="modal" data-target="#myModal"' );
		$payments_link    = anchor( 'admin/sales/payments/$2', '<i class="fa fa-money"></i> ' . lang( 'view_payments' ), 'data-toggle="modal" data-target="#myModal"' );
		$add_payment_link = anchor( 'admin/sales/add_payment/$2?reference_no=$3&amount=$4', '<i class="fa fa-money"></i> ' . lang( 'add_payment' ), 'data-toggle="modal" data-target="#myModal"' );
		$return_link      = anchor( 'admin/sales/return_sale/$2', '<i class="fa fa-angle-double-left"></i> ' . lang( 'add_return' ) );
		$delete_link      = "<a href='#' class='po' title='<b>" . lang( 'delete_pickup' ) . "</b>' data-content=\"<p>"
		                    . lang( 'r_u_sure' ) . "</p><a class='btn btn-danger po-delete' href='" . admin_url( 'delivery/delete_pickup/$1' ) . "'>"
		                    . lang( 'i_m_sure' ) . "</a> <button class='btn po-close'>" . lang( 'no' ) . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
		                    . sprintf( lang( 'delete_x' ), lang( 'pickup' ) ) . '</a>';
		$action           = '<div class="text-center"><div class="btn-group text-left">'
		                    . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
		                    . lang( 'actions' ) . ' <span class="caret"></span></button>
						    <ul class="dropdown-menu pull-right" role="menu">
						        <li>' . $detail_link . '</li>
						        <li>' . $edit_link . '</li>
						        <li class="divider"></li>
						        <li>' . $sales_invoice . '</li>
						        <li>' . $payments_link . '</li>
						        <li>' . $add_payment_link . '</li>
						        <li>' . $return_link . '</li>
						        <li class="divider"></li>
						        <li>' . $delete_link . '</li>
						    </ul>
						</div></div>';

		$t_pickup   = $this->db->dbprefix( 'pickup' );
		$t_delivery = $this->db->dbprefix( 'deliveries' );
		$t_sales    = $this->db->dbprefix( 'sales' );

		$this->load->library( 'datatables' );
		$this->datatables
			->select( $t_pickup . '.id as id, ' . $t_pickup . '.sale_id as sale_id, ' . $t_pickup . '.pickup_date as pickup_date, ' . $t_pickup . '.pickup_no as pickup_reference_no, ' . $t_delivery . '.do_reference_no as delivery_reference_no, ' . $t_sales . '.reference_no as sale_reference_no, ' . $t_sales . '.grand_total as total_amount, ' . $t_pickup . '.status as pickup_status, ' . $t_sales . '.payment_status', FALSE )
			->join( 'deliveries', 'deliveries.id = pickup.delivery_id', 'left' )
			->join( 'sales', 'sales.id = pickup.sale_id', 'left' )
			->from( 'pickup' )
			->where( $t_pickup . '.delete_flag', 0 )
			->add_column( 'Actions', $action, 'id, sale_id, pickup_reference_no, total_amount' )
			->unset_column( 'id' )
			->unset_column( 'sale_id' );


		echo $this->datatables->generate();
	}

	public function shipment_actions() {
		if ( ! $this->Owner && ! $this->GP['bulk_actions'] ) {
			$this->session->set_flashdata( 'warning', lang( 'access_denied' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		$this->form_validation->set_rules( 'other_action', lang( 'form_action' ), 'required' );
		if ( $this->form_validation->run() == TRUE ) {
			if ( $this->input->post( 'other_action' ) == 'delete' ) {
				$this->delete_package( $this->input->post( 'delete_id' ) );
			} else {
				$this->session->set_flashdata( 'error', lang( 'item_not_selected' ) );
				redirect( $_SERVER['HTTP_REFERER'] );
			}
		} else {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
	}

	public function view_shipment( $id = NULL ) {
		$sales      = new Sales_model();
		$deliveries = new Delivery_model();

		$this->rerp->checkPermissions( 'deliveries' );

		if ( $this->input->get( 'id' ) ) {
			$id = $this->input->get( 'id' );
		}

		$this->data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );

		$ship = new Erp_Shipment( $id );

		if ( $ship->getId() !== $id ) {
			$this->session->set_flashdata( 'error', lang( 'shipment_not_found' ) );
			$this->rerp->md();
		}

		$sale     = $sales->getInvoiceByID( $ship->sale_id );
		$items    = $sales->getAllInvoiceItems( $ship->sale_id );
		$del_info = $deliveries->getDeliveryByID( $ship->delivery_id );
		$package  = $deliveries->getAllPackageByShipmentID( $ship->getId() );

		// $shipment_amount = $deliveries->getShipmentCostingBYShipment_id( $ship->getId() );

		if ( ! $del_info ) {
			$this->session->set_flashdata( 'error', lang( 'delivery_not_found' ) );
			$this->rerp->md();
		}
		if ( ! $sale ) {
			$this->session->set_flashdata( 'error', lang( 'sale_not_found' ) );
			$this->rerp->md();
		}

		$total_amount = 0;
		$_table       = NULL;
		if ( $package ) {
			foreach ( $package as $packing ) {
				// $package_amount = $deliveries->getPackageCostingBYPackage_id( $packing->id );
				$i      = 0;
				$_table .= '<table class="table table-striped table-bordered">';
				$_table .= '<thead>';

				$_table .= '<tr>';
				$_table .= '<th colspan="5">' . $packing->package_no . '</th>';
				$_table .= '</tr>';
				$_table .= '<tr>';
				$_table .= '<th colspan="5">' . sprintf( lang( 'total_x_items_of_package' ), $packing->package_items_count ) . '</th>';
				$_table .= '</tr>';

				$_table .= '<tr>';
				$_table .= '<th>' . lang( 'no.' ) . '</th>';
				$_table .= '<th>' . lang( 'name' ) . '</th>';
				$_table .= '<th style="text-align:center; width:100px;">' . lang( 'quantity' ) . '</th>';
				$_table .= '<th style="text-align:center; width:100px;">' . lang( 'unit_price' ) . '</th>';
				$_table .= '<th style="text-align:center; width:100px;">' . lang( 'subtotal' ) . '</th>';
				$_table .= '</tr>';
				$_table .= '</thead>';
				$_table .= '<tbody>';

				$total_sale   = 0;
				$total_return = 0;
				foreach ( $deliveries->getPackedItemsByPackageID( $packing->id ) as $pac_item ) {
					$_table     .= '<tr>';
					$_table     .= '<td>' . ++ $i . '</td>';
					$_table     .= '<td>' . $pac_item[0]->product_code . ' - ' . $pac_item[0]->product_name . '</td>';
					$_table     .= '<td style="text-align:center; width:120px;">' . $this->rerp->formatQuantity( $pac_item[0]->quantity ) . ' ' . $pac_item[0]->product_unit_code . '</td>';
					$_table     .= '<td style="text-align:right; width:120px;">' . $this->rerp->formatMoney( $pac_item[0]->unit_price ) . '</td>';
					$_table     .= '<td style="text-align:right; width:120px;">' . $this->rerp->formatMoney( $pac_item[0]->subtotal ) . '</td>';
					$_table     .= '</tr>';
					$total_sale += $pac_item[0]->subtotal;
				}
				if ( $deliveries->getReturnItemsByPackage_id( $packing->id ) ) {
					$_table .= '<tr class="warning"><td colspan="100%" class="no-border"><strong>' . lang( 'returned_items' ) . '</strong></td></tr>';
					foreach ( $deliveries->getReturnItemsByPackage_id( $packing->id ) as $pac_item ) {
						$_table       .= '<tr class="warning">';
						$_table       .= '<td>' . ++ $i . '</td>';
						$_table       .= '<td>' . $pac_item->product_code . ' - ' . $pac_item->product_name . '</td>';
						$_table       .= '<td style="text-align:center; width:120px;">' . $this->rerp->formatQuantity( $pac_item->quantity ) . ' ' . $pac_item->product_unit_code . '</td>';
						$_table       .= '<td style="text-align:right; width:120px;">' . $this->rerp->formatMoney( $pac_item->unit_price ) . '</td>';
						$_table       .= '<td style="text-align:right; width:120px;">' . $this->rerp->formatMoney( $pac_item->subtotal ) . '</td>';
						$_table       .= '</tr>';
						$total_return += $pac_item->subtotal;
					}
				}
				$_table       .= '<tr>';
				$_table       .= '<td colspan="4" style="text-align:right;"><strong>' . lang( 'total' ) . '</td>';
				$_table       .= '<td colspan="1" style="text-align:right;"><strong>' . $this->rerp->formatMoney( $total_sale + $total_return ) . '</strong></td>';
				$_table       .= '</tr>';
				$_table       .= '</tbody>';
				$_table       .= '</table>';
				$total_amount += $total_sale + $total_return;
			}
			$_table .= '<table class="table table-striped table-bordered"><tfoot>
                    <tr>
                    	<td colspan="4" style="text-align:right; font-weight:bold;">' . lang( 'shipping_cost' ) . '</td>
                    	<td style="text-align:right; font-weight:bold; width:120px;">' . $this->rerp->formatMoney( ( $total_amount > 0 ) ? $ship->cost_adjustment : 0 ) . '</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:right; font-weight:bold;">' . lang( 'total_amount' ) . '</td>
                        <td style="text-align:right; padding-right:10px; font-weight:bold; width:120px;">' . $this->rerp->formatMoney( $total_amount + ( ( $total_amount > 0 ) ? $ship->cost_adjustment : 0 ) ) . '</td>
                    </tr>
                    </tfoot></table>';

		}
		$this->data['full_table'] = $_table;

		$this->data['shipment']   = $ship;
		$this->data['delivery']   = $del_info;
		$this->data['biller']     = $this->site->getCompanyByID( $sale->biller_id );
		$this->data['user']       = $this->site->getUser( $del_info->created_by );
		$this->data['page_title'] = lang( 'shipment_order' );

		$this->load->view( $this->theme . 'delivery/view_shipment', $this->data );
	}

	public function view_pickup( $id = NULL ) {
		$sales      = new Sales_model();
		$deliveries = new Delivery_model();

		$this->rerp->checkPermissions( 'deliveries' );

		if ( $this->input->get( 'id' ) ) {
			$id = $this->input->get( 'id' );
		}

		$this->data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );

		$pickup = new Erp_Pickup( $id );

		if ( $pickup->getId() !== $id ) {
			$this->session->set_flashdata( 'error', lang( 'pickup_not_found' ) );
			$this->rerp->md();
		}

		$sale        = $sales->getInvoiceByID( $pickup->sale_id );
		$items       = $sales->getAllInvoiceItems( $pickup->sale_id );
		$return_rows = $sales->getAllInvoiceItems( $sale->return_id );
		$del_info    = $deliveries->getDeliveryByID( $pickup->delivery_id );

		if ( ! $del_info ) {
			$this->session->set_flashdata( 'error', lang( 'delivery_not_found' ) );
			$this->rerp->md();
		}
		if ( ! $sale ) {
			$this->session->set_flashdata( 'error', lang( 'sale_not_found' ) );
			$this->rerp->md();
		}

		$this->data['warehouse'] = $this->site->getWarehouseByID( $sale->warehouse_id );

		foreach ( $items as $item ) {
			$packaging[] = [
				'name'     => $item->product_code . ' - ' . $item->product_name,
				'quantity' => $item->quantity,
				'unit'     => $item->product_unit_code,
				'rack'     => $this->sales_model->getItemRack( $item->product_id, $sale->warehouse_id ),
			];
		}
		$this->data['packaging'] = $packaging;

		$this->data['returned'] = FALSE;
		if ( $sale->sale_status == 'returned' || $sale->return_id ) {
			foreach ( $return_rows as $reitem ) {
				$return_packaging[] = [
					'name'     => $reitem->product_code . ' - ' . $reitem->product_name,
					'quantity' => $reitem->quantity,
					'unit'     => $reitem->product_unit_code,
					'rack'     => $this->sales_model->getItemRack( $reitem->product_id, $sale->warehouse_id ),
				];
			}
			$this->data['return_packaging'] = $return_packaging;
			$this->data['returned']         = TRUE;
		}

		$this->data['pickup']     = $pickup;
		$this->data['delivery']   = $del_info;
		$this->data['biller']     = $this->site->getCompanyByID( $sale->biller_id );
		$this->data['user']       = $this->site->getUser( $del_info->created_by );
		$this->data['page_title'] = lang( 'pickup_order' );

		$this->load->view( $this->theme . 'delivery/view_pickup', $this->data );
	}

	public function add_shipment() {
		$this->rerp->checkPermissions( 'deliveries', TRUE );

		if ( $this->input->get( 'id' ) ) {
			$this->data['id'] = $this->input->get( 'id' );
		}

		$deliveries                 = new Delivery_model();
		$delivery_ref               = $deliveries->getAllPendingDeliveryRefNo();
		$this->data['delivery_ref'] = $delivery_ref;

		$my_retail_model = new MY_RetailErp_Model();
		$date            = NULL;

		if ( $this->Owner || $this->Admin ) {
			$date = $this->rerp->fld( date( 'Y-m-d H:i:s' ) );
		} else {
			$date = date( 'Y-m-d H:i:s' );
		}

		$this->form_validation->set_rules( 'shipment_date', lang( 'shipment_date' ), 'required' );
		$this->form_validation->set_rules( 'shipment_no', lang( 'shipment_reference_no' ), 'required' );
		$this->form_validation->set_rules( 'delivery_id', lang( 'do_reference_no' ), 'greater_than[0]', array( 'greater_than[0]' => lang( 'please_select_delivery_reference_number' ) ) );

		if ( $this->form_validation->run() == TRUE ) {
			$shipment_date = $this->rerp->fld( trim( $this->input->post( 'shipment_date' ) ) );

			$delivery_id = $this->input->post( 'delivery_id' );
			$del_info    = $deliveries->getDeliveryByID( $delivery_id );

			$erp_shipment = new Erp_Shipment();
			$erp_shipment->setShipmentDate( $shipment_date );
			$erp_shipment->setShipmentNo( $this->input->post( 'shipment_no' ) );
			$erp_shipment->setSaleId( $del_info->sale_id );
			$erp_shipment->setDeliveryId( $delivery_id );

			if ( $erp_shipment->save() && $id = $erp_shipment->getId() ) {
				$this->session->set_flashdata( 'message', 'Shipment Created Successfully' );
				admin_redirect( 'delivery/edit_shipment/' . $id );
			}
		} elseif ( $this->input->post( 'create_shipment' ) ) {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$this->data['date']                  = $date;
		$this->data['shipment_reference_no'] = $my_retail_model->get_sequential_reference( 'SH' );

		$this->data['error']    = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$this->data['modal_js'] = $this->site->modal_js();
		$this->load->view( $this->theme . 'delivery/add_shipment', $this->data );

	}

	public function add_pickup() {
		$this->rerp->checkPermissions( 'deliveries', TRUE );

		if ( $this->input->get( 'id' ) ) {
			$this->data['id'] = $this->input->get( 'id' );
		}

		$deliveries                 = new Delivery_model();
		$delivery_ref               = $deliveries->getAllPendingDeliveryRefNoForPickup();
		$this->data['delivery_ref'] = $delivery_ref;

		$my_retail_model = new MY_RetailErp_Model();
		$date            = NULL;

		if ( $this->Owner || $this->Admin ) {
			$date = $this->rerp->fld( date( 'Y-m-d H:i:s' ) );
		} else {
			$date = date( 'Y-m-d H:i:s' );
		}

		$this->form_validation->set_rules( 'pickup_date', lang( 'pickup_date' ), 'required' );
		$this->form_validation->set_rules( 'pickup_no', lang( 'pickup_reference_no' ), 'required' );
		$this->form_validation->set_rules( 'delivery_id', lang( 'do_reference_no' ), 'greater_than[0]|is_unique[pickup.delivery_id]' );

		if ( $this->form_validation->run() == TRUE ) {
			$pickup_date = $this->rerp->fld( trim( $this->input->post( 'pickup_date' ) ) );

			$delivery_id = $this->input->post( 'delivery_id' );
			$del_info    = $deliveries->getDeliveryByID( $delivery_id );
			$sale        = $this->site->getSaleByID( $del_info->sale_id );

			$erp_pickup = new Erp_Pickup();
			$erp_pickup->setPickupDate( $pickup_date );
			$erp_pickup->setPickupNo( $this->input->post( 'pickup_no' ) );
			$erp_pickup->setSaleId( $del_info->sale_id );
			$erp_pickup->setDeliveryId( $delivery_id );
			$erp_pickup->setWarehouseId( $sale->warehouse_id );

			if ( $erp_pickup->save() && $id = $erp_pickup->getId() ) {
				$this->session->set_flashdata( 'message', 'Pickup Created Successfully' );
				admin_redirect( 'delivery/edit_pickup/' . $id );
			}
		} elseif ( $this->input->post( 'create_pickup' ) ) {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$this->data['date']                = $date;
		$this->data['pickup_reference_no'] = $my_retail_model->get_sequential_reference( 'PICK' );

		$this->data['error']    = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$this->data['modal_js'] = $this->site->modal_js();
		$this->load->view( $this->theme . 'delivery/add_pickup', $this->data );

	}

	public function edit_shipment( $id = NULL ) {
		$this->rerp->checkPermissions( 'deliveries', TRUE );

		if ( $id == NULL ) {
			$this->session->set_flashdata( 'message', 'Nothing is selected' );
			admin_redirect( 'delivery/shipment_list' );
		}

		$my_retail_model = new MY_RetailErp_Model();
		$shipment        = new Erp_Shipment( $id );
		$sales           = new Sales_model();
		$deliveries      = new Delivery_model();

		$sale           = $sales->getInvoiceByID( $shipment->sale_id );
		$items          = $sales->getAllInvoiceItems( $shipment->sale_id );
		$del_info       = $deliveries->getDeliveryByID( $shipment->delivery_id );
		$package        = $deliveries->getAllPackageByShipmentID( $shipment->getId() );
		$unpacked_items = $deliveries->getUnpackedItemsBySaleID( $shipment->sale_id );
		$shipping_cost  = $deliveries->getShippingCostAdjustmentBYSale_id( $shipment->sale_id );

		// packed list or unpacked list by sale_id
		$_table = NULL;
		if ( $package ) {
			foreach ( $package as $packing ) {
				if ( ! $packing->shipment_id ) {
					$_table .= '<div class="alert alert-warning" role="alert">' . sprintf( lang( 'please_add_this_package_x' ), $packing->package_no ) . '</div>';
				}
				$_table .= '<table class="table table-striped table-bordered">';
				$_table .= '<thead>';

				$_table .= '<tr>';
				$_table .= '<th>';
				if ( $packing->shipment_id ) {
					$_table .= '<input class="checkbox multi-select input-xs" type="checkbox" name="packval[]" value="1" checked="checked">';
				}
				$_table .= '</th>';
				$_table .= '<th>' . $packing->package_no . '</th>';

				$_table .= '<th><ul class="btn-tasks">
								                <li class="dropdown">
								                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
								                        <i class="icon fa fa-tasks tip" data-placement="left" title="' . lang( 'actions' ) . '" style="color: white"></i>
								                    </a>
								                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">';
				if ( ! $packing->shipment_id ) {
					$_table .= '<li><a href="' . admin_url( 'delivery/add_package_for_shipment/' . $id . '/' . $packing->id ) . '"><i class="fa fa-plus"></i> ' . sprintf( lang( 'add_x' ), lang( 'package_for_shipment' ) ) . '</a>';
				} else {
					$_table .= '<li>
								                            <a href="' . admin_url( 'delivery/edit_package/' . $packing->id ) . '">
								                                <i class="fa fa-edit"></i> ' . sprintf( lang( 'edit_x' ), lang( 'package' ) ) . '
								                            </a>
								                        </li>';
				}
				$_table .= '</ul>
								                </li>
								            </ul>
								</th>';

				$_table .= '</tr>';
				$_table .= '<tr>';
				$_table .= '<th colspan="3">' . sprintf( lang( 'total_x_items_of_package' ), $packing->package_items_count ) . '</th>';
				$_table .= '</tr>';

				$_table .= '<tr>';
				$_table .= '<th>&nbsp;</th>';
				$_table .= '<th>' . lang( 'name' ) . '</th>';
				$_table .= '<th>' . lang( 'quantity' ) . '</th>';
				$_table .= '</tr>';
				$_table .= '</thead>';
				$_table .= '<tbody>';
				foreach ( $this->sales_model->getPackedItemsByPackageID( $packing->id ) as $pac_item ) {
					$_table .= '<tr>';
					$_table .= '<td><input class="checkbox multi-select input-xs" type="checkbox" name="val[]" value="' . $pac_item[0]->id . '" checked="checked" readonly disabled></td>';
					$_table .= '<td>' . $pac_item[0]->product_code . ' - ' . $pac_item[0]->product_name . '</td>';
					$_table .= '<td>' . $this->rerp->formatQuantity( $pac_item[0]->quantity ) . ' ' . $pac_item[0]->product_unit_code . '</td>';
					$_table .= '</tr>';
				}
				if ( $deliveries->getReturnItemsByPackage_id( $packing->id ) ) {
					$_table .= '<tr class="warning"><td colspan="100%" class="no-border"><strong>' . lang( 'returned_items' ) . '</strong></td></tr>';
					foreach ( $deliveries->getReturnItemsByPackage_id( $packing->id ) as $pac_item ) {
						$_table .= '<tr class="warning">';
						$_table .= '<td>&nbsp;</td>';
						$_table .= '<td>' . $pac_item->product_code . ' - ' . $pac_item->product_name . '</td>';
						$_table .= '<td>' . $this->rerp->formatQuantity( $pac_item->quantity ) . ' ' . $pac_item->product_unit_code . '</td>';
						$_table .= '</tr>';
					}
				}
				$_table .= '</tbody>';
				$_table .= '</table>';
			}

		}
		if ( count( $unpacked_items ) ) {
			$_table .= '<div class="alert alert-danger" role="alert">' . lang( 'unpacked_list_warning_alert' ) . '</div>';
			$_table .= admin_form_open( 'delivery/packaging_action', 'id="action-form"' );
			$_table .= '<table class="table table-striped table-bordered">';
			$_table .= '<thead>';

			$_table .= '<tr>';
			$_table .= '<th>';
			$_table .= '</th>';
			$_table .= '<th>' . lang( 'unpacked_items' ) . '</th>';

			$_table .= '<th><ul class="btn-tasks">
								                <li class="dropdown">
								                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
								                        <i class="icon fa fa-tasks tip" data-placement="left" title="' . lang( 'actions' ) . '" style="color: white"></i>
								                    </a>
								                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
								                        <li>';
			$_table .= '<a href="#" id="add_package" data-action="add_package"><i class="fa fa-gift"></i> ' . lang( 'add_packaging' ) . '</a>';
			$_table .= '</li>';
			$_table .= '</ul>
								                </li>
								            </ul>
								</th>';
			$_table .= '</tr>';
			$_table .= '<tr>';
			$_table .= '<th colspan="3">' . sprintf( lang( 'total_x_unpacked_items' ), count( $unpacked_items ) ) . '</th>';
			$_table .= '</tr>';

			$_table .= '<tr>';
			$_table .= '<th>&nbsp;</th>';
			$_table .= '<th>' . lang( 'name' ) . '</th>';
			$_table .= '<th>' . lang( 'quantity' ) . '</th>';
			$_table .= '</tr>';
			$_table .= '</thead>';
			$_table .= '<tbody>';
			foreach ( $items as $item ) {
				if ( in_array( $item->id, $unpacked_items ) ) {
					$_table .= '<tr>';
					$_table .= '<td><input class="checkbox multi-select input-xs" type="checkbox" name="val[]" value="' . $item->id . '" ></td>';
					$_table .= '<td>' . $item->product_code . ' - ' . $item->product_name . '</td>';
					$_table .= '<td>' . $this->rerp->formatQuantity( $item->quantity ) . ' ' . $item->product_unit_code . '</td>';
					$_table .= '</tr>';
				}
			}
			$_table .= '</tbody>';
			$_table .= '</table>';
			$_table .= '<div style="display: none;">
                    <input type="hidden" name="form_action" value="" id="form_action" class="input-xs">
                    <input type="hidden" name="sale_id" value="' . $shipment->sale_id . '" class="input-xs">
                    <input type="hidden" name="shipment_id" value="' . $shipment->getId() . '" class="input-xs">
                    <input type="submit" name="performAction" value="performAction" id="action-form-submit"
                           class="input-xs">
                </div>';


			$_table .= '<div class="btn-group">
                            <a href="#" id="add_package" data-action="add_package" class="tip btn btn-primary tip title="' . lang( 'add_packaging' ) . '">
                                <i class="fa fa fa-gift"></i> <span
                                        class="hidden-sm hidden-xs">' . lang( 'add_packaging' ) . '</span>
                            </a>
                        </div>';
		}
		$this->data['full_table'] = $_table;
		// end of packed list or unpacked_list

		$date = NULL;
		if ( $this->Owner || $this->Admin ) {
			$date = $this->rerp->fld( date( 'Y-m-d H:i:s' ) );
		} else {
			$date = date( 'Y-m-d H:i:s' );
		}

		$this->form_validation->set_rules( 'shipment_date', lang( 'shipment_date' ), 'required' );
		$this->form_validation->set_rules( 'shipment_no', lang( 'shipment_reference_no' ), 'required' );

		if ( $this->form_validation->run() == TRUE ) {
			$shipment_date = $this->rerp->fld( trim( $this->input->post( 'shipment_date' ) ) );
			$erp_shipment  = new Erp_Shipment( $id );
			$erp_shipment->setShipmentDate( $shipment_date );
			$erp_shipment->setShipmentNo( $this->input->post( 'shipment_no' ) );
			$erp_shipment->setSaleId( $shipment->sale_id );
			$erp_shipment->setDeliveryId( $shipment->delivery_id );
			$erp_shipment->setCostAdjustment( $this->input->post( 'cost_adjustment' ) );
			$erp_shipment->setStatus( $this->input->post( 'status' ) );

			if ( $erp_shipment->save() ) {
				$this->session->set_flashdata( 'message', 'Shipment Updated Successfully' );
				admin_redirect( 'delivery/edit_shipment/' . $id );
			}
		} elseif ( $this->input->post( 'edit_shipment' ) ) {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$this->data['date']                  = $date;
		$this->data['shipment_reference_no'] = $my_retail_model->get_sequential_reference( 'SH' );
		$this->data['shipment']              = $shipment;
		$this->data['sale']                  = $sale;
		$this->data['delivery']              = $del_info;
		$this->data['package']               = $package;
		$this->data['shipping_cost']         = $shipping_cost;


		$bc   = [
			[ 'link' => base_url(), 'page' => lang( 'home' ) ],
			[ 'link' => admin_url( 'delivery' ), 'page' => lang( 'delivery' ) ],
			[ 'link' => admin_url( 'delivery/shipment_list' ), 'page' => lang( 'shipment_list' ) ],
			[ 'link' => '#', 'page' => lang( 'edit_shipment' ) ]
		];
		$meta = [ 'page_title' => lang( 'edit_shipment' ), 'bc' => $bc ];
		$this->page_construct( 'delivery/edit_shipment', $meta, $this->data );
	}

	public function edit_pickup( $id = NULL ) {
		$this->rerp->checkPermissions( 'deliveries', TRUE );

		if ( $id == NULL ) {
			$this->session->set_flashdata( 'message', 'Nothing is selected' );
			admin_redirect( 'delivery/pickup_list' );
		}

		$my_retail_model = new MY_RetailErp_Model();
		$pickup          = new Erp_Pickup( $id );
		$sales           = new Sales_model();
		$deliveries      = new Delivery_model();

		$sale        = $sales->getInvoiceByID( $pickup->sale_id );
		$items       = $sales->getAllInvoiceItems( $pickup->sale_id );
		$return_rows = $sales->getAllInvoiceItems( $sale->return_id );
		$del_info    = $deliveries->getDeliveryByID( $pickup->delivery_id );

		$date = NULL;
		if ( $this->Owner || $this->Admin ) {
			$date = $this->rerp->fld( date( 'Y-m-d H:i:s' ) );
		} else {
			$date = date( 'Y-m-d H:i:s' );
		}

		$this->form_validation->set_rules( 'pickup_date', lang( 'pickup_date' ), 'required' );
		$this->form_validation->set_rules( 'pickup_no', lang( 'pickup_reference_no' ), 'required' );

		if ( $this->form_validation->run() == TRUE ) {
			$pickup_date = $this->rerp->fld( trim( $this->input->post( 'pickup_date' ) ) );
			$erp_pickup  = new Erp_Pickup( $id );
			$erp_pickup->setPickupDate( $pickup_date );
			$erp_pickup->setPickupNo( $this->input->post( 'pickup_no' ) );
			$erp_pickup->setSaleId( $pickup->sale_id );
			$erp_pickup->setDeliveryId( $pickup->delivery_id );
			$erp_pickup->setStatus( $this->input->post( 'status' ) );

			if ( $erp_pickup->save() ) {
				$this->session->set_flashdata( 'message', 'Pickup Information Updated Successfully' );
				admin_redirect( 'delivery/edit_pickup/' . $id );
			}
		} elseif ( $this->input->post( 'edit_pickup' ) ) {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$this->data['warehouse'] = $this->site->getWarehouseByID( $sale->warehouse_id );

		foreach ( $items as $item ) {
			$packaging[] = [
				'name'     => $item->product_code . ' - ' . $item->product_name,
				'quantity' => $item->quantity,
				'unit'     => $item->product_unit_code,
				'rack'     => $this->sales_model->getItemRack( $item->product_id, $sale->warehouse_id ),
			];
		}
		$this->data['packaging'] = $packaging;

		$this->data['returned'] = FALSE;
		if ( $sale->sale_status == 'returned' || $sale->return_id ) {
			foreach ( $return_rows as $reitem ) {
				$return_packaging[] = [
					'name'     => $reitem->product_code . ' - ' . $reitem->product_name,
					'quantity' => $reitem->quantity,
					'unit'     => $reitem->product_unit_code,
					'rack'     => $this->sales_model->getItemRack( $reitem->product_id, $sale->warehouse_id ),
				];
			}
			$this->data['return_packaging'] = $return_packaging;
			$this->data['returned']         = TRUE;
		}

		$this->data['date']                = $date;
		$this->data['pickup_reference_no'] = $my_retail_model->get_sequential_reference( 'SH' );
		$this->data['pickup']              = $pickup;
		$this->data['sale']                = $sale;
		$this->data['delivery']            = $del_info;

		$bc   = [
			[ 'link' => base_url(), 'page' => lang( 'home' ) ],
			[ 'link' => admin_url( 'delivery' ), 'page' => lang( 'delivery' ) ],
			[ 'link' => admin_url( 'delivery/pickup_list' ), 'page' => lang( 'pickup_list' ) ],
			[ 'link' => '#', 'page' => lang( 'edit_pickup' ) ]
		];
		$meta = [ 'page_title' => lang( 'edit_pickup' ), 'bc' => $bc ];
		$this->page_construct( 'delivery/edit_pickup', $meta, $this->data );
	}

	public function delete_shipment( int $id ) {
		$this->rerp->checkPermissions( NULL, TRUE );
		if ( ! $id ) {
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		$shipment = new Erp_Shipment( $id );
		if ( $shipment->getId() ) {
			$shipment->setDeleteFlag( 1 );
			if ( $shipment->save() ) {
				if ( $this->input->is_ajax_request() ) {
					$this->rerp->send_json( [ 'error' => 0, 'msg' => lang( 'shipment_deleted' ) ] );
				}
				$this->session->set_flashdata( 'message', lang( 'shipment_deleted' ) );
				admin_redirect( 'delivery/shipment_list' );
			}
		} else {
			$this->session->set_flashdata( 'error', 'Sorry! Invalid Shipment' );
			admin_redirect( 'delivery/shipment_list' );
		}
	}

	public function delete_pickup( int $id ) {
		$this->rerp->checkPermissions( NULL, TRUE );
		if ( ! $id ) {
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		$shipment = new Erp_Pickup( $id );
		if ( $shipment->getId() ) {
			$shipment->setDeleteFlag( 1 );
			if ( $shipment->save() ) {
				if ( $this->input->is_ajax_request() ) {
					$this->rerp->send_json( [ 'error' => 0, 'msg' => lang( 'pickup_deleted' ) ] );
				}
				$this->session->set_flashdata( 'message', lang( 'pickup_deleted' ) );
				admin_redirect( 'delivery/pickup_list' );
			}
		} else {
			$this->session->set_flashdata( 'error', 'Sorry! Invalid Pickup' );
			admin_redirect( 'delivery/pickup_list' );
		}
	}

	public function packaging_list() {
		$this->rerp->checkPermissions();

		$data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$bc            = [
			[ 'link' => base_url(), 'page' => lang( 'home' ) ],
			[ 'link' => admin_url( 'delivery' ), 'page' => lang( 'delivery' ) ],
			[ 'link' => '#', 'page' => lang( 'packaging_list' ) ]
		];
		$meta          = [ 'page_title' => lang( 'packaging_list' ), 'bc' => $bc ];
		$this->page_construct( 'delivery/packaging_list', $meta, $this->data );
	}

	public function getPackagingList() {
		$this->rerp->checkPermissions( 'deliveries' );

		//$delivery_detail_link = anchor( 'admin/delivery/view_delivery/$2', '<i class="fa fa-file-text-o"></i> ' . lang( 'delivery_details' ), 'data-toggle="modal" data-target="#myModal"' );
		$edit_link   = anchor( 'admin/delivery/edit_package/$1', '<i class="fa fa-edit"></i> ' . lang( 'edit_package' ) );
		$delete_link = "<a href='#' class='po' title='<b>" . lang( 'delete_package' ) . "</b>' data-content=\"<p>"
		               . lang( 'r_u_sure' ) . "</p><a class='btn btn-danger po-delete' href='" . admin_url( 'delivery/delete_package/$1' ) . "'>"
		               . lang( 'i_m_sure' ) . "</a> <button class='btn po-close'>" . lang( 'no' ) . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
		               . lang( 'delete_package' ) . '</a>';
		$action      = '<div class="text-center"><div class="btn-group text-left">'
		               . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
		               . lang( 'actions' ) . ' <span class="caret"></span></button>
						    <ul class="dropdown-menu pull-right" role="menu">
						        <li>' . $edit_link . '</li>
						        <li class="divider"></li>						       
						        <li>' . $delete_link . '</li>
						    </ul>
						</div></div>';

		$t_package  = $this->db->dbprefix( 'package' );
		$t_shipment = $this->db->dbprefix( 'shipment' );
		$t_delivery = $this->db->dbprefix( 'deliveries' );
		$t_sales    = $this->db->dbprefix( 'sales' );

		$this->load->library( 'datatables' );
		$this->datatables
			->select( $t_package . '.id as id, ' . $t_package . '.delivery_id as delivery_id, ' . $t_package . '.sale_id as sale_id, ' . $t_package . '.package_no as package_reference_no, ' . $t_shipment . '.shipment_no as shipment_reference_no, ' . $t_delivery . '.do_reference_no as delivery_reference_no, ' . $t_sales . '.reference_no as sale_reference_no, CONCAT(' . $t_package . '.package_items_count, " Items") as package_items, ' . $t_package . '.status', FALSE )
			->join( 'shipment', 'shipment.id = package.shipment_id', 'left' )
			->join( 'deliveries', 'deliveries.id = package.delivery_id', 'left' )
			->join( 'sales', 'sales.id = package.sale_id', 'left' )
			->from( 'package' )
			->where( $t_package . '.delete_flag', 0 )
			->add_column( 'Actions', $action, 'id, delivery_id, sale_id' )
			->unset_column( 'id' )
			->unset_column( 'delivery_id' )
			->unset_column( 'sale_id' );

		echo $this->datatables->generate();
	}

	public function getPackageItemList( $package_id ) {
		$this->rerp->checkPermissions( 'deliveries' );

		$pi = $this->db->dbprefix( 'package_items' );
		$si = $this->db->dbprefix( 'sale_items' );

		$this->load->library( 'datatables' );
		$this->datatables
			->select( $pi . '.id as id, ' . $si . '.product_code as product_code, ' . $si . '.product_name as product_name, CONCAT(FORMAT(' . $si . '.quantity, 2), " (", ' . $si . '.product_unit_code, ")" ) as quantity', FALSE )
			->join( $si, $si . '.id = ' . $pi . '.sales_item_id', 'left' )
			->from( $pi )
			->where( $pi . '.package_id', $package_id )
			->add_column( 'Actions', "<div class=\"text-center\"><a href='#' class='tip po' title='<b>" . sprintf( lang( 'delete_x' ), lang( 'item' ) ) . "</b>' data-content=\"<p>" . lang( 'r_u_sure' ) . "</p><a class='btn btn-danger po-delete' href='" . admin_url( 'delivery/delete_package_item/$1' ) . "'>" . lang( 'i_m_sure' ) . "</a> <button class='btn po-close'>" . lang( 'no' ) . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id' )
			->unset_column( 'id' );

		echo $this->datatables->generate();
	}

	public function packaging( $id ) {
		$sale           = $this->sales_model->getInvoiceByID( $id );
		$items          = $this->sales_model->getAllInvoiceItems( $sale->id );
		$package        = $this->sales_model->getAllPackageBySaleID( $sale->id );
		$unpacked_items = $this->sales_model->getUnpackedItemsBySaleID( $sale->id );

		$this->data['returned'] = FALSE;
		if ( $sale->sale_status == 'returned' || $sale->return_id ) {
			$this->data['returned'] = TRUE;
		}
		$this->data['warehouse'] = $this->site->getWarehouseByID( $sale->warehouse_id );

		$_table = NULL;

		if ( $package ) {
			foreach ( $package as $packing ) {
				$i      = 0;
				$_table .= '<table class="table table-striped table-bordered">';
				$_table .= '<thead>';

				$_table .= '<tr>';
				$_table .= '<th colspan="3">' . $packing->package_no . '</th>';
				$_table .= '</tr>';
				$_table .= '<tr>';
				$_table .= '<th colspan="3">' . sprintf( lang( 'total_x_items_of_package' ), $packing->package_items_count ) . '</th>';
				$_table .= '</tr>';

				$_table .= '<tr>';
				$_table .= '<th>&nbsp;</th>';
				$_table .= '<th>' . lang( 'name' ) . '</th>';
				$_table .= '<th>' . lang( 'quantity' ) . '</th>';
				$_table .= '</tr>';
				$_table .= '</thead>';
				$_table .= '<tbody>';
				foreach ( $this->sales_model->getPackedItemsByPackageID( $packing->id ) as $pac_item ) {
					$_table .= '<tr>';
					$_table .= '<td>' . ++ $i . '</td>';
					$_table .= '<td>' . $pac_item[0]->product_code . ' - ' . $pac_item[0]->product_name . '</td>';
					$_table .= '<td>' . $this->rerp->formatQuantity( $pac_item[0]->quantity ) . ' ' . $pac_item[0]->product_unit_code . '</td>';
					$_table .= '</tr>';
				}
				$_table .= '</tbody>';
				$_table .= '</table>';
			}

		}

		if ( count( $unpacked_items ) ) {
			$_table .= '<table class="table table-striped table-bordered">';
			$_table .= '<thead>';

			$_table .= '<tr>';
			$_table .= '<th colspan="3">' . lang( 'unpacked_items' ) . '</th>';
			$_table .= '</tr>';
			$_table .= '<tr>';
			$_table .= '<th colspan="3">' . sprintf( lang( 'total_x_unpacked_items' ), count( $unpacked_items ) ) . '</th>';
			$_table .= '</tr>';

			$_table .= '<tr>';
			$_table .= '<th>&nbsp;</th>';
			$_table .= '<th>' . lang( 'name' ) . '</th>';
			$_table .= '<th>' . lang( 'quantity' ) . '</th>';
			$_table .= '</tr>';
			$_table .= '</thead>';
			$_table .= '<tbody>';
			foreach ( $items as $item ) {
				if ( in_array( $item->id, $unpacked_items ) ) {
					$_table .= '<tr>';
					$_table .= '<td><input class="checkbox multi-select input-xs" type="checkbox" name="val[]" value="' . $item->id . '" readonly disabled></td>';
					$_table .= '<td>' . $item->product_code . ' - ' . $item->product_name . '</td>';
					$_table .= '<td>' . $this->rerp->formatQuantity( $item->quantity ) . ' ' . $item->product_unit_code . '</td>';
					$_table .= '</tr>';
				}
			}
			$_table .= '</tbody>';
			$_table .= '</table>';
		}

		$this->data['full_table'] = $_table;
		$this->data['sale']       = $sale;

		$this->load->view( $this->theme . 'delivery/packaging', $this->data );
	}

	public function packaging_action() {
		if ( ! $this->Owner && ! $this->GP['bulk_actions'] ) {
			$this->session->set_flashdata( 'warning', lang( 'access_denied' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$this->form_validation->set_rules( 'form_action', lang( 'form_action' ), 'required' );

		if ( $this->form_validation->run() == TRUE ) {
			if ( ! empty( $_POST['val'] ) ) {
				if ( $this->input->post( 'form_action' ) == 'add_package' && ! empty( $this->input->post( 'sale_id' ) ) ) {
					$sale_id     = $this->input->post( 'sale_id' );
					$shipment_id = $this->input->post( 'shipment_id' );
					$item_id     = $_POST['val'];
					$package     = new Erp_package();
					$add_package = $package->add_package( $sale_id, $item_id );

					if ( $add_package ) {
						if ( $shipment_id ) {
							$this->add_package_for_shipment( $shipment_id, $add_package );
						}
						$this->session->set_flashdata( 'message', lang( 'package_added' ) );
						redirect( $_SERVER['HTTP_REFERER'] );
					} else {
						$this->session->set_flashdata( 'error', lang( 'package_not_added' ) );
						redirect( $_SERVER['HTTP_REFERER'] );
					}
				} elseif ( $this->input->post( 'form_action' ) == 'edit_package' && ! empty( $this->input->post( 'pack_id' ) ) ) {
					$delivery_model   = new Delivery_model();
					$package_id       = $this->input->post( 'pack_id' );
					$item_id          = $_POST['val'];
					$package          = new Erp_package( $package_id );
					$sale_id          = $package->sale_id;
					$add_package_item = $package->add_package_item( $package_id, $sale_id, $item_id );
					if ( $add_package_item ) {
						$all_pack_item = $delivery_model->getAllPackageItemsIDByPackageID( $package_id );
						$item_count    = count( $all_pack_item );
						$package->setPackageItemsCount( $item_count );
						$package->save();
						$this->session->set_flashdata( 'message', lang( 'item_added' ) );
						redirect( $_SERVER['HTTP_REFERER'] );
					} else {
						$this->session->set_flashdata( 'error', lang( 'item_not_added' ) );
						redirect( $_SERVER['HTTP_REFERER'] );
					}
				}

			} else {
				$this->session->set_flashdata( 'error', lang( 'item_not_selected' ) );
				redirect( $_SERVER['HTTP_REFERER'] );
			}
		} else {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
	}

	public function edit_package( $id = NULL ) {
		$this->rerp->checkPermissions( 'deliveries', TRUE );

		if ( $id == NULL ) {
			$this->session->set_flashdata( 'message', 'Nothing is selected' );
			admin_redirect( 'delivery/packaging_list' );
		}

		$package    = new Erp_package( $id );
		$sales      = new Sales_model();
		$deliveries = new Delivery_model();
		$shipment   = new Erp_Shipment( $package->shipment_id );

		$sale           = $sales->getInvoiceByID( $package->sale_id );
		$items          = $sales->getAllInvoiceItems( $package->sale_id );
		$del_info       = $deliveries->getDeliveryByID( $package->delivery_id );
		$unpacked_items = $deliveries->getUnpackedItemsBySaleID( $package->sale_id );

		// packed list or unpacked list by sale_id
		$_table = NULL;

		if ( count( $unpacked_items ) ) {
			$_table .= '<div class="alert alert-danger" role="alert">' . sprintf( lang( 'unpacked_item_warning_alert_x' ), $package->package_no ) . '</div>';
			$_table .= admin_form_open( 'delivery/packaging_action', 'id="action-form"' );
			$_table .= '<table class="table table-striped table-bordered">';
			$_table .= '<thead>';

			$_table .= '<tr>';
			$_table .= '<th>';
			$_table .= '</th>';
			$_table .= '<th>' . lang( 'unpacked_items' ) . '</th>';

			$_table .= '<th><ul class="btn-tasks">
								                <li class="dropdown">
								                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
								                        <i class="icon fa fa-tasks tip" data-placement="left" title="' . lang( 'actions' ) . '" style="color: white"></i>
								                    </a>
								                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
								                        <li>';
			$_table .= '<a href="#" id="add_package" data-action="add_package"><i class="fa fa-gift"></i> ' . lang( 'add_packaging' ) . '</a>';
			$_table .= '</li>';
			$_table .= '</ul>
								                </li>
								            </ul>
								</th>';
			$_table .= '</tr>';
			$_table .= '<tr>';
			$_table .= '<th colspan="3">' . sprintf( lang( 'total_x_unpacked_items' ), count( $unpacked_items ) ) . '</th>';
			$_table .= '</tr>';

			$_table .= '<tr>';
			$_table .= '<th>&nbsp;</th>';
			$_table .= '<th>' . lang( 'name' ) . '</th>';
			$_table .= '<th>' . lang( 'quantity' ) . '</th>';
			$_table .= '</tr>';
			$_table .= '</thead>';
			$_table .= '<tbody>';
			foreach ( $items as $item ) {
				if ( in_array( $item->id, $unpacked_items ) ) {
					$_table .= '<tr>';
					$_table .= '<td><input class="checkbox multi-select input-xs" type="checkbox" name="val[]" value="' . $item->id . '" ></td>';
					$_table .= '<td>' . $item->product_code . ' - ' . $item->product_name . '</td>';
					$_table .= '<td>' . $this->rerp->formatQuantity( $item->quantity ) . ' ' . $item->product_unit_code . '</td>';
					$_table .= '</tr>';
				}
			}
			$_table .= '</tbody>';
			$_table .= '</table>';
			$_table .= '<div style="display: none;">
                    <input type="hidden" name="form_action" value="" id="form_action" class="input-xs">
                    <input type="hidden" name="pack_id" value="' . $package->getId() . '" class="input-xs">
                    <input type="submit" name="performAction" value="performAction" id="action-form-submit"
                           class="input-xs">
                </div>';


			$_table .= '<div class="btn-group">
                            <a href="#" id="edit_package" data-action="edit_package" class="tip btn btn-primary tip title="' . lang( 'add_item' ) . '">
                                <i class="fa fa fa-gift"></i> <span
                                        class="hidden-sm hidden-xs">' . lang( 'add_item' ) . '</span>
                            </a>
                        </div>';
		}
		$this->data['full_table'] = $_table;
		// end of packed list or unpacked_list

		$date = NULL;
		if ( $this->Owner || $this->Admin ) {
			$date = $this->rerp->fld( date( 'Y-m-d H:i:s' ) );
		} else {
			$date = date( 'Y-m-d H:i:s' );
		}

		$this->form_validation->set_rules( 'status', lang( 'status' ), 'required' );

		if ( $this->form_validation->run() == TRUE ) {
			$erp_package = new Erp_package( $id );
			$erp_package->setStatus( $this->input->post( 'status' ) );

			if ( $erp_package->save() ) {
				$this->session->set_flashdata( 'message', 'Package Status Updated Successfully' );
				redirect( $_SERVER['HTTP_REFERER'] );
			}
		} elseif ( $this->input->post( 'edit_shipment' ) ) {
			$this->session->set_flashdata( 'error', validation_errors() );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$this->data['date']     = $date;
		$this->data['shipment'] = $shipment;
		$this->data['sale']     = $sale;
		$this->data['delivery'] = $del_info;
		$this->data['package']  = $package;

		$bc   = [
			[ 'link' => base_url(), 'page' => lang( 'home' ) ],
			[ 'link' => admin_url( 'delivery' ), 'page' => lang( 'delivery' ) ],
			[ 'link' => admin_url( 'delivery/packaging_list' ), 'page' => lang( 'packaging_list' ) ],
			[ 'link' => '#', 'page' => lang( 'edit_package' ) ]
		];
		$meta = [ 'page_title' => lang( 'edit_package' ), 'bc' => $bc ];
		$this->page_construct( 'delivery/edit_package', $meta, $this->data );
	}

	public function delete_package( int $id ) {
		$this->rerp->checkPermissions( NULL, TRUE );
		if ( ! $id ) {
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		$package = new Erp_package( $id );
		if ( $package->getId() ) {
			if ( $package->deleteFlag() ) {
				if ( $this->input->is_ajax_request() ) {
					$this->rerp->send_json( [ 'error' => 0, 'msg' => lang( 'package_deleted' ) ] );
				}
				$this->session->set_flashdata( 'message', lang( 'package_deleted' ) );
				redirect( $_SERVER['HTTP_REFERER'] );
			}
		} else {
			$this->session->set_flashdata( 'error', 'Sorry! Invalid Package' );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
	}

	public function delete_package_item( $id ) {
		$this->rerp->checkPermissions( NULL, TRUE );
		if ( ! $id ) {
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$delivery_model = new Delivery_model();
		$item           = new Erp_Package_Items( $id );
		$package_id     = $item->package_id;
		$package        = new Erp_package( $package_id );

		if ( $item->getId() ) {
			if ( $item->delete() ) {

				$all_pack_item = $delivery_model->getAllPackageItemsIDByPackageID( $package_id );
				$item_count    = count( $all_pack_item );
				$package->setPackageItemsCount( $item_count );
				$package->save();

				if ( $this->input->is_ajax_request() ) {
					$this->rerp->send_json( [ 'error' => 0, 'msg' => lang( 'package_item_deleted' ) ] );
				}
				$this->session->set_flashdata( 'message', lang( 'package_item_deleted' ) );
				redirect( $_SERVER['HTTP_REFERER'] );
			}
		} else {
			$this->session->set_flashdata( 'error', 'Sorry! Invalid Package Item' );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
	}


}
