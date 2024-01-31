<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Erp_Options extends MY_RetailErp_Model {
	protected $table = 'options';
	
	/**
	 * @var object[]
	 */
	protected $options;
	
	/**
	 * Erp_Options constructor.
	 *
	 * @param null $id
	 */
	public function __construct( $id = null ) {
		$options = $this->db
			->select( 'option_name, option_value' )
			->where( 'autoload', 'yes' )
			->get( $this->table )
			->result();
		foreach ( $options as $option ) {
			$this->options[ $option->option_name ] = $option->option_value;
		}
	}
	
	/**
	 * @param string $name
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function getOption( $name, $default = false ) {
		if ( isset( $this->options[ $name ] ) ) {
			return maybe_unserialize( $this->options[ $name ] );
		} else {
			$opt = $this->db
				->select( 'option_name, option_value' )
				->where( 'option_name', $name )
				->get( $this->table )
				->row();
			if ( $opt ) {
				return maybe_unserialize( $opt->option_value );
			}
		}
		return $default;
	}
	
	/**
	 * @param string $name
	 * @param mixed  $value
	 * @param bool   $autoload
	 *
	 * @return bool|int true|false on database insert/update 1 if data isn't changed.
	 */
	public function updateOption( $name, $value, $autoload = true ) {
		$old_value = $this->getOption( $name );
		$_value    = maybe_serialize( $value );
		$autoload  = $this->parseAutoload( $autoload );
		if ( $value === $old_value || $_value === maybe_serialize( $old_value ) ) {
			return 1;
		}
		$this->options[ $name ] = $_value;
		if ( false === $old_value ) {
			return $this->db->insert(
				$this->table,
				[
					'option_name'  => $name,
					'option_value' => $_value,
					'autoload'     => $autoload,
				]
			);
		}
		return $this->db->update(
			$this->table,
			[
				'option_value' => $_value,
				'autoload'     => $autoload,
			],
			[
				'option_name' => $name
			]
		);
	}
	
	/**
	 * @param string $name
	 * @param mixed $value
	 * @return bool
	 */
	public function deleteOption( $name, $value = '' ) {
		$where = [ 'option_name' => $name ];
		
		if ( $value ) {
			$where['option_value'] = maybe_serialize( $value );
		}
		unset( $this->options[ $name ] );
		
		$this->db->delete( $this->table, $where );
		
		return $this->db->affected_rows() > 0;
	}
	
	public function deleteOptionsIn( $names ) {
		$this->db->where_in( 'option_name', (array) $names );
		$this->db->delete( $this->table );
		return $this->db->affected_rows() > 0;
	}
	
	public function deleteOptionsLike( $name ) {
		$this->db->like( 'option_name', $name );
		$this->db->delete( $this->table );
		return $this->db->affected_rows() > 0;
	}
	
	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function deleteOptionById( $id ) {
		$where = [ 'id' => $id ];
		$saved = $this->db->select( 'name' )->where( $where )->get( $this->table )->row();
		if ( ! empty( $saved ) ) {
			unset( $this->options[ $saved->name ] );
			return $this->deleteOption( $saved->name );
		} else {
			$this->db->delete( $this->table, $where );
			return $this->db->affected_rows() > 0;
		}
	}
	
	/**
	 * @param string $name
	 * @param mixed  $value
	 * @param bool   $autoload
	 *
	 * @return bool
	 */
	public function addOption( $name, $value, $autoload = true ) {
		return $this->updateOption( $name, $value, $autoload );
	}
	
	/**
	 * @param bool|string $autoload
	 *
	 * @return string
	 */
	protected function parseAutoload( $autoload = true ) {
		if ( true === $autoload ) {
			$autoload = 'yes';
		}
		if ( false === $autoload ) {
			$autoload = 'no';
		}
		
		return 'no' == $autoload ? 'no' : 'yes'; // default is yes.
	}
	
	/**
	 * @param string $name
	 *
	 * @return void
	 */
	public function removeOption( $name ) {
		unset( $this->options[ $name ] );
		$this->db->delete( $this->table, [ 'option_name' => $name ] );
	}
	
	public function save() {
		return false;
	}
}
// End of file Erp_Options.php.
