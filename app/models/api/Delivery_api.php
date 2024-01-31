<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

/**
 * Class Products_api
 *
 */
class Delivery_api extends MY_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function getDeliveryCount( $filters = [] ) {
		$this->setupCommonFilters( $filters );
		return $this->db->count_all_results( 'deliveries' );
	}
	
	public function getDeliveries( $filters = [], $single = false ) {
		$this->setupCommonFilters( $filters );
		$filters = ci_parse_args( $filters, [
			'id' => '',
			'limit' => 10,
			'start' => 0,
			'order_by' => [ 'id', 'acs' ],
		] );
		
		$this->db->order_by( $filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
		$this->db->limit( $filters['limit'], $filters['start'] );
		
		$res = $this->db->get( 'deliveries' );
		if ( $single || $filters['id'] ) {
			return $res->row();
		}
		return $res->result();
	}
	
	protected function setupCommonFilters( $filters = [] ) {
		$filters = ci_parse_args( $filters, [
			'id'                => '',
			'delivered_by'      => '',
			'status'            => '',
			'do_reference_no'   => '',
			'sale_id'           => '',
			'sale_reference_no' => '',
			'date'              => '',
		] );
		
		if ( $filters['id'] ) {
			$this->db->where( 'id', $filters['id'] );
		}
		if ( $filters['delivered_by'] ) {
			if ( $filters['delivered_by'] < 0 ) {
				$this->db->where( 'delivered_by is null' );
			} else {
				$this->db->where( [ 'delivered_by' => absint( $filters['delivered_by'] ) ] );
			}
		}
		if ( $filters['status'] ) {
			$this->db->where( 'status', $filters['status'] );
			if ( 'pending' === $filters['status'] ) {
				$this->db->or_where( 'status is null' );
			}
		}
		if ( $filters['do_reference_no'] ) {
			$this->db->where( 'do_reference_no', $filters['do_reference_no'] );
		}
		if ( $filters['sale_id'] ) {
			$this->db->where( 'sale_id', $filters['sale_id'] );
		}
		if ( $filters['sale_reference_no'] ) {
			$this->db->where( 'sale_reference_no', $filters['sale_reference_no'] );
		}
		if ( $filters['date'] ) {
			$this->db->where( 'date', $filters['date'] );
		}
	}
	
	public function update_delivery( $id, $data ) {
		return $this->db->update( 'deliveries', $data, [ 'id' => $id ] );
	}
}
