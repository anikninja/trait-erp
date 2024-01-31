<?php
/**
 * Theme Helper Functions.
 */
defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

// Simple browser & server detection.
global $is_lynx, $is_gecko, $is_winIE, $is_macIE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone, $is_IE, $is_edge;
$is_lynx   = false;
$is_gecko  = false;
$is_winIE  = false;
$is_macIE  = false;
$is_opera  = false;
$is_NS4    = false;
$is_safari = false;
$is_chrome = false;
$is_iphone = false;
$is_edge   = false;

if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
	if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Lynx' ) !== false ) {
		$is_lynx = true;
	} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Edge' ) !== false ) {
		$is_edge = true;
	} elseif ( stripos( $_SERVER['HTTP_USER_AGENT'], 'chrome' ) !== false ) {
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'chromeframe' ) !== false ) {
			$is_chrome = true;
			if ( $is_chrome ) {
//				header( 'X-UA-Compatible: chrome=1' );
			}
			$is_winIE = ! $is_chrome;
		} else {
			$is_chrome = true;
		}
	} elseif ( stripos( $_SERVER['HTTP_USER_AGENT'], 'safari' ) !== false ) {
		$is_safari = true;
	} elseif ( ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE' ) !== false || strpos( $_SERVER['HTTP_USER_AGENT'], 'Trident' ) !== false ) && strpos( $_SERVER['HTTP_USER_AGENT'], 'Win' ) !== false ) {
		$is_winIE = true;
	} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE' ) !== false && strpos( $_SERVER['HTTP_USER_AGENT'], 'Mac' ) !== false ) {
		$is_macIE = true;
	} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Gecko' ) !== false ) {
		$is_gecko = true;
	} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera' ) !== false ) {
		$is_opera = true;
	} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Nav' ) !== false && strpos( $_SERVER['HTTP_USER_AGENT'], 'Mozilla/4.' ) !== false ) {
		$is_NS4 = true;
	}
}

if ( $is_safari && stripos( $_SERVER['HTTP_USER_AGENT'], 'mobile' ) !== false ) {
	$is_iphone = true;
}

$is_IE = ( $is_macIE || $is_winIE );

// Server detection.

/**
 * Whether the server software is Apache or something else
 *
 * @return bool
 */
function ci_is_apache() {
	return ( strpos( $_SERVER['SERVER_SOFTWARE'], 'Apache' ) !== false || strpos( $_SERVER['SERVER_SOFTWARE'], 'LiteSpeed' ) !== false );;
}

/**
 * Whether the server software is Nginx or something else
 *
 * @return bool
 */
function ci_is_nginx() {
	return ( strpos( $_SERVER['SERVER_SOFTWARE'], 'nginx' ) !== false );
}

/**
 * Whether the server software is IIS or something else
 *
 * @return bool
 */
function ci_is_iis() {
	return ! ci_is_apache() && ( strpos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS' ) !== false || strpos( $_SERVER['SERVER_SOFTWARE'], 'ExpressionDevServer' ) !== false );
}

/**
 * Whether the server software is IIS 7.X or greater
 *
 * @return bool
 */
function ci_is_iis7() {
	return ci_is_iis() && intval( substr( $_SERVER['SERVER_SOFTWARE'], strpos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS/' ) + 14 ) ) >= 7;
}

/**
 * Test if the current browser runs on a mobile device (smart phone, tablet, etc.)
 *
 * @return bool
 */
function ci_is_mobile() {
	if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
		$is_mobile = false;
	} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Mobile' ) !== false // Many mobile devices (all iPhone, iPad, etc.)
	           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Android' ) !== false
	           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Silk/' ) !== false
	           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Kindle' ) !== false
	           || strpos( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' ) !== false
	           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) !== false
	           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mobi' ) !== false ) {
		$is_mobile = true;
	} else {
		$is_mobile = false;
	}
	
	return $is_mobile;
}
function ci_is_iphone() {
	global $is_iphone;
	return $is_iphone;
}

if ( ! function_exists( 'array_key_first' ) ) {
	/**
	 * @param array $arr
	 *
	 * @return int|string|null
	 */
	function array_key_first( array $arr ) {
		foreach ( $arr as $key => $unused ) {
			return $key;
		}
		
		return null;
	}
}

// cache helper.
if ( ! function_exists( 'ci_delete_category_caches' ) ) {
	function ci_delete_category_caches() {
		ci_delete_options_like( 'category_list' );
	}
}
if ( ! function_exists( 'ci_delete_options' ) ) {
	function ci_delete_options( $option_names ) {
		$CI = get_instance();
		foreach( $option_names as $name ) {
			$CI->Erp_Options->deleteOption( $name );
		}
	}
}
if ( ! function_exists( 'ci_delete_options_in' ) ) {
	function ci_delete_options_in( $option_names ) {
		$CI = get_instance();
		foreach( $option_names as $name ) {
			$CI->Erp_Options->deleteOptionsIn( $name );
		}
	}
}
if ( ! function_exists( 'ci_delete_options_like' ) ) {
	function ci_delete_options_like( $option_names ) {
		$CI = get_instance();
		if ( is_string( $option_names ) ) {
			$CI->Erp_Options->deleteOptionsLike( $option_names );
			return;
		}
		
		foreach( (array) $option_names as $name ) {
			$CI->Erp_Options->deleteOptionsLike( $name );
		}
	}
}

if ( ! function_exists( 'get_template_file_path' ) ) {
	/**
	 * Get template file.
	 * @param string $slug file path relative to shop/views directory.
	 * @param string $theme Theme name. default to current theme.
	 * @param string $type admin or shop.
	 *
	 * @return string|void
	 */
	function get_template_file_path( $slug = '', $theme = '', $type = 'shop' ) {
		$slug = ltrim( $slug, '\\/' );
		$slug = rtrim( $slug, '\\/' );
		if ( ! $slug ) {
			return;
		}
		if ( ! $theme ) {
			$CI = get_instance();
			$CI->load->model( 'site' );
			/** @noinspection PhpUndefinedMethodInspection */
			/** @noinspection PhpPossiblePolymorphicInvocationInspection */
			$settings = $CI->site->get_setting();
			$theme    = $settings ? $settings->theme : false;
		}
		if ( ! $theme ) {
			return;
		}
		$type = 'admin' === $type ? 'admin' : 'shop';
		return FCPATH . 'themes' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $slug;
	}
}
if ( ! function_exists( 'absint' ) ) {
	/**
	 * @param mixed $maybeint The scalar value being converted to an positive integer
	 *
	 * @return int
	 */
	function absint( $maybeint ) {
		return abs( intval( $maybeint ) );
	}
}
if ( ! function_exists( 'absfloat' ) ) {
	/**
	 * @param mixed $maybeint The scalar value being converted to an positive integer
	 *
	 * @return int
	 */
	function absfloat( $maybeint ) {
		return abs( floatval( $maybeint ) );
	}
}
if ( ! function_exists( 'sanitize_formatted_id' ) ) {
	/**
	 * Sanitize Formatted id.
	 *
	 * @param string $string string to sanitize.
	 *
	 * @return int
	 */
	function sanitize_formatted_id( $string ) {
		return absint( preg_replace( '/[^0-9]/', '', $string ) );
	}
}
if ( ! function_exists( 'sanitize_referral_id' ) ) {
	/**
	 * Alias to sanitize_formatted_id
	 *
	 * @param string $string
	 *
	 * @return int
	 * @see sanitize_formatted_id()
	 */
	function sanitize_referral_id( $string ) {
		return sanitize_formatted_id( $string );
	}
}
if ( ! function_exists( 'sanitize_integer_array' ) ) {
	/**
     * Validate & Sanitize array of integer
     *
	 * @param $integers
	 *
	 * @return array
	 */
	function sanitize_integer_array( $integers ) {
	    if ( ! is_array( $integers ) ) {
	        return [];
        }
		$integers = array_map( 'absint', $integers );
		$integers = array_filter( $integers );
		return array_unique( $integers );
    }
}
if ( ! function_exists( 'sanitize_comma_separated_integers' ) ) {
	/**
     * Sanitize comma separated integers (ids)
     *
	 * @param string $integers input data.
	 * @param bool $csv_out
     * @return int[]|string
	 */
	function sanitize_comma_separated_integers( $integers, $csv_out = true ) {
		$integers = explode( ',', $integers );
		$integers = sanitize_integer_array( $integers );
		if ( ! $csv_out ) {
			return $integers;
		}

		return implode( ',', $integers );
	}
}
if ( ! function_exists( 'sanitize_email_array' ) ) {
	/**
	 * Validate & Sanitize array of email
	 *
	 * @param string[] $emails
	 *
	 * @return array
	 */
	function sanitize_email_array( $emails ) {
		if ( ! is_array( $emails ) ) {
			return [];
		}
		$emails = array_map( 'is_email', $emails );
		$emails = array_filter( $emails );
		return array_unique( $emails );
	}
}

if ( ! function_exists( 'sanitize_comma_separated_emails' ) ) {
	/**
	 * Sanitize comma separated emails
	 *
	 * @param string $emails input data.
	 * @param bool $csv_out
	 */
	function sanitize_comma_separated_emails( $emails, $csv_out = true ) {
		$emails = explode( ',', $emails );
		$emails = sanitize_email_array( $emails );
		if ( ! $csv_out ) {
			return $emails;
		}

		return implode( ',', $emails );
	}
}

if ( ! function_exists( 'format_referral_id' ) ) {
	function format_referral_id( $id ) {
		return '0000-' . absint( $id );
	}
}
if ( ! function_exists( 'ci_parse_args' ) ) {
	/**
	 * Merge user defined arguments into defaults array.
	 *
	 * @param string|array|object $args     Value to merge with $defaults. $args
	 * @param array               $defaults Optional. Array that serves as the defaults.
	 *                                      Default empty array.
	 *
	 * @return array Merged user defined values with defaults.
	 */
	function ci_parse_args( $args, $defaults = array() ) {
		if ( is_object( $args ) ) {
			$parsed_args = get_object_vars( $args );
		} elseif ( is_array( $args ) ) {
			$parsed_args =& $args;
		} else {
			parse_str( $args, $parsed_args );
		}
		
		if ( is_array( $defaults ) && $defaults ) {
			return array_merge( $defaults, $parsed_args );
		}
		return $parsed_args;
	}
}
if ( ! function_exists( 'ci_recursive_parse_args' ) ) {
	/**
	 * @param $args
	 * @param $defaults
	 *
	 * @return array
	 */
	function ci_recursive_parse_args( $args, $defaults ) {
		$new_args = (array) $defaults;
		
		foreach ( $args as $key => $value ) {
			if ( is_array( $value ) && isset( $new_args[ $key ] ) ) {
				$new_args[ $key ] = ci_recursive_parse_args( $value, $new_args[ $key ] );
			} else {
				$new_args[ $key ] = $value;
			}
		}
		
		return $new_args;
	}
}
if ( ! function_exists( 'ci_get_user_display_name' ) ) {
	function ci_get_user_display_name( $user = null, $use_username = true, $initial = true ) {
		if ( ! $user ) {
			return '';
		}
		
		if ( is_array( $user ) ) {
			$user = (object) $user;
		}
		
		if ( $user instanceof Erp_User ) {
			$user = (object) $user->to_array();
		}
		
		$display_name = '';
		
		if ( isset( $user->last_name, $user->first_name ) && ! empty( $user->first_name ) && ! empty( $user->last_name ) ) {
			$display_name = $user->first_name . ' ' . $user->last_name;
		} else if ( isset( $user->last_name ) && ! empty( $user->last_name ) ) {
			$display_name = $user->last_name;
		} else if ( isset( $user->first_name ) && ! empty( $user->first_name ) ) {
			$display_name = $user->first_name;
		} else if ( $use_username ) {
			$display_name = $user->username;
		}
		
		if ( $initial) {
			if ( isset( $user->last_name, $user->first_name ) && ! empty( $user->last_name ) && false !== preg_match( '/[a-z]/i', $user->first_name ) ) {
				$ln = explode( ' ', $user->last_name );
				$display_name = ucfirst( $user->first_name[0] ) . '. ' . $ln[0];
			} else {
				$display_name = ucfirst( substr( $display_name, 0, 2 ) );
			}
		}
		
		return $display_name;
	}
}
if ( ! function_exists( 'ci_get_gravatar' ) ) {
	function ci_get_gravatar( $email, $gs = 150, $gd = 'mp', $gr = 'g' ) {
		$email = strtolower( trim( $email ) );
		if ( ! $email ) {
			return false;
		}
		$validTypes = [ 'mp', 'identicon', 'monsterid', 'wavatar', 'retro', 'robohash', 'blank' ];
		$gd = strtolower( $gd );
		if ( ! in_array( $gd, $validTypes ) ) {
			$gd = 'mp';
		}
		$url = 'https://www.gravatar.com/avatar/';
		$url .= md5( $email );
		$url .= "?s=$gs&d=$gd&r=$gr";
		
		return $url;
	}
}
if ( ! function_exists( 'ci_get_user_avatar' ) ) {
	function ci_get_user_avatar( $user = null, $img = true, $attrs = [], $gravater = false, $gs = 150, $gd = 'mp', $gr = 'g' ) {
		$fallback = 'assets/images/male.png';
		$srcset = [];
		if ( ! $user || ! ( is_object( $user ) || is_array( $user ) ) ) {
			$url = base_url( $fallback );
		} else {
			$avatar = '';
			if ( $user instanceof Erp_User ) {
				$user = $user->to_array();
			} else {
				$user = (array) $user;
			}
			if ( isset( $user['avatar'] ) && ! empty( $user['avatar'] ) ) {
				$avatar = 'assets/uploads/avatars/thumbs/' . $user['avatar'];
			} else if ( isset( $user['gender'] ) && ! empty( $user['gender'] ) ) {
				$avatar = 'assets/images/' . $user['gender'] . '.png';
			} else if ( isset( $user[0] ) && is_string( $user[0] ) && false !== preg_match( '/\.(png|jpg|gif|bmp|jpeg)$/i', $user[0] ) ) {
				$avatar = 'assets/uploads/avatars/thumbs/' . $user[0];
			}
			
			if ( ! empty( $avatar ) && is_readable( BASEPATH . '/' . $avatar ) ) {
				$url = base_url( $avatar );
			} else {
				if ( isset( $user['email'] ) && ! empty( $user['email'] ) && $gravater ) {
					$url = ci_get_gravatar( $user['email'], $gs, $gd, $gr );
					$srcset[] = $url;
					$srcset[] = ci_get_gravatar( $user['email'], $gs * 2, $gd, $gr ) . ' 2x';
					$srcset[] = ci_get_gravatar( $user['email'], $gs * 3, $gd, $gr ) . ' 3x';
				} else {
					$url = base_url( $fallback );
				}
			}
		}
		
		if ( $img ) {
			if ( ! empty( $srcset ) ) {
				$srcset = 'srcset="' . implode( ', ', $srcset ) . '"';
			} else {
				$srcset = '';
			}
			if ( ! isset( $attrs['alt'] ) ) {
				$attrs['alt'] = ci_get_user_display_name( (object) $user );
			}
			$url = '<img src="' . $url . '"';
			$url .= ' ' . $srcset;
			foreach ( $attrs as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}
}
if ( ! function_exists( 'ci_rand' ) ) {
	/**
	 * Generates a random number.
	 *
	 * Uses PHP7 random_int() or the random_compat library if available.
	 *
	 * @global string $rnd_value
	 *
	 * @param int $min Lower limit for the generated number
	 * @param int $max Upper limit for the generated number
	 * @return int A random number between min and max
	 */
	function ci_rand( $min = 0, $max = 0 ) {
		global $rnd_value;
		
		// Some misconfigured 32-bit environments (Entropy PHP, for example)
		// truncate integers larger than PHP_INT_MAX to PHP_INT_MAX rather than overflowing them to floats.
		$max_random_number = 3000000000 === 2147483647 ? (float) '4294967295' : 4294967295; // 4294967295 = 0xffffffff
		
		// We only handle ints, floats are truncated to their integer value.
		$min = (int) $min;
		$max = (int) $max;
		
		// Use PHP's CSPRNG, or a compatible method.
		static $use_random_int_functionality = true;
		if ( $use_random_int_functionality ) {
			try {
				$_max = ( 0 != $max ) ? $max : $max_random_number;
				// wp_rand() can accept arguments in either order, PHP cannot.
				$_max = max( $min, $_max );
				$_min = min( $min, $_max );
				$val  = random_int( $_min, $_max );
				if ( false !== $val ) {
					return absint( $val );
				} else {
					$use_random_int_functionality = false;
				}
			} catch ( Error $e ) {
				$use_random_int_functionality = false;
			} catch ( Exception $e ) {
				$use_random_int_functionality = false;
			}
		}
		
		// Reset $rnd_value after 14 uses.
		// 32 (md5) + 40 (sha1) + 40 (sha1) / 8 = 14 random numbers from $rnd_value.
		if ( strlen( $rnd_value ) < 8 ) {
			static $seed;
			
			if ( ! $seed ) {
				$seed = '';
			}
			
			$rnd_value  = md5( uniqid( microtime() . mt_rand(), true ) . $seed );
			$rnd_value .= sha1( $rnd_value );
			$rnd_value .= sha1( $rnd_value . $seed );
			$seed       = md5( $seed . $rnd_value );
		}
		
		// Take the first 8 digits for our value.
		$value = substr( $rnd_value, 0, 8 );
		
		// Strip the first eight, leaving the remainder for the next call to wp_rand().
		$rnd_value = substr( $rnd_value, 8 );
		
		$value = abs( hexdec( $value ) );
		
		// Reduce the value to be within the min - max range.
		if ( 0 != $max ) {
			$value = $min + ( $max - $min + 1 ) * $value / ( $max_random_number + 1 );
		}
		
		return abs( (int) $value );
	}
}
if ( ! function_exists( 'ci_generate_password' ) ) {
	/**
	 * Generates a random password drawn from the defined set of characters.
	 *
	 * Uses ci_rand() is used to create passwords with far less predictability
	 * than similar native PHP functions like `rand()` or `mt_rand()`.
	 *
	 * @param int  $length              Optional. The length of password to generate. Default 12.
	 * @param bool $special_chars       Optional. Whether to include standard special characters.
	 *                                  Default true.
	 * @param bool $extra_special_chars Optional. Whether to include other special characters.
	 *                                  Used when generating secret keys and salts. Default false.
	 * @return string The random password.
	 */
	function ci_generate_password( $length = 12, $special_chars = true, $extra_special_chars = false ) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		if ( $special_chars ) {
			$chars .= '!@#$%^&*()';
		}
		if ( $extra_special_chars ) {
			$chars .= '-_ []{}<>~`+=,.;:/?|';
		}
		
		$password = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$password .= substr( $chars, ci_rand( 0, strlen( $chars ) - 1 ), 1 );
		}
		
		return $password;
	}
}
if ( ! function_exists( 'ci_format_delivery_schedule' ) ) {
	/**
	 * Format Start Date and End Date for scheduled date.
	 *
	 *
	 * @param string $start_date This $start_date should be mysql datetime format. like: 'Y-m-d H:i:s'.
	 * @param string $end_date This $start_date should be mysql datetime format. like: 'Y-m-d H:i:s'.
	 *
	 *
	 * @return string the output will be like: 2020-12-31, 12:00PM to 03:00PM.
     * @throws Exception 0 value.
	 */
	function ci_format_delivery_schedule( $start_date = null, $end_date = null ) {
        if ( $start_date == null || $end_date == null ){
            return 0;
        }
		$dt1 = new DateTime($start_date);
		$dt2 = new DateTime($end_date);
		$sdt =  $dt1->format('Y-m-d');
		$edt =  $dt2->format('Y-m-d');
		$s_time = $dt1->format("h:i A");
		$e_time = $dt2->format("h:i A");

		if ($sdt === $edt) {
			return $sdt . ", " . $s_time . " to " .  $e_time;
        }
		else{
			return $sdt . ", " . $s_time . " to " . $edt. ", " . $e_time;
        }
	}
}

if ( ! function_exists( 'safeJsonEncode' ) ) {
	function safeJsonEncode( $data ) {
		/** @noinspection PhpComposerExtensionStubsInspection */
		return htmlspecialchars( json_encode( $data ), ENT_QUOTES, 'UTF-8' );
	}
}
if ( ! function_exists( 'get_add_to_cart_link' ) ) {
	function get_add_to_cart_link( $product ) {
		if ( ! $product || ! isset( $product->id ) ) {
			return false;
		}
		return site_url( 'cart/add/' . $product->id . '?qty=1' );
	}
}
if ( ! function_exists( 'get_add_to_cart_button' ) ) {
	function get_add_to_cart_button( $product, $icon = true, $buttonText = '' ) {
		$link = get_add_to_cart_link( $product );
		if ( ! $link ) {
			return false;
		}
		if ( ! $buttonText ) {
			$buttonText = lang( 'add_to_cart' );
		}
		
		$icon_tag = $icon ? '<i class="fa fa-shopping-basket" aria-hidden="true"></i>' : '';
		
		/** @noinspection HtmlUnknownTarget */
		return sprintf(
			'<a class="add-to-cart add-to-cart-btn" href="%s" data-id="%d">%s<span>%s</span></a>',
			site_url( 'cart/add/' . $product->id . '?qty=1' ),
			$product->id,
			$icon_tag,
			$buttonText
		);
		
	}
}
if ( ! function_exists( 'get_product_permalink' ) ) {
	function get_product_permalink( $product ) {
		if ( ! $product || ! isset( $product->slug ) ) {
			return false;
		}
		
		return site_url( 'product/' . $product->slug );
	}
}
if ( ! function_exists( 'get_image_url' ) ) {
	function get_image_url( $image, $thumb = false, $check = false ) {
		if ( ! $image ) {
			return false;
		}
		$path = 'assets/uploads/';
		if ( $thumb ) {
			$path = 'assets/uploads/thumbs/';
		}
		$url = base_url( $path . $image );
		if ( ! $check ) {
			return $url;
		}
		$_path = str_replace( '\\', '/', realpath( $path ) );
		
		if ( file_exists( $_path . '/' . $image ) ) {
			return $url;
		}
		return false;
	}
}
if ( ! function_exists( 'get_image_url_thumb_first' ) ) {
	function get_image_url_thumb_first( $image, $fallback = false ) {
		$_image = get_image_url( $image, true, true );
		if ( ! $_image ) {
			$_image = get_image_url( $image, false, true );
		}
		
		return $_image ? $_image : $fallback;
	}
}
if ( ! function_exists( 'get_image_html' ) ) {
	function get_image_html( $image, $alt = '', $class = '', $noImage = '' ) {
		$url = get_image_url( $image, false, ! empty( $noImage ) );
		if ( $url ) {
			$img = '<img class="' . $class . '" src="' . $url . '"';
//			if ( $srcSet ) {
//				$srcSet = get_image_url( $image, true, false );
//				if ( false !== $srcSet ) {
//					$img .= 'srcset="' . $srcSet . ' 1x ' . $url . ' 2x"';
//				}
//			}
			return $img . ' alt="' . $alt . '">';
		}
		if ( ! empty( $noImage ) ) {
			return '<img class="' . $class . '" src="' . $noImage . '" alt="' . $alt . '">';
		}
		return false;
	}
}
if ( ! function_exists( 'prepare_slide_data' ) ) {
	/**
	 *
	 * @param object $slide
	 *
	 * @return array|bool
	 */
	function prepare_slide_data( $slide ) {
		$image = isset( $slide->image ) && ! empty( $slide->image ) ? get_image_url( $slide->image, false, true ) : false;
		if ( ! $image ) {
			return false;
		}
		$_slide = [
			'image'      => $image,
			'alignment'  => 'left',
			'link'       => $slide->link,
			'title'      => $slide->title,
			'caption'    => $slide->caption,
			'subtitle'   => '',
			'button'     => '',
			'target'     => '',
			'no_content' => false,
		];
		if ( false !== strpos( $slide->link, '|' ) ) {
			$link = explode( '|', $slide->link );
			if ( 2 === count( $link ) ) {
				$_slide['link']   = $link[0];
				$_slide['button'] = $link[1];
			} elseif ( 3 === count( $link ) ) {
				$_slide['link']   = $link[0];
				$_slide['button'] = $link[1];
				$_slide['target'] = $link[2];
			} else {
				$_slide['link'] = $link[0];
			}
		}
		if ( false !== strpos( $slide->title, '|' ) ) {
			$title = explode( '|', $slide->title );
			$_slide['title'] = $title[0];
			if ( isset( $title[1] ) ) {
				$_slide['subtitle'] = $title[1];
			}
		}
		if ( false !== strpos( $slide->caption, '|' ) ) {
			$caption = explode( '|', $slide->caption );
			$_slide['caption'] = $caption[1];
			if ( count( $caption ) === 2 ) {
				$_slide['alignment'] = 'right' === $caption[0] ? 'right' : 'left';
				$_slide['caption']   = $caption[1];
			} else {
				$_slide['alignment'] = 'left';
				$_slide['caption']   = $caption[0];
			}
		}
		if ( empty( $_slide['link'] ) ) {
			$_slide['link'] = '#';
		}
		if ( '#' !== $_slide['link'] && false === strpos( $_slide['link'], 'http' ) ) {
			$_slide['link'] = site_url( $_slide['link'] );
		}
		
		// check if slider has no content.
		$_slide['no_content'] = (
			empty( $_slide['title'] )
			&&
			empty( $_slide['caption'] )
			&&
			empty( $_slide['subtitle'] )
		);
		return $_slide;
	}
}
if ( ! function_exists( 'get_single_product_classes' ) ) {
	function get_single_product_classes( $product, $class = '' ) {
		$prodClasses = 'product product-'.$product->id;
		if ( isset( $product->inCart ) ) {
			if ( ! $product->inCart ) {
				$prodClasses .= ' not-added-in-cart';
			} else {
				$prodClasses .= ' added-in-cart';
			}
		}
		if ( ! empty( $class ) ) {
			if ( is_array( $class ) ) {
				$class = implode( ' ', $class );
			}
			$prodClasses .= ' ' . trim( $class );
		}
		
		return trim( $prodClasses );
	}
}
if ( ! function_exists( 'is_serialized' ) ) {
	/**
	 * Check value to find if it was serialized.
	 *
	 * If $data is not an string, then returned value will always be false.
	 * Serialized data is always a string.
	 *
	 * @since 2.0.5
	 *
	 * @param string $data   Value to check to see if was serialized.
	 * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
	 * @return bool False if not serialized and true if it was.
	 */
	function is_serialized( $data, $strict = true ) {
		// If it isn't a string, it isn't serialized.
		if ( ! is_string( $data ) ) {
			return false;
		}
		$data = trim( $data );
		if ( 'N;' == $data ) {
			return true;
		}
		if ( strlen( $data ) < 4 ) {
			return false;
		}
		if ( ':' !== $data[1] ) {
			return false;
		}
		if ( $strict ) {
			$lastc = substr( $data, -1 );
			if ( ';' !== $lastc && '}' !== $lastc ) {
				return false;
			}
		} else {
			$semicolon = strpos( $data, ';' );
			$brace     = strpos( $data, '}' );
			// Either ; or } must exist.
			if ( false === $semicolon && false === $brace ) {
				return false;
			}
			// But neither must be in the first X characters.
			if ( false !== $semicolon && $semicolon < 3 ) {
				return false;
			}
			if ( false !== $brace && $brace < 4 ) {
				return false;
			}
		}
		$token = $data[0];
		switch ( $token ) {
			case 's':
				if ( $strict ) {
					if ( '"' !== substr( $data, -2, 1 ) ) {
						return false;
					}
				} elseif ( false === strpos( $data, '"' ) ) {
					return false;
				}
			// Or else fall through.
			case 'a':
			case 'O':
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b':
			case 'i':
			case 'd':
				$end = $strict ? '$' : '';
				return (bool) preg_match( "/^{$token}:[0-9.E+-]+;$end/", $data );
		}
		return false;
	}
}
if ( ! function_exists( 'is_serialized_string' ) ) {
	/**
	 * Check whether serialized data is of string type.
	 *
	 * @since 2.0.5
	 *
	 * @param string $data Serialized data.
	 * @return bool False if not a serialized string, true if it is.
	 */
	function is_serialized_string( $data ) {
		// if it isn't a string, it isn't a serialized string.
		if ( ! is_string( $data ) ) {
			return false;
		}
		$data = trim( $data );
		if ( strlen( $data ) < 4 ) {
			return false;
		} elseif ( ':' !== $data[1] ) {
			return false;
		} elseif ( ';' !== substr( $data, -1 ) ) {
			return false;
		} elseif ( 's' !== $data[0] ) {
			return false;
		} elseif ( '"' !== substr( $data, -2, 1 ) ) {
			return false;
		} else {
			return true;
		}
	}
}
if ( ! function_exists( 'maybe_serialize' ) ) {
	/**
	 * Serialize data, if needed.
	 *
	 * @since 2.0.5
	 *
	 * @param string|array|object $data Data that might be serialized.
	 * @return mixed A scalar data
	 */
	function maybe_serialize( $data ) {
		if ( is_array( $data ) || is_object( $data ) ) {
			return serialize( $data );
		}
		
		/*
		 * Double serialization is required for backward compatibility.
		 * See https://core.trac.wordpress.org/ticket/12930
		 * Also the world will end. See WP 3.6.1.
		 */
		if ( is_serialized( $data, false ) ) {
			return serialize( $data );
		}
		
		return $data;
	}
}
if ( ! function_exists( 'maybe_unserialize' ) ) {
	/**
	 * Unserialize value only if it was serialized.
	 *
	 * @since 2.0.0
	 *
	 * @param string $original Maybe unserialized original, if is needed.
	 * @return mixed Unserialized data can be any type.
	 */
	function maybe_unserialize( $original ) {
		if ( is_serialized( $original ) ) { // Don't attempt to unserialize data that wasn't serialized going in.
			return @unserialize( $original );
		}
		return $original;
	}
}
if ( ! function_exists( '_is_writable' ) ) {
	/**
	 * Determine if a directory is writable.
	 *
	 * This function is used to work around certain ACL issues in PHP primarily
	 * affecting Windows Servers.
	 *
	 * @since 3.6.0
	 *
	 * @see win_is_writable()
	 *
	 * @param string $path Path to check for write-ability.
	 * @return bool Whether the path is writable.
	 */
	function _is_writable( $path ) {
		if ( 'WIN' === strtoupper( substr( PHP_OS, 0, 3 ) ) ) {
			return win_is_writable( $path );
		} else {
			return @is_writable( $path );
		}
	}
}
if ( ! function_exists( 'win_is_writable' ) ) {
	/**
	 * Workaround for Windows bug in is_writable() function
	 *
	 * PHP has issues with Windows ACL's for determine if a
	 * directory is writable or not, this works around them by
	 * checking the ability to open files rather than relying
	 * upon PHP to interprate the OS ACL.
	 *
	 * @since 2.8.0
	 *
	 * @see https://bugs.php.net/bug.php?id=27609
	 * @see https://bugs.php.net/bug.php?id=30931
	 *
	 * @param string $path Windows path to check for write-ability.
	 * @return bool Whether the path is writable.
	 */
	function win_is_writable( $path ) {
		if ( '/' === $path[ strlen( $path ) - 1 ] ) {
			// If it looks like a directory, check a random file within the directory.
			return win_is_writable( $path . uniqid( mt_rand() ) . '.tmp' );
		} elseif ( is_dir( $path ) ) {
			// If it's a directory (and not a file), check a random file within the directory.
			return win_is_writable( $path . '/' . uniqid( mt_rand() ) . '.tmp' );
		}
		
		// Check tmp file for read/write capabilities.
		$should_delete_tmp_file = ! file_exists( $path );
		
		$f = @fopen( $path, 'a' );
		if ( false === $f ) {
			return false;
		}
		fclose( $f );
		
		if ( $should_delete_tmp_file ) {
			unlink( $path );
		}
		
		return true;
	}
}
if ( ! function_exists( 'ci_array_values_multi' ) ) {
	function ci_array_values_multi( $array ) {
		$is_numeric = false;
		foreach ( $array as $k => $val ) {
			if ( is_array( $val ) ) {
				$array[ $k ] = ci_array_values_multi( $val );
				$is_numeric = true === is_numeric( $k );
			}
		}
		
		if ( $is_numeric ) {
			return array_values( $array );
		}
		return $array;
	}
}
if ( ! function_exists( 'is_countable' ) ) {
	function is_countable( $var ) {
		return ( is_array( $var ) || $var instanceof Countable );
	}
}

if ( ! function_exists( 'render_custom_fields' ) ) {
	function render_custom_fields( $fields, $values, $groupId = '' ) {
		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return;
		}
		foreach( $fields as $field ) {
			$field = ci_parse_args( $field, [ 'id' =>  '', 'label' => '', 'icon' => '', 'type' => '', 'fields' => [], 'desc' => '', 'repeat' => false ] );
			if ( empty( $field['type'] ) || empty( $field['id'] ) ) {
				continue;
			}
			$id = xss_clean( $field['id'] );
			$name = $id;
			if ( $groupId ) {
				$name = $groupId. '[' . $id . ']';
			}
			
			$label = $field['label'];
			if ( ! empty( $field['icon'] ) ) {
				$label = '<i class="' . $field['icon'] . '"></i> ' . $label;
			}
			?>
			<div class="col-md-12">
			<?php
			if ( 'group' === $field['type'] && is_array( $field['fields'] ) && ! empty( $field['fields'] ) ) {
				$_values = isset( $values[ $id ] ) ? $values[ $id ] : [ [] ];
				$_idx = 0;
				?>
				<div class="row field form-group">
					<div class="col-md-2 field-label">
						<h2><?= $label; ?></h2>
						<?php if ( ! empty( $field['desc'] ) ) { ?>
						<code style="margin-top: 3px;display: inline-block;" class="description"><?= $field['desc']; ?></code>
						<?php } ?>
					</div>
					<div class="col-md-10 group-field">
						<div class="group-items">
							<?php if ( ! $field['repeat'] ) {
								render_custom_fields( $field['fields'], $_values, $name );
							} else {
								foreach ( $_values as $idx => $val ) {
									?>
									<div class="group-item">
										<div class="group-header">
											<h3 class="item-label">
												<span><?= sprintf( 'Item %d', ( $idx + 1 ) ) ?></span>
												<a href="#" class="remove-item btn btn-warning btn-sm pull-right">
													<i class="fa fa-trash-o"></i>
													<span class="sr-only"><?= lang( 'remove_item' ); ?></span>
												</a>
											</h3>
										</div>
										<div class="group-fields"><?php render_custom_fields( $field['fields'], $val, $name . '[' . $idx . ']' ); ?></div>
									</div>
									<?php
									$_idx = $idx;
								}
							} ?>
						</div>
						<?php if( $field['repeat'] ) { ?>
						<div class="group-bottom">
							<script type="text/template" data-idx="<?= $_idx+1; ?>">
								<div class="group-item">
									<div class="group-header">
										<h3 class="item-label">
											<span>Item 0</span>
											<a href="#" class="remove-item btn btn-warning btn-sm pull-right">
												<i class="fa fa-trash-o"></i>
												<span class="sr-only"><?= lang( 'remove_item' ); ?></span>
											</a>
										</h3>
									</div>
									<div class="group-fields"><?php render_custom_fields( $field['fields'], [], $name . '[__IDX__]' ); ?></div>
								</div>
							</script>
							<a href="#" class="add-item btn btn-info btn-sm pull-right">
								<i class="fa fa-plus"></i>
								<span><?= lang( 'add_item' ); ?></span>
							</a>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="clearfix"></div>
			<?php } else { ?>
				<div class="row field form-group">
					<div class="col-md-2 field-label">
						<label for="<?= str_replace( [ '[', ']' ], '-', $name ); ?>"><?= $label; ?></label>
					</div>
					<div class="col-md-10"><?php ci_generate_field( $field, $name, isset( $values[ $id ] ) ? $values[ $id ] : '' ); ?></div>
					<div class="clearfix"></div>
					<div class="col-md-2 hidden-xs"></div>
					<div class="col-md-10">
						<?php if ( ! empty( $field['desc'] ) ) { ?>
							<code style="margin-top: 3px;display: inline-block;" class="description"><?= $field['desc']; ?></code>
						<?php } ?>
					</div>
				</div>
				<div class="clearfix"></div>
			<?php } ?>
			</div>
			<div class="clearfix"></div>
			<?php
		}
	}
}

if ( ! function_exists( 'ci_generate_field' ) ) {
	function ci_generate_field( $field, $name, $value = '' ) {
		$field = ci_parse_args( $field, [ 'type' => '', 'default' => '', 'options' => '', 'attributes' => [], 'desc' => '' ] );
		if ( ! is_array( $field['attributes'] ) ) {
			$field['attributes'] = [];
		}
		$classes = isset( $field['attributes']['class'] ) ? $field['attributes']['class'] : '';
		if ( is_array( $classes ) ) {
			$classes = implode( ' ', $classes );
		}
		
		$classes .= ' form-control';
		$field['attributes']['class'] = $classes;
		$field['attributes']['id'] = str_replace( [ '[', ']' ], '-', $name );
		$field['attributes']['type'] = $field['type'];
		$field['attributes']['name'] = $name;
		switch( $field['type'] ) {
			case 'text':
			case 'url':
			case 'number':
			case 'hidden':
				echo form_input( $field['attributes'], $value );
				break;
			case 'editor':
				echo form_textarea( $field['attributes'], $value );
				break;
			case 'textarea':
				$field['attributes']['class'] .= ' skip';
				echo form_textarea( $field['attributes'], $value );
				break;
			default:
				break;
		}
	}
}

if ( ! function_exists( 'render_settings_segments_sections' ) ) {
	function render_settings_segments_sections( $sections, $page, $segmentKey, $values = [] ) {
		foreach ( $sections as $k => $section ) {
			$section = ci_parse_args( $section,
				[
					'label' => '',
					'icon'  => '',
					'desc'  => '',
					'type'  => '',
				] );
			if ( ! $section['type'] ) continue;
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="box segment-sections segments-wrap segments-sections-wrap <?= $segmentKey; ?>-section-<?= $k; ?>">
					<div class="box-header has-collapse" data-toggle="collapse" id="settings-<?= $segmentKey; ?>-section-<?= $k; ?>-heading" data-parent="#theme-settings-section-<?= $k; ?>" href="#settings-<?= $segmentKey; ?>-section-<?= $k; ?>-collapse" aria-expanded="true" aria-controls="settings-<?= $segmentKey; ?>-section-<?= $k; ?>-collapse">
						<h2 class="blue"><?= $section['icon'] ? '<i class="'.$section['icon'].'"></i>' : ''; ?><?= $section['label']; ?></h2>
						<i class="fa-fw fa fa-compress"></i>
						<i class="fa-fw fa fa-expand"></i>
					</div>
					<div class="box-content collapse in" id="settings-<?= $segmentKey; ?>-section-<?= $k; ?>-collapse" role="tabpanel" aria-labelledby="settings-<?= $segmentKey; ?>-section-<?= $k; ?>-heading">
						<div class="row">
							<div class="col-md-12">
								<?php if ( ! empty( $section['desc'] ) ) { ?>
									<p class="introtext"><?= $section['desc']; ?></p>
								<?php } ?>
								<div class="row rendered-section static-segment">
								<?php
									$key = sprintf( '%s[%s][%s]', $page, $segmentKey, $k );
									$fn = 'render_theme_settings_' . $section['type'];
									$value = isset( $values[ $k ] ) ? $values[ $k ] : [];
									if ( is_callable( $fn ) ) {
										call_user_func( $fn, $key, $value );
									}
								?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		}
	}
}
if ( ! function_exists( 'render_settings_segments_supports' ) ) {
	function render_settings_segments_supports( $supports, $page, $segmentKey, $values = [] ) {
		?>
		<div class="row">
			<div class="col-md-8"></div>
			<div class="col-md-4">
				<?php
				echo '<select class="addSection form-control skip" style="width: 100%;">';
				echo '<option value="" selected readonly>Add Item</option>';
				foreach ( $supports as $item ) {
					printf(
						'<option value="%s" data-key="%s">%s</option>',
						$item,
						sprintf( '%s[%s][__IDX__]', $page, $segmentKey ),
						ucwords( str_replace( [ '_' ], ' ', $item ) )
					);
				}
				echo '</select>';
				?>
			</div>
		</div>
		<div class="row rendered-section" style="margin-top: 15px">
			<?php
			if ( ! empty( $values ) ) {
				foreach ( $values as $idx => $item ) {
					if ( ! is_array( $item ) || ! isset( $item['type'] ) ) continue;
					$key = sprintf( '%s[%s][%s]', $page, $segmentKey, $idx );
					$fn = 'render_theme_settings_' . $item['type'];
					if ( is_callable( $fn ) ) {
						call_user_func( $fn, $key, $item );
					}
				}
			}
			?>
		</div>
		<?php
	}
}
if ( ! function_exists( 'render_settings_segments_widgets' ) ) {
	function render_settings_segments_widgets( $widgets, $page, $segmentKey, $values = [] ) {
		foreach( $widgets as $wid => $widget ) {
			$widget = ci_parse_args( $widget,
				[
					'label'    => '',
					'icon'     => '',
					'desc'     => '',
					'supports' => [],
					'sections' => [],
					'max_section' => 0,
				] );
			if ( empty( $widget['label'] ) || empty( $widget['supports'] ) || ! is_array( $widget['supports'] ) ) {
				continue;
			}
			$widget_values = isset( $values[ $wid ] ) ? $values[ $wid ] : [];
			?>
			<div class="row">
				<div class="col-md-12">
					<div class="box segment-widgets segments-wrap <?= $segmentKey; ?>-widget-<?= $wid; ?>" data-max_item="<?= $widget['max_section']; ?>" data-widget="<?= $wid; ?>" data-item_count="<?= count( $widget_values ); ?>" data-idx="<?= count( $widget_values ); ?>">
						<div class="box-header has-collapse" data-toggle="collapse" id="settings-<?= $segmentKey; ?>-widget-<?= $wid; ?>-heading" data-parent="#theme-settings-widget-<?= $wid; ?>" href="#settings-<?= $segmentKey; ?>-widget-<?= $wid; ?>-collapse" aria-expanded="true" aria-controls="settings-<?= $segmentKey; ?>-widget-<?= $wid; ?>-collapse">
							<h2 class="blue"><?= $widget['icon'] ? '<i class="'.$widget['icon'].'"></i>' : ''; ?><?= $widget['label']; ?></h2>
							<i class="fa-fw fa fa-compress"></i>
							<i class="fa-fw fa fa-expand"></i>
						</div>
						<div class="box-content collapse in" id="settings-<?= $segmentKey; ?>-widget-<?= $wid; ?>-collapse" role="tabpanel" aria-labelledby="settings-<?= $segmentKey; ?>-widget-<?= $wid; ?>-heading">
							<div class="row">
								<div class="col-md-12">
									<?php if ( ! empty( $widget['desc'] ) ) { ?>
										<p class="introtext"><?= $widget['desc']; ?></p>
									<?php } ?>
									<div class="row">
										<div class="col-md-8"></div>
										<div class="col-md-4">
											<?php
											echo '<select class="addSection form-control skip" style="width: 100%;">';
											echo '<option value="" selected readonly>Add Item</option>';
											foreach ( $widget['supports'] as $item ) {
												printf(
													'<option value="%s" data-key="%s">%s</option>',
													$item,
													sprintf( '%s[%s][widgets][__WID__][__IDX__]', $page, $segmentKey ),
													ucwords( str_replace( [ '_' ], ' ', $item ) )
												);
											}
											echo '</select>';
											?>
										</div>
									</div>
									<div class="row rendered-section" style="margin-top: 15px">
										<?php
										
										if ( ! empty( $widget_values ) ) {
											foreach ( $widget_values as $idx => $item ) {
												if ( ! is_array( $item ) || ! isset( $item['type'] ) ) continue;
												$key = sprintf( '%s[%s][widgets][%s][%s]', $page, $segmentKey, $wid, $idx );
												$fn = 'render_theme_settings_' . $item['type'];
												if ( is_callable( $fn ) ) {
													call_user_func( $fn, $key, $item );
												}
											}
										}
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'render_theme_settings_url_list' ) ) {
	function render_theme_settings_url_list( $name, $value, $useDefault = false ) {
		$uid = uniqid( 'ci_', true );
		$list = isset( $value['list'] ) ? $value['list'] : [];
		if ( empty( $list ) && $useDefault ) {
			$list = [ [ 'label' => '', 'url' => '', 'target' => '' ] ];
		}
		?>
		<div class="col-md-12 settings-url-list setting-group">
			<input type="hidden" name="<?= $name; ?>[type]" value="url_list">
			<div class="form-group segment-label">
				<a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
				<h3>URL List</h3>
			</div>
			<div class="form-group">
				<?php render_theme_settings_label_fields( $name, $value, lang( 'important_links' ) ); ?>
				<div class="row">
					<div class="col-md-12 list-items" data-item_count="<?= count( $list ); ?>" data-idx="<?= count( $list ); ?>">
						<?php foreach ( $list as $idx => $item ) {
							if ( ! isset( $item['label'] ) || ! isset( $item['url'] ) ) {
								continue;
							}
							$target = isset( $item['target'] ) ? $item['target'] : '';
						?>
						<div class="row list-item">
							<div class="col-md-4">
								<label for="<?= $uid . $idx; ?>-list-label">Label</label>
								<input type="text" class="form-control" id="<?= $uid . $idx; ?>-list-label" name="<?= $name; ?>[list][<?= $idx; ?>][label]" value="<?= $item['label']; ?>">
							</div>
							<div class="col-md-4">
								<label for="<?= $uid . $idx; ?>-list-url">Url</label>
								<input type="url" class="form-control" id="<?= $uid . $idx; ?>-list-url" name="<?= $name; ?>[list][<?= $idx; ?>][url]" value="<?= $item['url']; ?>">
							</div>
							<div class="col-md-2">
								<label for="<?= $uid . $idx; ?>-list-target">Target</label>
								<select class="form-control" name="<?= $name; ?>[list][<?= $idx; ?>][target]" id="<?= $uid . $idx; ?>-list-target">
									<option value=""<?php selected( $target, '' ); ?>>Current Tab</option>
									<option value="_blank"<?php selected( $target, '_blank' ); ?>>New Tab</option>
								</select>
							</div>
							<div class="col-md-2">
								<a href="#" class="btn btn-danger btn-sm remove-url-list tip" style="margin-top: 30px;" title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
							</div>
						</div>
						<?php } ?>
					</div>
					<div class="form-group text-right">
						<script type="text/template">
							<div class="row list-item">
								<div class="col-md-4">
									<label for="<?= $uid; ?>__IDX__-list-label">Label</label>
									<input type="text" class="form-control" id="<?= $uid; ?>__IDX__-list-label" name="<?= $name; ?>[list][__IDX__][label]" value="">
								</div>
								<div class="col-md-4">
									<label for="<?= $uid; ?>__IDX__-list-url">Url</label>
									<input type="url" class="form-control" id="<?= $uid; ?>__IDX__-list-url" name="<?= $name; ?>[list][__IDX__][url]" value="">
								</div>
								<div class="col-md-2">
									<label for="<?= $uid; ?>__IDX__-list-target">Target</label>
									<select class="form-control" name="<?= $name; ?>[list][__IDX__][target]" id="<?= $uid; ?>__IDX__-list-target">
										<option value="">Current Tab</option>
										<option value="_blank">New Tab</option>
									</select>
								</div>
								<div class="col-md-2">
									<a href="#" class="btn btn-danger btn-sm remove-url-list tip" style="margin-top: 30px;" title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
								</div>
							</div>
						</script>
						<a href="#" class="btn btn-link addLink"><i class="fa-fw fa fa-plus"></i> Add New Link</a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
if ( ! function_exists( 'render_theme_settings_daily_deals' ) ) {
	function render_theme_settings_daily_deals( $name, $value ) {
		$uid = uniqid( 'ci_', true );
		$max = isset( $value['max'] ) ? $value['max'] : 16;
		?>
		<div class="col-md-12 settings-daily-deals setting-group">
			<input type="hidden" name="<?= $name; ?>[type]" value="daily_deals">
			<div class="form-group segment-label">
				<a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
				<h3>Daily Deals</h3>
			</div>
			<div class="form-group">
				<?php render_theme_settings_label_fields( $name, $value, lang( 'daily_deals' ) ); ?>
				<div class="row">
					<div class="col-md-12">
						<label for="<?= $uid; ?>-max">Max Items</label>
						<input class="form-control" id="<?= $uid; ?>-max" min="0" step="1" type="number" name="<?= $name; ?>[max]" placeholder="Max Item" value="<?= $max; ?>">
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
if ( ! function_exists( 'render_theme_settings_products' ) ) {
	function render_theme_settings_products( $name, $value ) {
		$uid = uniqid( 'ci_', true );
		$max = isset( $value['max'] ) ? $value['max'] : 16;
		$ids = isset( $value['ids'] ) ? $value['ids'] : '';
		$subtitle = isset( $value['subtitle'] ) ? $value['subtitle'] : '';
		?>
		<div class="col-md-12 settings-products setting-group">
			<input type="hidden" name="<?= $name; ?>[type]" value="products">
			<div class="form-group segment-label">
				<a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
				<h3>Products</h3>
			</div>
			<div class="form-group">
				<?php render_theme_settings_label_fields( $name, $value, lang( 'top_products' ) ); ?>
                <div class="row">
                    <div class="col-md-12">
                        <label for="<?= $uid; ?>-subtitle">Section Subtitle</label>
                        <input class="form-control" id="<?= $uid; ?>-subtitle" type="text" name="<?= $name; ?>[subtitle]" placeholder="Section Subtitle" value="<?= $subtitle; ?>">
                    </div>
                </div>
				<div class="row">
					<div class="col-md-6">
						<label for="<?= $uid; ?>-ids">Product Ids</label>
						<input class="form-control" id="<?= $uid; ?>-ids" type="text" name="<?= $name; ?>[ids]" placeholder="Product Ids" value="<?= $ids; ?>">
					</div>
					<div class="col-md-6">
						<label for="<?= $uid; ?>-max">Max Items</label>
						<input class="form-control" id="<?= $uid; ?>-max" min="0" step="1" type="number" name="<?= $name; ?>[max]" placeholder="Max Item" value="<?= $max; ?>">
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
if ( ! function_exists( 'render_theme_settings_custom' ) ) {
	function render_theme_settings_custom( $name, $value ) {
		render_theme_settings_code_edit( $name, $value, 'html', true, lang( 'Custom Content' ) );
	}
}
if ( ! function_exists( 'render_theme_settings_categories' ) ) {
	function render_theme_settings_categories( $name, $value ) {
		$uid = uniqid( 'ci_', true );
		$max = isset( $value['max'] ) ? $value['max'] : 16;
		$ids = isset( $value['ids'] ) ? $value['ids'] : '';
		$show = isset( $value['show_sub'] ) ? $value['show_sub'] : '';
		?>
		<div class="col-md-12 settings-categories setting-group">
			<input type="hidden" name="<?= $name; ?>[type]" value="categories">
			<div class="form-group segment-label">
				<a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
				<h3>Categories</h3>
			</div>
			<div class="form-group">
				<?php render_theme_settings_label_fields( $name, $value, lang( 'top_category' ) ); ?>
				<div class="row">
					<div class="col-md-5">
						<label for="<?= $uid; ?>-ids">Category Id</label>
						<input class="form-control" id="<?= $uid; ?>-ids" min="0" step="1" type="number" name="<?= $name; ?>[ids]" placeholder="Category Id" value="<?= $ids; ?>">
					</div>
					<div class="col-md-5">
						<label for="<?= $uid; ?>-max">Max Products</label>
						<input class="form-control" id="<?= $uid; ?>-max" min="0" step="1" type="number" name="<?= $name; ?>[max]" placeholder="Max Products" value="<?= $max; ?>">
					</div>
					<div class="col-md-2">
						<label for="<?= $uid; ?>-show_sub">Show Sub Categories</label>
						<select class="form-control" id="<?= $uid; ?>-show_sub" name="<?= $name; ?>[show_sub]">
							<option value="show"<?php selected( $show, 'show' ); ?>>Show</option>
							<option value="hide"<?php selected( $show, 'hide' ); ?>>Hide</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
if ( ! function_exists( 'render_theme_settings_new_products' ) ) {
	function render_theme_settings_new_products( $name, $value ) {
		$uid = uniqid( 'ci_', true );
		$max = isset( $value['max'] ) ? $value['max'] : 16;
		?>
		<div class="col-md-12 settings-new-products setting-group">
			<input type="hidden" name="<?= $name; ?>[type]" value="new_products">
			<div class="form-group segment-label">
				<a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
				<h3>New Products</h3>
			</div>
			<div class="form-group">
				<?php render_theme_settings_label_fields( $name, $value, lang( 'new_products' ) ); ?>
				<div class="row">
					<div class="col-md-12">
						<label for="<?= $uid; ?>-max">Max Items</label>
						<input class="form-control" id="<?= $uid; ?>-max" min="0" step="1" type="number" name="<?= $name; ?>[max]" placeholder="Max Item" value="<?= $max; ?>">
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
if ( ! function_exists( 'render_theme_settings_most_viewed' ) ) {
	function render_theme_settings_most_viewed( $name, $value ) {
		$uid = uniqid( 'ci_', true );
		$max = isset( $value['max'] ) ? $value['max'] : 16;
		?>
		<div class="col-md-12 settings-most-views setting-group">
			<input type="hidden" name="<?= $name; ?>[type]" value="most_viewed">
			<div class="form-group segment-label">
				<a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
				<h3>Most Viewed Products</h3>
			</div>
			<div class="form-group">
				<?php render_theme_settings_label_fields( $name, $value, lang( 'most_viewed' ) ); ?>
				<div class="row">
					<div class="col-md-12">
						<label for="<?= $uid; ?>-max">Max Items</label>
						<input class="form-control" id="<?= $uid; ?>-max" min="0" step="1" type="number" name="<?= $name; ?>[max]" placeholder="Max Item" value="<?= $max; ?>">
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
if ( ! function_exists( 'render_theme_settings_trending_products' ) ) {
	function render_theme_settings_trending_products( $name, $value ) {
		$uid = uniqid( 'ci_', true );
		$max = isset( $value['max'] ) ? $value['max'] : 16;
		$subtitle = isset( $value['subtitle'] ) ? $value['subtitle'] : '';
		?>
		<div class="col-md-12 settings-trending-products setting-group">
			<input type="hidden" name="<?= $name; ?>[type]" value="trending_products">
			<div class="form-group segment-label">
				<a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
				<h3>Tranding Products</h3>
			</div>
			<div class="form-group">
				<?php render_theme_settings_label_fields( $name, $value, lang( 'trending_products' ) ); ?>
                <div class="row">
                    <div class="col-md-12">
                        <label for="<?= $uid; ?>-subtitle">Section Subtitle</label>
                        <input class="form-control" id="<?= $uid; ?>-subtitle" type="text" name="<?= $name; ?>[subtitle]" placeholder="Section Subtitle" value="<?= $subtitle; ?>">
                    </div>
                </div>
				<div class="row">
					<div class="col-md-12">
						<label for="<?= $uid; ?>-max">Max Items</label>
						<input class="form-control" id="<?= $uid; ?>-max" min="0" step="1" type="number" name="<?= $name; ?>[max]" placeholder="Max Item" value="<?= $max; ?>">
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
if ( ! function_exists( 'render_theme_settings_featured_products' ) ) {
	function render_theme_settings_featured_products( $name, $value ) {
		$uid = uniqid( 'ci_', true );
		$max = isset( $value['max'] ) ? $value['max'] : 16;
		$promo = isset( $value['promo'] ) ? $value['promo'] : '';
		?>
		<div class="col-md-12 settings-featured-products setting-group">
			<input type="hidden" name="<?= $name; ?>[type]" value="featured_products">
			<div class="form-group segment-label">
				<a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
				<h3>Featured Products</h3>
			</div>
			<div class="form-group">
				<?php render_theme_settings_label_fields( $name, $value, lang( 'featured_products' ) ); ?>
				<div class="row">
					<div class="col-md-6">
						<label for="<?= $uid; ?>-max">Max Items</label>
						<input class="form-control" id="<?= $uid; ?>-max" min="0" step="1" type="number" name="<?= $name; ?>[max]" placeholder="Max Item" value="<?= $max; ?>">
					</div>
					<div class="col-md-6">
						<label for="<?= $uid; ?>-promo">Show Promo</label>
						<select class="form-control" name="<?= $name; ?>[promo]" id="<?= $uid; ?>-promo">
							<option value="show"<?php selected( $promo, 'show' ); ?>>Show</option>
							<option value="hide"<?php selected( $promo, 'hide' ); ?>>Hide</option>
						</select>
						<!-- /# -->
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
if ( ! function_exists( 'render_theme_settings_slider' ) ) {
    function render_theme_settings_slider( $name, $value ) {
        $uid = uniqid( 'ci_', true );
        $value = isset( $value['visibility'] ) ? $value['visibility'] : '';
        ?>
        <div class="col-md-12 settings-slider setting-group">
            <input type="hidden" name="<?= $name; ?>[type]" value="slider">
            <div class="form-group segment-label">
                <a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
                <h3>Main Slider</h3>
            </div>
            <div class="form-group">
                <label for="<?= $uid; ?>">Visibility</label>
                <select class="form-control" id="<?= $uid; ?>" name="<?= $name; ?>[visibility]">
                    <option value="show"<?php selected( $value, 'show' ); ?>><?= lang( 'Show' ); ?></option>
                    <option value="hide"<?php selected( $value, 'hide' ); ?>><?= lang( 'Hide' ); ?></option>
                </select>
            </div>
        </div>
        <?php
    }
}
if ( ! function_exists( 'render_theme_settings_text' ) ) {
    function render_theme_settings_text( $name, $value ) {
        $uid = uniqid( 'ci_', true );
        $value = isset( $value['content'] ) ? $value['content'] : '';
        ?>
        <div class="col-md-12 settings-slider setting-group">
            <input type="hidden" name="<?= $name; ?>[type]" value="text">
            <div class="form-group segment-label">
                <a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
                <h3>Text</h3>
            </div>
            <div class="form-group">
                <label for="<?= $uid; ?>">Content</label>
                <input type="text" class="form-control" id="<?= $uid; ?>" name="<?= $name; ?>[content]" value="<?= $value ?>">
            </div>
        </div>
        <?php
    }
}
if ( ! function_exists( 'render_theme_settings_brand_slider' ) ) {
	function render_theme_settings_brand_slider( $name, $value ) {
		$uid = uniqid( 'ci_', true );
		$visibility = isset( $value['visibility'] ) ? $value['visibility'] : '';
		$subtitle = isset( $value['subtitle'] ) ? $value['subtitle'] : '';
		?>
		<div class="col-md-12 settings-brand-slider setting-group">
			<input type="hidden" name="<?= $name; ?>[type]" value="brand_slider">
			<input type="hidden" name="<?= $name; ?>[visibility]" value="show">
			<div class="form-group segment-label">
				<a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
				<h3>Brand Slider</h3>
			</div>
            <div class="form-group">
	            <?php render_theme_settings_label_fields( $name, $value, lang( 'brand_slider' ) ); ?>
                <div class="row">
                    <div class="col-md-12">
                        <label for="<?= $uid; ?>-subtitle">Section Subtitle</label>
                        <input class="form-control" id="<?= $uid; ?>-subtitle" type="text" name="<?= $name; ?>[subtitle]" placeholder="Section Subtitle" value="<?= $subtitle; ?>">
                    </div>
                </div>
            </div>
			<?php /*<div class="form-group">
				<label for="<?= $uid; ?>">Max Brand To Show</label>
				<input class="form-control" id="<?= $uid; ?>" min="0" step="1" type="number" name="<?= $name; ?>[max]" placeholder="Max Item" value="<?= $max; ?>">
			</div>*/ ?>
		</div>
		<?php
	}
}
if ( ! function_exists( 'render_theme_settings_mailchimp' ) ) {
	function render_theme_settings_mailchimp( $name, $value ) {
		$uid = uniqid( 'ci_', true );
		$value = isset( $value['visibility'] ) ? $value['visibility'] : '';
		?>
		<div class="col-md-12 settings-brand-slider setting-group">
			<input type="hidden" name="<?= $name; ?>[type]" value="mailchimp">
			<input type="hidden" name="<?= $name; ?>[visibility]" value="show">
			<div class="form-group segment-label">
				<a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
				<h3>MailChimp Subscription Form</h3>
			</div>
		</div>
		<?php
	}
}
if ( ! function_exists( 'render_theme_settings_copyright' ) ) {
	function render_theme_settings_copyright( $name, $value ) {
		render_theme_settings_code_edit( $name, $value, 'html', false, '', 'copyright', lang( 'copyright' ) );
	}
}
if ( ! function_exists( 'render_theme_settings_code_edit' ) ) {
	function render_theme_settings_code_edit( $name, $value, $type = 'html', $show_label_fields = false, $labelValue = '', $settings_type = 'custom', $editor_label = '' ) {
		$uid = uniqid( 'ci_', true );
//		$content = isset( $value['content'] ) ? htmlentities( $value['content'] ) : '';
		$content = isset( $value['content'] ) ? $value['content'] : '';
		if ( ! $editor_label ) {
			$editor_label = lang( 'content' );
		}
		?>
		<div class="col-md-12 settings-code-edit setting-group">
			<input type="hidden" name="<?= $name; ?>[type]" value="<?= $settings_type; ?>">
			<div class="form-group segment-label">
				<a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
				<h3>Custom Content</h3>
			</div>
			<div class="form-group">
				<?php
				if ( $show_label_fields ) {
					render_theme_settings_label_fields( $name, $value, $labelValue );
				}
				?>
				<div class="row">
					<div class="col-md-12">
						<label for="<?= $uid; ?>_content"><?= $editor_label; ?></label>
					</div>
					<div class="col-md-12">
						<div class="code-edit not-init" data-type="<?= $type; ?>">
							<textarea id="<?= $uid; ?>_content" name="<?= $name; ?>[content]" class="form-control skip store"><?= $content; ?></textarea>
							<div class="stage" style="width: 100%;height: 250px;display: none;padding: 10px 0;background: #1e1e1e;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
if ( ! function_exists( 'render_theme_settings_label_fields' ) ) {
	function render_theme_settings_label_fields( $name, $value, $defaultValue = '', $defaultVisibility = 'show' ) {
		$uid = uniqid( 'ci_', true );
		$label = isset( $value['label']['value'] ) ? $value['label']['value'] : $defaultValue;
		$visibility = isset( $value['label']['visibility'] ) ? $value['label']['visibility'] : $defaultVisibility;
		?>
		<div class="row common-label">
			<div class="col-md-12">
				<label for="<?= $uid; ?>_label">Label</label>
			</div>
			<div class="col-md-10">
				<input type="text" id="<?= $uid; ?>_label" name="<?= $name; ?>[label][value]" class="form-control tip" placeholder="<?= lang( 'label'); ?>" value="<?= $label; ?>">
			</div>
			<div class="col-md-2">
				<select class="form-control" name="<?= $name; ?>[label][visibility]" style="width: 100%;">
					<option value="show" <?php selected( $visibility, 'show' ); ?>>Show</option>
					<option value="hide" <?php selected( $visibility, 'hide' ); ?>>Hide</option>
				</select>
			</div>
		</div>
		<?php
	}
}
if ( ! function_exists( 'render_theme_settings_selectpdf' ) ) {
	function render_theme_settings_selectpdf( $name, $value ) {
		$uid = uniqid( 'ci_', true );
		$value = isset( $value['value'] ) ? $value['value'] : '';
		?>
        <div class="col-md-12 settings-pdf-type setting-group">
            <input type="hidden" name="<?= $name; ?>[type]" value="selectpdf">
            <div class="form-group segment-label">
                <a href="#" class="btn btn-danger btn-sm remove-segment tip" title="" data-original-title="Remove Item"><i class="fa-fw fa fa-trash-o"></i></a>
                <h3>PDF Template</h3>
            </div>
            <div class="form-group">
                <label for="<?= $uid; ?>">Select PDF Template</label>
                <select class="form-control" id="<?= $uid; ?>" name="<?= $name; ?>[value]">
                    <option value="default"<?php selected( $value, 'default' ); ?>>Default</option>
                    <option value="minimal"<?php selected( $value, 'minimal' ); ?>>Minimal</option>
                </select>
            </div>
        </div>
		<?php
	}
}

if ( ! function_exists( 'unique_id' ) ) {
	/**
	 * Get Unique ID With Prefix.
	 * @param string $prefix
	 * @param string $sep
	 *
	 * @return string
	 */
	function unique_id( $prefix = '', $sep = '-' ) {
		static $count;
		if ( ! $count ) {
			$count = 1;
		}
		$id = $prefix . $sep . $count;
		$count++;
		
		return $id;
	}
}

// bootstrap helper.
if ( ! function_exists( 'bs_alert' ) ) {
	/**
	 * Render Bootstrap Alert Div.
	 * @param string $message
	 * @param string $type
	 * @param bool   $is_dismissible
	 * @param bool   $echo
	 *
	 * @return string|void
	 */
	function bs_alert( $message = '', $type = 'info', $is_dismissible = true, $echo = true ) {
		if ( ! $echo ) {
			ob_start();
		}
		$type = strtolower( $type );
		if ( ! in_array( $type, [ 'success', 'info', 'danger', 'warning' ] ) ) {
			$type = 'info';
		}
		
		$id = unique_id( $type );
		
		?>
		<div class="alert alert-<?= $type; ?>" role="alert" id="<?= $id; ?>">
			<?php if ( $is_dismissible ) { ?>
			<button data-dismiss="alert" class="close" type="button" aria-controls="<?= $id; ?>" aria-label="<?= lang( 'close' ); ?>">&times;</button>
			<?php } ?>
			<div class="alert-body"><?= $message; ?></div>
		</div>
		<?php
		
		if ( ! $echo ) {
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'detect_uesr_gender_by_name' ) ) {
	function detect_user_gender_by_name( $name, $check_input = '' ) {
		if ( $check_input ) {
			$check_input = strtolower( $check_input );
			if ( in_array( $check_input, [ 'male', 'female', 'other' ] ) ) {
				return $check_input;
			}
			
		}
//		$name = explode( ' ', $name );
		// get_remote_contents( 'https://api.genderize.io/?name='. $name[0] .'&country_id=BD' );
		return 'male';
	}
	
}
// End of file theme_helper.php.
