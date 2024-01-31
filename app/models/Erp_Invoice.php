<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Invoice extends MY_RetailErp_Model {
	
	protected $table = 'sales';
	
	/**
	 * @var string
	 */
	protected $date;
	
	/**
	 * @var string
	 */
	protected $reference_no;
	
	/**
	 * @var int
	 */
	protected $customer_id;
	
	/**
	 * @var int
	 */
	protected $user_id;
	
	/**
	 * @var int
	 */
	protected $is_guest;
	
	/**
	 * @var string
	 */
	protected $customer;
	
	/**
	 * @var int
	 */
	protected $biller_id;
	
	/**
	 * @var string
	 */
	protected $biller;
	
	/**
	 * @var int
	 */
	protected $warehouse_id;
	
	/**
	 * @var string
	 */
	protected $note;
	
	/**
	 * @var string
	 */
	protected $staff_note;
	
	/**
	 * @var float
	 */
	protected $total;
	
	/**
	 * @var float
	 */
	protected $product_discount;
	
	/**
	 * @var string
	 */
	protected $order_discount_id;
	
	/**
	 * @var float
	 */
	protected $total_discount;
	
	/**
	 * @var float
	 */
	protected $order_discount;
	
	/**
	 * @var float
	 */
	protected $product_tax;
	/**
	 * @var int
	 */
	protected $order_tax_id;
	
	/**
	 * @var float
	 */
	protected $order_tax;
	
	/**
	 * @var float
	 */
	protected $total_tax;
	
	/**
	 * @var float
	 */
	protected $shipping;
	
	/**
	 * @var Erp_Address
	 */
	protected $shipping_address;
	
	/**
	 * @var float
	 */
	protected $grand_total;
	
	/**
	 * @var string
	 */
	protected $sale_status;
	
	/**
	 * Possible Values
	 *
	 * pending
	 * due
	 * partial
	 * paid
	 * on-hold
	 *
	 * @var string
	 */
	protected $payment_status;
	
	/**
	 * @var int
	 */
	protected $payment_term;
	
	/**
	 * @var string
	 */
	protected $due_date;
	
	/**
	 * @var int
	 */
	protected $created_by;
	
	/**
	 * @var int
	 */
	protected $updated_by;
	
	/**
	 * @var string
	 */
	protected $updated_at;
	
	/**
	 * @var int
	 */
	protected $total_items;
	
	/**
	 * @var int
	 */
	protected $pos;
	
	/**
	 * @var float
	 */
	protected $paid;
	
	/**
	 * @var int
	 */
	protected $return_id;
	
	/**
	 * @var float
	 */
	protected $surcharge;
	
	/**
	 * @var string
	 */
	protected $attachment;
	
	/**
	 * @var string
	 */
	protected $return_sale_ref;
	
	/**
	 * @var int
	 */
	protected $sale_id;
	
	/**
	 * @var float
	 */
	protected $return_sale_total;
	
	/**
	 * @var float
	 */
	protected $rounding;
	
	/**
	 * @var string
	 */
	protected $suspend_note;
	
	/**
	 * @var int
	 */
	protected $api;
	
	/**
	 * @var int
	 */
	protected $shop;
	
	/**
	 * @var int
	 */
	protected $address_id;
	
	/**
	 * @var int
	 */
	protected $reserve_id;
	
	/**
	 * @var string
	 */
	protected $hash;
	
	/**
	 * @var string
	 */
	protected $manual_payment;
	
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
	
	/**
	 * @var Erp_Invoice_Item[]
	 */
	protected $items = [];
	
	/**
	 * @var Erp_Delivery[]
	 */
	protected $deliveries;
	
	/**
	 * @var string
	 */
	protected $payment_method;

	/**
	 * @return string
	 */
	public function getDate() {
		return $this->date;
	}
	
	/**
	 * @param string|DateTime|int $date
	 */
	public function setDate( $date ) {
		
		$this->date = $this->format_date( $date );
	}
	
	/**
	 * @return string
	 */
	public function getReferenceNo() {
		return $this->reference_no;
	}
	
	/**
	 * @param string $reference_no
	 */
	public function setReferenceNo( $reference_no ) {
		$this->reference_no = $reference_no;
	}
	
	/**
	 * @return int
	 */
	public function getCustomerId() {
		return $this->customer_id;
	}
	
	public function getUserId() {
		// back compact.
		if ( ! $this->user_id ) {
			$user = $this->db
				->select( 'id' )
				->where( 'company_id', $this->getCustomerId() )
				->get( 'users' )->row();
			if ( $user ) {
				$this->user_id = $user->id;
			}
		}
		return $this->user_id;
	}
	
	public function getIsGuest() {
		return ! ! $this->is_guest;
	}
	
	public function getCustomerUser() {}
	
	/**
	 * @param int $customer_id
	 */
	public function setCustomerId( $customer_id ) {
		$this->customer_id = (int) $customer_id;
	}
	
	public function setUserId( $user_id ) {
		$this->user_id = $user_id;
	}
	
	public function setIsGuest( $is_guest ) {
		$this->is_guest = $this->absint( $is_guest );
	}
	
	/**
	 * @return string
	 */
	public function getCustomer() {
		return $this->customer;
	}
	
	/**
	 * @param string $customer
	 */
	public function setCustomer( $customer ) {
		$this->customer = $customer;
	}
	
	/**
	 * @return int
	 */
	public function getBillerId() {
		return $this->biller_id;
	}
	
	/**
	 * @param int $biller_id
	 */
	public function setBillerId( $biller_id ) {
		$this->biller_id = (int) $biller_id;
		$this->biller = '';
	}
	
	/**
	 * @return string
	 */
	public function getBiller() {
		return $this->biller;
	}
	
	/**
	 * @return int
	 */
	public function getWarehouseId() {
		return $this->warehouse_id;
	}
	
	/**
	 * @param int $warehouse_id
	 */
	public function setWarehouseId( $warehouse_id ) {
		$this->warehouse_id = (int) $warehouse_id;
	}
	
	/**
	 * @return string
	 */
	public function getNote() {
		return $this->note;
	}
	
	/**
	 * @param string $note
	 */
	public function setNote( $note ) {
		$this->note = $note;
	}
	
	/**
	 * @return string
	 */
	public function getStaffNote() {
		return $this->staff_note;
	}
	
	/**
	 * @param string $staff_note
	 */
	public function setStaffNote( $staff_note ) {
		$this->staff_note = $staff_note;
	}
	
	/**
	 * @return float
	 */
	public function getTotal() {
		return $this->total;
	}
	
	/**
	 * @param float $total
	 */
	public function setTotal( $total ) {
		$this->total = (float) $total;
	}
	
	/**
	 * @return float
	 */
	public function getProductDiscount() {
		return $this->product_discount;
	}
	
	/**
	 * @param float $product_discount
	 */
	public function setProductDiscount( $product_discount ) {
		$this->product_discount = (float) $product_discount;
	}
	
	/**
	 * @return string
	 */
	public function getOrderDiscountId() {
		return $this->order_discount_id;
	}
	
	/**
	 * @param string $order_discount_id
	 */
	public function setOrderDiscountId( $order_discount_id ) {
		$this->order_discount_id = $order_discount_id;
	}
	
	/**
	 * @return float
	 */
	public function getTotalDiscount() {
		return $this->total_discount;
	}
	
	/**
	 * @param float $total_discount
	 */
	public function setTotalDiscount( $total_discount ) {
		$this->total_discount = (float) $total_discount;
	}
	
	/**
	 * @return float
	 */
	public function getOrderDiscount() {
		return $this->order_discount;
	}
	
	/**
	 * @param float $order_discount
	 */
	public function setOrderDiscount( $order_discount ) {
		$this->order_discount = (float) $order_discount;
	}
	
	/**
	 * @return float
	 */
	public function getProductTax() {
		return $this->product_tax;
	}
	
	/**
	 * @param float $product_tax
	 */
	public function setProductTax( $product_tax ) {
		$this->product_tax = (float) $product_tax;
	}
	
	/**
	 * @return int
	 */
	public function getOrderTaxId() {
		return $this->order_tax_id;
	}
	
	/**
	 * @param int $order_tax_id
	 */
	public function setOrderTaxId( $order_tax_id ) {
		$this->order_tax_id = (int) $order_tax_id;
	}
	
	/**
	 * @return float
	 */
	public function getOrderTax() {
		return $this->order_tax;
	}
	
	/**
	 * @param float $order_tax
	 */
	public function setOrderTax( $order_tax ) {
		$this->order_tax = (float) $order_tax;
	}
	
	/**
	 * @return float
	 */
	public function getTotalTax() {
		return $this->total_tax;
	}
	
	/**
	 * @param float $total_tax
	 */
	public function setTotalTax( $total_tax ) {
		$this->total_tax = (float) $total_tax;
	}
	
	/**
	 * @return float
	 */
	public function getShipping() {
		return $this->shipping;
	}
	
	/**
	 * @return Erp_Address
	 */
	public function getShippingAddress() {
		if ( $this->shipping_address ) {
			return $this->shipping_address;
		}
		$this->shipping_address = new Erp_Address( $this->getAddressId() );
		
		return $this->shipping_address;
	}
	
	/**
	 * @param float $shipping
	 */
	public function setShipping( $shipping ) {
		$this->shipping = $shipping;
	}
	
	/**
	 * @return float
	 */
	public function getGrandTotal() {
		return $this->grand_total;
	}
	
	/**
	 * @param float $grand_total
	 */
	public function setGrandTotal( $grand_total ) {
		$this->grand_total = (float) $grand_total;
	}
	
	/**
	 * @return string
	 */
	public function getSaleStatus() {
		return $this->sale_status;
	}
	
	/**
	 * @param string $sale_status
	 */
	public function setSaleStatus( $sale_status ) {
		$this->sale_status = $sale_status;
	}
	
	/**
	 * @return string
	 */
	public function getPaymentStatus() {
		return $this->payment_status;
	}
	
	/**
	 * @param string $payment_status
	 */
	public function setPaymentStatus( $payment_status ) {
		$this->payment_status = $payment_status;
	}
	
	/**
	 * @return int
	 */
	public function getPaymentTerm() {
		return $this->payment_term;
	}
	
	/**
	 * @param int $payment_term
	 */
	public function setPaymentTerm( $payment_term ) {
		$this->payment_term = $payment_term;
	}
	
	/**
	 * @return string
	 */
	public function getDueDate() {
		return $this->due_date;
	}
	
	/**
	 * @param string|DateTime|int $due_date date
	 */
	public function setDueDate( $due_date ) {
		$this->due_date = $this->format_date( $due_date );
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
		$this->created_by = absint( $created_by );
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
		$this->updated_by = absint( $updated_by );
	}
	
	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->updated_at;
	}
	
	/**
	 * @param string|DateTime|int $updated_at timestamp
	 */
	public function setUpdatedAt( $updated_at ) {
		$this->updated_at = $this->format_date( $updated_at );
	}
	
	/**
	 * @return int
	 */
	public function getTotalItems() {
		return $this->total_items;
	}
	
	/**
	 * @param int $total_items
	 */
	public function setTotalItems( $total_items ) {
		$this->total_items = (int) $total_items;
	}
	
	/**
	 * @return Erp_Invoice_Item[]
	 */
	public function getItems() {
		if ( ! $this->getId() ) {
			return [];
		}
		
		if ( $this->items ) {
			return $this->items;
		}
		
		$data = $this->db->select( 'id' )
		                 ->from( 'sale_items' )
		                 ->where( 'sale_id', $this->getId() )
		                 ->get()
		                 ->result_object();
		if ( ! empty( $data ) ) {
			$this->items = array_map( function( $item ) {
				return new Erp_Invoice_Item( $item->id );
			}, $data );
		}
		return $this->items;
	}
	
	/**
	 * @return Erp_Delivery[]
	 */
	public function getDeliveries() {
		if ( ! $this->getId() ) {
			return [];
		}
		
		if ( $this->deliveries ) {
			return $this->deliveries;
		}
		
		$data = $this->db->select( 'id' )
		                 ->from( 'deliveries' )
						 ->where( 'sale_id', $this->getId() )
						 ->get()
						 ->result_object();

		if ( ! empty( $data ) ) {
			$this->deliveries = array_map( function( $item ) {
				return new Erp_Delivery( $item->id );
			}, $data );
		}
		return $this->deliveries;
	}
	
	/**
	 * @return int
	 */
	public function getPos() {
		return $this->pos;
	}
	
	/**
	 * @param int $pos
	 */
	public function setPos( $pos ) {
		$this->pos = (int) $pos;
	}
	
	/**
	 * @return mixed
	 */
	public function getPaid() {
		return $this->paid;
	}
	
	public function getUnPaidBalance() {
		return ( ( $this->getGrandTotal() - $this->getTotalDiscount() ) - $this->getPaid() );
	}
	
	public function isUnPaid() {
		return $this->getUnPaidBalance() > 0;
	}
	
	public function needsPayment() {
		return $this->isUnPaid() && 'paid' !== $this->getPaymentStatus();
	}
	
	/**
	 * @param float $paid
	 */
	public function setPaid( $paid ) {
		$this->paid = (float) $paid;
	}
	
	/**
	 * @return int
	 */
	public function getReturnId() {
		return $this->return_id;
	}
	
	/**
	 * @param int $return_id
	 */
	public function setReturnId( $return_id ) {
		$this->return_id = absint( $return_id );
	}
	
	/**
	 * @return float
	 */
	public function getSurcharge() {
		return $this->surcharge;
	}
	
	/**
	 * @param float $surcharge
	 */
	public function setSurcharge( $surcharge ) {
		$this->surcharge = (float) $surcharge;
	}
	
	/**
	 * @return string
	 */
	public function getAttachment() {
		return $this->attachment;
	}
	
	/**
	 * @param string $attachment
	 */
	public function setAttachment( $attachment ) {
		$this->attachment = $attachment;
	}
	
	/**
	 * @return string
	 */
	public function getReturnSaleRef() {
		return $this->return_sale_ref;
	}
	
	/**
	 * @param string $return_sale_ref
	 */
	public function setReturnSaleRef( $return_sale_ref ) {
		$this->return_sale_ref = $return_sale_ref;
	}
	
	/**
	 * @return int
	 */
	public function getSaleId() {
		return $this->sale_id;
	}
	
	/**
	 * @param int $sale_id
	 */
	public function setSaleId( $sale_id ) {
		$this->sale_id = absint( $sale_id );
	}
	
	/**
	 * @return float
	 */
	public function getReturnSaleTotal() {
		return $this->return_sale_total;
	}
	
	/**
	 * @param float $return_sale_total
	 */
	public function setReturnSaleTotal( $return_sale_total ) {
		$this->return_sale_total = (float) $return_sale_total;
	}
	
	/**
	 * @return float
	 */
	public function getRounding() {
		return $this->rounding;
	}
	
	/**
	 * @param float $rounding
	 */
	public function setRounding( $rounding ) {
		$this->rounding = (float) $rounding;
	}
	
	/**
	 * @return string
	 */
	public function getSuspendNote() {
		return $this->suspend_note;
	}
	
	/**
	 * @param string $suspend_note
	 */
	public function setSuspendNote( $suspend_note ) {
		$this->suspend_note = $suspend_note;
	}
	
	/**
	 * @return int
	 */
	public function getApi() {
		return $this->api;
	}
	
	/**
	 * @param int $api
	 */
	public function setApi( $api ) {
		$this->api = absint( $api );
	}
	
	/**
	 * @return int
	 */
	public function getShop() {
		return $this->shop;
	}
	
	/**
	 * @param int $shop
	 */
	public function setShop( $shop ) {
		$this->shop = absint( $shop );
	}
	
	/**
	 * @return int
	 */
	public function getAddressId() {
		return $this->address_id;
	}
	
	/**
	 * @param int $address_id
	 */
	public function setAddressId( $address_id ) {
		$this->address_id = (int) $address_id;
	}
	
	/**
	 * @return int
	 */
	public function getReserveId() {
		return $this->reserve_id;
	}
	
	/**
	 * @param int $reserve_id
	 */
	public function setReserveId( $reserve_id ) {
		$this->reserve_id = (int) $reserve_id;
	}
	
	/**
	 * @return string
	 */
	public function getHash() {
		return $this->hash;
	}
	
	/**
	 * @param string $hash
	 */
	public function setHash( $hash ) {
		$this->hash = $hash;
	}
	
	/**
	 * Hash to match
	 * @param string $hash
	 *
	 * @return bool
	 */
	public function match_hash( $hash ) {
		return $this->hash === $hash;
	}
	
	/**
	 * @return string
	 */
	public function getManualPayment() {
		return $this->manual_payment;
	}
	
	/**
	 * @param string $manual_payment
	 */
	public function setManualPayment( $manual_payment ) {
		$this->manual_payment = $manual_payment;
	}
	
	/**
	 * @return float
	 */
	public function getCgst() {
		return $this->cgst;
	}
	
	/**
	 * @param float $cgst
	 */
	public function setCgst( $cgst ) {
		$this->cgst = (float) $cgst;
	}
	
	/**
	 * @return float
	 */
	public function getSgst() {
		return $this->sgst;
	}
	
	/**
	 * @param float $sgst
	 */
	public function setSgst( $sgst ) {
		$this->sgst = (float) $sgst;
	}
	
	/**
	 * @return float
	 */
	public function getIgst() {
		return $this->igst;
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
	public function getPaymentMethod() {
		return $this->payment_method;
	}
	
	/**
	 * @param string $payment_method
	 */
	public function setPaymentMethod( $payment_method ) {
		$this->payment_method = $payment_method;
	}
	
	public function to_array() {
		$out = parent::to_array();
		unset( $out['shipping_address'] );
		unset( $out['items'] );
		return $out;
	}
}
// End of file Erp_Address.php.
