<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Warehouse_model extends My_Model {

	public function __construct() {
		parent::__construct();

		$this->load->admin_model( 'Sales_model' );
		$this->load->admin_model( 'Delivery_model' );
		$this->load->admin_model( 'Delivery_schedule_model' );

	}

	public function addWarehouse( $data ) {
		if ( $this->db->insert( 'warehouses', $data ) ) {
			return TRUE;
		}

		return FALSE;
	}

	public function getAllPriceGroups() {
		$q = $this->db->get( 'price_groups' );
		if ( $q->num_rows() > 0 ) {
			foreach ( ( $q->result() ) as $row ) {
				$data[] = $row;
			}

			return $data;
		}

		return FALSE;
	}

	public function getWarehouseByID( $id ) {
		$q = $this->db->get_where( 'warehouses', [ 'id' => $id ], 1 );
		if ( $q->num_rows() > 0 ) {
			return $q->row();
		}

		return FALSE;
	}

	public function updateWarehouse( $id, $data = [] ) {
		$this->db->where( 'id', $id );
		if ( $this->db->update( 'warehouses', $data ) ) {
			return TRUE;
		}

		return FALSE;
	}

	public function deleteWarehouse( $id ) {

		$delete_warehouse = $this->db->update( 'warehouses', [ 'delete_flag' => 1 ], [ 'id' => $id ] );
		// $delete_warehouse_product_data = $this->db->delete('warehouses_products', ['warehouse_id' => $id]);
		if ( $delete_warehouse ) {
			return TRUE;
		}

		return FALSE;
	}

	public function getShippingZonesList() {
		$this->db->select( '*', FALSE );
		$this->db->where( 'is_enabled', 1 );

		return $this->db->get( 'shipping_zones' )->result();
	}

	public function getShippingAreaList() {

		return $this->db->select( '*', FALSE )
		                ->where( 'is_enabled', 1 )
		                ->get( 'shipping_zone_areas' )
		                ->result();
	}

	public function getShippingAreasByZone( $zone ) {
		return $this->db
			->select( 'id, name' )
			->where( 'is_enabled', 1 )
			->where( 'zone_id', $zone )
			->get( 'shipping_zone_areas' )
			->result_array();
	}

	public function getUnassignedDeliveryAreas() {
		return $this->db
			->select( 'shipping_zone_areas.id, shipping_zone_areas.name as area_name, shipping_zones.name as zone_name' )
			->join('shipping_zones', 'shipping_zones.id = shipping_zone_areas.zone_id', 'left')
			->join('delivery_area', 'delivery_area.area_id = shipping_zone_areas.id', 'left')
			->where('delivery_area.warehouse_id')
			->get( 'shipping_zone_areas' )
			->result_array();
	}

	public function getUnassignedPickupAreas() {
		return $this->db
			->select( 'shipping_zone_areas.id, shipping_zone_areas.name as area_name, shipping_zones.name as zone_name' )
			->join('shipping_zones', 'shipping_zones.id = shipping_zone_areas.zone_id', 'left')
			->join('pickup_area', 'pickup_area.area_id = shipping_zone_areas.id', 'left')
			->where('pickup_area.warehouse_id')
			->get( 'shipping_zone_areas' )
			->result_array();
	}

}
