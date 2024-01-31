<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Class Migration_Update316
 *
 * @property CI_DB_mysqli_forge  $dbforge
 * @property CI_DB_mysqli_driver $db
 */
class Migration_Update317 extends CI_Migration {
	
	public function up() {
		// commission_groups table
		$this->dbforge->drop_table( 'commission_groups', true );
		$this->dbforge->add_field( [
			'id'          => [ 'type' => 'INT', 'constraint' => 11, 'auto_increment' => true ],
			'name'        => [ 'type' => 'VARCHAR', 'constraint' => 100 ],
			// category_id should be unique, manage in business logic, so we can add group without category.
			'category_id' => [ 'type' => 'INT', 'constraint' => 11, 'default' => null, 'null' => true ],
			'rate'        => [ 'type' => 'DECIMAL', 'constraint' => '25,4', 'default' => '0.00' ],
			'description' => [
				'type'       => 'VARCHAR',
				'constraint' => 256,
				'null'       => true,
				'default'    => null,
			],
			'created_by'  => [ 'type' => 'INT', 'constraint' => 11 ],
			'created'     => [ 'type' => 'DATETIME' ],
			'modified_by' => [ 'type' => 'INT', 'constraint' => 11 ],
			'modified'    => [ 'type' => 'DATETIME' ],
		] );
		$this->dbforge->add_key( 'id', true );
		$this->dbforge->create_table( 'commission_groups', true, [ 'ENGINE' => 'InnoDB', 'AUTO_INCREMENT' => 1 ] );
		
		// commission_groups table
		$this->dbforge->drop_table( 'commission_users', true );
		$this->dbforge->add_field( [
			'id'          => [ 'type' => 'INT', 'constraint' => 11, 'auto_increment' => true ],
			'group_id'    => [ 'type' => 'INT', 'constraint' => 11 ],
			'user_id'     => [ 'type' => 'INT', 'constraint' => 11 ],
			'rate'        => [ 'type' => 'DECIMAL', 'constraint' => '25,4', 'default' => '0.00' ],
			'description' => [
				'type'       => 'VARCHAR',
				'constraint' => 256,
				'null'       => true,
				'default'    => null,
			],
			'created_by'  => [ 'type' => 'INT', 'constraint' => 11 ],
			'created'     => [ 'type' => 'DATETIME' ],
			'modified_by' => [ 'type' => 'INT', 'constraint' => 11 ],
			'modified'    => [ 'type' => 'DATETIME' ],
		] );
		$this->dbforge->add_key( 'id', true );
		$this->dbforge->add_key( 'group_id' );
		$this->dbforge->add_key( 'user_id' );
		$this->dbforge->create_table( 'commission_users', true, [ 'ENGINE' => 'InnoDB', 'AUTO_INCREMENT' => 1 ] );
		
		// referral table
		$this->dbforge->drop_table( 'referral', true );
		$this->dbforge->add_field( [
			'id'          => [ 'type' => 'INT', 'constraint' => 11, 'auto_increment' => true ],
			'user_id'     => [ 'type' => 'INT', 'constraint' => 11 ],
			'referral_id' => [ 'type' => 'INT', 'constraint' => 11 ],
			'description' => [
				'type'       => 'VARCHAR',
				'constraint' => 256,
				'null'       => true,
				'default'    => null,
			],
			'created_by'  => [ 'type' => 'INT', 'constraint' => 11 ],
			'created'     => [ 'type' => 'DATETIME' ],
			'modified_by' => [ 'type' => 'INT', 'constraint' => 11 ],
			'modified'    => [ 'type' => 'DATETIME' ],
		] );
		$this->dbforge->add_key( 'id', true );
		$this->dbforge->add_key( 'referral_id' );
		$this->dbforge->create_table( 'referral', true, [ 'ENGINE' => 'InnoDB', 'AUTO_INCREMENT' => 1 ] );
		
		// transactions table
		$this->dbforge->drop_table( 'transactions', true );
		$this->dbforge->add_field( [
			'id'               => [ 'type' => 'INT', 'constraint' => 11, 'auto_increment' => true ],
			'user_id'          => [ 'type' => 'INT', 'constraint' => 11 ],
			'reference_no'     => [ 'type' => 'VARCHAR', 'constraint' => 50 ],
			'type'             => [ 'type' => 'VARCHAR', 'constraint' => 50 ],
			'description'      => [ 'type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null ],
			'debit'            => [ 'type' => 'DECIMAL', 'constraint' => '25,4', 'default' => '0.00', ],
			'credit'           => [ 'type' => 'DECIMAL', 'constraint' => '25,4', 'default' => '0.00', ],
			'status'           => [ 'type' => 'VARCHAR', 'constraint' => 50 ],
			'created_by'       => [ 'type' => 'INT', 'constraint' => 11 ],
			'modified_by'      => [ 'type' => 'INT', 'constraint' => 11 ],
			'transaction_date' => [ 'type' => 'DATETIME' ],
			'balance_date'     => [ 'type' => 'DATETIME' ],
		] );
		$this->dbforge->add_key( 'id', true );
		$this->dbforge->add_key( 'user_id' );
		$this->dbforge->add_key( 'status' );
		$this->dbforge->add_key( 'type' );
		$this->dbforge->add_key( 'transaction_type' );
		$this->dbforge->add_key( 'balance_date' );
		$this->dbforge->create_table( 'transactions', true, [ 'ENGINE' => 'InnoDB', 'AUTO_INCREMENT' => 1 ] );
		
		// wallet table
		$this->dbforge->drop_table( 'wallet', true );
		$this->dbforge->add_field( [
			'id'      => [ 'type' => 'INT', 'constraint' => 11, 'auto_increment' => true ],
			'user_id' => [ 'type' => 'INT', 'constraint' => 11 ],
			'amount'  => [ 'type' => 'DECIMAL', 'constraint' => '25,4', 'default' => '0.00' ],
			'created' => [ 'type' => 'DATETIME' ],
			'updated' => [ 'type' => 'DATETIME' ],
		] );
		$this->dbforge->add_key( 'id', true );
		$this->dbforge->add_key( 'approved_by' );
		$this->dbforge->create_table( 'wallet', true, [ 'ENGINE' => 'InnoDB', 'AUTO_INCREMENT' => 1 ] );
		
		// withdrawal table
		$this->dbforge->drop_table( 'wallet_withdraw', true );
		$this->dbforge->add_field( [
			'id'             => [ 'type' => 'INT', 'constraint' => 11, 'auto_increment' => true ],
			'user_id'        => [ 'type' => 'INT', 'constraint' => 11 ],
			'reference_no'   => [
				'type'       => 'VARCHAR',
				'constraint' => 50,
				'null'       => true,
				'default'    => null,
			],
			'type'           => [
				'type'       => 'ENUM',
				'constraint' => [ 'purchase', 'bank', 'check', 'cash', 'other' ],
			],
			'status'         => [
				'type'       => 'ENUM',
				'constraint' => [ 'applied', 'approved', 'reject' ],
				'default'    => 'applied',
			],
			'amount'         => [
				'type'       => 'DECIMAL',
				'constraint' => '25,4',
				'default'    => '0.00',
			],
			'transaction_id' => [ 'type' => 'INT', 'constraint' => 11, 'null' => true ],
			'attachment'     => [ 'type' => 'VARCHAR', 'constraint' => 55, 'null' => true ],
			'description'    => [
				'type'       => 'VARCHAR',
				'constraint' => '255',
				'null'       => true,
				'default'    => null,
			],
			'payment_detail' => [
				'type'       => 'VARCHAR',
				'constraint' => '255',
				'null'       => true,
				'default'    => null,
			],
			'request_by'     => [ 'type' => 'INT', 'constraint' => 11 ],
			'request_date'   => [ 'type' => 'DATETIME' ],
			'approved_by'    => [ 'type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => null ],
			'approved_date'  => [ 'type' => 'DATETIME', 'null' => true, 'default' => null ],
			'modified_by'    => [ 'type' => 'INT', 'constraint' => 11 ],
			'modified_date'  => [ 'type' => 'DATETIME' ],
		] );
		$this->dbforge->add_key( 'id', true );
		$this->dbforge->add_key( 'user_id' );
		$this->dbforge->add_key( 'type' );
		$this->dbforge->add_key( 'status' );
		$this->dbforge->add_key( 'request_by' );
		$this->dbforge->add_key( 'approved_by' );
		$this->dbforge->create_table( 'wallet_withdraw', true, [ 'ENGINE' => 'InnoDB', 'AUTO_INCREMENT' => 1 ] );
		
		// add permission columns.
		$permissions = [
			'wallet-list'              => [
				'type'       => 'TINYINT',
				'constraint' => '1',
				'null'       => true,
				'default'    => 0,
			],
			'wallet-withdrawal_list'   => [
				'type'       => 'TINYINT',
				'constraint' => '1',
				'null'       => true,
				'default'    => 0,
			],
			'wallet-withdrawal_add'    => [
				'type'       => 'TINYINT',
				'constraint' => '1',
				'null'       => true,
				'default'    => 0,
			],
			'wallet-withdrawal_accept' => [
				'type'       => 'TINYINT',
				'constraint' => '1',
				'null'       => true,
				'default'    => 0,
			],
		];
		$this->dbforge->add_column( 'permissions', $permissions );
	}
	
	public function down() {
	}
}
