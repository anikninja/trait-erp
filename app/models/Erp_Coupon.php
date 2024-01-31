<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Erp_Coupon extends MY_RetailErp_Model {
	protected $table = 'coupons';

	protected $applied_table = 'applied_coupon';

	/**
	 * @var string
	 */
	public $coupon_code;

	/**
	 * @var string
	 */
	public $coupon_type;

	/**
	 * @var float
	 */
	public $coupon_amount;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 */
	public $start_date;

	/**
	 * @var string
	 */
	public $end_date;

	/**
	 * @var float
	 */
	public $minimum_spend;

	/**
	 * @var float
	 */
	public $maximum_spend;

	/**
	 * @var int
	 */
	public $exclude_sale_items;

	/**
	 * @var int[]
	 */
	public $products;

	/**
	 * @var int[]
	 */
	public $exclude_products;

	/**
	 * @var int[]
	 */
	public $product_categories;

	/**
	 * @var int[]
	 */
	public $exclude_categories;

	/**
	 * @var string[]
	 */
	public $allowed_emails;

	/**
	 * @var int
	 */
	public $usage_limit_per_coupon;

	/**
	 * @var int
	 */
	public $limit_usage_items;

	/**
	 * @var int
	 */
	public $usage_limit_per_user;

	/**
	 * @var string
	 */
	public $status = 'active';

	/**
	 * @var int
	 */
	public $created_by;

	/**
	 * @var string
	 */
	public $created_at;

	/**
	 * @var int
	 */
	public $updated_by;

	/**
	 * @var string
	 */
	public $updated_at;

	/**
	 * @var int
	 */
	public $delete_flag = 0;

	/**
	 * @return string
	 */
	public function getCouponCode() {
		return $this->coupon_code;
	}

	/**
	 * @param string $coupon_code
	 */
	public function setCouponCode( $coupon_code ) {
		$this->coupon_code = $coupon_code;
	}

	/**
	 * @return string
	 */
	public function getCouponType() {
		return $this->coupon_type;
	}

	/**
	 * @param string $coupon_type
	 */
	public function setCouponType( $coupon_type ) {
		$this->coupon_type = $coupon_type;
	}

	/**
	 * @return float
	 */
	public function getCouponAmount() {
		return $this->coupon_amount;
	}

	/**
	 * @param float $coupon_amount
	 */
	public function setCouponAmount( $coupon_amount ) {
		$this->coupon_amount = $coupon_amount;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = strip_tags($description);
	}

	/**
	 * @return string
	 */
	public function getStartDate() {
		return $this->start_date;
	}

	/**
	 * @param string $start_date
	 */
	public function setStartDate( $start_date ) {
		$this->start_date = $start_date;
	}

	/**
	 * @return string
	 */
	public function getEndDate() {
		return $this->end_date;
	}

	/**
	 * @param string $end_date
	 */
	public function setEndDate( $end_date ) {
		$this->end_date = $end_date;
	}

	/**
	 * @return float
	 */
	public function getMinimumSpend() {
		return $this->minimum_spend;
	}

	/**
	 * @param float $minimum_spend
	 */
	public function setMinimumSpend( $minimum_spend ) {
		$this->minimum_spend = absfloat( $minimum_spend );
	}

	/**
	 * @return float
	 */
	public function getMaximumSpend() {
		return $this->maximum_spend;
	}

	/**
	 * @param float $maximum_spend
	 */
	public function setMaximumSpend( $maximum_spend ) {
		$this->maximum_spend = absfloat( $maximum_spend );
	}

	/**
	 * @return int
	 */
	public function getExcludeSaleItems() {
		return $this->exclude_sale_items;
	}

	/**
	 * @param int $exclude_sale_items
	 */
	public function setExcludeSaleItems( $exclude_sale_items ) {
		$this->exclude_sale_items = $exclude_sale_items;
	}

	/**
	 * @return array
	 */
	public function getProducts() {
		return $this->products;
	}

	public function getProductSelect2() {

		if ( empty( $this->products ) ) {
			return '[]';
		}

		$this->db->select('id, CONCAT(name, " (", code, ")") as text', false);
		$this->db->where_in( 'id', $this->products );

		$items = $this->db->get('products')->result_array();

		foreach ( $items as &$item ) {
			$item['selected'] = true;
		}

		return json_encode( $items );
	}

	/**
	 * @param string $products
	 */
	public function setProducts( $products ) {
		$this->products = sanitize_comma_separated_integers( $products, false );
	}

	/**
	 * Get Exclude products.
	 *
	 * @return int[]
	 */
	public function getExcludeProducts() {
		return $this->exclude_products;
	}

	public function getExcludeProductsSelect2() {
		if ( empty( $this->exclude_products ) ) {
			return '[]';
		}

		$this->db->select('id, CONCAT( name, " (", code, ")") as text', false);
		$this->db->where_in( 'id', $this->exclude_products );

		$items = $this->db->get('products')->result_array();

		foreach ( $items as &$item ) {
			$item['selected'] = true;
		}

		return json_encode( $items );
	}

	/**
	 * @param string $exclude_products
	 */
	public function setExcludeProducts( $exclude_products ) {
		$this->exclude_products = sanitize_comma_separated_integers( $exclude_products, false );
	}

	/**
	 *
	 * @return int[]
	 */
	public function getProductCategories() {
		return $this->product_categories;
	}

	public function getProductCategoriesSelect2() {
		if ( empty( $this->product_categories ) ) {
			return '[]';
		}

		$this->db->select('id, CONCAT(name, " (", code, ")") as text', false);
		$this->db->where_in( 'id', $this->product_categories );

		$items = $this->db->get('categories')->result_array();

		foreach ( $items as &$item ) {
			$item['selected'] = true;
		}

		return json_encode( $items );
	}

	/**
	 * @param string $product_categories
	 */
	public function setProductCategories( $product_categories ) {
		$this->product_categories = sanitize_comma_separated_integers( $product_categories, false );
	}

	/**
	 *
	 * @return int[]
	 */
	public function getExcludeCategories() {
		return $this->exclude_categories;
	}

	public function getExcludeCategoriesSelect2() {
		if ( empty( $this->exclude_categories ) ) {
			return '[]';
		}

		$this->db->select('id, CONCAT(name, " (", code, ")") as text', false);
		$this->db->where_in( 'id', $this->exclude_categories );

		$items = $this->db->get('categories')->result_array();

		foreach ( $items as &$item ) {
			$item['selected'] = true;
		}

		return json_encode( $items );
	}

	/**
	 * @param string $exclude_categories
	 */
	public function setExcludeCategories( $exclude_categories ) {
		$this->exclude_categories = sanitize_comma_separated_integers( $exclude_categories, false );
	}

	/**
	 * @return string[]
	 */
	public function getAllowedEmails() {
		return $this->allowed_emails;
	}

	public function getAllowedEmailsSelect2() {
		if ( empty( $this->allowed_emails ) ) {
			return '[]';
		}

		$this->db->select('email as id, CONCAT(first_name, " ", last_name, " (", email, ")") as text', false);
		$this->db->where_in( 'email', $this->allowed_emails );

		$items = $this->db->get('users')->result_array();

		foreach ( $items as &$item ) {
			$item['selected'] = true;
		}


		return json_encode( $items );
	}

	/**
	 * @param string $allowed_emails
	 */
	public function setAllowedEmails( $allowed_emails ) {
		$this->allowed_emails = sanitize_comma_separated_emails( $allowed_emails, false );
	}

	/**
	 * @return int
	 */
	public function getUsageLimitPerCoupon() {
		return $this->usage_limit_per_coupon;
	}

	/**
	 * @param int $usage_limit_per_coupon
	 */
	public function setUsageLimitPerCoupon( $usage_limit_per_coupon ) {
		$this->usage_limit_per_coupon = $usage_limit_per_coupon;
	}

	/**
	 * @return int
	 */
	public function getLimitUsageItems() {
		return $this->limit_usage_items;
	}

	/**
	 * @param int $limit_usage_items
	 */
	public function setLimitUsageItems( $limit_usage_items ) {
		$this->limit_usage_items = $limit_usage_items;
	}

	/**
	 * @return int
	 */
	public function getUsageLimitPerUser() {
		return $this->usage_limit_per_user;
	}

	/**
	 * @param int $usage_limit_per_user
	 */
	public function setUsageLimitPerUser( $usage_limit_per_user ) {
		$this->usage_limit_per_user = $usage_limit_per_user;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string $status
	 */
	public function setStatus( $status ) {
		$this->status = $status;
	}

	/**
	 * @return int
	 */
	public function getCreatedBy() {
		return $this->created_by;
	}

	/**
	 * @param int $created_by
	 */
	public function setCreatedBy( $created_by ) {
		$this->created_by = $created_by;
	}

	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->created_at;
	}

	/**
	 * @param string $created_at
	 */
	public function setCreatedAt( $created_at ) {
		$this->created_at = $created_at;
	}

	/**
	 * @return int
	 */
	public function getUpdatedBy() {
		return $this->updated_by;
	}

	/**
	 * @param int $updated_by
	 */
	public function setUpdatedBy( $updated_by ) {
		$this->updated_by = $updated_by;
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->updated_at;
	}

	/**
	 * @param string $updated_at
	 */
	public function setUpdatedAt( $updated_at ) {
		$this->updated_at = $updated_at;
	}

	/**
	 * @return int
	 */
	public function getDeleteFlag() {
		return $this->delete_flag;
	}

	/**
	 * @param int $delete_flag
	 */
	public function setDeleteFlag( $delete_flag ) {
		$this->delete_flag = $delete_flag;
	}
	
	protected function getData() {
		$data = parent::getData();
		
		if ( is_array( $data['products'] ) ) {
			$data['products'] = implode( ',', $data['products'] );
		}

		if ( is_array( $data['exclude_products'] ) ) {
			$data['exclude_products'] = implode( ',', $data['exclude_products'] );
		}

		if ( is_array( $data['product_categories'] ) ) {
			$data['product_categories'] = implode( ',', $data['product_categories'] );
		}

		if ( is_array( $data['exclude_categories'] ) ) {
			$data['exclude_categories'] = implode( ',', $data['exclude_categories'] );
		}

		if ( is_array( $data['allowed_emails'] ) ) {
			$data['allowed_emails'] = implode( ',', $data['allowed_emails'] );
		}
		
		return $data;
	}

	public function save() {
		$update_sequence = false;

		if ( ! $this->coupon_code ) {
			$update_sequence = true;
			$this->coupon_code = $this->get_sequential_reference( 'COUPON' );
		}

		if ( $this->created_by == null ) {
			$this->created_by = $this->session->userdata( 'user_id' );
		}

		if ( ! $this->created_at ) {
			$this->created_at = $this->format_date();
		}

		if ( $this->updated_by == null ) {
			$this->updated_by = $this->session->userdata( 'user_id' );
		}

		$this->updated_at = $this->format_date();

		if ( parent::save() ) {
			if ( $update_sequence ) {
				$this->update_sequential_reference( 'COUPON' );
			}

			return true;
		}

		return false;
	}

	/**
	 * Get Coupon by code.
	 *
	 * @param string $code Coupon Code.
	 *
	 * @return Erp_Coupon|false
	 */
	public static function getByCode( $code ) {
		/** @noinspection PhpUndefinedFieldInspection */
		$row = self::get_ci_instance()->db
			->select( 'id' )
			->where( 'status', 'active' )
			->where( 'delete_flag', 0 )
			->get_where( 'coupons', [ 'coupon_code' => $code ], 1 )
			->row();

		if ( $row && $row->id ) {
			return new self( $row->id );
		}

		return false;
	}

	public function isActive() {
		return 'active' === $this->status;
	}

	protected function count_uses_by_user( $user ) {

		if ( is_a( $user, 'Erp_User' ) ) {
			$user = $user->getId();
		} else {
			$user = $this->absint( $user );
		}

		return $this->count_all_where(
			[
				'coupon_id' => $this->getId(),
				'user_id'   => $user
			],
			$this->applied_table
		);
	}

	protected function count_uses_by_email( $email ) {

		return $this->count_all_where(
			[
				'coupon_id' => $this->getId(),
				'email'     => $email
			],
			$this->applied_table
		);
	}

	protected function get_total_uses() {

		return $this->count_all_where( [ 'coupon_id' => $this->getId(), ], $this->applied_table );
	}

	public function is_limit_expired() {

		if ( $this->usage_limit_per_coupon ) {
			if ( $this->get_total_uses() >= $this->usage_limit_per_coupon ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * check if user's or guest's (email) usage limit is expired for coupon.
	 *
	 * @param Erp_User|int $user
	 * @param string $emailgit
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function is_user_limit_expired( $user = null, $email = null ) {
		if ( $this->is_limit_expired() ) {
			throw new Exception( lang( 'coupon_expired' ) );
		}

		if ( $this->usage_limit_per_user ) {
			if ( ! $user && ! $email ) {
				throw new Exception( lang( 'user_or_email_required' ) );
			}

			if ( $user ) {

				$user = $this->get_user( $user );

				if ( $this->count_uses_by_user( $user ) >= $this->usage_limit_per_user ) {
					throw new Exception( lang( 'coupon_uses_limit_expired' ) );
				}
			}

			if ( $email ) {
				if ( ! is_email( $email ) ) {
					throw new Exception( lang( 'invalid_email' ) );
				}

				if ( $this->count_uses_by_email( $email ) >= $this->usage_limit_per_user ) {
					throw new Exception( lang( 'coupon_uses_limit_expired' ) );
				}
			}
		}

		return false;
	}

	/**
	 * check if user or guest (email) is allowed to use coupon
	 *
	 * @param Erp_User|int $user
	 * @param string $email
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function is_user_allowed( $user = null, $email = null ) {
		if ( $this->allowed_emails ) {
			if ( ! $user && ! $email ) {
				throw new Exception( lang( 'user_or_email_required' ) );
			}

			if ( $user ) {
				$user = $this->get_user( $user );

				if ( ! in_array( $user->getEmail(), $this->allowed_emails ) ) {
					throw new Exception( lang( 'you_are_not_allowed_to_use_this_coupon' ) );
				}
			}

			if ( $email ) {
				if ( ! is_email( $email ) ) {
					throw new Exception( lang( 'invalid_email' ) );
				}

				if ( ! in_array( $email, $this->allowed_emails ) ) {
					throw new Exception( lang( 'you_are_not_allowed_to_use_this_coupon' ) );
				}
			}
		}

		return true;
	}

	/**
	 * Check if this coupon is usable at all..
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function can_use() {

		if ( ! $this->exists() || $this->getDeleteFlag() ) {
			throw new Exception( lang( 'coupon_not_exists' ) );
		}

		if ( ! $this->isActive() ) {
			throw new Exception( lang( 'coupon_is_not_available' ) );
		}

		$date = $this->format_date();

		if ( $date < $this->start_date ) {
			throw new Exception( lang( 'this_coupon_is_not_available_for_use' ) );
		}

		if ( $date > $this->end_date ) {
			throw new Exception( lang( 'coupon_expired' ) );
		}

		if ( $this->is_limit_expired() ) {
			throw new Exception( lang( 'coupon_expired' ) );
		}

		return true;
	}

	/**
	 * Check if the user or guest (email) can use coupon.
	 *
	 * @param int $user
	 * @param string $email
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function can_user_use( $user = null, $email = null ) {

		return $this->can_use() &&
		       $this->is_user_allowed( $user, $email ) &&
		       ! $this->is_user_limit_expired( $user, $email );
	}

	/**
	 * check if coupon is applicable in a cart.
	 *
	 * @param float $cart_amount
	 * @param null $user
	 * @param null $email
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function can_apply( $cart_amount, $user = null, $email = null ) {
		if ( $this->minimum_spend > 0 && $cart_amount < $this->minimum_spend ) {
			throw new Exception( sprintf( lang( 'minimum_spend_x' ), $this->minimum_spend ) );
		}

		if ( $this->maximum_spend > 0 && $cart_amount > $this->maximum_spend ) {
			throw new Exception( sprintf( lang('maximum_spend_x' ), $this->maximum_spend ) );
		}

		if ( $user || $email ) {
			return $this->can_user_use( $user, $email );
		}

		return $this->can_use();
	}

	/**
	 *
	 * @param $cart_amount
	 * @param $products
	 *
	 * @return array
	 * @throws Exception
	 */
	public function calculate_discount( $cart_amount, $products ) {
		try {
			$this->can_apply( $cart_amount );
		} catch ( Exception $e ) {
			return [
				'discount' => 0,
				'items'    => $products,
			];
		}

		if ( $cart_amount < $this->coupon_amount ) {
			return [
				'discount' => $cart_amount,
				'items'    => $products,
			];
		}

		switch ( $this->coupon_type ) {
			case 'fixed_cart':
				$sum = absfloat( ( $cart_amount <= $this->coupon_amount ) ? $cart_amount : $this->coupon_amount );

				return [ 'discount' => $sum, 'items' => $products ];
			case 'fixed_product':
				$items_sum = 0;
				foreach ( $products as &$item ) {
					if ( empty( $this->products ) && empty( $this->product_categories) ) {
						$item['discount'] = absfloat( ( $item['subtotal'] <= $this->coupon_amount ) ? $item['subtotal'] : $this->coupon_amount );
						$items_sum += $item['discount'];
					} else {
						if ( in_array( $item['product_id'], $this->products ) || in_array( $item['category_id'], $this->product_categories ) ) {
							$item['discount'] = absfloat( ( $item['subtotal'] <= $this->coupon_amount ) ? $item['subtotal'] : $this->coupon_amount );
							$items_sum += $item['discount'];
						}
					}
				}

				return [ 'discount' => $items_sum, 'items' => $products ];
			case 'percentage':
				$sum = 0;
				if ( empty( $this->products ) && empty( $this->product_categories) ) {
					$sum = absfloat( ( $cart_amount * $this->coupon_amount ) / 100 );
				} else {
					foreach ( $products as &$item ) {
						if ( in_array( $item['product_id'], $this->products ) || in_array( $item['category_id'], $this->product_categories ) ) {
							$item['discount'] = absfloat( ( $item['subtotal'] * $this->coupon_amount ) / 100 );
							$sum              += $item['discount'];
						}
					}
				}

				return [ 'discount' => $sum, 'items' => $products ];
			default:
				return [ 'discount' => 0, 'items' => $products ];
		}
	}

	/**
	 * @param int $sale_id
	 * @param int $user_id
	 * @param string $email
	 *
	 * @return false|int
	 * @throws Exception
	 */
	public function apply_coupon( $sale_id, $user_id = NULL, $email = NULL ) {
		$sale_id = $this->absint( $sale_id );
		if ( ! $sale_id ) {
			throw new Exception( lang( 'invalid_invoice_id' ) );
		}

		if ( ! $user_id && ! $email ) {
			throw new Exception( lang( 'user_or_email_required' ) );
		}
		$data = [
			'coupon_id' => $this->getId(),
			'sale_id'   => $sale_id,
			'user_id'   => $user_id,
			'email'     => $email,
		];
		if ( $this->db->insert( $this->applied_table, $data ) ) {
			return $this->db->insert_id();
		}

		return FALSE;
	}
}

// End of file Erp_Coupon.php.
