<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Class Migration_Update316
 *
 * @property CI_DB_mysqli_forge  $dbforge
 * @property CI_DB_mysqli_driver $db
 */
class Migration_Update318 extends CI_Migration {
	
	public function up() {
		// add product cash-back columns
		$cashBack = [
			'cash_back' => [
				'type'       => 'TINYINT',
				'constraint' => '1',
				'null'       => true,
				'default'    => 0,
			],
			'cash_back_amount' => [
				'type'       => 'DECIMAL',
				'constraint' => '25,4',
				'null'       => true,
				'default'    => null,
			],
			'cash_back_start_date' => [
				'type'    => 'DATE',
				'null'    => true,
				'default' => null,
			],
			'cash_back_end_date' => [
				'type'    => 'DATE',
				'null'    => true,
				'default' => null,
			]
		];
		$this->dbforge->add_column( 'products', $cashBack, 'end_date' );
	}
	
	public function down() {
	}
}
