<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Shop_settings
 * @property Shop_admin_model $shop_admin_model
 */
class Shop_settings extends MY_Controller {
    public function __construct()
    {
        parent::__construct();
	
	    if ( ! $this->loggedIn ) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->rerp->md('login');
        }

        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect('admin');
        }
        $this->lang->admin_load('front_end', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('shop_admin_model');
        $this->upload_path       = 'assets/uploads/';
        $this->image_types       = 'gif|jpg|jpeg|png';
        $this->allowed_file_size = '1024';
    }

    public function add_page()
    {
        $this->form_validation->set_rules('name', lang('name'), 'required|max_length[15]');
        $this->form_validation->set_rules('title', lang('title'), 'required|max_length[60]');
        $this->form_validation->set_rules('description', lang('description'), 'required');
        $this->form_validation->set_rules('body', lang('body'), 'required');
        $this->form_validation->set_rules('order_no', lang('order_no'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('slug', lang('slug'), 'trim|required|is_unique[pages.slug]|alpha_dash');
        if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'title'       => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'body'        => $this->input->post('body', true),
                'slug'        => $this->input->post('slug'),
                'order_no'    => $this->input->post('order_no'),
                'active'      => $this->input->post('active') ? $this->input->post('active') : 0,
                'updated_at'  => date('Y-m-d H:i:s'),
            ];
        }

        if ($this->form_validation->run() == true && $this->shop_admin_model->addPage($data)) {
            $this->session->set_flashdata('message', lang('page_added'));
            admin_redirect('shop_settings/pages');
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('add_page')]];
            $meta                = ['page_title' => lang('add_page'), 'bc' => $bc];
            $this->page_construct('shop/add_page', $meta, $this->data);
        }
    }

    public function delete_page($id = null)
    {
        if ($this->shop_admin_model->deletePage($id)) {
            $this->rerp->send_json(['error' => 0, 'msg' => lang('page_deleted')]);
        }
    }

    public function edit_page($id = null)
    {
        $page = $this->shop_admin_model->getPageByID($id);
        $this->form_validation->set_rules('name', lang('name'), 'required|max_length[15]');
        $this->form_validation->set_rules('title', lang('title'), 'required|max_length[60]');
        $this->form_validation->set_rules('description', lang('description'), 'required');
        $this->form_validation->set_rules('body', lang('body'), 'required');
        $this->form_validation->set_rules('order_no', lang('order_no'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('slug', lang('slug'), 'trim|required|alpha_dash');
        if ($page->slug != $this->input->post('slug')) {
            $this->form_validation->set_rules('slug', lang('slug'), 'is_unique[pages.slug]');
        }
        if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'title'       => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'body'        => $this->input->post('body', true),
                'slug'        => $this->input->post('slug'),
                'order_no'    => $this->input->post('order_no'),
                'active'      => $this->input->post('active') ? $this->input->post('active') : 0,
                'updated_at'  => date('Y-m-d H:i:s'),
            ];
        }

        if ($this->form_validation->run() == true && $this->shop_admin_model->updatePage($id, $data)) {
            $this->session->set_flashdata('message', lang('page_updated'));
            admin_redirect('shop_settings/pages');
        } else {
            $this->data['page']  = $page;
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('edit_page')]];
            $meta                = ['page_title' => lang('edit_page'), 'bc' => $bc];
            $this->page_construct('shop/edit_page', $meta, $this->data);
        }
    }

    public function getPages()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, name, slug, active, order_no, title')
            ->from('pages')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('shop_settings/edit_page/$1') . "' class='tip' title='" . lang('edit_page') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_page') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('shop_settings/delete_page/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }

    public function index()
    {
        $this->form_validation->set_rules('shop_name', lang('shop_name'), 'trim|required');
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'trim|required');
        $this->form_validation->set_rules('biller', lang('biller'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');
        $this->form_validation->set_rules('products_description', lang('products_description'), 'trim|required');
        $this->form_validation->set_rules('delivery_slot_offset', lang('delivery_slot_offset'), 'less_than_equal_to[12]');
        $this->form_validation->set_rules('wallet_percentage_cart', lang('wallet_percentage_cart'), 'less_than_equal_to[100]');

        if ($this->form_validation->run() == true) {
            $data = [
            	'shop_name'            => DEMO ? 'Retail SHOP' : $this->input->post('shop_name'),
                'description'          => DEMO ? 'RtailERP - Demo Ecommerce Shop that would help you to sell your products from your site. Locked on demo.' : $this->input->post('description'),
                'products_description' => DEMO ? 'This is products page description and is locked on demo.' : $this->input->post('products_description'),
                'warehouse'            => $this->input->post('warehouse'),
                'biller'               => $this->input->post('biller'),
                'about_link'           => $this->input->post('about_link'),
                'terms_link'           => $this->input->post('terms_link'),
                'privacy_link'         => $this->input->post('privacy_link'),
                'contact_link'         => $this->input->post('contact_link'),
                'payment_text'         => $this->input->post('payment_text'),
                'follow_text'          => $this->input->post('follow_text'),
                'facebook'             => $this->input->post('facebook'),
                'twitter'              => $this->input->post('twitter'),
                'google_plus'          => $this->input->post('google_plus'),
                'instagram'            => $this->input->post('instagram'),
                'phone'                => $this->input->post('phone'),
                'email'                => $this->input->post('email'),
                'cookie_message'       => DEMO ? 'We use cookies to improve your experience on our website. By browsing this website, you agree to our use of cookies.' : $this->input->post('cookie_message'),
                'cookie_link'          => $this->input->post('cookie_link'),
                'shipping'             => $this->input->post('shipping'),
                //'bank_details'         => $this->input->post('bank_details'),
                'products_page'        => $this->input->post('products_page'),
                'hide0'                => (int) $this->input->post('hide0'),
                'hide_price'           => $this->input->post('hide_price'),
                'private'              => $this->input->post('private'),
                'stripe'               => $this->input->post('stripe'),
                'shop_address'         => $this->input->post('shop_address'),
                'mc_api'               => $this->input->post('mc_api'),
                'free_shipping'        => absfloat( $this->input->post('free_shipping') ),
                'minimum_order'        => absfloat( $this->input->post('minimum_order') ),
                'delivery_slot_offset' => absfloat( $this->input->post('delivery_slot_offset') ),
	            'wallet_percentage_cart' => absfloat( $this->input->post('wallet_percentage_cart') ),
            ];
	
	        if ( $_FILES['logo']['size'] > 0 ) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path . 'logos/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = '';
                $config['max_height']    = '';
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
		        if ( ! $this->upload->do_upload( 'logo' ) ) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $data['logo'] = $this->upload->file_name;
                }
            }
        }
	
	    if ( $this->form_validation->run() == true && $this->shop_admin_model->updateShopSettings( $data ) ) {
	        ci_delete_category_caches();
            $this->session->set_flashdata('message', lang('settings_updated'));
            admin_redirect('shop_settings');
        } else {
        	$pages                       = $this->shop_admin_model->getAllPages();
            $this->data['warehouses']    = $this->site->getAllWarehouses();
            $this->data['billers']       = $this->site->getAllCompanies('biller');
            $this->data['shop_settings'] = $this->shop_admin_model->getShopSettings();
            $this->data['error']         = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('shop_settings')]];
            $meta                        = ['page_title' => lang('shop_settings'), 'bc' => $bc];
            $this->data['pages']         = [ '' => lang( 'select' ) . ' ' . lang( 'page' ) ];
	        if ( $pages ) {
		        foreach ( $pages as $page ) {
			        $this->data['pages'][ $page->slug ] = $page->title;
		        }
	        }
            
            $this->page_construct('shop/index', $meta, $this->data);
        }
    }

    public function install_update($file, $m_version, $version)
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->load->helper('update');
        save_remote_file($file . '.zip');
        $this->rerp->unzip('./files/updates/' . $file . '.zip');
        if ($m_version) {
            $this->load->library('migration');
            if (!$this->migration->latest()) {
                $this->session->set_flashdata('error', $this->migration->error_string());
                admin_redirect('shop_settings/updates');
            }
        }
        $this->db->update('shop_settings', ['version' => $version], ['shop_id' => 1]);
        unlink('./files/updates/' . $file . '.zip');
        $this->session->set_flashdata('success', lang('update_done'));
        admin_redirect('shop_settings/updates');
    }

    public function page_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->shop_admin_model->deletePage($id);
                    }
                    $this->session->set_flashdata('message', lang('pages_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function pages() {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('shop_settings'), 'page' => lang('shop_settings')], ['link' => '#', 'page' => lang('pages')]];
        $meta = ['page_title' => lang('pages'), 'bc' => $bc];
        $this->page_construct('shop/pages', $meta, $this->data);
    }
    
    public function theme_settings() {
    	// this page generates a layout settings based on theme.php settings.
	    $settingsKeys = array_keys( $this->themeInfos['settings'] );
	    if ( $this->input->post() ) {
	    	foreach ( $settingsKeys as $key ) {
			    $data = $this->input->post( $key, true );
			    if ( ! empty( $data ) ) {
			        $data = ci_array_values_multi( $data );
				    $this->Erp_Options->updateOption( $this->shopThemeName . '_' . $key, $data );
			    }
		    }
	    	
	    	$this->Erp_Options->updateOption( 'custom_css', $this->input->post( 'custom_css', true ) );
	    	$this->Erp_Options->updateOption( 'custom_js', $this->input->post( 'custom_js', true ) );
	    	
		    $this->session->set_flashdata( 'message', lang( 'settings_saved' ) );
		    redirect($_SERVER['HTTP_REFERER']);
	    } else {
		    $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
		    $bc   = [
			    [ 'link' => base_url(), 'page' => lang( 'home' ) ],
			    [ 'link' => admin_url( 'shop_settings' ), 'page' => lang( 'shop_settings' ) ],
			    [ 'link' => '#', 'page' => lang( 'theme_settings' ) ],
		    ];
		    $meta = [ 'page_title' => lang( 'theme_settings' ), 'bc' => $bc ];
		    $this->themeInfos['settings'] = isset( $this->themeInfos['settings'] ) ? $this->themeInfos['settings'] : [];
		    $this->data['settings'] = [];
		    foreach ( $settingsKeys as $key ) {
			    $this->data['settings'][ $key ] = $this->Erp_Options->getOption( $this->shopThemeName . '_' . $key, [] );
		    }
		    $this->data['settings']['custom_css'] = $this->Erp_Options->getOption( 'custom_css', '' );
		    $this->data['settings']['custom_js'] = $this->Erp_Options->getOption( 'custom_js', '' );
		    $this->page_construct('shop/theme_settings', $meta, $this->data);
	    }
    }
    
    public function color_mappings() {
    	$optionName = 'color_mappings';
	    $this->form_validation->set_rules( '[colors]', lang( 'colors' ), 'required|xss_clean' );
	    if ( $this->form_validation->run( 'colors' ) == true ) {
	    	$data = $this->input->post( 'colors', true );
	    	foreach( $data as $k => &$color ) {
	    		$color = ci_parse_args( $color, [ 'code' => '', 'swatch' => '', 'file_type' => '' ] );
	    		var_dump( ( ! empty( $color['swatch'] ) && ! empty( $color['file_type'] ) ) );
	    		if ( ! empty( $color['swatch'] ) && ! empty( $color['file_type'] ) ) {
				    $color['swatch'] = $color['file_type'] . ';base64,' . $color['swatch'];
			    }
	    		unset( $color['file_type'] );
	    		if ( false === preg_match( '/^#[a-f0-9]{6}$/i', $color['code'] ) ) {
				    $color['code'] = '';
			    }
	    		if ( empty( $color['code'] ) && empty( $color['swatch'] ) ) {
				    unset( $data[$k] );
			    }
		    }
	     
		    if ( $this->Erp_Options->updateOption( $optionName, $data ) ) {
			    $this->session->set_flashdata( 'message', lang( 'settings_saved' ) );
		    } else {
			    $this->session->set_flashdata( 'error', lang( 'error_saving_settings' ) );
		    }
	    	
		    redirect($_SERVER['HTTP_REFERER']);
	    } else {
		    $this->load->config( 'colors' );
		    $colorHex       = $this->config->item( 'colors' );
		    $colors         = [];
		    $colorsValues   = $this->Erp_Options->getOption( $optionName, [] );
		    $productsColors = $this->shop_admin_model->getColorsFromVariations();
		    foreach( $productsColors as $k => $color ) {
			    $colors[ $k ] = [
				    'label' => $color,
				    'value' => isset( $colorsValues[ $k ] ) ? $colorsValues[ $k ] : ( isset( $colorHex[ $k ] ) ? $colorHex[ $k ] : '' ),
			    ];
		    }
		    
	    	$this->data['colors'] = $colors;
		    $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
		    $bc   = [
			    [ 'link' => base_url(), 'page' => lang( 'home' ) ],
			    [ 'link' => admin_url( 'shop_settings' ), 'page' => lang( 'shop_settings' ) ],
			    [ 'link' => '#', 'page' => lang( 'color_mappings' ) ],
		    ];
		    $meta = [ 'page_title' => lang( 'color_mappings' ), 'bc' => $bc ];
		    $this->page_construct('shop/color_mappings', $meta, $this->data);
	    }
    }
    
    public function get_theme_settings_field() {
    	// name, type, count
	    $this->form_validation->set_rules( 'name', lang( 'field_name_attribute' ), 'required|xss_clean' );
	    $this->form_validation->set_rules( 'type', lang( 'field_type' ), 'required|xss_clean' );
	    $this->form_validation->set_rules( 'count', lang( 'field_index' ), 'required|xss_clean' );
	    $output  = '';
	    $success = true;
	    $fn='';
	    if ( $this->form_validation->run() == true ) {
		    $type  = $this->input->post( 'type' );
		    $name  = $this->input->post( 'name' );
		    //$count = absint( $this->input->post( 'count' ) );
		    $fn = 'render_theme_settings_' . $type;
		    if ( is_callable( $fn ) ) {
		    	ob_start();
			    call_user_func( $fn, $name, '', true );
			    $output = ob_get_clean();
		    }
		    if ( empty( $output ) ) {
			    $output = lang( 'something_went_wrong' );
			    $success = false;
		    }
	    } else {
		    $output = validation_errors();
		    $success = false;
	    }
	    
    	$this->rerp->send_json( [ 'success' => $success, 'data' => $output, 'fn' => $fn, 'r' => $this->form_validation->run(), 'v' => validation_errors() ] );
    }

    public function menus( $slug = null, $id = null ) {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
	    
        $menus = new Erp_Menus();
	    $this->data['menus'] = $menus->getMenus( false, true );
	    $selectedParent = ( isset( $_POST['parent'] ) ? $_POST['parent'] : '' );
	    
	    if ( 'edit' === $slug && $id ) {
		    $menu = $menus->getMenu( $id );
		    $this->data['menu'] = $menu;
		    if ( ! $selectedParent ) {
			    $selectedParent = $menu->parent;
		    }
	    }
	    
	    if ( $this->themeInfos['sub_nav'] ) {
	        $this->data['parent_dropdown'] = $this->build_menu_dropdown_options( null, $id, $selectedParent );
		}
	    
	    if ( $slug == 'add' || $slug == 'edit' ) {
		    $this->load->helper('security');
		
		    $this->form_validation->set_rules( 'label', lang( 'Menu Label' ), 'required|xss_clean|min_length[2]' );
		    $this->form_validation->set_rules( 'slug', lang( 'Menu Slug/Path' ), 'required|xss_clean' );
		    $this->form_validation->set_rules( 'tip', lang( 'Menu Tooltip' ), 'xss_clean' );
		    $this->form_validation->set_rules( 'class', lang( 'Css Class' ), 'xss_clean' );
		    $this->form_validation->set_rules( 'order', lang( 'Sorting order' ), 'xss_clean' );
		    $this->form_validation->set_rules( 'parent', lang( 'Parent Menu' ), 'xss_clean|numeric' );
		    $this->form_validation->set_rules( 'add_menu', lang( 'Update Action' ), 'xss_clean' );
	    }
        
        if ( $slug == 'add' ) {
        	
	        if ( $this->form_validation->run() == true ) {
		        $menu = [
			        'parent' => absint( $this->input->post( 'parent' ) ),
			        'label'  => $this->input->post( 'label' ),
			        'slug'   => $this->input->post( 'slug' ),
			        'tip'    => $this->input->post( 'tip' ),
			        'class'  => $this->input->post( 'class' ),
			        'order'  => absint( $this->input->post( 'sort' ) ),
		        ];
		        
		        if ( $menus->addMenu( $menu ) ) {
			        $this->session->set_flashdata( 'message', lang( 'menu_added' ) );
			        admin_redirect( 'shop_settings/menus' );
		        } else {
			        $this->session->set_flashdata( 'error', lang( 'Unable to add the menu. Please try again after sometime.' ) );
		        }
		        admin_redirect( 'shop_settings/menus' );
	        } else if ( $this->input->post( 'add_menu' ) ) {
		        $this->session->set_flashdata( 'error', validation_errors() );
		        admin_redirect( 'shop_settings/menus' );
		        return;
	        } else {
		        $this->data['menu']     = new Erp_Menu_Item();
		        $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );
		        $this->data['modal_js'] = $this->site->modal_js();
		        $this->load->view( $this->theme . 'menus/add_menu', $this->data );
		        return;
	        }
        }
	    if ( $slug == 'edit' ) {
		    
		    if ( $this->form_validation->run() == true ) {
		    	if ( $id ) {
		    		$menu = [
					    'parent' => absint( $this->input->post( 'parent' ) ),
					    'label'  => $this->input->post( 'label' ),
					    'slug'   => $this->input->post( 'slug' ),
					    'tip'    => $this->input->post( 'tip' ),
					    'class'  => $this->input->post( 'class' ),
					    'order'  => absint( $this->input->post( 'sort' ) ),
				    ];
		    		
		    		if ( $menus->updateMenu( $menu, $id ) ) {
					    $this->session->set_flashdata( 'message', lang( 'menu_updated' ) );
					    admin_redirect( 'shop_settings/menus' );
				    }
			    }
			    $this->session->set_flashdata( 'error', lang( 'Unable to update the menu data. Please try again after sometime.' ) );
			    admin_redirect( 'shop_settings/menus' );
		    } else if ( $this->input->post( 'edit_menu' ) ) {
			    $this->session->set_flashdata( 'error', validation_errors() );
			    admin_redirect( 'shop_settings/menus' );
			    return;
		    } else {
			    if ( ! $id ) {
				    $this->session->set_flashdata( 'error', lang( 'Invalid_Request' ) );
			    }
			
			    $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			    
			    $this->data['id']       = $id;
			    $this->data['modal_js'] = $this->site->modal_js();
			    $this->load->view($this->theme . 'menus/edit_menu', $this->data);
			    return;
		    }
        }
	    if ( $slug == 'delete' ) {
	    	if ( $id ) {
		        $menus->deleteMenu( $id );
			    $this->session->set_flashdata( 'message', lang( 'Menu_Deleted' ) );
			    admin_redirect( 'shop_settings/menus' );
		    }
		    $this->session->set_flashdata( 'error', lang( 'Error_Deleting_Menu' ) );
		    admin_redirect( 'shop_settings/menus' );
	    }
	
	    $bc   = [
		    [ 'link' => base_url(), 'page' => lang( 'home' ) ],
		    [ 'link' => admin_url( 'shop_settings' ), 'page' => lang( 'shop_settings' ) ],
		    [ 'link' => '#', 'page' => lang( 'pages' ) ],
	    ];
	    $meta = [ 'page_title' => lang( 'menus' ), 'bc' => $bc ];
	    
        $this->page_construct('menus/index', $meta, $this->data);
    }
    
    public function menu_actions() {
    	$action = $this->input->post( 'form_action' );
    	if ( 'delete' === $action ) {
    		$vals = $this->input->post( 'val' );
    		if ( ! empty( $vals ) ) {
    			foreach( $vals as $id ) {
				    $this->Erp_Menus->deleteMenu( $id );
			    }
			    $this->session->set_flashdata( 'message', lang( 'menu_deleted' ) );
		    }
	    }
	    admin_redirect( 'shop_settings/menus' );
    }
	
	/**
	 * This only output the option tag.
	 *
	 * @param Erp_Menu_Item[] $menus  Menu list
	 * @param int      $currentItem selected menu id.
	 * @param int      $selected    selected menu id.
	 * @param int      $level       dept level. for tracking.
	 * @param int      $maxDept     Max Dept to display.
	 *
	 * @return string
	 */
	protected function build_menu_dropdown_options( $menus = null, $currentItem = null, $selected = 0, $level = 1, $maxDept = 6 ) {
		$output = '';
		if ( null === $menus ) {
			$menus = $this->Erp_Menus->getMenus( true );
		}
		foreach( $menus as $menu ) {
			if ( $currentItem == $menu->id ) {
				continue;
			}
			$output .= sprintf(
				'<option class="level-%s" value="%s"%s>%s</option>',
				$level,
				$menu->id,
				$selected == $menu->id ? ' selected' : '',
				str_repeat( '–', $level ) . '' . $menu->label
			);
			
			if ( $level <= $maxDept ) {
				$subs = $this->Erp_Menus->getChildren( $menu->id );
				if ( $subs ) {
					$output .= $this->build_menu_dropdown_options( $subs, $currentItem, $selected, $level + 1, $maxDept );
				}
			}
		}
		return $output;
	}
    
    public function send_sms($date = null)
    {
        $this->form_validation->set_rules('mobile', lang('mobile'), 'trim|required');
        $this->form_validation->set_rules('message', lang('message'), 'trim|required');
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        if ($this->form_validation->run() == true) {
            $this->load->library('sms');
            $res = $this->sms->send($this->input->post('mobile'), $this->input->post('message'));
            if (isset($res['error']) && $res['error']) {
                $this->data['error'] = lang('sms_request_failed');
            } else {
                $this->data['message'] = lang('sms_request_sent');
            }
        }

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('send_sms')]];
        $meta = ['page_title' => lang('send_sms'), 'bc' => $bc];
        $this->page_construct('shop/send_sms', $meta, $this->data);
    }

    public function sitemap()
    {
        $categories = $this->shop_admin_model->getAllCategories();
        $products   = $this->shop_admin_model->getAllProducts();
        $brands     = $this->shop_admin_model->getAllBrands();
        $pages      = $this->shop_admin_model->getAllPages();
        $map        = '<?xml version="1.0" encoding="UTF-8" ?>';

        $map .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $map .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 ';
        $map .= 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $map .= '<url>';
        $map .= '<loc>' . site_url() . '</loc> ';
        $map .= '<priority>1.0</priority>';
        $map .= '<changefreq>daily</changefreq>';
        // $map .= '<lastmod>'.date('Y-m-d').'</lastmod>';
        $map .= '</url>';
        // @TODO add recursive sub categories.
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $map .= '<url>';
                $map .= '<loc>' . site_url('category/' . $category->slug) . '</loc> ';
                $map .= '<priority>0.8</priority>';
                $map .= '</url>';
                $subcategories = $this->shop_admin_model->getSubCategories($category->id);
                if (!empty($subcategories)) {
                    foreach ($subcategories as $subcategory) {
                        $map .= '<url>';
                        $map .= '<loc>' . site_url('category/' . $category->slug . '/' . $subcategory->slug) . '</loc> ';
                        $map .= '<priority>0.8</priority>';
                        $map .= '</url>';
                    }
                }
            }
        }

        if (!empty($brands)) {
            foreach ($brands as $brand) {
                $map .= '<url>';
                $map .= '<loc>' . shop_url($brand->slug) . '</loc> ';
                $map .= '<priority>0.8</priority>';
                $map .= '</url>';
            }
        }

        if (!empty($products)) {
            foreach ($products as $products) {
                $map .= '<url>';
                $map .= '<loc>' . site_url('product/' . $products->slug) . '</loc> ';
                $map .= '<priority>0.6</priority>';
                $map .= '</url>';
            }
        }

        if (!empty($pages)) {
            foreach ($pages as $page) {
                $map .= '<url>';
                $map .= '<loc>' . site_url('page/' . $page->slug) . '</loc> ';
                $map .= '<priority>0.8</priority>';
                $map .= '<changefreq>yearly</changefreq>';
                if ($page->updated_at) {
                    $map .= '<lastmod>' . date('Y-m-d', strtotime($page->updated_at)) . '</lastmod>';
                }
                $map .= '</url>';
            }
        }

        $map .= '</urlset>';
        file_put_contents('sitemap.xml', $map);
        header('Location: ' . base_url('sitemap.xml'));
        exit;
    }

    public function slider() {
        // $this->form_validation->set_rules('image1', lang('image1'), 'trim|required');
        $this->form_validation->set_rules('caption1', lang('caption') . ' 1', 'trim|max_length[160]');
        // $this->form_validation->set_rules('image2', lang('image2'), 'trim|required');
        // $this->form_validation->set_rules('caption2', lang('caption2'), 'trim|max_length[160]');

        if ($this->form_validation->run() == true) {
            $uploaded = ['image1' => '', 'image2' => '', 'image3' => '', 'image4' => '', 'image5' => ''];
	        $this->load->library( 'upload' );
	        $config['upload_path']   = $this->upload_path;
	        $config['allowed_types'] = $this->image_types;
	        $config['max_size']      = $this->allowed_file_size;
	        $config['overwrite']     = false;
	        $config['max_filename']  = 25;
	        $config['encrypt_name']  = true;
	
	        $this->upload->initialize( $config );
	
	        $images = [ 'image1', 'image2', 'image3', 'image4', 'image5' ];
	
	        foreach ( $images as $image ) {
		        if ( isset( $_FILES[ $image ] ) && $_FILES[ $image ]['size'] > 0 ) {
			        if ( ! $this->upload->do_upload( $image ) ) {
				        $error = $this->upload->display_errors();
				        $this->session->set_flashdata( 'error', $error );
				        redirect( $_SERVER['HTTP_REFERER'] );
			        }
			        $uploaded[ $image ] = $this->upload->file_name;
			        // remove old.
			        if ( $this->input->post( 'file_' . $image ) ) {
				        if ( $uploaded[ $image ] !== $this->input->post( 'file_' . $image ) ) {
					        $del = $this->upload_path . $this->input->post( 'file_' . $image );
					        if ( file_exists( $del ) ) {
						        unlink( $del );
					        }
				        }
			        }
		        } else if ( $this->input->post( 'file_' . $image ) ) {
			        if ( $this->input->post( 'del_' . $image ) ) {
				        $del = $this->upload_path . $this->input->post( 'file_' . $image );
				        if ( file_exists( $del ) ) {
					        unlink( $del );
				        }
			        } else {
				        $uploaded[$image] = $this->input->post( 'file_' . $image );
			        }
		        }
	        }
	        $data = [];
	        for( $i=1; $i<5; $i++ ) {
		        $image = isset( $uploaded[ 'image' . $i ] ) ? $uploaded[ 'image' . $i ] : false;
		        $idx = ($i-1);
		        if ( 'del' === $image ) {
		            $data[ $idx ] = [ 'link'    => '', 'title'   => '', 'caption' => '', ];
		        } else {
		            $data[ $idx ] = [
			            'link'    => $this->input->post( 'link' . $i ),
			            'title'   => $this->input->post( 'title' . $i ),
			            'caption' => $this->input->post( 'caption' . $i ),
		            ];
		            if ( $image ) {
			            $data[ $idx ]['image'] = $image;
		            }
		        }
	        }
	        
	        usort( $data, function( $slide ) {
	        	return isset( $slide['image'] ) && ! empty( $slide['image'] ) ? 0 : 1;
	        } );
        }

        if ($this->form_validation->run() == true && $this->shop_admin_model->updateSlider($data)) {
            $this->session->set_flashdata('message', lang('silder_updated'));
            admin_redirect('shop_settings/slider');
        } else {
	        $shop_settings                 = $this->shop_admin_model->getShopSettings();
	        $this->data['slider_settings'] = json_decode( $shop_settings->slider );
	        $this->data['error']           = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );
	        $bc                            = [
		        [ 'link' => base_url(), 'page' => lang( 'home' ) ],
		        [ 'link' => admin_url( 'shop_settings' ), 'page' => lang( 'shop_settings' ) ],
		        [ 'link' => '#', 'page' => lang( 'slider_settings' ) ],
	        ];
	        $meta                          = [
		        'page_title' => lang( 'slider_settings' ),
		        'bc'         => $bc,
	        ];
            $this->page_construct('shop/slider', $meta, $this->data);
        }
    }

    public function slugify()
    {
        if ($products = $this->shop_admin_model->getAllProducts()) {
            $this->db->update('products', ['slug' => null]);
            foreach ($products as $product) {
                $slug = $this->rerp->slug($product->name);
                $this->db->update('products', ['slug' => $slug], ['id' => $product->id]);
            }
            $this->session->set_flashdata('message', lang('slugs_updated'));
            redirect($_SERVER['HTTP_REFERER'] ?? 'admin/shop_settings');
        }
        $this->session->set_flashdata('error', lang('no_product_found'));
        redirect($_SERVER['HTTP_REFERER'] ?? 'admin/shop_settings');
    }

    public function sms_log($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        $file = APPPATH . 'logs/' . 'sms-' . $date . '.log';
        if (file_exists($file)) {
            $log   = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
            $lines = explode("\n", $log);
            array_walk($lines, function (&$item) {
                if (strpos($item, 'SMS.ERROR') !== false) {
                    $item = "<span class='text-danger' style='white-space: normal;'>{$item}</span>";
                } else {
                    $item = "<span style='white-space: normal;'>{$item}</span>";
                }
            });
            $content = implode("\n\n", $lines);
        } else {
            $content = "<span class='text-danger'>" . lang('log_x_exists') . '</span>';
        }

        $this->data['log']   = $content;
        $this->data['date']  = $date;
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('sms_log')]];
        $meta                = ['page_title' => lang('sms_log'), 'bc' => $bc];
        $this->page_construct('shop/log', $meta, $this->data);
    }

    public function sms_settings()
    {
        // $this->form_validation->set_rules('auto_send', lang('auto_send'), 'trim|required');
        $this->form_validation->set_rules('gateway', lang('gateway'), 'trim|required');

        if ($this->input->post('gateway') == 'Custom') {
            $this->form_validation->set_rules('Custom_url', lang('url'), 'trim|required');
            $this->form_validation->set_rules('Custom_send_to_name', lang('send_to_name'), 'trim|required');
            $this->form_validation->set_rules('Custom_msg_name', lang('msg_name'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Clickatell') {
            $this->form_validation->set_rules('Clickatell_apiKey', lang('apiKey'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Gupshup') {
            $this->form_validation->set_rules('Gupshup_userid', lang('userid'), 'trim|required');
            $this->form_validation->set_rules('Gupshup_password', lang('password'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Itexmo') {
            $this->form_validation->set_rules('Itexmo_api_code', lang('api_code'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'MVaayoo') {
            $this->form_validation->set_rules('MVaayoo_user', lang('MVaayoo_user'), 'trim|required');
            $this->form_validation->set_rules('MVaayoo_senderID', lang('senderID'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'SmsAchariya') {
            $this->form_validation->set_rules('SmsAchariya_domain', lang('domain'), 'trim|required');
            $this->form_validation->set_rules('SmsAchariya_uid', lang('uid'), 'trim|required');
            $this->form_validation->set_rules('SmsAchariya_pin', lang('pin'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'SmsCountry') {
            $this->form_validation->set_rules('SmsCountry_user', lang('user'), 'trim|required');
            $this->form_validation->set_rules('SmsCountry_passwd', lang('passwd'), 'trim|required');
            $this->form_validation->set_rules('SmsCountry_sid', lang('sid'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'SmsLane') {
            $this->form_validation->set_rules('SmsLane_user', lang('user'), 'trim|required');
            $this->form_validation->set_rules('SmsLane_password', lang('password'), 'trim|required');
            $this->form_validation->set_rules('SmsLane_sid', lang('sid'), 'trim|required');
            $this->form_validation->set_rules('SmsLane_gwid', lang('gwid'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Nexmo') {
            $this->form_validation->set_rules('Nexmo_api_key', lang('api_key'), 'trim|required');
            $this->form_validation->set_rules('Nexmo_api_secret', lang('api_secret'), 'trim|required');
            $this->form_validation->set_rules('Nexmo_from', lang('from'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Twilio') {
            $this->form_validation->set_rules('Twilio_account_sid', lang('account_sid'), 'trim|required');
            $this->form_validation->set_rules('Twilio_auth_token', lang('auth_token'), 'trim|required');
            $this->form_validation->set_rules('Twilio_from', lang('from'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Mocker') {
            $this->form_validation->set_rules('Mocker_sender_id', lang('sender_id'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Infobip') {
            $this->form_validation->set_rules('Infobip_username', lang('username'), 'trim|required');
            $this->form_validation->set_rules('Infobip_password', lang('password'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Bulksms') {
            $this->form_validation->set_rules('Bulksms_eapi_url', lang('eapi_url'), 'trim|required');
            $this->form_validation->set_rules('Bulksms_username', lang('username'), 'trim|required');
            $this->form_validation->set_rules('Bulksms_password', lang('password'), 'trim|required');
        } elseif ($this->input->post('gateway') == 'Smsapi') {
            $this->form_validation->set_rules('Smsapi_access_token', lang('access_token'), 'trim|required');
            $this->form_validation->set_rules('Smsapi_from', lang('from'), 'trim|required');
        }

        if ($this->form_validation->run() == true) {
            $Custom = [
                'url'    => $this->input->post('Custom_url'),
                'params' => [
                    'send_to_name' => $this->input->post('Custom_send_to_name'),
                    'msg_name'     => $this->input->post('Custom_msg_name'),
                    'keys'         => [
                        'param1' => $this->input->post('Custom_param1_key'),
                        'param2' => $this->input->post('Custom_param2_key'),
                        'param3' => $this->input->post('Custom_param3_key'),
                        'param4' => $this->input->post('Custom_param4_key'),
                        'param5' => $this->input->post('Custom_param5_key'),
                    ],
                    'others' => [
                        $this->input->post('Custom_param1_key') => $this->input->post('Custom_param1_value'),
                        $this->input->post('Custom_param2_key') => $this->input->post('Custom_param2_value'),
                        $this->input->post('Custom_param3_key') => $this->input->post('Custom_param3_value'),
                        $this->input->post('Custom_param4_key') => $this->input->post('Custom_param4_value'),
                        $this->input->post('Custom_param5_key') => $this->input->post('Custom_param5_value'),
                    ],
                ],
            ];

            $data = [
                'gateway'    => DEMO ? 'Log' : $this->input->post('gateway'),
                'Custom'     => $Custom,
                'Clickatell' => [
                    'apiKey' => $this->input->post('Clickatell_apiKey'),
                ],
                'Gupshup' => [
                    'userid'   => $this->input->post('Gupshup_userid'),
                    'password' => $this->input->post('Gupshup_password'),
                ],
                'Itexmo'  => ['api_code' => $this->input->post('Itexmo_api_code')],
                'MVaayoo' => [
                    'user'     => $this->input->post('MVaayoo_user'),
                    'senderID' => $this->input->post('MVaayoo_senderID'),
                ],
                'SmsAchariya' => [
                    'domain' => $this->input->post('SmsAchariya_domain'),
                    'uid'    => $this->input->post('SmsAchariya_uid'),
                    'pin'    => $this->input->post('SmsAchariya_pin'),
                ],
                'SmsCountry' => [
                    'user'   => $this->input->post('SmsCountry_user'),
                    'passwd' => $this->input->post('SmsCountry_passwd'),
                    'sid'    => $this->input->post('SmsCountry_sid'),
                ],
                'SmsLane' => [
                    'user'     => $this->input->post('SmsLane_user'),
                    'password' => $this->input->post('SmsLane_password'),
                    'sid'      => $this->input->post('SmsLane_sid'),
                    'gwid'     => $this->input->post('SmsLane_gwid'),
                ],
                'Nexmo' => [
                    'api_key'    => $this->input->post('Nexmo_api_key'),
                    'api_secret' => $this->input->post('Nexmo_api_secret'),
                    'from'       => $this->input->post('Nexmo_from'),
                ],
                'Twilio' => [
                    'account_sid' => $this->input->post('Twilio_account_sid'),
                    'auth_token'  => $this->input->post('Twilio_auth_token'),
                    'from'        => $this->input->post('Twilio_from'),
                ],
                'Mocker'  => ['sender_id' => $this->input->post('Mocker_sender_id')],
                'Infobip' => [
                    'username' => $this->input->post('Infobip_username'),
                    'password' => $this->input->post('Infobip_password'),
                ],
                'Bulksms' => [
                    'eapi_url' => $this->input->post('Bulksms_eapi_url'),
                    'username' => $this->input->post('Bulksms_username'),
                    'password' => $this->input->post('Bulksms_password'),
                ],
                'Smsapi' => [
                    'access_token' => $this->input->post('Smsapi_access_token'),
                    'from'         => $this->input->post('Smsapi_from'),
                ],
            ];

            $sms_config = [
                'auto_send' => $this->input->post('auto_send'),
                'config'    => json_encode($data),
            ];
        }

        if ($this->form_validation->run() == true && $this->shop_admin_model->updateSmsSettings($sms_config)) {
            $this->session->set_flashdata('message', lang('settings_updated'));
            admin_redirect('shop_settings/sms_settings');
        } else {
            $sms_settings               = $this->site->getSmsSettings();
            $sms_settings->config       = json_decode($sms_settings->config);
            $this->data['sms_settings'] = $sms_settings;
            $this->data['error']        = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('sms_settings')]];
            $meta                       = ['page_title' => lang('sms_settings'), 'bc' => $bc];
            $this->page_construct('shop/sms_settings', $meta, $this->data);
        }
    }

    public function updates()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->form_validation->set_rules('purchase_code', lang('purchase_code'), 'required');
        $this->form_validation->set_rules('envato_username', lang('envato_username'), 'required');
        if ($this->form_validation->run() == true) {
            $this->db->update('shop_settings', ['purchase_code' => $this->input->post('purchase_code', true), 'envato_username' => $this->input->post('envato_username', true)], ['shop_id' => 1]);
            admin_redirect('shop_settings/updates');
        } else {
            $shop_settings = $this->shop_admin_model->getShopSettings();
            $fields        = ['version' => $shop_settings->version, 'code' => $shop_settings->purchase_code, 'username' => $shop_settings->envato_username, 'site' => base_url()];
            $this->load->helper('update');
            $protocol                    = is_https() ? 'https://' : 'http://';
            $updates                     = get_remote_contents($protocol . 'api.retailpremier.com/v1/update/', $fields);
            $this->data['shop_settings'] = $shop_settings;
            $this->data['updates']       = json_decode($updates);
            $bc                          = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('updates')]];
            $meta                        = ['page_title' => lang('updates'), 'bc' => $bc];
            $this->page_construct('shop/updates', $meta, $this->data);
        }
    }
}
