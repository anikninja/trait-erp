<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

if ( ! trait_exists( 'MY_Controller_Trait' ) ) {
	require 'MY_Controller_Trait.php';
}

/**
 * Class MY_Shop_Controller.
 *
 * @property string              $theme
 * @property Ion_auth|Auth_model $ion_auth
 * @property CI_Session $session
 */
class MY_REST_Controller extends REST_Controller {
	
	use MY_Controller_Trait;
	
    public function __construct() {
        parent::__construct();
	    $this->doingREST = true;
        $this->setupCommonController(false, false );
	    $this->lang->admin_load( 'rerp' );
	    $this->lang->admin_load( 'api_lang' );
	    $this->lang->admin_load( 'shop_lang' );
	
	    $this->load->library('form_validation' );
	    $this->load->library('ion_auth');
	
	    $this->lang->admin_load( 'auth', $this->Settings->user_language );
	    $this->lang->load( 'shop', $this->Settings->user_language );
    }
    
    protected function isUserLoggedIn() {
	    return $this->rerp->logged_in();
    }
    
    protected function isCustomer() {
    	return $this->isUserLoggedIn() && $this->Customer;
    }
    
    protected function isStaff() {
    	return $this->isUserLoggedIn() && $this->Staff;
    }
    
    protected function isAdmin() {
    	return $this->isUserLoggedIn() && $this->Admin;
    }
    
    protected function isOwner() {
    	return $this->isUserLoggedIn() && $this->Owner;
    }

    protected function getCurrentUser( $removeSpecial = true ) {
	    $user              = $this->site->getUser();
	    $user->avatar      = ci_get_user_avatar( $user, false, [], true );
	    $user->referral_id = format_referral_id( $user->id );
	    if ( $removeSpecial ) {
		    unset(
			    $user->last_ip_address, $user->ip_address, $user->password,
			    $user->salt, $user->activation_code, $user->forgotten_password_code,
			    $user->forgotten_password_time, $user->remember_code
		    );
	    }
    	return $user;
    }
	
	/**
	 * Retrieve the validation errors
	 *
	 * @access public
	 * @return array
	 */
	public function validation_errors() {
		return $this->form_validation->error_array();
	}
	
	protected function response_invalid_form() {
		$this->set_response(
			[
				'status' => false,
				'error' => $this->validation_errors(),
			],
			REST_Controller::HTTP_BAD_REQUEST
		);
	}
	
	/**
	 * @param string $message
	 * @param string $error_code
	 */
	protected function response_404( $message = null, $error_code = 'UNKNOWN' ) {
		$this->set_response(
			[
				'status'  => false,
				'error'   => $error_code,
				'message' => $message ? $message : lang( 'api_error_404' ),
			],
			REST_Controller::HTTP_NOT_FOUND
		);
	}
	
	protected function response_user_unauthorized( $error = null ) {
		$this->set_response(
			[
				'status' => false,
				'error'  => $error ? $error : lang( 'text_rest_unauthorized' ),
			],
			REST_Controller::HTTP_UNAUTHORIZED
		);
	}

	protected function response_permission_denied( $error = null ) {
		$this->set_response(
			[
				'status' => false,
				'error'  => $error ? $error : lang( 'access_denied' ),
			],
			REST_Controller::HTTP_UNAUTHORIZED
		);
	}
	
	protected function not_implemented() {
		$this->set_response(
			[
				'status' => false,
				'error' => 'NOT_IMPLEMENTED',
				'message' => 'METHOD_NOT_IMPLEMENTED',
			],
			REST_Controller::HTTP_NOT_IMPLEMENTED
		);
	}
	
	protected function ion_auth_errors( $langify = false ) {
		return $this->ion_auth_message_format( $this->ion_auth->errors_array( false ), $langify );
	}
	
	protected function ion_auth_messages( $langify = false ) {
		return $this->ion_auth_message_format( $this->ion_auth->messages_array( false ), $langify );
	}
	
	protected function ion_auth_message_format( $messages, $langify = false ) {
		if ( 1 === count( $messages ) ) {
			$messages = $messages[0];
		}
		if ( $langify ) {
			if ( is_array( $messages ) ) {
				$output = [];
				foreach( $messages as $message ) {
					if ( 'login_unsuccessful' === $message ) {
						$message = 'login_unsuccessful2';
					}
					$output[] = lang( $message );
				}
				return $output;
			} else {
				return lang( $messages );
			}
		}
		return $messages;
	}
	
	protected function checkPermission( $module = null, $action = null) {
		if ( ! $module ) {
			$module = $this->m;
		}
		if ( ! $action ) {
			$action = str_replace( [ '_get', '_post' ], '', $this->v );
		}
		return $this->rerp->actionPermissions( $action, $module );
	}
	
	protected function response_user_no_permission( $message = null ) {
		$this->response( [
			'status'  => false,
			'error'   => lang( 'permission_denied' ),
			'message' => $message ? $message : lang( 'permission_denied' ),
		],
			self::HTTP_UNAUTHORIZED
		);
	}
}
