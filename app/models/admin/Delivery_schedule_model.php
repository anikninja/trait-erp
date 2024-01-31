<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Delivery_schedule_model extends MY_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function addEvent( $data = [] ) {
		if ( $this->db->insert( 'calendar', $data ) ) {
			return true;
		}
		
		return false;
	}
	
	public function deleteEvent( $id ) {
		if ( $this->db->delete( 'calendar', [ 'id' => $id ] ) ) {
			return true;
		}
		
		return false;
	}
	
	public function getEventByID( $id ) {
		$q = $this->db->get_where( 'calendar', [ 'id' => $id ], 1 );
		if ( $q->num_rows() > 0 ) {
			return $q->row();
		}
		
		return false;
	}
	
	public function getEvents( $start, $end ) {
		// @XXX color can be used with case when then to visualize delivered, pending, expired etc.,
		$this->db->select( 'c.id, CONCAT( "#", ss.reference_no, " [Slot:", s.name, "]" ) as title, c.start, c.end, c.sales_id, c.delivery_id, d.status delivery_status, ss.note as description, "" as color', false );
		$this->db->from( 'delivery_schedules c' )
			->join( 'shipping_area_slots s', 's.id = c.slot_id', 'left' )
			->join( 'deliveries d', 'd.id = c.delivery_id', 'left' )
			->join( 'sales ss', 'ss.id = c.sales_id', 'left' );
		$this->db->where( 'c.start >=', $start )->where( 'c.start <=', $end );
//		$this->db->where( 'sales_id <> ', '' );
		
		$q = $this->db->get();
		return $q->result_array();
//		if ( $q->num_rows() > 0 ) {
//			foreach ( ( $q->result_array() ) as $row ) {
//				$data[] = $row;
//			}
//
//			return $data;
//		}
//
//		return false;
	}
	
	public function updateEvent( $id, $data = [] ) {
		if ( $this->db->update( 'calendar', $data, [ 'id' => $id ] ) ) {
			return true;
		}
		
		return false;
	}
}
