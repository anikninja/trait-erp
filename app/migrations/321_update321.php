<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Class Migration_Update316
 *
 * @property CI_DB_mysqli_forge  $dbforge
 * @property CI_DB_mysqli_driver $db
 */
class Migration_Update321 extends CI_Migration {
	
	public function up() {
		
		$user_data = [
			'delivery_slot_offset' => [
				'type'       => 'DECIMAL',
				'constraint' => '4,2',
				'null'       => true,
				'default'    => 0,
			],
		];
		$this->dbforge->add_column( 'shop_settings', $user_data, 'minimum_order' );
	}
	
	public function down() {
	}
}
