<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Products_api
 *
 */
class Products_api extends MY_Model {
    public function __construct()
    {
        parent::__construct();
    }
	
	public function countProducts( $filters = [] ) {
		$this->setupCommonFilters( $filters );
        return $this->db->count_all_results( 'products' );
    }
	
	public function getProducts( $filters = [] ) {
		$filters = ci_parse_args( $filters, [
			'code' => '',
			'id' => '',
			'limit' => 10,
			'start' => 0,
			'order_by' => [ 'id', 'asc' ],
		] );
		$this->setupCommonFilters( $filters );
		if ( $filters['code'] ) {
			$this->db->where( 'products.code', $filters['code'] );
		} elseif ( $filters['id'] ) {
			$this->db->where( 'products.id', $filters['id'] );
		}
		$this->db->order_by( 'products.' . $filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
		$this->db->limit( $filters['limit'], $filters['start'] );
		
		return $this->db->get( 'products' )->result();
	}
	
	public function getProduct( $filters ) {
	    if ( ! empty( $products = $this->getProducts( $filters ) ) ) {
            return array_values($products)[0];
        }
        return false;
    }
    
    public function getPromoProductCount( $filters ) {
    	$this->setupPromoProductQuery( $filters );
	    $this->db->from( 'products' );
	    return $this->db->count_all_results();
    }
    
    public function getPromoProducts( $filters ) {
	    $filters = ci_parse_args( $filters, [
		    'limit' => 10,
		    'start' => 0,
		    'order_by' => [ 'id', 'acs' ],
	    ] );
	    $this->setupPromoProductQuery( $filters );
    	$sort_order = $filters['order_by'][1] ? $filters['order_by'][1] : 'asc';
	    $this->db
		    ->order_by( 'products.' . $filters['order_by'][0], $sort_order )
	        ->limit( $filters['limit'], $filters['start'] );
	    
	    return $this->db->get( 'products' )->result();
    }
	
	public function getTrendingProductCount( $filters ) {
		$this->setupTrendingProductsQuery( $filters );
		$this->db->from( 'products' );
		return $this->db->count_all_results();
	}
    
    public function getTrendingProducts( $filters ) {
	    $filters = ci_parse_args( $filters, [
		    'limit' => 10,
		    'start' => 0,
	    ] );
	    $this->setupTrendingProductsQuery( $filters );
	    $this->db->limit( $filters['limit'], $filters['start'] );
	    
	    return $this->db->get( 'products' )->result();
    }
    
    protected function setupPromoProductQuery( $filters ) {
	    $filters = ci_parse_args( $filters, [
		    'code' => '',
		    'id' => '',
		    'limit' => 10,
		    'start' => 0,
		    'order_by' => [ 'id', 'acs' ],
	    ] );
	
	    $filters['promo'] = ''; // disable common filter's promo.
	
	    $this->setupCommonFilters( $filters );
	    
	    $today  = date( 'Y-m-d' );
	    
	    $this->db->where( 'products.promotion', 1 );
	    
	    if ( false === $filters['date'] ) {
		    $this->db->where( 'products.start_date <=', $today )
		             ->where( 'products.end_date >=', $today );
	    } else {
		    $filters['date'] = strtotime( $filters['date'] );
		    if ( $filters['date'] ) {
			    $filters['date'] = date( 'Y-m-d', $filters['date'] );
		    }
		
		    $this->db->where( 'products.start_date <=', $today )
		             ->where( 'products.end_date =', $filters['date'] );
	    }
    }
    
    protected function setupTrendingProductsQuery( $filters ) {
	    $filters = ci_parse_args( $filters, [
		    'code' => '',
		    'id' => '',
		    'limit' => 10,
		    'start' => 0,
		    'order_by' => [ 'id', 'acs' ],
	    ] );
	
	    $this->setupCommonFilters( $filters );
	
	    $this->db->select( 'CAST( SUM(si.quantity) AS SIGNED ) AS sold', false );
	    $this->db
		    ->join( 'sale_items si', 'products.id=si.product_id', 'left' )
		    ->where( 'si.quantity <> \'\'' )
		    ->group_by( 'si.product_id' )
		    ->order_by( 'promotion DESC, sold DESC, views DESC' );
    }
	
	protected function setupCommonFilters( $filters = [] ) {
	    $filters = ci_parse_args( $filters, [
	    	'category' => '',
	    	'brand' => '',
	    	'include' => [],
	    	'promo' => '',
	    ] );
	
	    $catIds = [];
	    $brand  = false;
	    
	    if ( $filters['category'] ) {
		    $catIds = $this->getCatIdsForQuery( $filters['category'] );
	    }
	    if ( $filters['brand'] ) {
		    $brand = $this->getBrandByID( $filters['brand'] );
		    $this->db->where( 'brand', $brand->id );
	    }
	
	    $this->db->select("{$this->db->dbprefix('products')}.*" );
	
	    if ( ! empty( $filters['include'] ) ) {
		    foreach ( $filters['include'] as $include ) {
			    if ( $include == 'brand' ) {
				    $this->db->select( 'brand' );
			    } elseif ( $include == 'category' ) {
				    $this->db->select( 'category_id as category' );
			    }
		    }
	    }
	
	    if ( ! empty( $filters['promo'] ) ) {
		    $today = date( 'Y-m-d' );
		    $this->db->where( 'products.promotion', 1 )
		             ->where( 'products.start_date <=', $today )
		             ->where( 'products.end_date >=', $today );
	    }
	
	    if ( ! empty( $catIds ) ) {
		    $this->db->join( 'categories', 'categories.id=products.category_id', 'left' );
		    $this->db->where_in( "{$this->db->dbprefix('categories')}.id", $catIds );
	    }
	    if ( $brand ) {
		    $this->db->join( 'brands', 'brands.id=products.brand', 'left' );
		    $this->db->where( "{$this->db->dbprefix('brands')}.id", $brand->id );
	    }
	    
	    if ( ! empty( $filters['query'] ) ) {
		    $this->db->where( "products.name LIKE '%{$filters['query']}%' OR products.code LIKE '%{$filters['query']}%' OR products.product_details LIKE '%{$filters['query']}%'" );
	    }
    }
	
	public function getProductPhotos( $product_id ) {
		$uploads_url = base_url('assets/uploads/');
		$this->db->select("CONCAT('{$uploads_url}', photo) as photo_url");
		return $this->db->get_where('product_photos', ['product_id' => $product_id])->result();
	}
	
	public function getBrandByCode( $code ) {
		return $this->db->get_where('brands', ['code' => $code], 1)->row();
	}
	
	public function getBrandByID( $id ) {
		return $this->db->get_where('brands', ['id' => $id], 1)->row();
	}
	
	public function getCategoryByCode( $code ) {
		return $this->db->get_where('categories', ['code' => $code], 1)->row();
	}
	
	public function getCategoryByID( $id ) {
		return $this->db->get_where('categories', ['id' => $id], 1)->row();
	}
	
	public function getProductUnit( $id ) {
        return $this->db->get_where('units', ['id' => $id], 1)->row();
    }
	
	public function getSubUnits( $base_unit ) {
        return $this->db->get_where('units', ['base_unit' => $base_unit])->result();
    }
	
	public function getTaxRateByID( $id ) {
        return $this->db->get_where('tax_rates', ['id' => $id], 1)->row();
    }
    
    public function get_category( $id, $getChildren = false, $featured = false ) {
    	$cat = $this->db->get_where( 'categories', [ 'id' => $id ] )->row();
    	if ( $cat ) {
		    $cat = $this->prepare_category_data( $cat );
		    if ( $getChildren && $cat->has_children ) {
		    	$cat->subcategories = $this->get_category_tree( $featured, false, $cat->id );
		    }
	    }
    	
    	return $cat;
    }
	
    /**
	 * Get Cat Tree.
	 *
	 * @param bool $featured
	 * @param bool $parentOnly
	 * @param int $parent
	 *
	 * @return array
	 */
	public function get_category_tree( $featured = false, $parentOnly = false, $parent = 0 ) {
		$cacheKey = 'get_';
		if ( $featured ) {
			$cacheKey .= 'featured_';
		}
		if ( $parentOnly ) {
			$cacheKey .= 'parent_';
		}
		if ( $parent ) {
			$cacheKey .= 'pid_' . $parent . '_';
		}
		$cacheKey .= 'category_list_';
		
		$cached = $this->Erp_Options->getOption( $cacheKey, false );
		if ( false === $cached ) {
			$cats = $this->build_category_tree( ! $parentOnly, $featured, $parent );
			$this->Erp_Options->updateOption( $cacheKey, $cats, false );
			return $cats;
		}
		return $cached;
	}
	
	/**
	 * Build Cat Tree
	 * @param bool $getSubCategories Internal uses.
	 * @param bool $featured Internal uses.
	 * @param int $parent Internal uses.
	 *
	 * @return array
	 */
	public function build_category_tree( $getSubCategories = true, $featured = false, $parent = 0 ) {
		$parent = absint( $parent );
		$this->db
			->select( '*', false )
			->from( 'categories c' );
		
		$this->db->where( 'c.parent_id', $parent );
		if ( $featured ) {
			$this->db->where( 'c.featured', 1 );
		}
		$this->db->order_by( 'c.name' );
		
		$cats = $this->db->get()->result();
		
		// check for prod count inc sub cats.
		if ( $cats ) {
			foreach ( $cats as $k => &$cat ) {
				$cat = $this->prepare_category_data( $cat );
				
				if ( $getSubCategories ) {
					if ( $cats[$k]->has_children ) {
						$cats[$k]->subcategories = $this->build_category_tree( $getSubCategories, $featured, $cats[$k]->id );
					} else {
						$cats[$k]->subcategories = [];
					}
				}
				
				if ( (int) $this->shop_settings->hide0 > 0 && ! $cats[$k]->product_count ) {
					unset( $cats[$k] );
				}
			}
		}
		
		return $cats;
	}
	
	protected function prepare_category_data( $category ) {
		$category->featured = (int) $category->featured;
		// image url
		$image = get_image_url( $category->image, false, true );
		$category->image = $image ? $image : '';
		$image = get_image_url( $category->image, true, true );
		$category->thumb = $image ? $image : '';
		
		$subIds = $this->getChildCatIds( $category->id );
		
		if ( ! empty( $subIds ) ) {
			$category->has_children = true;
		} else {
			$category->product_count = 0;
			$category->has_children = false;
		}
		array_unshift( $subIds, $category->id );
		$this->db->select( 'count(*) as total', false );
		$this->db->where_in( 'category_id', $subIds );
		$count = $this->db->get( 'products' )->row_array();
		$category->product_count = (int) $count['total'];
		
		return $category;
	}
	
	public function getCatIdsForQuery( $catId ) {
		$childIds = $this->getChildCatIds( $catId );
		array_unshift( $childIds, $catId );
		return $childIds;
	}
	
	/**
	 * Get Sub Cat Id down to grand grand ...n child.
	 *
	 * @param int   $catId
	 * @param bool   $featured
	 * @param array $subIds
	 *
	 * @return array
	 */
	public function getChildCatIds( $catId, $featured = false, &$subIds = [] ) {
		$this->db
			->select( 'id' )
			->where( 'parent_id', $catId );
		if ( $featured ) {
			$this->db->where( 'featured', '1' ); // <<< ENUM numeric doesn't works.
		}
		$subs = $this->db
			->get( 'categories' )
			->result();
		foreach ( $subs as $k => $sub ) {
			$subIds[] = $sub->id;
			$this->getChildCatIds( $sub->id, $featured, $subIds );
		}
		
		return $subIds;
	}
}
