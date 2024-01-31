<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Class Migration_Update316
 *
 * @property CI_DB_mysqli_forge  $dbforge
 * @property CI_DB_mysqli_driver $db
 */
class Migration_Update322 extends CI_Migration {
	
	public function up() {
		
		$cash_back_percentage = [
			'cash_back_percentage' => [
				'type'       => 'DECIMAL',
				'constraint' => '5,2',
				'null'       => true,
				'default'    => 0,
			],
		];
		$this->dbforge->add_column( 'products', $cash_back_percentage, 'cash_back_amount' );
	}
	
	public function down() {
	}
}
