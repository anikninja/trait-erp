<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Class Migration_Update316
 *
 * @property CI_DB_mysqli_forge  $dbforge
 * @property CI_DB_mysqli_driver $db
 */
class Migration_Update320 extends CI_Migration {
	
	public function up() {
		
		$user_data = [
			'is_enabled' => [
				'type'       => 'TINYINT',
				'constraint' => '1',
				'null'       => true,
				'default'    => 1,
			],
		];
		$this->dbforge->add_column( 'commission_groups', $user_data, 'description' );
		
		$user_data = [
			'is_enabled' => [
				'type'       => 'TINYINT',
				'constraint' => '1',
				'null'       => true,
				'default'    => 1,
			],
		];
		$this->dbforge->add_column( 'commission_users', $user_data, 'description' );
	}
	
	public function down() {
	}
}
