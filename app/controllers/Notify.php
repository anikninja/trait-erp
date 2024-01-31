<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Notify
 * @property CI_Output $output
 * @property CI_Session $session
 * @property MY_LANG $lang
 */
class Notify extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->admin_load('rerp');
    }

    public function csrf($msg = null)
    {
        $data['page_title'] = lang('csrf_error');
        if (!$msg) {
            $msg = lang('cesr_error_msg');
        }
        $this->session->set_flashdata('error', $msg);
        redirect('/', 'location');
    }

    public function error_404()
    {
    	if ( preg_match( '/\/api\/v\d+\//', array_key_first( $_REQUEST ), $m ) > 0 ) {
		    $this->output->set_status_header( 404 );
		    $this->output->set_content_type( 'json' );
		    $data = json_encode( [
			    'status'  => false,
			    'error' => 'NOT_FOUND',
			    'message' => lang( 'api_error_404' ),
		    ] );
		    $this->output->set_output( $data );
	    } else {
		    $url = $this->session->userdata('requested_page') ?? isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
		    if ( $this->session->userdata('requested_page') ) {
			    $this->session->unset_userdata('requested_page');
		    }
		    $this->session->set_flashdata( 'error', lang( 'error_404_message' ) . ( $url ? $url : '' ) );
		    redirect('/');
	    }
    }

    public function offline($msg = null)
    {
        $data['page_title'] = lang('site_offline');
        $data['msg']        = $msg;
        $this->load->view('default/notify', $data);
    }

    public function payment()
    {
        $data['page_title'] = lang('payment');
        $data['msg']        = lang('info');
        $data['msg1']       = lang('payment_processing');
        $this->load->view('default/notify', $data);
    }

    public function payment_failed($msg = null)
    {
        $data['page_title'] = lang('payment');
        $data['msg']        = $msg ? $msg : lang('error');
        $data['msg1']       = lang('payment_failed');
        $this->load->view('default/notify', $data);
    }

    public function payment_success($msg = null)
    {
        $data['page_title'] = lang('payment');
        $data['msg']        = $msg ? $msg : lang('thank_you');
        $data['msg1']       = lang('payment_added');
        $this->load->view('default/notify', $data);
    }
}
