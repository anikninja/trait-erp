<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Package_Items extends MY_RetailErp_Model {
	protected $table = 'package_items';

	/**
	 * @var int
	 */
	public $package_id;

	/**
	 * @var int
	 */
	public $sale_id;

	/**
	 * @var int
	 */
	public $sales_item_id;

	/**
	 * @return int
	 */
	public function getPackageId() {
		return $this->package_id;
	}

	/**
	 * @param int $package_id
	 */
	public function setPackageId( int $package_id ) {
		$this->package_id = $package_id;
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
	public function setSaleId( int $sale_id ) {
		$this->sale_id = $sale_id;
	}

	/**
	 * @return int
	 */
	public function getSalesItemId() {
		return $this->sales_item_id;
	}

	/**
	 * @param int $sales_item_id
	 */
	public function setSalesItemId( int $sales_item_id ) {
		$this->sales_item_id = $sales_item_id;
	}
}
// End of file Erp_package_items.php.
