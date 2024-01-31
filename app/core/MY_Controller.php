<?php
defined('BASEPATH') or exit('No direct script access allowed');

if ( ! trait_exists( 'MY_Controller_Trait' ) ) {
	require 'MY_Controller_Trait.php';
}
if ( ! trait_exists( 'MY_Order_Trait' ) ) {
	require 'MY_Order_Trait.php';
}

/**
 * Class MY_Controller
 *
 */
class MY_Controller extends CI_Controller {
	
	use MY_Controller_Trait;
	
    public function __construct() {
        parent::__construct();
        
	    $this->setupCommonController( false, true );
	    
	    if ( $this->loggedIn ) {
            //$this->default_currency = $this->Settings->currency_code;
            //$this->data['default_currency'] = $this->default_currency;
		    
            $this->data['dt_lang'] = json_encode( lang( 'datatables_lang' ) );
	        $this->data['dp_lang'] = json_encode( [
	            'days'        => [
		            lang( 'cal_sunday' ),
		            lang( 'cal_monday' ),
		            lang( 'cal_tuesday' ),
		            lang( 'cal_wednesday' ),
		            lang( 'cal_thursday' ),
		            lang( 'cal_friday' ),
		            lang( 'cal_saturday' ),
		            lang( 'cal_sunday' ),
	            ],
	            'daysShort'   => [
		            lang( 'cal_sun' ),
		            lang( 'cal_mon' ),
		            lang( 'cal_tue' ),
		            lang( 'cal_wed' ),
		            lang( 'cal_thu' ),
		            lang( 'cal_fri' ),
		            lang( 'cal_sat' ),
		            lang( 'cal_sun' ),
	            ],
	            'daysMin'     => [
		            lang( 'cal_su' ),
		            lang( 'cal_mo' ),
		            lang( 'cal_tu' ),
		            lang( 'cal_we' ),
		            lang( 'cal_th' ),
		            lang( 'cal_fr' ),
		            lang( 'cal_sa' ),
		            lang( 'cal_su' ),
	            ],
	            'months'      => [
		            lang( 'cal_january' ),
		            lang( 'cal_february' ),
		            lang( 'cal_march' ),
		            lang( 'cal_april' ),
		            lang( 'cal_may' ),
		            lang( 'cal_june' ),
		            lang( 'cal_july' ),
		            lang( 'cal_august' ),
		            lang( 'cal_september' ),
		            lang( 'cal_october' ),
		            lang( 'cal_november' ),
		            lang( 'cal_december' ),
	            ],
	            'monthsShort' => [
		            lang( 'cal_jan' ),
		            lang( 'cal_feb' ),
		            lang( 'cal_mar' ),
		            lang( 'cal_apr' ),
		            lang( 'cal_may' ),
		            lang( 'cal_jun' ),
		            lang( 'cal_jul' ),
		            lang( 'cal_aug' ),
		            lang( 'cal_sep' ),
		            lang( 'cal_oct' ),
		            lang( 'cal_nov' ),
		            lang( 'cal_dec' ),
	            ],
	            'today'       => lang( 'today' ),
	            'suffix'      => [],
	            'meridiem'    => [],
            ] );
        }
    }
	
	public function page_construct( $page, $meta = [], $data = [] ) {
		
		$meta['alert_messages']      = [
			'message' => $data['message'] ?? $this->session->flashdata( 'message' ),
			'error'   => $data['error'] ?? $this->session->flashdata( 'error' ),
			'warning' => $data['warning'] ?? $this->session->flashdata( 'warning' ),
			'info'    => $data['info'] ?? $this->session->flashdata( 'info' ),
		];
		$meta['message']             = $data['message'] ?? $this->session->flashdata( 'message' );
		$meta['error']               = $data['error'] ?? $this->session->flashdata( 'error' );
		$meta['warning']             = $data['warning'] ?? $this->session->flashdata( 'warning' );
		$meta['info']                = $this->site->getNotifications();
		$meta['events']              = $this->site->getUpcomingEvents();
		$meta['ip_address']          = $this->input->ip_address();
		$meta['Owner']               = $data['Owner'];
		$meta['Admin']               = $data['Admin'];
		$meta['Supplier']            = $data['Supplier'];
		$meta['Customer']            = $data['Customer'];
		$meta['Settings']            = $data['Settings'];
		$meta['dateFormats']         = $data['dateFormats'];
		$meta['assets']              = $data['assets'];
		$meta['GP']                  = $data['GP'];
		$meta['qty_alert_num']       = $this->site->get_total_qty_alerts();
		$meta['exp_alert_num']       = $this->site->get_expiring_qty_alerts();
		$meta['shop_sale_alerts']    = SHOP ? $this->site->get_shop_sale_alerts() : 0;
		$meta['shop_payment_alerts'] = SHOP ? $this->site->get_shop_payment_alerts() : 0;
		$meta['admin_logo']          = $data['assets'] . 'images/retail-premier-logo.png';
		$meta['admin_icon']          = $data['assets'] . 'images/retail-premier-icon.png';
	    
	    if ( isset( $this->themeInfos['admin_logo' ] ) ) {
		    $file = $this->shopThemeDir . '/shop' . $this->themeInfos['admin_logo' ];
		    if ( file_exists( $file ) && is_readable( $file ) ) {
		    	$meta['admin_logo'] = $this->shopThemeURL . $this->themeInfos['admin_logo' ];
		    }
	    }
	    
	    if ( isset( $this->themeInfos['admin_icon' ] ) ) {
		    $file = $this->shopThemeDir . '/shop/' . $this->themeInfos['admin_icon' ];
		    if ( file_exists( $file ) && is_readable( $file ) ) {
			    $meta['admin_icon'] = $this->shopThemeURL . $this->themeInfos['admin_icon' ];
		    }
	    }
		
	    $this->load->view( $this->theme . 'header', $meta );
	    $this->load->view( $this->theme . $page, $data );
	    $this->load->view( $this->theme . 'footer' );
    }
}
