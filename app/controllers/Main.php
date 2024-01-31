<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Main
 *
 * @property Ion_auth|Auth_model $ion_auth
 */
class Main extends MY_Shop_Controller {
	
    public function __construct() {
        parent::__construct();

        if ($this->Settings->mmode && $this->v != 'login') {
            redirect('notify/offline');
        }
        $this->load->library('ion_auth');
        $this->load->library('form_validation');
        $this->lang->admin_load('auth', $this->Settings->user_language);
    }

    public function activate( $id, $code )
    {
	    if ( ! SHOP ) {
		    redirect( 'admin/auth/activate/' . $id . '/' . $code );
        }
	    if ( $code ) {
		    if ( $activation = $this->ion_auth->activate( $id, $code ) ) {
			    $this->session->set_flashdata( 'message', $this->ion_auth->messages() );
			    redirect( 'login' );
		    }
	    } else {
		    $this->session->set_flashdata( 'error', $this->ion_auth->errors() );
		    redirect( 'login' );
	    }
    }

    public function captcha_check( $cap )
    {
        $expiration = time() - 300; // 5 minutes limit
        $this->db->delete('captcha', ['captcha_time <' => $expiration]);

        $this->db->select('COUNT(*) AS count')
        ->where('word', $cap)
        ->where('ip_address', $this->input->ip_address())
        ->where('captcha_time >', $expiration);

        if ($this->db->count_all_results('captcha')) {
            return true;
        }
        $this->form_validation->set_message('captcha_check', lang('captcha_wrong'));
        return false;
    }

    public function cookie( $val ) {
	    $val = 'accepted' == $val ? 'accepted' : 'declined';
	    set_cookie('shop_use_cookie', $val, 31536000);
	    if ( $this->input->is_ajax_request() ) {
	        $this->rerp->send_json( [ 'success' => true, 'response' => $val ] );
	    }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function currency( $currency )
    {
        set_cookie('shop_currency', $currency, 31536000);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function forgot_password( $type = NULL ) {
	    if ( ! SHOP ) {
            redirect('admin/auth/forgot_password');
        }
	    
	    if ( 'id1' === $type ) {
		    $this->form_validation->set_rules( 'email', lang( 'email_address' ), 'required|valid_email' );
	    } else {
		    $this->form_validation->set_rules( 'identity', lang( 'identity1' ), 'required' );
	    }
	
	    if ( $this->form_validation->run() == false ) {
	        if ( $this->input->is_ajax_request() ) {
                $this->rerp->send_json(validation_errors());
	        } else {
		        $this->session->set_flashdata( 'error', validation_errors() );
		        redirect($_SERVER['HTTP_REFERER']);
	        }
        } else {
		    if ( 'id1' === $type ) {
			    $identity = $this->input->post( 'email', true );
		    } else {
			    
			    $identity = $this->input->post( 'identity', true );
//			    $identity = false;
//			    if ( $whoami['type'] === 'phone' ) {
//				    $identity = $this->site->getUserByPhone( $whoami['identity'] );
//			    }
//			    $identity = $identity ? $identity->email : $whoami['identity'];
		    }
		
		    $error   = false;
		    $message = '';
		    $whoami  = $this->whoami( $identity );
		    if ( ! $whoami['type'] || ! $whoami['identity'] ) {
			    $error   = true;
			    $message = lang( 'forgot_password_email_not_found' );
		    } else {
			    $identity = $this->ion_auth->where( $whoami['type'], $whoami['identity'] )->users()->row();
			    if ( empty( $identity ) ) {
				    $error   = true;
				    $message = lang( 'forgot_password_email_not_found' );
			    } else {
				    if ( $this->ion_auth->forgotten_password( $identity->email ) ) {
					    $message = $this->ion_auth->messages();
				    } else {
					    $error = true;
					    $message = $this->ion_auth->errors();
				    }
			    }
		    }
		    
	        if ( $this->input->is_ajax_request() ) {
		        $this->rerp->send_json( [ 'status' => ( false !== $error ? 'error' : 'success'), 'message' => $message ] );
	        } else {
		        $this->session->set_flashdata( ( false !== $error ? 'error' : 'message' ), $message );
		        redirect($_SERVER['HTTP_REFERER']);
	        }
        }
    }

    public function hide( $id = null ) {
        $this->session->set_userdata('hidden' . $id, 1);
        echo true;
    }
    
    public function index() {
    	
	    if ( ! SHOP ) {
		    redirect( 'admin' );
	    }
	    
	    if ( $this->shop_settings->private && ! $this->loggedIn ) {
		    redirect( '/login' );
	    }
	    
	    $this->data['page_title'] = $this->shop_settings->shop_name;
	    $this->data['page_desc']  = $this->shop_settings->description;

        $this->page_construct('index', $this->data);
    }
	
	public function language( $lang ) {
        $folder        = 'app/language/';
        $languagefiles = scandir($folder);
        if (in_array($lang, $languagefiles)) {
            set_cookie('shop_language', $lang, 31536000);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function login($m = null)
    {
    	
        if (!SHOP || $this->Settings->mmode) {
            redirect('admin/login');
        }
        if ($this->loggedIn) {
            $this->session->set_flashdata('error', $this->session->flashdata('error'));
            redirect('/');
        }

        if ($this->Settings->captcha) {
            $this->form_validation->set_rules('captcha', lang('captcha'), 'required|callback_captcha_check');
        }
	
	    if ( $this->form_validation->run( 'auth/login' ) == true ) {
		    $remember = (bool) $this->input->post( 'remember_me' );
		    $whoami   = $this->whoami( $this->input->post( 'identity' ) );
		    $identity = false;
		    if ( $whoami['type'] === 'phone' ) {
			    $identity = $this->site->getUserByPhone( $whoami['identity'] );
		    }
		    $identity = $identity ? $identity->email : $whoami['identity'];
      
		    if ( $this->ion_auth->login( $identity, $this->input->post( 'password' ), $remember ) ) {
			    if ( $this->Settings->mmode ) {
				    if ( ! $this->ion_auth->in_group( 'owner' ) ) {
					    $this->session->set_flashdata( 'error', lang( 'site_is_offline_plz_try_later' ) );
					    redirect( 'logout' );
				    }
			    }
			    $this->session->set_flashdata( 'message', $this->ion_auth->messages() );
			    redirect( $this->getLoginRedirectTo() );
            } else {
			    $this->session->set_flashdata( 'error', $this->ion_auth->errors() );
			    redirect( 'login' );
            }
        } else {
            if ($this->Settings->captcha) {
                $this->load->helper('captcha');
                $vals = [
                    'img_path'    => './assets/captcha/',
                    'img_url'     => base_url('assets/captcha/'),
                    'img_width'   => 150,
                    'img_height'  => 34,
                    'word_length' => 5,
                    'colors'      => ['background' => [255, 255, 255], 'border' => [204, 204, 204], 'text' => [102, 102, 102], 'grid' => [204, 204, 204]],
                ];
                $cap     = create_captcha($vals);
                $capdata = [
                    'captcha_time' => $cap['time'],
                    'ip_address'   => $this->input->ip_address(),
                    'word'         => $cap['word'],
                ];

                $query = $this->db->insert_string('captcha', $capdata);
                $this->db->query($query);
                $this->data['image']   = $cap['image'];
                $this->data['captcha'] = ['name' => 'captcha',
                    'id'                         => 'captcha',
                    'type'                       => 'text',
                    'class'                      => 'form-control',
                    'required'                   => 'required',
                    'placeholder'                => lang('type_captcha'),
                ];
            }
            $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['message']    = $m ? lang('password_changed') : $this->session->flashdata('message');
            $this->data['page_title'] = lang('login');
            $this->data['page_desc']  = $this->shop_settings->description;
            if ($this->shop_settings->private) {
                $this->data['message']       = $data['message'] ?? $this->session->flashdata('message');
                $this->data['error']         = isset($this->data['error']) ? $this->data['error'] : $this->session->flashdata('error');
                $this->data['warning']       = $data['warning']  ?? $this->session->flashdata('warning');
                $this->data['reminder']      = $data['reminder'] ?? $this->session->flashdata('reminder');
                $this->data['Settings']      = $this->Settings;
                $this->data['shop_settings'] = $this->shop_settings;
                $this->load->view($this->theme . 'user/private_login.php', $this->data);
            } else {
                $this->page_construct('user/login', $this->data);
            }
        }
    }
    
    protected function getLoginRedirectTo() {
	    $referrer = ($this->session->userdata('requested_page') && $this->session->userdata('requested_page') != 'admin') ? $this->session->userdata('requested_page') : '';
	    if ( empty( $referrer ) && $this->input->post( 'redirect_to' ) ) {
		    $referrer = str_replace( site_url(), '', $this->input->post( 'redirect_to' ) );
		    $referrer = rtrim( $referrer, '/\\' );
		    $referrer = ltrim( $referrer, '/\\' );
	    }
	    
	    return empty( $referrer ) ? '/' : $referrer;
    }
	
	public function logout( $m = null ) {
		if ( ! SHOP ) {
			redirect( 'admin/logout' );
		}
        $logout   = $this->ion_auth->logout();
        $referrer = ($_SERVER['HTTP_REFERER'] ?? '/');
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        redirect($m ? 'login/m' : $referrer);
    }
	
	public function profile( $act = null ) {
		if ( ! $this->loggedIn ) {
			redirect( '/' );
		}
		if ( ! SHOP || $this->Staff ) {
            redirect('admin/users/profile/' . $this->session->userdata('user_id'));
        }
        $user = $this->ion_auth->user()->row();
		if ( $act == 'user' ) {
            $this->form_validation->set_rules('first_name', lang('first_name'), 'required');
            $this->form_validation->set_rules('last_name', lang('last_name'), 'required');
            $this->form_validation->set_rules('phone', lang('phone'), 'required');
            $this->form_validation->set_rules('email', lang('email'), 'required|valid_email');
            $this->form_validation->set_rules('company', lang('company'), 'trim');
            $this->form_validation->set_rules('vat_no', lang('vat_no'), 'trim');
            $this->form_validation->set_rules('address', lang('billing_address'), 'required');
            $this->form_validation->set_rules('city', lang('city'), 'required');
            $this->form_validation->set_rules('state', lang('state'), 'required');
            $this->form_validation->set_rules('postal_code', lang('postal_code'), 'required');
            $this->form_validation->set_rules('country', lang('country'), 'required');
            $updateEmail = false;
			if ( $user->email != $this->input->post( 'email' ) ) {
				$updateEmail = true;
                $this->form_validation->set_rules('email', lang('email'), 'trim|is_unique[users.email]');
            }
			
			if ( $this->form_validation->run() === true ) {
				
                $bdata = [
                    'name'        => $this->input->post('first_name') . ' ' . $this->input->post('last_name'),
                    'phone'       => $this->input->post('phone'),
                    'email'       => $this->input->post('email'),
                    'company'     => $this->input->post('company'),
                    'vat_no'      => $this->input->post('vat_no'),
                    'address'     => $this->input->post('address'),
                    'city'        => $this->input->post('city'),
                    'state'       => $this->input->post('state'),
                    'postal_code' => $this->input->post('postal_code'),
                    'country'     => $this->input->post('country'),
                ];

                $udata = [
                    'first_name' => $this->input->post('first_name'),
                    'last_name'  => $this->input->post('last_name'),
                    'company'    => $this->input->post('company'),
                    'phone'      => $this->input->post('phone'),
                    'email'      => $this->input->post('email'),
                ];
				
				if ( $this->ion_auth->update( $user->id, $udata ) && $this->shop_model->updateCompany( $user->company_id, $bdata ) ) {
					$this->session->set_flashdata( 'message', lang( 'user_updated' ) );
					$this->session->set_flashdata( 'message', lang( 'billing_data_updated' ) );
					if ( $updateEmail ) {
						if ( $this->ion_auth->deactivate_user_account( $user->id ) ) {
							$send = $this->ion_auth->send_activation_email( $user->id, $udata['email'], $udata );
							if ( $send ) {
								$this->session->set_flashdata( 'message', $this->ion_auth->messages() );
							} else {
								// keep the old data.
								$this->ion_auth->update( $user->id, [ 'email' => $user->email ] );
								$this->shop_model->updateCompany( $user->company_id, [ 'email' => $user->email ] );
								
								$this->session->set_flashdata( 'error', $this->ion_auth->errors() );
								$this->session->set_flashdata( 'error', lang( 'email_update_failed' ) );
								redirect( 'profile' );
							}
							$this->ion_auth->logout();
							redirect( 'login' );
						} else {
							// keep the old data.
							$this->ion_auth->update( $user->id, [ 'email' => $user->email ] );
							$this->shop_model->updateCompany( $user->company_id, [ 'email' => $user->email ] );
							
							$this->session->set_flashdata( 'error', $this->ion_auth->errors() );
							$this->session->set_flashdata( 'error', lang( 'email_update_failed' ) );
							redirect( 'profile' );
						}
					} else {
						redirect( 'profile' );
					}
				}
            } else {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER['HTTP_REFERER']);
            }
        } elseif ($act == 'password') {
            $this->form_validation->set_rules('old_password', lang('old_password'), 'required');
            $this->form_validation->set_rules('new_password', lang('new_password'), 'required|min_length[8]|max_length[25]');
            $this->form_validation->set_rules('new_password_confirm', lang('confirm_password'), 'required|matches[new_password]');

            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('profile');
            } else {
                if (DEMO) {
                    $this->session->set_flashdata('warning', lang('disabled_in_demo'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));
                $change   = $this->ion_auth->change_password($identity, $this->input->post('old_password'), $this->input->post('new_password'));

                if ($change) {
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    $this->logout('m');
                } else {
                    $this->session->set_flashdata('error', $this->ion_auth->errors());
                    redirect('profile');
                }
            }
        }

        $this->data['featured_products'] = $this->shop_model->getFeaturedProducts();
        $this->data['customer']          = $this->site->getCompanyByID($this->session->userdata('company_id'));
        $this->data['page_title']        = lang('profile');
        $this->data['page_desc']         = $this->shop_settings->description;
        $this->page_construct('user/profile', $this->data);
    }

    public function register() {
	    if ( $this->shop_settings->private ) {
		    redirect( '/login' );
	    }
	
	    if ( 'grocerant' === $this->getCurrentThemeName() ) {
		    $this->form_validation->set_rules( 'name', lang( 'full_name' ), 'required' );
	    }
	    elseif ( 'namibd' === $this->getCurrentThemeName() ) {
		    $this->form_validation->set_rules( 'name', lang( 'full_name' ), 'required' );
	    }
	    else {
		    $this->form_validation->set_rules( 'first_name', lang( 'first_name' ), 'required' );
		    $this->form_validation->set_rules( 'last_name', lang( 'last_name' ), 'required' );
	    }
	
	    if ( $this->input->post( 'username' ) ) {
		    $this->form_validation->set_rules('username', lang('username'), 'is_unique[users.username]');
	    }
	    
	    $this->form_validation->set_rules( 'phone', lang( 'phone' ), 'required' );
	    $this->form_validation->set_rules( 'email', lang( 'email_address' ), 'required|is_unique[users.email]' );
	    
	    $this->form_validation->set_rules( 'password', lang( 'password' ), 'required|min_length[6]|max_length[25]|matches[password_confirm]' );
	    $this->form_validation->set_rules( 'password_confirm', lang( 'confirm_password' ), 'required' );
	
	    if ( $this->form_validation->run() == true ) {
		
		    if ( 'grocerant' === $this->getCurrentThemeName() ) {
			    $name       = $this->input->post( 'name' );
			    $names      = explode( ' ', $name );
			    $first_name = ! empty( $names ) ? $names[0] : null;
			    array_shift( $names );
			    $last_name = ! empty( $names ) ? implode( ' ', $names ) : null;
		    }
		    elseif ( 'namibd' === $this->getCurrentThemeName() ) {
			    $name       = $this->input->post( 'name' );
			    $names      = explode( ' ', $name );
			    $first_name = ! empty( $names ) ? $names[0] : null;
			    array_shift( $names );
			    $last_name = ! empty( $names ) ? implode( ' ', $names ) : null;
		    }
		    else {
			    $first_name = $this->input->post( 'first_name' );
			    $last_name  = $this->input->post( 'last_name' );
			    $name       = implode( ' ', [ $first_name, $last_name ] );
		    }
		    
		    $email    = strtolower( $this->input->post( 'email' ) );
		    $username = strtolower( $this->input->post( 'username' ) );
		    if ( ! $username ) {
			    $username = $this->generate_username( $email, $name );
		    }
	    	
	        $password       = $this->input->post( 'password' );
	        $price_group    = $this->shop_model->getPriceGroup( $this->Settings->price_group );
	        $customer_group = $this->shop_model->getCustomerGroup( $this->Settings->customer_group );
            $company_data   = [
	            'company'             => $this->input->post( 'company' ) ? $this->input->post( 'company' ) : '-',
	            'name'                => $name,
	            'email'               => $this->input->post( 'email' ),
	            'phone'               => $this->input->post( 'phone' ),
	            'group_id'            => 3,
	            'group_name'          => 'customer',
	            'customer_group_id'   => ( ! empty( $customer_group ) ) ? $customer_group->id : null,
	            'customer_group_name' => ( ! empty( $customer_group ) ) ? $customer_group->name : null,
	            'price_group_id'      => ( ! empty( $price_group ) ) ? $price_group->id : null,
	            'price_group_name'    => ( ! empty( $price_group ) ) ? $price_group->name : null,
            ];
            
            $company_id = $this->shop_model->addCustomer($company_data);
            
            $additional_data = [
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'phone'      => $this->input->post('phone'),
                'company'    => $this->input->post('company'),
                'gender'     => detect_user_gender_by_name( $name, $this->input->post( 'gender' ) ),
                'company_id' => $company_id,
                'group_id'   => 3,
            ];
            $this->load->library('ion_auth');
        }
	
	    $register = null;
	    if ( $this->form_validation->run() == true && $register = $this->ion_auth->register( $username, $password, $email, $additional_data ) ) {
	    	
	        $referral = $this->input->post( 'referral_id', true );
	        
	        if ( ! $referral ) {
	            $referral = get_cookie( 'referral_id', true );
	        }
	        $this->add_referral( is_array( $register ) ? $register['id'] : $register, $referral );

		    $this->session->set_flashdata( 'message', lang( 'account_created' ) );
		    redirect( 'login' );
	    } else {
		    $this->session->set_flashdata( 'error', validation_errors() );
		    redirect( 'login#register' );
	    }
    }
	
	/**
	 *
	 * @param string $email
	 * @param string $name
	 *
	 * @return string
	 */
	protected function generate_username( $email = '', $name = '' ) {
		list( $local, $domain ) = explode( '@', $email, 2 );
		$name        = str_replace( ' ', '.', $name );
		$names       = [ $name ];
		$names       = array_merge( $names, explode( '.', $name ) );
		$names       = array_filter( $names );
		$names       = array_unique( $names );
		$combination = [ $local ];
		$combination = array_merge(
			$combination,
			$names,
			array_map( function ( $n ) use ( $local ) {
				return $local . '.' . $n;
			},
				$names ),
			array_map( function ( $n ) use ( $local ) {
				return $n . $local;
			},
				$names )
		);
		$combination = array_map( function ( $n ) {
			$n = strtolower( $n );
			$n = trim( $n );
			
			return $n;
		},
			$combination );
		$combination = array_filter( $combination );
		$combination = array_unique( $combination );
		
		return $this->check_username_combinations( $combination );
	}
    
	/**
	 * @param string[] $combination
	 * @param int      $suffix
	 *
	 * @return string
	 */
    protected function check_username_combinations( $combination, $suffix = 1 ) {
    	$out = '';
	    foreach ( $combination as $un  ) {
	    	if ( ! $this->site->checkUserNameExists( $un ) ) {
			    $out = $un;
			    break;
		    }
	    }
	    if ( empty( $out ) ) {
		    $out = $this->check_username_combinations( array_map( function( $un ) use ( $suffix ) {return $un . $suffix; }, $combination ), $suffix + 1 );
	    }
	
	    return $out;
    }
    
    public function reset_password( $code = null ) {
	    if ( ! SHOP ) {
		    redirect( 'admin/auth/reset_password/' . $code );
	    }
	    if ( ! $code ) {
		    $this->session->set_flashdata( 'error', lang( 'page_not_found' ) );
		    redirect( '/' );
        }
	
	    $user = $this->ion_auth->forgotten_password_check( $code );
	
	    if ( $user ) {
            $this->form_validation->set_rules('new', lang('password'), 'required|min_length[8]|max_length[25]|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', lang('confirm_password'), 'required');
		
		    if ( $this->form_validation->run() == false ) {
			    $this->data['error']                = ( validation_errors() ) ? validation_errors() : $this->session->flashdata( 'error' );
			    $this->data['message']              = $this->session->flashdata( 'message' );
			    $this->data['min_password_length']  = $this->config->item( 'min_password_length', 'ion_auth' );
			    $this->data['new_password']         = [
				    'name'                   => 'new',
				    'id'                     => 'new',
				    'type'                   => 'password',
				    'class'                  => 'form-control',
				    'required'               => 'required',
				    'pattern'                => '(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}',
				    'data-fv-regexp-message' => lang( 'pasword_hint' ),
				    'placeholder'            => lang( 'new_password' ),
			    ];
			    $this->data['new_password_confirm'] = [
				    'name'                      => 'new_confirm',
				    'id'                        => 'new_confirm',
				    'type'                      => 'password',
				    'class'                     => 'form-control',
				    'required'                  => 'required',
				    'data-fv-identical'         => 'true',
				    'data-fv-identical-field'   => 'new',
				    'data-fv-identical-message' => lang( 'pw_not_same' ),
				    'placeholder'               => lang( 'confirm_password' ),
			    ];
			    $this->data['user_id']              = [
				    'name'  => 'user_id',
				    'id'    => 'user_id',
				    'type'  => 'hidden',
				    'value' => $user->id,
			    ];
			    $this->data['code']                 = $code;
			    $this->data['identity_label']       = $user->email;
			    $this->data['page_title']           = lang( 'reset_password' );
			    $this->data['page_desc']            = '';
			    $this->page_construct( 'user/reset_password', $this->data );
		    } else {
			    // do we have a valid request?
			    if ( $user->id != $this->input->post( 'user_id' ) ) {
				    $this->ion_auth->clear_forgotten_password_code( $code );
				    $this->session->set_flashdata( 'error', lang( 'invalid_request' ) );
				    redirect( '/' );
			    } else {
				    // finally change the password
				    $identity = $user->email;
				    $change   = $this->ion_auth->reset_password( $identity, $this->input->post( 'new' ) );
				    if ( $change ) {
					    //if the password was successfully changed
					    $this->session->set_flashdata( 'message', $this->ion_auth->messages() );
					    redirect( 'login' );
				    } else {
					    $this->session->set_flashdata( 'error', $this->ion_auth->errors() );
					    redirect( 'reset_password/' . $code );
				    }
			    }
		    }
        } else {
            //if the code is invalid then send them back to the forgot password page
		    $this->session->set_flashdata( 'error', $this->ion_auth->errors() );
		    redirect( '/' );
        }
    }
    
    public function referral() {
        if ( ! $this->loggedIn ) {
            redirect( '/login' );
        }
        if ( ! SHOP || $this->Staff ) {
            redirect('admin/users/profile/' . $this->session->userdata('user_id'));
        }
	
	    $this->data['page_title']        = lang( 'referral' );
	    $this->data['featured_products'] = $this->shop_model->getFeaturedProducts();
	    $this->data['page_desc']         = $this->shop_settings->description;
	    
	    $this->page_construct( 'user/referral', $this->data );
    }
    
    public function wallet() {
        if ( ! $this->loggedIn ) {
            redirect( '/login' );
        }
        if ( ! SHOP || $this->Staff ) {
	        redirect( 'admin/wallet' );
        }
	    
        $this->load->model('Erp_Transaction_History');
		$walletHistory = new Erp_Transaction_History( $this->session->userdata( 'user_id' ) );
	
	    $this->load->helper( 'pagination' );
		
	    $this->data['walletHistory'] = $walletHistory;
	    $this->data['wallet']        = $walletHistory->getWallet();
	    $this->data['transCount']    = $walletHistory->getTransactionCount();
	    $this->data['transactions']  = $walletHistory->getTransactions( max( 1, absint( $this->input->get('page') ) ) );
	    $this->data['pagination']    = pagination('wallet', $walletHistory->getTransactionCount(), $walletHistory->getItemPerPage() );
	    $this->data['page_title']    = lang( 'wallet' );
        $this->page_construct( 'user/wallet', $this->data );
    }
    
    protected function whoami( $identity, $why = 'login' ) {
	    if ( $_identity = is_email( $identity ) ) {
		    $whoami = [
			    'identity' => $_identity,
			    'type'     => 'email',
		    ];
	    } elseif ( $_identity = is_phone( $identity ) ) {
		    $whoami = [
			    'identity' => $_identity,
			    'type'     => 'phone',
		    ];
	    } elseif ( 'login' === $why && $_identity = is_valid_username( $identity ) ) {
		    $whoami = [
			    'identity' => $_identity,
			    'type'     => 'username',
		    ];
	    } else {
		    $whoami = [ 'identity' => false, 'type' => false ];
	    }
    	
    	return $whoami;
    }
}
