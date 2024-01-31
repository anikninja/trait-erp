<?php

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

class Update extends MY_Controller {
	public function __construct() {
		parent::__construct();
		
		$this->onlyOwnerAllowed();
		
		$this->load->library( 'migration' );
		$this->load->config( 'migration' );
	}
	
	public function index() {
		if ( ! $this->config->item( 'migration_enabled' ) ) {
			$this->session->set_flashdata( 'error', 'Database Update (Migration) Not Enabled.' );
			admin_redirect( 'welcome' );
		}
		$this->runMigration();
		
	}
	
	/**
	 * Run DB Migrations.
	 *
	 * @return void
	 */
	protected function runMigration() {
		$dbVersion = $this->get_db_version();
		$update    = $this->get_db_last_update_version();
		
		if ( ( $dbVersion && $update ) && $update > $dbVersion ) {
			if ( false === $this->migration->latest() ) {
				$this->session->set_flashdata( 'error', $this->migration->error_string() );
			} else {
				$this->session->set_flashdata( 'message', 'Database Updated Successfully' );
			}
		} else {
			$this->session->set_flashdata( 'info', 'Nothing To Update!' );
		}
		
		admin_redirect( 'welcome' );
	}
	
	protected function get_db_version() {
		$row = $this->db->select( 'version' )->get( $this->config->item( 'migration_table' ) )->row();
		return $row ? $row->version : '0';
	}
	
	protected function get_db_last_update_version() {
		$migrations = $this->migration->find_migrations();
		if ( empty( $migrations ) ) {
			return false;
		}
		
		$migration = basename( end( $migrations ) );
		
		return sscanf( $migration, '%[0-9]+', $number ) ? $number : '0';
	}
	
}
