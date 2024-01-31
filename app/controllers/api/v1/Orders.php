<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

/**
 * Class Products
 *
 * @property Products_api $products_api
 * @property object $bank
 * @property Pay_model $pay_model
 * @property CI_Parser $parser
 */
class Orders extends MY_REST_Controller {
    public function __construct() {
	    parent::__construct();
        $this->methods['index_get']['limit'] = 500;
        $this->setup_payment_methods();
    }
    
    public function index_get() {
	    if ( ! $this->isCustomer() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
	    $start    = $this->get('start') && is_numeric($this->get('start')) ? absint( $this->get('start') ) : 0;
        $limit    = $this->get('limit') && is_numeric($this->get('limit')) ? absint( $this->get('limit') ) : 10;
	    $order_by = $this->get( 'order_by' ) ? explode( ',', $this->get( 'order_by' ) ) : [ 'id', 'acs', ];
	    
	    $this->db->order_by( $order_by[0],  ( $order_by[1] && strtolower( $order_by[1] ) === 'desc' ? 'DESC' : 'ASC' ) );
	    $this->db->limit( $limit, $start );
	    
	    $data = [
		    'status' => true,
		    'data'   => $this->shop_model->getOrders( $limit, $start ),
		    'limit'  => $limit,
		    'start'  => $start,
		    'total'  => $this->shop_model->getOrdersCount(),
	    ];
    	$this->set_response( $data, REST_Controller::HTTP_OK );
    }
    
    public function invoice_get() {
	    if ( ! $this->isCustomer() ) {
		    $this->response_user_unauthorized();
		    return;
	    }
	    $id = absint( $this->get( 'id' ) );
	    if ( ! $id ) {
	    	$this->set_response(
	    		[
	    			'status' => false,
				    'error'  => [ 'id' => lang( 'order_is_id_missing' ) ],
			    ],
			    REST_Controller::HTTP_BAD_REQUEST
		    );
	    	return;
	    }
	    if ( $order = $this->shop_model->getOrder( [ 'id' => $id ] ) ) {
		    $data = [
			    'order'       => $order,
			    'rows'        => $this->shop_model->getOrderItems( $id ),
			    'customer'    => $this->site->getCompanyByID( $order->customer_id ),
			    'biller'      => $this->site->getCompanyByID( $order->biller_id ),
			    'address'     => $this->shop_model->getAddressByID( $order->address_id ),
			    'return_sale' => $order->return_id ? $this->shop_model->getOrder( [ 'id' => $id ] ) : null,
			    'return_rows' => $order->return_id ? $this->shop_model->getOrderItems( $order->return_id ) : null,
		    ];
		    $this->set_response(
			    [
				    'status' => true,
				    'data'   => $data,
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
    
	public function create_cart_post() {
		if ( ! $this->isCustomer() ) {
			$this->response_user_unauthorized( lang( 'staff_not_allowed' ) );
			return;
		}
		
		$this->form_validation->set_data( $this->post() );
		$count = ! empty( $this->post( 'products' ) ) ? count( $this->post( 'products' ) ) : 0;
		$this->form_validation->set_rules( 'products[]', lang( 'products' ), 'required' );
		for ( $i = 0; $i < $count; $i ++ ) {
			$this->form_validation->set_rules( 'products[' . $i . '][product_id]', lang( 'product_id' ), 'required|numeric' );
			$this->form_validation->set_rules( 'products[' . $i . '][quantity]', lang( 'quantity' ), 'required|numeric' );
			$this->form_validation->set_rules( 'products[' . $i . '][variation]', lang( 'variation' ), 'numeric' );
		}
		
		if ( $this->form_validation->run() ) {
			$products = $this->post( 'products' );
			$errors = [];
			$cart   = [];
			foreach ( $products as $product ) {
				$id = absint( $product['product_id'] );
				if ( ! $id ) {
					continue;
				}
				$data = $this->prepareCartData( [
					'id'       => $id,
					'quantity' => absfloat( $product['quantity'] ),
					'option'   => isset( $product['variation'] ) ? absint( $product['variation'] ) : null,
				] );
				
				if ( ! is_array( $data ) ) {
					$errors[$id] = $data;
				} else {
					$cart[] = $data;
				}
			}
			if ( ! empty( $errors ) ) {
				$this->set_response(
					[
						'status' => false,
						'error'  => $errors,
					]
				);
				return;
			}
			if ( ! empty( $cart ) && $this->cart->insert( $cart ) ) {
				$cart   = $this->cart->cart_data( true );
				$status = true;
			} else {
				$cart     = [];
				$errors[] = lang( 'unable_to_add_item_to_cart' );
				$status   = false;
			}
			$data = [ 'status' => $status, 'cart' => $cart ];
			if ( $status ) {
				$data['message'] = lang( 'items_added_to_cart' );
			}
			if ( ! empty( $errors ) ) {
				$data['error'] = $errors;
			}
			$this->set_response( $data, REST_Controller::HTTP_OK );
		} else {
			$this->response_invalid_form();
		}
	}
	
	public function cart_get() {
		if ( ! $this->isCustomer() ) {
			$this->response_user_unauthorized();
			return;
		}
		
		$this->set_response( [
			'status' => true,
			'data'   => $this->cart->cart_data( true, false ),
		], REST_Controller::HTTP_OK );
	}
	
	public function remove_cart_item_post() {
		if ( ! $this->isCustomer() ) {
			$this->response_user_unauthorized();
			return;
		}
		
		$this->form_validation->set_data( $this->post() );
		$this->form_validation->set_rules( 'row_id', 'row_id', 'required' );
		if ( $this->form_validation->run() ) {
			$this->set_response( [
				'status' => $this->cart->remove( $this->post( 'row_id' ) ),
				'data'   => $this->cart->cart_data( true, false ),
			] );
		} else {
			$this->response_invalid_form();
		}
	}
	
	public function clear_cart_post() {
		if ( ! $this->isCustomer() ) {
			$this->response_user_unauthorized();
			return;
		}
		
    	$deleted = $this->cart->destroy();
    	$this->set_response(
		    [
			    'status' => $deleted,
			    'message' => $deleted ? lang( 'cart_items_deleted' ) : lang( 'unable_to_remove_cart_items' ),
		    ],
		    $deleted ? REST_Controller::HTTP_OK : REST_Controller::HTTP_INTERNAL_SERVER_ERROR
	    );
	}
	
	public function confirm_post() {
		if ( ! $this->isCustomer() ) {
			$this->response_user_unauthorized();
			return;
		}
		
		if ( ! $this->cart->total_items() ) {
			$this->set_response(
				[
					'status' => false,
					'error'  => lang( 'cart_is_empty' )
				],
				REST_Controller::HTTP_NOT_ACCEPTABLE
			);
			return;
		}
		$min_order = absfloat( $this->shop_settings->minimum_order );
		if ( $min_order > 0 && $min_order > $this->cart->total() ) {
			$this->set_response(
				[
					'status' => false,
					'error'  => sprintf( lang('minimum_order_amount_x'), $this->rerp->convertMoney( $this->shop_settings->minimum_order ) ),
				],
				REST_Controller::HTTP_NOT_ACCEPTABLE
			);
			return;
		}
		
		$this->form_validation->set_data( $this->post() );
		$this->form_validation->set_rules( 'address', lang( 'address' ), 'trim|required' );
		$this->form_validation->set_rules( 'note', lang( 'comment' ), 'trim' );
		$this->form_validation->set_rules( 'payment_method', lang( 'payment_method' ), 'required' );
		
		$area    = false;
		$zone    = false;
		$slot    = false;
		$address = false;
		
		if ( $address = $this->shop_model->getUserAddressByID( $this->post( 'address' ) ) ) {
			if ( $address->zone ) {
				$zone = new Erp_Shipping_Zone( $address->zone );
				if ( $zone->getIsEnabled() && ! empty( $zone->getShippingMethods() ) ) {
					$this->form_validation->set_rules( 'shipping_method', 'required' );
					if ( $address->area && $zone->has_area( $area ) ) {
						$area = new Erp_Shipping_Zone_Area( $address->area );
						if ( $area->has_slots() ) {
							$this->form_validation->set_rules( 'slot', 'trim|required|numeric' );
							$this->form_validation->set_rules( 'delivery_date', 'trim|required' );
						}
					} else {
						$zone = false;
					}
				} else {
					$zone = false;
				}
			}
		}
		
		if ( $this->form_validation->run() == true ) {
			$payment_methods = array_keys( $this->get_available_payment_methods() );
			$payment_method  = strtolower( $this->post( 'payment_method' ) );
			if ( ! in_array( $payment_method, $payment_methods ) ) {
				$this->set_response(
					[
						'status' => false,
						'error'  => lang( 'invalid_payment_method' )
					],
					REST_Controller::HTTP_NOT_ACCEPTABLE
				);
				return;
			}
			
			if ( ! $address ) {
				$this->set_response(
					[
						'status' => false,
						'error'  => lang( 'invalid_delivery_address' )
					],
					REST_Controller::HTTP_NOT_ACCEPTABLE
				);
				return;
			}
			
			$schedule = false;
			
			// check if slot is needed and validate slot id & delivery date.
			if ( $zone && $area && $area->getSlots() ) {
				$slot = $this->post( 'slot' );
				if ( ! $area->has_slot( $slot ) ) {
					$this->set_response(
						[
							'status' => false,
							'error'  => sprintf( lang( 'invalid_x' ), lang( 'delivery_slot') ),
						],
						REST_Controller::HTTP_NOT_ACCEPTABLE
					);
					return;
				}
				
				$slot = new Erp_Shipping_Zone_Area_Slot( $this->post( 'slot' ) );
				$date     = strtotime( $this->post( 'delivery_date' ) );
				$date    = $date ? date( 'Y-m-d', $date ) : '';
				$schedule = new Erp_Delivery_Schedule();
				if ( $date && $this->shop_model->checkSlot( $slot->getId(), $area->getId(), $date ) ) {
					$schedule->setStart( $date . ' ' . $slot->getStartAt() );
					$schedule->setEnd( $date . ' ' . $slot->getEndAt() );
					$schedule->setZoneId( $area->getZoneId() );
					$schedule->setAreaId( $area->getId() );
					$schedule->setSlotId( $slot->getId() );
					$schedule->save();
				}
				if ( ! $schedule->getId() ) {
					$this->set_response(
						[
							'status' => false,
							'error'  => lang( 'invalid_delivery_schedule' ),
						],
						REST_Controller::HTTP_NOT_ACCEPTABLE
					);
					return;
				}
			}
			
			// prepare order data.
			$customer    = $this->site->getCompanyByID( $this->session->userdata( 'company_id' ) );
			$biller      = $this->site->getCompanyByID( $this->shop_settings->biller );
			$note        = $this->db->escape_str( $this->post( 'note' ) );
			$product_tax = 0;
			$total       = 0;
			$gst_data    = [];
			$total_cgst  = $total_sgst = $total_igst = 0;
			// set shipping.
			$free_shipping = absfloat( $this->shop_settings->free_shipping );
			$shipping      = 0;
			$error         = '';
			$method        = $this->post( 'shipping_method' );

			if ( 'free_shipping' === $method && $free_shipping > 0 && absfloat( $this->cart->total( FALSE ) ) >= $free_shipping ) {
				$shipping = 0;
			} else {
				if ( 'default' !== $method && ( $zone && ! empty( $zone->getShippingMethods() ) ) ) {
					$method = new Erp_Shipping_Method( $method );
					if ( $method->getId() && $method->getIsEnabled() ) {
						$shipping = $method->getCost() + ( $area ? $area->getCostAdjustment() : 0 ) + ( $slot ? $slot->getCostAdjustment() : 0 );
					} else {
						$error = lang( 'invalid_shipping_method' );
					}
				} else {
					// @TODO Need to fix correct calculation of shipping zone on create Cart
					$method = new Erp_Shipping_Method( $method );
					if( $method->getId() && $method->getIsEnabled() ){
						$shipping = $method->getCost() + ( $area ? $area->getCostAdjustment() : 0 ) + ( $slot ? $slot->getCostAdjustment() : 0 );
					}
					else{
						// default shipping.
						$shipping = $this->cart->shipping();
						$shipping = absfloat( isset( $shipping['cost'] ) ? $shipping['cost'] : $this->shop_settings->shipping );
						if ( $shipping <= 0 ) {
							$error = lang( 'no_shipping_method' );
						}
					}
				}
			}
			
			if ( ! empty( $error ) ) {
				$this->set_response(
					[
						'status' => false,
						'error'  => $error,
					],
					REST_Controller::HTTP_NOT_ACCEPTABLE
				);
				return;
			}
			
			$products    = [];
			$error       = [];
			foreach ( $this->cart->contents() as $item ) {
				$item_option = null;
				if ( $product_details = $this->shop_model->getProductForCart( $item['product_id'] ) ) {
					$price = ( $this->loggedIn && isset( $product_details->special_price ) ? $product_details->special_price : $product_details->price );
					$price = $this->rerp->setCustomerGroupPrice( $price, $this->customer_group );
					$price = $this->rerp->isPromo( $product_details ) ? $product_details->promo_price : $price;
					if ( $item['option'] ) {
						if ( $product_variant = $this->shop_model->getProductVariantByID( $item['option'] ) ) {
							$item_option = $product_variant->id;
							$price       = $product_variant->price + $price;
						}
					}
					
					$item_net_price = $unit_price = $price;
					$item_quantity  = $item_unit_quantity  = $item['qty'];
					$pr_item_tax    = $item_tax    = 0;
					$tax            = '';
					
					if ( ! empty( $product_details->tax_rate ) ) {
						$tax_details = $this->site->getTaxRateByID($product_details->tax_rate);
						$ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
						$item_tax    = $ctax['amount'];
						$tax         = $ctax['tax'];
						if ($product_details->tax_method != 1) {
							$item_net_price = $unit_price - $item_tax;
						}
						$pr_item_tax = $this->rerp->formatDecimal(($item_tax * $item_unit_quantity), 4);
						if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller->state == $customer->state), $tax_details)) {
							$total_cgst += $gst_data['cgst'];
							$total_sgst += $gst_data['sgst'];
							$total_igst += $gst_data['igst'];
						}
					}
					
					$product_tax += $pr_item_tax;
					$subtotal = ( ( $item_net_price * $item_unit_quantity ) + $pr_item_tax );
					
					$unit = $this->site->getUnitByID($product_details->unit);
					
					$product = [
						'product_id'        => $product_details->id,
						'product_code'      => $product_details->code,
						'product_name'      => $product_details->name,
						'product_type'      => $product_details->type,
						'option_id'         => $item_option,
						'net_unit_price'    => $item_net_price,
						'unit_price'        => $this->rerp->formatDecimal($item_net_price + $item_tax),
						'quantity'          => $item_quantity,
						'product_unit_id'   => $unit ? $unit->id : null,
						'product_unit_code' => $unit ? $unit->code : null,
						'unit_quantity'     => $item_unit_quantity,
						'warehouse_id'      => $this->shop_settings->warehouse,
						'item_tax'          => $pr_item_tax,
						'tax_rate_id'       => $product_details->tax_rate,
						'tax'               => $tax,
						'discount'          => null,
						'item_discount'     => 0,
						'subtotal'          => $this->rerp->formatDecimal($subtotal),
						'serial_no'         => null,
						'real_unit_price'   => $price,
					];
					
					$products[] = ($product + $gst_data);
					$total += $this->rerp->formatDecimal(($item_net_price * $item_unit_quantity), 4);
				} else {
					$error[] = sprintf( lang( 'product_x_not_available' ), $item['name'] );
				}
			}
			
			if ( ! empty( $error ) ) {
				$this->set_response(
					[
						'status' => false,
						'error'  => count( $error ) == 1 ? $error[0] : $error,
					],
					REST_Controller::HTTP_NOT_ACCEPTABLE
				);
				return;
			}
			
			$order_tax       = $this->site->calculateOrderTax( $this->Settings->default_tax_rate2, ( $total + $product_tax ) );
			$total_tax       = $this->rerp->formatDecimal( ( $product_tax + $order_tax ), 4 );
			$grand_total     = $this->rerp->formatDecimal( ( $total + $total_tax + $shipping ), 4 );
			$shipping_method = $this->post( 'shipping_method' );
			
			if ( 'default' === $shipping_method || 'free_shipping' === $shipping_method ) {
				$shipping_method = 0;
			}
			
			$data = [
				'date'               => date('Y-m-d H:i:s'),
				'reference_no'       => $this->site->getReference('so'),
				'customer_id'        => isset($customer->id) ? $customer->id : '',
				'customer'           => ($customer->company && $customer->company != '-' ? $customer->company : $customer->name),
				'biller_id'          => $biller->id,
				'biller'             => ($biller->company && $biller->company != '-' ? $biller->company : $biller->name),
				'warehouse_id'       => $this->shop_settings->warehouse,
				'note'               => $note,
				'staff_note'         => null,
				'total'              => $total,
				'product_discount'   => 0,
				'order_discount_id'  => null,
				'order_discount'     => 0,
				'total_discount'     => 0,
				'product_tax'        => $product_tax,
				'order_tax_id'       => $this->Settings->default_tax_rate2,
				'order_tax'          => $order_tax,
				'total_tax'          => $total_tax,
				'shipping'           => $shipping,
				'grand_total'        => $grand_total,
				'total_items'        => $this->cart->total_items(),
				'sale_status'        => 'cod' === $payment_method ? 'completed' : 'pending',
				'payment_status'     => 'pending',
				'payment_term'       => null,
				'due_date'           => null,
				'paid'               => 0,
				'created_by'         => $this->session->userdata('user_id') ? $this->session->userdata('user_id') : null,
				'api'                => 1,
				'shop'               => 1,
				'address_id'         => $address->id,
				'hash'               => hash('sha256', microtime() . mt_rand( 10, mt_getrandmax() )),
				'payment_method'     => $payment_method,
				'shipping_method_id' => absint( $shipping_method ),
			];
			
			if ( $this->Settings->invoice_view == 2 ) {
				$data['cgst'] = $total_cgst;
				$data['sgst'] = $total_sgst;
				$data['igst'] = $total_igst;
			}
			
			if ( $sale_id = $this->shop_model->addSale( $data, $products, $customer, $address ) ) {
				if ( $schedule && $schedule->getId() ) {
					$schedule->setSalesId( $sale_id );
					$schedule->save();
				}
				
				$email = $this->order_received( $sale_id, $data['hash'] );
				
				$this->load->library('sms');
				$this->sms->newSale( $sale_id );
				$this->cart->destroy();
				$payment_url = '';
				// $payment_method
				if ( in_array( $payment_method, [ 'paypal', 'skrill', 'stripe', 'sslcommerz' ], true ) ) {
					$payment_url = site_url( 'pay/' . $payment_method . '/' . $sale_id . '?api=1' );
				}
				
				$this->set_response(
					[
						'status'  => true,
						'message' => lang( 'new_order_received' ),
						'data'    => [
							'order'        => $sale_id,
							'email'        => $email,
							'need_payment' => 'cod' !== $payment_method,
							'payment_url'  => $payment_url,
						],
					],
					REST_Controller::HTTP_OK
				);
			} else {
				$this->set_response(
					[
						'status' => false,
						'error'  => lang( 'unable_to_create_order' ),
					],
					REST_Controller::HTTP_INTERNAL_SERVER_ERROR
				);
			}
		} else {
			$this->response_invalid_form();
		}
	}
	
	public function pay_post() {
		if ( ! $this->isCustomer() ) {
			$this->response_user_unauthorized();
			return;
		}
		
		$this->form_validation->set_data( $this->post() );
		$this->form_validation->set_rules( 'order', lang( 'order_id' ), 'required|trim|numeric' );
		$this->form_validation->set_rules( 'payment_method', lang( 'payment_method' ), 'required' );
		
		if ( $this->form_validation->run() == true ) {
			$payment_methods = array_keys( $this->get_available_payment_methods() );
			$payment_method  = strtolower( $this->post( 'payment_method' ) );
			if ( ! in_array( $payment_method, $payment_methods ) ) {
				$this->set_response(
					[
						'status' => false,
						'error'  => lang( 'invalid_payment_method' )
					],
					REST_Controller::HTTP_NOT_ACCEPTABLE
				);
				return;
			}
			$sale_id = absint( $this->post( 'order' ) );
			$invoice = new Erp_Invoice( $sale_id );
			if ( $invoice->needsPayment() ) {
				$payment_url = '';
				// $payment_method
				if ( in_array( $payment_method, [ 'paypal', 'skrill', 'stripe', 'sslcommerz' ], true ) ) {
					$payment_url = site_url( 'pay/' . $payment_method . '/' . $sale_id . '?api=1' );
				}
				
				$this->set_response(
					[
						'status'      => true,
						'payment_url' => $payment_url,
					],
					REST_Controller::HTTP_OK
				);
			} else {
				$this->set_response(
					[
						'status' => true,
						'error'  => lang( 'order_already_paid' ),
					],
					REST_Controller::HTTP_NOT_ACCEPTABLE
				);
			}
		} else {
			$this->response_invalid_form();
		}
	}
	
	public function delivery_slots_get() {
		if ( ! $this->isCustomer() ) {
			$this->response_user_unauthorized();
			return;
		}
    	
    	$data = $this->get();
    	if ( empty( $data ) ) {
		    $data = [ 'xxx' => 'xxx' ];
	    }
		$this->form_validation->set_data( $data );
		$this->form_validation->set_rules( 'address', lang( 'address' ), 'required' );
		$this->form_validation->set_rules( 'delivery_date', lang( 'delivery_date' ), 'required' );
		
		if ( $this->form_validation->run() ) {
			$address  = $this->get( 'address' );
			$date     = $this->get( 'delivery_date' );
			$address  = $this->shop_model->getUserAddressByID( $address );
			
			if ( ! $address ) {
				$this->set_response(
					[
						'status' => false,
						'error'  => lang( 'invalid_delivery_address' )
					],
					REST_Controller::HTTP_BAD_REQUEST
				);
				return;
			}
			if ( ! $address->zone || ! $address->area ) {
				$this->set_response(
					[
						'status' => false,
						'error'  => lang( 'update_address' )
					],
					REST_Controller::HTTP_BAD_REQUEST
				);
				return;
			}
			$_date    = strtotime( $date );
			$error    = '';
			$slots    = [];
			$_slots   = [];
			$today    = $date == date( 'Y-m-d' );
			$tomorrow = '';
			if ( $address->area ) {
				if ( is_null( $date ) || ( $_date && $date >= date( 'Y-m-d' ) ) ) {
					$tomorrow = date( 'Y-m-d', strtotime( '+1 day', $_date ) );
					$slots    = $this->shop_model->getAvailableSlots( $address->area, date( 'Y-m-d', $_date ), true );
					$_slots   = $this->shop_model->getAvailableSlots( $address->area, $tomorrow, true );
				} else {
					$error = lang( 'invalid_delivery_date' );
				}
			} else {
				$error = lang( 'invalid_delivery_address' );
			}
			$data = [
				'status' => empty( $error ),
				'data'   => [
					[
						'label' => $today ? lang( 'today' ) : $date,
						'date'  => $date,
						'slots' => $slots
					],
					[
						'label' => $today ? lang( 'tomorrow' ) : $tomorrow,
						'date'  => $tomorrow,
						'slots' => $_slots
					],
				],
			];
			if ( ! empty( $error ) ) {
				$data['error'] = $error;
			}
			$this->set_response( $data );
		} else {
			$this->response_invalid_form();
		}
	}
	
	public function shipping_methods_get() {
		if ( ! $this->isCustomer() ) {
			$this->response_user_unauthorized();
			return;
		}
		
		if ( $this->cart->total() >= absfloat( $this->shop_settings->free_shipping ) ) {
			$this->set_response( [
				'status' => true,
				'data'    => [
					[
						'id'      => 'free_shipping',
						'checked' => true,
						'name'    => lang( 'free_shipping_label' ),
						'cost'    => '',
						'desc'    => '',
					]
				],
				'message' => '',
			] );
		} else {
			$error   = '';
			$data    = [];
			$address = $this->get( 'address' );
			$address = $this->shop_model->getUserAddressByID( $address );
			$_slot   = $this->get( 'slot' );
			if ( ! $address ) {
				$this->set_response(
					[
						'status' => false,
						'error'  => lang( 'invalid_delivery_address' )
					],
					REST_Controller::HTTP_BAD_REQUEST
				);
				return;
			}
			
			if ( $address->zone ) {
				$area           = 0;
				$slot           = 0;
				$zone           = new Erp_Shipping_Zone( $address->zone );
				$methods       = $zone->getShippingMethods();
				$current_method = $this->cart->shipping();
				$current_method = isset( $current_method['id'] ) ? $current_method['id'] : 'default';
				if ( ! empty( $methods ) ) {
					if ( $address->area ) {
						$area = new Erp_Shipping_Zone_Area( $address->area );
						if ( $area->getIsEnabled() ) {
							if ( ! empty( $area->getSlots() && $_slot ) ) {
								$slot = new Erp_Shipping_Zone_Area_Slot( $_slot );
								if ( $slot->getIsEnabled() ) {
									$slot = $slot->getId() ? $slot->getCostAdjustment() : 0;
								} else {
									$error = lang( 'shipping_invalid_slot' );
								}
							}
						} else {
							$error = lang( 'invalid_delivery_address' );
						}
						$area = $area->getId() ? $area->getCostAdjustment() : 0;
					}
					if ( empty( $error ) ) {
						foreach ( $methods as $method ) {
							$cost = $method->getCost();
							if ( 'free_shipping' !== $method->getMethodId() ) {
								$cost += $area;
								$cost += $slot;
							}
							$data[] = [
								'id'      => $method->getId(),
								'checked' => $method->getId() == $current_method,
								'name'    => $method->getName(),
								'cost'    => $this->rerp->convertMoney( $cost ),
								'desc'    => trim( strip_tags( $method->getDescription() ) ),
							];
						}
					}
				}
			}
			
			if ( empty( $data ) && absfloat( $this->shop_settings->shipping ) > 0 ) {
				// flat rate outside shipping zone if enabled.
				$cost = $this->rerp->convertMoney( $this->shop_settings->shipping );
				$data[] = [
					'id'      => 'default',
					'checked' => true,
					'name'    => lang( 'flat_rate_shipping_label' ),
					'cost'    => $cost,
					'desc'    => '',
				];
			}
			
			$data = [
				'status' => empty( $error ),
				'data'    => $data,
			];
			if ( ! empty( $error ) ) {
				$data['error'] = $error;
			}
			$this->set_response( $data );
		}
	}
	
	public function payment_methods_get() {
		$data = $this->get_available_payment_methods();
		if ( ! empty( $data ) ) {
			$this->set_response(
				[
					'status' => true,
					'data'   => $data,
				],
				REST_Controller::HTTP_OK
			);
		} else {
			$this->set_response(
				[
					'status' => false,
					'error'  => lang( 'no_payment_methods' ),
					'data'   => [],
				],
				REST_Controller::HTTP_OK
			);
		}
	}
    
    protected function prepareCartData( $args ) {
	    $args = ci_parse_args( $args, [ 'id' => 0, 'quantity' => 0, 'option' => 0 ] );
		
		if ( ! $args['quantity'] ) {
			return lang('quantity_must_be_1_or_more' );
		}
		
	    $product = $this->shop_model->getProductForCart( $args['id'] );
		if ( ! $product ) {
			return lang( 'product_not_found' );
		}
	    $options = $this->shop_model->getProductVariants( $product->id );
	    $price   = $this->rerp->setCustomerGroupPrice( ( isset( $product->special_price ) && ! empty( $product->special_price ) ? $product->special_price : $product->price ), $this->customer_group );
	    $price   = $this->rerp->isPromo($product) ? $product->promo_price : $price;
	    $option  = false;
		
	    if ( ! empty( $options ) ) {
		    if ( $args[ 'option' ] ) {
			    foreach ($options as $op) {
				    if ( $op['id'] == $args[ 'option' ] ) {
					    $option = $op;
					    break;
				    }
			    }
		    } else {
			    $option = array_values( $options )[0];
		    }
		    $price = $price + $option['price'];
	    }
	    $selected = $option ? $option['id'] : false;
		
	    if ( ! $this->Settings->overselling ) {
	    	$outOfStock = $this->checkProductOutOfStock( $product, $args['quantity'], $selected );
	    	$lowerQty = ! $this->checkProductOutOfStock( $product, 1, $selected );
	    	if ( $lowerQty && $outOfStock ) {
	    		return lang( 'item_stock_is_less_then_order_qty' );
		    } else if ( $outOfStock ) {
			    return lang('_item_out_of_stock');
		    }
	    }
		
	    $tax_rate   = $this->site->getTaxRateByID( $product->tax_rate );
	    $calc_tax   = $this->site->calculateTax( $product, $tax_rate, $price );
	    $tax        = $this->rerp->formatDecimal( $calc_tax['amount'] );
	    $price      = $this->rerp->formatDecimal( $price );
	    
	    return [
		    'id'         => $selected ? md5( $selected ) : md5( $product->id ),
		    'product_id' => $product->id,
		    'option'     => $selected,
		    'name'       => $product->name,
		    'slug'       => $product->slug,
		    'code'       => $product->code,
		    'unit_name'  => $product->unit_name,
		    'qty'        => $args['quantity'],
		    'price'      => $this->rerp->formatDecimal( $product->tax_method ? $price + $tax : $price ),
		    'tax'        => $tax,
		    'image'      => '',
		    'options'    => ! empty( $options ) ? $options : null,
	    ];
    }
	
	/**
	 * Check if product has stock (requested for order)
	 *
	 * @param object $product
	 * @param int    $qty
	 * @param int    $option_id
	 *
	 * @return bool
	 */
	private function checkProductOutOfStock( $product, $qty, $option_id = null ) {
		if ( $product->type == 'service' || $product->type == 'digital' ) {
			return false;
		}
		$check = [];
		if ( $product->type == 'standard' ) {
			$quantity = 0;
			if ( $pis = $this->site->getPurchasedItems( $product->id, $this->shop_settings->warehouse, $option_id ) ) {
				foreach ( $pis as $pi ) {
					$quantity += $pi->quantity_balance;
				}
			}
			$check[] = ( $qty <= $quantity );
		} elseif ( $product->type == 'combo' ) {
			$combo_items = $this->site->getProductComboItems( $product->id, $this->shop_settings->warehouse );
			foreach ( $combo_items as $combo_item ) {
				if ( $combo_item->type == 'standard' ) {
					$quantity = 0;
					if ( $pis = $this->site->getPurchasedItems( $combo_item->id, $this->shop_settings->warehouse, $option_id ) ) {
						foreach ( $pis as $pi ) {
							$quantity += $pi->quantity_balance;
						}
					}
					$check[] = ( ( $combo_item->qty * $qty ) <= $quantity );
				}
			}
		}
		
		return empty( $check ) || in_array( false, $check );
	}
	
	private function order_received( $id, $hash ) {
		
		$this->load->library( 'parser' );
		$this->load->model( 'pay_model' );
		
		$inv        = $this->shop_model->getOrder( [ 'id' => $id, 'hash' => $hash ] );
		$user       = $inv->created_by ? $this->site->getUser( $inv->created_by ) : false;
		$customer   = $this->site->getCompanyByID( $inv->customer_id );
		$biller     = $this->site->getCompanyByID( $inv->biller_id );
		$warehouse  = $this->site->getWarehouseByID( $inv->warehouse_id );
		$parse_data = [
			'reference_number' => $inv->reference_no,
			'contact_person'   => $customer->name,
			'company'          => $customer->company && $customer->company != '-' ? '(' . $customer->company . ')' : '',
			'order_link'       => shop_url( 'orders/' . $id . '/' . ( $this->loggedIn ? '' : $inv->hash ) ),
			'site_link'        => base_url(),
			'site_name'        => $this->Settings->site_name,
			'logo'             => '<img src="' . base_url() . 'assets/uploads/logos/' . $biller->logo . '" alt="' . ( $biller->company && $biller->company != '-' ? $biller->company : $biller->name ) . '"/>',
		];
		$msg        = file_get_contents( './themes/' . $this->Settings->adminTheme . 'email_templates/sale.html' );
		$message    = $this->parser->parse_string( $msg, $parse_data );
		$paypal     = $this->pay_model->getPaypalSettings();
		$skrill     = $this->pay_model->getSkrillSettings();
		$btn_code   = '<div id="payment_buttons" class="text-center margin010">';
		
		if ( ! empty( $this->shop_settings->bank_details ) ) {
			$btn_code .= '<div style="width:100%;">' . $this->shop_settings->bank_details . '</div><hr class="divider or">';
		}
		if ( $inv->grand_total != '0.00' ) {
			// @TODO replace this with pay/invId so system can handle.
			// @TODO add sslcommerz
			if ( $paypal->active == '1' ) {
				if ( trim( strtolower( $customer->country ) ) == $biller->country ) {
					$paypal_fee = $paypal->fixed_charges + ( $inv->grand_total
					                                         * $paypal->extra_charges_my / 100 );
				} else {
					$paypal_fee = $paypal->fixed_charges + ( $inv->grand_total
					                                         * $paypal->extra_charges_other / 100 );
				}
				$btn_code .= '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business='
				             . $paypal->account_email . '&item_name=' . $inv->reference_no
				             . '&item_number=' . $inv->id . '&image_url=' . base_url()
				             . 'assets/uploads/logos/' . $this->Settings->logo . '&amount='
				             . ( ( $inv->grand_total - $inv->paid ) + $paypal_fee )
				             . '&no_shipping=1&no_note=1&currency_code='
				             . $this->default_currency->code . '&bn=BuyNow&rm=2&return='
				             . admin_url( 'sales/view/' . $inv->id ) . '&cancel_return='
				             . admin_url( 'sales/view/' . $inv->id ) . '&notify_url='
				             . admin_url( 'payments/paypalipn' ) . '&custom=' . $inv->reference_no
				             . '__' . ( $inv->grand_total - $inv->paid ) . '__' . $paypal_fee
				             . '"><img src="' . base_url( 'assets/images/btn-paypal.png' )
				             . '" alt="Pay by PayPal"></a> ';
			}
			if ( $skrill->active == '1' ) {
				if ( trim( strtolower( $customer->country ) ) == $biller->country ) {
					$skrill_fee = $skrill->fixed_charges + ( $inv->grand_total
					                                         * $skrill->extra_charges_my / 100 );
				} else {
					$skrill_fee = $skrill->fixed_charges + ( $inv->grand_total
					                                         * $skrill->extra_charges_other / 100 );
				}
				$btn_code .= ' <a href="https://www.moneybookers.com/app/payment.pl?method=get&pay_to_email='
				             . $skrill->account_email
				             . '&language=EN&merchant_fields=item_name,item_number&item_name='
				             . $inv->reference_no . '&item_number=' . $inv->id . '&logo_url='
				             . base_url() . 'assets/uploads/logos/' . $this->Settings->logo
				             . '&amount=' . ( ( $inv->grand_total - $inv->paid ) + $skrill_fee )
				             . '&return_url=' . admin_url( 'sales/view/' . $inv->id )
				             . '&cancel_url=' . admin_url( 'sales/view/' . $inv->id )
				             . '&detail1_description=' . $inv->reference_no
				             . '&detail1_text=Payment for the sale invoice ' . $inv->reference_no
				             . ': ' . $inv->grand_total . '(+ fee: ' . $skrill_fee . ') = '
				             . $this->rerp->formatMoney( $inv->grand_total + $skrill_fee )
				             . '&currency=' . $this->default_currency->code . '&status_url='
				             . admin_url( 'payments/skrillipn' ) . '"><img src="'
				             . base_url( 'assets/images/btn-skrill.png' )
				             . '" alt="Pay by Skrill"></a>';
			}
		}
		
		$btn_code   .= '<div class="clearfix"></div></div>';
		$message    = $message . $btn_code;
		$attachment = $this->order_invoice( $id, $hash );
		$subject    = lang( 'new_order_received' );
		$sent       = $error = false;
		$cc         = $bcc = [];
		$cc[]       = $biller->email;
		if ( $user ) {
			$cc[] = $customer->email;
		}
		if ( $warehouse->email ) {
			$cc[] = $warehouse->email;
		}
		
		try {
			if ( $this->rerp->send_email( ( $user ? $user->email : $customer->email ), $subject, $message, null, null, $attachment, $cc, $bcc ) ) {
				delete_files( $attachment );
				$sent = true;
			}
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}
		
		return [ 'sent' => $sent, 'error' => $error ];
	}
	
	private function order_invoice( $id, $hash ) {
		// Download order pdf.
		$order                       = $this->shop_model->getOrder( [ 'id' => $id, 'hash' => $hash ] );
		$this->data['inv']           = $order;
		$this->data['rows']          = $this->shop_model->getOrderItems($id);
		$this->data['customer']      = $this->site->getCompanyByID($order->customer_id);
		$this->data['biller']        = $this->site->getCompanyByID($order->biller_id);
		$this->data['address']       = $this->shop_model->getAddressByID($order->address_id);
		$this->data['return_sale']   = $order->return_id ? $this->shop_model->getOrder(['id' => $id]) : null;
		$this->data['return_rows']   = $order->return_id ? $this->shop_model->getOrderItems($order->return_id) : null;
		$this->data['Settings']      = $this->Settings;
		$this->data['shop_settings'] = $this->shop_settings;
		$pdf_template                = 'pdf_invoice';
		if ( isset( $this->themeSettings['theme_options']['pdf_settings']['template_type']['value'] ) ) {
			$_pdf_template = $this->themeSettings['theme_options']['pdf_settings']['template_type']['value'];
			if ( 'default' != $_pdf_template && file_exists( $this->getCurrentThemeViews( 'pages/' . $pdf_template . '_' . $_pdf_template . '.php', true ) ) ) {
				$pdf_template = $pdf_template . '_' . $_pdf_template;
			}
		}
		$pdf_template_file = $this->getCurrentThemeViews( 'pages/' . $pdf_template );
		$html = $this->load->view( $pdf_template_file, $this->data, true );
		$name = lang( 'invoice' ) . '_' . str_replace( '/', '_', $order->reference_no ) . '.pdf';
		return $this->rerp->generate_pdf( $html, $name, 'S', $this->data['biller']->invoice_footer );
	}
}
