<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Commission
 * 
 */
class Commission extends MY_Controller {
	
    public function __construct() {
        parent::__construct();
        
	    $this->onlyOwnerAllowed();
	
	    $this->load->admin_model( 'commission_model' );
	    
	    $this->load->model( [
	    	'Erp_Commission_Group',
		    'Erp_Commission_User',
		    'Erp_Referral_Commission',
		    'Erp_Shopper_Commission',
	    ] );
    }
    
    public function index() {
        redirect( admin_url( 'commission/groups' ) );
    }
    
    public function groups() {
    	
    	$this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );
	    $bc                  = [
		    [
			    'link' => base_url(),
			    'page' => lang( 'home' ),
		    ],
		    [
			    'link' => admin_url( 'commission/groups' ),
			    'page' => lang( 'commission_groups' ),
		    ],
	    ];
	    $meta                = [
		    'page_title' => lang( 'commission_groups' ),
		    'bc'         => $bc,
	    ];
	
	    $this->data['categories'] = $this->build_category_dropdown_options(
	    	null,
		    null,
		    $this->input->post( 'category_id', true ),
		    1,
		    0
	    );
        
        $this->page_construct( 'commission/groups', $meta, $this->data );
    }
    
    public function add_group() {
        $this->form_validation->set_rules('name', lang('group_name'), 'trim|required|is_unique[commission_groups.name]|alpha_numeric_spaces');
        $this->form_validation->set_rules('description', lang('description'), 'trim');
        $this->form_validation->set_rules('rate', lang('rate'), 'trim|required|numeric');
        $this->form_validation->set_rules('category_id', lang('category'), 'trim|required|numeric|is_unique[commission_groups.category_id]');
        $this->form_validation->set_rules('rate', lang('rate'), 'trim|required');
        
        if ( $this->form_validation->run() == true ) {
            $group = new Erp_Commission_Group();
            $group->setName( $this->input->post( 'name', true ) );
            $group->setDescription( $this->input->post( 'description', true ) );
            $group->setRate( $this->input->post( 'rate', true ) );
            $group->setCategoryId( $this->input->post( 'category_id', true ) );
            if ( $group->save() ) {
                $this->session->set_flashdata('message', lang('group_added'));
	            admin_redirect('commission/edit_group/' . $group->getId() );
            } else {
                $this->session->set_flashdata('message', lang('failed_to_add_commission_group'));
            }
            admin_redirect('commission/groups');
        } elseif( $this->input->post( 'add_group' ) ) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('commission/groups');
        }
	
	    $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );
	    $bc                  = [
		    [
			    'link' => base_url(),
			    'page' => lang( 'home' ),
		    ],
		    [
			    'link' => admin_url( 'commission/groups' ),
			    'page' => lang( 'commission_groups' ),
		    ],
	    ];
	    $meta                = [
		    'page_title' => lang( 'add_commission_group' ),
		    'bc'         => $bc,
	    ];
	    $this->data['categories'] = $this->build_category_dropdown_options(
		    null,
		    null,
		    $this->input->post( 'category_id', true ),
		    1,
		    0
	    );
	    $this->data['group']      = new Erp_Commission_Group();
	    $this->page_construct( 'commission/add_group', $meta, $this->data );
    }
    
    public function edit_group( $id ) {
		$group = new Erp_Commission_Group( $id );
        if ( ! $group->getId() ) {
	        $this->session->set_flashdata( 'error', lang( 'invalid_commission_group' ) );
	        admin_redirect( 'commission/groups' );
        }
	    $shopper_commission = false;
	    if ( 'grocerant' === $this->getCurrentThemeName() ) {
		    $shopper_commission = true;
	    }
        $this->form_validation->set_rules('name', lang('group_name'), 'trim|required|alpha_numeric_spaces');
        $this->form_validation->set_rules('description', lang('description'), 'trim');
        $this->form_validation->set_rules('rate', lang('rate'), 'trim|required|numeric');
        $this->form_validation->set_rules('category_id', lang('category'), 'required');
	    $this->form_validation->set_rules( 'is_enabled', lang( 'status' ), 'required' );
        
        if ( $this->form_validation->run() == true ) {
            $group->setName( $this->input->post('name', true ) );
            $group->setRate( $this->input->post('rate', true ) );
            $group->setCategoryId( $this->input->post('category_id', true ) );
            $group->setDescription( $this->input->post('description', true ) );
            if ( $this->input->post( 'is_enabled' ) ) {
	            $group->setIsEnabled( $this->input->post( 'is_enabled' ) );
            }
            
            if ( $group->save() ) {
            	$referral = $this->input->post( 'referral', true );
            	if ( $referral && isset( $referral['rate'], $referral['note'], $referral['is_enabled'] ) ) {
            		$refCom = $group->getReferralCommission();
		            $refCom->setRate( (float) $referral['rate'] );
		            $refCom->setDescription( htmlentities( $referral['note'] ) );
		            $refCom->setIsEnabled( $referral['is_enabled'] );
		            $refCom->save();
	            }
	            if ( $shopper_commission ) {
		            $shopper = $this->input->post( 'shopper', true );
		            if ( $shopper && isset( $shopper['rate'], $shopper['note'], $shopper['is_enabled'] ) ) {
			            $shopperCom = $group->getShopperCommission();
			            $shopperCom->setRate( (float) $shopper['rate'] );
			            $shopperCom->setDescription( htmlentities( $shopper['note'] ) );
			            $shopperCom->setIsEnabled( $shopper['is_enabled'] );
			            $shopperCom->save();
		            }
	            }
	            
	            $users = $this->input->post( 'users' );
	            if ( ! empty( $users ) && is_array( $users ) ) {
		            foreach ( $users as $user_com ) {
			            if ( ! ( isset( $user_com['user_id'] ) && ! empty( $user_com['user_id'] ) ) ) {
			            	continue;
			            }
			            if ( ! ( isset( $user_com['rate'] ) && ! empty( $user_com['rate'] ) ) ) {
			            	continue;
			            }
						
			            $id = isset( $user_com['id'] ) && ! empty( $user_com['id'] ) ? absint( $user_com['id'] ) : null;
			            $userCom = new Erp_Commission_User( $id );
			            $userCom->setGroupId( $group->getId() );
			            $userCom->setUserId( absint( $user_com['user_id'] ) );
			            $userCom->setRate( absfloat( $user_com['rate'] ) );
			            $userCom->setIsEnabled( absfloat( $user_com['is_enabled'] ) );
			            if ( isset( $user_com['note'] ) && ! empty( $user_com['note'] ) ) {
				            $userCom->setDescription( xss_clean( $user_com['note'] ) );
			            }
			            $update = ! ! $userCom->getId();
			            if ( $userCom->save() ) {
				            $update = $update ? lang( 'x_updated' ) : lang( 'x_added' );
				            $this->session->set_flashdata( 'info', sprintf( $update, lang( 'user' ) ) );
			            }
		            }
	            } else {
		            $this->session->set_flashdata( 'warning', sprintf( lang( 'no_x_added' ), lang( 'users' ) ) );
	            }
	            $this->session->set_flashdata( 'message', sprintf( lang( 'x_updated' ), lang( 'group' ) ) );
	            admin_redirect('commission/groups');
            } else {
	            $this->session->set_flashdata( 'error', sprintf( lang( 'x_not_saved' ), lang( 'group' ) ) );
            }
	        admin_redirect('commission/groups');
        } elseif( $this->input->post( 'edit_group' ) ) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('commission/groups');
        }
        
        $this->data['error']       = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );
        $this->data['group']       = $group;
        $this->data['categories']  = $this->build_category_dropdown_options( null, null, $group->getCategoryId(), 1, 0 );
        $this->data['shopper_com'] = $shopper_commission;
	    $bc = [
		    [ 'link' => base_url(), 'page' => lang( 'home' ) ],
		    [ 'link' => admin_url( 'commission/groups' ), 'page' => lang( 'commission_group' ) ],
	        [ 'link' => admin_url( 'commission/edit_group/'.$id ), 'page' => lang( 'edit_group' ) ],
	    ];
	    $meta = [ 'page_title' => sprintf( lang( 'edit_x' ), lang( 'commission_group' ) ), 'bc' => $bc ];
	    $this->page_construct( 'commission/edit_group', $meta, $this->data );
    }
    
    public function getCommissionGroups() {
        $this->load->library('datatables');
        $this->datatables
        ->select('commission_groups.id as id, commission_groups.name as name, commission_groups.rate as rate, categories.name as category, is_enabled' )
        ->from('commission_groups')
        ->join('categories', 'categories.id = commission_groups.category_id')
        ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('commission/edit_group/$1') . "' class='tip' title='" . sprintf( lang('edit_x'), lang( 'group' ) ) . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . sprintf( lang('delete_x'), lang( 'group' ) ) . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('commission/delete_group/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        
        echo $this->datatables->generate();
    }
    
    public function delete_group ( $id ) {
        $group = new Erp_Commission_Group( $id );
        if ( $group->delete() ) {
            $this->rerp->send_json( [
                'error' => 0,
                'msg'   => sprintf( lang( 'x_deleted' ), lang( 'group' ) ),
            ] );
        } else {
            $this->rerp->send_json( [
                'error' => 1,
                'msg'   => sprintf( lang( 'error_deleting_x' ), lang( 'group' ) ),
            ] );
        }
    }
    
    public function delete_group_user() {
	    $this->form_validation->set_rules( 'group_id', lang( 'group' ), 'trim|required|numeric' );
	    $this->form_validation->set_rules( 'user_com_id', lang( 'user' ), 'trim|required|numeric' );
	    if ( $this->form_validation->run() == true ) {
		    $comUser = new Erp_Commission_User( absint( $this->input->post( 'user_com_id' ) ) );
		    if ( $comUser->getGroupId() == $this->input->post( 'group_id' ) && $comUser->delete() ) {
			    $this->rerp->send_json( [
				    'error' => 0,
				    'msg'   => sprintf( lang( 'x_deleted' ), lang( 'user' ) ),
			    ] );
		    } else {
			    $this->rerp->send_json( [
				    'error' => 1,
				    'msg'   => sprintf( lang( 'error_deleting_x' ), lang( 'user' ) ),
			    ] );
		    }
	    }
    }
    
    public function users(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [ [ 'link' => base_url(), 'page' => lang('home')], ['link' => admin_url('commission/users'), 'page' => lang( 'commission_users' ) ] ];
        $meta                = [ 'page_title' => lang('commission_users'), 'bc' => $bc ];
        $this->page_construct( 'commission/group_users', $meta, $this->data );
    }
    
    public function getCommissionUsers() {
        $this->load->library('datatables');
        $this->datatables
        ->select('rerp_commission_users.id as id, rerp_commission_groups.name as Groupname, rerp_users.username as username, rerp_commission_users.rate as rate')
        ->from('rerp_commission_users')
        ->join('rerp_commission_groups', 'rerp_commission_groups.id = rerp_commission_users.group_id')
        ->join('rerp_users', 'rerp_users.id = rerp_commission_users.user_id')
        ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('commission/edit_user/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_user') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_user') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('commission/delete_user/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }
    
    public function add_user( $group_id ) {
	    $this->form_validation->set_rules( 'user_id', lang( 'user' ), 'trim|required|numeric' );
	    $this->form_validation->set_rules( 'rate', lang( 'rate' ), 'trim|required|numeric' );
	    $this->form_validation->set_rules( 'description', lang( 'note' ), 'trim' );
	    $this->form_validation->set_rules( 'is_enabled', lang( 'status' ), 'required' );
        
        if ( $this->form_validation->run() == true ) {
	        $comUser = new Erp_Commission_User();
	        $comUser->setGroupId( $group_id );
	        $comUser->setUserId( $this->input->post( 'user_id', true ) );
	        $comUser->setRate( $this->input->post( 'rate', true ) );
	        $comUser->setDescription( $this->input->post( 'description', true ) );
	        $comUser->setIsEnabled( $this->input->post( 'is_enabled' ) );
	        if ( $comUser->save() ) {
		        $this->session->set_flashdata( 'message', lang( 'group_user_added' ) );
	        } else {
		        $this->session->set_flashdata( 'message', lang( 'failed_to_add_commission_group_user' ) );
	        }
	        admin_redirect( 'commission/edit_group/' . $group_id );
        } elseif( $this->input->post( 'add_user' ) ) {
            $this->session->set_flashdata('error', validation_errors());
	        admin_redirect( 'commission/edit_group/' . $group_id );
        }
        
        $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['modal_js']   = $this->site->modal_js();
        $this->data['group_id'] = $group_id;
        $this->data['user_list']  = $this->commission_model->getUsersIdName( $group_id );
        $this->load->view($this->theme . 'commission/add_group_user', $this->data);
    }
    
    public function edit_user( $id = null ) {
        $this->load->helper('security');
        // this canbe handle with a method for getion group data
        $q = $this->db->get_where('rerp_commission_users', ['id' => $id], 1);
        if ( $q->num_rows() > 0 ) {
            $com_user = $q->row();
        }
        
        $this->form_validation->set_rules('group_id', lang('Group name'), 'trim|required|numeric');
        $this->form_validation->set_rules('user_id', lang('Username'), 'trim|required|numeric');
        $this->form_validation->set_rules('rate', lang('rate'), 'trim|required|numeric');
        $this->form_validation->set_rules('description', lang('description'), 'trim');
        
        if ( $this->form_validation->run() == true ) {
            $group = new Erp_Commission_User();
            
            $data = [
                'group_id' => $this->input->post('group_id'),
                'user_id'             => $this->input->post('user_id'),
                'rate'                => $this->input->post('rate'),
                'description'         => $this->input->post('description'),
            ];
            
            if ( $group->save() ) {
                $this->session->set_flashdata('message', lang('user_edited'));
            } else {
                $this->session->set_flashdata('message', lang('failed_to_edit_commission_group_user'));
            }
            admin_redirect('commission/users');
        } elseif( $this->input->post( 'edit_user' ) ) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('commission/users');
        }
        
        $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['modal_js']   = $this->site->modal_js();
        $this->data['com_user'] = $com_user;
        $this->data['group_list'] = $this->commission_model->getCommissionGroupsIdName();
        $this->data['user_list'] = $this->commission_model->getUsersIdName();
        $this->load->view($this->theme . 'commission/edit_group_user', $this->data);
    }
    
    public function delete_user ( $id ) {
        $area = new Erp_Commission_User( $id );
        if ( $area->delete() ) {
            $this->rerp->send_json( [
                'error' => 0,
                'msg'   => sprintf( lang( 'x_deleted' ), lang( 'user' ) ),
            ] );
        } else {
            $this->rerp->send_json( [
                'error' => 1,
                'msg'   => sprintf( lang( 'error_deleting_x' ), lang( 'user' ) ),
            ] );
        }
    }
    
}
