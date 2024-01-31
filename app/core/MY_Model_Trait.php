<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class MY_Model
 *
 * @property CI_Loader $load
 * @property Site $site
 * @property CI_DB_mysqli_driver $db
 * @property Rerp $rerp
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Config $config
 * @property MY_Lang $lang
 *
 * @property bool|object $Settings
 *
 * @property string $theme
 *
 * @property bool $loggedIn
 * @property Erp_Menus $Erp_Menus
 * @property Erp_Options $Erp_Options
 * @property bool|object $default_currency
 * @property bool|object $selected_currency
 * @property CI_Form_validation $form_validation
 * @property Shop_model $shop_model
 * @property CI_Router $router
 * @property Tec_cart $cart
 * @property Sms $sms
 *
 * @property bool $Owner
 * @property bool $Customer
 * @property bool $Supplier
 * @property bool $Admin
 * @property bool|null $Staff
 * @property object $loggedInUser
 * @property bool|object $shop_settings
 * @property object $customer
 * @property object $customer_group
 * @property object $warehouse
 * @property array $dateFormats
 * @property array $data
 *
 * @property string $m Current Class Being loaded by the router.
 * @property string $v Current Method (of $m class) Being loaded by the router.
 *
 * @property string $shopThemeName
 * @property string $shopTheme
 * @property string $shopThemeDir
 * @property string $shopAssets
 * @property string $shopAssetsURL
 *
 * @property string $adminThemeName
 * @property string $adminTheme
 * @property string $adminThemeDir
 * @property string $adminAssets
 * @property string $adminAssetsURL
 * @property CI_URI $uri
 * @property bool $doingAjax
 */
trait MY_Model_Trait {
	
	protected $simpleCache = [];
	
	/**
	 * @var CI_Controller
	 */
	protected static $CI;
	
	/**
	 * @return CI_Controller
	 */
	public static function get_ci_instance() {
		if ( null === self::$CI ) {
			self::$CI = & get_instance();
		}
		
		return self::$CI;
	}
	
	/**
	 * @param string $_data
	 * @param string $__data
	 * @param string $___data
	 *
	 * @return string
	 */
	protected function getSimpleCacheKey( $_data, $__data, $___data ) {
		return md5( maybe_serialize( [ 'simple_cache', $_data, $__data, $___data ] ) );
	}
	
	/**
	 * @param string $cacheKey
	 * @param string $group
	 *
	 * @return bool|mixed
	 */
	protected function getSimpleCache( $cacheKey, $group = 'default' ) {
		$group = $group ?? 'default';
		if ( isset( $this->simpleCache[ $group ][ $cacheKey ] ) ) {
			$cached = $this->simpleCache[ $group ][ $cacheKey ];
			// is expired?
			if ( false !== $cached['expire'] || $cached['expired_in'] < time() ) {
				return $cached['data'];
			}
		}
		return false;
	}
	
	/**
	 * @param mixed $value
	 * @param string $cacheKey
	 * @param int $expire
	 * @param string $group
	 *
	 * @return bool
	 */
	protected function setSimpleCache( $value, $cacheKey, $expire = 0, $group = 'default' ) {
		$group = $group ?? 'default';
		$time  = time();
		$this->simpleCache[ $group ][ $cacheKey ] = [
			'data'       => $value,
			'expire'     => 0 !== absint( $expire ),
			'expired_in' => $time + absint( $expire ),
			'created'    => $time,
		];
		return true;
	}
	
	/**
	 * @param string $cacheKey
	 * @param string $group
	 */
	protected function deleteSimpleCache( $cacheKey, $group = 'default' ) {
		$group = $group ?? 'default';
		if ( $this->getSimpleCache( $cacheKey, $group ) ) {
			unset( $this->simpleCache[ $group ][ $cacheKey ] );
		}
	}
	
	/**
	 * @param int    $product_id
	 * @param string $meta_key
	 * @param bool   $single
	 *
	 * @return array|bool|mixed|null
	 */
	public function getProductMeta( $product_id, $meta_key = '', $single = false ) {
		return $this->get_metadata( 'product', $product_id, $meta_key, $single );
	}
	
	public function updateProductMeta( $product_id, $meta_key, $meta_value ) {
		return $this->update_metadata( 'product', $product_id, $meta_key, $meta_value );
	}
	
	public function addProductMeta( $product_id, $meta_key, $meta_value, $unique = false ) {
		return $this->add_metadata( 'product', $product_id, $meta_key, $meta_value, $unique );
	}
	
	/**
	 * Retrieves the value of a metadata field for the specified object type and ID.
	 *
	 * If the meta field exists, a single value is returned if `$single` is true,
	 * or an array of values if it's false.
	 *
	 * If the meta field does not exist, the result depends on get_metadata_default().
	 * By default, an empty string is returned if `$single` is true, or an empty array
	 * if it's false.
	 *
	 * @param string $meta_type Type of object metadata is for. Accepts 'post', 'comment', 'term', 'user',
	 *                          or any other object type with an associated meta table.
	 * @param int    $object_id ID of the object metadata is for.
	 * @param string $meta_key  Optional. Metadata key. If not specified, retrieve all metadata for
	 *                          the specified object. Default empty.
	 * @param bool   $single    Optional. If true, return only the first value of the specified meta_key.
	 *                          This parameter has no effect if meta_key is not specified. Default false.
	 * @return mixed Single metadata value, or array of values.
	 *               False if there's a problem with the parameters passed to the function.
	 */
	public function get_metadata( $meta_type, $object_id, $meta_key = '', $single = false ) {
		if ( ! $meta_type || ! is_numeric( $object_id ) ) {
			return false;
		}
		$object_id = absint( $object_id );
		if ( ! $object_id ) {
			return false;
		}
		
		$table = $this->get_meta_table( $meta_type );
		
		if ( ! $table ) {
			return false;
		}
		$cacheKey = $this->getSimpleCacheKey( $meta_type, $object_id, $meta_key );
		$results = $this->getSimpleCache( $cacheKey );
		
		if ( ! $results ) {
			$column = $meta_type . '_id';
			$where = [ $column => $object_id ];
			
			if ( $meta_key ) {
				$where['meta_key'] = $meta_key;
			}
			$this->db->where( $where );
			
			$results = $this->db->get( $table )->result_array();
			
			if ( $results ) {
				$this->setSimpleCache( $results, $cacheKey );
			}
		}
		
		if ( ! $results ) {
			return null;
		}
		
		if ( $single ) {
			return maybe_unserialize( $results[0]['meta_value'] );
		} else {
			$meta = [];
			foreach ( $results as $idx => $row ) {
				if ( $meta_key ) {
					$meta[$idx] = maybe_unserialize( $row['meta_value'] );
				} else {
					$meta[ $row['meta_key'] ][$idx] = maybe_unserialize( $row['meta_value'] );
				}
				
			}
			
			return $meta;
		}
	}
	
	/**
	 * Adds metadata for the specified object.
	 *
	 *
	 * @param string $meta_type  Type of object metadata is for. Accepts 'post', 'comment', 'term', 'user',
	 *                           or any other object type with an associated meta table.
	 * @param int    $object_id  ID of the object metadata is for.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param bool   $unique     Optional. Whether the specified metadata key should be unique for the object.
	 *                           If true, and the object already has a value for the specified metadata key,
	 *                           no change will be made. Default false.
	 * @return int|false The meta ID on success, false on failure.
	 */
	public function add_metadata( $meta_type, $object_id, $meta_key, $meta_value, $unique = false ) {
		if ( ! $meta_type || ! $meta_key || ! is_numeric( $object_id ) ) {
			return false;
		}
		
		$object_id = absint( $object_id );
		if ( ! $object_id ) {
			return false;
		}
		$table = $this->get_meta_table( $meta_type );
		if ( ! $table ) {
			return false;
		}
		
		$column = $meta_type . '_id';
		
		if ( $unique && ! empty( $this->db->get( $table )->result() ) ) {
			return false;
		}
		
		return $this->db->insert( $table, [ $column => $object_id, 'meta_key' => $meta_key, 'meta_value' => maybe_serialize( $meta_value ) ] );
	}
	
	/**
	 * Updates metadata for the specified object. If no value already exists for the specified object
	 * ID and metadata key, the metadata will be added.
	 *
	 * @param string $meta_type  Type of object metadata is for. Accepts 'post', 'comment', 'term', 'user',
	 *                           or any other object type with an associated meta table.
	 * @param int    $object_id  ID of the object metadata is for.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param mixed  $prev_value Optional. Previous value to check before updating.
	 *                           If specified, only update existing metadata entries with
	 *                           this value. Otherwise, update all entries. Default empty.
	 * @return int|bool The new meta field ID if a field with the given key didn't exist
	 *                  and was therefore added, true on successful update,
	 *                  false on failure or if the value passed to the function
	 *                  is the same as the one that is already in the database.
	 */
	public function update_metadata( $meta_type, $object_id, $meta_key, $meta_value, $prev_value = '' ) {
		
		if ( ! $meta_type || ! $meta_key || ! is_numeric( $object_id ) ) {
			return false;
		}
		$object_id = absint( $object_id );
		if ( ! $object_id ) {
			return false;
		}
		$table = $this->get_meta_table( $meta_type );
		if ( ! $table ) {
			return false;
		}
		
		$column    = $meta_type . '_id';
		
		if ( empty( $prev_value ) ) {
			$old_value = $this->get_metadata( $meta_type, $object_id, $meta_key );
			if ( is_countable( $old_value ) && count( $old_value ) === 1 ) {
				if ( $old_value[0] === $meta_value ) {
					return false;
				}
			}
		}
		
		$this->setSimpleCache( $meta_value, $this->getSimpleCacheKey( $meta_type, $object_id, $meta_key ) );
		
		$this->db->select( 'id' )
			->where( [ 'meta_key' => $meta_key, $column => $object_id ] );
		$meta_ids = $this->db->get( $table )->result_array();
		
		if ( empty( $meta_ids ) ) {
			return $this->add_metadata( $meta_type, $object_id, $meta_key, $meta_value );
		} else {
			return $this->db->update(
				$table,
				[ 'meta_value' => maybe_serialize( $meta_value ) ],
				[ $column => $object_id, 'meta_key' => $meta_key ]
			);
		}
	}
	
	/**
	 * Deletes metadata for the specified object.
	 *
	 * @param string $meta_type  Type of object metadata is for. Accepts 'post', 'comment', 'term', 'user',
	 *                           or any other object type with an associated meta table.
	 * @param int    $object_id  ID of the object metadata is for.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Optional. Metadata value. Must be serializable if non-scalar.
	 *                           If specified, only delete metadata entries with this value.
	 *                           Otherwise, delete all entries with the specified meta_key.
	 *                           Pass `null`, `false`, or an empty string to skip this check.
	 *                           (For backward compatibility, it is not possible to pass an empty string
	 *                           to delete those entries with an empty string for a value.)
	 * @param bool   $delete_all Optional. If true, delete matching metadata entries for all objects,
	 *                           ignoring the specified object_id. Otherwise, only delete
	 *                           matching metadata entries for the specified object_id. Default false.
	 * @return bool True on successful delete, false on failure.
	 */
	public function delete_metadata( $meta_type, $object_id, $meta_key, $meta_value = '', $delete_all = false ) {
		if ( ! $meta_type || ! $meta_key || ! is_numeric( $object_id ) && ! $delete_all ) {
			return false;
		}
		$object_id = absint( $object_id );
		if ( ! $object_id && ! $delete_all ) {
			return false;
		}
		$table = $this->get_meta_table( $meta_type );
		if ( ! $table ) {
			return false;
		}
		$type_column = $meta_type . '_id';
		
		$_meta_value = $meta_value;
		$meta_value  = maybe_serialize( $meta_value );
		
		$this->db->select( 'id' );
		$where = [ 'meta_key' => $meta_key ];
		if ( ! $delete_all ) {
			$where[ $type_column ] = $object_id;
		}
		$this->db->where( $where );
		$results = $this->db->get( $table )->result_array();
		if ( empty( $results ) ) {
			return false;
		}
		$meta_ids = array_map( function( $meta ) {
			return $meta->id;
		}, $results );
		
		$object_ids = false;
		if ( $delete_all ) {
			if ( '' !== $meta_value && null !== $meta_value && false !== $meta_value ) {
				$object_ids = $this->db
					->select( $type_column )
                    ->where( [ 'meta_key' => $meta_key, 'meta_value' => $meta_value ] )
                    ->get( $table )->result_array();
			} else {
				$object_ids = $this->db
					->select( $type_column )
					->where( [ 'meta_key' => $meta_key ] )
					->get( $table )->result_array();
			}
		}
		if ( $delete_all && $object_ids ) {
			foreach ( $object_ids as $object_id ) {
				$this->deleteSimpleCache( $this->getSimpleCacheKey( $meta_type, $object_id->{$type_column}, $meta_key ) );
			}
		} else {
			$this->deleteSimpleCache( $this->getSimpleCacheKey( $meta_type, $object_id, $meta_key ) );
		}
		$this->db->where_in('id', $meta_ids );
		$this->db->delete( $table );
		return $this->db->affected_rows() > 0;
	}
	
	protected function get_meta_table( $type ) {
		$types = [
			'product' => 'product_meta',
		];
		return isset( $types[ $type ] ) ? $types[ $type ] : false;
	}
	
	/**
	 * Handle Commission Calculation.
	 *
	 * @param int|Erp_Invoice $invoice Invoice ID or Object.
	 *
	 * @return bool
	 */
	public static function calculate_cash_back_and_commission( $invoice ) {
		
		if ( ! ( $invoice instanceof Erp_Invoice ) ) {
			$invoice = new Erp_Invoice( $invoice );
		}
		
		if ( $invoice->getIsGuest() ) {
			return false;
		}
		
		if ( 'completed' === $invoice->getSaleStatus() && 'paid' === $invoice->getPaymentStatus() ) {
			
			$ref = Erp_Referral::get_by( $invoice->getCustomerId(), 'customer' );
			
			foreach ( $invoice->getItems() as $item ) {
				
				$product = new Erp_Product( $item->getProductId() );
				
				if ( ! $product->getId() ) {
					// invalid item.
					continue;
				}
				
				if ( $product->getCashBack() ) {
					// Product offers cash-back... transfer to customer's wallet.
					$transaction = new Erp_Transaction();
					$transaction->setUserId( $invoice->getUserId() );
					$transaction->setDebit( $item->getQuantity() * $product->getCashBackAmount() );
					$transaction->setDescription(
						sprintf(
							'Product [%s] CashBack %sx%s',
							$product->getId(),
							$item->getQuantity(),
							$product->getCashBackAmount()
						)
					);
					$transaction->setType( 'deposit' );
					$transaction->setApproved();
					$transaction->save();
					
					// [~_^] Product offers cash-back item,
					// don't give any commission (also applicable on referral) to anyone.
					// @XXX We might have an settings, which owner enable or disable the distribution of remaining profit.
					continue;
				}
				
				if ( ! $item->getCategoryId() ) {
					continue;
				}
				
				// Get parent (top) cat for getting com group.
				$parentCat     = self::get_ci_instance()->shop_model->getCatParentId( $item->getCategoryId() );
				$comGroup      = Erp_Commission_Group::getByCategoryId( $parentCat );
				if( ! $comGroup ){
					continue;
				}
				if ( ! $comGroup->getRate() ) {
					continue;
				}
				// Sub total = final sale price for the item.
				// Calculate profit for current sale.
				$profit = ( $item->getSubtotal() - ( $item->getQuantity() * $product->getCost() ) );

				// If item has any discount or promo offers...
				if ( $profit <= 0 ) {
					continue;
				}

				$comGroupUsers = $comGroup->getUsers( true );
				$refCom        = $ref ? $comGroup->getReferralCommission() : false;
				$shopperCom    = $comGroup->getShopperCommission();

				// Commission for current group.
				$commission = ( ( $profit * $comGroup->getRate() ) / 100 );
				
				// Get commission group users, calculate individual commission & distribute it.
				$total_rats = 0;
				$transactions = [];

				foreach( $comGroupUsers as $user ) {
					if ( ! $user->getRate() ) {
						continue;
					}
					
					$total_rats += $user->getRate();
					
					$user_commission = ( ( $commission * $user->getRate() ) / 100 );
					
					$transaction = new Erp_Transaction();
					$transaction->setUserId( $user->getUserId() );
					$transaction->setDebit( $user_commission );
					$transaction->setDescription(
						sprintf(
							'Sales Commission Deposit (Commission Group "%s [%s]"). Profit: %s, Group Commission: %s, Group Ratio: %s%%, User Ratio: %s%%',
							$comGroup->getName(),
							$comGroup->getId(),
							$profit,
							$commission,
							$comGroup->getRate(),
							$user->getRate()
						)
					);
					$transaction->setType( 'deposit' );
					$transaction->setApproved();
					$transactions[] = $transaction;
				}
				
				// Referral commission.
				if ( $refCom && $refCom->getIsEnabled() && $refCom->getRate() ) {
					$total_rats += $refCom->getRate();
					
					$ref_commission = ( ( $commission * $refCom->getRate() ) / 100 );
					
					$transaction = new Erp_Transaction();
					$transaction->setUserId( $ref->getReferralId() );
					$transaction->setDebit( $ref_commission );
					$transaction->setDescription(
						sprintf(
							'Sales Referral Commission Deposit (Commission Group "%s [%s]"). Profit: %s, Group Commission: %s, Group Ratio: %s%%, Referral Ratio: %s%%',
							$comGroup->getName(),
							$comGroup->getId(),
							$profit,
							$commission,
							$comGroup->getRate(),
							$refCom->getRate()
						)
					);
					$transaction->setType( 'deposit' );
					$transaction->setApproved();
					$transactions[] = $transaction;
				}
				
				// Grocerant Shopper's commission.
				if ( $shopperCom && $shopperCom->getIsEnabled() && $shopperCom->getRate() ) {
					$total_rats += $shopperCom->getRate();
					
					$shopper_commission = ( ( $commission * $shopperCom->getRate() ) / 100 );
					
					$transaction = new Erp_Transaction();
					$transaction->setUserId( $invoice->getUserId() );
					$transaction->setDebit( $shopper_commission );
					$transaction->setDescription(
						sprintf(
							'Sales Shopper Commission Deposit (Commission Group "%s [%s]"). Profit: %s, Group Commission: %s, Group Ratio: %s%%, Shopper Ratio: %s%%',
							$comGroup->getName(),
							$comGroup->getId(),
							$profit,
							$commission,
							$comGroup->getRate(),
							$shopperCom->getRate()
						)
					);
					$transaction->setType( 'deposit' );
					$transaction->setApproved();
					$transactions[] = $transaction;
				}
				
				if ( $total_rats > 100 ) {
					log_message( 'error', sprintf( 'Commission Group [%s] – %s :: Total Commission distribution cannot be greater then 100 for', $comGroup->getName(), $comGroup->getId() ) );
					continue;
				}
				
				if ( ! empty( $transactions ) ) {
					// Test..
					/*array_sum(
						array_map( function ( $transaction ) {
							return $transaction->getDebit();
						},
							$transactions )
					);*/
					array_map( function( $transaction ) {
						if ( $transaction instanceof Erp_Transaction ) {
							$transaction->save();
						}
					}, $transactions );
				}
			}
			
			return true;
		}
		
		return false;
	}
}
