<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Wallet
 *
 * @property CI_Parser $parser
 */
class Wallet extends MY_Controller {
	
    public function __construct() {
        parent::__construct();
        
        $this->requireAdminLogin();
        
	    $this->load->model( [
		    'Erp_Wallet',
		    'Erp_Wallet_Withdraw'
	    ] );
	    
    }
    
    public function index() {
    	$user_id = $this->session->userdata( 'user_id' );
    	
    	$this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );
    	$meta                = [
    	    'page_title' => lang( 'my_wallet' ),
    	    'bc'         => [
		        [
			        'link' => base_url(),
			        'page' => lang( 'home' ),
		        ],
		        [
			        'link' => admin_url( 'wallet' ),
			        'page' => lang( 'my_wallet' ),
		        ],
	        ],
    	];
	
	    $wallet = Erp_Wallet::get_user_wallet( $user_id );
	    $this->data['user']   = new Erp_User( $user_id );
	    $this->data['wallet'] = $wallet;
	    
	    
	    $this->data['can_withdraw'] = $this->can_withdraw( $wallet );
	    $this->data['has_pending_request'] = $wallet->has_pending_withdrawal();
    	$this->page_construct( 'wallet/index', $meta, $this->data );
    }
	
	/**
	 * @param Erp_Wallet $wallet
	 * @param bool       $route_permission
	 *
	 * @return bool
	 */
    protected function can_withdraw( $wallet, $route_permission = false ) {
    	if ( false !== $route_permission ) {
    		$this->rerp->checkPermissions( $route_permission, $this->m );
	    }
    	// 1. doesn't have any pending request && have balance to withdraw.
	    $can_withdraw = ( ! $wallet->has_pending_withdrawal() && $wallet->getAmount() > 0 );
	    // 2. minimum bail if minimum withdrawal is disable.
	    if ( $can_withdraw && $this->commission_settings['minimum_withdrawal'] == 0 ) {
	    	return true;
	    }
	    // minimum withdrawal not disabled check wallet balance is greater then or equal to min amount.
	    if ( $this->commission_settings['minimum_withdrawal'] > 0 ) {
		    return $can_withdraw && ( $wallet->getAmount() >= absfloat( $this->commission_settings['minimum_withdrawal'] ) );
	    }
	    
	    return false;
    }
    
    public function list() {
    	
    	$this->rerp->checkPermissions();
    	
    	$this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );
	    $bc                  = [
		    [
			    'link' => base_url(),
			    'page' => lang( 'home' ),
		    ],
		    [
			    'link' => admin_url( 'wallet/list' ),
			    'page' => lang( 'wallet_list' ),
		    ],
	    ];
	    $meta                = [
		    'page_title' => lang( 'wallet_list' ),
		    'bc'         => $bc,
	    ];
        
        $this->page_construct( 'wallet/list', $meta, $this->data );
    }
    
    public function withdrawal() {
	    $this->rerp->checkPermissions( 'withdrawal_list' );
	    $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );
	    $bc                  = [
	        [
	            'link' => base_url(),
	            'page' => lang( 'home' ),
	        ],
	        [
	            'link' => admin_url( 'wallet/list' ),
	            'page' => lang( 'wallet_list' ),
	        ],
	        [
	            'link' => admin_url( 'wallet/withdrawal/'),
	            'page' => lang( 'withdrawal' ),
	        ],
	    ];
	    $meta                = [
	        'page_title' => lang( 'withdrawal_list' ),
	        'bc'         => $bc,
	    ];
	    
	    $this->page_construct( 'wallet/withdraw_list', $meta, $this->data );
    }
    
    public function getWithdrawalList() {
        $this->rerp->checkPermissions( 'withdrawal_list' );
        
        $actions = "<a href='" . admin_url('wallet/withdrawal_accept/$1') . "' class='tip' title='" . sprintf( lang( 'update_x' ), lang( 'withdrawal' ) ) . "' data-toggle=\"modal\" data-target=\"#myModal\"><i class=\"fa fa-edit\"></i></a>";
        $actions = "<div class=\"text-center\">{$actions}</div>";
        $this->load->library('datatables');
        $this->datatables
        ->select( "wallet_withdraw.id as id, CONCAT( u1.first_name, ' ', u1.last_name ) as username, amount, reference_no, CONCAT( u2.first_name, ' ', u2.last_name ) as request_user, request_date, status", false )
        ->from('wallet_withdraw')
        ->join('users as u1', 'u1.id = wallet_withdraw.user_id', 'left' )
        ->join('users as u2', 'u2.id = wallet_withdraw.request_by', 'left' )
        ->add_column('Actions', $actions, 'id')
        ->unset_column( 'id' );
        
        echo $this->datatables->generate();
    }
	
	public function withdrawal_add( $user_id ) {
		
		$redirect_to = $user_id == $this->session->userdata( 'user_id' ) ? 'wallet' : 'wallet/details/' . $user_id;
		
		$this->rerp->checkPermissions( 'withdrawal_add', true );
		
		$this->form_validation->set_rules( 'payment_details', lang( 'payment_details' ), 'required' );
		$this->form_validation->set_rules( 'type', lang( 'type' ), 'required|in_list[' . $this->withdrawal_types( true ) . ']' );
		$this->form_validation->set_rules( 'note', lang( 'note' ), 'trim' );
		
		$user    = new Erp_User( $user_id );
		$company = new Erp_Company( $user->getCompanyId() );
		$wallet  = Erp_wallet::get_user_wallet( $user_id );
		
		if ( $this->form_validation->run() == true ) {
			if ( $wallet->getAmount() <= 0 ) {
				$this->session->set_flashdata( 'error', lang( 'wallet_balance_zero' ) );
				if ( $this->session->userdata( 'user_id' ) == $user_id ) {
					admin_redirect( 'wallet' );
				} else {
					admin_redirect( 'wallet/details/' . $user_id );
				}
			} elseif ( $wallet->has_pending_withdrawal() ) {
				//this is the validation of current withdrawal request
				$this->session->set_flashdata( 'error', lang( 'current_request_processing' ) );
				if ( $this->session->userdata( 'user_id' ) == $user_id ) {
					admin_redirect( 'wallet' );
				} else {
					admin_redirect( 'wallet/details/' . $user_id );
				}
			}
			
			$wdl = new Erp_Wallet_Withdraw();
			$wdl->setUserId( $user->getId() );
			$wdl->setAmount( $wallet->getAmount() );
			$wdl->setRequestBy( $this->session->userdata( 'user_id' ) );
			$wdl->setDescription( $this->input->post( 'note', true ) );
			$wdl->setType( $this->input->post( 'type' ) );
			$wdl->setStatus( 'applied' );
			$wdl->setPaymentDetail( $this->input->post( 'payment_details', true ) );
			
			if ( $wdl->save() ) {
				$this->sent_withdrawal_request_notification( $wdl, $user );
				
				$this->session->set_flashdata( 'message', lang( 'withdrawal_request_sent' ) );
			} else {
				$this->session->set_flashdata( 'error',
					lang( 'unable_to_save_withdrawal_request' ) );
			}
			admin_redirect( $redirect_to );
		} elseif ( $this->input->post( 'withdrawal_add' ) ) {
			
			$this->session->set_flashdata( 'error', validation_errors() );
			admin_redirect( $redirect_to );
		}
		
		$this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );
		$this->data['user']     = $user;
		$this->data['company']  = $company;
		$this->data['wallet']   = $wallet;
		$this->data['statuses'] = $this->withdrawal_statuses();
		$this->data['types']    = $this->withdrawal_types();
		$this->data['modal_js'] = $this->site->modal_js();
		
		if ( $wallet->has_pending_withdrawal() ) {
			// $this->session->set_flashdata( 'error', lang( 'wallet_has_pending_withdrawal_request' ) );
			$this->rerp->md( $redirect_to );
		}
		
		if ( ! $this->can_withdraw( $wallet ) ) {
			// $this->session->set_flashdata( 'error', lang( 'not_enough_wallet_balance' ) );
			$this->rerp->md( $redirect_to );
		}
		
		$this->load->view( $this->theme . 'wallet/withdraw_add', $this->data );
	}
    
    public function withdrawal_accept( $withdrawal_id ) {
	    $this->rerp->checkPermissions( 'withdrawal_accept', true );
	    
	    $wdl  = new Erp_Wallet_Withdraw( $withdrawal_id );
	    $user = new Erp_User( $wdl->getUserId() );
	    
	    $oldStatus = $wdl->getStatus();
	    
	    if ( 'approved' !== $oldStatus ) {
		    $this->form_validation->set_rules( 'status', lang( 'status' ), 'required|in_list[' . $this->withdrawal_statuses( true ) . ']' );
		    $this->form_validation->set_rules( 'payment_details', lang( 'payment_details' ), 'required' );
		    $this->form_validation->set_rules( 'type', lang( 'type' ), 'required|in_list[' . $this->withdrawal_types( true ) . ']' );
		    $this->form_validation->set_rules( 'note', lang( 'note' ), 'trim' );
	        $this->form_validation->set_rules( 'userfile', lang( 'attachment' ), 'xss_clean' );
	    }
	    
	    if ( $this->form_validation->run() == true ) {
	        $status = $this->input->post( 'status', true );
	        if( $oldStatus == 'approved' ) {
	            $this->session->set_flashdata( 'error', lang( 'this_is_already_approved' ) );
	            admin_redirect( 'wallet/withdrawal');
	        }
	        
	        if ( ! in_array( $status, array_keys( $this->withdrawal_statuses() ) ) ) {
	            $this->session->set_flashdata( 'error', lang( 'invalid_withdrawal_status' ) );
	            admin_redirect( 'wallet/withdrawal');
	        }
		
		    if ( $_FILES['userfile']['size'] > 0 ) {
			    $this->load->library( 'upload' );
			    $config['upload_path']   = $this->digital_upload_path;
			    $config['allowed_types'] = $this->digital_file_types;
			    $config['max_size']      = $this->allowed_file_size;
			    $config['overwrite']     = false;
			    $config['encrypt_name']  = true;
			    $this->upload->initialize( $config );
			    if ( ! $this->upload->do_upload() ) {
				    $error = $this->upload->display_errors();
				    $this->session->set_flashdata( 'error', $error );
				    redirect( $_SERVER['HTTP_REFERER'] );
			    }
			
			    if ( $wdl->getAttachment() ) {
				    @unlink( 'files/' . $wdl->getAttachment() );
			    }
			    $wdl->setAttachment( $this->upload->file_name );
		    }
	        
	        $wdl->setModifiedBy( $this->session->userdata( 'user_id' ) );
	        if ( 'approved' === $status ) {
	            $wdl->setApprovedBy( $this->session->userdata( 'user_id' ) );
	        }
	        
	        $wdl->setDescription( $this->input->post( 'note', true ) );
	        $wdl->setPaymentDetail( $this->input->post( 'payment_details', true ) );
	        
	        if ( 'approved' !== $oldStatus ) {
	        	// once approved. it can't be take back
	        	$wdl->setStatus( $status );
	        }
		    
		    if ( $wdl->save() ) {
			    if ( $oldStatus === $wdl->getStatus() ) {
				    $this->session->set_flashdata( 'message', sprintf( lang( 'x_updated' ), lang( 'withdrawal') ) );
			    } else {
			        $this->session->set_flashdata( 'message', lang( 'withdrawal_' . $wdl->getStatus() ) );
			    }
			
			    $this->sent_withdrawal_request_notification( $wdl, $user, $oldStatus === $wdl->getStatus() );
		    } else {
		        $this->session->set_flashdata( 'error', sprintf( lang( 'x_not_saved' ), lang( 'withdrawal' ) ) );
		    }
		    
		    admin_redirect( 'wallet/withdrawal');
	    } elseif ( $this->input->post( 'withdrawal_accept' ) ) {
		    $this->session->set_flashdata('error', validation_errors());
		    admin_redirect( 'wallet/withdrawal');
	    }
	
	    $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );
	    $this->data['user']     = $user;
	    $this->data['wdl']      = $wdl;
	    $this->data['statuses'] = $this->withdrawal_statuses();
	    $this->data['types']    = $this->withdrawal_types();
	    $this->data['modal_js'] = $this->site->modal_js();
	    
	    $this->load->view( $this->theme . 'wallet/withdraw_accept', $this->data );
    }
	
	/**
	 * @param Erp_Wallet_Withdraw $withdraw
	 * @param Erp_User            $user
	 * @param bool                $updated
	 *
	 */
	protected function sent_withdrawal_request_notification( $withdraw, $user = null, $updated = false ) {
		
		try {
			$this->load->library('parser');
			
			if ( ! ( $user instanceof Erp_User ) ) {
				$user = new Erp_User( $withdraw->getUserId() );
			}
			
			$attachment = null;
			if ( $withdraw->getAttachment() && file_exists( './files/' . $withdraw->getAttachment() ) ) {
				$attachment = './files/' . $withdraw->getAttachment();
			}
			
			$data = [
				'stylesheet' => '<link href="' . $this->data['assets'] . 'styles/helpers/bootstrap.min.css" rel="stylesheet"/>',
				'heading'    => lang('wallet_withdrawal_request') . '<hr>',
				'site_link'  => base_url(),
				'site_name'  => $this->Settings->site_name,
				'logo'       => '<img src="' . base_url('assets/uploads/logos/' . $this->Settings->logo) . '" alt="' . $this->Settings->site_name . '"/>',
			];
			
			$user_notification  = $this->get_user_notification( $withdraw, $user, $updated );
			if ( $user_notification ) {
				$message = $this->parser->parse_string(
					$this->get_withdrawal_notification_template(),
					ci_parse_args(
						[
							'name'  => $user->getDisplayName( true, false ),
							'email' => $user->getEmail(),
							'msg'   => $user_notification[1],
						],
						$data
					)
				);
				$this->rerp->send_email(
					$user->getEmail(),
					$user_notification[0],
					$message,
					null,
					null,
					$attachment
				);
			}
			
			$admin_notification = $this->get_admin_notification( $withdraw, $user, $updated );
			if ( $admin_notification ) {
				$message = $this->parser->parse_string(
					$this->get_withdrawal_notification_template(),
					ci_parse_args(
						[
							'name'  => 'Admin',
							'email' => $this->commission_settings['notification_email'],
							'msg'   => $admin_notification[1],
						],
						$data
					)
				);
				$this->rerp->send_email(
					$this->commission_settings['notification_email'],
					$admin_notification[0],
					$message,
					null,
					null,
					$attachment
				);
			}
		} catch ( Exception $e ) {}
	}
	
	/**
	 * @param Erp_Wallet_Withdraw $withdraw
	 * @param Erp_User            $user
	 * @param                     $updated
	 *
	 * @return bool|array
	 */
    protected function get_user_notification( $withdraw, $user, $updated = false ) {
    	$statuses = $this->withdrawal_statuses();
	    $subject = '';
	    ob_start();
	    if ( $updated ) {
		    $subject = sprintf( lang('x_updated'), lang( 'withdrawal_request' ) ) . ' - ' . $this->Settings->site_name;
		    ?>
		    <div class="modal-dialog modal-lg no-modal-header" style="width: 100%;max-width: 100%;">
			    <div class="modal-content">
				    <div class="modal-body print">
					    <div class="row padding10">
						    <div class="col-xs-12">
							    <h2 class="">Hi <?= $user->getDisplayName(); ?>,</h2>
						    </div>
						    <div class="col-xs-12">
							    <p>Your withdrawal request has been updated. Please check the details below.</p>
						    </div>
					    </div>
					    <div class="row">
						    <div class="col-sm-12">
							    <p style="font-weight:bold;"><?= sprintf( lang( 'reference_no__x' ), $withdraw->getReferenceNo() ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'applied_on_x' ), $this->rerp->hrsd( $withdraw->getRequestDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'status__x' ), $statuses[ $withdraw->getStatus() ] ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'updated_on_x' ), $this->rerp->hrsd( $withdraw->getModifiedDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'withdrawal_amount___x' ), $this->rerp->convertMoney( $withdraw->getAmount() ) ); ?></p>
						    </div>
						    <div class="col-sm-12">
							    <h4><?= lang('payment_details'); ?></h4>
							    <p><?= $withdraw->getPaymentDetail(); ?></p>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>
		    <?php
	    } else if ( 'applied' === $withdraw->getStatus() ) {
		    $subject = lang('new_withdrawal_request') . ' - ' . $this->Settings->site_name;
		    ?>
		    <div class="modal-dialog modal-lg no-modal-header" style="width: 100%;max-width: 100%;">
			    <div class="modal-content">
				    <div class="modal-body print">
					    <div class="row padding10">
						    <div class="col-xs-12">
							    <h2 class="">Hi <?= $user->getDisplayName(); ?>,</h2>
						    </div>
						    <div class="col-xs-12">
							    <p>We have received your withdrawal application. Your application is currently under review.<br>Please check the details below.</p>
						    </div>
					    </div>
					    <div class="row">
						    <div class="col-sm-12">
							    <p style="font-weight:bold;"><?= sprintf( lang( 'reference_no__x' ), $withdraw->getReferenceNo() ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'applied_on_x' ), $this->rerp->hrsd( $withdraw->getRequestDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'status__x' ), $statuses[ $withdraw->getStatus() ] ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'updated_on_x' ), $this->rerp->hrsd( $withdraw->getModifiedDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'withdrawal_amount___x' ), $this->rerp->convertMoney( $withdraw->getAmount() ) ); ?></p>
						    </div>
						    <div class="col-sm-12">
							    <h4><?= lang('payment_details'); ?></h4>
							    <p><?= $withdraw->getPaymentDetail(); ?></p>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>
		    <?php
	    } else if ( 'approved' === $withdraw->getStatus() ) {
		    $subject = sprintf( lang( 'your_x_request_has_been_approved' ), lang( 'withdrawal' ) ) . ' - ' . $this->Settings->site_name;
		    $authorizedBy = new Erp_User( $withdraw->getApprovedBy() );
		    $tnx = new Erp_Transaction( $withdraw->getTransactionId() );
		    ?>
		    <div class="modal-dialog modal-lg no-modal-header" style="width: 100%;max-width: 100%;">
			    <div class="modal-content">
				    <div class="modal-body print">
					    <div class="row padding10">
						    <div class="col-xs-12">
							    <h2 class="">Hi <?= $user->getDisplayName(); ?>,</h2>
						    </div>
						    <div class="col-xs-12">
							    <p>Congratulations your withdrawal request has been approved. Please check below for more details.</p>
						    </div>
					    </div>
					    <div class="row">
						    <div class="col-sm-12">
							    <p style="font-weight:bold;"><?= sprintf( lang( 'reference_no__x' ), $withdraw->getReferenceNo() ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'applied_on_x' ), $this->rerp->hrsd( $withdraw->getRequestDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'status__x' ), $statuses[ $withdraw->getStatus() ] ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'approved_on_x' ), $this->rerp->hrsd( $withdraw->getApprovedDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'withdrawal_amount___x' ), $this->rerp->convertMoney( $withdraw->getAmount() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'transaction_id__x' ), $tnx->getReferenceNo() ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'authorized__by' ), $authorizedBy->getDisplayName() ); ?></p>
						    </div>
						    <div class="col-sm-12">
							    <h4><?= lang('payment_details'); ?></h4>
							    <p><?= $withdraw->getPaymentDetail(); ?></p>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>
		    <?php
	    } else if ( 'reject' === $withdraw->getStatus() ) {
		    $subject = sprintf( lang('x_canceled'), lang( 'withdrawal_request' ) ) . ' - ' . $this->Settings->site_name;
		    $authorizedBy = new Erp_User( $withdraw->getModifiedBy() );
		    ?>
		    <div class="modal-dialog modal-lg no-modal-header" style="width: 100%;max-width: 100%;">
			    <div class="modal-content">
				    <div class="modal-body print">
					    <div class="row padding10">
						    <div class="col-xs-12">
							    <h2 class="">Hi <?= $user->getDisplayName(); ?>,</h2>
						    </div>
						    <div class="col-xs-12">
							    <p>Sorry your withdrawal request got canceled. Please check the details below and contact us if necessary.</p>
						    </div>
					    </div>
					    <div class="row">
						    <div class="col-sm-12">
							    <p style="font-weight:bold;"><?= sprintf( lang( 'reference_no__x' ), $withdraw->getReferenceNo() ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'applied_on_x' ), $this->rerp->hrsd( $withdraw->getRequestDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'status__x' ), $statuses[ $withdraw->getStatus() ] ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'updated_on_x' ), $this->rerp->hrsd( $withdraw->getModifiedDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'withdrawal_amount___x' ), $this->rerp->convertMoney( $withdraw->getAmount() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'authorized__by' ), $authorizedBy->getDisplayName() ); ?></p>
						    </div>
						    <div class="col-sm-12">
							    <h4><?= lang('payment_details'); ?></h4>
							    <p><?= $withdraw->getPaymentDetail(); ?></p>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>
		    <?php
	    }
	    $message = ob_get_clean();
	    
	    if ( empty( $subject ) || empty( $message ) ) {
	    	return false;
	    }
	    
	    return [ $subject, $message ];
    }
	
	/**
	 * @param Erp_Wallet_Withdraw $withdraw
	 * @param Erp_User            $user
	 * @param                     $updated
	 *
	 * @return bool|array
	 */
    protected function get_admin_notification( $withdraw, $user, $updated = false ) {
    	
	    $statuses = $this->withdrawal_statuses();
	    $subject = '';
	    ob_start();
	    if ( $updated ) {
		    $subject = sprintf( lang('x_updated'), lang( 'withdrawal_request' ) ) . ' - ' . $this->Settings->site_name;
		    ?>
		    <div class="modal-dialog modal-lg no-modal-header" style="width: 100%;max-width: 100%;">
			    <div class="modal-content">
				    <div class="modal-body print">
					    <div class="row padding10">
						    <div class="col-xs-12">
							    <h2 class="">Hi Admin,</h2>
						    </div>
						    <div class="col-xs-12">
							    <p>Withdrawal request #<?= $withdraw->getReferenceNo(); ?> has been updated. Please check the details below.</p>
						    </div>
					    </div>
					    <div class="row">
						    <div class="col-sm-12">
							    <p style="font-weight:bold;"><?= sprintf( lang( 'reference_no__x' ), $withdraw->getReferenceNo() ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'applied_on_x' ), $this->rerp->hrsd( $withdraw->getRequestDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'status__x' ), $statuses[ $withdraw->getStatus() ] ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'updated_on_x' ), $this->rerp->hrsd( $withdraw->getModifiedDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'withdrawal_amount___x' ), $this->rerp->convertMoney( $withdraw->getAmount() ) ); ?></p>
						    </div>
						    <div class="col-sm-12">
							    <h4><?= lang('payment_details'); ?></h4>
							    <p><?= $withdraw->getPaymentDetail(); ?></p>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>
		    <?php
	    } else if ( 'applied' === $withdraw->getStatus() ) {
		    $subject = lang('new_withdrawal_request') . ' - ' . $this->Settings->site_name;
		    ?>
		    <div class="modal-dialog modal-lg no-modal-header" style="width: 100%;max-width: 100%;">
			    <div class="modal-content">
				    <div class="modal-body print">
					    <div class="row padding10">
						    <div class="col-xs-12">
							    <h2 class="">Hi Admin,</h2>
						    </div>
						    <div class="col-xs-12">
							    <p>There's new withdrawal (#<?= $withdraw->getReferenceNo(); ?>) request pending for your approval .<br>Please check the details below.</p>
						    </div>
					    </div>
					    <div class="row">
						    <div class="col-sm-12">
							    <p style="font-weight:bold;"><?= sprintf( lang( 'reference_no__x' ), $withdraw->getReferenceNo() ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'applied_on_x' ), $this->rerp->hrsd( $withdraw->getRequestDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'status__x' ), $statuses[ $withdraw->getStatus() ] ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'updated_on_x' ), $this->rerp->hrsd( $withdraw->getModifiedDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'withdrawal_amount___x' ), $this->rerp->convertMoney( $withdraw->getAmount() ) ); ?></p>
						    </div>
						    <div class="col-sm-12">
							    <h4><?= lang('payment_details'); ?></h4>
							    <p><?= $withdraw->getPaymentDetail(); ?></p>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>
		    <?php
	    } else if ( 'approved' === $withdraw->getStatus() ) {
		    $subject = sprintf( lang( 'withdrawal_request_x_has_been_approved' ), '#' . $withdraw->getReferenceNo() ) . ' - ' . $this->Settings->site_name;
		    $authorizedBy = new Erp_User( $withdraw->getApprovedBy() );
		    $tnx = new Erp_Transaction( $withdraw->getTransactionId() );
		    ?>
		    <div class="modal-dialog modal-lg no-modal-header" style="width: 100%;max-width: 100%;">
			    <div class="modal-content">
				    <div class="modal-body print">
					    <div class="row padding10">
						    <div class="col-xs-12">
							    <h2 class="">Hi Admin,</h2>
						    </div>
						    <div class="col-xs-12">
							    <p>Withdrawal request #<?= $withdraw->getReferenceNo(); ?> has been approved.<br>Please check below for more details.</p>
						    </div>
					    </div>
					    <div class="row">
						    <div class="col-sm-12">
							    <p style="font-weight:bold;"><?= sprintf( lang( 'reference_no__x' ), $withdraw->getReferenceNo() ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'applied_on_x' ), $this->rerp->hrsd( $withdraw->getRequestDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'status__x' ), $statuses[ $withdraw->getStatus() ] ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'approved_on_x' ), $this->rerp->hrsd( $withdraw->getApprovedDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'withdrawal_amount___x' ), $this->rerp->convertMoney( $withdraw->getAmount() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'transaction_id__x' ), $tnx->getReferenceNo() ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'authorized__by' ), $authorizedBy->getDisplayName() ); ?></p>
						    </div>
						    <div class="col-sm-12">
							    <h4><?= lang('payment_details'); ?></h4>
							    <p><?= $withdraw->getPaymentDetail(); ?></p>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>
		    <?php
	    } else if ( 'reject' === $withdraw->getStatus() ) {
		    $subject = sprintf( lang( 'withdrawal_request_x_has_been_canceled' ), '#' . $withdraw->getReferenceNo() ) . ' - ' . $this->Settings->site_name;
		    $authorizedBy = new Erp_User( $withdraw->getModifiedBy() );
		    ?>
		    <div class="modal-dialog modal-lg no-modal-header" style="width: 100%;max-width: 100%;">
			    <div class="modal-content">
				    <div class="modal-body print">
					    <div class="row padding10">
						    <div class="col-xs-12">
							    <h2 class="">Hi <?= $user->getDisplayName(); ?>,</h2>
						    </div>
						    <div class="col-xs-12">
							    <p>Withdrawal request #<?= $withdraw->getReferenceNo(); ?> has been canceled.<br>Please check the details below and contact us if necessary.</p>
						    </div>
					    </div>
					    <div class="row">
						    <div class="col-sm-12">
							    <p style="font-weight:bold;"><?= sprintf( lang( 'reference_no__x' ), $withdraw->getReferenceNo() ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'applied_on_x' ), $this->rerp->hrsd( $withdraw->getRequestDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'status__x' ), $statuses[ $withdraw->getStatus() ] ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'updated_on_x' ), $this->rerp->hrsd( $withdraw->getModifiedDate() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'withdrawal_amount___x' ), $this->rerp->convertMoney( $withdraw->getAmount() ) ); ?></p>
							    <p style="font-weight:bold;"><?= sprintf( lang( 'authorized__by' ), $authorizedBy->getDisplayName() ); ?></p>
						    </div>
						    <div class="col-sm-12">
							    <h4><?= lang('payment_details'); ?></h4>
							    <p><?= $withdraw->getPaymentDetail(); ?></p>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>
		    <?php
	    }
	    $message = ob_get_clean();
	    
	    if ( empty( $subject ) || empty( $message ) ) {
	    	return false;
	    }
	    
	    return [ $subject, $message ];
    }
    
    public function getWalletList() {
    	$this->rerp->checkPermissions( 'list' );
    	$actions = "<a href='" . admin_url('wallet/details/$1') . "' class='tip' title='" . lang( 'view_details' ) . "'><i class=\"fa fa-eye\"></i></a>";
    	$actions = "<div class=\"text-center\">{$actions}</div>";
        $this->load->library('datatables');
        $this->datatables
        ->select('user_id as id, users.username as name, amount as balance, updated as date', false )
        ->from('wallet w')
        ->join('users', 'users.id = user_id')
        // ->join('companies c', 'c.id = users.company_id')
        ->add_column('Actions', $actions, 'id')
        ->unset_column( 'id' );
        
        echo $this->datatables->generate();
    }
    
    public function details( $user_id = null ) {
	    if ( $user_id == $this->session->userdata( 'user_id' ) ) {
		    admin_redirect( 'wallet' );
	    }
    	$this->rerp->checkPermissions( 'list' );
        if($user_id == null){
            admin_redirect( 'wallet/list' );
        }
        
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );
        $bc                  = [
            [
                'link' => base_url(),
                'page' => lang( 'home' ),
            ],
            [
                'link' => admin_url( 'wallet/list' ),
                'page' => lang( 'wallet_list' ),
            ],
            [
                'link' => admin_url( 'wallet/details/'.$user_id ),
                'page' => lang( 'user_wallet_details' ),
            ],
        ];
        $meta                = [
            'page_title' => lang( 'user_wallet_details' ),
            'bc'         => $bc,
        ];
        
        $this->data['user']       = new Erp_User( $user_id );
	    $wallet = Erp_Wallet::get_user_wallet($user_id);
        $this->data['wallet'] = $wallet;
	    $this->data['can_withdraw'] = $this->can_withdraw( $wallet );
	    $this->data['has_pending_request'] = $wallet->has_pending_withdrawal();
        $this->page_construct( 'wallet/details', $meta, $this->data );
    }
        
    public function getWalletDetails( $user_id, $is_my_wallet = false ) {
        if($is_my_wallet === false){
            $this->rerp->checkPermissions( 'withdrawal_list' );
        }
        
        $this->load->library('datatables');
        $this->datatables
        ->select('rerp_transactions.transaction_date as transaction_date, rerp_transactions.description as description, rerp_transactions.debit as debit, rerp_transactions.credit as credit')
        ->from('rerp_transactions')
        ->where('rerp_transactions.user_id', $user_id)
        ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('auth/profile/$1') . "' class='tip' title='" . lang('view_user_profile') . "'><i class=\"fa fa-edit\"></i></a></div>", $user_id);
        
        
        echo $this->datatables->generate();
    }
	
	protected function withdrawal_types( $to_string = false ) {
		$types = [
			'cash'     => lang( 'cash' ),
			'purchase' => lang( 'purchase' ),
			'bank'     => lang( 'bank' ),
			'check'    => lang( 'check' ),
			'other'    => lang( 'other' ),
		];
		return $to_string ? implode( ',', array_keys( $types ) ) : $types;
	}
	
	protected function withdrawal_statuses( $to_string = false ) {
		$statuses = [
			'applied'  => lang( 'applied' ),
			'approved' => lang( 'approved' ),
			'reject'   => lang( 'reject' ),
		];
		
		return $to_string ? implode( ',', array_keys( $statuses ) ) : $statuses;
	}
    
    public function geMyWalletDetails ( $user_id ){
        $this->getWalletDetails($user_id, true );
    }
    
    protected function get_withdrawal_notification_template() {
    	ob_start();
    	?><!doctype html>
<html>
<head>
	<meta name="viewport" content="width=device-width">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>RetailErp - Email Container</title>
	{stylesheet}
	<style>
		* { font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0; } img { max-width: 100%; } body { -webkit-font-smoothing: antialiased; height: 100%; -webkit-text-size-adjust: none; width: 100% !important; } a { color: #348eda; } .btn-primary { Margin-bottom: 10px; width: auto !important; } .btn-primary td { background-color: #62cb31; border-radius: 3px; font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; font-size: 14px; text-align: center; vertical-align: top; } .btn-primary td a { background-color: #62cb31; border: solid 1px #62cb31; border-radius: 3px; border-width: 4px 20px; display: inline-block; color: #ffffff; cursor: pointer; font-weight: bold; line-height: 2; text-decoration: none; } .last { margin-bottom: 0; } .first { margin-top: 0; } .padding { padding: 10px 0; } table.body-wrap { padding: 20px; width: 100%; } table.body-wrap .container { border: 1px solid #e4e5e7; } table.footer-wrap { clear: both !important; width: 100%; } .footer-wrap .container p { color: #666666; font-size: 12px; } table.footer-wrap a { color: #999999; } h1, h2, h3 { color: #111111; font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; font-weight: 200; line-height: 1.2em; margin: 10px 0 10px; } h1 { font-size: 36px; } h2 { font-size: 28px; } h3 { font-size: 22px; } p, ul, ol {font-size: 14px;font-weight: normal;margin-bottom: 10px;} ul li, ol li {margin-left: 5px;list-style-position: inside;} .container { clear: both !important; display: block !important; Margin: 0 auto !important; max-width: 600px !important; } .body-wrap .container { padding: 40px; } .content { display: block; margin: 0 auto; max-width: 600px; } .content table { width: 100%; }
	</style>
</head>

<body bgcolor="#f7f9fa">
<table class="body-wrap" bgcolor="#f7f9fa">
	<tr>
		<td></td>
		<td class="container" bgcolor="#FFFFFF">
			<div class="content">
				<table>
					<tr>
						<td><h2>{logo}</h2></td>
					</tr>
					<tr>
						<td>
							<div style="clear:both;height:15px;"></div>
							<strong>{heading}</strong>
							<div style="clear:both;height:15px;"></div>
							{msg}
							<div style="clear:both;height:25px;"></div>
							<strong>{site_name}</strong>
							<p>{site_link}</p>
							<div style="clear:both;height:15px;"></div>
							<p style="border-top:1px solid #CCC;margin-bottom:0;">This email is sent to {name} ({email}).</p>
						</td>
					</tr>
				</table>
			</div>
		
		</td>
		<td></td>
	</tr>
</table>
</body>
</html><?php
	    return ob_get_clean();
    }
}
