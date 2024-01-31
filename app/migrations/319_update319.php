<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Class Migration_Update316
 *
 * @property CI_DB_mysqli_forge  $dbforge
 * @property CI_DB_mysqli_driver $db
 */
class Migration_Update319 extends CI_Migration {
	
	public function up() {
		// add product cash-back columns
		$user_data = [
			'user_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'null'       => true,
				'default'    => null,
			],
			'is_guest' => [
				'type'       => 'TINYINT',
				'constraint' => '1',
				'null'       => true,
				'default'    => 0,
			],
		];
		$this->dbforge->add_column( 'sales', $user_data, 'customer_id' );
	}
	
	public function down() {
	}
}
