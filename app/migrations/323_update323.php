<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Class Migration_Update316
 *
 * @property CI_DB_mysqli_forge  $dbforge
 * @property CI_DB_mysqli_driver $db
 */
class Migration_Update323 extends CI_Migration {
	
	public function up() {
		
		$menu_order = [
			'menu_order' => [
				'type'       => 'int',
				'constraint' => '11',
				'null'       => false,
				'default'    => 0,
			],
		];
		$this->dbforge->add_column( 'categories', $menu_order, 'featured' );
		$this->dbforge->add_key( 'menu_order' );
	}
	
	public function down() {
	}
}
