<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Shop
 * @property object $bank
 * @property Pay_model $pay_model
 */
class Shop extends MY_Shop_Controller {
	
	// get order & checkout helper functions.
	use MY_Order_Trait;
	
    public function __construct() {
        parent::__construct();
        $this->bank = $this->shop_model->getBankSettings();
	    $this->shop_settings->bank_details = '';
	    if ( $this->bank->active && ! empty( $this->bank->details ) ) {
	        $this->shop_settings->bank_details = $this->bank->details;
	    }
        if ($this->Settings->mmode) {
            redirect('notify/offline');
        }
        $this->load->library('form_validation');
        if ($this->shop_settings->private && !$this->loggedIn) {
            redirect('/login');
        }
    }

    public function delete_address() {
	    if ( ! $this->loggedIn ) {
		    $this->rerp->send_json(['status' => 'error', 'message' => lang('please_login')]);
	    }
	    $this->form_validation->set_rules('address_id', lang('id'), 'trim|required|numeric');

	    if ($this->form_validation->run() == true) {
	    	$address_id = $this->input->post('address_id');
		    $company_id = $this->session->userdata( 'company_id' );
		    $data = $this->db->get_where( 'addresses', [ 'id' => $address_id, 'company_id' => $company_id ] )->row();
		    if ( $data ) {
		    	if ( $this->shop_model->deleteAddress( $address_id ) ) {
				    $this->rerp->send_json(['status' => 'success', 'title' => 'Success', 'message' => 'Address Deleted']);
			    } else {
				    $this->rerp->send_json(['status' => 'error', 'title' => 'Error', 'message' => 'Unable to delete this address please try again after sometimes.']);
			    }
		    } else {
			    $this->rerp->send_json(['status' => 'error', 'title' => 'Error', 'message' => 'Invalid Address']);
		    }
	    }  elseif ($this->input->is_ajax_request()) {
		    $this->rerp->send_json(['status' => 'error', 'title' => 'Error', 'message' => validation_errors()]);
	    } else {
		    shop_redirect('shop/addresses');
	    }
    }

    // Add/edit customer address
	public function address( $id = null ) {
		if ( ! $this->loggedIn ) {
			$this->rerp->send_json( [ 'status' => 'error', 'message' => lang( 'please_login' ) ] );
        }
	    $this->form_validation->set_rules('title', lang('title'), 'trim|required');
        $this->form_validation->set_rules('line1', lang('line1'), 'trim|required');
        // $this->form_validation->set_rules('line2', lang("line2"), 'trim|required');
        $this->form_validation->set_rules('city', lang('city'), 'trim');
        $this->form_validation->set_rules('state', lang('state'), 'trim');
        // $this->form_validation->set_rules('postal_code', lang("postal_code"), 'trim|required');
        $this->form_validation->set_rules('country', lang('country'), 'trim');
        $this->form_validation->set_rules('phone', lang('phone'), 'trim|required');
		$this->form_validation->set_rules( 'zone', lang( 'zone' ), 'trim|numeric' );
		$this->form_validation->set_rules( 'area', lang( 'area' ), 'trim|numeric' );

        if ($this->form_validation->run() == true) {
            $user_addresses = $this->shop_model->getAddresses();
	        if ( count( $user_addresses ) > 6 || count( $user_addresses ) >= 6 && empty( $id ) ) {
                $this->rerp->send_json(['status' => 'error', 'title' => 'Error', 'message' => lang('already_have_max_addresses'), 'level' => 'error']);
            }
            
	        $data = [
		        'title'       => $this->input->post( 'title', true ),
		        'line1'       => $this->input->post( 'line1', true ),
		        'line2'       => $this->input->post( 'line2', true ),
		        'phone'       => $this->input->post( 'phone', true ),
		        'city'        => $this->input->post( 'city', true ),
		        'state'       => $this->input->post( 'state', true ),
		        'postal_code' => $this->input->post( 'postal_code', true ),
		        'country'     => $this->input->post( 'country', true ),
		        'area'        => $this->input->post( 'area', true ),
		        'zone'        => $this->input->post( 'zone', true ),
		        'company_id'  => $this->session->userdata( 'company_id' ),
	        ];

	        if ( $id ) {
		        $this->db->update( 'addresses', $data, [ 'id' => $id ] );
		        $data['id'] = $id;
		        $message    = lang( 'address_updated' );
	        } else {
		        $this->db->insert( 'addresses', $data );
		        $data['id'] = $this->db->insert_id();
		        $message = lang( 'address_added' );
	        }
	        if ( $data['area'] ) {
	        	$area = new Erp_Shipping_Zone_Area( $data['area'] );
		        $data['area_name'] = $area->getName();
	        }
	        $this->rerp->send_json( [
		        'status'  => 'success',
		        'title'  => 'Success',
		        'message' => $message,
		        'address' => $data
	        ] );
        } elseif ( $this->input->is_ajax_request() ) {
	        $this->rerp->send_json( [
		        'status'  => 'error',
		        'title'   => 'Error',
		        'message' => validation_errors(),
	        ] );
        } else {
            shop_redirect('shop/addresses');
        }
    }

    // Customer address list
    public function addresses() {
	    if ( ! $this->loggedIn ) {
            redirect('login');
        }
	    if ( $this->Staff ) {
            admin_redirect('customers');
        }
        $this->session->set_userdata('requested_page', $this->uri->uri_string());
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['addresses']  = $this->shop_model->getAddresses();
        $this->data['page_title'] = lang('my_addresses');
        $this->data['page_desc']  = '';
        $this->page_construct('pages/addresses', $this->data);
    }

    // Digital products download
    public function downloads($id = null, $hash = null)
    {
        if (!$this->loggedIn) {
            redirect('login');
        }
        if ($this->Staff) {
            admin_redirect();
        }
        if ($id && $hash && md5($id) == $hash) {
            $sale = $this->shop_model->getDownloads(1, 0, $id);
            if (!empty($sale)) {
                $product = $this->site->getProductByID($id);
                if (file_exists('./files/' . $product->file)) {
                    $this->load->helper('download');
                    force_download('./files/' . $product->file, null);
                    exit;
                }
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Transfer-Encoding: Binary');
                header('Content-disposition: attachment; filename="' . basename($product->file) . '"');
                // header('Content-Length: ' . filesize($product->file));
                readfile($product->file);
            }
            $this->session->set_flashdata('error', lang('file_x_exist'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $page   = $this->input->get('page') ? $this->input->get('page', true) : 1;
            $limit  = 10;
            $offset = ($page * $limit) - $limit;
            $this->load->helper('pagination');
            $total_rows = $this->shop_model->getDownloadsCount();
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['downloads']  = $this->shop_model->getDownloads($limit, $offset);
            $this->data['pagination'] = pagination('shop/download', $total_rows, $limit);
            $this->data['page_info']  = ['page' => $page, 'total' => ceil($total_rows / $limit)];
            $this->data['page_title'] = lang('my_downloads');
            $this->data['page_desc']  = '';
            $this->page_construct('pages/downloads', $this->data);
        }
    }

    // Add attachment to sale on manual payment
	public function manual_payment( $order_id ) {
        if ($_FILES['payment_receipt']['size'] > 0) {
            $this->load->library('upload');
            $config['upload_path']   = 'files/';
            $config['allowed_types'] = 'zip|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
            $config['max_size']      = 2048;
            $config['overwrite']     = false;
            $config['max_filename']  = 25;
            $config['encrypt_name']  = true;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('payment_receipt')) {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('error', $error);
                redirect($_SERVER['HTTP_REFERER']);
            }
            $manual_payment = $this->upload->file_name;
            $this->db->update('sales', ['attachment' => $manual_payment], ['id' => $order_id]);
            $this->session->set_flashdata('message', lang('file_submitted'));
            redirect($_SERVER['HTTP_REFERER'] ?? '/shop/orders');
        }
    }
    
    // Add new Order form shop
    public function order() {
    	
	    $shippingZone    = false;
	    $shippingArea    = false;
	    $hasSlots        = false;
	    $schedule        = false;
	    $sameAsBilling   = true;
	    $shippingAddress = false;
	    $guest_checkout  = absint( $this->input->post('guest_checkout') ) > 0;
	    $minimum_order   = $this->shop_settings->minimum_order;
	    $zoneRequired    = $this->shop_model->hasShippingZone();
	    
	    // user must logged in or checkout as guest.
	    // redirect to login otherwise.
	    if ( ! $guest_checkout && ! $this->loggedIn ) {
		    $this->session->set_userdata( 'requested_page', $this->uri->uri_string() );
		    redirect( 'login' );
        }
		
	    // check if minimum order is on & and cart value is sufficient.
	    // redirect to checkout page with message.
	    if ( $minimum_order > 0 && $minimum_order > $this->cart->total( false ) ) {
		    $this->session->set_flashdata( 'reminder', sprintf( lang('minimum_order_amount_x'), $this->rerp->convertMoney( $minimum_order ) ) );
		    shop_redirect( 'checkout' );
	    }
	    
	    // set validation for guest checkout.
	    if ( $guest_checkout ) {
		    $this->form_validation->set_rules( 'name', lang( 'name' ), 'trim|required' );
		    $this->form_validation->set_rules( 'email', lang( 'email' ), 'trim|required|valid_email' );
		    $this->form_validation->set_rules( 'phone', lang( 'phone' ), 'trim|required' );
		    $this->form_validation->set_rules( 'billing_line1', lang( 'billing_address' ) . ' ' . lang( 'line1' ), 'trim|required' );
		    $this->form_validation->set_rules( 'billing_city', lang( 'billing_address' ) . ' ' . lang( 'city' ), 'trim|required' );
		    $this->form_validation->set_rules( 'billing_country', lang( 'billing_address' ) . ' ' . lang( 'country' ), 'trim|required' );
		    
		    if ( $this->Settings->indian_gst ) {
			    $this->form_validation->set_rules('billing_state', lang('billing_address') . ' ' . lang('state'), 'trim|required');
		    }
		
		    if ( $zoneRequired ) {
			    $this->form_validation->set_rules( 'billing_zone', lang( 'invalid_address' ), 'trim|required' );
		        $shippingZone = $this->input->post( 'billing_zone' );
		    }
		
		    // different shipping?
		    $sameAsBilling = ! ! $this->input->post( 'same' );
		    
		    if ( ! $sameAsBilling ) {
			    $this->form_validation->set_rules('shipping_line1', lang('shipping_address') . ' ' . lang('line1'), 'trim|required');
			    $this->form_validation->set_rules('shipping_city', lang('shipping_address') . ' ' . lang('city'), 'trim|required');
			    $this->form_validation->set_rules('shipping_country', lang('shipping_address') . ' ' . lang('country'), 'trim|required');
			    $this->form_validation->set_rules('shipping_phone', lang('shipping_address') . ' ' . lang('phone'), 'trim|required');
			    
			    if ( $this->Settings->indian_gst ) {
				    $this->form_validation->set_rules( 'shipping_state', lang( 'shipping_address' ) . ' ' . lang( 'state' ), 'trim|required' );
			    }
			
			    if ( $zoneRequired ) {
				    $this->form_validation->set_rules('shipping_zone', lang('shipping_zone' ), 'trim|required');
				    $shippingZone = $this->input->post( 'shipping_zone' );
			    }
		    }
		
		    if ( $zoneRequired && $shippingZone ) {
			    $billing_or_shipping = ! $sameAsBilling ? 'shipping_' : 'billing_';
			    $shippingZone = new Erp_Shipping_Zone( $shippingZone );
			    if ( $shippingZone->getIsEnabled() && $shippingZone->getAreas() ) {
				    $this->form_validation->set_rules( $billing_or_shipping . 'area', lang( $billing_or_shipping . 'area' ), 'trim|required' );
				    $shippingArea = $this->input->post( $billing_or_shipping . 'area' );
				    if ( $shippingArea ) {
				    	if ( ! $shippingZone->has_area( $shippingArea ) ) {
						    $this->flash_response( lang( 'invalid_area' ), 'cart/checkout' );
					    }
					    $shippingArea = new Erp_Shipping_Zone_Area( $shippingArea );
					    if ( $shippingArea->getIsEnabled() && $shippingArea->getSlots() ) {
						    $hasSlots = true;
					    } else {
						    $shippingArea = false;
						    $hasSlots    = false;
					    }
				    }
			    }
		    }
		    
        } else {
		    $this->form_validation->set_rules( 'address', lang( 'address' ), 'trim|required' );
		    $shippingAddress = new Erp_Address( $this->input->post( 'address' ) );
		    if ( $shippingAddress->exists() && $shippingAddress->getZone() && $zoneRequired ) {
			    $shippingZone = new Erp_Shipping_Zone( $shippingAddress->getZone() );
			    if ( $shippingAddress->getArea() && $shippingZone->getIsEnabled() && $shippingZone->getAreas() ) {
				    if ( ! $shippingZone->has_area( $shippingAddress->getArea() ) ) {
					    $this->flash_response( lang( 'invalid_area_please_update_address' ), 'cart/checkout' );
				    }
				
				    $shippingArea = new Erp_Shipping_Zone_Area( $shippingAddress->getArea() );
				    if ( $shippingArea->getIsEnabled() && $shippingArea->getSlots() ) {
					    $hasSlots = true;
				    } else {
					    $shippingArea = false;
					    $hasSlots    = false;
				    }
			    }
		    }
	    }
	
	    $this->form_validation->set_rules( 'note', lang( 'comment' ), 'trim' );
	    $this->form_validation->set_rules( 'payment_method', lang( 'payment_method' ), 'required' );
	    
	    if ( $shippingZone ) {
		    if ( $shippingZone->has_shipping_methods() ) {
			    $this->form_validation->set_rules( 'shipping_method', 'required' );
		    }
	    }
	
	    // validate slots.
	    if ( $hasSlots ) {
		    $this->form_validation->set_rules( 'delivery_slot', 'delivery_slot', 'trim|required' );
		    $this->form_validation->set_rules( 'delivery_date', 'delivery_date', 'trim|required' );
		    $this->form_validation->set_rules( 'delivery_area', 'delivery_area', 'trim|required' );
	    }
	    
	    if ( $this->form_validation->run() == true ) {
		    $new_customer = false;
		    if ( $guest_checkout ) {
			    $billingAddress  = new Erp_Address();
			    $shippingAddress = new Erp_Address();
			    foreach( [ 'phone', 'line1', 'line2', 'city', 'state', 'postal_code', 'country', 'area', 'zone' ] as $part ) {
				    $setter = ucfirst( $part );
				    if ( 'postal_code' === $part ) {
					    $setter = 'PostalCode';
				    }
				    $_data = $this->input->post( 'phone' !== $part ? 'billing_' . $part : $part );
				    $billingAddress->{$setter}( $_data );
				    if ( ! $sameAsBilling ) {
					    $_data = $this->input->post( 'shipping_' . $part );
				    }
				    $shippingAddress->{$setter}( $_data );
			    }
			    unset( $_data );
			    $shippingAddress->save();
			    $customer = new Erp_Company();
			    $customer->setName( $this->input->post('name') );
			    $customer->setCompany( $this->input->post('company') );
			    $customer->setPhone( $this->input->post('phone') );
			    $customer->setEmail( $this->input->post('email') );
			    $customer->setAddress( $billingAddress->getLine( '<br>' ) );
			    $customer->setCity( $billingAddress->getCity() );
			    $customer->setState( $billingAddress->getState() );
			    $customer->setPostalCode( $billingAddress->getPostalCode() );
			    $customer->setCountry( $billingAddress->getCountry() );
			    $customer->setGroupId( 3 );
			    $customer->setPriceGroupName( 'customer' );
			    $customer->setCustomerGroupId( $this->Settings->customer_group );
			    $customer->setPriceGroupId( $this->Settings->price_group  );
			    unset( $billingAddress );
			    $new_customer = true;
			    
			    if ( $customer->save() ) {
				    $shippingAddress->setCompanyId( $customer->getId() );
				    $shippingAddress->save();
			    }
//				    else {
//					    if ( $shippingAddress->getId() ) {
//						    $shippingAddress->delete();
//					    }
//					    // if unable to save address pass it to shop model to insert.
//					    $shippingAddress = $shippingAddress->to_array();
//					    $customer = $customer->to_array();
//				    }
			
		    } else {
			    $customer = new Erp_Company( $this->session->userdata( 'company_id' ) );
		    }
	    	
		    if ( $hasSlots ) {
			    $slot = $this->input->post( 'delivery_slot' );
			    if ( ! $shippingArea->has_slot( $slot ) ) {
			    	$this->flash_response( sprintf( lang( 'invalid_x' ), lang( 'delivery_slot') ), 'cart/checkout', 'error' );
			    }
			    $slot     = new Erp_Shipping_Zone_Area_Slot( $slot );
			    $date     = strtotime( $this->input->post( 'delivery_date' ) );
			    $date    = $date ? date( 'Y-m-d', $date ) : false;
			    $schedule = new Erp_Delivery_Schedule();
			    //...
			    if ( $date && $this->shop_model->checkSlot( $slot->getId(), $shippingArea->getId(), $date ) ) {
			    	// block if available. so other's can use it.
			        $schedule->setStart( $date . ' ' . $slot->getStartAt() );
			        $schedule->setEnd( $date . ' ' . $slot->getEndAt() );
				    $schedule->setZoneId( $shippingArea->getZoneId() );
				    $schedule->setAreaId( $shippingArea->getId() );
				    $schedule->setSlotId( $slot->getId() );
				    $schedule->save();
				    unset( $slot, $shippingArea, $date );
			    }
			    
			    if ( ! $schedule->exists() ) {
			    	// schedule unavailable or unable to save the schedule.
			    	$this->flash_response( lang( 'invalid_delivery_schedule' ), 'cart/checkout' );
			    }
		    }
		    
		    if ( $shippingAddress->exists() ) {
		    	$biller        = new Erp_Company( $this->shop_settings->biller );
			    $order_data    = $this->prepare_order_data( $this->cart->contents(), $customer, $biller, false );
			    
			    $free_shipping = absfloat( $this->shop_settings->free_shipping );
			    
			    if ( $free_shipping > 0 && absfloat( $this->cart->total( false ) ) >= $free_shipping ) {
				    $shipping = 0;
			    } else {
				    $shipping = $this->cart->shipping();
				    $shipping = absfloat( isset( $shipping['cost'] ) ? $shipping['cost'] : $this->shop_settings->shipping );
			    }
			    
			    $order_tax       = $this->site->calculateOrderTax( $this->Settings->default_tax_rate2, ( $order_data['total'] + $order_data['product_tax'] ), false );
			    $total_tax       = ( $order_data['product_tax'] + $order_tax );
			    $grand_total     = ( $order_data['total'] + $total_tax + $shipping );
			    $payment_method  = $this->input->post( 'payment_method' );
			    $shipping_method = $this->input->post( 'shipping_method' );

			    $valid_coupons = [];

			    $coupons = $this->cart->getCoupons();
			    $discount = 0;
			    if ( !empty( $coupons ) ) {
			    	$total = $order_data['total'];
				    foreach ( $coupons as $code) {
					    try {
						    $coupon = Erp_Coupon::getByCode( $code );
						    if ( ! $coupon ) {
							    throw new Exception( sprintf( lang( 'coupon_x_does_not_exist' ), $code ) );
						    }

						    $this->cart->check_coupon( $coupon );
						    $calculated = $coupon->calculate_discount( $total,  $this->cart->contents() );
						    $total -= $calculated['discount'];
						    $discount += $calculated['discount'];
						    $valid_coupons[] = $coupon;
					    }
					    catch ( Exception $e ) {
						    $this->session->set_flashdata( 'error', $e->getMessage() );
						    redirect( $_SERVER['HTTP_REFERER'] );
					    }

				    }
			    }

			    if ( 'default' === $shipping_method ) {
				    $shipping_method = 0;
			    }
			    
			    $current_user = $this->session->userdata( 'user_id' ) ? $this->session->userdata( 'user_id' ) : null;
       
			    $data = [
                    'date'               => date( 'Y-m-d H:i:s' ),
                    'reference_no'       => $this->site->getReference( 'so' ),
                    'customer_id'        => $customer->exists() ? $customer->getId() : '',
                    'user_id'            => $current_user,
                    'is_guest'           => $guest_checkout,
                    'customer'           => $customer->getCompany( true ),
                    'biller_id'          => $biller->getId(),
                    'biller'             => $biller->getCompany( true ),
                    'warehouse_id'       => $this->shop_settings->warehouse,
                    'note'               => $this->db->escape_str( $this->input->post( 'comment' ) ),
                    'staff_note'         => null,
                    'total'              => $this->rerp->formatDecimal( $order_data['total'], 4 ),
                    'product_discount'   => 0,
                    'order_discount_id'  => $discount,
                    'order_discount'     => $discount,
                    'total_discount'     => $discount,
                    'product_tax'        => $this->rerp->formatDecimal( $order_data['product_tax'], 4 ),
                    'order_tax_id'       => $this->Settings->default_tax_rate2,
                    'order_tax'          => $this->rerp->formatDecimal( $order_tax, 4 ),
                    'total_tax'          => $this->rerp->formatDecimal( $total_tax, 4 ),
                    'shipping'           => $this->rerp->formatDecimal( $shipping, 4 ),
                    'grand_total'        => $this->rerp->formatDecimal( $grand_total, 4 ),
                    'total_items'        => $this->cart->total_items(),
                    'sale_status'        => 'cod' === $payment_method ? 'completed' : 'pending',
                    'payment_status'     => 'pending',
                    'payment_term'       => null,
                    'due_date'           => null,
                    'paid'               => 0,
                    'shop'               => 1,
                    'address_id'         => $shippingAddress->getId(),
                    'hash'               => hash( 'sha256', microtime() . mt_rand() ),
                    'payment_method'     => $payment_method,
                    'shipping_method_id' => absint( $shipping_method ),
                    'created_by'         => $current_user,
                ];
			
			    if ( $this->Settings->invoice_view == 2 ) {
				    $data['cgst'] = $this->rerp->formatDecimal( $order_data['total_cgst'], 4 );
				    $data['sgst'] = $this->rerp->formatDecimal( $order_data['total_sgst'], 4 );
				    $data['igst'] = $this->rerp->formatDecimal( $order_data['total_igst'], 4 );
			    }
                
			    if ( $new_customer || ! $customer->exists() ) {
                    $customer = (array) $customer->to_array();
                }
                
			    if ( $guest_checkout && ! $shippingAddress->exists() ) {
				    $shippingAddress = (array) $shippingAddress->to_array();
			    }
			    
			    // insert order data.
			    $sale_id = $this->shop_model->addSale( $data, $order_data['products'], $customer, $shippingAddress );
			    if ( $sale_id ) {
			    	//apply coupon
				    if ( ! empty( $valid_coupons ) ) {

				    	//@TODO Execute apply coupon into the calculation loop, so during the cart processing no one else can apply (if limit exceed)
				    	//@TODO Also undo apply coupon if checkout failed.
				    	//@TODO Sale ID will be optional into apply coupon.

					    array_map( function ( $coupon ) use ( $sale_id, $current_user, $customer ) {
				    		/** @var  Erp_Coupon $coupon */
						    $coupon->apply_coupon( $sale_id, $current_user, $customer->getEmail() );
					    }, $valid_coupons );
				    }

			    	if ( $hasSlots && $schedule && $schedule->exists() ) {
			    	    // save schedule if needed.
					    $schedule->setSalesId( $sale_id );
					    $schedule->save();
				    }
			    	
			    	// send email notification.
				    $this->order_received( $sale_id, $data['hash'] );
                    $this->cart->destroy();
				    $this->load->library( 'sms' );
				    $this->sms->newSale( $sale_id );
				    $this->session->set_flashdata( 'info', lang( 'order_added_make_payment' ) );
				    $wallet_status = false;
				    $order_page = shop_url( 'orders/' . $sale_id . '/' . ( $this->loggedIn ? '' : $data['hash'] ) );
				    
				    if ( ! $guest_checkout && $this->input->post( 'use_wallet_credit' ) ) {
				        // if has wallet balance and user want's to use wallet balance
				        // check & handle wallet credit (referral-balance)
					    $wallet_status = $this->process_wallet_pay( $sale_id, $data );
				    }
				    
				    // full paid from wallet or cod payment.
				    if ( 1 === $wallet_status || 'cod' === $payment_method ) {
				    	if ( 1 === $wallet_status ) {
				    		
						    $this->flash_response( lang( 'payment_added' ), $order_page );
					    }
				    	
				    	redirect( $order_page );
				    }
				    
				    // handle partial payment for wallet or full payment for direct checkout.
                    if ( in_array( $payment_method, [ 'paypal', 'skrill', 'stripe', 'sslcommerz' ], true ) ) {
	                    redirect('pay/' . $payment_method . '/' . $sale_id );
                    }
                    elseif ( $this->input->post( 'payment_method' ) == 'authorize' ) {
                    	// handle partial payment in auth-net
                    	// dont' store cc info over session or other mins.
	                    // so can't redirect to pay/by_authorize for the procession
	                    // deal with it here 8)...
	                    $inv = $this->shop_model->getSaleByID( $sale_id );
	                    $card_expiry = $this->input->post('card_expiry');
	                    $card_expiry = explode( '/', $card_expiry );
	                    if ( count( $card_expiry ) < 2 ) {
		                    $card_expiry[] = '';
	                    }
	                    $data = [
		                    'sale_id' => $sale_id,
		                    'cc_no' => $this->input->post('cc_no'),
		                    'cc_holder' => $this->input->post('cc_holder'),
		                    'cc_month' => $card_expiry[0],
		                    'cc_year' => $card_expiry[1],
		                    'cc_type' => $this->input->post('cc_type'),
		                    'cc_cvv2' => $this->input->post('security'),
		                    'note' => $this->input->post('comment'),
		                    'hash' => $inv->hash,
	                    ];
	                    $this->load->model('pay_model');
	                    $result = $this->pay_model->pay_authorize( $data );
	                    if ( ! empty( $result['error'] ) ) {
	                    	$this->flash_response( $result['error'], $order_page );
	                    } else {
		                    $this->flash_response( lang( 'payment_added' ), $order_page );
	                    }
                    } else {
	                    redirect( $order_page );
                    }
                } else {
			    	$this->flash_response( lang( 'order_failed' ), 'cart/checkout' );
			    }
            } else {
			    $this->flash_response( lang( 'address_x_found' ), 'cart/checkout' );
            }
        } else {
		    $this->flash_response( validation_errors(), 'cart/checkout', 'cart/checkout' . ( $guest_checkout ? '#guest' : '' ) );
        }
    }
	
    // Customer order/orders page
	public function orders( $id = null, $hash = null, $pdf = null, $buffer_save = null ) {
		$hash = $hash ? $hash : $this->input->get( 'hash', true );
		if ( ! $this->loggedIn && ! $hash ) {
			redirect( 'login' );
		}
		if ( $this->Staff ) {
			admin_redirect( 'sales' );
		}
		if ( $id && ! $pdf ) {
        	// View Single Order.
	        if ( $order = $this->shop_model->getOrder( [ 'id' => $id, 'hash' => $hash ] ) ) {
                $this->data['inv']         = $order;
                $this->data['rows']        = $this->shop_model->getOrderItems($id);
                $this->data['customer']    = $this->site->getCompanyByID($order->customer_id);
                $this->data['biller']      = $this->site->getCompanyByID($order->biller_id);
                $this->data['address']     = $this->shop_model->getAddressByID($order->address_id);
                $this->data['return_sale'] = $order->return_id ? $this->shop_model->getOrder(['id' => $id]) : null;
                $this->data['return_rows'] = $order->return_id ? $this->shop_model->getOrderItems($order->return_id) : null;
                
                $this->data['paypal']      = $this->shop_model->getPaypalSettings();
                $this->data['skrill']      = $this->shop_model->getSkrillSettings();
		        $this->data['authorize']   = $this->shop_model->getAuthorizeSettings();
                $this->data['sslcommerz']  = $this->shop_model->getSslcommerzSettings();
		        $this->data['cod']         = (object) ci_parse_args( $this->Erp_Options->getOption( 'cash_on_delivery', [] ), [ 'active' => 1 ] );
		        $this->data['bank']        = $this->bank;
                
                $this->data['page_title']  = lang('view_order');
                $this->data['page_desc']   = '';
                
		        $this->config->load( 'payment_gateways' );
		        $this->data['stripe_secret_key']      = $this->config->item( 'stripe_secret_key' );
		        $this->data['stripe_publishable_key'] = $this->config->item( 'stripe_publishable_key' );

				if ( ( $delivery_schedule = Erp_Delivery_Schedule::get_delivery_schedule_by_sales_id( $order->id ) ) && ( $order->shop || $order->api ) ){
					$this->data['delivery_schedule'] = ci_format_delivery_schedule( $delivery_schedule->start, $delivery_schedule->end );
				}

		        $this->page_construct( 'pages/view_order', $this->data );
            } else {
		        $this->session->set_flashdata( 'error', lang( 'access_denied' ) );
		        redirect( '/' );
            }
        } elseif ( $pdf || $this->input->get( 'download' ) ) {
        	// Download order pdf.
            $id                          = $pdf ? $id : $this->input->get('download', true);
            $hash                        = $hash ? $hash : $this->input->get('hash', true);
            $order                       = $this->shop_model->getOrder(['id' => $id, 'hash' => $hash]);
            $this->data['inv']           = $order;
            $this->data['rows']          = $this->shop_model->getOrderItems($id);
            $this->data['customer']      = $this->site->getCompanyByID($order->customer_id);
            $this->data['biller']        = $this->site->getCompanyByID($order->biller_id);
            $this->data['address']       = $this->shop_model->getAddressByID($order->address_id);
            $this->data['return_sale']   = $order->return_id ? $this->shop_model->getOrder(['id' => $id]) : null;
            $this->data['return_rows']   = $order->return_id ? $this->shop_model->getOrderItems($order->return_id) : null;
            $this->data['Settings']      = $this->Settings;
            $this->data['shop_settings'] = $this->shop_settings;

			if ( ( $delivery_schedule = Erp_Delivery_Schedule::get_delivery_schedule_by_sales_id( $order->id ) ) && ( $order->shop || $order->api ) ){
				$this->data['delivery_schedule'] = ci_format_delivery_schedule( $delivery_schedule->start, $delivery_schedule->end );
			}

	        $pdf_template = 'pdf_invoice';
	        
	        if ( isset( $this->themeSettings['theme_options']['pdf_settings']['template_type']['value'] ) ) {
		        $_pdf_template = $this->themeSettings['theme_options']['pdf_settings']['template_type']['value'];
		        if ( 'default' != $_pdf_template && file_exists( $this->getCurrentThemeViews( 'pages/' . $pdf_template . '_' . $_pdf_template . '.php', true ) ) ) {
			        $pdf_template = $pdf_template . '_' . $_pdf_template;
		        }
	        }
			$pdf_template_file = $this->getCurrentThemeViews( 'pages/' . $pdf_template );
			$html = $this->load->view( $pdf_template_file, $this->data, true );
			
	        if ( $this->input->get( 'view' ) ) {
		        echo $html;
		        exit;
	        }
            $name = lang('invoice') . '_' . str_replace('/', '_', $order->reference_no) . '.pdf';
			if ( $buffer_save ) {
				return $this->rerp->generate_pdf( $html, $name, $buffer_save, $this->data['biller']->invoice_footer );
			}
			$this->rerp->generate_pdf( $html, $name, false, $this->data['biller']->invoice_footer );
        } elseif ( ! $id ) {
        	// User Profile Order List Page
            $page   = $this->input->get('page') ? $this->input->get('page', true) : 1;
            $limit  = 10;
            $offset = ($page * $limit) - $limit;
            $this->load->helper('pagination');
            $total_rows = $this->shop_model->getOrdersCount();
            $this->load->admin_model( 'sales_model' );
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['orders']     = $this->shop_model->getOrders($limit, $offset);
            $this->data['pagination'] = pagination('shop/orders', $total_rows, $limit);
            $this->data['page_info']  = ['page' => $page, 'total' => ceil($total_rows / $limit)];
            $this->data['page_title'] = lang('my_orders');
            $this->data['page_desc']  = '';
            $this->page_construct('pages/orders', $this->data);
        }
    }

    // Display Page
    public function page( $slug ) {
	    $page = $this->shop_model->getPageBySlug( $slug );
	    if ( ! $page ) {
		    redirect( 'notify/error_404' );
	    }
        $this->data['page']       = $page;
        $this->data['page_title'] = $page->title;
        $this->data['page_desc']  = $page->description;
        $this->page_construct('pages/page', $this->data);
    }

    // Display Page
	public function product( $slug ) {
    	
        $product = $this->shop_model->getProductBySlug($slug);
		
		if ( ! $slug || ! $product ) {
			if ( $this->input->is_ajax_request() ) {
				$this->session->set_flashdata('error', lang('product_not_found'));
				$this->rerp->send_json( [ 'error' => 1, 'reload' => 1 ] );
			}
			$this->session->set_flashdata( 'error', lang( 'product_not_found' ) );
			$this->rerp->md( '/' );
		}
		
		$this->shop_model->updateProductViews( $product->id, $product->views );
		
		if ( 'default' !== $this->shopThemeName ) {
			$this->setup_product_data( $product, true );
		}
		
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $product->code . '/' . $product->barcode_symbology . '/40/0') . "' alt='" . $product->code . "' class='pull-left' />";
		if ( $product->type == 'combo' ) {
			$this->data['combo_items'] = $this->shop_model->getProductComboItems( $product->id );
		}
		
		$this->load->helper( 'text' );
		
		$this->data['product']        = $product;
        $this->data['other_products'] = $this->shop_model->getOtherProducts($product->id, $product->category_id, $product->brand);
        $this->data['unit']           = $this->site->getUnitByID($product->unit);
        $this->data['brand']          = $this->site->getBrandByID($product->brand);
        $this->data['images']         = $this->shop_model->getProductPhotos($product->id);
        $this->data['category']       = $this->site->getCategoryByID($product->category_id);
//        $this->data['subcategory']    = $product->subcategory_id ? $this->site->getCategoryByID($product->subcategory_id) : null;
        $this->data['tax_rate']       = $product->tax_rate ? $this->site->getTaxRateByID($product->tax_rate) : null;
        $this->data['warehouse']      = $this->shop_model->getAllWarehouseWithPQ($product->id);
        $this->data['options']        = $this->shop_model->getProductOptionsWithWH($product->id);
        $this->data['variants']       = $this->shop_model->getProductOptions($product->id);
        
        $this->data['page_title'] = $product->code . ' - ' . $product->name;
        $this->data['page_desc']  = character_limiter(strip_tags($product->product_details), 160);
		
		if ( $this->input->is_ajax_request() ) {
			$this->rerp->send_json( $product );
		}
		
		$this->data['view_product'] = $product;
		
		// if theme has ajax-loading support
		if ( 'default' === $this->shopThemeName ) {
			$this->page_construct('pages/view_product', $this->data);
		} else {
			$this->page_construct( 'index', $this->data );
		}
    }
    
    public function track_view() {
    	if ( ! $this->input->is_ajax_request() ) {
		    $this->session->set_flashdata( 'error', 'Invalid Request.' );
    		redirect( '/' );
	    }
	    $this->form_validation->set_rules('slug', lang('slug'), 'required');
	    if ( $this->form_validation->run() == true ) {
	    	$slug = $this->input->post( 'slug', true );
	    	if ( $slug ) {
		        $product = $this->shop_model->getProductBySlug( $slug );
		        if ( $product ) {
			        $this->shop_model->updateProductViews( $product->id, $product->views );
			        $this->rerp->send_json( [
				        'success' => true,
				        'count'   => is_numeric( $product->views ) ? ( (int) $product->views + 1 ) : 1,
			        ] );
		        } else {
			        $this->rerp->send_json( [ 'success' => false, 'code' => 404 ] );
		        }
		    } else {
	    		$this->rerp->send_json( [ 'success' => false, 'code' => 403 ] );
		    }
	    }
	    redirect( '/' );
    }

    // Products,  categories and brands page
	// $category_slug = null, $subcategory_slug = null, $brand_slug = null, $promo = null
    public function products( $category_slug = null, $brand_slug = null, $promo = null, $cashback = null ) {
    	$this->session->set_userdata('requested_page', $this->uri->uri_string());
        if ($this->input->get('category')) {
            $category_slug = $this->input->get('category', true);
        }
        if ($this->input->get('brand')) {
            $brand_slug = $this->input->get('brand', true);
        }
        if ($this->input->get('promo') && $this->input->get('promo') == 'yes') {
            $promo = true;
        }
	    if ($this->input->get('cashback') && $this->input->get('cashback') == 'yes') {
		    $cashback = true;
	    }
	
	    $category_slug    = $category_slug ? $this->shop_model->getCategoryBySlug( $category_slug ) : null;
//	    $subcategory_slug = $subcategory_slug ? $this->shop_model->getCategoryBySlug( $subcategory_slug ) : null;
        
        if ( $this->input->post( '__category') && ! $category_slug ) {
	        $category_id = $this->input->post( '__category');
	        $category_id = array_map( 'absint', explode( '/', $category_id ) );
	        $category_id = array_filter( $category_id );
	        if ( ! empty( $category_id ) ) {
	        	$category_slug = $this->shop_model->getCategoryByID( $category_id[0] );
//	        	if ( isset( $category_id[1] ) ) {
//			        $subcategory_slug = $this->shop_model->getProductByID( $category_id[1] );
//		        }
	        }
        }
		
	    $reset = $category_slug || $brand_slug ? true : false;
//	    $reset = $category_slug || $subcategory_slug || $brand_slug ? true : false;
	
	    $filters = [
		    'query'     => $this->input->post( 'query' ),
		    'category'  => $category_slug,
		    'brand'     => $brand_slug ? $this->shop_model->getBrandBySlug( $brand_slug ) : null,
		    'promo'     => $promo ? 'yes' : '',
		    'cashback'  => $cashback ? 'yes' : '',
		    'sorting'   => $reset ? null : $this->input->get( 'sorting' ),
		    'min_price' => $reset ? null : $this->input->get( 'min_price' ),
		    'max_price' => $reset ? null : $this->input->get( 'max_price' ),
		    'in_stock'  => $reset ? null : $this->input->get( 'in_stock' ),
		    'page'      => $this->input->get( 'page' ) ? $this->input->get( 'page', true ) : 1,
	    ];
	    
        $range = ci_parse_args( $this->shop_model->getMinMaxPrices(), [ 'max' => 0, 'min' => 0 ] );
		
        $this->data['filters']     = $filters;
        $this->data['price_range'] = (object) $range;
        $this->data['error']       = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title']  = (!empty($filters['category']) ? $filters['category']->name : (!empty($filters['brand']) ? $filters['brand']->name : lang('products'))) . ' - ' . $this->shop_settings->shop_name;
        $this->data['page_desc']   = !empty($filters['category']) ? $filters['category']->description : (!empty($filters['brand']) ? $filters['brand']->description : $this->shop_settings->products_description);
        
        $this->page_construct('pages/products', $this->data);
    }
    
    public function brand( $brand ) {
	    $this->session->set_userdata('requested_page', $this->uri->uri_string());
	    $brand = $this->shop_model->getBrandBySlug( $brand );
	    if ( ! $brand ) {
		    $this->session->set_flashdata( 'error', lang( 'brand_not_found' ) );
		    $this->rerp->md( '/' );
	    }
	    $this->products( 0, $brand->slug );
    }
    
    public function category() {
	    $this->session->set_userdata('requested_page', $this->uri->uri_string());
	    // passed by router.
    	$categories = func_get_args();
    	$cc = count( $categories );
    	if ( $cc > 8 ) {
		    $this->session->set_flashdata( 'error', lang( 'category_not_found' ) );
		    $this->rerp->md( '/' );
	    }
    	$this->data['selected_categories'] = $categories;
    	$categories = array_reverse( $categories );
    	$this->products( $categories[0] );
	    // call_user_func_array( [ $this, 'products'], $categories );
    }

    // Customer quotations
	public function quotes( $id = null, $hash = null ) {
	    if ( ! $this->loggedIn && ! $hash ) {
            redirect('login');
        }
	    if ( $this->Staff ) {
            admin_redirect('quotes');
        }
	    if ( $id ) {
            if ($order = $this->shop_model->getQuote(['id' => $id, 'hash' => $hash])) {
                $this->data['inv']        = $order;
                $this->data['rows']       = $this->shop_model->getQuoteItems($id);
                $this->data['customer']   = $this->site->getCompanyByID($order->customer_id);
                $this->data['biller']     = $this->site->getCompanyByID($order->biller_id);
                $this->data['created_by'] = $this->site->getUser($order->created_by);
                $this->data['updated_by'] = $this->site->getUser($order->updated_by);
                $this->data['page_title'] = lang('view_quote');
                $this->data['page_desc']  = '';
                $this->page_construct('pages/view_quote', $this->data);
            } else {
                $this->session->set_flashdata('error', lang('access_denied'));
                redirect('/');
            }
        } else {
            if ($this->input->get('download')) {
                $id                     = $this->input->get('download', true);
                $order                  = $this->shop_model->getQuote(['id' => $id]);
                $this->data['inv']      = $order;
                $this->data['rows']     = $this->shop_model->getQuoteItems($id);
                $this->data['customer'] = $this->site->getCompanyByID($order->customer_id);
                $this->data['biller']   = $this->site->getCompanyByID($order->biller_id);
                // $this->data['created_by'] = $this->site->getUser($order->created_by);
                // $this->data['updated_by'] = $this->site->getUser($order->updated_by);
                $this->data['Settings'] = $this->Settings;
                $html                   = $this->load->view( $this->getCurrentThemeViews( 'pages/pdf_quote' ), $this->data, true);
                if ($this->input->get('view')) {
                    echo $html;
                    exit;
                }
                $name = lang('quote') . '_' . str_replace('/', '_', $order->reference_no) . '.pdf';
                $this->rerp->generate_pdf($html, $name);
            }
            $page   = $this->input->get('page') ? $this->input->get('page', true) : 1;
            $limit  = 10;
            $offset = ($page * $limit) - $limit;
            $this->load->helper('pagination');
            $total_rows = $this->shop_model->getQuotesCount();
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['orders']     = $this->shop_model->getQuotes($limit, $offset);
            $this->data['pagination'] = pagination('shop/quotes', $total_rows, $limit);
            $this->data['page_info']  = ['page' => $page, 'total' => ceil($total_rows / $limit)];
            $this->data['page_title'] = lang('my_orders');
            $this->data['page_desc']  = '';
            $this->page_construct('pages/quotes', $this->data);
        }
    }
	
    // Search products page - ajax
    public function search() {
        $filters           = $this->input->post('filters') ? $this->input->post('filters', true) : false;
        $limit             = 15;
        $total_rows        = $this->shop_model->getProductsCount($filters);
        $filters['limit']  = $limit;
        $filters['offset'] = isset( $filters['page'] ) && ! empty( $filters['page'] ) && ( $filters['page'] > 1 ) ? ( ( $filters['page'] * $limit ) - $limit ) : null;
	    
        if ( $products = $this->shop_model->getProducts( $filters ) ) {
            $this->load->helper(['text', 'pagination']);
	        foreach ( $products as &$value ) {
                $value['details'] = character_limiter(strip_tags($value['details']), 140);
                if ($this->shop_settings->hide_price) {
                    $value['price']         = $value['formated_price']         = 0;
                    $value['promo_price']   = $value['formated_promo_price']   = 0;
                    $value['special_price'] = $value['formated_special_price'] = 0;
                } else {
                    $value['price']                  = $this->rerp->setCustomerGroupPrice($value['price'], $this->customer_group);
                    $value['formated_price']         = $this->rerp->convertMoney($value['price']);
                    $value['promo_price']            = $this->rerp->isPromo($value) ? $value['promo_price'] : 0;
                    $value['formated_promo_price']   = $this->rerp->convertMoney($value['promo_price']);
                    $value['special_price']          = isset($value['special_price']) && !empty($value['special_price']) ? $this->rerp->setCustomerGroupPrice($value['special_price'], $this->customer_group) : 0;
                    $value['formated_special_price'] = $this->rerp->convertMoney($value['special_price']);
                }
                $value = (object) $value;
                $this->setup_product_data( $value );
	            $value = (array) $value;
            }
	        
	        $pgArgs     = [
	        	'full_tag_open' => '',
	        	'full_tag_close' => '',
	        ];
	        $info = [
		        'page'  => (int) ( isset( $filters['page'] ) && ! empty( $filters['page'] ) ? $filters['page'] : 1 ),
		        'total' => ceil( $total_rows / $limit ),
	        ];
	        
	        $pagination = pagination( 'shop/products', $total_rows, $limit, $pgArgs );
	        if ( $total_rows > $limit ) {
		        if ( $info['page'] === 1 ) {
			        $pagination = '<li class="prev disabled"><a rel="prev"><i class="fa fa-angle-left"></i></a></li>' . $pagination;
		        }
		        if ( $info['page'] < 3 ) {
			        $class = $lnk = '';
			        if ( $info['page'] !== 1 ) {
				        $lnk = ' href="' . site_url( 'shop/products' ) . '" data-ci-pagination-page="1"';
				        $class = ' disabled';
			        }
			        $pagination = '<li class="first'.$class.'"><a'.$lnk.'><i class="fa fa-angle-double-left"></i></a></li>' . $pagination;
		        }
		        if ( $info['page'] == $info['total'] ) {
			        $pagination .= '<li class="next"><a rel="prev"><i class="fa fa-angle-right"></i></a></li>';
		        }
		        if ( $info['page'] > ( $info['total'] - 3 ) ) {
			        $class = $lnk = '';
			        if ( $info['page'] < $info['total'] ) {
				        $lnk = ' href="' . site_url( 'shop/products?page=' . $info['total'] ) . '" data-ci-pagination-page="'.  $info['total'] .'"';
				        $class = ' disabled';
			        }
			        $pagination .= '<li class="last'.$class.'"><a'.$lnk.'><i class="fa fa-angle-double-right"></i></a></li>';
		        }
	        }
	        
	        // response.
	        $data = [
		        'filters'    => $filters,
		        'products'   => $products,
		        'pagination' => '<ul class="list-inline list-unstyled">' . $pagination . '</ul>',
		        'info'       => $info,
	        ];
            $this->rerp->send_json( $data );
        } else {
            $this->rerp->send_json(['filters' => $filters, 'products' => false, 'pagination' => false, 'info' => false]);
        }
    }
	
    // Send us email
    public function send_message() {
        $this->form_validation->set_rules('name', lang('name'), 'required');
        $this->form_validation->set_rules('email', lang('email'), 'required|valid_email');
        $this->form_validation->set_rules('subject', lang('subject'), 'required');
        $this->form_validation->set_rules('message', lang('message'), 'required');
	
	    if ( $this->form_validation->run() == true ) {
            try {
                if ($this->rerp->send_email($this->shop_settings->email, $this->input->post('subject'), $this->input->post('message'), $this->input->post('email'), $this->input->post('name'))) {
                    $this->rerp->send_json(['status' => 'Success', 'message' => lang('message_sent')]);
                }
                $this->rerp->send_json(['status' => 'error', 'message' => lang('action_failed')]);
            } catch (Exception $e) {
                $this->rerp->send_json(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } elseif ($this->input->is_ajax_request()) {
            $this->rerp->send_json(['status' => 'Error!', 'message' => validation_errors(), 'level' => 'error']);
        } else {
            $this->session->set_flashdata('warning', 'Please try to send message from contact page!');
            shop_redirect();
        }
    }

    // Customer wishlist page
    public function wishlist() {
	    if ( ! $this->loggedIn ) {
            redirect('login');
        }
	    $this->session->set_userdata( 'requested_page', $this->uri->uri_string() );
	    $this->data['error'] = ( validation_errors() ) ? validation_errors() : $this->session->flashdata( 'error' );
	    //$total    = $this->shop_model->getWishlist( true );
	    $list = $this->shop_model->getWishlist();
	    $this->load->helper( 'text' );
	    $items = [];
	    foreach ( $list as $item ) {
		    $product = $this->shop_model->getProductByID( $item->product_id );
		    if ( $product ) {
			    $this->setup_product_data( $product );
			    $items[] = $product;
		    }
        }
	    $this->data['items']      = $items;
	    $this->data['page_title'] = lang( 'wishlist' );
	    $this->data['page_desc']  = '';
	    $this->page_construct( 'pages/wishlist', $this->data );
    }
    
    public function subscribe() {
	    $this->form_validation->set_rules( 'subs_email', lang( 'email' ), 'required|valid_email' );
	    if ( $this->form_validation->run() == true ) {
		    try {
			    $mcApi = explode( ',', $this->shop_settings->mc_api );
			    $mcApi = array_filter( array_map( 'trim', $mcApi ) );
			    if ( count( $mcApi ) < 2 ) {
				    log_message( 'debug', 'MailChimp API Key or List Id Missing');
				    $this->rerp->send_json(
					    [
						    'status'  => lang( 'error' ),
						    'level' => 'error',
						    'message' => lang( 'error_occurred' ),
					    ]
				    );
			    }
		    	$mc = new \DrewM\MailChimp\MailChimp( $mcApi[0] );
//			    $result = $mc->get('lists');
//			    $this->rerp->send_json( $result );
			    $result = $mc->post( "lists/{$mcApi[1]}/members", [
				    'email_address' => $this->input->post( 'subs_email' ),
				    'status'        => 'subscribed',
			    ] );
			    if ( $mc->success() ) {
				    set_cookie( 'hide_subscribe_popup', 'yes', 31536000 );
				    $this->rerp->send_json(
					    [
						    'status'  => lang( 'success' ),
						    'level' => 'success',
						    'message' => sprintf( lang( 'subscription_success' ), $this->shop_settings->shop_name ),
					    ]
				    );
			    }
			    else if ( 'Member Exists' == $result['title'] ) {
				    set_cookie( 'hide_subscribe_popup', 'yes', 31536000 );
				    $this->rerp->send_json(
					    [
						    'status'  => 'Success',
						    'message' => lang( 'your_already_subscribed' ),
					    ]
				    );
			    } else {
				    log_message( 'debug', 'MailChimp Response: ' . json_encode( $result ) );
				    set_cookie( 'hide_subscribe_popup', 'yes', 10800 );
				    $this->rerp->send_json(
					    [
						    'status'  => lang( 'error' ),
						    'level' => 'error',
						    'message' => lang( 'action_failed' ),
						    'res' => $result['detail'],
						    'resx' => $result,
					    ]
				    );
			    }
		    } catch (Exception $e) {
			    log_message( 'error', 'MailChimp Api Error. Message: ' . $e->getMessage() );
			    set_cookie( 'hide_subscribe_popup', 'yes', 10800 );
			    $this->rerp->send_json(
				    [
					    'status'  => lang( 'error' ),
					    'level'   => 'error',
					    'message' => lang( 'error_occurred' ),
				    ]
			    );
		    }
	    } elseif ( $this->input->is_ajax_request() ) {
		    $this->rerp->send_json(
			    [
				    'status'  => 'Error!',
				    'message' => validation_errors(),
				    'level'   => 'error',
			    ]
		    );
	    } else {
		    $this->session->set_flashdata('warning', 'Please try to subscribe from home page!');
		    shop_redirect( '', 'auto', 301 );
	    }
    }
}
