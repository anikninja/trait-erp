<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class MY_RetailErp_Model
 *
 * @property object $Settings
 * @property Erp_Options $Erp_Options
 */
class MY_RetailErp_Model extends MY_Model {
	
	/**
	 * @var string
	 */
	protected $table;
	
	/**
	 * @var int
	 */
	protected $id;
	
	/**
	 * Exclusion list for toArray method.
	 *
	 * @var array
	 */
	protected $toArrayExcludes = [
		'toArrayExcludes',
		'simpleCache',
		'table',
		'option',
	];
	
	/**
	 * MY_RetailErp_Model constructor.
	 *
	 * @param int $id [Optional] Read data from database.
	 */
	public function __construct( $id = null ) {
		parent::__construct();
		$id = $this->absint( $id );
		if ( $id ) {
			$data = $this->db->get_where( $this->table, [ 'id' => $id ] )->row();
			if ( $data ) {
				$this->setData( $data );
				$this->id = $id;
			}
		}
	}
	
	/**
	 * @return int
	 */
	final public function getId() {
		return $this->id;
	}
	
	/**
	 * Check if instance data exists on db.
	 *
	 * @return bool
	 */
	public function exists() {
		return $this->id && $this->id > 0;
	}

	/** @noinspection PhpUnusedPrivateMethodInspection */
	final private function setId() {
		// Id setter shouldn't exists on the first place. :p
		// Please set from the constructor.
	}
	
	/**
	 * MySQL Timestamp
	 *
	 * @param string|DateTime|int $time   Input time string or object to convert.
	 *                                    empty for current time.
	 * @param string              $format Output format. default to mysql timestamp format.
	 *
	 * @return string
	 */
	protected function format_date( $time = '', $format = 'Y-m-d H:i:s' ) {
		if ( $time instanceof DateTime ) {
			$time = $time->format( $format );
		} else if ( is_numeric( $time ) ) {
			$time = date( $format, $time );
		} else if ( $_time = strtotime( $time ) ) {
			$time = date( $format, $_time );
		} else {
			$time = date( $format );
		}
		
		return $time;
	}
	
	/**
	 * To Array.
	 * Only public member.
	 *
	 * @return array
	 */
	public function to_array() {
		$out  = [];
		$keys = array_keys( get_object_vars( $this ) );
		$keys = array_diff( $keys, $this->toArrayExcludes );
	
		foreach ( $keys as $k ) {
			$m = $this->propToMethod( $k );
			if ( false !== $m ) {
				$out[ $k ] = $this->$m();
			}
		}
		
		return $out;
	}
	
	protected function propToMethod( $prop, $type = 'get' ) {
		
		$type = 'get' == $type ? 'get' : 'set';
		if ( strpos( $prop, '_' ) === 0 && 'set' === $type ) {
			return false;
		}
		$_prop = str_replace( '_', ' ', $prop );
		$_prop = str_replace( ' ', '', ucwords( $_prop ) );
		
		$_prop = $type . $_prop;
		if ( method_exists( $this, $_prop ) ) {
			return $_prop;
		}
		return false;
	}
	
	/**
	 * Set Data.
	 * @param object|array $data
	 */
	protected function setData( $data ) {
		$data = (array) $data;
		if ( empty( $data ) ) {
			return;
		}
		foreach ( $data as $k => $v ) {
			$m = $this->propToMethod( $k, 'set' );
			if ( $m ) {
				$this->$m( $v );
			} else if ( property_exists( $this, $k ) ) {
				$this->{$k} = $v;
			}
		}
	}
	
	/**
	 * Map id to db class object.
	 *
	 * @param string $object Object.
	 * @param int[]  $ids    Object Id.
	 * @param string $column Id Column if it's multidimensional.
	 * @param bool   $sort   Sort the object ids before mapping.
	 * @param bool   $unique Apply array_unique.
	 *
	 * @return array
	 */
	protected function idsToObject( $ids, $object, $column = 'id', $sort = false, $unique = false ) {
		if ( class_exists( $object, false ) && is_array( $ids ) ) {
			if ( isset( $ids[0][$column] ) ) {
				$ids = array_column( $ids, $column );
			}
			if ( $sort ) {
				
				sort( $ids );
			}
			if ( false !== $unique ) {
				$args = [ $ids ];
				if ( true !== $unique ) {
					$args[] = $unique;
				}
				$ids = call_user_func_array( 'array_unique', $args );
			}
			$objects = array_map( function( $id ) use( $object ) {
				if ( is_numeric( $id ) ) {
					return new $object( $id );
				}
				return null;
			}, $ids );
			return array_filter( $objects );
		}

		return [];
	}

	/**
	 * Get Data for save/update db.
	 *
	 * @return array
	 */
	protected function getData() {
		$out  = [];
		$keys = array_keys( get_object_vars( $this ) );
		$keys = array_diff( $keys, $this->toArrayExcludes );

//		unset( $data['table'] );
//		unset( $data['date_format'] );
//		unset( $data['id'] );

		// data can be null so don't remove any item from data array only because it's empty/null.
		// check if the item has setter to verify it has db column.
		// may be we can add a another column list to verify that.

		foreach ( $keys as $k ) {
			$m = $this->propToMethod( $k );
			if ( false !== $m && false !== $this->propToMethod( $k, 'set' ) ) {
				$out[ $k ] = $this->$m();
			}
		}

		return $out;
	}
	
	/**
	 * Save to database.
	 * Create/insert if needed.
	 *
	 * @return bool
	 */
	public function save() {
		$data = $this->getData();
		if ( ! $this->getId() ) {
			if ( $this->db->insert( $this->table, $data ) ) {
				$this->id = $this->absint( $this->db->insert_id() );
				return true;
			}
		} else {
			return $this->db->update( $this->table, $data, [ 'id' => $this->getId() ] ) ;
		}
		return false;
	}
	
	/**
	 * Delete the record.
	 *
	 * @return bool
	 */
	public function delete() {
		if ( $this->getId() ) {
			$this->db->delete( $this->table, [ 'id' => $this->getId() ] );
			return $this->db->affected_rows() > 0;
		}
		return false;
	}

	/**
	 * Update the Delete Flag 1 into the record.
	 *
	 * @return bool
	 */
	public function deleteFlag() {

		// Make sure that the entity has delete_flat column.

		if ( property_exists( $this, 'delete_flag' ) ) {
			if ( $this->getId() ) {
				return $this->db->update( $this->table, ['delete_flag' => 1], [ 'id' => $this->getId() ] ) ;
			}
		}

		return false;
	}
	
	/**
	 * To absolute integer
	 * @param mixed $maybeint any number
	 *
	 * @return int
	 */
	protected function absint( $maybeint ) {
		return abs( intval( $maybeint ) );
	}
	
	/**
	 *
	 * @param int|float $number
	 *
	 * @return string
	 */
	protected function money_format( $number ) {
		return $this->rerp->convertMoney( $number );
	}
	
	/**
	 * Get Prefixed table name for query.
	 *
	 * @param string $table Table name without prefix.
	 *
	 * @return string
	 */
	protected function get_table( $table ) {
		
		if ( false === strpos( $table, $this->db->dbprefix ) ) {
			return $this->db->dbprefix.$table;
		}
		
		return $table;
	}

	/**
	 * @param $where
	 * @param string $table
	 *
	 * @return int
	 */
	protected function count_all_where( $where, $table = '' ) {
		if ( empty( $table ) ) {
			$table = $this->table;
		}

		return $this->db->where( $where )->count_all_results( $table );
	}

	/**
	 * @param Erp_User|int $user
	 *
	 * @return Erp_User
	 * @throws Exception
	 */
	protected function get_user( $user ) {
		if ( ! is_a( $user, 'Erp_User' ) ) {
			$user = $this->absint( $user );
			$user = new Erp_User( $user );
		}

		if ( ! $user->exists() ) {
			throw new Exception( lang( 'user_not_exists' ) );
		}

		return $user;
	}

	private function generateRandomNumber( $len = 12 ) {
		$result = '';
		for ( $i = 0; $i < $len; $i ++ ) {
			$result .= mt_rand( 0, 9 );
		}

		return $result;
	}

	private function getSaleByReference( $ref ) {
		$this->db->like( 'reference_no', $ref );
		$q = $this->db->get( 'sales', 1 );
		if ( $q->num_rows() > 0 ) {
			return $q->row();
		}

		return false;
	}

	/** @noinspection PhpUnused */
	public function getRandomReference( $len = 12 ) {

		$ref = $this->generateRandomNumber( $len );

		while ( $this->getSaleByReference( $ref ) ) {
			$ref = $this->generateRandomNumber( $len );
		}

		return $ref;
	}
	
	/**
	 * clean reference prefix.
	 *
	 * @param string $prefix
	 *
	 * @return string
	 */
	private function _ref_prefix( $prefix = '' ) {
		$prefix = rtrim( $prefix, '/\\' );
		$prefix = strip_tags( $prefix );

		if ( is_numeric( $prefix ) || '' === $prefix ) {
			$prefix = 'rend';
		}

		return '_' . strtolower( $prefix );
	}
	
	/**
	 * Get Reference Sequence for Prefix.
	 * @param string $prefix
	 *
	 * @return int
	 */
	private function get_reference_sequence( $prefix = '' ) {
		$references = $this->Erp_Options->getOption( '_sequential_reference_storage' );
		$prefix     = $this->_ref_prefix( $prefix );
		return isset( $references[ $prefix ] ) ? absint( $references[ $prefix ] ) : 1;
	}

	/**
	 * Get Ref Sequence for Prefix.
	 *
	 * @param string $prefix Prefix.
	 * @param bool $exclude Exclude Prefix and return only ref.
	 *
	 * @return string
	 */
	public function get_sequential_reference( $prefix = '', $exclude = false ) {
		$sequence = $this->get_reference_sequence( $prefix );
		$sequence = sprintf( '%04s', $sequence );
		$ref_no   = ! $exclude ? $prefix : '';
		if ( $this->Settings->reference_format == 1 ) {
			$ref_no .= date( 'Y' ) . '/' . $sequence;
		} elseif ( $this->Settings->reference_format == 2 ) {
			$ref_no .= date( 'Y/m' ) . '/' . $sequence;
		} elseif ( $this->Settings->reference_format == 3 ) {
			$ref_no .= $sequence;
		} else {
			$ref_no .= $sequence;
		}
		
		return $ref_no;
	}
	
	/**
	 * Update Ref Sequence for Prefix.
	 *
	 * @param string $prefix
	 *
	 * @return bool
	 */
	protected function update_sequential_reference( $prefix = '' ) {
		$prefix     = $this->_ref_prefix( $prefix );
		$references = $this->Erp_Options->getOption( '_sequential_reference_storage' );
		$sequence = isset( $references[ $prefix ] ) ? $this->absint( $references[ $prefix ] ) : 1;
		$sequence += 1;
		$references[ $prefix ] = $sequence;
		
		return (bool) $this->Erp_Options->updateOption( '_sequential_reference_storage', $references, false );
	}
}
// End of the file MY_RetailErp_Model.php
