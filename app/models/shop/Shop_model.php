<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Shop_model extends MY_Model {
    public function __construct()
    {
        parent::__construct();
    }

    public function addCustomer($data)
    {
        if ($this->db->insert('companies', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function addSale($data, $items, $customer, $address) {
        $cost = $this->site->costing($items);
        // $this->rerp->print_arrays($cost);

        if (is_array($customer) && !empty($customer)) {
            $this->db->insert('companies', $customer);
            $data['customer_id'] = $this->db->insert_id();
        }

        if (is_array($address) && !empty($address)) {
            $address['company_id'] = $data['customer_id'];
            $this->db->insert('addresses', $address);
            $data['address_id'] = $this->db->insert_id();
        }

        $this->db->trans_start();
        if ($this->db->insert('sales', $data)) {
            $sale_id = $this->db->insert_id();
            $this->site->updateReference('so');

            foreach ($items as $item) {
                $item['sale_id'] = $sale_id;
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed') {
                    $item_costs = $this->site->item_costing($item);
                    foreach ($item_costs as $item_cost) {
                        if (isset($item_cost['date']) || isset($item_cost['pi_overselling'])) {
                            $item_cost['sale_item_id'] = $sale_item_id;
                            $item_cost['sale_id']      = $sale_id;
                            $item_cost['date']         = date('Y-m-d', strtotime($data['date']));
                            if (!isset($item_cost['pi_overselling'])) {
                                $this->db->insert('costing', $item_cost);
                            }
                        } else {
                            foreach ($item_cost as $ic) {
                                $ic['sale_item_id'] = $sale_item_id;
                                $ic['sale_id']      = $sale_id;
                                $ic['date']         = date('Y-m-d', strtotime($data['date']));
                                if (!isset($ic['pi_overselling'])) {
                                    $this->db->insert('costing', $ic);
                                }
                            }
                        }
                    }
                }
            }

            // $this->site->syncQuantity($sale_id);
            // $this->rerp->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);
            // return $sale_id;
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Shop_model.php)');
        } else {
            return $sale_id;
        }

        return false;
    }

    public function addWishlist($product_id)
    {
	    $user_id = $this->session->userdata( 'user_id' );
	    if ( ! $this->getWishlistItem( $product_id, $user_id ) ) {
		    return $this->db->insert( 'wishlist', [ 'product_id' => $product_id, 'user_id' => $user_id ] );
        }
        return false;
    }
	
	public function getAddressByID( $id ) {
		$id = absint( $id );
		if ( ! $id ) {
			return false;
		}
        $address = $this->db->get_where('addresses', ['id' => $id], 1)->row();
		return $address ? $address : false;
    }
    
    public function deleteAddress( $address_id ) {
	    return $this->db->delete( 'addresses', [ 'id' => $address_id ] );
    }
	
	public function getUserAddressByID( $address_id, $company_id = null ) {
    	if ( is_null( $company_id ) ) {
		    $company_id = $this->session->userdata('company_id');
	    }
    	
		return $this->db->get_where( 'addresses', [ 'id' => absint( $address_id ), 'company_id' => absint( $company_id ) ], 1 )->row();
    }

    public function getAddresses()
    {
        return $this->db->get_where('addresses', ['company_id' => $this->session->userdata('company_id')])->result();
    }
    
    public function countAddresses() {
	    $this->db->where( [ 'company_id' => $this->session->userdata( 'company_id' ) ] );
        return $this->db->count_all_results('addresses' );
    }

    public function getAllBrands( $count = false, $limit = 0, $offset = 0 ) {
	    if ( $this->shop_settings->hide0 ) {
            $pc = "(SELECT count(*) FROM {$this->db->dbprefix('products')} WHERE {$this->db->dbprefix('products')}.brand = {$this->db->dbprefix('brands')}.id)";
            $this->db->select("{$this->db->dbprefix('brands')}.*, {$pc} AS product_count", false);
		    $this->db->having( 'product_count >', 0 );
        }
	    if ( $count ) {
		    return $this->db->count_all_results( 'brands' );
	    }
	    if ( $limit && $limit > 0 ) {
	    	$this->db->limit( $limit, $offset );
	    }
	    $this->db->order_by( 'name' );
        return $this->db->get('brands')->result();
    }

    public function getAllCategories()
    {
	    if ( $this->shop_settings->hide0 ) {
            $pc = "(
                SELECT
                count(*)
                FROM {$this->db->dbprefix('products')} as Prod
                LEFT JOIN {$this->db->dbprefix('warehouses_products')} AS WH ON Prod.id = WH.product_id
                WHERE
                    WH.warehouse_id = {$this->shop_settings->warehouse}
                    AND
                    Prod.category_id = {$this->db->dbprefix('categories')}.id
            )";
            $this->db->select("{$this->db->dbprefix('categories')}.*, {$pc} AS product_count", false);
		    $this->db->having( 'product_count >', 0 );
        }
        $this->db->group_start()
                 ->where('parent_id', null)
                 ->or_where('parent_id', 0)
                 ->group_end()
                 ->order_by('name');
        
        return $this->db->get('categories')->result();
    }
	
	/**
	 * Build Cat Tree
	 * @param int $parent Internal uses.
	 *
	 * @return array
	 */
	public function build_category_tree( $parent = 0 ) {
		$parent = absint( $parent );
		$this->db
			->select( '*', false )
			->from( 'categories c' );
		
		$this->db->where( 'c.parent_id', $parent );
		$this->db->order_by( 'c.menu_order', 'ASC', FALSE );
		$this->db->order_by( 'c.name', 'ASC', FALSE );
		
		$cats = $this->db->get()->result();
		
		// check for prod count inc sub cats.
		if ( $cats ) {
			foreach ( $cats as $k => &$cat ) {
				$subIds = $this->getChildCatIds( $cats[$k]->id );
				
				if ( ! empty( $subIds ) ) {
					
					$cats[$k]->has_children = true;
				} else {
					$cats[$k]->product_count = 0;
					$cats[$k]->has_children = false;
				}
				array_unshift( $subIds, $cats[$k]->id );
				$this->db->select( 'count(*) as total', false );
				$this->db->where_in( 'category_id', $subIds );
				$count = $this->db->get( 'products' )->row_array();
				$cat->product_count = (int) $count['total'];
				
				if ( $cats[$k]->has_children ) {
					$cats[$k]->subcategories = $this->build_category_tree( $cats[$k]->id );
				} else {
					$cats[$k]->subcategories = [];
				}
				if ( (int) $this->shop_settings->hide0 > 0 && ! $cats[$k]->product_count ) {
					unset( $cats[$k] );
				}
			}
		}
		
		return $cats;
	}
	
	/**
	 * Get Sub Cat Id down to grand grand ...n child.
	 *
	 * @param int   $catId
	 * @param array $subIds
	 *
	 * @return array
	 */
	public function getChildCatIds( $catId, &$subIds = [] ) {
		$subs = $this->db
			->select( 'id' )
			->where( 'parent_id', $catId )
			->order_by( 'menu_order', 'ASC', FALSE )
			->order_by( 'name', 'ASC', FALSE )
			->get( 'categories' )
		    ->result();
		foreach ( $subs as $k => $sub ) {
			$subIds[] = $sub->id;
			$this->getChildCatIds( $sub->id, $subIds );
		}
		
		return $subIds;
	}
	
	public function getCatParentId( $catId, $get_adam = true ) {
		$pops = $this->db
			->select( 'id, parent_id' )
			->where( 'id', $catId )
			->order_by( 'menu_order', 'ASC', FALSE )
			->order_by( 'name', 'ASC', FALSE )
			->get( 'categories' )
			->row();

		if ( ! $pops ) {
			// no pops return current.
			return (int) $catId;
		}
		
		
		if ( ! $pops->parent_id ) {
			// no more pops...
			return (int) $pops->id;
		}
		
		if ( $get_adam ) {
			return $this->getCatParentId( $pops->parent_id, $get_adam );
		}
		
		return (int) $pops->parent_id;
	}
	
	public function getCatIdsForQuery( $catId ) {
		$childIds = $this->getChildCatIds( $catId );
		array_unshift( $childIds, $catId );
		return $childIds;
	}
	
	/**
	 * Get Cat Tree.
	 *
	 * @return array
	 */
	public function get_category_tree() {
		$cached = $this->Erp_Options->getOption( 'category_list', false );
		if ( false === $cached ) {
			$cats = $this->build_category_tree();
			$this->Erp_Options->updateOption( 'category_list', $cats, false );
			
			return $cats;
		}
		
		return $cached;
	}

	public function getFeaturedCategories (){
		$this->db
			->select( '*', false )
			->from( 'categories' )
			->where( 'featured', 1 );

		return $this->db->get()->result_array();
	}

    public function getAllCurrencies()
    {
        return $this->db->get('currencies')->result();
    }

    public function getAllPages()
    {
        $this->db->select('name, slug')
	             ->not_like( 'slug', '_settings' )
                 ->order_by('order_no asc');
        return $this->db->get_where('pages', ['active' => 1])->result();
    }
	
	public function getAllSettingsPages()
	{
		$this->db->select('name, slug')
		         ->like( 'slug', '_settings' )
		         ->order_by('order_no asc');
		return $this->db->get_where('pages', ['active' => 1])->result();
	}

    public function getAllWarehouseWithPQ($product_id, $warehouse_id = null)
    {
        if (!$warehouse_id) {
            $warehouse_id = $this->shop_settings->warehouse;
        }
        $this->db->select('' . $this->db->dbprefix('warehouses') . '.*, ' . $this->db->dbprefix('warehouses_products') . '.quantity, ' . $this->db->dbprefix('warehouses_products') . '.rack')
            ->join('warehouses_products', 'warehouses_products.warehouse_id=warehouses.id', 'left')
            ->where('warehouses_products.product_id', $product_id)
            ->where('warehouses_products.warehouse_id', $warehouse_id)
            ->group_by('warehouses.id');
        return $this->db->get('warehouses')->row();
    }

    public function getBrandBySlug($slug)
    {
        return $this->db->get_where('brands', ['slug' => $slug], 1)->row();
    }

    public function getCategoryBySlug($slug) {
        return $this->db->get_where('categories', ['slug' => $slug], 1)->row();
    }
    public function getCategoryById( $id ) {
        return $this->db->get_where('categories', ['id' => $id], 1)->row();
    }

    public function getCompanyByEmail($email)
    {
        $q = $this->db->get_where('companies', ['email' => $email], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getCompanyByID($id)
    {
        return $this->db->get_where('companies', ['id' => $id])->row();
    }

    public function getCurrencyByCode($code)
    {
        return $this->db->get_where('currencies', ['code' => $code], 1)->row();
    }

    public function getCustomerGroup($id)
    {
        return $this->db->get_where('customer_groups', ['id' => $id])->row();
    }

    public function getDateFormat($id)
    {
        return $this->db->get_where('date_format', ['id' => $id], 1)->row();
    }

    public function getDownloads($limit, $offset, $product_id = null)
    {
        if ($this->loggedIn) {
            $this->db->select("{$this->db->dbprefix('sale_items')}.product_id, {$this->db->dbprefix('sale_items')}.product_code, {$this->db->dbprefix('sale_items')}.product_name, {$this->db->dbprefix('sale_items')}.product_type")
            ->distinct()
            ->join('sale_items', 'sales.id=sale_items.sale_id', 'left')
            ->where('sales.sale_status', 'completed')->where('sales.payment_status', 'paid')
            ->where('sales.customer_id', $this->session->userdata('company_id'))
            ->where('sale_items.product_type', 'digital')
            ->order_by('sales.id', 'desc')->limit($limit, $offset);
            if ($product_id) {
                $this->db->where('sale_items.product_id', $product_id);
            }
            return $this->db->get('sales')->result();
        }
        return false;
    }

    public function getDownloadsCount()
    {
        $this->db->select("{$this->db->dbprefix('sale_items')}.product_id, {$this->db->dbprefix('sale_items')}.product_code, {$this->db->dbprefix('sale_items')}.product_name, {$this->db->dbprefix('sale_items')}.product_type")
        ->distinct()
            ->join('sale_items', 'sales.id=sale_items.sale_id', 'left')
            ->where('sales.sale_status', 'completed')->where('sales.payment_status', 'paid')
            ->where('sales.customer_id', $this->session->userdata('company_id'))
            ->where('sale_items.product_type', 'digital');
        return $this->db->count_all_results('sales');
    }
	
	public function getFeaturedProducts( $limit = 16, $promo = true ) {
    	$this->getCommonProductsQuery( $limit );
		$this->db->where( 'products.featured', 1 );
        
        if ($promo) {
            $this->db->order_by('promotion desc');
        } else {
	        $this->db->order_by('RAND()');
        }
        return $this->db->get()->result();
    }

    public function getPromoProducts( $limit = 16 ) {
    	
    	$this->getCommonProductsQuery( $limit );
        $this->db
        ->where('products.promotion', 1)
        ->where('end_date >', 'CURDATE()', false);

        $this->db->order_by('end_date DESC');

        return $this->db->get()->result();
    }

	public function getProductsByCategory( $category_id, $limit = 16 ) {
		$catIds = $this->getCatIdsForQuery( $category_id );
    	$this->getCommonProductsQuery();
		$this->db
			->where_in('products.category_id', $catIds );
		
		$this->db->order_by('id desc');
		$this->db->limit( $limit );
		
		return $this->db->get()->result();
	}

	public function getProductsByIds( $ids, $limit = 16 ) {
    	if ( empty( $ids ) || ! is_array( $ids ) ) {
    		return [];
	    }
    	$this->getCommonProductsQuery();
		$this->db
			->where_in('products.id', $ids );
		if ( $limit && $limit > 0 ) {
			$this->db->limit( $limit );
		}
		$this->db->order_by('id desc');
		
		return $this->db->get()->result();
	}
	
	public function getNewProducts( $limit = 16 ) {
    	$this->getCommonProductsQuery( $limit );
		$this->db->order_by('id desc');
		return $this->db->get()->result();
	}
	
	/**
	 * DB Helper with common for fetching multiple products.
	 * @param int $limit
	 * @param int $offset=0
	 */
	protected function getCommonProductsQuery( $limit = 16, $offset = 0 ) {
		$this->db
			->select( "
			{$this->db->dbprefix('products')}.*,
			{$this->db->dbprefix('products')}.id as id,
			{$this->db->dbprefix('products')}.name as name,
			{$this->db->dbprefix('products')}.code as code,
			{$this->db->dbprefix('products')}.image as image,
			{$this->db->dbprefix('products')}.slug as slug,
			{$this->db->dbprefix('products')}.price as price,
			{$this->db->dbprefix('products')}.quantity as prod_qty,
			{$this->db->dbprefix('warehouses_products')}.quantity as quantity,
			type,
			promotion,
			promo_price,
			start_date,
			end_date,
			b.name as brand_name, b.slug as brand_slug,
			c.name as category_name, c.slug as category_slug,
			sc.name as subcategory_name, sc.slug as subcategory_slug,
			u.code as unit_code, u.name as unit_name
			" )
			->from( 'products' )
			->join( 'warehouses_products', 'products.id=warehouses_products.product_id', 'left' )
			->join( 'brands b', 'products.brand=b.id', 'left' )
			->join( 'categories c', 'products.category_id=c.id', 'left' )
			->join( 'categories sc', 'products.subcategory_id=sc.id', 'left' )
			->join( 'units u', 'products.unit=u.id', 'left' )
			->where( 'warehouses_products.warehouse_id', $this->shop_settings->warehouse )
			->where( 'hide !=', 1 )
			->limit( $limit );
		
		if ( $limit && $limit > 0 ) {
			$this->db->limit( $limit, $offset );
		}
		
		$sp = $this->getSpecialPrice();
		if ( $sp->cgp ) {
			$this->db->select( 'cgp.price as special_price', false )
			         ->join( $sp->cgp, 'products.id=cgp.product_id', 'left' );
		} elseif ( $sp->wgp ) {
			$this->db->select( 'wgp.price as special_price', false )
			         ->join( $sp->wgp, 'products.id=wgp.product_id', 'left' );
		}
	}

    public function getNotifications()
    {
        $date = date('Y-m-d H:i:s', time());
        $this->db->where('from_date <=', $date)
        ->where('till_date >=', $date)->where('scope !=', 2);
        return $this->db->get('notifications')->result();
    }

    public function getOrder($clause)
    {
        if ($this->loggedIn) {
            $this->db->order_by('id desc');
            $sale = $this->db->get_where('sales', ['id' => $clause['id']], 1)->row();
            return ($sale->customer_id == $this->session->userdata('company_id')) ? $sale : false;
        } elseif (!empty($clause['hash'])) {
            return $this->db->get_where('sales', $clause, 1)->row();
        }
        return false;
    }

    public function getOrderItems($sale_id)
    {
        $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.image, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code,  products.second_name as second_name')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->group_by('sale_items.id')
            ->order_by('id', 'asc');

        return $this->db->get_where('sale_items', ['sale_id' => $sale_id])->result();
    }
	
	public function getOrders( $limit, $offset = 0 ) {
        if ($this->loggedIn) {
            $this->db->select('sales.*, deliveries.status as delivery_status')
            ->join('deliveries', 'deliveries.sale_id=sales.id', 'left')
            ->order_by('id', 'desc')->limit($limit, $offset);
            return $this->db->get_where('sales', ['customer_id' => $this->session->userdata('company_id')])->result();
        }
        return false;
    }

    public function getOrdersCount()
    {
        $this->db->where('customer_id', $this->session->userdata('company_id'));
        return $this->db->count_all_results('sales');
    }

    public function getOtherProducts($id, $category_id, $brand) {
	    $catIds = $this->getCatIdsForQuery( $category_id );
        $this->db->select("{$this->db->dbprefix('products')}.id as id, {$this->db->dbprefix('products')}.name as name, {$this->db->dbprefix('products')}.code as code, {$this->db->dbprefix('products')}.image as image, {$this->db->dbprefix('products')}.slug as slug, {$this->db->dbprefix('products')}.price, quantity, type, promotion, promo_price, start_date, end_date, b.name as brand_name, b.slug as brand_slug, c.name as category_name, c.slug as category_slug")
        ->join('brands b', 'products.brand=b.id', 'left')
        ->join('categories c', 'products.category_id=c.id', 'left')
        ->where_in('category_id', $catIds )->where('brand', $brand)
        ->where('products.id !=', $id)->where('hide !=', 1)
        ->order_by('rand()')->limit(4);

        $sp = $this->getSpecialPrice();
        if ($sp->cgp) {
            $this->db->select('cgp.price as special_price', false)->join($sp->cgp, 'products.id=cgp.product_id', 'left');
        } elseif ($sp->wgp) {
            $this->db->select('wgp.price as special_price', false)->join($sp->wgp, 'products.id=wgp.product_id', 'left');
        }
        return $this->db->get('products')->result();
    }

    public function getPageBySlug($slug) {
	    return $this->db->get_where( 'pages', [ 'slug' => $slug ], 1 )->row();
    }
    
    public function getPageById($id) {
	    return $this->db->get_where( 'pages', [ 'id' => $id ], 1 )->row();
    }

    public function getPaypalSettings()
    {
        return $this->db->get_where('paypal', ['id' => 1])->row();
    }
	
	public function getAuthorizeSettings()
	{
		$this->config->load('payment_gateways', true);
		$payment_config = $this->config->item('payment_gateways');
		return $payment_config['authorize'];
	}

    public function getSslcommerzSettings()
    {
        return $this->db->get_where('sslcommerz', ['id' => 1])->row();
    }

    public function getBankSettings()
    {
	    return (object) ci_parse_args(
		    $this->Erp_Options->getOption( 'bank_payment', [] ),
		    [
			    'active'              => 1,
			    'details'             => '',
			    'fixed_charges'       => 0,
			    'extra_charges_my'    => 0,
			    'extra_charges_other' => 0,
		    ]
	    );
    }

    public function getPriceGroup($id)
    {
        return $this->db->get_where('price_groups', ['id' => $id])->row();
    }
	
	public function getProductByID( $id ) {
		$this->getCommonProductsQuery( 1 );
	    $this->db->where( [ 'products.id' => (int) $id ] );
	    
        return $this->db->get()->row();
    }
	
	public function getProductBySlug( $slug ) {
	    $this->getCommonProductsQuery();
	    $this->db->where( [ $this->db->dbprefix('products') . '.slug' => $slug, 'hide !=' => 1] );
	    return $this->db->get()->row();
    }

    public function getProductComboItems($pid)
    {
        $this->db->select($this->db->dbprefix('products') . '.id as id, ' . $this->db->dbprefix('products') . '.code as code, ' . $this->db->dbprefix('combo_items') . '.quantity as qty, ' . $this->db->dbprefix('products') . '.name as name, ' . $this->db->dbprefix('combo_items') . '.unit_price as price')->join('products', 'products.code=combo_items.item_code', 'left')->group_by('combo_items.id');
        $q = $this->db->get_where('combo_items', ['product_id' => $pid]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return false;
    }
	
	public function getProductForCart( $id ) {
        $this->db->select("
            {$this->db->dbprefix('products')}.*,
	        b.name as brand_name, b.slug as brand_slug, c.name as category_name, c.slug as category_slug, u.code as unit_code,
	        u.name as unit_name
            ")
		        ->from('products p')
		        ->join('brands b', 'products.brand=b.id', 'left')
		        ->join('categories c', 'products.category_id=c.id', 'left')
		        ->join('units u', 'products.unit=u.id', 'left')
                 ->where('products.id', $id);
        $sp = $this->getSpecialPrice();
        if ($sp->cgp) {
            $this->db->select('cgp.price as special_price', false)->join($sp->cgp, 'products.id=cgp.product_id', 'left');
        } elseif ($sp->wgp) {
            $this->db->select('wgp.price as special_price', false)->join($sp->wgp, 'products.id=wgp.product_id', 'left');
        }
        $q = $this->db->get('products', 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getProductOptions($product_id)
    {
    	$this->db->order_by( 'product_variants.id' );
        return $this->db->get_where('product_variants', ['product_id' => $product_id])->result();
    }

    public function getProductOptionsWithWH($product_id, $warehouse_id = null)
    {
	    if ( ! $warehouse_id ) {
            $warehouse_id = $this->shop_settings->warehouse;
        }
	    $this->db->select( $this->db->dbprefix( 'product_variants' ) . '.*, '
	                       . $this->db->dbprefix( 'warehouses' ) . '.name as wh_name, '
	                       . $this->db->dbprefix( 'warehouses' ) . '.id as warehouse_id, '
	                       . $this->db->dbprefix( 'warehouses_products_variants' )
	                       . '.quantity as wh_qty' )
	             ->join( 'warehouses_products_variants',
		             'warehouses_products_variants.option_id=product_variants.id',
		             'left' )
	             ->join( 'warehouses',
		             'warehouses.id=warehouses_products_variants.warehouse_id',
		             'left' )
	             ->group_by( [
		             '' . $this->db->dbprefix( 'product_variants' ) . '.id',
		             '' . $this->db->dbprefix( 'warehouses_products_variants' ) . '.warehouse_id',
	             ] )
	             ->order_by( 'product_variants.id' );
	
	    return $this->db->get_where( 'product_variants',
		    [
			    'product_variants.product_id' => $product_id,
			    'warehouses.id' => $warehouse_id,
			    'warehouses_products_variants.quantity !=' => null,
		    ] )->result();
    }

    public function getProductPhotos($id) {
        $q = $this->db->get_where('product_photos', ['product_id' => $id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
    
    public function getTrendingProducts( $limit = 16 ) {
		$this->getCommonProductsQuery( $limit );
		$this->db->select( 'CAST( SUM(si.quantity) AS SIGNED ) AS sold', false );
		$this->db
			->join( 'sale_items si', 'products.id=si.product_id', 'left' )
			->where( 'si.quantity <> \'\'' )
			->group_by( 'si.product_id' )
			->order_by( 'promotion DESC, sold DESC, views DESC' );
		return $this->db->get()->result();
    }
	
	public function getProducts( $filters = [], $output = 'array' ) {
		$filters = ci_parse_args( $filters, [
			'limit'       => 15,
			'offset'      => 0,
			'trending'    => '',
			'promo'       => '',
			'cashback'    => '',
			'featured'    => '',
			'category'    => '',
			'brand'       => '',
			'min_price'   => '',
			'in_stock'    => '',
			'sorting'     => '',
		] );
		
		$catIds = [];
		if ( ! empty( $filters['category'] ) ) {
			$catIds = $this->getCatIdsForQuery( $filters['category']['id'] );
		}
		
		$this->getCommonProductsQuery();
		
        $this->db
	        ->group_by( 'products.id' );
        
	    $this->db
		    ->where( 'hide !=', 1 )
		    ->limit( $filters['limit'], $filters['offset'] );
	
	    if ( ! empty( $filters ) ) {
	    	if ( ! empty( $filters['trending'] ) ) {
			    $this->db->select( 'CAST( SUM(si.quantity) AS SIGNED ) AS sold', false );
			    $this->db
				    ->join( 'sale_items si', 'products.id=si.product_id', 'left' )
				    ->where( 'si.quantity <> \'\'' )
				    ->group_by( 'si.product_id' )
				    ->order_by( 'promotion DESC, sold DESC, views DESC' );
		    }
	    	
		    if ( ! empty( $filters['promo'] ) ) {
			    $today = date( 'Y-m-d' );
			    $this->db->where( 'promotion', 1 )
				         ->where( 'promo_price >', 0 )
			             ->where( 'start_date <=', $today )
			             ->where( 'end_date >=', $today );
		    }
		    
		    if ( ! empty( $filters['cashback'] ) ) {
			    $today = date( 'Y-m-d' );
			    $this->db->where( 'cash_back', 1 )
			             ->where( 'cash_back_amount >', 0 )
			             ->where( 'cash_back_start_date <=', $today )
			             ->where( 'cash_back_end_date >=', $today );
		    }
		    if ( ! empty( $filters['featured'] ) ) {
			    $this->db->where( 'products.featured', 1 );
		    }
		    if ( ! empty( $filters['query'] ) ) {
			    $this->db->group_start()
			             ->like( 'products.name', $filters['query'], 'both' )
			             ->or_like( 'products.code', $filters['query'], 'both' )
			             ->group_end();
		    }
		    
		    if ( ! empty( $filters['category'] ) ) {
		    	$this->db->where_in( 'category_id', $catIds );
		    }
		    if ( ! empty( $filters['brand'] ) ) {
			    $this->db->where( 'brand', $filters['brand']['id'] );
		    }
		    if ( ! empty( $filters['min_price'] ) ) {
			    $this->db->where( 'products.price >=', $filters['min_price'] );
		    }
		    if ( ! empty( $filters['max_price'] ) ) {
			    $this->db->where( 'products.price <=', $filters['max_price'] );
		    }
		    if ( ! empty( $filters['in_stock'] ) ) {
			    $this->db->group_start()
			             ->where( 'warehouses_products.quantity >=', 1 )
			             ->or_where( 'type !=', 'standard' )
			             ->group_end();
		    }
		    if ( ! empty( $filters['sorting'] ) ) {
			    $sort = explode( '-', $filters['sorting'] );
			    $this->db->order_by( $sort[0], $this->db->escape_str( $sort[1] ) );
		    } else {
			    $this->db->order_by( 'products.name asc' );
		    }
        } else {
		    $this->db->order_by( 'products.name asc' );
        }
	    
	    return $this->db->get()->result( $output );
    }
    
    public function getMinMaxPrices() {
	    $this->db->select( 'MIN(price) AS min, MAX(price) AS max' );
	    return $this->db->get( 'products' )->row();
    }
	
	public function getProductsCount( $filters = [] ) {
		$filters = ci_parse_args( $filters, [
			'limit'       => 15,
			'offset'      => 0,
			'trending'    => '',
			'promo'       => '',
			'cashback'    => '',
			'featured'    => '',
			'category'    => '',
			'brand'       => '',
			'min_price'   => '',
			'in_stock'    => '',
			'sorting'     => '',
		] );
		
	    $catIds = [];
	    if ( ! empty( $filters['category'] ) ) {
		    $catIds = $this->getCatIdsForQuery( $filters['category']['id'] );
	    }
	    
        $this->db->select("{$this->db->dbprefix('products')}.id as id")
        ->join('warehouses_products', 'products.id=warehouses_products.product_id', 'left')
        ->where('warehouses_products.warehouse_id', $this->shop_settings->warehouse)
        ->group_by('products.id');

        $sp = $this->getSpecialPrice();
        if ($sp->cgp) {
            $this->db->select('cgp.price as special_price', false)->join($sp->cgp, 'products.id=cgp.product_id', 'left');
        } elseif ($sp->wgp) {
            $this->db->select('wgp.price as special_price', false)->join($sp->wgp, 'products.id=wgp.product_id', 'left');
        }

        if (!empty($filters)) {
	        if ( ! empty( $filters['promo'] ) ) {
		        $today = date( 'Y-m-d' );
		        $this->db->where( 'promotion', 1 )
		                 ->where( 'promo_price >', 0 )
		                 ->where( 'start_date <=', $today )
		                 ->where( 'end_date >=', $today );
	        }

	        if ( ! empty( $filters['cashback'] ) ) {
		        $today = date( 'Y-m-d' );
		        $this->db->where( 'cash_back', 1 )
		                 ->where( 'cash_back_amount >', 0 )
		                 ->where( 'cash_back_start_date <=', $today )
		                 ->where( 'cash_back_end_date >=', $today );
	        }
            if (!empty($filters['featured'])) {
                $this->db->where('products.featured', 1);
            }
            if (!empty($filters['query'])) {
                $this->db->group_start()->like('name', $filters['query'], 'both')->or_like('code', $filters['query'], 'both')->group_end();
            }
	
	        if ( ! empty( $filters['category'] ) ) {
		        $this->db->where_in( 'category_id', $catIds );
	        }
            if (!empty($filters['brand'])) {
                $this->db->where('brand', $filters['brand']['id']);
            }
	        if (!empty($filters['min_price'])) {
		        $this->db->where('products.price >=', $filters['min_price']);
	        }
	        if (!empty($filters['max_price'])) {
		        $this->db->where('products.price <=', $filters['max_price']);
	        }
            if (!empty($filters['in_stock'])) {
                $this->db->group_start()->where('warehouses_products.quantity >=', 1)->or_where('type !=', 'standard')->group_end();
            }
        }

        $this->db->where('hide !=', 1);
        return $this->db->count_all_results('products');
    }

    public function getProductVariantByID($id)
    {
        return $this->db->get_where('product_variants', ['id' => $id])->row();
    }

    public function getProductVariants($product_id, $warehouse_id = null, $all = null)
    {
        if (!$warehouse_id) {
            $warehouse_id = $this->shop_settings->warehouse;
        }
        $wpv = "( SELECT option_id, warehouse_id, quantity from {$this->db->dbprefix('warehouses_products_variants')} WHERE product_id = {$product_id}) FWPV";
        $this->db->select('product_variants.id as id, product_variants.name as name, product_variants.price as price, product_variants.quantity as total_quantity, FWPV.quantity as quantity', false)
            ->join($wpv, 'FWPV.option_id=product_variants.id', 'left')
            //->join('warehouses', 'warehouses.id=product_variants.warehouse_id', 'left')
            ->where('product_variants.product_id', $product_id)
            ->group_by('product_variants.id');

        if (!$this->Settings->overselling && !$all) {
            $this->db->where('FWPV.warehouse_id', $warehouse_id);
            $this->db->where('FWPV.quantity >', 0);
        }
        return $this->db->get('product_variants')->result_array();
    }

    public function getProductVariantWarehouseQty($option_id, $warehouse_id)
    {
        return $this->db->get_where('warehouses_products_variants', ['option_id' => $option_id, 'warehouse_id' => $warehouse_id], 1)->row();
    }

    public function getQuote($clause)
    {
        if ($this->loggedIn) {
            $this->db->order_by('id desc');
            $sale = $this->db->get_where('quotes', ['id' => $clause['id']], 1)->row();
            return ($sale->customer_id == $this->session->userdata('company_id')) ? $sale : false;
        } elseif (!empty($clause['hash'])) {
            return $this->db->get_where('quotes', $clause, 1)->row();
        }
        return false;
    }

    public function getQuoteItems($quote_id)
    {
        $this->db->select('quote_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.image, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code,  products.second_name as second_name')
            ->join('products', 'products.id=quote_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=quote_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=quote_items.tax_rate_id', 'left')
            ->group_by('quote_items.id')
            ->order_by('id', 'asc');
        return $this->db->get_where('quote_items', ['quote_id' => $quote_id])->result();
    }

    public function getQuotes($limit, $offset)
    {
        if ($this->loggedIn) {
            $this->db->order_by('id', 'desc')->limit($limit, $offset);
            return $this->db->get_where('quotes', ['customer_id' => $this->session->userdata('company_id')])->result();
        }
        return false;
    }

    public function getQuotesCount()
    {
        $this->db->where('customer_id', $this->session->userdata('company_id'));
        return $this->db->count_all_results('quotes');
    }

    public function getSaleByID($id)
    {
        return $this->db->get_where('sales', ['id' => $id])->row();
    }

    public function getSettings() {
        return $this->db->get('settings')->row();
    }

    public function getShopSettings() {
	    return $this->db->get('shop_settings')->row();
    }

    public function getSkrillSettings()
    {
        return $this->db->get_where('skrill', ['id' => 1])->row();
    }

    public function getSpecialPrice()
    {
        $sp      = new stdClass();
        $sp->cgp = ($this->customer && $this->customer->price_group_id) ? "( SELECT {$this->db->dbprefix('product_prices')}.price as price, {$this->db->dbprefix('product_prices')}.product_id as product_id, {$this->db->dbprefix('product_prices')}.price_group_id as price_group_id from {$this->db->dbprefix('product_prices')} WHERE {$this->db->dbprefix('product_prices')}.price_group_id = {$this->customer->price_group_id} ) cgp" : null;

        $sp->wgp = ($this->warehouse && $this->warehouse->price_group_id && !$this->customer) ? "( SELECT {$this->db->dbprefix('product_prices')}.price as price, {$this->db->dbprefix('product_prices')}.product_id as product_id, {$this->db->dbprefix('product_prices')}.price_group_id as price_group_id from {$this->db->dbprefix('product_prices')} WHERE {$this->db->dbprefix('product_prices')}.price_group_id = {$this->warehouse->price_group_id} ) wgp" : null;

        return $sp;
    }
	
	public function getSubCategories( $parent_id ) {
	    if ( $this->shop_settings->hide0 ) {
		    $pc = "(
                SELECT
                count(*)
                FROM {$this->db->dbprefix('products')} as Prod
                LEFT JOIN {$this->db->dbprefix('warehouses_products')} AS WH ON Prod.id = WH.product_id
                WHERE
                    WH.warehouse_id = {$this->shop_settings->warehouse}
                    AND
                    Prod.category_id = {$this->db->dbprefix('categories')}.id
            )";
		    $this->db->select( "{$this->db->dbprefix('categories')}.*, {$pc} AS product_count", false );
		    $this->db->having( 'product_count >', 0 );
	    }
	    $this->db->where( 'parent_id', $parent_id )->order_by( 'name' );
	
	    return $this->db->get( 'categories' )->result();
    }

    public function getUserByEmail($email)
    {
        return $this->db->get_where('users', ['email' => $email], 1)->row();
    }
	
	public function getWishlist( $count = null, $limit = null, $offset = 0 ) {
	    $this->db->where( 'user_id', $this->session->userdata( 'user_id' ) );
	    if ( $count ) {
	    	return $this->db->count_all_results('wishlist');
	    }
	    if ( $limit && $limit > 0 ) {
	    	$this->db->limit( $limit, $offset );
	    }
        return $this->db->get('wishlist')->result();
    }

    public function getWishlistItem($product_id, $user_id)
    {
        return $this->db->get_where('wishlist', ['product_id' => $product_id, 'user_id' => $user_id])->row();
    }

    public function isPromo()
    {
        $today = date('Y-m-d');
	    $this->db
		    ->where( 'promotion', 1 )
		    ->where( 'promo_price >', 0 )
		    ->where( 'start_date <=', $today )
		    ->where( 'end_date >=', $today );
        return $this->db->count_all_results('products');
    }

    public function isCashBack()
    {
        $today = date('Y-m-d');
	    $this->db
		    ->where( 'cash_back', 1 )
		    ->where( 'cash_back_amount >', 0 )
		    ->where( 'cash_back_start_date <=', $today )
		    ->where( 'cash_back_end_date >=', $today );
        return $this->db->count_all_results('products');
    }

    public function removeWishlist($product_id)
    {
        $user_id = $this->session->userdata('user_id');
        return $this->db->delete('wishlist', ['product_id' => $product_id, 'user_id' => $user_id]);
    }

    public function updateCompany($id, $data = [])
    {
        return $this->db->update('companies', $data, ['id' => $id]);
    }

    public function updateProductViews($id, $views = 0 ) {
	    $views = is_numeric( $views ) ? ( (int) $views + 1 ) : 1;
        return $this->db->update('products', ['views' => $views], ['id' => $id]);
    }
	
	public function getShippingZones( $cc, $sc = null, $city = null, $zip = null, $field = '*' ) {
		$this->db->select( $field, false );
		$this->db->where( 'is_enabled', 1 );
		$this->db->where( 'country', $cc );
		if ( $sc ) {
			$this->db->where( 'state', $sc );
		}
		if ( $city ) {
			$this->db->where( 'city', $city );
		}
//		if ( $zip ) {
//			$this->db->where( 'zip', $zip );
//		}
		return $this->db->get( 'shipping_zones' )->result();
	}
	
	public function getShippingCountries( $label = false ) {
		$result = $this->db->select( 'country' )
		                   ->distinct()
		                   ->where( 'is_enabled', 1 )
		                   ->get( 'shipping_zones' )
		                   ->result_array();
		if ( empty( $result ) ) {
			return [];
		}
		
		$result = array_column( $result, 'country' );
		
		if ( ! $label ) {
			return $result;
		}
		
		return array_intersect_key( ci_get_countries(), array_flip( $result ) );
	}
	
	public function getShippingStates( $cc, $label = false ) {
		$result = $this->db->select( 'state' )
		                   ->distinct()
		                   ->where( 'is_enabled', 1 )
		                   ->where( 'country', $cc )
		                   ->get( 'shipping_zones' )
		                   ->result_array();
		if ( empty( $result ) ) {
			return [];
		}
		
		$result = array_column( $result, 'state' );
		
		if ( ! $label ) {
			return $result;
		}
		
		return array_intersect_key( ci_get_states( $cc ), array_flip( $result ) );
	}
	
	public function getShippingCities( $cc, $sc ) {
		$result = $this->db
			->select( 'city' )
			->distinct()
			->where( 'is_enabled', 1 )
			->where( 'country', $cc )
			->where( 'state', $sc )
			->get( 'shipping_zones' )
			->result_array();
		if ( ! empty( $result ) ) {
			return array_column( $result, 'city' );
		}
		
		return $result;
	}
	
	public function getShippingAreasByZone( $zone ) {
		return $this->db
			->select( 'id, name' )
		    ->where( 'is_enabled', 1 )
		    ->where( 'zone_id', $zone )
		    ->get( 'shipping_zone_areas' )
		    ->result_array();
	}
	
	public function getShippingAreas( $cc, $sc, $city, $zip = null ) {
		
		$zones = $this->getShippingZones( $cc, $sc, $city, $zip, 'id' );
		if ( empty( $zones ) ) {
			return [];
		}
		$zones = array_column( $zones, 'id' );
		return $this->db
			->select( 'id, name' )
		    ->where( 'is_enabled', 1 )
		    ->where_in( 'zone_id', $zones )
		    ->get( 'shipping_zone_areas' )
		    ->result();
	}
	
	public function getScheduleCountForDate( $date = null, $slot = null ) {
		if ( is_null( $date ) ) {
			$date = 'current_date()';
		} else {
			$date = "CAST( '{$date}' AS DATE )";
		}
		$this->db->where( 'CAST( start AS DATE ) = ' . $date, '', false );
		if ( $slot ) {
			$this->db->where( 'slot_id', absint( $slot ) );
		}
		
		return $this->db->count_all_results( 'delivery_schedules' );
	}
	
	public function getAvailableSlots( $area = null, $date = null, $count_booked = false ) {
		$schedule    = $this->db->dbprefix( 'delivery_schedules' );
		$select      = '';
		
		/**
		 * Check if slot stops taking order (ends_at time) and global delay delivery (offset with start time of the slot)
		 * these are applicable only if slot query is for the same day as the checkout (current day)
		 *
		 * ---
		 * slot is available if
		 * 1. closing time (close_before) is not exceed (current time is smaller).
		 * 2. max order is not exceed.
		 * If delivery delay is set (shop settings) & is greater then zero.
		 */
		
		$check_close = is_null( $date ) || $date == date( 'Y-m-d' );
		$check_close = $check_close ? 'close_before > CURRENT_TIME AND ' : '';
		$check_delay = absfloat( $this->shop_settings->delivery_slot_offset );
		$check_close .= $check_delay && $check_close ? "DATE_SUB( s.start_at, INTERVAL ( {$check_delay} * 60 * 60 ) SECOND ) > CURRENT_TIME AND" : '';
		
		if ( is_null( $date ) ) {
			$date = 'current_date()';
		} else {
			$date = "CAST( '{$date}' AS DATE )";
		}
		$select .= 's.id, s.name, s.max_order, ';
		$select .= 'TIME_FORMAT( s.start_at, "%h:%i %p" ) as start_at, s.close_before, TIME_FORMAT( s.end_at, "%h:%i %p" ) as end_at, ';
		if ( $count_booked ) {
			$select .= "( select count(c.id) from {$schedule} c WHERE c.slot_id = s.id and CAST( c.start AS DATE ) = {$date} ) as booked, ";
		}
		$select .= '( ';
		$select .= "CASE WHEN {$check_close} ( select count(c.id) from {$schedule} c ";
		$select .= "WHERE c.slot_id = s.id and CAST( c.start AS DATE ) = {$date} ) < s.max_order THEN 1 ELSE 0 END ";
		$select .= ') AS isAvailable';
		
		$this->db
			->select( $select, false )
			->from( 'shipping_area_slots s' );
		$this->db->where( 's.is_enabled', 1 );
		if ( ! is_null( $area ) ) {
			$this->db->where( 's.area_id', absint( $area ) );
		}
		
		return $this->db->get()->result();
	}
	
	public function hasShippingZone() {
		$this->db->limit( 1 );
		return ! ! $this->db->get( 'shipping_zones' )->row();
	}
	
	public function getShippingZoneData() {
//		$this->db
//			->select(
//				"
//				( select count( DISTINCT country ) from {$this->db->dbprefix}shipping_zones where is_enabled = 1 ) as country,
//				( select count( DISTINCT state ) from {$this->db->dbprefix}shipping_zones where is_enabled = 1 ) as state,
//				( select count( DISTINCT city ) from {$this->db->dbprefix}shipping_zones where is_enabled = 1 ) as city",
//				true
//			);
		$count = $this->db
			->select( 'count(DISTINCT id) as zone, count(DISTINCT country) as country,count(DISTINCT state) as state,count(DISTINCT city) as city', true )
			->where( 'is_enabled', 1 )
			->get( 'shipping_zones' )
			->row();
		$this->db->where( 'is_enabled', 1 );
		$this->db->order_by( 'id', 'asc' );
		$zone = $this->db->get( 'shipping_zones', 1 )->row();
		
		return [
			'defaults'  => [
				'zone'    => $zone ? $zone->id : null,
				'country' => $zone ? $zone->country : null,
				'state'   => $zone ? $zone->state : null,
				'city'    => $zone ? $zone->city : null,
				'zip'     => $zone ? $zone->zip : null,
			],
			'data'      => [
				'countries' => true ? $this->getShippingCountries( true ) : [],
				'states'    => $count->state > 1 ? $this->getShippingStates( $zone->country, true ) : [],
				'cities'    => $count->city > 1 ? $this->getShippingCities( $zone->country, $zone->state ) : [],
				'areas'     => $zone ? $this->getShippingAreasByZone( $zone->id ) : [],
			],
			'count'     => $count,
			'hasArea'   => $this->hasShippingArea(),
			'hasMethod' => $this->hasShippingMethod(),
			'hasSlot'   => $this->hasShippingSlot(),
		];
	}
	
	public function hasShippingMethod() {
		$this->db->limit( 1 );
		
		return ! ! $this->db->get( 'shipping_zone_methods' )->row();
	}
	
	public function hasShippingArea() {
		$this->db->limit( 1 );
		
		return ! ! $this->db->get( 'shipping_zone_areas' )->row();
	}
	
	public function hasShippingSlot( $area = null, $date = null ) {
		return ! empty( $this->getAvailableSlots( $area, $date ) );
	}
	
	public function checkSlot( $slot, $area, $date = null ) {
		//SELECT s.id, max_order, (
		//	select count(c.id)
		//	from rerp_delivery_schedules c
		//	where
		//		c.slot_id = s.id and CAST( c.start AS DATE ) = '2020-10-23'
		//	) as total
		//FROM rerp_shipping_area_slots s
		//WHERE s.id = 7;
		
		$schedule    = $this->db->dbprefix( 'delivery_schedules' );
		$select      = '';
		$check_close = is_null( $date ) || $date == date( 'Y-m-d' );
		$check_close = $check_close ? 'close_before > CURRENT_TIME AND ' : '';
		if ( is_null( $date ) ) {
			$date = 'current_date()';
		} else {
			$date = "CAST( '{$date}' AS DATE )";
		}
		$select .= 's.id, s.max_order, ';
		$select .= '( ';
		$select .= "CASE WHEN {$check_close} ( select count(c.id) from {$schedule} c ";
		$select .= "WHERE c.slot_id = s.id and CAST( c.start AS DATE ) = {$date} ) < s.max_order THEN 1 ELSE 0 END ";
		$select .= ') AS isAvailable';
		$this->db->select( $select, false )
			->from( 'shipping_area_slots s' );
		$this->db->where( 's.is_enabled', 1 );
		$this->db->where( 's.id', absint( $slot ) );
		$this->db->where( 's.area_id', absint( $area ) );
		$slot = $this->db->get()->row();
		return ( $slot && $slot->isAvailable > 0 );
	}
}
