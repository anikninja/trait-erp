<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Invoice_Item extends MY_RetailErp_Model {
	
	protected $table = 'sale_items';
	
	/**
	 * @var int
	 */
	protected $sale_id;
	
	/**
	 * @var int
	 */
	protected $product_id;
	
	/**
	 * @var string
	 */
	protected $product_code;
	
	/**
	 * @var string
	 */
	protected $product_name;
	
	/**
	 * @var int
	 */
	protected $product_type;
	
	/**
	 * @var int
	 */
	protected $option_id;
	
	/**
	 * @var int
	 */
	protected $net_unit_price;
	
	/**
	 * @var int
	 */
	protected $unit_price;
	
	/**
	 * @var int
	 */
	protected $quantity;
	
	/**
	 * @var int
	 */
	protected $warehouse_id;
	
	/**
	 * @var float
	 */
	protected $item_tax;
	
	/**
	 * @var int
	 */
	protected $tax_rate_id;
	
	/**
	 * @var string
	 */
	protected $tax;
	
	/**
	 * @var string
	 */
	protected $discount;
	
	/**
	 * @var float
	 */
	protected $item_discount;
	
	/**
	 * @var float
	 */
	protected $subtotal;
	
	/**
	 * @var string
	 */
	protected $serial_no;
	
	/**
	 * @var float
	 */
	protected $real_unit_price;
	
	/**
	 * @var int
	 */
	protected $sale_item_id;
	
	/**
	 * @var int
	 */
	protected $product_unit_id;
	
	/**
	 * @var string
	 */
	protected $product_unit_code;
	
	/**
	 * @var float
	 */
	protected $unit_quantity;
	
	/**
	 * @var string
	 */
	protected $comment;
	
	/**
	 * @var string
	 */
	protected $gst;
	
	/**
	 * @var float
	 */
	protected $cgst;
	
	/**
	 * @var float
	 */
	protected $sgst;
	
	/**
	 * @var float
	 */
	protected $igst;
	
	public $tax_code;
	public $tax_name;
	public $tax_rate;
	public $variant;
	public $hsn_code;
	public $second_name;
	public $details;
	public $image;
	public $thumb;
	public $category_id;
	
	public function __construct( $id = null ) {
		parent::__construct( $id );
		//$this->shop_model->getOrderItems( $id );
		if ( $this->getProductId() ) {
			$this->db->select( 'image, second_name, details, hsn_code, category_id' )
			         ->where( 'id', $this->getProductId() );
			$product = $this->db->get( 'products' )->row();
			if ( $product ) {
				$this->hsn_code    = $product->hsn_code;
				$this->second_name = $product->second_name;
				$this->details     = $product->details;
				$this->image       = $product->image;
				$this->category_id = (int) $product->category_id;
			}
		}
		if ( $this->getTaxRateId() ) {
			$this->db
				->select( 'code, name, rate' )
				->where( 'id', $this->getTaxRateId() );
			$tax = $this->db->get( 'tax_rates' )->row();
			if ( $tax ) {
				$this->tax_code = $tax->code;
				$this->tax_name = $tax->name;
				$this->tax_rate = $tax->rate;
			}
			
		}
		if ( $this->getOptionId() ) {
			$this->db->select( 'name')->where( 'id', $this->getOptionId() );
			$variant = $this->db->get( 'product_variants' )->row();
			if ( $variant ) {
				$this->variant = $variant->name;
			}
		}
	}

	/**
	 * @return int
	 */
	public function getSaleId() {
		return $this->sale_id;
	}
	
	/**
	 * @return int
	 */
	public function getProductId() {
		return $this->product_id;
	}
	
	/**
	 * @return string
	 */
	public function getProductCode() {
		return $this->product_code;
	}
	
	/**
	 * @return string
	 */
	public function getProductName() {
		return $this->product_name;
	}
	
	/**
	 * @return int
	 */
	public function getProductType() {
		return $this->product_type;
	}
	
	/**
	 * @return int
	 */
	public function getOptionId() {
		return $this->option_id;
	}
	
	/**
	 * @return int
	 */
	public function getNetUnitPrice() {
		return $this->net_unit_price;
	}
	
	/**
	 * @return int
	 */
	public function getUnitPrice() {
		return $this->unit_price;
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
	public function getWarehouseId() {
		return $this->warehouse_id;
	}
	
	/**
	 * @return float
	 */
	public function getItemTax() {
		return $this->item_tax;
	}
	
	/**
	 * @return int
	 */
	public function getTaxRateId() {
		return $this->tax_rate_id;
	}
	
	/**
	 * @return string
	 */
	public function getTax() {
		return $this->tax;
	}
	
	/**
	 * @return string
	 */
	public function getDiscount() {
		return $this->discount;
	}
	
	/**
	 * @return float
	 */
	public function getItemDiscount() {
		return $this->item_discount;
	}
	
	/**
	 * @return float
	 */
	public function getSubtotal() {
		return $this->subtotal;
	}
	
	/**
	 * @return string
	 */
	public function getSerialNo() {
		return $this->serial_no;
	}
	
	/**
	 * @return float
	 */
	public function getRealUnitPrice() {
		return $this->real_unit_price;
	}
	
	/**
	 * @return int
	 */
	public function getSaleItemId() {
		return $this->sale_item_id;
	}
	
	/**
	 * @return int
	 */
	public function getProductUnitId() {
		return $this->product_unit_id;
	}
	
	/**
	 * @return string
	 */
	public function getProductUnitCode() {
		return $this->product_unit_code;
	}
	
	/**
	 * @return float
	 */
	public function getUnitQuantity() {
		return $this->unit_quantity;
	}
	
	/**
	 * @return string
	 */
	public function getComment() {
		return $this->comment;
	}
	
	public function getProductImage( $url = true, $thumb = true, $check = true ) {
		if ( ! $url ) {
			return $this->image;
		}
		
		return get_image_url( $this->image, $thumb, $check );
	}
	
	/**
	 * @return string
	 */
	public function getGst() {
		return $this->gst;
	}
	
	/**
	 * @return float
	 */
	public function getCgst() {
		return $this->cgst;
	}
	
	/**
	 * @return float
	 */
	public function getSgst() {
		return $this->sgst;
	}
	
	/**
	 * @return float
	 */
	public function getIgst() {
		return $this->igst;
	}
	
	/**
	 * @param int $sale_id
	 */
	public function setSaleId( $sale_id ) {
		$this->sale_id = (int) $sale_id;
	}
	
	/**
	 * @param int $product_id
	 */
	public function setProductId( $product_id ) {
		$this->product_id = (int) $product_id;
	}
	
	/**
	 * @param string $product_code
	 */
	public function setProductCode( $product_code ) {
		$this->product_code = $product_code;
	}
	
	/**
	 * @param string $product_name
	 */
	public function setProductName( $product_name ) {
		$this->product_name = $product_name;
	}
	
	/**
	 * @param int $product_type
	 */
	public function setProductType( $product_type ) {
		$this->product_type = (int) $product_type;
	}
	
	/**
	 * @param int $option_id
	 */
	public function setOptionId( $option_id ) {
		$this->option_id = (int) $option_id;
	}
	
	/**
	 * @param int $net_unit_price
	 */
	public function setNetUnitPrice( $net_unit_price ) {
		$this->net_unit_price = (int) $net_unit_price;
	}
	
	/**
	 * @param int $unit_price
	 */
	public function setUnitPrice( $unit_price ) {
		$this->unit_price = (int) $unit_price;
	}
	
	/**
	 * @param int $quantity
	 */
	public function setQuantity( $quantity ) {
		$this->quantity = (int) $quantity;
	}
	
	/**
	 * @param int $warehouse_id
	 */
	public function setWarehouseId( $warehouse_id ) {
		$this->warehouse_id = (int) $warehouse_id;
	}
	
	/**
	 * @param float $item_tax
	 */
	public function setItemTax( $item_tax ) {
		$this->item_tax = (float) $item_tax;
	}
	
	/**
	 * @param int $tax_rate_id
	 */
	public function setTaxRateId( $tax_rate_id ) {
		$this->tax_rate_id = (int) $tax_rate_id;
	}
	
	/**
	 * @param string $tax
	 */
	public function setTax( $tax ) {
		$this->tax = $tax;
	}
	
	/**
	 * @param string $discount
	 */
	public function setDiscount( $discount ) {
		$this->discount = $discount;
	}
	
	/**
	 * @param float $item_discount
	 */
	public function setItemDiscount( $item_discount ) {
		$this->item_discount = (float) $item_discount;
	}
	
	/**
	 * @param float $subtotal
	 */
	public function setSubtotal( $subtotal ) {
		$this->subtotal = (float) $subtotal;
	}
	
	/**
	 * @param string $serial_no
	 */
	public function setSerialNo( $serial_no ) {
		$this->serial_no = $serial_no;
	}
	
	/**
	 * @param float $real_unit_price
	 */
	public function setRealUnitPrice( $real_unit_price ) {
		$this->real_unit_price = (float) $real_unit_price;
	}
	
	/**
	 * @param int $sale_item_id
	 */
	public function setSaleItemId( $sale_item_id ) {
		$this->sale_item_id = (int) $sale_item_id;
	}
	
	/**
	 * @param int $product_unit_id
	 */
	public function setProductUnitId( $product_unit_id ) {
		$this->product_unit_id = (int) $product_unit_id;
	}
	
	/**
	 * @param string $product_unit_code
	 */
	public function setProductUnitCode( $product_unit_code ) {
		$this->product_unit_code = $product_unit_code;
	}
	
	/**
	 * @param float $unit_quantity
	 */
	public function setUnitQuantity( $unit_quantity ) {
		$this->unit_quantity = (float) $unit_quantity;
	}
	
	/**
	 * @param string $comment
	 */
	public function setComment( $comment ) {
		$this->comment = (float) $comment;
	}
	
	/**
	 * @param string $gst
	 */
	public function setGst( $gst ) {
		$this->gst = (float) $gst;
	}
	
	/**
	 * @param float $cgst
	 */
	public function setCgst( $cgst ) {
		$this->cgst = (float) $cgst;
	}
	
	/**
	 * @param float $sgst
	 */
	public function setSgst( $sgst ) {
		$this->sgst = (float) $sgst;
	}
	
	/**
	 * @param float $igst
	 */
	public function setIgst( $igst ) {
		$this->igst = (float) $igst;
	}
	
	/**
	 * @return string
	 */
	public function getTaxCode() {
		return $this->tax_code;
	}
	
	/**
	 * @return string
	 */
	public function getTaxName() {
		return $this->tax_name;
	}
	
	/**
	 * @return string
	 */
	public function getTaxRate() {
		return $this->tax_rate;
	}
	
	/**
	 * @return string
	 */
	public function getVariant() {
		return $this->variant;
	}
	
	/**
	 * @return string
	 */
	public function getHsnCode() {
		return $this->hsn_code;
	}
	
	/**
	 * @return string
	 */
	public function getSecondName() {
		return $this->second_name;
	}
	
	/**
	 * @return string
	 */
	public function getDetails() {
		return $this->details;
	}
	
	/**
	 * @return string
	 */
	public function getImage() {
		return $this->image;
	}
	
	/**
	 * @return string
	 */
	public function getThumb() {
		return $this->thumb;
	}
	
	/**
	 * @return int
	 */
	public function getCategoryId() {
		return (int) $this->category_id;
	}
}
// End of file Erp_Invoice_Item.php.
