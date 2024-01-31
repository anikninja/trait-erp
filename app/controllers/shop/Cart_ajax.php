<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Cart_ajax
 * @property Shop_model $shop_model
 * @property bool|object $Settings
 * @property CI_Input $input
 * @property Rerp $rerp
 * @property CI_Session $session
 * @property Site $site
 * @property bool $loggedIn
 * @property array $data
 * @property Tec_cart $cart
 * @property CI_Config $config
 * @property object $bank
 *
 */
class Cart_ajax extends MY_Shop_Controller {
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
	    if ( $this->shop_settings->hide_price ) {
            redirect('/');
        }
	    if ( $this->shop_settings->private && ! $this->loggedIn ) {
            redirect('/login');
        }
    }

	public function add( $product_id ) {

		$qty = (
			$this->input->get( 'qty' ) ? $this->input->get( 'qty' ) :
			(
			$this->input->post( 'quantity' ) ? $this->input->post( 'quantity' ) : 1
			)
		);

		$qty = absint( $qty );

		if ( ! $qty ) {
			if ($this->input->is_ajax_request()) {
				$this->send_cart( lang('quantity_must_be_1_or_more' ), true );
			} else {
				$this->session->set_flashdata( 'error', lang('quantity_must_be_1_or_more' ) );
				redirect( $_SERVER['HTTP_REFERER'] );
			}
		}

		$on_sale = false;
		$special = false;
		$product = $this->shop_model->getProductForCart( $product_id );
		$options = $this->shop_model->getProductVariants( $product_id );
		if ( isset( $product->special_price ) && ! empty( $product->special_price ) ) {
			$price = $this->rerp->setCustomerGroupPrice( $product->special_price, $this->customer_group );
			$special = true;
		} else {
			$price = $this->rerp->setCustomerGroupPrice( $product->price, $this->customer_group );
		}
		if ( $this->rerp->isPromo($product) ) {
			$price = $product->promo_price;
			$on_sale = true;
		}

		$product->cash_back = (bool) $product->cash_back;
		if ( $product->cash_back ) {
			$today = date( 'Y-m-d' );

			$cb_start = ( ! $product->cash_back_start_date ) ? $today : $product->cash_back_start_date;
			$cb_end   = ( ! $product->cash_back_end_date ) ? $today : $product->cash_back_end_date;
			$product->cash_back_amount = absfloat( $product->cash_back_amount );

			if ( ( $cb_start <= $today && $cb_end >= $today ) && $product->cash_back_amount > 0 ) {
				$product->cash_back_amount = $this->rerp->convertMoney( $product->cash_back_amount );
				$product->cash_back = true;
			} else {
				$product->cash_back = false;
			}
		}

		$option  = false;

		if ( ! empty( $options ) ) {
			if ( $this->input->post( 'option' ) ) {
				foreach ($options as $op) {
					if ( $op['id'] == $this->input->post( 'option' ) ) {
						$option = $op;
					}
				}
			} else {
				$option = array_values($options)[0];
			}
			$price = $price + $option['price'];
		}

		$selected = $option ? $option['id'] : false;

		if ( ! $this->Settings->overselling && $this->checkProductStock( $product, $qty, $selected ) ) {
			if ($this->input->is_ajax_request()) {
				$this->send_cart( lang('item_out_of_stock' ), true );
			} else {
				$this->session->set_flashdata('error', lang('item_out_of_stock'));
				redirect($_SERVER['HTTP_REFERER']);
			}
		}

		$tax_rate   = $this->site->getTaxRateByID( $product->tax_rate );
		$ctax       = $this->site->calculateTax( $product, $tax_rate, $price );
		$tax        = $this->rerp->formatDecimal( $ctax['amount'] );
		$price      = $this->rerp->formatDecimal( $price );
		$unit_price = $this->rerp->formatDecimal( $product->tax_method ? $price + $tax : $price );
		$id         = $selected ? md5( $selected ) : md5( $product->id );
		//$id         = $this->Settings->item_addition ? md5($product->id) : md5(microtime());

		$img = $this->getThumb( $product->image );

		$data = [
			'id'          => $id,
			'product_id'  => $product->id,
			'category_id' => $product->category_id,
			'qty'         => $qty,
			'name'        => $product->name,
			'slug'        => $product->slug,
			'code'        => $product->code,
			'price'       => $unit_price,
			'on_sale'     => $on_sale,
			'special'     => $special,
			'cash_back'    => $product->cash_back,
			'unit_name'   => $product->unit_name,
			'tax'         => $tax,
			'image'       => $img,
			'option'      => $selected,
			'options'     => ! empty( $options ) ? $options : NULL,
		];



		$rowId = $this->cart->insert( $data );

		if ( $rowId ) {
			if ( ! $this->input->is_ajax_request() ) {
				$this->session->set_flashdata( 'message', lang( 'item_added_to_cart' ) );
				redirect( $_SERVER['HTTP_REFERER'] );
			} else {
				$cart = $this->cart->cart_data( true );
				$cart['newItem'] = [
					'rowId'  => $rowId,
					'qty'    => $qty,
					'option' => $selected,
				];
				$items = array_filter( $cart['contents'], function( $item ) use( $product ) {
					return $item['product_id'] == $product->id;
				} );
				$count = (int) array_reduce( $items, function ( $total, $item ) {
					$total += $item['qty'];
					return $total;
				} );
				if ( 1 === $qty ) {
					if ( 1 ===  $count ) {
						$cart['message'] = sprintf( lang( 'x_added_to_the_cart' ), $product->name );
					} else {
						$cart['message'] = sprintf( lang( 'x_added_to_the_cart_total' ), $product->name, $count );
					}
				} else {
					if ( $qty ===  $count ) {
						$cart['message'] = sprintf( lang( 'qty_x_added_to_the_cart' ), $product->name, $qty );
					} else {
						$cart['message'] = sprintf( lang( 'qty_x_added_to_the_cart_total' ), $product->name, $qty, $count );
					}
				}

				$this->rerp->send_json( $cart );
			}
		}
		$this->session->set_flashdata( 'error', lang( 'unable_to_add_item_to_cart' ) );
		redirect( $_SERVER['HTTP_REFERER'] );
    }

	public function update( $data = null ) {
		if ( is_array( $data ) ) {
			return $this->cart->update( $data );
		}
		if ($this->input->is_ajax_request()) {
			if ( $rowid = $this->input->post( 'rowid', true ) ) {
				$item = $this->cart->get_item($rowid);
				// $product = $this->site->getProductByID($item['product_id']);
				$product = $this->shop_model->getProductForCart($item['product_id']);
				$options = $this->shop_model->getProductVariants($product->id);
				$price   = $this->rerp->setCustomerGroupPrice((isset($product->special_price) ? $product->special_price : $product->price), $this->customer_group);
				$price   = $this->rerp->isPromo($product) ? $product->promo_price : $price;
				// $price = $this->rerp->isPromo($product) ? $product->promo_price : $product->price;
				if ( $option = $this->input->post( 'option' ) ) {
					foreach ( $options as $op ) {
						if ($op['id'] == $option) {
							$price = $price + $op['price'];
						}
					}
				}
				$selected = $this->input->post('option') ? $this->input->post('option', true) : false;
				if ($this->checkProductStock($product, $this->input->post('qty', true), $selected)) {
					if ($this->input->is_ajax_request()) {
						$cart = $this->cart->cart_data( true );
						$item = $this->cart->get_item( $rowid );
						$cart['currentItem'] = [
							'rowId'  => $rowid,
							'qty'    => $item['qty'],
							'option' => $selected,
						];
						$this->send_cart( lang( 'item_stock_is_less_then_order_qty' ), true, $cart );
					} else {
						$this->session->set_flashdata('error', lang('item_stock_is_less_then_order_qty'));
						redirect($_SERVER['HTTP_REFERER']);
					}
				}

				$tax_rate   = $this->site->getTaxRateByID($product->tax_rate);
				$ctax       = $this->site->calculateTax($product, $tax_rate, $price);
				$tax        = $this->rerp->formatDecimal($ctax['amount']);
				$price      = $this->rerp->formatDecimal($price);
				$unit_price = $this->rerp->formatDecimal($product->tax_method ? $price + $tax : $price);

				$data = [
					'rowid'  => $rowid,
					'price'  => $price,
					'tax'    => $tax,
					'qty'    => $this->input->post('qty', true),
					'option' => $selected,
				];
				if ( $this->cart->update( $data ) ) {
					$this->send_cart( lang( 'cart_updated' ), false );
				} else {
					$this->send_cart( lang( 'cart_not_updated' ), true );
				}
			}
		}
	}

	public function remove( $rowid = null ) {
		if ( $rowid ) {
			return $this->cart->remove($rowid);
		}
		if ($this->input->is_ajax_request()) {
			if ($rowid = $this->input->post('rowid', true)) {
				if ($this->cart->remove($rowid)) {
					$this->send_cart( lang( 'cart_item_deleted' ), false );
				} else {
					$this->send_cart( lang( 'cart_item_not_deleted' ), true );
				}
			}
		}
	}

	public function apply_coupon( $code = NULL ) {
		if ( ! $code ) {
			$code =  $this->input->get( 'code'  );
		}
		if ( ! $code ) {
			$code =  $this->input->post( 'code'  );
		}

		try {
			if ( ! $code ) {
				throw new Exception( lang('invalid_coupon_code' ) );
			}
			$coupon = Erp_Coupon::getByCode( $code );
			if ( ! $coupon ) {
				throw new Exception( sprintf( 'Coupon "%s" does not exist!', $code ) );
			}

			if ( $this->cart->apply_coupon( $coupon ) ) {
				if ($this->input->is_ajax_request()) {
					$this->send_cart( lang('coupon_code_applied_successfully' ), false );
				} else {
					$this->session->set_flashdata( 'success', lang('coupon_code_applied_successfully' ) );
					redirect( $_SERVER['HTTP_REFERER'] );
				}
			} else {
				throw new Exception( lang('unable_to_apply_this_coupon' ) );
			}
		} catch ( Exception $e ) {
			if ($this->input->is_ajax_request()) {
				$this->send_cart( $e->getMessage(), true );
			} else {
				$this->session->set_flashdata( 'error', $e->getMessage() );
				redirect( $_SERVER['HTTP_REFERER'] );
			}
		}
	}

	public function remove_coupon( $hash = null ) {
		if ( ! $hash ) {
			$hash =  $this->input->get( 'hash'  );
		}
		if ( ! $hash ) {
			$hash =  $this->input->post( 'hash'  );
		}

		if ( $hash ) {
			if ( $this->cart->remove_coupon( $hash ) ) {
				if ($this->input->is_ajax_request()) {
					$this->send_cart( lang('coupon_code_successfully_removed' ), false );
				} else {
					$this->session->set_flashdata( 'success', lang('coupon_code_successfully_removed' ) );
					redirect( $_SERVER['HTTP_REFERER'] );
				}
				return;
			}
		}

		if ($this->input->is_ajax_request()) {
			$this->send_cart( lang('unable_to_remove_coupon_code' ), true );
		} else {
			$this->session->set_flashdata( 'success', lang('unable_to_remove_coupon_code' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
	}

	/**
	 * @param string $message
	 * @param bool $error
	 * @param bool $cart
	 */
	protected function send_cart( $message, $error = false, $cart = false ) {
		$data = [ 'message' => $message, ];
    	if ( is_array( $cart ) ) {
    		$data['cart'] = $cart;
	    } else {
    		$data['cart'] = $this->cart->cart_data( true );
	    }
    	if ( $error ) {
    		$data['success'] = false;
		    $data['error'] = 1;
		    $data['status'] = lang( 'error' );
	    }

    	if ( ! $error ) {
		    $data['success'] = true;
		    $data['status'] = lang( 'success' );
	    }
		$this->rerp->send_json( $data );
	}

    public function add_wishlist( $product_id = null ) {
	    $product = absint( $product_id );
	    $product = $product ? $this->shop_model->getProductByID( $product ) : false;
	    if ( ! $this->loggedIn ) {
		    $this->session->set_userdata('requested_page', $_SERVER['HTTP_REFERER']);
		    if ( 'default' === $this->shopThemeName ) {
			    $data = [ 'redirect' => site_url( 'login' ) ];
		    } else {
			    $data = [
				    'status'  => lang( 'warning' ),
				    'message' => sprintf(
					    lang( 'wishlist_login_required' ),
					    site_url( 'login' ),
					    site_url( 'login' ),
					    get_product_permalink( $product ),
					    $product->name,
					    shop_url( 'wishlist' )
				    ),
				    'level'   => 'warning',
			    ];
		    }

		    $this->rerp->send_json( $data );
	    }

		if ( ! $product ) {
			$this->invalidRequestResponse();
		}
	    // check
	    if ( $this->shop_model->getWishlist( true ) >= 10 ) {
	        $data = [
		        'status'  => lang( 'warning' ),
		        'message' => lang( 'max_wishlist' ),
		        'level'   => 'warning',
	        ];
        } else {
	        if ( $this->shop_model->addWishlist( $product->id ) ) {
		        $data = [
			        'status'  => lang( 'success' ),
			        'message' => lang( 'added_wishlist' ),
			        'level'   => 'success',
			        'total'   => $this->shop_model->getWishlist(true),
		        ];
	        } else {
		        $data = [
			        'status'  => lang( 'info' ),
			        'message' => lang( 'product_exists_in_wishlist' ),
			        'level'   => 'info',
		        ];
	        }
        }

	    if ( ! $this->doingAjax ) {
		    $this->session->set_flashdata( $data['level'], $data['message'] );
		    redirect( $_SERVER['HTTP_REFERER'] );
	    }
	    // Response.
	    $this->rerp->send_json( $data );
    }

	public function remove_wishlist( $product_id = null ) {

		$product_id = absint( $product_id );

		if ( ! $product_id || ! $this->loggedIn ) {
			$this->invalidRequestResponse();
		}

		$this->session->set_userdata('requested_page', $_SERVER['HTTP_REFERER']);
		if ( $this->shop_model->removeWishlist( $product_id ) ) {
			$data = [
				'status'  => lang( 'success' ),
				'message' => lang( 'removed_wishlist' ),
				'level'   => 'success',
				'total'   => $this->shop_model->getWishlist( true ),
			];
		} else {
			$data = [
				'status'  => lang( 'error' ),
				'message' => lang( 'error_occurred' ),
				'level'   => 'error',
			];
		}

		if ( ! $this->doingAjax ) {
			$this->session->set_flashdata( $data['level'], $data['message'] );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		// Response.
		$this->rerp->send_json( $data );
	}

    public function checkout() {
        $this->session->set_userdata( 'requested_page', $this->uri->uri_string() );
	    if ( $this->cart->total_items() < 1 ) {
		    $this->session->set_flashdata( 'reminder', lang( 'cart_is_empty' ) );
		    shop_redirect( 'products' );
	    }

	    if ( $this->shop_settings->minimum_order > $this->cart->total() ) {
		    $this->session->set_flashdata( 'reminder', sprintf( lang('minimum_order_amount_x'), $this->rerp->convertMoney( $this->shop_settings->minimum_order ) ) );
		    shop_redirect( 'products' );
	    }

        $this->data['sslcommerz'] = $this->shop_model->getSslcommerzSettings();
        $this->data['paypal']     = $this->shop_model->getPaypalSettings();
	    $this->data['authorize']  = $this->shop_model->getAuthorizeSettings();
        $this->data['skrill']     = $this->shop_model->getSkrillSettings();
	    $this->data['cod']        = (object) ci_parse_args( $this->Erp_Options->getOption( 'cash_on_delivery', [] ), [ 'active' => 1 ] );
	    $this->data['bank']       = $this->bank;
        $this->data['addresses']  = $this->loggedIn ? $this->getAddresses() : false;
        $this->data['wallet']     = $this->get_wallet_for_checkout( false, true );
        $this->data['page_title'] = lang('checkout');

	    $this->data['countries'] = $this->shop_model->getShippingCountries(true );
//	    $this->data['states'] = $this->shop_model->getShippingStates('BD', true );
//	    $this->data['cities'] = $this->shop_model->getShippingCities('BD', 'BD-13' );
//	    $this->data['areas'] = $this->shop_model->getShippingAreas(1 );

        $this->page_construct('pages/checkout', $this->data);
    }

    protected function getAddresses() {
		$addresses = $this->shop_model->getAddresses();
		if ( ! empty( $addresses ) ) {
			foreach ( $addresses as &$address ) {
				if ( $address->area ) {
					$area = new Erp_Shipping_Zone_Area( $address->area );
					$address->area_name = $area->getName();
				} else {
					$address->area = '';
				}
			}
		}
	    return $addresses;
    }

    public function destroy()
    {
	    if ($this->cart->destroy()) {
		    $this->session->set_flashdata('message', lang('cart_items_deleted'));
	    	if ($this->input->is_ajax_request()) {
			    $this->rerp->send_json(['redirect' => base_url()]);
			    return;
		    }
	    } else {
		    if ($this->input->is_ajax_request()) {
			    $this->send_cart( lang('error_occurred'), true );
			    return;
		    }
		    else {
			    $this->session->set_flashdata('error', lang('error_occurred'));
		    }
	    }
	    redirect( $_SERVER['HTTP_REFERER'] );
    }

    public function index()
    {
        $this->session->set_userdata('requested_page', $this->uri->uri_string());
        if ($this->cart->total_items() < 1) {
            $this->session->set_flashdata('reminder', lang('cart_is_empty'));
            shop_redirect('products');
        }
        $this->data['page_title'] = lang('shopping_cart');

	    if ( 'default' === $this->shopThemeName ) {
		    $this->page_construct('pages/cart', $this->data);
	    } else {
		    $this->page_construct( 'index', $this->data );
	    }

    }

    private function checkProductStock( $product, $qty, $option_id = null )
    {
        if ($product->type == 'service' || $product->type == 'digital') {
            return false;
        }
        $chcek = [];
        if ($product->type == 'standard') {
            $quantity = 0;
            if ($pis = $this->site->getPurchasedItems($product->id, $this->shop_settings->warehouse, $option_id)) {
                foreach ($pis as $pi) {
                    $quantity += $pi->quantity_balance;
                }
            }
            $chcek[] = ($qty <= $quantity);
        } elseif ($product->type == 'combo') {
            $combo_items = $this->site->getProductComboItems($product->id, $this->shop_settings->warehouse);
            foreach ($combo_items as $combo_item) {
                if ($combo_item->type == 'standard') {
                    $quantity = 0;
                    if ($pis = $this->site->getPurchasedItems($combo_item->id, $this->shop_settings->warehouse, $option_id)) {
                        foreach ($pis as $pi) {
                            $quantity += $pi->quantity_balance;
                        }
                    }
                    $chcek[] = (($combo_item->qty * $qty) <= $quantity);
                }
            }
        }
        return empty($chcek) || in_array(false, $chcek);
    }

    private function invalidRequestResponse() {
	    if ( ! $this->doingAjax ) {
		    $this->session->set_flashdata('error', lang('invalid_request' ) );
		    if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			    redirect( $_SERVER['HTTP_REFERER'] );
		    } else {
			    redirect( site_url() );
		    }
	    } else {
		    $this->rerp->send_json(['status' => lang('error'), 'message' => lang('invalid_request'), 'level' => 'error']);
	    }
    }

    public function getShippingCountries() {
	    $this->rerp->send_json( $this->shop_model->getShippingCountries( true ) );
    }

    public function getShippingStates() {
	    $cc = $this->input->get_post( 'cc' );
	    if ( $this->input->get_post( 'all' ) ) {
	    	$output = ci_get_states( $cc );
	    } else {
		    $output = $this->shop_model->getShippingStates( $cc, true );
	    }
		$states = [];

	    foreach ( $output as $k => $v ) {
			$states[] = ['id'=>$k, 'name'=>$v];
		}

	    if ( $this->input->get_post( 'get_zone' ) ) {
		    $zone = $this->shop_model->getShippingZones( $cc, null, null, null, 'id' );
		    $this->rerp->send_json( [ 'zone' => $zone ? $zone[0]->id : '', 'states' => $states ] );
	    } else {
		    $this->rerp->send_json( $states );
	    }
    }

    public function getShippingCities(){
		$cc = $this->input->get_post( 'cc' );
		$sc = $this->input->get_post( 'sc' );

	    $cities = $this->shop_model->getShippingCities( $cc, $sc );

	    if ( $this->input->get_post( 'get_zone' ) ) {
		    $zone = $this->shop_model->getShippingZones( $cc, null, null, null, 'id' );
		    $this->rerp->send_json( [ 'zone' => $zone ? $zone[0]->id : '', 'cities' => $cities ] );
	    } else {
		    $this->rerp->send_json( $cities );
	    }
    }

    public function getShippingAreas() {
	    $cc   = $this->input->get_post( 'cc' );
	    $sc   = $this->input->get_post( 'sc' );
	    $city = $this->input->get_post( 'city' );
	    $zip  = $this->input->get_post( 'zip' );
	    $zone = $this->shop_model->getShippingZones( $cc, $sc, $city, $zip, 'id' );
	    $area = $this->shop_model->getShippingAreas( $cc, $sc, $city, $zip );
	    // Response.
	    $this->rerp->send_json( [ 'zone' => $zone ? $zone[0]->id : '', 'area' => $area ] );
    }

	public function getAvailableShippingSlots() {
		$area     = $this->input->post( 'area' );
		$date     = $this->input->post( 'date' );
		$_date    = strtotime( $date );
		$error    = '';
		$slots    = [];
		$_slots   = [];
		$today    = $date == date( 'Y-m-d' );
		$tomorrow = '';
	    if ( $area ) {
		    if ( is_null( $date ) || ( $_date && $date >= date( 'Y-m-d' ) ) ) {
			    $tomorrow = date( 'Y-m-d', strtotime( '+1 day', $_date ) );
			    $slots    = $this->shop_model->getAvailableSlots( $area, date( 'Y-m-d', $_date ), true );
			    $_slots   = $this->shop_model->getAvailableSlots( $area, $tomorrow, true );
		    } else {
		    	$error = lang( 'invalid_delivery_date' );
		    }
	    } else {
		    $error = lang( 'invalid_delivery_address' );
	    }
		// Response.
		$this->rerp->send_json( [
			'success' => empty( $error ),
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
			'message' => $error,
		] );
    }

    public function getAvailableShippingMethods() {
	    if ( $this->cart->total() >= absfloat( $this->shop_settings->free_shipping ) && absfloat( $this->shop_settings->free_shipping ) !== absfloat( 0 ) ) {
		    $this->rerp->send_json( [
			    'success' => true,
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
		    $error = '';
		    $data  = [];
		    $_zone  = $this->input->post( 'zone' );
		    $_area  = $this->input->post( 'area' );
		    $_slot  = $this->input->post( 'slot' );
		    if ( $this->shop_model->hasShippingMethod() ) {
			    if ( $_zone ) {
				    $area           = $slot = 0;
				    $zone           = new Erp_Shipping_Zone( $_zone );
				    $methods        = $zone->getShippingMethods();
				    $current_method = $this->cart->shipping();
				    $current_method = isset( $current_method['id'] ) ? $current_method['id'] : 'default';
				    if ( empty( $methods ) ) {
					    $error = lang( 'no_shipping_available' );
				    } else {
					    if ( $this->shop_model->hasShippingArea() && $_area ) {
						    $area = new Erp_Shipping_Zone_Area( $_area );
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
								    'desc'    => $method->getDescription(),
							    ];

							    $isAnyItemChecked = $method->getId() == $current_method;
						    }
						    if ($isAnyItemChecked === false){
							    $data[0]['checked'] = true;
						    }
					    }
				    }
			    }
		    } else {
			    $cost = $this->rerp->convertMoney( $this->shop_settings->shipping );
			    $data[] = [
				    'id'      => 'default',
				    'checked' => true,
				    'name'    => lang( 'flat_rate_shipping_label' ),
				    'cost'    => $cost,
				    'desc'    => '',
			    ];
		    }
		    // Response.
		    $this->rerp->send_json( [
			    'success' => empty( $error ),
			    'data'    => $data,
			    'message' => $error,
		    ] );
	    }
    }

	public function shipping() {
		$method = $this->input->post( 'shipping_method', true );
		$data   = [];
		if ( 'free_shipping' === $method && $this->cart->total() >= absfloat( $this->shop_settings->free_shipping ) ) {
			$data = [
				'success' => true,
				'message' => '',
				'data'    => $this->cart->cart_data( true ),
			];
		} else {
			if ( 'default' !== $method && $this->shop_model->hasShippingMethod() ) {
				$error = '';
				$method = new Erp_Shipping_Method( $method );
				if ( $method->getId() && $method->getIsEnabled() ) {
					$areaCost = $slotCost = 0;
					$zone = new Erp_Shipping_Zone( $method->getZoneId() );
					if ( 'free_shipping' !== $method->getMethodId() && ! empty( $zone->getAreas() ) ) {
						$_area  = $this->input->post( 'area' );
						$area = new Erp_Shipping_Zone_Area( $_area );
						if ( $area->getId() && $area->getIsEnabled() ) {
							$_slot  = $this->input->post( 'slot' );
							if ( ! empty( $area->getSlots() ) ) {
								$slot = new Erp_Shipping_Zone_Area_Slot( $_slot );
								if ( $slot->getId() && $slot->getIsEnabled() ) {
									$slotCost = $slot->getCostAdjustment();
								} else {
									$error = lang( 'invalid_delivery_schedule' );
								}
							}
							$areaCost = $area->getCostAdjustment();
							unset( $slot, $area, $_slot, $_area, $zone );
						} else {
							$error = lang( 'invalid_delivery_address' );
						}
					}
					if ( ! empty( $error ) ) {
						$data = [
							'success' => false,
							'message' => $error,
						];
					} else {
						$cost = $method->getCost() + $areaCost + $slotCost;
						$set  = $this->cart->setShipping( $method->getId(), $method->getName(), $method->getMethodId(), $cost );
						$data = [
							'success' => $set,
							'message' => ! $set ? lang( 'unable_to_save_shipping_method' ) : '',
							'data' => $this->cart->cart_data( true ),
						];
					}
				} else {
					$data = [
						'success' => false,
						'message' => lang( 'invalid_shipping_method' ),
						'data'    => [],
					];
				}
			} else {
				$data = [
					'success' => true,
					'message' => '',
					'data'    => $this->cart->cart_data( true ),
				];
			}
		}

		$this->rerp->send_json( $data );
	}

	protected function getShippingMethods( $slot = null, $area = null, $zone = null ) {
		$methods = [];
		if ( $slot || $area || $zone ) {
			$slot    = new Erp_Shipping_Zone_Area_Slot( $slot );
			$area    = new Erp_Shipping_Zone_Area( $area || $slot->getAreaId() );
			$zone    = new Erp_Shipping_Zone( $zone || $area->getZoneId() );
			$methods = $zone->getShippingMethods();
		}

		return $methods;
	}

	public function get_wallet_for_checkout( $user = false, $return = false ) {
		if ( false === $user ) {
			$user = $this->session->userdata( 'user_id' );
		}
		$data   = false;
		$wallet = Erp_Wallet::get_user_wallet( absint( $user ) );
		if ( $wallet ) {
			$amount = 0;
			if( $wallet->getAmount() > 0 ){
				$free_shipping = absfloat( $this->shop_settings->free_shipping );

				if ( $free_shipping > 0 && absfloat( $this->cart->total() ) >= $free_shipping ) {
					$shipping = 0;
				} else {
					$shipping = $this->cart->shipping();
					$shipping = absfloat( isset( $shipping['cost'] ) ? $shipping['cost'] : $this->shop_settings->shipping );
				}

				$order_data  = $this->site->prepare_order_data( $this->cart->contents(), false, false, true );
				$order_tax   = $this->site->calculateOrderTax( $this->Settings->default_tax_rate2, ( $order_data['total'] + $order_data['product_tax'] ), false );
				$total_tax   = ( $order_data['product_tax'] + $order_tax );
				$grand_total = ( $order_data['total'] + $total_tax + $shipping );
				//$grand_total = preg_replace( '/^\D/', '', $cart['grand_total'] );
				$max_uses    = absfloat( $this->shop_settings->wallet_percentage_cart );
				$amount      = $grand_total;
				if ( $max_uses > 0 ) {
					$max_uses    = $this->rerp->formatDecimal( ( ( $grand_total * $max_uses ) / 100 ), 4 );
					$amount      = ( $max_uses > $wallet->getAmount() )  ? $wallet->getAmount() : $max_uses;
				}
			}
			$data = [
				'usable'  => $this->rerp->convertMoney( $amount ),
				'_usable'  => (float) $amount,
				'balance' => $this->rerp->convertMoney( $wallet->getAmount() ),
			];
		}

		if ( $return ) {
			return $data;
		} else {
			$this->rerp->send_json( [
				'success' => false !== $data,
				'message' => false === $data ? lang( 'user_not_found' ) : '',
				'data'    => $data,
			] );
		}
	}
}
