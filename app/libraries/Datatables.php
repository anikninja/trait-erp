<?php /** @noinspection PhpUndefinedVariableInspection */
/** @noinspection PhpUnused */
/** @noinspection RegExpRedundantEscape */
/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedFieldInspection */

if ( ! defined( 'BASEPATH' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Ignited Datatables
 *
 * This is a wrapper class/library based on the native Datatables server-side implementation by Allan Jardine
 * found at http://datatables.net/examples/data_sources/server_side.html for CodeIgniter
 *
 * @package    CodeIgniter
 * @subpackage libraries
 * @category   library
 * @version    1.15
 * @author     Vincent Bambico <metal.conspiracy@gmail.com>
 *             Yusuf Ozdemir <yusuf@ozdemir.be>
 * @link       http://ellislab.com/forums/viewthread/160896/
 */
class Datatables {
	
	/**
	 * @var array
	 */
	private $add_columns = [];
	/**
	 * Global container variables for chained argument results
	 *
	 * @var CI_Controller
	 */
	private $ci;
	/**
	 * @var array
	 */
	private $columns = [];
	/**
	 * @var string
	 */
	private $distinct;
	/**
	 * @var array
	 */
	private $edit_columns = [];
	/**
	 * @var array
	 */
	private $filter = [];
	/**
	 * @var array
	 */
	private $group_by = [];
	/**
	 * @var array
	 */
	private $joins = [];
	/**
	 * @var array
	 */
	private $like = [];
	/**
	 * @var array
	 */
	private $or_where = [];
	/**
	 * @var array
	 */
	private $select = [];
	/**
	 * @var string
	 */
	private $table;
	/**
	 * @var array
	 */
	private $unset_columns = [];
	/**
	 * @var array
	 */
	private $where = [];
	
	/**
	 * Copies an instance of CI
	 */
	public function __construct() {
		$this->ci = &get_instance();
	}
	
	/**
	 * Sets additional column variables for adding custom columns
	 *
	 * @param string $column
	 * @param string $content
	 * @param string $match_replacement
	 *
	 * @return Datatables
	 */
	public function add_column( $column, $content, $match_replacement = null ) {
		$this->add_columns[ $column ] = [
			'content'     => $content,
			'replacement' => $this->explode( ',', $match_replacement ),
		];
		
		return $this;
	}
	
	/**
	 * Generates the DISTINCT portion of the query
	 *
	 * @param string $column
	 *
	 * @return Datatables
	 */
	public function distinct( $column ) {
		$this->distinct = $column;
		$this->ci->db->distinct( $column );
		
		return $this;
	}
	
	/**
	 * Sets additional column variables for editing columns
	 *
	 * @param string $column
	 * @param string $content
	 * @param string $match_replacement
	 *
	 * @return Datatables
	 */
	public function edit_column( $column, $content, $match_replacement ) {
		$this->edit_columns[ $column ][] = [
			'content'     => $content,
			'replacement' => $this->explode( ',',
				$match_replacement ),
		];
		
		return $this;
	}
	
	/**
	 * Generates the WHERE portion of the query
	 *
	 * @param mixed  $key_condition
	 * @param string $val
	 * @param bool   $backtick_protect
	 *
	 * @return Datatables
	 */
	public function filter( $key_condition, $val = null, $backtick_protect = true ) {
		$this->filter[] = [ $key_condition, $val, $backtick_protect ];
		
		return $this;
	}
	
	/**
	 * Generates the FROM portion of the query
	 *
	 * @param string $table
	 *
	 * @return Datatables
	 */
	public function from( $table ) {
		$this->table = $table;
		
		return $this;
	}
	
	/**
	 * Builds all the necessary query segments and performs the main query based on results set from chained statements
	 *
	 * @param string $output
	 * @param string $charset
	 *
	 * @return string
	 */
	public function generate( $output = 'json', $charset = 'UTF-8' ) {
		if ( strtolower( $output ) == 'json' ) {
			$this->get_paging();
		}
		
		$this->get_ordering();
		$this->get_filtering();
		
		return $this->produce_output( strtolower( $output ), strtolower( $charset ) );
	}
	
	/**
	 * Generates a custom GROUP BY portion of the query
	 *
	 * @param string $val
	 *
	 * @return Datatables
	 */
	public function group_by( $val ) {
		$this->group_by[] = $val;
		$this->ci->db->group_by( $val );
		
		return $this;
	}
	
	/**
	 * Generates the JOIN portion of the query
	 *
	 * @param string $table
	 * @param string $fk
	 * @param string $type
	 *
	 * @return Datatables
	 */
	public function join( $table, $fk, $type = null ) {
		$this->joins[] = [ $table, $fk, $type ];
		$this->ci->db->join( $table, $fk, $type );
		
		return $this;
	}
	
	/**
	 * Generates a %LIKE% portion of the query
	 *
	 * @param mixed  $key_condition
	 * @param string $val
	 * @param bool   $backtick_protect
	 *
	 * @return Datatables
	 */
	public function like( $key_condition, $val = null, $backtick_protect = true ) {
		$this->like[] = [ $key_condition, $val, $backtick_protect ];
		$this->ci->db->like( $key_condition, $val, $backtick_protect );
		
		return $this;
	}
	
	/**
	 * Generates the WHERE portion of the query
	 *
	 * @param mixed  $key_condition
	 * @param string $val
	 * @param bool   $backtick_protect
	 *
	 * @return Datatables
	 */
	public function or_where( $key_condition, $val = null, $backtick_protect = true ) {
		$this->or_where[] = [ $key_condition, $val, $backtick_protect ];
		$this->ci->db->or_where( $key_condition, $val, $backtick_protect );
		
		return $this;
	}
	
	/**
	 * Generates the SELECT portion of the query
	 *
	 * @param string $columns
	 * @param bool   $backtick_protect
	 *
	 * @return Datatables
	 */
	public function select( $columns, $backtick_protect = true ) {
		foreach ( $this->explode( ',', $columns ) as $val ) {
			$column                  = trim( preg_replace( '/(.*)\s+as\s+(\w*)/i', '$2', $val ) );
			$this->columns[]         = $column;
			$this->select[ $column ] = trim( preg_replace( '/(.*)\s+as\s+(\w*)/i', '$1', $val ) );
		}
		
		$this->ci->db->select( $columns, $backtick_protect );
		
		return $this;
	}
	
	/**
	 * If you establish multiple databases in config/database.php this will allow you to
	 * set the database (other than $active_group) - more info: http://ellislab.com/forums/viewthread/145901/#712942
	 * @param string $db_name
	 */
	public function set_database( $db_name ) {
		$db_data      = $this->ci->load->database( $db_name, true );
		$this->ci->db = $db_data;
	}
	
	/**
	 * Unset column
	 *
	 * @param string $column
	 *
	 * @return Datatables
	 */
	public function unset_column( $column ) {
		$column              = explode( ',', $column );
		$this->unset_columns = array_merge( $this->unset_columns, $column );
		
		return $this;
	}
	
	/**
	 * Generates the WHERE portion of the query
	 *
	 * @param mixed  $key_condition
	 * @param string $val
	 * @param bool   $backtick_protect
	 *
	 * @return Datatables
	 */
	public function where( $key_condition, $val = null, $backtick_protect = true ) {
		$this->where[] = [ $key_condition, $val, $backtick_protect ];
		$this->ci->db->where( $key_condition, $val, $backtick_protect );
		
		return $this;
	}
	
	/**
	 * Return the difference of open and close characters
	 *
	 * @param string $str
	 * @param string $open
	 * @param string $close
	 *
	 * @return int
	 */
	private function balanceChars( $str, $open, $close ) {
		$openCount  = substr_count( $str, $open );
		$closeCount = substr_count( $str, $close );
		
		return $openCount - $closeCount;
	}
	
	/**
	 * Check mDataprop
	 *
	 * @return bool
	 */
	private function check_mDataprop() {
		if ( ! $this->ci->input->post( 'mDataProp_0' ) ) {
			return false;
		}
		
		for ( $i = 0; $i < intval( $this->ci->input->post( 'iColumns' ) ); $i ++ ) {
			if ( ! is_numeric( $this->ci->input->post( 'mDataProp_' . $i ) ) ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Runs callback functions and makes replacements
	 *
	 * @param mixed $custom_val
	 * @param mixed $row_data
	 *
	 * @return string $custom_val['content']
	 */
	private function exec_replace( $custom_val, $row_data ) {
		if ( isset( $custom_val['replacement'] ) && is_array( $custom_val['replacement'] ) ) {
			foreach ( $custom_val['replacement'] as $key => $val ) {
				$sval = preg_replace( "/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim( $val ) );
				
				if ( preg_match( '/(\w+::\w+|\w+)\((.*)\)/i', $val, $matches )
				     && is_callable( $matches[1] ) ) {
					$func = $matches[1];
					$args = preg_split( "/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . '[,]+/', $matches[2], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
					
					foreach ( $args as $args_key => $args_val ) {
						$args_val          = preg_replace( "/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim( $args_val ) );
						$args[ $args_key ] = ( in_array( $args_val, $this->columns ) ) ? ( $row_data[ ( $this->check_mDataprop() ) ? $args_val : array_search( $args_val, $this->columns ) ] ) : $args_val;
					}
					
					$replace_string = call_user_func_array( $func, $args );
				} elseif ( in_array( $sval, $this->columns ) ) {
					$replace_string = $row_data[ ( $this->check_mDataprop() ) ? $sval
						: array_search( $sval, $this->columns ) ];
				} else {
					$replace_string = $sval;
				}
				
				$custom_val['content'] = str_ireplace( '$' . ( $key + 1 ), $replace_string, $custom_val['content'] );
			}
		}
		
		return $custom_val['content'];
	}
	
	/**
	 * Explode, but ignore delimiter until closing characters are found
	 *
	 * @param string $delimiter
	 * @param string $str
	 * @param string $open
	 * @param string $close
	 *
	 * @return array $retval
	 */
	private function explode( $delimiter, $str, $open = '(', $close = ')' ) {
		$retval  = [];
		$hold    = [];
		$balance = 0;
		$parts   = explode( $delimiter, $str );
		
		foreach ( $parts as $part ) {
			$hold[]  = $part;
			$balance += $this->balanceChars( $part, $open, $close );
			
			if ( $balance < 1 ) {
				$retval[] = implode( $delimiter, $hold );
				$hold     = [];
				$balance  = 0;
			}
		}
		
		if ( count( $hold ) > 0 ) {
			$retval[] = implode( $delimiter, $hold );
		}
		
		return $retval;
	}
	
	/**
	 * Compiles the select statement based on the other functions called and runs the query
	 *
	 * @return CI_DB_mysqli_result
	 */
	private function get_display_result() {
		return $this->ci->db->get( $this->table );
	}
	
	/**
	 * Generates a %LIKE% portion of the query
	 *
	 * @return void
	 */
	private function get_filtering() {
		if ( $this->check_mDataprop() ) {
			$mColArray = $this->get_mDataprop();
		} elseif ( $this->ci->input->post( 'sColumns' ) ) {
			$mColArray = explode( ',', $this->ci->input->post( 'sColumns' ) );
		} else {
			$mColArray = $this->columns;
		}
		
		$sWhere    = '';
		$sSearch   = $this->ci->db->escape_like_str( $this->ci->input->post( 'sSearch' ) );
		$mColArray = array_values( array_diff( $mColArray, $this->unset_columns ) );
		$columns   = array_values( array_diff( $this->columns, $this->unset_columns ) );
		
		if ( $sSearch != '' ) {
			for ( $i = 0; $i < count( $mColArray ); $i ++ ) {
				if ( $this->ci->input->post( 'bSearchable_' . $i ) == 'true'
				     && in_array( $mColArray[ $i ], $columns ) ) {
					$sWhere .= $this->select[ $mColArray[ $i ] ] . " LIKE '%" . $sSearch . "%' OR ";
				}
			}
		}
		
		$sWhere = substr_replace( $sWhere, '', - 3 );
		
		if ( $sWhere != '' ) {
			$this->ci->db->where( '(' . $sWhere . ')' );
		}
		
		$sRangeSeparator = $this->ci->input->post( 'sRangeSeparator' );
		
		for ( $i = 0; $i < intval( $this->ci->input->post( 'iColumns' ) ); $i ++ ) {
			if ( isset( $_POST[ 'sSearch_' . $i ] )
			     && $this->ci->input->post( 'sSearch_' . $i ) != ''
			     && in_array( $mColArray[ $i ], $columns ) ) {
				$miSearch = explode( ',', $this->ci->input->post( 'sSearch_' . $i ) );
				
				foreach ( $miSearch as $val ) {
					if ( preg_match( "/(<=|>=|=|<|>)(\s*)(.+)/i", trim( $val ), $matches ) ) {
						$this->ci->db->where( $this->select[ $mColArray[ $i ] ] . ' ' . $matches[1],
							$matches[3] );
					} elseif ( ! empty( $sRangeSeparator )
					           && preg_match( "/(.*)$sRangeSeparator(.*)/i",
							trim( $val ),
							$matches ) ) {
						$rangeQuery = '';
						
						if ( ! empty( $matches[1] ) ) {
							$rangeQuery = 'STR_TO_DATE(' . $this->select[ $mColArray[ $i ] ]
							              . ",'%d/%m/%y %H:%i:%s') >= STR_TO_DATE('" . $matches[1]
							              . " 00:00:00','%d/%m/%y %H:%i:%s')";
						}
						
						if ( ! empty( $matches[2] ) ) {
							$rangeQuery .= ( ! empty( $rangeQuery ) ? ' AND ' : '' )
							               . 'STR_TO_DATE(' . $this->select[ $mColArray[ $i ] ]
							               . ",'%d/%m/%y %H:%i:%s') <= STR_TO_DATE('" . $matches[2]
							               . " 23:59:59','%d/%m/%y %H:%i:%s')";
						}
						
						if ( ! empty( $matches[1] ) || ! empty( $matches[2] ) ) {
							$this->ci->db->where( $rangeQuery );
						}
					} else {
						$this->ci->db->where( $this->select[ $mColArray[ $i ] ] . ' LIKE',
							'%' . $val . '%' );
					}
				}
			}
		}
		
		foreach ( $this->filter as $val ) {
			$this->ci->db->where( $val[0], $val[1], $val[2] );
		}
	}
	
	/**
	 * Get mDataprop order
	 *
	 * @return string[]
	 */
	private function get_mDataprop() {
		$mDataProp = [];
		
		for ( $i = 0; $i < intval( $this->ci->input->post( 'iColumns' ) ); $i ++ ) {
			$mDataProp[] = $this->ci->input->post( 'mDataProp_' . $i );
		}
		
		return $mDataProp;
	}
	
	/**
	 * Generates the ORDER BY portion of the query
	 *
	 * @return void
	 */
	private function get_ordering() {
		if ( $this->check_mDataprop() ) {
			$mColArray = $this->get_mDataprop();
		} elseif ( $this->ci->input->post( 'sColumns' ) ) {
			$mColArray = explode( ',', $this->ci->input->post( 'sColumns' ) );
		} else {
			$mColArray = $this->columns;
		}
		
		$mColArray = array_values( array_diff( $mColArray, $this->unset_columns ) );
		$columns   = array_values( array_diff( $this->columns, $this->unset_columns ) );
		
		for ( $i = 0; $i < intval( $this->ci->input->post( 'iSortingCols' ) ); $i ++ ) {
			if (
				isset( $mColArray[ intval( $this->ci->input->post( 'iSortCol_' . $i ) ) ] ) &&
				in_array( $mColArray[ intval( $this->ci->input->post( 'iSortCol_' . $i ) ) ], $columns ) &&
				$this->ci->input->post( 'bSortable_' . intval( $this->ci->input->post( 'iSortCol_' . $i ) ) ) == 'true'
			) {
				$this->ci->db->order_by(
					$mColArray[ intval( $this->ci->input->post( 'iSortCol_' . $i ) ) ],
					$this->ci->input->post( 'sSortDir_' . $i )
				);
			}
		}
	}
	
	/**
	 * Generates the LIMIT portion of the query
	 *
	 * @return void
	 */
	private function get_paging() {
		$iStart  = $this->ci->input->post( 'iDisplayStart' );
		$iLength = $this->ci->input->post( 'iDisplayLength' );
		
		if ( $iLength != '' && $iLength != '-1' ) {
			$this->ci->db->limit( $iLength, ( $iStart ) ? $iStart : 0 );
		}
	}
	
	/**
	 * Get result count
	 *
	 * @param bool $filtering
	 * @return int
	 */
	private function get_total_results( $filtering = false ) {
		if ( $filtering ) {
			$this->get_filtering();
		}
		
		foreach ( $this->joins as $val ) {
			$this->ci->db->join( $val[0], $val[1], $val[2] );
		}
		
		foreach ( $this->where as $val ) {
			$this->ci->db->where( $val[0], $val[1], $val[2] );
		}
		
		foreach ( $this->or_where as $val ) {
			$this->ci->db->or_where( $val[0], $val[1], $val[2] );
		}
		
		foreach ( $this->group_by as $val ) {
			$this->ci->db->group_by( $val );
		}
		
		foreach ( $this->like as $val ) {
			$this->ci->db->like( $val[0], $val[1], $val[2] );
		}
		
		if ( strlen( $this->distinct ) > 0 ) {
			$this->ci->db->distinct( $this->distinct );
			$this->ci->db->select( $this->columns );
		}
		
		$query = $this->ci->db->get( $this->table, null, null, false );
		
		return $query->num_rows();
	}
	
	/**
	 * Workaround for json_encode's UTF-8 encoding if a different charset needs to be used
	 *
	 * @param mixed $result
	 *
	 * @return string
	 */
	private function jsonify( $result = false ) {
		if ( is_null( $result ) ) {
			return 'null';
		}
		
		if ( $result === false ) {
			return 'false';
		}
		
		if ( $result === true ) {
			return 'true';
		}
		
		if ( is_scalar( $result ) ) {
			if ( is_float( $result ) ) {
				return floatval( str_replace( ',', '.', strval( $result ) ) );
			}
			
			if ( is_string( $result ) ) {
				static $jsonReplaces = [
					[ '\\', '/', '\n', '\t', '\r', '\b', '\f', '"' ],
					[ '\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"' ],
				];
				
				return '"' . str_replace( $jsonReplaces[0], $jsonReplaces[1], $result ) . '"';
			} else {
				return $result;
			}
		}
		
		$isList = true;
		
		for ( $i = 0, reset( $result ); $i < count( $result ); $i ++, next( $result ) ) {
			if ( key( $result ) !== $i ) {
				$isList = false;
				break;
			}
		}
		
		$json = [];
		
		if ( $isList ) {
			foreach ( $result as $value ) {
				$json[] = $this->jsonify( $value );
			}
			
			return '[' . join( ',', $json ) . ']';
		} else {
			foreach ( $result as $key => $value ) {
				$json[] = $this->jsonify( $key ) . ':' . $this->jsonify( $value );
			}
			
			return '{' . join( ',', $json ) . '}';
		}
	}
	
	/**
	 * Builds an encoded string data. Returns JSON by default, and an array of aaData and sColumns if output is set to raw.
	 *
	 * @param string $output
	 * @param string $charset
	 *
	 * @return string|array
	 */
	private function produce_output( $output, $charset ) {
		$aaData  = [];
		$rResult = $this->get_display_result();
		
		if ( $output == 'json' ) {
			$iTotal         = $this->get_total_results();
			$iFilteredTotal = $this->get_total_results( true );
		}
		
		foreach ( $rResult->result_array() as $row_key => $row_val ) {
			$aaData[ $row_key ] = ( $this->check_mDataprop() ) ? $row_val
				: array_values( $row_val );
			
			foreach ( $this->add_columns as $field => $val ) {
				if ( $this->check_mDataprop() ) {
					$aaData[ $row_key ][ $field ] = $this->exec_replace( $val,
						$aaData[ $row_key ] );
				} else {
					$aaData[ $row_key ][] = $this->exec_replace( $val, $aaData[ $row_key ] );
				}
			}
			
			foreach ( $this->edit_columns as $modkey => $modval ) {
				foreach ( $modval as $val ) {
					$aaData[ $row_key ][ ( $this->check_mDataprop() ) ? $modkey
						: array_search( $modkey, $this->columns ) ]
						= $this->exec_replace( $val, $aaData[ $row_key ] );
				}
			}
			
			$aaData[ $row_key ] = array_diff_key( $aaData[ $row_key ],
				( $this->check_mDataprop() ) ? $this->unset_columns
					: array_intersect( $this->columns, $this->unset_columns ) );
			
			if ( ! $this->check_mDataprop() ) {
				$aaData[ $row_key ] = array_values( $aaData[ $row_key ] );
			}
		}
		
		$sColumns = array_diff( $this->columns, $this->unset_columns );
		$sColumns = array_merge_recursive( $sColumns, array_keys( $this->add_columns ) );
		
		if ( $output == 'json' ) {
			$sOutput = [
				'sEcho'                => intval( $this->ci->input->post( 'sEcho' ) ),
				'iTotalRecords'        => $iTotal,
				'iTotalDisplayRecords' => $iFilteredTotal,
				'aaData'               => $aaData,
				'sColumns'             => implode( ',', $sColumns ),
			];
			
			if ( $charset == 'utf-8' ) {
				return json_encode( $sOutput );
			} else {
				return $this->jsonify( $sOutput );
			}
		} else {
			return [ 'aaData' => $aaData, 'sColumns' => $sColumns ];
		}
	}
}
/* End of file Datatables.php */
/* Location: ./application/libraries/Datatables.php */
