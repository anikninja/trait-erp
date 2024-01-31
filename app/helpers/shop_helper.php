<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('convert_currency')) {
    function convert_currency($amount, $from, $to)
    {
        $data = file_get_contents("https://www.google.com/finance/converter?a=$amount&from=$from&to=$to");
        preg_match("/<span class=bld>(.*)<\/span>/", $data, $converted);
        $converted = preg_replace('/[^0-9.]/', '', $converted[1]);
        return number_format(round($converted, 3), 2);
    }
}

if ( ! function_exists( 'normalize_exponent' ) ) {
	/**
	 * Normalize Exponent Value.
	 *
	 * Eg. 1.0E-6 -> 0.000001
	 *
	 * @param string|float $bitFloat Exponent.
	 * @param int $precision         Precision.
	 *
	 * @return string
	 */
	function normalize_exponent( $bitFloat, $precision = 17 ) {
		if ( ! $bitFloat ) {
			return '0';
		}
		$bitFloat = (string) rtrim( number_format( doubleval( $bitFloat ), $precision ), 0 );
		if ( strpos( $bitFloat, '.' ) == strlen( $bitFloat ) - 1 ) {
			$bitFloat = rtrim( $bitFloat, '.' );
		}
		
		return $bitFloat;
	}
}

if ( ! function_exists( 'is_email' ) ) {
	/**
	 * @param string $email
	 *
	 * @return false|string
	 */
	function is_email( $email ) {
		
		// Test for the minimum length the email can be.
		if ( strlen( $email ) < 6 ) {
			/**
			 * Filters whether an email address is valid.
			 *
			 * This filter is evaluated under several different contexts, such as 'email_too_short',
			 * 'email_no_at', 'local_invalid_chars', 'domain_period_sequence', 'domain_period_limits',
			 * 'domain_no_periods', 'sub_hyphen_limits', 'sub_invalid_chars', or no specific context.
			 *
			 */
			return false; // email_too_short
		}
		
		// Test for an @ character after the first position.
		if ( strpos( $email, '@', 1 ) === false ) {
			return false; // email_no_at
		}
		
		// Split out the local and domain parts.
		list( $local, $domain ) = explode( '@', $email, 2 );
		
		// LOCAL PART
		// Test for invalid characters.
		if ( ! preg_match( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local ) ) {
			return false; // local_invalid_chars
		}
		
		// DOMAIN PART
		// Test for sequences of periods.
		if ( preg_match( '/\.{2,}/', $domain ) ) {
			return false; // domain_period_sequence
		}
		
		// Test for leading and trailing periods and whitespace.
		if ( trim( $domain, " \t\n\r\0\x0B." ) !== $domain ) {
			return false; // domain_period_limits
		}
		
		// Split the domain into subs.
		$subs = explode( '.', $domain );
		
		// Assume the domain will have at least two subs.
		if ( 2 > count( $subs ) ) {
			return false; // domain_no_periods
		}
		
		// Loop through each sub.
		foreach ( $subs as $sub ) {
			// Test for leading and trailing hyphens and whitespace.
			if ( trim( $sub, " \t\n\r\0\x0B-" ) !== $sub ) {
				return false; // sub_hyphen_limits
			}
			
			// Test for invalid characters.
			if ( ! preg_match( '/^[a-z0-9-]+$/i', $sub ) ) {
				return false; // sub_invalid_chars
			}
		}
		
		// Why not let php check a bit.
		$email = filter_var( $email, FILTER_VALIDATE_EMAIL );
		
		// Congratulations, your email made it!
		return $email; // OK
	}
}

if ( ! function_exists( 'is_phone' ) ) {
	/**
	 *
	 * @param string|int $phone
	 *
	 * @return bool|int
	 */
	function is_phone( $phone ) {
		// @TODO detect and remove dial code for different region.
		// Use google's phone lib.
		// also validate frontend to reduce backend calculation.
		// use google's phone lib to validate only on registration and store that phone number.
		// do same when user tries to update.
		// other operation can use frontend validation and use the data to query, not to store.
		
		$phone = preg_replace( '/[-\(\)\s]/', '', $phone );
		$phone = preg_replace( '/^\+88/', '', $phone ); // bd
		$phone = preg_replace( '/^\+1/', '', $phone ); // us
		
		return is_numeric( $phone ) ? $phone : false;
	}
}

if ( ! function_exists( 'is_valid_username' ) ) {
	function is_valid_username( $username ) {
		$username = strtolower( $username );
		return preg_match('/^[a-zA-Z0-9-_.]{3,}$/', $username ) > 0 ? $username : false;
	}
}
