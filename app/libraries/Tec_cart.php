<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Tec_cart
 *
 * @property CI_Session $session
 * @property Rerp $rerp
 * @property CI_DB_mysqli_driver $db
 * @property CI_Loader $load
 * @property Object $selected_currency
 * @property Object $shop_settings
 * @property Object $Settings
 */
class Tec_cart {

	/**
	 * @var string|bool
	 */
	public $cart_id = false;

	/**
	 * @var string
	 */
	public $product_id_rules = '\.a-z0-9_-';

	/**
	 * @var string
	 */
	public $product_name_rules = '\s\S'; // '\w \-\.\:';

	/**
	 * @var bool
	 */
	public $product_name_safe = true;

	/**
	 * @var array
	 */
	protected $_cart_contents = [];

	/**
	 * @var array
	 */
	protected $_item_qty = [];

	/**
	 * Tec_cart constructor.
	 */
	public function __construct() {
		$this->load->helper( 'cookie' );
		if ( $cart_id = get_cookie( 'cart_id', true ) ) {
			$this->cart_id        = $cart_id;
			$result               = $this->db
				->get_where( 'cart', [ 'id' => $this->cart_id ] )
				->row();
			$this->_cart_contents = $result ? json_decode( $result->data, true ) : null;
		} else {
			$this->_setup();
		}

		if ( empty( $this->_cart_contents ) ) {
			$this->_empty();
		}
    }
	
	/**
	 * Insert item to cart and save to db
	 * @param array $items
	 *
	 * @return bool|string
	 */
	protected function _insert( $items = [] ) {
		if ( ! is_array( $items ) or count( $items ) === 0 ) {
			return false;
		}
		
		$items['name'] = htmlentities( $items['name'] );
		
		if ( ! isset( $items['id'], $items['qty'], $items['name'] ) ) {
			return false;
		}
		
		$items['qty'] = (float) $items['qty'];
		
		if ( $items['qty'] == 0 ) {
			return false;
		}
		
		if ( ! preg_match( '/^[' . $this->product_id_rules . ']+$/i', $items['id'] ) ) {
			return false;
		}
		
		/** @noinspection RegExpUnexpectedAnchor */
		if ( $this->product_name_safe && ! preg_match( '/^[' . $this->product_name_rules . ']+$/i' . ( UTF8_ENABLED ? 'u' : '' ), $items['name'] ) ) {
			return false;
		}
		
		$items['price'] = (float) $items['price'];
		
		if ( isset( $items['options'] ) && count( $items['options'] ) > 0 ) {
			$rowid = md5( $items['id'] . serialize( (array) $items['options'] ) );
		} else {
			$rowid = md5( $items['id'] );
		}

		/** @noinspection DuplicatedCode */
		$old_quantity = isset( $this->_cart_contents[ $rowid ]['qty'] ) ? (int) $this->_cart_contents[ $rowid ]['qty'] : 0;

		$items['rowid'] = $rowid;
		$items['qty']   += $old_quantity;

		// Add to contents.
		$this->_cart_contents[ $rowid ] = $items;
		$this->_cart_contents[ 'products' ][] = $items['product_id'];
		$this->_cart_contents[ 'categories' ][] = $items['category_id'];

		if ( $items['on_sale'] ) {
			$this->_cart_contents['has_on_sale'] = TRUE;
		}
		if ( $items['special'] ) {
			$this->_cart_contents['has_special'] = TRUE;
		}
		if ( $items['cash_back'] ) {
			$this->_cart_contents['has_cash_back'] = TRUE;
		}

		return $rowid;
	}
	
	/**
	 * Update cart item array for single item data.
	 * @param array $items
	 *
	 * @return bool
	 */
	protected function _update( $items = [] ) {
		if ( ! isset( $items['rowid'], $this->_cart_contents[ $items['rowid'] ] ) ) {
			return false;
		}
		
		if ( isset( $items['qty'] ) ) {
			$items['qty'] = (float) $items['qty'];
			if ( $items['qty'] == 0 ) {
				unset( $this->_cart_contents[ $items['rowid'] ] );
				
				return true;
			}
		}
		
		$keys = array_intersect( array_keys( $this->_cart_contents[ $items['rowid'] ] ), array_keys( $items ) );
		if ( isset( $items['price'] ) ) {
			$items['price'] = (float) $items['price'];
		}
		
		foreach ( array_diff( $keys, [ 'id', 'name' ] ) as $key ) {
			$this->_cart_contents[ $items['rowid'] ][ $key ] = $items[ $key ];
		}
		
		return true;
	}

	/**
	 * Check coupon.
	 *
	 * @param Erp_Coupon $coupon
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function check_coupon ( $coupon ) {
		$user = $email = null;
		if ( $this->loggedIn ) {
			$user = $this->loggedInUser->id;
			$email = $this->loggedInUser->email;
		} else {
			if ( $this->input->post( 'email' ) ) {
				$email = $this->input->post( 'email' );
			}
		}

		if ( $user || $email ) {
			$coupon->can_user_use( $user, $email );
		} else {
			$coupon->can_use();
		}

		if ( $coupon->getExcludeSaleItems() ) {
			if ( $this->_cart_contents['has_on_sale'] || $this->_cart_contents['has_special'] || $this->_cart_contents['has_cash_back'] ) {
				throw new Exception( lang( 'has_on_sale' ) );
			}
		}

		if ( ! empty( $coupon->getExcludeProducts() ) ) {
			$ex_pp = array_intersect( $this->_cart_contents['products'], $coupon->getExcludeProducts() );
			if ( ! empty( $ex_pp ) ) {
				throw new Exception( lang( 'has_exclude_products' ) );
			}
		}

		if ( ! empty( $coupon->getExcludeCategories() ) ) {
			$ex_pp = array_intersect( $this->_cart_contents['categories'], $coupon->getExcludeCategories() );
			if ( ! empty( $ex_pp ) ) {
				throw new Exception( lang( 'has_exclude_categories' ) );
			}
		}

		$total = $this->total( FALSE );
		if ( ! empty( $this->_cart_contents['coupons'] ) ) {
			try {
				foreach ( $this->_cart_contents['coupons'] as $hash => $code ) {
					$coupon = Erp_Coupon::getByCode( $code );
					if ( $coupon ) {
						try {
							$calculated = $coupon->calculate_discount( $total, $this->contents() );
							if ( $calculated['discount'] ) {

								if ( $total > $calculated['discount'] ) {
									$total -= $calculated['discount'];
								} else {
									$total = 0;
								}
							}
						} catch ( Exception $e ) {}
					}
				}
			} catch ( Exception $e ) {}
		}

		if ( $coupon->getMinimumSpend() && $total < $coupon->getMinimumSpend() ) {
			throw new Exception( sprintf( lang( 'minimum_spend_x' ), $this->rerp->convertMoney( $coupon->getMinimumSpend() ) ) );
		}

		if ( $coupon->getMaximumSpend() && $total > $coupon->getMaximumSpend() ) {
			throw new Exception( sprintf( lang('maximum_spend_x' ), $this->rerp->convertMoney( $coupon->getMaximumSpend() ) ) );
		}

		return true;
	}

	/**
	 * Apply coupon.
	 *
	 * @param Erp_Coupon $coupon
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function apply_coupon( $coupon ) {

		$this->check_coupon( $coupon );

		$code = $coupon->getCouponCode();
		$hash = md5( $code );

		if ( isset( $this->_cart_contents['coupons'][ $hash ] ) ) {
			throw new Exception( lang( 'coupon_already_used' ) );
		}

		$this->_cart_contents['coupons'][ $hash ] = $code;

		if ( $this->_save_cart() ) {
			return true;
		}

		return false;
	}

	/**
	 * Remove Coupon from cart.
	 *
	 * @param string $hash coupon code or hash.
	 *
	 * @return bool
	 */
	public function remove_coupon( $hash ) {

		if ( 32 != strlen( $hash ) ) {
			$hash = md5( $hash );
		}

		unset( $this->_cart_contents['coupons'][ $hash ] );

		if ( $this->_save_cart() ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	protected function _save_cart() {
		$this->_cart_contents['cart_total']         = 0;
		$this->_cart_contents['total_items']        = 0;
		$this->_cart_contents['total_item_tax']     = 0;
		$this->_cart_contents['total_unique_items'] = 0;
		
		foreach ( $this->_cart_contents as $key => $val ) {
			if ( ! is_array( $val ) or ! isset( $val['price'], $val['qty'] ) ) {
				continue;
			}
			
			$this->_cart_contents['total_unique_items'] += 1;
			$this->_cart_contents['total_items']        += $val['qty'];
			$this->_cart_contents['cart_total']         += $this->rerp->formatDecimal( ( $val['price'] * $val['qty'] ), 4 );
			$this->_cart_contents['total_item_tax']     += $this->rerp->formatDecimal( ( $val['tax'] * $val['qty'] ), 4 );
			$this->_cart_contents[ $key ]['row_tax']    = $this->rerp->formatDecimal( ( $this->_cart_contents[ $key ]['tax'] * $this->_cart_contents[ $key ]['qty'] ), 4 );
			$this->_cart_contents[ $key ]['subtotal']   = $this->rerp->formatDecimal( ( $this->_cart_contents[ $key ]['price'] * $this->_cart_contents[ $key ]['qty'] ), 4 );
		}
		
		// shipping will be set by the setShipping method.
		// $this->_cart_contents['shipping']           = [ 'name' => '', 'cost' => 0 ];
		// so total element of cart content should be 5 instate of 4.
		if ( count( $this->_cart_contents ) <= 5 ) {
			$this->db->delete( 'cart', [ 'id' => $this->cart_id ] );
			
			return false;
		}
		
		// prepare the data.
		$data = [
			'time'    => time(),
			'user_id' => $this->session->userdata( 'user_id' ),
			'data'    => json_encode( $this->_cart_contents ),
		];
		
		// save to db.
		if ( $this->db->get_where( 'cart', [ 'id' => $this->cart_id ] )->num_rows() > 0 ) {
			return $this->db->update( 'cart', $data, [ 'id' => $this->cart_id ] );
		} else {
			$data['id'] = $this->cart_id;
			return $this->db->insert( 'cart', $data );
		}
	}
	
	/**
	 * @param bool $return
	 * @param bool $include_options
	 *
	 * @return array|void
	 */
	public function cart_data( $return = false, $include_options = true ) {
		$citems = $this->contents();
		foreach ( $citems as &$value ) {
			$value['price']    = $this->rerp->convertMoney( $value['price'] );
			$value['subtotal'] = $this->rerp->convertMoney( $value['subtotal'] );
			if ( $include_options && $this->has_options( $value['rowid'] ) ) {
				$value['options'] = $this->product_options( $value['rowid'] );
				foreach ( $value['options'] as &$opt_value ) {
					$opt_value['price'] = $this->rerp->convertMoney( $opt_value['price'] );
				}
			}
			if ( ! $include_options ) {
				unset( $value['options'] );
			}
		}
		$_shipping   = $this->shipping();
		$total       = $this->rerp->convertMoney( $this->total(), false, false );
		$shipping    = $this->rerp->convertMoney( absfloat( $_shipping['cost'] ), false, false );
		$order_tax   = $this->rerp->convertMoney( $this->order_tax(), false, false );
		$grand_total = $order_tax;
		$cart       = [
			'total_items'        => $this->total_items(),
			'total_unique_items' => $this->total_items( TRUE ),
			'contents'           => $citems,
			'subtotal'           => $this->rerp->convertMoney( $this->total() - $this->total_item_tax() ),
			'total_item_tax'     => $this->rerp->convertMoney( $this->total_item_tax() ),
			'total'              => $this->rerp->formatMoney( $total, $this->selected_currency->symbol ),
			'_total'             => absfloat( $total ),
			'shipping'           => [],
			'order_tax'          => $this->rerp->formatMoney( $order_tax, $this->selected_currency->symbol ),
			'grand_total'        => '',
			'coupons'            => [],
		];

		// Re-assign for discount & grand total calculation.
		$total = $this->total( FALSE );

		if ( ! empty( $this->_cart_contents['coupons'] ) ) {
			$coupons = [];
			$invalid_coupons = [];
			try {
				foreach ( $this->_cart_contents['coupons'] as $hash => $code ) {
					$coupon = Erp_Coupon::getByCode( $code );
					if ( $coupon ) {
						try {
							$this->check_coupon( $coupon );
							$calculated = $coupon->calculate_discount( $total, $this->contents() );
							$coupons[ $hash ] = $calculated;
							$coupons[ $hash ]['code'] = $code;
							$coupons[ $hash ]['discount_price'] = $this->rerp->convertMoney( $calculated['discount'] );
							if ( $calculated['discount'] ) {

								$coupons[ $hash ]['discount_price'] = '-' . $coupons[ $hash ]['discount_price'];

								if ( $total > $calculated['discount'] ) {
									$total -= $calculated['discount'];
								} else {
									$total = 0;
								}
							}
						} catch ( Exception $e ){
							$invalid_coupons[] = sprintf( lang('coupon_x_remove_error_x'), $code, $e->getMessage() );
							unset($this->_cart_contents['coupons'][$hash]);
						}
					}
				}
			} catch ( Exception $e ) {}
			$cart['coupons'] = $coupons;
			if ( ! empty( $invalid_coupons ) ) {
				$this->_save_cart();
				//@TODO Reload the browser
			}
		}

		$grand_total += $this->rerp->convertMoney( $total, false, false );

		if ( $this->total_items() ) {
			$cart['shipping'] = [
				'id'   => $_shipping['id'],
				'type' => $_shipping['type'],
				'name' => $_shipping['name'],
				'cost' => $this->rerp->formatMoney( $shipping, $this->selected_currency->symbol ),
			];
			$grand_total += $shipping;
		}

		$cart['grand_total'] = $this->rerp->formatMoney( $grand_total, $this->selected_currency->symbol );


		if ( $return ) {
			return $cart;
		}
		
		$this->rerp->send_json( $cart );
	}

	/**
	 * @return bool|string
	 */
    public function cart_id() {
	    return $this->cart_id;
    }

	/**
	 * @param false $newest_first
	 *
	 * @return array
	 */
	public function contents( $newest_first = false ) {
		$cart = ( $newest_first ) ? array_reverse( $this->_cart_contents ) : $this->_cart_contents;

		unset(
			$cart['total_items'],
			$cart['total_item_tax'],
			$cart['total_unique_items'],
			$cart['cart_total'],
			$cart['shipping'],
			$cart['coupons'],
			$cart['products'],
			$cart['categories'],
			$cart['has_on_sale'],
			$cart['has_special'],
			$cart['has_cash_back']
		);
		
		return $cart;
	}
	
	/**
	 * Get Cart item by product id.
	 *
	 * @param bool $prod_id
	 * @ param bool $optId
	 *
	 * @return array|bool
	 */
    public function getItems( $prod_id = false ) {
	    if ( empty( $this->_item_qty ) ) {
		    $_items = $this->contents();
		    if ( ! empty( $_items ) ) {
			    $this->_item_qty = [];
			    foreach ( $_items as $row_id => $cart_data ) {
				    $prodId = $cart_data['product_id'];
				    // no support for option now.
				    $this->_item_qty[ $prodId ]['qty']   = $cart_data['qty'];
				    $this->_item_qty[ $prodId ]['rowId'] = $row_id;
			    }
		    }
	    }
	    // -->
	    if ( false !== $prod_id ) {
		    return isset( $this->_item_qty[ $prod_id ] ) ? $this->_item_qty[ $prod_id ] : false;
	    }
	
	    return $this->_item_qty;
    }

	/**
	 * @return bool
	 */
	public function destroy() {
		$this->_empty();
		
		$this->db->delete( 'cart', [ 'id' => $this->cart_id ] );
		return $this->db->affected_rows() > 0;
	}

	/**
	 * @param $row_id
	 *
	 * @return false|array
	 */
	public function get_item( $row_id ) {
	    return ( in_array( $row_id, [ 'total_items', 'cart_total' ], true ) or ! isset( $this->_cart_contents[ $row_id ] ) ) ? false : $this->_cart_contents[ $row_id ];
    }

	/**
	 * @param string $row_id
	 *
	 * @return bool
	 */
	public function has_options( $row_id = '' ) {
		return ( isset( $this->_cart_contents[ $row_id ]['options'] ) && count( $this->_cart_contents[ $row_id ]['options'] ) !== 0 );
	}

	/**
	 * @param array $items
	 *
	 * @return bool|string
	 */
	public function insert( $items = [] ) {
		if ( ! is_array( $items ) or count( $items ) === 0 ) {
			return false;
		}
		
		$save_cart = false;
		if ( isset( $items['id'] ) ) {
			if ( ( $rowid = $this->_insert( $items ) ) ) {
				$save_cart = true;
			}
		} else {
			foreach ( $items as $val ) {
				if ( is_array( $val ) && isset( $val['id'] ) ) {
					if ( $this->_insert( $val ) ) {
						$save_cart = true;
					}
				}
			}
		}
		
		if ( $save_cart === true ) {
			$this->_save_cart();
			return isset( $rowid ) ? $rowid : true;
		}
		
		return false;
	}

	/**
	 * @return int|string
	 */
	public function order_tax() {
		$order_tax = 0;
		if ( ! empty( $this->Settings->tax2 ) ) {
			if ( $order_tax_details = $this->site->getTaxRateByID( $this->Settings->default_tax_rate2 ) ) {
				if ( $order_tax_details->type == 2 || $order_tax_details->rate == 0 ) {
					$order_tax = $this->rerp->formatDecimal( $order_tax_details->rate, 4 );
				} elseif ( $order_tax_details->type == 1 ) {
					$order_tax = $this->rerp->formatDecimal( ( ( ( $this->total() ) * $order_tax_details->rate ) / 100 ), 4 );
				}
			}
		}
		
		return $order_tax;
	}

	/**
	 * @param string $row_id
	 *
	 * @return array|array
	 */
	public function product_options( $row_id = '' ) {
		return isset( $this->_cart_contents[ $row_id ]['options'] ) ? $this->_cart_contents[ $row_id ]['options'] : [];
	}

	/**
	 * @param string $rowid
	 *
	 * @return bool
	 */
	public function remove( $rowid ) {
		$key = array_search( $this->_cart_contents[ $rowid ]['id'], $this->_cart_contents[ 'products' ] );
		if ( false !== $key ) {
			unset( $this->_cart_contents['products'][$key] );
		}

		$key = array_search( $this->_cart_contents[ $rowid ]['category_id'], $this->_cart_contents[ 'categories' ] );
		if ( false !== $key ) {
			unset( $this->_cart_contents['categories'][$key] );
		}

		unset( $this->_cart_contents[ $rowid ] );
		$items = $this->contents();
		$this->_cart_contents['has_on_sale'] = ! empty( array_filter( array_column( $items, 'on_sale' ) ) );
		$this->_cart_contents['has_special'] = ! empty( array_filter( array_column( $items, 'special' ) ) );
		$this->_cart_contents['has_cash_back'] = ! empty( array_filter( array_column( $items, 'cash_back' ) ) );

		return $this->_save_cart();
	}
	
	/**
	 * Set Shipping Cost.
	 * if any of parameters are empty it will set from global settings.
	 *
	 * @param int|string $id default is empty
	 * @param string $name default is empty
	 * @param string $type default is empty
	 * @param float $cost default is empty
	 *
	 * @return bool
	 */
	public function setShipping( $id, $name, $type, $cost ) {
		// @TODO validate shipping method.
		$this->_cart_contents['shipping'] = [
			'id'   => $id,
			'name' => $name,
			'type' => $type,
			'cost' => $this->rerp->formatDecimal( absfloat( $cost ), 4 ),
		];
		
		return $this->_save_cart();
	}

	public function getCoupons() {
		return $this->_cart_contents['coupons'];
	}
	
	public function shipping() {
		if ( $this->total() >= absfloat( $this->shop_settings->free_shipping ) && absfloat( $this->shop_settings->free_shipping ) !== absfloat( 0 ) ) {
			return [
				'id'   => 'free_shipping',
				'type' => 'flat_rate_shipping',
				'name' => lang( 'free_shipping_label' ),
				'cost' => 0,
			];
		}
		
		if ( isset( $this->_cart_contents['shipping'], $this->_cart_contents['shipping']['type'] ) ) {
			return $this->_cart_contents['shipping'];
		}
		
		$default = absfloat( $this->shop_settings->shipping );
		if ( $default > 0 || $this->total() < absfloat( $this->shop_settings->free_shipping ) ) {
			return [
				'id'   => 'default',
				'type' => 'flat_rate_shipping',
				'name' => lang( 'flat_rate_shipping_label' ),
				'cost' => $this->rerp->formatDecimal( $default, 4 ),
			];
		} else {
			return [
				'id'   => '',
				'type' => '',
				'name' => lang( 'no_shipping_method' ),
				'cost' => '',
			];
		}
	}
	
	public function total( $format = true ) {
		return ! $format ? (float) $this->_cart_contents['cart_total'] : $this->rerp->formatDecimal( $this->_cart_contents['cart_total'], 4 );
	}
	
	public function total_item_tax( $format = true ) {
		return ! $format ? (float) $this->_cart_contents['total_item_tax'] : $this->rerp->formatDecimal( $this->_cart_contents['total_item_tax'], 4 );
	}
	
	public function total_items( $unique = false ) {
		return $unique ? $this->_cart_contents['total_unique_items'] : $this->_cart_contents['total_items'];
	}
	
	public function update( $items = [] ) {
		if ( ! is_array( $items ) or count( $items ) === 0 ) {
			return false;
		}
		
		$save_cart = false;
		if ( isset( $items['rowid'] ) ) {
			if ( $this->_update( $items ) === true ) {
				$save_cart = true;
			}
		} else {
			foreach ( $items as $val ) {
				if ( is_array( $val ) && isset( $val['rowid'] ) ) {
					if ( $this->_update( $val ) === true ) {
						$save_cart = true;
					}
				}
			}
		}
		
		if ( $save_cart === true ) {
			$this->_save_cart();
			
			return true;
		}
		
		return false;
	}

	private function _empty() {
		$this->_cart_contents = [
			'cart_total'         => 0,
			'total_item_tax'     => 0,
			'total_items'        => 0,
			'total_unique_items' => 0,
			'shipping'           => FALSE,
			'coupons'            => [],
			'products'           => [],
			'categories'         => [],
			'has_on_sale' => false,
			'has_special' => false,
			'has_cash_back' => false,
		];
	}
	
	private function _setup() {
		$this->load->helper( 'string' );
		$val = md5( random_string( 'alnum', 16 ) . microtime() );
		set_cookie( 'cart_id', $val, 2592000 );
		
		return $this->cart_id = $val;
	}
	
	public function __get( $var ) {
		return get_instance()->$var;
	}
}