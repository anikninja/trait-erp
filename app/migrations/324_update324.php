<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Class Migration_Update316
 *
 * @property CI_DB_mysqli_forge  $dbforge
 * @property CI_DB_mysqli_driver $db
 */
class Migration_Update324 extends CI_Migration {
	
	public function up() {
		
		$wallet_percentage_cart = [
			'wallet_percentage_cart' => [
				'type'       => 'DECIMAL',
				'constraint' => '5,2',
				'null'       => true,
				'default'    => 0,
			],
		];
		$this->dbforge->add_column( 'shop_settings', $wallet_percentage_cart, 'delivery_slot_offset' );
	}
	
	public function down() {
	}
}
