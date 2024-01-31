<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Product extends MY_RetailErp_Model {
	
	protected $table = 'products';
	
	/**
	 * @var Erp_Product[]
	 */
	protected $relatedProducts;
	protected $_unit;
	protected $_brand;
	/**
	 * @var string[]
	 */
	protected $_images;
	/**
	 * @var Erp_Product_Category
	 */
	protected $category;
	/**
	 * @var Erp_Product_Category
	 */
	protected $subcategory;
	/**
	 * @var
	 */
	protected $_tax_rate;
	/**
	 * @var []
	 */
	protected $options = [];
	/**
	 * @var []
	 */
	protected $variations = [];
	/**
	 * @var float
	 */
	protected $saved;
	/**
	 * @var float
	 */
	protected $sale_price;
	/**
	 * @var float
	 */
	protected $regular_price;
	/**
	 * @var float
	 */
	protected $current_price;
	/**
	 * @var string
	 */
	protected $link = '';
	/**
	 * @var string
	 */
	protected $add_to_cart = '';
	/**
	 * @var bool
	 */
	protected $inCart = false;
	/**
	 * @var string
	 */
	protected $rowId = '';
	/**
	 * @var int
	 */
	protected $cartQty = 0;
	
	public function getCurrentPrice( $format = true ) {
		if ( null === $this->current_price ) {
			$this->current_price = $this->getSalePrice( false ) ? $this->getSalePrice( false ) : $this->getRegularPrice( false );
		}
		if ( ! $format ) {
			return $this->current_price;
		}
		
		return $this->money_format( $this->current_price );
	}
	
	public function getSaved() {
		if ( null === $this->saved ) {
			$this->saved      = round( ( ( $this->price - $this->promo_price ) / $this->price ) * 100 , 2 );
		}
		
		return $this->saved;
	}
	
	public function getSalePrice( $format = true ) {
		if ( null === $this->sale_price ) {
			$this->sale_price = ( $this->promotion != 0 && $this->promo_price != '' ) ? $this->promo_price : '';
		}
		
		if ( ! $format ) {
			return $this->sale_price ? $this->sale_price : '';
		}
		
		return $this->sale_price ? $this->money_format( $this->sale_price ) : '';
	}
	
	public function getRegularPrice( $format = true ) {
		if ( ! $this->regular_price ) {
			$this->regular_price = $this->price;
		}
		$this->regular_price = $this->price;
		if ( ! $format ) {
			return $this->regular_price;
		}
		return $this->money_format( $this->regular_price );
	}
	
	public function getInCart() {
		$this->inCart = false !== $this->cart->getItems( $this->getId() );
		return $this->inCart;
	}
	
	public function getRowId() {
		$cart = $this->cart->getItems( $this->getId() );
		if ( $cart ) {
			$this->rowId = $cart['rowId'];
		}
		return $this->rowId;
	}
	
	public function getCartQty() {
		$cart = $this->cart->getItems( $this->getId() );
		if ( $cart ) {
			$this->cartQty = $cart['qty'];
		}
		return $this->cartQty;
		
	}
	
	/**
	 * @return Erp_Product[]
	 */
	public function getRelatedProducts() {
		if ( ! empty( $this->relatedProducts ) ) {
			return $this->relatedProducts;
		}
		$prods = $this->shop_model->getOtherProducts( $this->getId(), $this->category_id, $this->brand );
		if ( ! empty( $prods ) ) {
			$this->relatedProducts = array_map( function( $prod ) {
				return new Erp_Product( $prod->id );
			}, $prods );
		}
		return $this->relatedProducts;
	}
	
	/**
	 * @param bool $url
	 * @param bool $thumb
	 * @param bool $check
	 *
	 * @return string[]
	 */
	public function getImages( $url = true, $thumb = true, $check = false ) {
		if ( ! $this->_images ) {
			$this->_images = $this->db->get_where('product_photos', ['product_id' => $this->getId() ] )->result_array();
		}
		if ( ! $url ) {
			return $this->_images;
		}
		return array_map( function ( $image ) use( $thumb, $check ) {
			return get_image_url( $image, $thumb, $check );
		}, $this->_images );
	}
	
	/**
	 * @return Erp_Product_Category
	 */
	public function getCategory() {
		if ( $this->category ) {
			return $this->category;
		}
		
		if ( $this->category_id ) {
			$this->category = new Erp_Product_Category( $this->category_id );
		}
		
		return $this->category;
	}
	
	/**
	 * @return Erp_Product_Category
	 */
	public function getSubcategory() {
		if ( $this->subcategory ) {
			return  $this->subcategory;
		}
		
		if ( $this->subcategory_id ) {
			$this->subcategory = new Erp_Product_Category( $this->subcategory_id );
		}
		
		return $this->subcategory;
	}
	
	public function getOptions() {
		
		if ( ! $this->options ) {
			$s = $this->db->dbprefix('product_variants') . '.*, ' .
			     $this->db->dbprefix('warehouses') . '.name as wh_name, ' .
			     $this->db->dbprefix('warehouses') . '.id as warehouse_id, ' .
			     $this->db->dbprefix('warehouses_products_variants') . '.quantity as wh_qty';
			
			$this->db->select( $s )
			         ->join('warehouses_products_variants', 'warehouses_products_variants.option_id=product_variants.id', 'left')
			         ->join('warehouses', 'warehouses.id=warehouses_products_variants.warehouse_id', 'left')
			         ->group_by( [
				         '' . $this->db->dbprefix('product_variants') . '.id',
				         '' . $this->db->dbprefix('warehouses_products_variants') . '.warehouse_id'
			         ] )
			         ->order_by( 'product_variants.id' );
			$this->options = $this->db->get_where('product_variants', ['product_variants.product_id' => $this->getId(), 'warehouses.id' => $this->getWarehouse( false ), 'warehouses_products_variants.quantity !=' => null])->result();
		}
		
		return $this->options;
	}
	
	public function getVariations() {
		if ( ! $this->variations ) {
			$this->variations = $this->db->get_where('product_variants', ['product_id' => $this->getId() ])->result();
		}
		
		return $this->variations;
	}
	
	/**
	 * @var string
	 */
	protected $code;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var int
	 */
	protected $unit;
	/**
	 * @var float
	 */
	protected $cost;
	/**
	 * @var float
	 */
	protected $price;
	/**
	 * @var int
	 */
	protected $alert_quantity;
	/**
	 * @var string
	 */
	protected $image;
	/**
	 * @var int
	 */
	protected $category_id;
	/**
	 * @var int
	 */
	protected $subcategory_id;
	/**
	 * @var string
	 */
	protected $cf1;
	/**
	 * @var string
	 */
	protected $cf2;
	/**
	 * @var string
	 */
	protected $cf3;
	/**
	 * @var string
	 */
	protected $cf4;
	/**
	 * @var string
	 */
	protected $cf5;
	/**
	 * @var string
	 */
	protected $cf6;
	/**
	 * @var int
	 */
	protected $quantity;
	/**
	 * @var int
	 */
	protected $tax_rate;
	/**
	 * @var int
	 */
	protected $track_quantity;
	/**
	 * @var string
	 */
	protected $details;
	/**
	 * @var int
	 */
	protected $warehouse;
	/**
	 * @var string
	 */
	protected $barcode_symbology;
	/**
	 * @var string
	 */
	protected $file;
	/**
	 * @var string
	 */
	protected $product_details;
	/**
	 * @var int
	 */
	protected $tax_method;
	/**
	 * @var string
	 */
	protected $type;
	/**
	 * @var int
	 */
	protected $supplier1;
	/**
	 * @var float
	 */
	protected $supplier1price;
	/**
	 * @var int
	 */
	protected $supplier2;
	/**
	 * @var float
	 */
	protected $supplier2price;
	/**
	 * @var int
	 */
	protected $supplier3;
	/**
	 * @var float
	 */
	protected $supplier3price;
	/**
	 * @var int
	 */
	protected $supplier4;
	/**
	 * @var float
	 */
	protected $supplier4price;
	/**
	 * @var int
	 */
	protected $supplier5;
	/**
	 * @var float
	 */
	protected $supplier5price;
	/**
	 * @var int
	 */
	protected $promotion;
	/**
	 * @var float
	 */
	protected $promo_price;
	/**
	 * @var string
	 */
	protected $start_date;
	/**
	 * @var string
	 */
	protected $end_date;
	/**
	 * @var int
	 */
	protected $cash_back;
	/**
	 * @var float
	 */
	protected $cash_back_amount;
	/**
	 * @var string
	 */
	protected $cash_back_start_date;
	/**
	 * @var string
	 */
	protected $cash_back_end_date;
	/**
	 * @var float
	 */
	protected $cash_back_percentage;
	/**
	 * @var string
	 */
	protected $supplier1_part_no;
	/**
	 * @var string
	 */
	protected $supplier2_part_no;
	/**
	 * @var string
	 */
	protected $supplier3_part_no;
	/**
	 * @var string
	 */
	protected $supplier4_part_no;
	/**
	 * @var string
	 */
	protected $supplier5_part_no;
	/**
	 * @var int
	 */
	protected $sale_unit;
	/**
	 * @var int
	 */
	protected $purchase_unit;
	/**
	 * @var int
	 */
	protected $brand;
	/**
	 * @var string
	 */
	protected $slug;
	/**
	 * @var int
	 */
	protected $featured;
	/**
	 * @var int
	 */
	protected $weight;
	/**
	 * @var int
	 */
	protected $hsn_code;
	/**
	 * @var int
	 */
	protected $views;
	/**
	 * @var string
	 */
	protected $hide;
	/**
	 * @var string
	 */
	protected $second_name;
	/**
	 * @var int
	 */
	protected $hide_pos;
	
	/**
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}
	
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @param bool $object
	 * @return int|object
	 */
	public function getUnit( $object = false ) {
		if ( false === $object ) {
			return $this->unit;
		}
		if ( $this->_unit ) {
			return  $this->_unit;
		}
		
		$this->_unit = $this->db->get_where( 'units', [ 'id' => $this->unit ], 1 )->row();
		
		return  $this->_unit;
	}
	
	/**
	 * @return float
	 */
	public function getCost() {
		return (float) $this->cost;
	}
	
	/**
	 * @return float
	 */
	public function getPrice() {
		return (float) $this->price;
	}
	
	/**
	 * @return int
	 */
	public function getAlertQuantity() {
		return $this->alert_quantity;
	}
	
	/**
	 * @param bool $url
	 * @param bool $thumb
	 * @param bool $check
	 *
	 * @return bool|string
	 */
	public function getImage( $url = true, $thumb = true, $check = false ) {
		if ( $url ) {
			return get_image_url( $this->image, $thumb, $check );
		}
		
		return $this->image;
	}
	
	/**
	 * @return int
	 */
	public function getCategoryId() {
		return $this->category_id;
	}
	
	/**
	 * @return int
	 */
	public function getSubcategoryId() {
		return $this->subcategory_id;
	}
	
	/**
	 * @return string
	 */
	public function getCf1() {
		return $this->cf1;
	}
	
	/**
	 * @return string
	 */
	public function getCf2() {
		return $this->cf2;
	}
	
	/**
	 * @return string
	 */
	public function getCf3() {
		return $this->cf3;
	}
	
	/**
	 * @return string
	 */
	public function getCf4() {
		return $this->cf4;
	}
	
	/**
	 * @return string
	 */
	public function getCf5() {
		return $this->cf5;
	}
	
	/**
	 * @return string
	 */
	public function getCf6() {
		return $this->cf6;
	}
	
	/**
	 * @return int
	 */
	public function getQuantity() {
		return $this->quantity;
	}
	
	/**
	 * @return int
	 */
	public function getTaxRate( $object = false ) {
		if ( ! $object ) {
			return $this->tax_rate;
		}
		
		if ( ! $this->_tax_rate ) {
			$this->_tax_rate = $this->db->get_where('tax_rates', ['id' => $this->tax_rate ], 1)->row();
		}
		
		return $this->_tax_rate;
	}
	
	/**
	 * @return int
	 */
	public function getTrackQuantity() {
		return $this->track_quantity;
	}
	
	/**
	 * @return string
	 */
	public function getDetails() {
		return $this->details;
	}
	
	/**
	 *
	 * @param bool $object
	 * @return int
	 */
	public function getWarehouse( $object = false ) {
		if ( ! $this->warehouse ) {
			$this->warehouse = $this->shop_settings->warehouse;
		}
		if ( ! $object ) {
			return $this->warehouse;
		}
		
		if ( ! $this->_warehouse ) {
			
			$this->db->select('' . $this->db->dbprefix('warehouses') . '.*, ' . $this->db->dbprefix('warehouses_products') . '.quantity, ' . $this->db->dbprefix('warehouses_products') . '.rack')
			         ->join('warehouses_products', 'warehouses_products.warehouse_id=warehouses.id', 'left')
			         ->where('warehouses_products.product_id', $this->getId() )
			         ->where('warehouses_products.warehouse_id', $this->warehouse )
			         ->group_by('warehouses.id');
			$this->_warehouse = $this->db->get('warehouses')->row();
		}
		
		return $this->_warehouse;
	}
	
	/**
	 * @return string
	 */
	public function getBarcodeSymbology() {
		return $this->barcode_symbology;
	}
	
	/**
	 * @return string
	 */
	public function getFile() {
		return $this->file;
	}
	
	/**
	 * @return string
	 */
	public function getProductDetails() {
		return $this->product_details;
	}
	
	/**
	 * @return int
	 */
	public function getTaxMethod() {
		return $this->tax_method;
	}
	
	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * @return int
	 */
	public function getSupplier1() {
		return $this->supplier1;
	}
	
	/**
	 * @return float
	 */
	public function getSupplier1price() {
		return (float) $this->supplier1price;
	}
	
	/**
	 * @return int
	 */
	public function getSupplier2() {
		return $this->supplier2;
	}
	
	/**
	 * @return float
	 */
	public function getSupplier2price() {
		return (float) $this->supplier2price;
	}
	
	/**
	 * @return int
	 */
	public function getSupplier3() {
		return $this->supplier3;
	}
	
	/**
	 * @return float
	 */
	public function getSupplier3price() {
		return (float) $this->supplier3price;
	}
	
	/**
	 * @return int
	 */
	public function getSupplier4() {
		return $this->supplier4;
	}
	
	/**
	 * @return float
	 */
	public function getSupplier4price() {
		return (float) $this->supplier4price;
	}
	
	/**
	 * @return int
	 */
	public function getSupplier5() {
		return $this->supplier5;
	}
	
	/**
	 * @return float
	 */
	public function getSupplier5price() {
		return (float) $this->supplier5price;
	}
	
	/**
	 * @return bool|int
	 */
	public function getPromotion() {
		return (bool) $this->promotion;
	}
	
	/**
	 * @return float
	 */
	public function getPromoPrice() {
		return (float) $this->promo_price;
	}
	
	/**
	 * @return string
	 */
	public function getStartDate() {
		return $this->start_date;
	}
	
	/**
	 * @return string
	 */
	public function getEndDate() {
		return $this->end_date;
	}
	
	/**
	 * @param bool $validate_date
	 * @return bool
	 */
	public function getCashBack( $validate_date = true ) {
//		if ( $this->isCashBackEnded() ) {
//			$this->setCashBack( 0 );
//			$this->setCashBackEndDate( null );
//			$this->setCashBackStartDate( null );
//			$this->setCashBackAmount( null );
//			$this->save();
//			return false;
//		}
//		if ( $this->isCashBackStarted() ) {
//			return false;
//		}
		return $validate_date ? ( $this->isCashBackStarted() && ! $this->isCashBackEnded() && $this->cash_back ) : (bool) $this->cash_back;
	}
	
	/**
	 * @return float
	 */
	public function getCashBackAmount() {
		return (float) $this->cash_back_amount;
	}
	
	/**
	 * @return string
	 */
	public function getCashBackStartDate() {
		return $this->cash_back_start_date;
	}
	
	/**
	 * Is cash back (date) started.
	 * No start date (in db) means already started.
	 * @return bool
	 */
	public function isCashBackStarted() {
		return ( $this->getCashBackStartDate() ? date( 'Y-m-d' ) >= $this->getCashBackStartDate() : true );
	}
	
	/**
	 * @return string
	 */
	public function getCashBackEndDate() {
		return $this->cash_back_end_date;
	}
	
	/**
	 * Is Cash back (date) ended.
	 * No End date (in db) means it never ends.
	 * @return bool
	 */
	public function isCashBackEnded() {
		return ( $this->getCashBackEndDate() ? $this->getCashBackEndDate() <= date( 'Y-m-d' ) : false );
	}
	
	/**
	 * @return string
	 */
	public function getSupplier1PartNo() {
		return $this->supplier1_part_no;
	}
	
	/**
	 * @return string
	 */
	public function getSupplier2PartNo() {
		return $this->supplier2_part_no;
	}
	
	/**
	 * @return string
	 */
	public function getSupplier3PartNo() {
		return $this->supplier3_part_no;
	}
	
	/**
	 * @return string
	 */
	public function getSupplier4PartNo() {
		return $this->supplier4_part_no;
	}
	
	/**
	 * @return string
	 */
	public function getSupplier5PartNo() {
		return $this->supplier5_part_no;
	}
	
	/**
	 * @return int
	 */
	public function getSaleUnit() {
		return $this->sale_unit;
	}
	
	/**
	 * @return int
	 */
	public function getPurchaseUnit() {
		return $this->purchase_unit;
	}
	
	/**
	 * @return int|Erp_Product_Brand
	 */
	public function getBrand( $object = false ) {
		if ( ! $object ) {
			return $this->brand;
		}
		
		if ( ! $this->_brand ) {
			$_brand = new Erp_Product_Brand( $this->brand );
			if ( $_brand->exists() ) {
				$this->_brand = $_brand;
			}
		}
		
		return  $this->_brand;
	}
	
	/**
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}
	
	/**
	 * @return int
	 */
	public function getFeatured() {
		return $this->featured;
	}
	
	/**
	 * @return int
	 */
	public function getWeight() {
		return $this->weight;
	}
	
	/**
	 * @return int
	 */
	public function getHsnCode() {
		return $this->hsn_code;
	}
	
	/**
	 * @return int
	 */
	public function getViews() {
		return $this->views;
	}
	
	/**
	 * @return string
	 */
	public function getHide() {
		return $this->hide;
	}
	
	/**
	 * @return string
	 */
	public function getSecondName() {
		return $this->second_name;
	}
	
	/**
	 * @return int
	 */
	public function getHidePos() {
		return $this->hide_pos;
	}
	
	/**
	 * @param string $code
	 */
	public function setCode( $code ) {
		$this->code = $code;
	}
	
	/**
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}
	
	/**
	 * @param int $unit
	 */
	public function setUnit( $unit ) {
		$this->unit = $unit;
		$this->_unit = null;
	}
	
	/**
	 * @param float $cost
	 */
	public function setCost( $cost ) {
		$this->cost = $cost;
	}
	
	/**
	 * @param float $price
	 */
	public function setPrice( $price ) {
		$this->price = $price;
	}
	
	/**
	 * @param int $alert_quantity
	 */
	public function setAlertQuantity( $alert_quantity ) {
		$this->alert_quantity = $alert_quantity;
	}
	
	/**
	 * @param string $image
	 */
	public function setImage( $image ) {
		$this->image = $image;
	}
	
	/**
	 * @param int $category_id
	 */
	public function setCategoryId( $category_id ) {
		$this->category_id = $category_id;
	}
	
	/**
	 * @param int $subcategory_id
	 */
	public function setSubcategoryId( $subcategory_id ) {
		$this->subcategory_id = $subcategory_id;
	}
	
	/**
	 * @param string $cf1
	 */
	public function setCf1( $cf1 ) {
		$this->cf1 = $cf1;
	}
	
	/**
	 * @param string $cf2
	 */
	public function setCf2( $cf2 ) {
		$this->cf2 = $cf2;
	}
	
	/**
	 * @param string $cf3
	 */
	public function setCf3( $cf3 ) {
		$this->cf3 = $cf3;
	}
	
	/**
	 * @param string $cf4
	 */
	public function setCf4( $cf4 ) {
		$this->cf4 = $cf4;
	}
	
	/**
	 * @param string $cf5
	 */
	public function setCf5( $cf5 ) {
		$this->cf5 = $cf5;
	}
	
	/**
	 * @param string $cf6
	 */
	public function setCf6( $cf6 ) {
		$this->cf6 = $cf6;
	}
	
	/**
	 * @param int $quantity
	 */
	public function setQuantity( $quantity ) {
		$this->quantity = $quantity;
	}
	
	/**
	 * @param int $tax_rate
	 */
	public function setTaxRate( $tax_rate ) {
		$this->tax_rate = $tax_rate;
	}
	
	/**
	 * @param int $track_quantity
	 */
	public function setTrackQuantity( $track_quantity ) {
		$this->track_quantity = $track_quantity;
	}
	
	/**
	 * @param string $details
	 */
	public function setDetails( $details ) {
		$this->details = $details;
	}
	
	/**
	 * @param int $warehouse
	 */
	public function setWarehouse( $warehouse ) {
		$this->warehouse = $warehouse;
	}
	
	/**
	 * @param string $barcode_symbology
	 */
	public function setBarcodeSymbology( $barcode_symbology ) {
		$this->barcode_symbology = $barcode_symbology;
	}
	
	/**
	 * @param string $file
	 */
	public function setFile( $file ) {
		$this->file = $file;
	}
	
	/**
	 * @param string $product_details
	 */
	public function setProductDetails( $product_details ) {
		$this->product_details = $product_details;
	}
	
	/**
	 * @param int $tax_method
	 */
	public function setTaxMethod( $tax_method ) {
		$this->tax_method = $tax_method;
	}
	
	/**
	 * @param string $type
	 */
	public function setType( $type ) {
		$this->type = $type;
	}
	
	/**
	 * @param int $supplier1
	 */
	public function setSupplier1( $supplier1 ) {
		$this->supplier1 = $supplier1;
	}
	
	/**
	 * @param float $supplier1price
	 */
	public function setSupplier1price( $supplier1price ) {
		$this->supplier1price = $supplier1price;
	}
	
	/**
	 * @param int $supplier2
	 */
	public function setSupplier2( $supplier2 ) {
		$this->supplier2 = $supplier2;
	}
	
	/**
	 * @param float $supplier2price
	 */
	public function setSupplier2price( $supplier2price ) {
		$this->supplier2price = $supplier2price;
	}
	
	/**
	 * @param int $supplier3
	 */
	public function setSupplier3( $supplier3 ) {
		$this->supplier3 = $supplier3;
	}
	
	/**
	 * @param float $supplier3price
	 */
	public function setSupplier3price( $supplier3price ) {
		$this->supplier3price = $supplier3price;
	}
	
	/**
	 * @param int $supplier4
	 */
	public function setSupplier4( $supplier4 ) {
		$this->supplier4 = $supplier4;
	}
	
	/**
	 * @param float $supplier4price
	 */
	public function setSupplier4price( $supplier4price ) {
		$this->supplier4price = $supplier4price;
	}
	
	/**
	 * @param int $supplier5
	 */
	public function setSupplier5( $supplier5 ) {
		$this->supplier5 = $supplier5;
	}
	
	/**
	 * @param float $supplier5price
	 */
	public function setSupplier5price( $supplier5price ) {
		$this->supplier5price = $supplier5price;
	}
	
	/**
	 * @param int $promotion
	 */
	public function setPromotion( $promotion ) {
		$this->promotion = $promotion;
	}
	
	/**
	 * @param float $promo_price
	 */
	public function setPromoPrice( $promo_price ) {
		$this->promo_price = $promo_price;
	}
	
	/**
	 * @param string $start_date
	 */
	public function setStartDate( $start_date ) {
		$this->start_date = $start_date;
	}
	
	/**
	 * @param string $end_date
	 */
	public function setEndDate( $end_date ) {
		$this->end_date = $end_date;
	}
	
	/**
	 * @param int $cash_back
	 */
	public function setCashBack( $cash_back ) {
		$this->cash_back = $cash_back ? 1 : 0;
	}
	
	/**
	 * @param float $cash_back_amount
	 */
	public function setCashBackAmount( $cash_back_amount ) {
		$this->cash_back_amount = $cash_back_amount;
	}
	
	/**
	 * @param string $cash_back_start_date
	 */
	public function setCashBackStartDate( $cash_back_start_date ) {
		$this->cash_back_start_date = $cash_back_start_date;
	}
	
	/**
	 * @param string $cash_back_end_date
	 */
	public function setCashBackEndDate( $cash_back_end_date ) {
		$this->cash_back_end_date = $cash_back_end_date;
	}

	/**
	 * @param float $cash_back_percentage
	 */
	public function setCashBackPercentage( $cash_back_percentage ) {
		$this->cash_back_percentage = $cash_back_percentage;
	}
	
	/**
	 * @param string $supplier1_part_no
	 */
	public function setSupplier1PartNo( $supplier1_part_no ) {
		$this->supplier1_part_no = $supplier1_part_no;
	}
	
	/**
	 * @param string $supplier2_part_no
	 */
	public function setSupplier2PartNo( $supplier2_part_no ) {
		$this->supplier2_part_no = $supplier2_part_no;
	}
	
	/**
	 * @param string $supplier3_part_no
	 */
	public function setSupplier3PartNo( $supplier3_part_no ) {
		$this->supplier3_part_no = $supplier3_part_no;
	}
	
	/**
	 * @param string $supplier4_part_no
	 */
	public function setSupplier4PartNo( $supplier4_part_no ) {
		$this->supplier4_part_no = $supplier4_part_no;
	}
	
	/**
	 * @param string $supplier5_part_no
	 */
	public function setSupplier5PartNo( $supplier5_part_no ) {
		$this->supplier5_part_no = $supplier5_part_no;
	}
	
	/**
	 * @param int $sale_unit
	 */
	public function setSaleUnit( $sale_unit ) {
		$this->sale_unit = $sale_unit;
	}
	
	/**
	 * @param int $purchase_unit
	 */
	public function setPurchaseUnit( $purchase_unit ) {
		$this->purchase_unit = $purchase_unit;
	}
	
	/**
	 * @param int $brand
	 */
	public function setBrand( $brand ) {
		$this->brand = $brand;
		$this->_brand;
	}
	
	/**
	 * @param string $slug
	 */
	public function setSlug( $slug ) {
		$this->slug = $slug;
	}
	
	/**
	 * @param int $featured
	 */
	public function setFeatured( $featured ) {
		$this->featured = $featured;
	}
	
	/**
	 * @param int $weight
	 */
	public function setWeight( $weight ) {
		$this->weight = $weight;
	}
	
	/**
	 * @param int $hsn_code
	 */
	public function setHsnCode( $hsn_code ) {
		$this->hsn_code = $hsn_code;
	}
	
	/**
	 * @param int $views
	 */
	public function setViews( $views ) {
		$this->views = $views;
	}
	
	/**
	 * @param string $hide
	 */
	public function setHide( $hide ) {
		$this->hide = $hide;
	}
	
	/**
	 * @param string $second_name
	 */
	public function setSecondName( $second_name ) {
		$this->second_name = $second_name;
	}
	
	/**
	 * @param int $hide_pos
	 */
	public function setHidePos( $hide_pos ) {
		$this->hide_pos = $hide_pos;
	}
}
// End of file Erp_Product.php.
