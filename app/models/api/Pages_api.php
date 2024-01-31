<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Products_api
 *
 */
class Pages_api extends MY_Model {
    public function __construct() {
        parent::__construct();
    }

    public function countPages( $filters = [] ) {
    	// active records where doesn't works with like/or_like query.
	    $this->db->where( 'active > 0' );
	    if ( ! empty( $filters['query'] ) ) {
		    $this->db->where( "(`name` LIKE '%{$filters['query']}%' OR  `title` LIKE '%{$filters['query']}%' OR  `description` LIKE '%{$filters['query']}%' OR  `body` LIKE '%{$filters['query']}%')" );
	    }
	    
	    $this->db->from( 'pages' );
	    
        return $this->db->count_all_results();
    }
    
	public function getPages( $filters = [] ) {
     
		$this->db->where( [ 'active' => 1] );
	    if ( ! empty( $filters['query'] ) ) {
		    $this->db->where( "(`name` LIKE '%{$filters['query']}%' OR  `title` LIKE '%{$filters['query']}%' OR  `description` LIKE '%{$filters['query']}%' OR  `body` LIKE '%{$filters['query']}%')" );
	    }
		
		$this->db->order_by( $filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc' );
		$this->db->limit( $filters['limit'], ( $filters['start'] - 1 ) );
		
		return $this->db->get( 'pages' )->result();
    }
    
    public function getPageBySlug( $slug ) {
	    return $this->db->get_where( 'pages', [ 'slug' => $slug, 'active' => 1 ] )->row();
    }
}
