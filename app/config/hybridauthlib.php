<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/* ----------------------------------------------------------------------------
/* HybridAuth Guide: http://hybridauth.github.io/hybridauth/userguide.html
/* ------------------------------------------------------------------------- */

/**
 * This is for repository and testing.
 * Duplicate this config file with hAuth.php as the file name.
 * And hAuth.php will be used by the system automatically.
 * Also, hAuth.php is ignored in .gitignore.
 */

$config = [
	
	'Hauth_base_url' => 'social_auth/endpoint',
	
	"providers"  => [
		
		"Google" => [
			"enabled" => true,
			"keys"    => [ "id" => "xxx", "secret" => "xxx" ],
		],
		
		"Facebook" => [
			"enabled" => true,
			"keys"    => [ "id" => "xxx", "secret" => "xxx" ],
//			"scope"   => "email, public_profile, user_birthday",
			"scope"   => "email, public_profile", // user_birthday permission cause issues.
		],
		
		"Twitter" => [
			"enabled" => false,
			"keys"    => [ "key" => "", "secret" => "" ],
		],
		
		"Yahoo" => [
			"enabled" => false,
			"keys"    => [ "id" => "", "secret" => "" ],
		],
		
		"Live" => [
			"enabled" => false,
			"keys"    => [ "id" => "", "secret" => "" ],
		],
		
		"MySpace" => [
			"enabled" => false,
			"keys"    => [ "key" => "", "secret" => "" ],
		],
		
		"OpenID" => [
			"enabled" => false,
		],
		
		"LinkedIn" => [
			"enabled" => false,
			"keys"    => [ "key" => "", "secret" => "" ],
		],
		
		"Foursquare" => [
			"enabled" => false,
			"keys"    => [ "id" => "", "secret" => "" ],
		],
		
		"AOL" => [
			"enabled" => false,
		],
	],
	"debug_mode" => ( ENVIRONMENT != 'production' ),
	"debug_file" => APPPATH . 'logs/hybridauth' . date( 'Y-m-d' ) . '.php',
];
