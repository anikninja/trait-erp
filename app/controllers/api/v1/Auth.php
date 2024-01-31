<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

/**
 * Class Products
 *
 */
class Auth extends MY_REST_Controller {
    public function __construct() {
	    parent::__construct();
	    $this->methods['index_get']['limit'] = 500;
    }
    
    public function login_post() {
    	$this->form_validation->set_data( $this->post() );
	    $this->form_validation->set_rules('identity', lang('identity'), 'required');
	    $this->form_validation->set_rules('password', lang('password'), 'required');
	    $status = REST_Controller::HTTP_OK;
	    if ( $this->form_validation->run() == true ) {
		    $identity = $this->post( 'identity' );
		    $password = $this->post( 'password' );
		    $remember = (bool) $this->post( 'remember_me' );
		    if ( ! is_email( $identity ) && is_phone( $identity ) ) {
			    if ( $user = $this->site->getUserByPhone( $identity ) ) {
				    $identity = $user->email;
			    }
		    }
		    $login = $this->ion_auth->login( $identity, $password, $remember );
		    if ( ! $login ) {
		    	$data = [
		    		'status' => false,
				    'error'  => $this->ion_auth_errors( true ),
			    ];
			    $status = $this->ion_auth_errors() === 'login_timeout' ? REST_Controller::HTTP_TOO_MANY_REQUESTS : REST_Controller::HTTP_UNAUTHORIZED;
		    } else {
		    	
			    $data = [
				    'status'  => true,
				    'data'    => $this->getCurrentUser(),
				    'message' => $this->ion_auth_messages( true ),
			    ];
		    }
	    } else {
	    	$data = [
			    'status' => false,
			    'error'  => $this->validation_errors(),
		    ];
		    $status = REST_Controller::HTTP_BAD_REQUEST;
	    }
	    // cookies are set.
	    // Client needs to sends those cookie back.
	    $this->set_response( $data, $status );
    }
    
    public function check_get() {
    	$loggedIn = (bool) $this->session->userdata( 'identity' );
	    $this->set_response(
	    	[
	    		'status' => $loggedIn,
			    'message' => '',
		    ],
		    REST_Controller::HTTP_OK
	    );
    }
    
    public function logout_get() {
	    $this->ion_auth->logout();
	    $this->set_response(
		    [
			    'status'  => true,
			    'message' => $this->ion_auth_messages( true ),
		    ],
		    REST_Controller::HTTP_OK
	    );
    }
	
	public function register_post() {
		$this->form_validation->set_data( $this->post() );
		
		if ( 'grocerant' === $this->getCurrentThemeName() ) {
			$this->form_validation->set_rules( 'name', lang( 'full_name' ), 'required' );
		} else {
			$this->form_validation->set_rules( 'first_name', lang( 'first_name' ), 'required' );
			$this->form_validation->set_rules( 'last_name', lang( 'last_name' ), 'required' );
		}
		
		if ( $this->input->post( 'username' ) ) {
			$this->form_validation->set_rules('username', lang('username'), 'is_unique[users.username]');
		}
		
		$this->form_validation->set_rules( 'phone', lang( 'phone' ), 'required' );
		$this->form_validation->set_rules( 'email', lang( 'email_address' ), 'required|is_unique[users.email]' );
		$this->form_validation->set_rules( 'password', lang( 'password' ), 'required|min_length[8]|max_length[25]|matches[password_confirm]' );
		$this->form_validation->set_rules( 'password_confirm', lang( 'confirm_password' ), 'required' );
		$this->form_validation->set_rules( 'referral_id', lang( 'referral' ), 'trim' );
		$status = REST_Controller::HTTP_OK;
		if ( $this->form_validation->run() == true ) {
			
			if ( 'grocerant' === $this->getCurrentThemeName() ) {
				$name       = $this->input->post( 'name' );
				$names      = explode( ' ', $name );
				$first_name = ! empty( $names ) ? $names[0] : null;
				array_shift( $names );
				$last_name = ! empty( $names ) ? implode( ' ', $names ) : null;
			} else {
				$first_name = $this->input->post( 'first_name' );
				$last_name  = $this->input->post( 'last_name' );
				$name       = implode( ' ', [ $first_name, $last_name ] );
			}
			
			$email    = strtolower( $this->input->post( 'email' ) );
			$username = strtolower( $this->input->post( 'username' ) );
			if ( ! $username ) {
				$username = $this->generate_username( $email, $name );
			}
			
			$password = $this->post( 'password' );
			
			$customer_group = $this->shop_model->getCustomerGroup( $this->Settings->customer_group );
			$price_group    = $this->shop_model->getPriceGroup( $this->Settings->price_group );
			
			$company_data = [
				'company'             => $this->post('company') ? $this->post('company') : '-',
				'name'                => $name,
				'email'               => $this->post('email'),
				'phone'               => $this->post('phone'),
				'group_id'            => 3,
				'group_name'          => 'customer',
				'customer_group_id'   => (!empty($customer_group)) ? $customer_group->id : null,
				'customer_group_name' => (!empty($customer_group)) ? $customer_group->name : null,
				'price_group_id'      => (!empty($price_group)) ? $price_group->id : null,
				'price_group_name'    => (!empty($price_group)) ? $price_group->name : null,
			];
			
			$company_id = $this->shop_model->addCustomer($company_data);
			
			$additional_data = [
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'phone'      => $this->post('phone', true ),
				'company'    => $this->post('company', true ),
				'gender'     => detect_user_gender_by_name( $name, $this->post( 'male' ) ),
				'company_id' => $company_id,
				'group_id'   => 3,
			];
			if ( $register = $this->ion_auth->register($username, $password, $email, $additional_data) ) {
				
				$referral = $this->input->post( 'referral_id', true );
				$this->add_referral( is_array( $register ) ? $register['id'] : $register, $referral );
				
				$data = [
					'status' => true,
					'message' => lang( 'account_created' ),
				];
			} else {
				$data = [
					'status' => false,
					'error' => $this->ion_auth_errors( true ),
				];
				$status = REST_Controller::HTTP_BAD_REQUEST;
			}
		} else {
			$data = [
				'status' => false,
				'error'  => $this->validation_errors(),
			];
			$status = REST_Controller::HTTP_BAD_REQUEST;
		}
		$this->set_response( $data, $status );
	}
	
	public function activate_post() {
		$this->form_validation->set_data( $this->post() );
		$this->form_validation->set_rules('email', lang('identity1'), 'required');
		$this->form_validation->set_rules('code', lang('activation_code'), 'required');
		
		if ( $this->form_validation->run() ) {
			$email = $this->post( 'email' );
			$code = $this->post( 'code' );
			
			$identity = $this->post( 'email', true );
			if ( ! is_email( $identity ) && is_phone( $identity ) ) {
				if ( $user = $this->site->getUserByPhone( $identity ) ) {
					$identity = $user->email;
				}
			}
			
			$user = $this->db->get_where( 'users', [ 'email' => $identity ], 1 )->row();
			if ( $user ) {
				if ( $user->active < 1 ) {
					$activation = $this->ion_auth->activate( $user->id, $code );
					if ( $activation ) {
						$this->set_response(
							[
								'status' => true,
								'message' => $this->ion_auth->messages(),
							],
							REST_Controller::HTTP_OK
						);
					} else {
						$this->set_response(
							[
								'status' => false,
								'error' => $this->ion_auth->errors(),
							],
							REST_Controller::HTTP_NOT_ACCEPTABLE
						);
					}
				} else {
					$this->set_response(
						[
							'status' => true,
							'message' => lang( 'account_already_activated' ),
						],
						REST_Controller::HTTP_NOT_ACCEPTABLE
					);
				}
			} else {
				$this->set_response(
					[
						'status' => false,
						'error' => lang('user_not_found' ),
					],
					REST_Controller::HTTP_NOT_FOUND
				);
			}
		} else {
			$this->set_response(
				[
					'status' => false,
					'error' => $this->validation_errors(),
				],
				REST_Controller::HTTP_BAD_REQUEST
			);
		}
	}
    
	public function forgot_post() {
    	$this->form_validation->set_data( $this->post() );
		$this->form_validation->set_rules( 'email', lang( 'identity1' ), 'required' );
		if ( $this->form_validation->run() ) {
			$identity = $this->post( 'email', true );
			if ( ! is_email( $identity ) && is_phone( $identity ) ) {
				if ( $user = $this->site->getUserByPhone( $identity ) ) {
					$identity = $user->email;
				}
			}
			$identity = $this->ion_auth
				->where( 'email', $identity )
				->users()
				->row();
			if ( empty( $identity ) ) {
				$this->set_response(
					[
						'status' => false,
						'error' => lang('user_not_found'),
					],
					REST_Controller::HTTP_NOT_FOUND
				);
			}
			$forgotten = $this->ion_auth->forgotten_password( $identity->email );
			if ( $forgotten ) {
				$this->set_response(
					[
						'status' => true,
						'message' => $this->ion_auth_messages( true ),
					],
					REST_Controller::HTTP_OK
				);
			} else {
				$this->set_response(
					[
						'status' => true,
						'message' => $this->ion_auth_errors( true ),
					],
					REST_Controller::HTTP_INTERNAL_SERVER_ERROR
				);
			}
		} else {
			$this->set_response(
				[
					'status' => false,
					'error' => $this->validation_errors(),
				],
				REST_Controller::HTTP_BAD_REQUEST
			);
		}
	}
	
	public function reset_post( $code = null ) {
		if ( ! $code ) {
			$this->set_response(
				[
					'status' => false,
					'error'  => lang( 'reset_password_code_invalid' ),
				],
				REST_Controller::HTTP_BAD_REQUEST
			);
		} else {
			$user = $this->ion_auth->forgotten_password_check( $code );
			if ( $user ) {
				$this->form_validation->set_data( $this->post() );
				$this->form_validation->set_rules('password', lang('password'), 'required|min_length[8]|max_length[25]|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', lang('confirm_password'), 'required');
				if ( $this->form_validation->run() ) {
					$change = $this->ion_auth->reset_password( $user->email, $this->post( 'new' ) );
					if ( $change ) {
						$this->set_response(
							[
								'status'  => true,
								'message' => $this->ion_auth_messages( true ),
							],
							REST_Controller::HTTP_OK
						);
					} else {
						$this->set_response(
							[
								'status' => false,
								'error'  => $this->ion_auth_errors( true ),
							],
							REST_Controller::HTTP_BAD_REQUEST
						);
					}
				} else {
					$this->set_response(
						[
							'status' => false,
							'error'  => $this->validation_errors(),
						],
						REST_Controller::HTTP_BAD_REQUEST
					);
				}
			} else {
				$this->set_response(
					[
						'status' => false,
						'error'  => lang( 'reset_password_code_invalid' ),
					],
					REST_Controller::HTTP_BAD_REQUEST
				);
			}
		}
	}
	
	public function logout_post() {
    	// alias method
		$this->logout_get();
	}
}
