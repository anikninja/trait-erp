<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Commission_model extends My_Model {
    
    public function getCommissionGroups( $offset = 0, $limit = 20 ) {
        return $this->db->get( 'commission_groups', $limit, $offset )->result_object( 'Erp_Commission_Group' );
    }
    
    public function getCommissionGroupsIdName() {
        
        $this->db->select('commission_groups.id, commission_groups.name');
        return $this->db->get( 'commission_groups')->result_array();
    }
    
    public function getUsersIdName( $group_id = null ) {
    	$where_not_in = [];
        if ( $group_id ) {
	        $where_not_in = $this->db
		        ->select( 'user_id' )
		        ->where( 'group_id', $group_id )
		        ->get( 'commission_users' )
		        ->result_array();
	        if ( $where_not_in ) {
		        $where_not_in = array_column( $where_not_in, 'user_id' );
	        }
        }
        
	    $this->db->select( 'rerp_users.id, rerp_users.username' );
        
        if ( ! empty( $where_not_in ) ) {
        	$this->db->where_not_in( 'id', $where_not_in );
        }
        return $this->db->get( 'rerp_users')->result_array();
    }
}
