<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Add admin_form_open
if (!function_exists('admin_form_open')) {
    function admin_form_open($action = '', $attributes = [], $hidden = [])
    {
        return form_open('admin/' . $action, $attributes, $hidden);
    }
}

// Add admin_form_open_multipart
if (!function_exists('admin_form_open_multipart')) {
    function admin_form_open_multipart($action = '', $attributes = [], $hidden = [])
    {
        if (is_string($attributes)) {
            $attributes .= ' enctype="multipart/form-data"';
        } else {
            $attributes['enctype'] = 'multipart/form-data';
        }
        return admin_form_open($action, $attributes, $hidden);
    }
}

// Add shop_form_open
if (!function_exists('shop_form_open')) {
    function shop_form_open($action = '', $attributes = [], $hidden = [])
    {
        return form_open('shop/' . $action, $attributes, $hidden);
    }
}

// Add shop_form_open_multipart
if (!function_exists('shop_form_open_multipart')) {
    function shop_form_open_multipart($action = '', $attributes = [], $hidden = [])
    {
        if (is_string($attributes)) {
            $attributes .= ' enctype="multipart/form-data"';
        } else {
            $attributes['enctype'] = 'multipart/form-data';
        }
        return shop_form_open($action, $attributes, $hidden);
    }
}

if ( ! function_exists( '__checked_selected_helper' ) ) {
	/**
	 * Private helper function for checked, selected, disabled and readonly.
	 *
	 * @param mixed  $helper  One of the values to compare
	 * @param mixed  $current (true) The other value to compare if not just true
	 * @param bool   $echo    Whether to echo or just return the string
	 * @param string $type    The type of checked|selected|disabled|readonly we are doing
	 * @return string HTML attribute or empty string
	 */
	function __checked_selected_helper( $helper, $current, $echo, $type ) {
		if ( (string) $helper === (string) $current ) {
			$result = " $type='$type'";
		} else {
			$result = '';
		}
		if ( $echo ) {
			echo $result;
		}
		
		return $result;
	}
}
if ( ! function_exists( 'checked' ) ) {
	/**
	 * Outputs the HTML checked attribute.
	 *
	 * Compares the first two arguments and if identical marks as checked
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $checked One of the values to compare
	 * @param mixed $current (true) The other value to compare if not just true
	 * @param bool  $echo    Whether to echo or just return the string
	 * @return string HTML attribute or empty string
	 */
	function checked( $checked, $current = true, $echo = true ) {
		return __checked_selected_helper( $checked, $current, $echo, 'checked' );
	}
}
if ( ! function_exists( 'selected' ) ) {
	/**
	 * Outputs the HTML selected attribute.
	 *
	 * Compares the first two arguments and if identical marks as selected
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $selected One of the values to compare
	 * @param mixed $current  (true) The other value to compare if not just true
	 * @param bool  $echo     Whether to echo or just return the string
	 * @return string HTML attribute or empty string
	 */
	function selected( $selected, $current = true, $echo = true ) {
		return __checked_selected_helper( $selected, $current, $echo, 'selected' );
	}
}
if ( ! function_exists( 'disabled' ) ) {
	/**
	 * Outputs the HTML disabled attribute.
	 *
	 * Compares the first two arguments and if identical marks as disabled
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $disabled One of the values to compare
	 * @param mixed $current  (true) The other value to compare if not just true
	 * @param bool  $echo     Whether to echo or just return the string
	 * @return string HTML attribute or empty string
	 */
	function disabled( $disabled, $current = true, $echo = true ) {
		return __checked_selected_helper( $disabled, $current, $echo, 'disabled' );
	}
}
if ( ! function_exists( 'readonly' ) ) {
	/**
	 * Outputs the HTML readonly attribute.
	 *
	 * Compares the first two arguments and if identical marks as readonly
	 *
	 * @since 4.9.0
	 *
	 * @param mixed $readonly One of the values to compare
	 * @param mixed $current  (true) The other value to compare if not just true
	 * @param bool  $echo     Whether to echo or just return the string
	 * @return string HTML attribute or empty string
	 */
	function readonly( $readonly, $current = true, $echo = true ) {
		return __checked_selected_helper( $readonly, $current, $echo, 'readonly' );
	}
}
if ( ! function_exists( 'readonly_disabled' ) ) {
	function readonly_disabled( $check, $current = true, $echo = true ) {
		$result = readonly( $check, $current, false ) . disabled( $check, $current, false );
		if ( $echo ) {
			echo $result;
		}
		
		return $result;
	}
}
if ( ! function_exists( 'is_unique_exclude_where' ) ) {
	function is_unique_exclude_where( $str, $field ) {
		sscanf( $field, '%[^.].%[^.]', $table, $field );
		$field = explode(',', $field );
		$field2 = explode( ':', $field[1] );
		$CI = get_instance();
		if ( isset( $CI->db ) ) {
			$CI->db->limit(1);
			$CI->db->where( $field[0], $str );
			$CI->db->where( "{$field2[0]} <> {$field2[1]}" );
			if ( $CI->db->get( $table )->num_rows() ) {
				$CI->form_validation->set_message( 'unique', $CI->lang->line( 'form_validation_is_unique' ) );
				return false;
			}
			return true;
		} else {
			return false;
		}
	}
}
if ( ! function_exists( 'validate_conditional_field' ) ) {
	function validate_conditional_field( $val, $args ) {
		$args = explode( '|', $args );
		$name = $args[0];
		unset( $args[0] );
		$args = array_map( function( $arg ) {
			return explode( ':', $arg );
		}, $args );
		$args = array_filter( $args );
		if ( ! empty( $args ) ) {
			$CI = get_instance();
			foreach ( $args as $arg ) {
				if ( count( $arg ) !== 2 ) {
					continue;
				}
				list( $cond, $_field ) = $arg;
				$field = isset( $_REQUEST[ $_field ] ) ? xss_clean( $_REQUEST[ $_field ] ) : '';
				
				switch( $cond ) {
					case '<':
						if ( ! ( $val < $field ) ) {
							$m = sprintf(
								lang( 'x_must_be_less_than_y' ),
								lang( $name ),
								lang( $_field )
							);
							$CI->form_validation->set_message( 'validate_conditional_field', $m );
							return false;
						}
						break;
					case '<=':
						if ( ! ( $val <= $field ) ) {
							$m = sprintf(
								lang( 'x_must_be_less_than_or_equal_to_y' ),
								lang( $name ),
								lang( $_field )
							);
							$CI->form_validation->set_message( 'validate_conditional_field', $m );
							return false;
						}
						break;
					case '>':
						if ( ! ( $val > $field ) ) {
							$m = sprintf(
								lang( 'x_must_be_greater_than_y' ),
								lang( $name ),
								lang( $_field )
							);
							$CI->form_validation->set_message( 'validate_conditional_field', $m );
							return false;
						}
						break;
					case '>=':
						if ( ! ( $val >= $field ) ) {
							$m = sprintf(
								lang( 'x_must_be_greater_than_or_equal_to_y' ),
								lang( $name ),
								lang( $_field )
							);
							$CI->form_validation->set_message( 'validate_conditional_field', $m );
							return false;
						}
						break;
					case '!=':
					case '!==':
						if ( ! ( $val != $field ) ) {
							$m = sprintf(
								lang( 'x_must_not_be_equal_to_y' ),
								lang( $name ),
								lang( $_field )
							);
							$CI->form_validation->set_message( 'validate_conditional_field', $m );
							return false;
						}
						break;
					case '==':
					case '===':
						if ( ! ( $val == $field ) ) {
							$m = sprintf(
								lang( 'x_must_be_equal_to_y' ),
								lang( $name ),
								lang( $_field )
							);
							$CI->form_validation->set_message( 'validate_conditional_field', $m );
							return false;
						}
					break;
				}
			}
		}
		return true;
	}
}
