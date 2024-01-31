<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class system_settings
 * @property Settings_Model $settings_model
 */
class system_settings extends MY_Controller {
	
	public $upload_path;
	
	public $thumbs_path;
	
	public $image_types;
	
	public $allowed_file_size;
	
	public $digital_file_types;
	
	public $import_config;
	
    public function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->rerp->md('login');
        }
	
	    if ( ! $this->Owner ) {
		    $this->session->set_flashdata( 'warning', lang( 'access_denied' ) );
		    redirect( 'admin' );
	    }
	    $this->lang->admin_load( 'settings', $this->Settings->user_language );
	    $this->load->library( 'form_validation' );
	    $this->load->admin_model( 'settings_model' );
	    $this->upload_path        = 'assets/uploads/';
	    $this->thumbs_path        = 'assets/uploads/thumbs/';
	    $this->image_types        = 'gif|jpg|jpeg|png|tif';
	    $this->allowed_file_size  = '1024';
	    $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
	    $this->import_config = [
		    'upload_path'   => 'files/',
		    'allowed_types' => 'csv',
		    'max_size'      => $this->allowed_file_size,
		    'overwrite'     => true,
	    ];
    }
	
    public function add_brand()
    {
        $this->form_validation->set_rules('name', lang('brand_name'), 'trim|required|is_unique[brands.name]|alpha_numeric_spaces');
        $this->form_validation->set_rules('slug', lang('slug'), 'trim|required|is_unique[brands.slug]|alpha_dash');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'slug'        => $this->input->post('slug'),
                'description' => $this->input->post('description'),
            ];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = $this->upload_path . $photo;
                $config['new_image']      = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = true;
                $config['width']          = $this->Settings->twidth;
                $config['height']         = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                $this->image_lib->clear();
            }
        } elseif ($this->input->post('add_brand')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/brands');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addBrand($data)) {
            $this->session->set_flashdata('message', lang('brand_added'));
            admin_redirect('system_settings/brands');
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_brand', $this->data);
        }
    }
	
    public function add_category() {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('category_code'), 'trim|is_unique[categories.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required|min_length[3]');
        $this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[categories.slug]|alpha_dash');
        $this->form_validation->set_rules('userfile', lang('category_image'), 'xss_clean');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');
	    $this->form_validation->set_rules('menu_order', lang('menu_order'), 'trim|numeric');
	
	    if ( $this->form_validation->run() == true ) {
            $data = [
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'slug'        => $this->input->post('slug'),
                'description' => $this->input->post('description'),
                'parent_id'   => absint( $this->input->post('parent') ),
                'featured'    => absint( $this->input->post('featured') ),
                'menu_order'  => absint( $this->input->post('menu_order') ),
            ];
		
		    if ( $_FILES['userfile']['size'] > 0 ) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = $this->upload_path . $photo;
                $config['new_image']      = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = true;
                $config['width']          = $this->Settings->twidth;
                $config['height']         = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image']     = $this->upload_path . $photo;
                    $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type']          = 'text';
                    $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                    $wm['quality']          = '100';
                    $wm['wm_font_size']     = '16';
                    $wm['wm_font_color']    = '999999';
                    $wm['wm_shadow_color']  = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding']       = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = null;
            }
        } elseif ( $this->input->post( 'add_category' ) ) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/categories');
        }
	
	    if ( $this->form_validation->run() == true && $this->settings_model->addCategory( $data ) ) {
		    ci_delete_category_caches();
            $this->session->set_flashdata('message', lang('category_added'));
            admin_redirect('system_settings/categories');
        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js']   = $this->site->modal_js();
            $this->data['categories'] = [ '' => lang('select') . ' ' . lang('parent_category') ];
	        $catPost                  = $this->input->post( 'parent', true );
	        $this->data['categories'] = $this->build_category_dropdown_options( null, null, $catPost );
            $this->load->view($this->theme . 'settings/add_category', $this->data);
        }
    }
    
    public function add_currency()
    {
        $this->form_validation->set_rules('code', lang('currency_code'), 'trim|is_unique[currencies.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required');
        $this->form_validation->set_rules('rate', lang('exchange_rate'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = ['code'   => $this->input->post('code'),
                'name'        => $this->input->post('name'),
                'rate'        => $this->input->post('rate'),
                'symbol'      => $this->input->post('symbol'),
                'auto_update' => $this->input->post('auto_update') ? $this->input->post('auto_update') : 0,
            ];
        } elseif ($this->input->post('add_currency')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/currencies');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCurrency($data)) { //check to see if we are creating the customer
            $this->session->set_flashdata('message', lang('currency_added'));
            admin_redirect('system_settings/currencies');
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js']   = $this->site->modal_js();
            $this->data['page_title'] = lang('new_currency');
            $this->load->view($this->theme . 'settings/add_currency', $this->data);
        }
    }
	
    public function add_customer_group()
    {
        $this->form_validation->set_rules('name', lang('group_name'), 'trim|is_unique[customer_groups.name]|required');
        $this->form_validation->set_rules('percent', lang('group_percentage'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name'),
                'percent'   => $this->input->post('percent'),
            ];
        } elseif ($this->input->post('add_customer_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/customer_groups');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCustomerGroup($data)) {
            $this->session->set_flashdata('message', lang('customer_group_added'));
            admin_redirect('system_settings/customer_groups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_customer_group', $this->data);
        }
    }
	
    public function add_expense_category()
    {
        $this->form_validation->set_rules('code', lang('category_code'), 'trim|is_unique[categories.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required|min_length[3]');

        if ($this->form_validation->run() == true) {
            $data = [
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
            ];
        } elseif ($this->input->post('add_expense_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/expense_categories');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addExpenseCategory($data)) {
            $this->session->set_flashdata('message', lang('expense_category_added'));
            admin_redirect('system_settings/expense_categories');
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_expense_category', $this->data);
        }
    }
	
    public function add_price_group()
    {
        $this->form_validation->set_rules('name', lang('group_name'), 'trim|is_unique[price_groups.name]|required|alpha_numeric_spaces');

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name')];
        } elseif ($this->input->post('add_price_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/price_groups');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addPriceGroup($data)) {
            $this->session->set_flashdata('message', lang('price_group_added'));
            admin_redirect('system_settings/price_groups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_price_group', $this->data);
        }
    }
	
    public function add_tax_rate()
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|is_unique[tax_rates.name]|required');
        $this->form_validation->set_rules('type', lang('type'), 'required');
        $this->form_validation->set_rules('rate', lang('tax_rate'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name'),
                'code'      => $this->input->post('code'),
                'type'      => $this->input->post('type'),
                'rate'      => $this->input->post('rate'),
            ];
        } elseif ($this->input->post('add_tax_rate')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/tax_rates');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addTaxRate($data)) {
            $this->session->set_flashdata('message', lang('tax_rate_added'));
            admin_redirect('system_settings/tax_rates');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_tax_rate', $this->data);
        }
    }
	
    public function add_unit()
    {
        $this->form_validation->set_rules('code', lang('unit_code'), 'trim|is_unique[units.code]|required');
        $this->form_validation->set_rules('name', lang('unit_name'), 'trim|required');
        if ($this->input->post('base_unit')) {
            $this->form_validation->set_rules('operator', lang('operator'), 'required');
            $this->form_validation->set_rules('operation_value', lang('operation_value'), 'trim|required');
        }

        if ($this->form_validation->run() == true) {
            $data = [
                'name'            => $this->input->post('name'),
                'code'            => $this->input->post('code'),
                'base_unit'       => $this->input->post('base_unit') ? $this->input->post('base_unit') : null,
                'operator'        => $this->input->post('base_unit') ? $this->input->post('operator') : null,
                'operation_value' => $this->input->post('operation_value') ? $this->input->post('operation_value') : null,
            ];
        } elseif ($this->input->post('add_unit')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/units');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addUnit($data)) {
            $this->session->set_flashdata('message', lang('unit_added'));
            admin_redirect('system_settings/units');
        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
	        $this->data['modal_js']   = $this->site->modal_js();
            $this->data['base_units'] = [ '' => lang('select') . ' ' . lang('unit') ];
	        
            foreach ( (array) $this->site->getAllBaseUnits() as $baseUnit ) {
            	if ( ! isset( $baseUnit->id ) ) {
            		continue;
	            }
	            $this->data['base_units'][ $baseUnit->id ] = $baseUnit->name . ' (' . $baseUnit->code . ')';
            }
            $this->load->view($this->theme . 'settings/add_unit', $this->data);
        }
    }
	
    public function add_variant()
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|is_unique[variants.name]|required');

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name')];
        } elseif ($this->input->post('add_variant')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/variants');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addVariant($data)) {
            $this->session->set_flashdata('message', lang('variant_added'));
            admin_redirect('system_settings/variants');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_variant', $this->data);
        }
    }
	
    public function add_warehouse()
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('code'), 'trim|is_unique[warehouses.code]|required');
        $this->form_validation->set_rules('name', lang('name'), 'required');
        $this->form_validation->set_rules('address', lang('address'), 'required');
        $this->form_validation->set_rules('userfile', lang('map_image'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = '2000';
                $config['max_height']    = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect('system_settings/warehouses');
                }

                $map = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $map;
                $config['new_image']      = 'assets/uploads/thumbs/' . $map;
                $config['maintain_ratio'] = true;
                $config['width']          = 76;
                $config['height']         = 76;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            } else {
                $map = null;
            }
            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $this->input->post('address'),
                'price_group_id' => $this->input->post('price_group'),
                'map'            => $map,
            ];
        } elseif ($this->input->post('add_warehouse')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/warehouses');
        }

        if ($this->form_validation->run() == true && $this->settings_model->addWarehouse($data)) {
            $this->session->set_flashdata('message', lang('warehouse_added'));
            admin_redirect('system_settings/warehouses');
        } else {
            $this->data['error']        = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['price_groups'] = $this->settings_model->getAllPriceGroups();
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/add_warehouse', $this->data);
        }
    }
	
    public function backup_database()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->load->dbutil();
        $prefs = [
            'format'   => 'txt',
            'filename' => 'rerp_db_backup.sql',
        ];
        $back    = $this->dbutil->backup($prefs);
        $backup  = &$back;
        $db_name = 'db-backup-on-' . date('Y-m-d-H-i-s') . '.txt';
        $save    = './files/backups/' . $db_name;
        $this->load->helper('file');
        write_file($save, $backup);
        $this->session->set_flashdata('messgae', lang('db_saved'));
        admin_redirect('system_settings/backups');
    }
	
    public function backup_files()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $name = 'file-backup-' . date('Y-m-d-H-i-s');
        $this->rerp->zip('./', './files/backups/', $name);
        $this->session->set_flashdata('messgae', lang('backup_saved'));
        admin_redirect('system_settings/backups');
        exit();
    }
	
    public function backups()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->data['files'] = glob('./files/backups/*.zip', GLOB_BRACE);
        $this->data['dbs']   = glob('./files/backups/*.txt', GLOB_BRACE);
        krsort($this->data['files']);
        krsort($this->data['dbs']);
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('backups')]];
        $meta = ['page_title' => lang('backups'), 'bc' => $bc];
        $this->page_construct('settings/backups', $meta, $this->data);
    }
	
    public function brand_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteBrand($id);
                    }
                    $this->session->set_flashdata('message', lang('brands_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('brands'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('image'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $brand = $this->site->getBrandByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $brand->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $brand->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $brand->image);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical('center');
                    $filename = 'brands_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
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
	
    public function brands()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('brands')]];
        $meta                = ['page_title' => lang('brands'), 'bc' => $bc];
        $this->page_construct('settings/brands', $meta, $this->data);
    }
	
    public function categories()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('categories')]];
        $meta                = ['page_title' => lang('categories'), 'bc' => $bc];
        $this->page_construct('settings/categories', $meta, $this->data);
    }
	
    public function category_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCategory($id);
                    }
                    $this->session->set_flashdata('message', lang('categories_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('slug'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('image'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('parent_category'));
	                $this->excel->getActiveSheet()->SetCellValue('F1', lang('menu_order'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc              = $this->settings_model->getCategoryByID($id);
                        $parent_category = '';
                        if ($sc->parent_id) {
                            $pc              = $this->settings_model->getCategoryByID($sc->parent_id);
                            $parent_category = $pc->code;
                        }
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->slug);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sc->image);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $parent_category);
	                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $sc->menu_order);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical('center');
                    $filename = 'categories_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
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
	
    public function change_logo()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            $this->rerp->md();
        }
        $this->load->helper('security');
        $this->form_validation->set_rules('site_logo', lang('site_logo'), 'xss_clean');
        $this->form_validation->set_rules('login_logo', lang('login_logo'), 'xss_clean');
        $this->form_validation->set_rules('biller_logo', lang('biller_logo'), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($_FILES['site_logo']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path . 'logos/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = 300;
                $config['max_height']    = 80;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('site_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $site_logo = $this->upload->file_name;
                $this->db->update('settings', ['logo' => $site_logo], ['setting_id' => 1]);
            }

            if ($_FILES['login_logo']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path . 'logos/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = 300;
                $config['max_height']    = 80;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('login_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $login_logo = $this->upload->file_name;
                $this->db->update('settings', ['logo2' => $login_logo], ['setting_id' => 1]);
            }

            if ($_FILES['biller_logo']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path . 'logos/';
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = 300;
                $config['max_height']    = 80;
                $config['overwrite']     = false;
                $config['max_filename']  = 25;
                //$config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('biller_logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo = $this->upload->file_name;
            }

            $this->session->set_flashdata('message', lang('logo_uploaded'));
            redirect($_SERVER['HTTP_REFERER']);
        } elseif ($this->input->post('upload_logo')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/change_logo', $this->data);
        }
    }
	
    public function create_group()
    {
        $this->form_validation->set_rules('group_name', lang('group_name'), 'required|alpha_dash|is_unique[groups.name]');

        if ($this->form_validation->run() == true) {
            $data = ['name' => strtolower($this->input->post('group_name')), 'description' => $this->input->post('description')];
        } elseif ($this->input->post('create_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/user_groups');
        }

        if ($this->form_validation->run() == true && ($new_group_id = $this->settings_model->addGroup($data))) {
            $this->session->set_flashdata('message', lang('group_added'));
            admin_redirect('system_settings/permissions/' . $new_group_id);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['group_name'] = [
                'name'  => 'group_name',
                'id'    => 'group_name',
                'type'  => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('group_name'),
            ];
            $this->data['description'] = [
                'name'  => 'description',
                'id'    => 'description',
                'type'  => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('description'),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/create_group', $this->data);
        }
    }
	
    public function currencies()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('currencies')]];
        $meta = ['page_title' => lang('currencies'), 'bc' => $bc];
        $this->page_construct('settings/currencies', $meta, $this->data);
    }
	
    public function currency_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCurrency($id);
                    }
                    $this->session->set_flashdata('message', lang('currencies_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('currencies'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('rate'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getCurrencyByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->rate);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical('center');
                    $filename = 'currencies_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
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
    
    public function shipping_zones() {
	    $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
	    $bc = [
		    [ 'link' => base_url(), 'page' => lang( 'home' ) ],
		    [ 'link' => admin_url( 'system_settings' ), 'page' => lang( 'system_settings' ) ],
		    [ 'link' => '#', 'page' => lang( 'shipping_zones' ) ],
	    ];
	    $meta = [ 'page_title' => lang( 'shipping_zones' ), 'bc' => $bc ];
	    $this->page_construct('settings/shipping/index', $meta, $this->data);
    }
	
	public function getShippingZones() {
		$this->load->library('datatables');
		$z = $this->db->dbprefix( 'shipping_zones' );
		$ac = "( SELECT COUNT(*) FROM {$this->db->dbprefix('shipping_zone_areas')} WHERE zone_id=z.id AND is_enabled=1 )";
		$this->datatables
			->select( "z.id as id, z.name as name, country, city, zip, {$ac} as areas", false )
			->from('shipping_zones z')
			->where( 'z.is_enabled=1' )
			->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/zone_editor/$1') . "' class='tip' title='" . lang('edit_zone' ) . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_zone') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_zone/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id' );
		//->unset_column('id');

		echo $this->datatables->generate();
	}
	
	private function handleZoneData() {}
	
	public function world_data() {
    	$which = $this->input->get_post( 'which', true );
		$data  = [];
    	if ( 'country' === $which ) {
    		if ( $continent = $this->input->get_post( 'continent' ) ) {
			    $data = ci_get_continents( $continent );
		    } else {
			    $data = [ '' => sprintf( lang( 'select_x' ), lang( 'continent' ) ) ];
		    }
	    }
		
		if ( 'states' === $which ) {
			$states = false;
			if ( $cc = $this->input->get_post( 'cc' ) ) {
				$states = ci_get_states( $cc );
				if ( $states ) {
					$data = ci_get_states( $cc );
				}
			}
			if ( false === $states ) {
				$data = [ '' => sprintf( lang( 'select_x' ), lang( 'country' ) ) ];
			}
		}
		
		$this->rerp->send_json( $data );
	}
	
	public function zone_editor( $id = null ) {
		$isEdit = ! is_null( $id );
    	$zone   = new Erp_Shipping_Zone( $id );
    	if ( $isEdit ) {
		    $this->form_validation->set_rules( 'name', lang( 'name' ), 'trim|is_unique_exclude_where[shipping_zones.name,id:'.$id.']|required' );
	    } else {
		    $this->form_validation->set_rules( 'name', lang( 'name' ), 'trim|is_unique[shipping_zones.name]|required' );
	    }
		$this->form_validation->set_rules( 'continent', lang( 'continent' ), 'required' );
		$this->form_validation->set_rules( 'country', lang( 'country' ), 'required' );
		$this->form_validation->set_rules( 'state', lang( 'state' ), 'required' );
		$this->form_validation->set_rules( 'city', lang( 'city' ), 'required' );
		
		if ( $this->input->post('submit_zone') ) {
			if ($this->form_validation->run() == true) {
				$zone->setName( $this->input->post( 'name' ) );
				$zone->setContinent( $this->input->post( 'continent' ) );
				$zone->setCountry( $this->input->post( 'country' ) );
				$zone->setState( $this->input->post( 'state' ) );
				$zone->setCity( $this->input->post( 'city' ) );
				$zip = $this->input->post( 'zip' );
				if ( $zip ) {
					$zone->setZip( $zip );
				}
				$zone->setIsEnabled( 1 == $this->input->post( 'is_enabled' ) ? 1 : 0 );
				
				if ( $zone->save() ) {
					$message = $isEdit ? lang( 'zone_updated' ) : lang( 'zone_added' );
					$this->session->set_flashdata( 'message', $message );
				} else {
					$this->session->set_flashdata('error', ( validation_errors() ? validation_errors() : sprintf( lang( 'error_saving_x' ), lang( 'shipping_zone' ) ) ) );
				}
			} else {
				$this->session->set_flashdata('error', validation_errors());
			}
			
			if ( $isEdit ) {
				admin_redirect('system_settings/zone_editor/' . $id );
			} else {
				admin_redirect('system_settings/shipping_zones' );
			}
		}
		
		$this->data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$this->data['zone'] = $zone;
		$this->data['edit'] = $isEdit;
		$pageTitle = $isEdit ? lang( 'edit_zone' ) : lang( 'add_zone' );
		$bc = [
			[
				'link' => base_url(),
				'page' => lang( 'home' ),
			],
			[
				'link' => admin_url( 'shipping_zones' ),
				'page' => lang( 'shipping_zones' ),
			],
			[
				'link' => '#',
				'page' => $pageTitle,
			],
		];
		$meta = [
			'page_title' => $pageTitle,
			'bc'         => $bc,
		];
		$this->page_construct( 'settings/shipping/zone_editor', $meta, $this->data );
	}
	public function shipping_method_editor( $id = null, $type = 'edit' ) {
    	$isEdit = 'edit' === $type ? true : false;
    	if ( $isEdit ) {
		    $method = new Erp_Shipping_Method( $id );
		    $zone   = new Erp_Shipping_Zone( $method->getZoneId() );
	    } else {
		    $method = new Erp_Shipping_Method();
    		$zone   = new Erp_Shipping_Zone( $id );
    		$method->setName( lang( 'flat_rate_shipping' ) );
	    }
		
		if (  ! $id || ! $zone->getId() ) {
			$this->session->set_flashdata( 'error', lang( 'nothing_found' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		
		$this->form_validation->set_rules( 'zone_id', lang( 'zone_id' ), 'required' );
		$this->form_validation->set_rules( 'name', lang( 'name' ), 'required' );
		$this->form_validation->set_rules( 'method_id', lang( 'method_id' ), 'required' );
		//$this->form_validation->set_rules( 'description', lang( 'description' ), 'required' );
		$this->form_validation->set_rules( 'cost', lang( 'cost' ), 'required|numeric' );
		
		if ( $this->input->post('submit_shipping_method') ) {
			if ( $this->form_validation->run() == true ) {
				$method->setZoneId( $this->input->post( 'zone_id' ) );
				$method->setName( $this->input->post( 'name' ) );
				$method->setDescription( $this->input->post( 'description' ) );
				$method->setMethodId( $this->input->post( 'method_id' ) );
				$method->setCost( $this->input->post( 'cost' ) );
				$method->setIsEnabled( 1 == $this->input->post( 'is_enabled' ) ? 1 : 0 );
				
				if ( $method->save() ) {
					$message = sprintf( $isEdit ? lang( 'x_updated' ) : lang( 'x_added' ), lang( 'shipping_method' ) );
					$this->session->set_flashdata( 'message', $message );
				} else {
					$this->session->set_flashdata('error', ( validation_errors() ? validation_errors() : sprintf( lang( 'error_saving_x' ), lang( 'shipping_method' ) ) ) );
				}
			} else {
				$this->session->set_flashdata('error', validation_errors());
			}
			
			if ( $isEdit ) {
				admin_redirect('system_settings/shipping_method_editor/' . $id );
			} else {
				admin_redirect('system_settings/zone_editor/' . $id );
			}
		}
		
		$this->data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$this->data['zone'] = $zone;
		$this->data['edit'] = $isEdit;
		$this->data['method'] = $method;
		
		$pageTitle = $isEdit ? lang( 'edit_x' ) : lang( 'add_x' );
		$pageTitle = sprintf( $pageTitle, lang( 'shipping_method' ) );
		$bc = [
			[
				'link' => base_url(),
				'page' => lang( 'home' ),
			],
			[
				'link' => admin_url( 'shipping_zones' ),
				'page' => lang( 'shipping_zones' ),
			],
			[
				'link' => admin_url( 'system_settings/zone_editor/' . $zone->getId() ),
				'page' => $zone->getName(),
			],
			[
				'link' => '#',
				'page' => $pageTitle,
			],
		];
		$meta = [
			'page_title' => $pageTitle,
			'bc'         => $bc,
		];
		$this->page_construct( 'settings/shipping/method_editor', $meta, $this->data );
	}
	public function area_editor( $id = null, $type = 'edit' ) {
		$isEdit = 'edit' === $type ? true : false;
		if ( $isEdit ) {
			$area = new Erp_Shipping_Zone_Area( $id );
			$zone   = new Erp_Shipping_Zone( $area->getZoneId() );
		} else {
			$area = new Erp_Shipping_Zone_Area();
			$zone   = new Erp_Shipping_Zone( $id );
		}
		
		if ( ! $id || ! $zone->getId() ) {
			$this->session->set_flashdata( 'error', lang( 'nothing_found' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		
		$this->form_validation->set_rules( 'zone_id', lang( 'zone_id' ), 'required' );
		$this->form_validation->set_rules( 'name', lang( 'name' ), 'required' );
		$this->form_validation->set_rules( 'cost_adjustment', lang( 'cost_adjustment' ), 'required|numeric' );
		
		if ( $this->input->post('submit_zone_area') ) {
			if ( $this->form_validation->run() == true ) {
				$area->setZoneId( $this->input->post( 'zone_id' ) );
				$area->setName( $this->input->post( 'name' ) );
				$area->setCostAdjustment( $this->input->post( 'cost_adjustment' ) );
				$area->setIsEnabled( 1 == $this->input->post( 'is_enabled' ) ? 1 : 0 );
				$area->setDeliveryEnabled( 1 == $this->input->post( 'delivery_enabled' ) ? 1 : 0 );
				$area->setPickupEnabled( 1 == $this->input->post( 'pickup_enabled' ) ? 1 : 0 );

				if ( $area->save() ) {
					$message = sprintf( $isEdit ? lang( 'x_updated' ) : lang( 'x_added' ), lang( 'area' ) );
					$this->session->set_flashdata( 'message', $message );
				} else {
					$this->session->set_flashdata('error', ( validation_errors() ? validation_errors() : sprintf( lang( 'error_saving_x' ), lang( 'area' ) ) ) );
				}
			} else {
				$this->session->set_flashdata( 'error', validation_errors() );
			}
			
			if ( $isEdit ) {
				admin_redirect('system_settings/area_editor/' . $id );
			} else {
				admin_redirect('system_settings/zone_editor/' . $id );
			}
		}
		
		$this->data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$this->data['zone'] = $zone;
		$this->data['edit'] = $isEdit;
		$this->data['area'] = $area;
		
		$pageTitle = $isEdit ? lang( 'edit_x' ) : lang( 'add_x' );
		$pageTitle = sprintf( $pageTitle, lang( 'zone_area' ) );
		$bc = [
			[
				'link' => base_url(),
				'page' => lang( 'home' ),
			],
			[
				'link' => admin_url( 'shipping_zones' ),
				'page' => lang( 'shipping_zones' ),
			],
			[
				'link' => admin_url( 'system_settings/zone_editor/' . $zone->getId() ),
				'page' => $zone->getName(),
			],
			[
				'link' => '#',
				'page' => $pageTitle,
			],
		];
		$meta = [
			'page_title' => $pageTitle,
			'bc'         => $bc,
		];
		$this->page_construct( 'settings/shipping/area_editor', $meta, $this->data );
	}
	public function slot_editor( $id = null, $type = 'edit' ) {
		$isEdit = 'edit' === $type ? true : false;
		if ( $isEdit ) {
			$slot = new Erp_Shipping_Zone_Area_Slot( $id );
			$area = new Erp_Shipping_Zone_Area( $slot->getAreaId() );
			$zone = new Erp_Shipping_Zone( $area->getZoneId() );
		} else {
			$slot = new Erp_Shipping_Zone_Area_Slot();
			$area = new Erp_Shipping_Zone_Area( $id );
			$zone   = new Erp_Shipping_Zone( $area->getZoneId() );
		}
		
		if ( ! $id || ! $zone->getId() ) {
			$this->session->set_flashdata( 'error', lang( 'nothing_found' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		
		$this->form_validation->set_rules( 'area_id', lang( 'area_id' ), 'required' );
		$this->form_validation->set_rules( 'name', lang( 'name' ), 'required' );
		
		$this->form_validation->set_rules( 'start_at', lang( 'start_at' ), 'required|validate_conditional_field[start_at|<:end_at]' );
		$this->form_validation->set_rules( 'end_at', lang( 'end_at' ), 'required|validate_conditional_field[end_at|>:start_at|>:close_before]' );
		$this->form_validation->set_rules( 'close_before', lang( 'close_before' ), 'required|validate_conditional_field[close_before|>:start_at|<:end_at]' );
		
		$this->form_validation->set_rules( 'max_order', lang( 'max_order' ), 'required|numeric' );
		$this->form_validation->set_rules( 'cost_adjustment', lang( 'cost_adjustment' ), 'required|numeric' );
		
		if ( $this->input->post('submit_slot') ) {
			if ( $this->form_validation->run() == true ) {
				$slot->setAreaId( $this->input->post( 'area_id' ) );
				$slot->setName( $this->input->post( 'name' ) );
				
				$start = $this->input->post( 'start_at' );
				$end   = $this->input->post( 'end_at' );
				$close = $this->input->post( 'close_before' );
				
				$slot->setStartAt( $start );
				$slot->setEndAt( $end );
				$slot->setCloseBefore( $close );
				
				$slot->setMaxOrder( $this->input->post( 'max_order' ) );
				$slot->setCostAdjustment( $this->input->post( 'cost_adjustment' ) );
				$slot->setIsEnabled( 1 == $this->input->post( 'is_enabled' ) ? 1 : 0 );
				
				if ( $slot->save() ) {
					$message = sprintf( $isEdit ? lang( 'x_updated' ) : lang( 'x_added' ), lang( 'slot' ) );
					$this->session->set_flashdata( 'message', $message );
				} else {
//					if ( $error ) {
//						$this->form_validation->set_message( 'validate_time', $error );
//					}
					$this->session->set_flashdata('error', ( validation_errors() ? validation_errors() : sprintf( lang( 'error_saving_x' ), lang( 'slot' ) ) ) );
				}
			} else {
				$this->session->set_flashdata( 'error', validation_errors() );
			}
			
			if ( $isEdit ) {
				admin_redirect('system_settings/slot_editor/' . $id );
			} else {
				admin_redirect('system_settings/view_slots/' . $id );
			}
		}
		
		$this->data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$this->data['edit'] = $isEdit;
		$this->data['zone'] = $zone;
		$this->data['area'] = $area;
		$this->data['slot'] = $slot;
		
		$pageTitle = $isEdit ? lang( 'edit_x' ) : lang( 'add_x' );
		$pageTitle = sprintf( $pageTitle, lang( 'slot' ) );
		$bc = [
			[
				'link' => base_url(),
				'page' => lang( 'home' ),
			],
			[
				'link' => admin_url( 'shipping_zones' ),
				'page' => lang( 'shipping_zones' ),
			],
			[
				'link' => admin_url( 'system_settings/zone_editor/' . $zone->getId() ),
				'page' => $zone->getName(),
			],
			[
				'link' => admin_url( 'system_settings/zone_editor/' . $zone->getId() ),
				'page' => $area->getName(),
			],
			[
				'link' => admin_url( 'system_settings/view_slots/' . $area->getId() ),
				'page' => lang( 'slots' ),
			],
			[
				'link' => '#',
				'page' => $pageTitle,
			],
		];
		$meta = [
			'page_title' => $pageTitle,
			'bc'         => $bc,
		];
		$this->page_construct( 'settings/shipping/slot_editor', $meta, $this->data );
	}

	public function pickup_slot_editor( $id = null, $type = 'edit' ) {
    	$isEdit = 'edit' === $type ? true : false;
		if ( $isEdit ) {
			$slot = new Erp_Pickup_Area_Slot( $id );
			$area = new Erp_Shipping_Zone_Area( $slot->getAreaId() );
			$zone = new Erp_Shipping_Zone( $area->getZoneId() );
		} else {
			$slot = new Erp_Pickup_Area_Slot();
			$area = new Erp_Shipping_Zone_Area( $id );
			$zone   = new Erp_Shipping_Zone( $area->getZoneId() );
		}

		if ( ! $id || ! $zone->getId() ) {
			$this->session->set_flashdata( 'error', lang( 'nothing_found' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
		}

		$this->form_validation->set_rules( 'area_id', lang( 'area_id' ), 'required' );
		$this->form_validation->set_rules( 'name', lang( 'name' ), 'required' );

		$this->form_validation->set_rules( 'start_at', lang( 'start_at' ), 'required|validate_conditional_field[start_at|<:end_at]' );
		$this->form_validation->set_rules( 'end_at', lang( 'end_at' ), 'required|validate_conditional_field[end_at|>:start_at|>:close_before]' );
		$this->form_validation->set_rules( 'close_before', lang( 'close_before' ), 'required|validate_conditional_field[close_before|>:start_at|<:end_at]' );

		$this->form_validation->set_rules( 'max_order', lang( 'max_order' ), 'required|numeric' );
		$this->form_validation->set_rules( 'cost_adjustment', lang( 'cost_adjustment' ), 'required|numeric' );

		if ( $this->input->post('submit_slot') ) {
			if ( $this->form_validation->run() == true ) {
				$slot->setAreaId( $this->input->post( 'area_id' ) );
				$slot->setName( $this->input->post( 'name' ) );

				$start = $this->input->post( 'start_at' );
				$end   = $this->input->post( 'end_at' );
				$close = $this->input->post( 'close_before' );

				$slot->setStartAt( $start );
				$slot->setEndAt( $end );
				$slot->setCloseBefore( $close );

				$slot->setMaxOrder( $this->input->post( 'max_order' ) );
				$slot->setCostAdjustment( $this->input->post( 'cost_adjustment' ) );
				$slot->setIsEnabled( 1 == $this->input->post( 'is_enabled' ) ? 1 : 0 );

				if ( $slot->save() ) {
					$message = sprintf( $isEdit ? lang( 'x_updated' ) : lang( 'x_added' ), lang( 'slot' ) );
					$this->session->set_flashdata( 'message', $message );
				} else {
//					if ( $error ) {
//						$this->form_validation->set_message( 'validate_time', $error );
//					}
					$this->session->set_flashdata('error', ( validation_errors() ? validation_errors() : sprintf( lang( 'error_saving_x' ), lang( 'slot' ) ) ) );
				}
			} else {
				$this->session->set_flashdata( 'error', validation_errors() );
			}

			if ( $isEdit ) {
				admin_redirect('system_settings/pickup_slot_editor/' . $id );
			} else {
				admin_redirect('system_settings/pickup_slots/' . $id );
			}
		}

		$this->data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$this->data['edit'] = $isEdit;
		$this->data['zone'] = $zone;
		$this->data['area'] = $area;
		$this->data['slot'] = $slot;

		$pageTitle = $isEdit ? lang( 'edit_x' ) : lang( 'add_x' );
		$pageTitle = sprintf( $pageTitle, sprintf( lang('pickup_x'), lang( 'slot' ) ) );
		$bc = [
			[
				'link' => base_url(),
				'page' => lang( 'home' ),
			],
			[
				'link' => admin_url( 'shipping_zones' ),
				'page' => lang( 'shipping_zones' ),
			],
			[
				'link' => admin_url( 'system_settings/zone_editor/' . $zone->getId() ),
				'page' => $zone->getName(),
			],
			[
				'link' => admin_url( 'system_settings/zone_editor/' . $zone->getId() ),
				'page' => $area->getName(),
			],
			[
				'link' => admin_url( 'system_settings/pickup_slots/' . $area->getId() ),
				'page' => lang( 'pickup_slots' ),
			],
			[
				'link' => '#',
				'page' => $pageTitle,
			],
		];
		$meta = [
			'page_title' => $pageTitle,
			'bc'         => $bc,
		];
		$this->page_construct( 'settings/shipping/pickup_slot_editor', $meta, $this->data );
	}
	
	public function view_slots( $id ) {
    	
    	$area = new Erp_Shipping_Zone_Area( $id );
    	
    	if ( ! $area->getId() ) {
		    $this->session->set_flashdata( 'error', lang( 'nothing_found' ) );
		    redirect( $_SERVER['HTTP_REFERER'] );
	    }
    	
    	$zone = new Erp_Shipping_Zone( $area->getZoneId() );
		
		$this->data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$this->data['zone'] = $zone;
		$this->data['area'] = $area;
		
		$pageTitle = sprintf( lang( 'delivery_x' ), lang( 'slots' ) );
        $bc = [
			[
				'link' => base_url(),
				'page' => lang( 'home' ),
			],
			[
				'link' => admin_url( 'shipping_zones' ),
				'page' => lang( 'shipping_zones' ),
			],
	        [
		        'link' => admin_url( 'system_settings/zone_editor/' . $zone->getId() ),
		        'page' => sprintf( lang( 'edit_x' ), $zone->getName() ),
	        ],
	        [
		        'link' => '#',
		        'page' => $area->getName(),
	        ],
			[
				'link' => '#',
				'page' => $pageTitle,
			],
		];
		$meta = [
			'page_title' => $pageTitle,
			'bc'         => $bc,
		];
		$this->page_construct( 'settings/shipping/slot_index', $meta, $this->data );
	}

	public function pickup_slots( $id ) {

    	$area = new Erp_Shipping_Zone_Area( $id );

    	if ( ! $area->getId() ) {
		    $this->session->set_flashdata( 'error', lang( 'nothing_found' ) );
		    redirect( $_SERVER['HTTP_REFERER'] );
	    }

    	$zone = new Erp_Shipping_Zone( $area->getZoneId() );

		$this->data['error'] = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		$this->data['zone'] = $zone;
		$this->data['area'] = $area;

		$pageTitle = lang( 'pickup_slots' );
        $bc = [
			[
				'link' => base_url(),
				'page' => lang( 'home' ),
			],
			[
				'link' => admin_url( 'shipping_zones' ),
				'page' => lang( 'shipping_zones' ),
			],
	        [
		        'link' => admin_url( 'system_settings/zone_editor/' . $zone->getId() ),
		        'page' => sprintf( lang( 'edit_x' ), $zone->getName() ),
	        ],
	        [
		        'link' => '#',
		        'page' => $area->getName(),
	        ],
			[
				'link' => '#',
				'page' => $pageTitle,
			],
		];
		$meta = [
			'page_title' => $pageTitle,
			'bc'         => $bc,
		];
		$this->page_construct( 'settings/shipping/pickup_slot', $meta, $this->data );
	}

	public function getZoneAreaSlots( $area_id ) {
		$this->load->library('datatables');
		$z = $this->db->dbprefix( 'shipping_area_slots' );
		$this->datatables
			->select( "id, name, start_at, end_at, max_order, cost_adjustment, close_before, IF( is_enabled > 0, '".lang('active')."', '".lang('inactive')."' ) as is_enabled", false )
			->from('shipping_area_slots')
			->where( 'area_id', $area_id )
			->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/slot_editor/$1') . "' class='tip' title='" . sprintf( lang('edit_x' ), lang('slot' ) ) . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . sprintf( lang('delete_x'), lang('slot') ) . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_slot/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id' );
		//->unset_column('id');
		
		echo $this->datatables->generate();
	}

	public function getPickupAreaSlots( $area_id ) {
		$this->load->library('datatables');
		$z = $this->db->dbprefix( 'pickup_area_slots' );
		$this->datatables
			->select( "id, name, start_at, end_at, max_order, cost_adjustment, close_before, IF( is_enabled > 0, '".lang('active')."', '".lang('inactive')."' ) as is_enabled", false )
			->from('pickup_area_slots')
			->where( 'area_id', $area_id )
			->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/pickup_slot_editor/$1') . "' class='tip' title='" . sprintf( lang('edit_x' ), sprintf( lang('pickup_x'), lang('slot' ) ) ) . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . sprintf( lang('delete_x'), lang('slot') ) . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_pickup_slot/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id' );
		//->unset_column('id');

		echo $this->datatables->generate();
	}
	
	public function delete_zone( $id ) {
    	$zone = new Erp_Shipping_Zone( $id );
		if ( $zone->delete() ) {
			$this->rerp->send_json( [
				'error' => 0,
				'msg'   => sprintf( lang( 'x_deleted' ), lang( 'shipping_zone' ) ),
			] );
		} else {
			$this->rerp->send_json( [
				'error' => 1,
				'msg'   => sprintf( lang( 'error_deleting_x' ), lang( 'shipping_zone' ) ),
			] );
		}
	}
	public function delete_shipping_method( $id ) {
    	$method = new Erp_Shipping_Method( $id );
		if ( $method->delete() ) {
			$this->rerp->send_json( [
				'error' => 0,
				'msg'   => sprintf( lang( 'x_deleted' ), lang( 'shipping_method' ) ),
			] );
		} else {
			$this->rerp->send_json( [
				'error' => 1,
				'msg'   => sprintf( lang( 'error_deleting_x' ), lang( 'shipping_method' ) ),
			] );
		}
	}
	public function delete_area( $id ) {
		$area = new Erp_Shipping_Zone_Area( $id );
		if ( $area->delete() ) {
			$this->rerp->send_json( [
				'error' => 0,
				'msg'   => sprintf( lang( 'x_deleted' ), lang( 'area' ) ),
			] );
		} else {
			$this->rerp->send_json( [
				'error' => 1,
				'msg'   => sprintf( lang( 'error_deleting_x' ), lang( 'area' ) ),
			] );
		}
	}
	public function delete_slot( $id ) {
		$slot = new Erp_Shipping_Zone_Area_Slot( $id );
		if ( $slot->delete() ) {
			$this->rerp->send_json( [
				'error' => 0,
				'msg'   => sprintf( lang( 'x_deleted' ), lang( 'slot' ) ),
			] );
		} else {
			$this->rerp->send_json( [
				'error' => 1,
				'msg'   => sprintf( lang( 'error_deleting_x' ), lang( 'slot' ) ),
			] );
		}
	}
	public function delete_pickup_slot( $id ) {
		$slot = new Erp_Pickup_Area_Slot( $id );
		if ( $slot->delete() ) {
			$this->rerp->send_json( [
				'error' => 0,
				'msg'   => sprintf( lang( 'x_deleted' ), lang( 'slot' ) ),
			] );
		} else {
			$this->rerp->send_json( [
				'error' => 1,
				'msg'   => sprintf( lang( 'error_deleting_x' ), lang( 'slot' ) ),
			] );
		}
	}
    
    public function shipping_zone_actions() {
	    $this->form_validation->set_rules('form_action', lang('form_action'), 'required');
	    if ( $this->form_validation->run() == true ) {
		    if ( ! empty( $_POST['val'] ) ) {
			    if ( $this->input->post( 'form_action' ) == 'delete' ) {
				    $count = count( $_POST['val'] );
				    foreach ( $_POST['val'] as $id ) {
					    $zone = new Erp_Shipping_Zone( $id );
					    $zone->delete();
				    }
				    $this->session->set_flashdata( 'message', sprintf( lang( 'x_deleted' ), $count > 1 ? lang( 'shipping_zones' ) : lang( 'shipping_zone' ) ) );
				    redirect( $_SERVER['HTTP_REFERER'] );
			    }
		    } else {
			    $this->session->set_flashdata( 'error', lang( 'no_record_selected' ) );
			    redirect( $_SERVER['HTTP_REFERER'] );
		    }
	    } else {
		    $this->session->set_flashdata( 'error', validation_errors() );
		    redirect( $_SERVER['HTTP_REFERER'] );
	    }
    }
    public function slot_actions() {
	    $this->form_validation->set_rules('form_action', lang('form_action'), 'required');
	    if ( $this->form_validation->run() == true ) {
		    if ( ! empty( $_POST['val'] ) ) {
			    if ( $this->input->post( 'form_action' ) == 'delete' ) {
			    	$count = count( $_POST['val'] );
				    foreach ( $_POST['val'] as $id ) {
			    	    $slot = new Erp_Shipping_Zone_Area_Slot( $id );
					    $slot->delete();
				    }
				    $this->session->set_flashdata( 'message', sprintf( lang( 'x_deleted' ), $count > 1 ? lang( 'slots' ) : lang( 'slot' ) ) );
				    redirect( $_SERVER['HTTP_REFERER'] );
			    }
		    } else {
			    $this->session->set_flashdata( 'error', lang( 'no_record_selected' ) );
			    redirect( $_SERVER['HTTP_REFERER'] );
		    }
	    } else {
		    $this->session->set_flashdata( 'error', validation_errors() );
		    redirect( $_SERVER['HTTP_REFERER'] );
	    }
    }
	
    public function customer_group_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCustomerGroup($id);
                    }
                    $this->session->set_flashdata('message', lang('customer_groups_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('tax_rates'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('group_name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('group_percentage'));
                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $pg = $this->settings_model->getCustomerGroupByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $pg->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $pg->percent);
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical('center');
                    $filename = 'customer_groups_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_customer_group_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
	
    public function customer_groups()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('customer_groups')]];
        $meta = ['page_title' => lang('customer_groups'), 'bc' => $bc];
        $this->page_construct('settings/customer_groups', $meta, $this->data);
    }
	
    public function delete_backup( $zipfile )
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        unlink('./files/backups/' . $zipfile . '.zip');
        $this->session->set_flashdata('messgae', lang('backup_deleted'));
        admin_redirect('system_settings/backups');
    }
	
    public function delete_brand( $id = null )
    {
        if ($this->settings_model->brandHasProducts($id)) {
            $this->rerp->send_json(['error' => 1, 'msg' => lang('brand_has_products')]);
        }

        if ($this->settings_model->deleteBrand($id)) {
            $this->rerp->send_json(['error' => 0, 'msg' => lang('brand_deleted')]);
        }
    }
	
    public function delete_category( $id = null )
    {
	    if ( $this->site->getSubCategories( $id ) ) {
            $this->rerp->send_json(['error' => 1, 'msg' => lang('category_has_subcategory')]);
        }
	
	    if ( $this->settings_model->deleteCategory( $id ) ) {
		    ci_delete_category_caches();
            $this->rerp->send_json(['error' => 0, 'msg' => lang('category_deleted')]);
        }
    }
	
    public function delete_currency( $id = null )
    {
        if ($this->settings_model->deleteCurrency($id)) {
            $this->rerp->send_json(['error' => 0, 'msg' => lang('currency_deleted')]);
        }
    }
	
    public function delete_customer_group( $id = null )
    {
        if ($this->settings_model->deleteCustomerGroup($id)) {
            $this->rerp->send_json(['error' => 0, 'msg' => lang('customer_group_deleted')]);
        }
    }
	
    public function delete_database( $dbfile )
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        unlink('./files/backups/' . $dbfile . '.txt');
        $this->session->set_flashdata('messgae', lang('db_deleted'));
        admin_redirect('system_settings/backups');
    }
	
    public function delete_expense_category( $id = null )
    {
        if ($this->settings_model->hasExpenseCategoryRecord($id)) {
            $this->rerp->send_json(['error' => 1, 'msg' => lang('category_has_expenses')]);
        }

        if ($this->settings_model->deleteExpenseCategory($id)) {
            $this->rerp->send_json(['error' => 0, 'msg' => lang('expense_category_deleted')]);
        }
    }
	
    public function delete_group( $id = null )
    {
        if ($this->settings_model->checkGroupUsers($id)) {
            $this->session->set_flashdata('error', lang('group_x_b_deleted'));
            admin_redirect('system_settings/user_groups');
        }

        if ($this->settings_model->deleteGroup($id)) {
            $this->session->set_flashdata('message', lang('group_deleted'));
            admin_redirect('system_settings/user_groups');
        }
    }
	
    public function delete_price_group( $id = null )
    {
        if ($this->settings_model->deletePriceGroup($id)) {
            $this->rerp->send_json(['error' => 0, 'msg' => lang('price_group_deleted')]);
        }
    }
	
    public function delete_tax_rate( $id = null )
    {
        if ($this->settings_model->deleteTaxRate($id)) {
            $this->rerp->send_json(['error' => 0, 'msg' => lang('tax_rate_deleted')]);
        }
    }
	
    public function delete_unit( $id = null )
    {
        if ($this->settings_model->getUnitChildren($id)) {
            $this->rerp->send_json(['error' => 1, 'msg' => lang('unit_has_subunit')]);
        }

        if ($this->settings_model->deleteUnit($id)) {
            $this->rerp->send_json(['error' => 0, 'msg' => lang('unit_deleted')]);
        }
    }
	
    public function delete_variant( $id = null )
    {
        if ($this->settings_model->deleteVariant($id)) {
            $this->rerp->send_json(['error' => 0, 'msg' => lang('variant_deleted')]);
        }
    }
	
    public function delete_warehouse( $id = null )
    {
        if ($this->settings_model->deleteWarehouse($id)) {
            $this->rerp->send_json(['error' => 0, 'msg' => lang('warehouse_deleted')]);
        }
    }
	
    public function download_backup( $zipfile )
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->load->helper('download');
        force_download('./files/backups/' . $zipfile . '.zip', null);
        exit();
    }
	
    public function download_database( $dbfile )
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->load->library('zip');
        $this->zip->read_file('./files/backups/' . $dbfile . '.txt');
        $name = $dbfile . '.zip';
        $this->zip->download($name);
        exit();
    }
	
    public function edit_brand( $id = null )
    {
        $this->form_validation->set_rules('name', lang('brand_name'), 'trim|required|alpha_numeric_spaces');
        $brand_details = $this->site->getBrandByID($id);
        if ($this->input->post('name') != $brand_details->name) {
            $this->form_validation->set_rules('name', lang('brand_name'), 'required|is_unique[brands.name]');
        }
        $this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash');
        if ($this->input->post('slug') != $brand_details->slug) {
            $this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash|is_unique[brands.slug]');
        }
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run() == true) {
            $data = [
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'slug'        => $this->input->post('slug'),
                'description' => $this->input->post('description'),
            ];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = $this->upload_path . $photo;
                $config['new_image']      = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = true;
                $config['width']          = $this->Settings->twidth;
                $config['height']         = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                $this->image_lib->clear();
            }
        } elseif ($this->input->post('edit_brand')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/brands');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateBrand($id, $data)) {
            $this->session->set_flashdata('message', lang('brand_updated'));
            admin_redirect('system_settings/brands');
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['brand']    = $brand_details;
            $this->load->view($this->theme . 'settings/edit_brand', $this->data);
        }
    }
	
	public function edit_category( $id = null ) {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('category_code'), 'trim|required');
        $pr_details = $this->settings_model->getCategoryByID($id);
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang('category_code'), 'required|is_unique[categories.code]');
        }
        $this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash');
        if ($this->input->post('slug') != $pr_details->slug) {
            $this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash|is_unique[categories.slug]');
        }
        $this->form_validation->set_rules('name', lang('category_name'), 'required|min_length[3]');
        $this->form_validation->set_rules('userfile', lang('category_image'), 'xss_clean');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');
		$this->form_validation->set_rules('menu_order', lang('menu_order'), 'trim|numeric');
		
		if ( $this->form_validation->run() == true ) {
            $data = [
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'slug'        => $this->input->post('slug'),
                'description' => $this->input->post('description'),
                'parent_id'   => absint( $this->input->post('parent') ),
                'featured'    => absint( $this->input->post('featured') ),
                'menu_order'  => absint( $this->input->post('menu_order') ),
            ];
			
			if ( $_FILES['userfile']['size'] > 0 ) {
                $this->load->library('upload');
                $config['upload_path']   = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = $this->Settings->iwidth;
                $config['max_height']    = $this->Settings->iheight;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo         = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = $this->upload_path . $photo;
                $config['new_image']      = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = true;
                $config['width']          = $this->Settings->twidth;
                $config['height']         = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image']     = $this->upload_path . $photo;
                    $wm['wm_text']          = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type']          = 'text';
                    $wm['wm_font_path']     = 'system/fonts/texb.ttf';
                    $wm['quality']          = '100';
                    $wm['wm_font_size']     = '16';
                    $wm['wm_font_color']    = '999999';
                    $wm['wm_shadow_color']  = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'left';
                    $wm['wm_padding']       = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = null;
            }
		} elseif ( $this->input->post( 'edit_category' ) ) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/categories');
        }
		
		if ( $this->form_validation->run() == true && $this->settings_model->updateCategory( $id, $data ) ) {
			ci_delete_category_caches();
            $this->session->set_flashdata('message', lang('category_updated'));
            admin_redirect('system_settings/categories');
        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
	        $category                 = $this->settings_model->getCategoryByID($id);
            $this->data['category']   = $category;
            $catPost                  = $this->input->post( 'parent', true );
            $this->data['categories'] = $this->build_category_dropdown_options( null, $category->id, ( $catPost ?? $category->parent_id ) );
            $this->data['modal_js']   = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_category', $this->data);
        }
    }
	
    public function edit_currency( $id = null )
    {
        $this->form_validation->set_rules('code', lang('currency_code'), 'trim|required');
        $cur_details = $this->settings_model->getCurrencyByID($id);
        if ($this->input->post('code') != $cur_details->code) {
            $this->form_validation->set_rules('code', lang('currency_code'), 'required|is_unique[currencies.code]');
        }
        $this->form_validation->set_rules('name', lang('currency_name'), 'required');
        $this->form_validation->set_rules('rate', lang('exchange_rate'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = ['code'   => $this->input->post('code'),
                'name'        => $this->input->post('name'),
                'rate'        => $this->input->post('rate'),
                'symbol'      => $this->input->post('symbol'),
                'auto_update' => $this->input->post('auto_update') ? $this->input->post('auto_update') : 0,
            ];
        } elseif ($this->input->post('edit_currency')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/currencies');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCurrency($id, $data)) {
        	//check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('currency_updated'));
            admin_redirect('system_settings/currencies');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['currency'] = $this->settings_model->getCurrencyByID($id);
            $this->data['id']       = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_currency', $this->data);
        }
    }
	
    public function edit_customer_group( $id = null )
    {
        $this->form_validation->set_rules('name', lang('group_name'), 'trim|required');
        $pg_details = $this->settings_model->getCustomerGroupByID($id);
        if ($this->input->post('name') != $pg_details->name) {
            $this->form_validation->set_rules('name', lang('group_name'), 'required|is_unique[tax_rates.name]');
        }
        $this->form_validation->set_rules('percent', lang('group_percentage'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name'),
                'percent'   => $this->input->post('percent'),
            ];
        } elseif ($this->input->post('edit_customer_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/customer_groups');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateCustomerGroup($id, $data)) {
            $this->session->set_flashdata('message', lang('customer_group_updated'));
            admin_redirect('system_settings/customer_groups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['customer_group'] = $this->settings_model->getCustomerGroupByID($id);

            $this->data['id']       = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_customer_group', $this->data);
        }
    }
	
    public function edit_expense_category( $id = null )
    {
        $this->form_validation->set_rules('code', lang('category_code'), 'trim|required');
        $category = $this->settings_model->getExpenseCategoryByID($id);
        if ($this->input->post('code') != $category->code) {
            $this->form_validation->set_rules('code', lang('category_code'), 'required|is_unique[expense_categories.code]');
        }
        $this->form_validation->set_rules('name', lang('category_name'), 'required|min_length[3]');

        if ($this->form_validation->run() == true) {
            $data = [
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
            ];
        } elseif ($this->input->post('edit_expense_category')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/expense_categories');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateExpenseCategory( $id, $data ) ) {
            $this->session->set_flashdata('message', lang('expense_category_updated'));
            admin_redirect('system_settings/expense_categories');
        } else {
            $this->data['error']    = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['category'] = $category;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_expense_category', $this->data);
        }
    }
	
    public function edit_group( $id )
    {
        if (!$id || empty($id)) {
            admin_redirect('system_settings/user_groups');
        }

        $group = $this->settings_model->getGroupByID($id);

        $this->form_validation->set_rules('group_name', lang('group_name'), 'required|alpha_dash');

        if ($this->form_validation->run() === true) {
            $data         = ['name' => strtolower($this->input->post('group_name')), 'description' => $this->input->post('description')];
            $group_update = $this->settings_model->updateGroup($id, $data);

            if ($group_update) {
                $this->session->set_flashdata('message', lang('group_udpated'));
            } else {
                $this->session->set_flashdata('error', lang('attempt_failed'));
            }
            admin_redirect('system_settings/user_groups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['group'] = $group;

            $this->data['group_name'] = [
                'name'  => 'group_name',
                'id'    => 'group_name',
                'type'  => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('group_name', $group->name),
            ];
            $this->data['group_description'] = [
                'name'  => 'group_description',
                'id'    => 'group_description',
                'type'  => 'text',
                'class' => 'form-control',
                'value' => $this->form_validation->set_value('group_description', $group->description),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_group', $this->data);
        }
    }
	
    public function edit_price_group( $id = null )
    {
        $this->form_validation->set_rules('name', lang('group_name'), 'trim|required|alpha_numeric_spaces');
        $pg_details = $this->settings_model->getPriceGroupByID($id);
        if ($this->input->post('name') != $pg_details->name) {
            $this->form_validation->set_rules('name', lang('group_name'), 'required|is_unique[price_groups.name]');
        }

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name')];
        } elseif ($this->input->post('edit_price_group')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/price_groups');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updatePriceGroup($id, $data)) {
            $this->session->set_flashdata('message', lang('price_group_updated'));
            admin_redirect('system_settings/price_groups');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['price_group'] = $pg_details;
            $this->data['id']          = $id;
            $this->data['modal_js']    = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_price_group', $this->data);
        }
    }
	
    public function edit_tax_rate( $id = null )
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $tax_details = $this->settings_model->getTaxRateByID($id);
        if ($this->input->post('name') != $tax_details->name) {
            $this->form_validation->set_rules('name', lang('name'), 'required|is_unique[tax_rates.name]');
        }
        $this->form_validation->set_rules('type', lang('type'), 'required');
        $this->form_validation->set_rules('rate', lang('tax_rate'), 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name'),
                'code'      => $this->input->post('code'),
                'type'      => $this->input->post('type'),
                'rate'      => $this->input->post('rate'),
            ];
        } elseif ($this->input->post('edit_tax_rate')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/tax_rates');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateTaxRate($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('tax_rate_updated'));
            admin_redirect('system_settings/tax_rates');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['tax_rate'] = $this->settings_model->getTaxRateByID($id);

            $this->data['id']       = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_tax_rate', $this->data);
        }
    }
	
    public function edit_unit( $id = null )
    {
        $this->form_validation->set_rules('code', lang('code'), 'trim|required');
        $unit_details = $this->site->getUnitByID($id);
        if ($this->input->post('code') != $unit_details->code) {
            $this->form_validation->set_rules('code', lang('code'), 'required|is_unique[units.code]');
        }
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        if ($this->input->post('base_unit')) {
            $this->form_validation->set_rules('operator', lang('operator'), 'required');
            $this->form_validation->set_rules('operation_value', lang('operation_value'), 'trim|required');
        }

        if ($this->form_validation->run() == true) {
            $data = [
                'name'            => $this->input->post('name'),
                'code'            => $this->input->post('code'),
                'base_unit'       => $this->input->post('base_unit') ? $this->input->post('base_unit') : null,
                'operator'        => $this->input->post('base_unit') ? $this->input->post('operator') : null,
                'operation_value' => $this->input->post('operation_value') ? $this->input->post('operation_value') : null,
            ];
        } elseif ($this->input->post('edit_unit')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/units');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateUnit($id, $data)) {
            $this->session->set_flashdata('message', lang('unit_updated'));
            admin_redirect('system_settings/units');
        } else {
            $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js']   = $this->site->modal_js();
            $this->data['unit']       = $unit_details;
            $this->data['base_units'] = $this->site->getAllBaseUnits();
            $this->load->view($this->theme . 'settings/edit_unit', $this->data);
        }
    }
	
    public function edit_variant( $id = null )
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $tax_details = $this->settings_model->getVariantByID($id);
        if ($this->input->post('name') != $tax_details->name) {
            $this->form_validation->set_rules('name', lang('name'), 'required|is_unique[variants.name]');
        }

        if ($this->form_validation->run() == true) {
            $data = ['name' => $this->input->post('name')];
        } elseif ($this->input->post('edit_variant')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/variants');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateVariant($id, $data)) {
            $this->session->set_flashdata('message', lang('variant_updated'));
            admin_redirect('system_settings/variants');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['variant']  = $tax_details;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_variant', $this->data);
        }
    }
	
    public function edit_warehouse( $id = null )
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('code', lang('code'), 'trim|required');
        $wh_details = $this->settings_model->getWarehouseByID($id);
        if ($this->input->post('code') != $wh_details->code) {
            $this->form_validation->set_rules('code', lang('code'), 'required|is_unique[warehouses.code]');
        }
        $this->form_validation->set_rules('address', lang('address'), 'required');
        $this->form_validation->set_rules('map', lang('map_image'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            $data = ['code'      => $this->input->post('code'),
                'name'           => $this->input->post('name'),
                'phone'          => $this->input->post('phone'),
                'email'          => $this->input->post('email'),
                'address'        => $this->input->post('address'),
                'price_group_id' => $this->input->post('price_group'),
            ];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = $this->allowed_file_size;
                $config['max_width']     = '2000';
                $config['max_height']    = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message', $error);
                    admin_redirect('system_settings/warehouses');
                }

                $data['map'] = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $data['map'];
                $config['new_image']      = 'assets/uploads/thumbs/' . $data['map'];
                $config['maintain_ratio'] = true;
                $config['width']          = 76;
                $config['height']         = 76;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        } elseif ($this->input->post('edit_warehouse')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/warehouses');
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateWarehouse($id, $data)) { //check to see if we are updateing the customer
            $this->session->set_flashdata('message', lang('warehouse_updated'));
            admin_redirect('system_settings/warehouses');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['warehouse']    = $this->settings_model->getWarehouseByID($id);
            $this->data['price_groups'] = $this->settings_model->getAllPriceGroups();
            $this->data['id']           = $id;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/edit_warehouse', $this->data);
        }
    }
	
    public function email_templates($template = 'credentials')
    {
        $this->form_validation->set_rules('mail_body', lang('mail_message'), 'trim|required');
        $this->load->helper('file');
        $temp_path = is_dir('./themes/' . $this->theme . 'email_templates/');
        $theme     = $temp_path ? $this->theme : 'default';
        if ($this->form_validation->run() == true) {
            $data = $_POST['mail_body'];
            if (write_file('./themes/' . $this->theme . 'email_templates/' . $template . '.html', $data)) {
                $this->session->set_flashdata('message', lang('message_successfully_saved'));
                admin_redirect('system_settings/email_templates#' . $template);
            } else {
                $this->session->set_flashdata('error', lang('failed_to_save_message'));
                admin_redirect('system_settings/email_templates#' . $template);
            }
        } else {
            $this->data['credentials']     = file_get_contents('./themes/' . $this->theme . 'email_templates/credentials.html');
            $this->data['sale']            = file_get_contents('./themes/' . $this->theme . 'email_templates/sale.html');
            $this->data['quote']           = file_get_contents('./themes/' . $this->theme . 'email_templates/quote.html');
            $this->data['purchase']        = file_get_contents('./themes/' . $this->theme . 'email_templates/purchase.html');
            $this->data['transfer']        = file_get_contents('./themes/' . $this->theme . 'email_templates/transfer.html');
            $this->data['payment']         = file_get_contents('./themes/' . $this->theme . 'email_templates/payment.html');
            $this->data['forgot_password'] = file_get_contents('./themes/' . $this->theme . 'email_templates/forgot_password.html');
            $this->data['activate_email']  = file_get_contents('./themes/' . $this->theme . 'email_templates/activate_email.html');
            $bc                            = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('email_templates')]];
            $meta                          = ['page_title' => lang('email_templates'), 'bc' => $bc];
            $this->page_construct('settings/email_templates', $meta, $this->data);
        }
    }
	
    public function expense_categories()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('expense_categories')]];
        $meta                = ['page_title' => lang('categories'), 'bc' => $bc];
        $this->page_construct('settings/expense_categories', $meta, $this->data);
    }
	
    public function expense_category_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteCategory($id);
                    }
                    $this->session->set_flashdata('message', lang('categories_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->settings_model->getCategoryByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->name);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical('center');
                    $filename = 'expense_categories_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
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
	
    public function getBrands()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, image, code, name, slug')
            ->from('brands')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_brand/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_brand') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_brand') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_brand/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }
	
    public function getCategories()
    {
        $print_barcode = anchor('admin/products/print_barcodes/?category=$1', '<i class="fa fa-print"></i>', 'title="' . lang('print_barcodes') . '" class="tip"');

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('categories')}.id as id, {$this->db->dbprefix('categories')}.image, {$this->db->dbprefix('categories')}.code, {$this->db->dbprefix('categories')}.name, {$this->db->dbprefix('categories')}.slug, c.name as parent, IF( {$this->db->dbprefix('categories')}.featured=1, '<i class=\"fa fa-star\"></i>', '<i class=\"fa fa-star-o\"></i>' ) as featured", false)
            ->from('categories')
            ->join('categories c', 'c.id=categories.parent_id', 'left')
            ->group_by('categories.id')
            ->add_column('Actions', '<div class="text-center">' . $print_barcode . " <a href='" . admin_url('system_settings/edit_category/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_category') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_category') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_category/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }
	
    public function getCurrencies()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, code, name, rate, symbol')
            ->from('currencies')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_currency/$1') . "' class='tip' title='" . lang('edit_currency') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_currency') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_currency/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }
	
    public function getCustomerGroups()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, name, percent')
            ->from('customer_groups')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_customer_group/$1') . "' class='tip' title='" . lang('edit_customer_group') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_customer_group') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_customer_group/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }
	
    public function getExpenseCategories()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, code, name')
            ->from('expense_categories')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_expense_category/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_expense_category') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_expense_category') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_expense_category/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }
	
    public function getPriceGroups()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, name')
            ->from('price_groups')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/group_product_prices/$1') . "' class='tip' title='" . lang('group_product_prices') . "'><i class=\"fa fa-eye\"></i></a>  <a href='" . admin_url('system_settings/edit_price_group/$1') . "' class='tip' title='" . lang('edit_price_group') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_price_group') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_price_group/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }
	
    public function getProductPrices($group_id = null)
    {
        if (!$group_id) {
            $this->session->set_flashdata('error', lang('no_price_group_selected'));
            admin_redirect('system_settings/price_groups');
        }

        $pp = "( SELECT {$this->db->dbprefix('product_prices')}.product_id as product_id, {$this->db->dbprefix('product_prices')}.price as price FROM {$this->db->dbprefix('product_prices')} WHERE price_group_id = {$group_id} ) PP";

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('products')}.id as id, {$this->db->dbprefix('products')}.code as product_code, {$this->db->dbprefix('products')}.name as product_name, PP.price as price ")
            ->from('products')
            ->join($pp, 'PP.product_id=products.id', 'left')
            ->edit_column('price', '$1__$2', 'id, price')
            ->add_column('Actions', '<div class="text-center"><button class="btn btn-primary btn-xs form-submit" type="button"><i class="fa fa-check"></i></button></div>', 'id');

        echo $this->datatables->generate();
    }
	
    public function getTaxRates()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, name, code, rate, type')
            ->from('tax_rates')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_tax_rate/$1') . "' class='tip' title='" . lang('edit_tax_rate') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_tax_rate') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_tax_rate/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }
	
    public function getUnits()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('units')}.id as id, {$this->db->dbprefix('units')}.code, {$this->db->dbprefix('units')}.name, b.name as base_unit, {$this->db->dbprefix('units')}.operator, {$this->db->dbprefix('units')}.operation_value", false)
            ->from('units')
            ->join('units b', 'b.id=units.base_unit', 'left')
            ->group_by('units.id')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_unit/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_unit') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_unit') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_unit/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }
	
    public function getVariants()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, name')
            ->from('variants')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_variant/$1') . "' class='tip' title='" . lang('edit_variant') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_variant') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_variant/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
        //->unset_column('id');

        echo $this->datatables->generate();
    }
	
    public function getWarehouses()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('warehouses')}.id as id, map, code, {$this->db->dbprefix('warehouses')}.name as name, {$this->db->dbprefix('price_groups')}.name as price_group, phone, email, address")
            ->from('warehouses')
            ->join('price_groups', 'price_groups.id=warehouses.price_group_id', 'left')
            ->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('system_settings/edit_warehouse/$1') . "' class='tip' title='" . lang('edit_warehouse') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_warehouse') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_warehouse/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

        echo $this->datatables->generate();
    }
	
    public function group_product_prices( $group_id = null )
    {
        if (!$group_id) {
            $this->session->set_flashdata('error', lang('no_price_group_selected'));
            admin_redirect('system_settings/price_groups');
        }

        $this->data['price_group'] = $this->settings_model->getPriceGroupByID($group_id);
        $this->data['error']       = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                        = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')],  ['link' => admin_url('system_settings/price_groups'), 'page' => lang('price_groups')], ['link' => '#', 'page' => lang('group_product_prices')]];
        $meta                      = ['page_title' => lang('group_product_prices'), 'bc' => $bc];
        $this->page_construct('settings/group_product_prices', $meta, $this->data);
    }
	
    public function import_brands() {
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');
        if ($this->form_validation->run() == true) {
	        if ( isset( $_FILES['userfile'] ) ) {
		        $this->load->library( 'upload' );
		        $this->upload->initialize( $this->import_config );
		
		        if ( ! $this->upload->do_upload() ) {
			        $error = $this->upload->display_errors();
			        $this->session->set_flashdata( 'error', $error );
			        admin_redirect( 'system_settings/brands' );
		        }
		
		        $csv = $this->upload->file_name;
		
		        $brands = $arrResult = [];
		        $handle    = fopen( 'files/' . $csv, 'r' );
		        if ( $handle ) {
					fgetcsv( $handle );
					$updated = '';
					while ( ( $row = fgetcsv( $handle ) ) !== false ) {
						$row = array_filter( $row );
						if ( count( $row ) < 2 ) {
							// image can be missing.
							continue;
						}
						$brand = [
							'code'  => trim( $row[1] ),
							'name'  => trim( $row[0] ),
							'image' => isset( $row[2] ) ? trim( $row[2] ) : '',
						];
						$exist = $this->settings_model->getBrandByCode( $row[1] );
						if ( $exist && $exist->id ) {
							$this->settings_model->updateBrand( $exist->id, $brand );
							$updated .= '<p>' . lang('brand_updated') . ' (' . $brand['code'] . ')</p>';
						} else {
							$this->settings_model->addBrand( $brand );
						}
					}
			        $this->session->set_flashdata( 'message', sprintf( '<p>%s</p>%s', lang( 'brands_added' ), $updated ) );
			        fclose( $handle );
		        } else {
			        $this->session->set_flashdata( 'error', 'Data Import Failed' );
		        }
	        } else {
		        $this->session->set_flashdata( 'error', 'No file uploaded.' );
	        }
	        admin_redirect( 'system_settings/brands' );
        } else {
	        $this->data['error']    = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
	        $this->data['userfile'] = [
		        'name'  => 'userfile',
		        'id'    => 'userfile',
		        'type'  => 'text',
		        'value' => $this->form_validation->set_value( 'userfile' ),
	        ];
	        $this->data['modal_js'] = $this->site->modal_js();
	        $this->load->view( $this->theme . 'settings/import_brands', $this->data );
        }
    }
	
    public function import_categories()
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('system_settings/categories');
                }
                $csv       = $this->upload->file_name;
                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles     = array_shift($arrResult);
                $updated    = '';
                $categories = $subcategories = [];
                foreach ($arrResult as $key => $value) {
                    $code  = trim($value[0]);
                    $name  = trim($value[1]);
                    $pcode = isset($value[4]) ? trim($value[4]) : null;
                    if ($code && $name && trim($value[2])) {
                        $category = [
                            'code'        => $code,
                            'name'        => $name,
                            'slug'        => isset($value[2]) ? trim($value[2]) : $code,
                            'image'       => isset($value[3]) ? trim($value[3]) : '',
                            'parent_id'   => $pcode,
                            'description' => isset($value[5]) ? trim($value[5]) : null,
	                        'menu_order'  => isset($value[6]) ? absint($value[6]) : 0,
                        ];
                        if (!empty($pcode) && ($pcategory = $this->settings_model->getCategoryByCode($pcode))) {
                            $category['parent_id'] = $pcategory->id;
                        }
                        if ($c = $this->settings_model->getCategoryByCode($code)) {
                            $updated .= '<p>' . lang('category_updated') . ' (' . $code . ')</p>';
                            $this->settings_model->updateCategory($c->id, $category);
                        } else {
                            if ($category['parent_id']) {
                                $subcategories[] = $category;
                            } else {
                                $categories[] = $category;
                            }
                        }
                    }
                }
            }

            // $this->rerp->print_arrays($categories, $subcategories);
        }

        if ($this->form_validation->run() == true && $this->settings_model->addCategories($categories, $subcategories)) {
            $this->session->set_flashdata('message', lang('categories_added') . $updated);
            admin_redirect('system_settings/categories');
        } else {
            if ((isset($categories) && empty($categories)) || (isset($subcategories) && empty($subcategories))) {
                if ($updated) {
                    $this->session->set_flashdata('message', $updated);
                } else {
                    $this->session->set_flashdata('warning', lang('data_x_categories'));
                }
                admin_redirect('system_settings/categories');
            }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/import_categories', $this->data);
        }
    }
    
    public function import_units() {
        $this->load->helper('security');
	    $this->form_validation->set_rules( 'userfile', lang( 'upload_file' ), 'xss_clean' );
	    if ( $this->form_validation->run() == true ) {
		    if ( isset( $_FILES['userfile'] ) ) {
                $this->load->library('upload');
			    $this->upload->initialize( $this->import_config );
			    if ( ! $this->upload->do_upload() ) {
				    $error = $this->upload->display_errors();
				    $this->session->set_flashdata( 'error', $error );
				    admin_redirect( 'system_settings/units' );
			    }
                $handle    = fopen('files/' . $this->upload->file_name, 'r');
			    if ( $handle ) {
                	// Ignore the first (heading) row.
	                fgetcsv( $handle );
	                $updated   = '';
	                $subUnits  = [];
	                $existing  = [];
	                while ( ( $row = fgetcsv( $handle ) ) !== false ) {
	                	$row = array_filter( $row );
		                if ( count( $row ) < 2 || ( empty( $row[0] ) || empty( $row[1] ) ) ) {
		                	continue;
		                }
		                $unit = [
			                'code'            => $row[0],
			                'name'            => $row[1],
			                'base_unit'       => isset( $row[2] ) && ! empty( $row[2] ) ? $row[2] : null,
			                'operator'        => isset( $row[3] ) && ! empty( $row[3] ) ? $row[3] : null,
			                'operation_value' => isset( $row[4] ) && ! empty( $row[4] ) ? $row[4] : null,
		                ];
		                
		                // check for existing one.
		                $exists = $this->settings_model->getUnitByCode( $unit['code'] );
		                if ( $exists ) {
		                	$existing[ $unit['code'] ] = $exists->id;
			                $updated .= '<p>' . lang('unit_updated') . ' (' . $unit['code'] . ')</p>';
			                $this->settings_model->updateUnit( $exists->id, $unit );
		                } else {
		                	// list main & sub separately so we can insert the subs after inserting
			                // new item and then use the id with the subs if needed.
			                if ( empty( $unit['base_unit'] ) ) {
				                $id = $this->settings_model->addUnit( $unit );
				                if ( $id ) {
					                $existing[ $unit['code'] ] = $id;
				                }
			                } else {
				                $baseFound = false;
				                if ( isset( $existing[ $unit['base_unit'] ] ) ) {
					                $unit['base_unit'] = $existing[ $unit['base_unit'] ];
					                $baseFound = true;
				                } else {
					                $parent = $this->settings_model->getUnitByCode( $unit['base_unit'] );
					                if ( $parent ) {
						                $unit['base_unit'] = $parent->id;
						                $baseFound = true;
					                }
				                }
				                if ( $baseFound ) {
					                $id = $this->settings_model->addUnit( $unit );
					                if ( $id ) {
						                $existing[ $unit['code'] ] = $id;
					                }
				                } else {
					                // the remaining subs.
					                $subUnits[] = $unit;
				                }
			                }
		                }
	                }
	                fclose( $handle );
	                if ( ! empty( $subUnits ) ) {
		                $this->settings_model->addUnits( [], $subUnits );
	                }
				    $this->session->set_flashdata( 'message', '<p>Units Added.</p>' . $updated );
                } else {
				    $this->session->set_flashdata( 'error', 'Data Import Failed' );
			    }
            } else {
			    $this->session->set_flashdata( 'error', 'No file uploaded.' );
		    }
		    admin_redirect( 'system_settings/units' );
        } else {
		    
		    $this->data['error']    = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
		    $this->data['userfile'] = [
			    'name'  => 'userfile',
			    'id'    => 'userfile',
			    'type'  => 'text',
			    'value' => $this->form_validation->set_value( 'userfile' ),
		    ];
		    $this->data['modal_js'] = $this->site->modal_js();
		    $this->load->view( $this->theme . 'settings/import_units', $this->data );
	    }
    }
	
    public function import_expense_categories()
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('system_settings/expense_categories');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys   = ['code', 'name'];
                $final  = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                foreach ($final as $csv_ct) {
                    if (!$this->settings_model->getExpenseCategoryByCode(trim($csv_ct['code']))) {
                        $data[] = [
                            'code' => trim($csv_ct['code']),
                            'name' => trim($csv_ct['name']),
                        ];
                    }
                }
            }

            // $this->rerp->print_arrays($data);
        }

        if ($this->form_validation->run() == true && $this->settings_model->addExpenseCategories($data)) {
            $this->session->set_flashdata('message', lang('categories_added'));
            admin_redirect('system_settings/expense_categories');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/import_expense_categories', $this->data);
        }
    }
	
    public function import_subcategories()
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('system_settings/categories');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys   = ['code', 'name', 'category_code', 'image'];
                $final  = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                $rw = 2;
                foreach ($final as $csv_ct) {
                    if (!$this->settings_model->getSubcategoryByCode(trim($csv_ct['code']))) {
                        if ($parent_actegory = $this->settings_model->getCategoryByCode(trim($csv_ct['category_code']))) {
                            $data[] = [
                                'code'        => trim($csv_ct['code']),
                                'name'        => trim($csv_ct['name']),
                                'image'       => trim($csv_ct['image']),
                                'category_id' => $parent_actegory->id,
                            ];
                        } else {
                            $this->session->set_flashdata('error', lang('check_category_code') . ' (' . $csv_ct['category_code'] . '). ' . lang('category_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                            admin_redirect('system_settings/categories');
                        }
                    }
                    $rw++;
                }
            }

            // $this->rerp->print_arrays($data);
        }

        if ($this->form_validation->run() == true && $this->settings_model->addSubCategories($data)) {
            $this->session->set_flashdata('message', lang('subcategories_added'));
            admin_redirect('system_settings/categories');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/import_subcategories', $this->data);
        }
    }
	
    public function index() {
	    $this->load->library( 'gst' );
	    $this->form_validation->set_rules( 'site_name', lang( 'site_name' ), 'trim|required' );
	    $this->form_validation->set_rules( 'dateformat', lang( 'dateformat' ), 'trim|required' );
	    $this->form_validation->set_rules( 'timezone', lang( 'timezone' ), 'trim|required' );
	    $this->form_validation->set_rules( 'mmode', lang( 'maintenance_mode' ), 'trim|required' );
	    //$this->form_validation->set_rules('logo', lang('logo'), 'trim');
	    $this->form_validation->set_rules( 'iwidth', lang( 'image_width' ), 'trim|numeric|required' );
	    $this->form_validation->set_rules( 'iheight', lang( 'image_height' ), 'trim|numeric|required' );
	    $this->form_validation->set_rules( 'twidth', lang( 'thumbnail_width' ), 'trim|numeric|required' );
	    $this->form_validation->set_rules( 'theight', lang( 'thumbnail_height' ), 'trim|numeric|required' );
	    $this->form_validation->set_rules( 'display_all_products', lang( 'display_all_products' ), 'trim|numeric|required' );
	    $this->form_validation->set_rules( 'watermark', lang( 'watermark' ), 'trim|required' );
	    $this->form_validation->set_rules( 'currency', lang( 'default_currency' ), 'trim|required' );
	    $this->form_validation->set_rules( 'email', lang( 'default_email' ), 'trim|required' );
	    $this->form_validation->set_rules( 'language', lang( 'language' ), 'trim|required' );
	    $this->form_validation->set_rules( 'warehouse', lang( 'default_warehouse' ), 'trim|required' );
	    $this->form_validation->set_rules( 'biller', lang( 'default_biller' ), 'trim|required' );
	    $this->form_validation->set_rules( 'tax_rate', lang( 'product_tax' ), 'trim|required' );
	    $this->form_validation->set_rules( 'tax_rate2', lang( 'invoice_tax' ), 'trim|required' );
	    $this->form_validation->set_rules( 'sales_prefix', lang( 'sales_prefix' ), 'trim' );
	    $this->form_validation->set_rules( 'quote_prefix', lang( 'quote_prefix' ), 'trim' );
	    $this->form_validation->set_rules( 'purchase_prefix', lang( 'purchase_prefix' ), 'trim' );
	    $this->form_validation->set_rules( 'transfer_prefix', lang( 'transfer_prefix' ), 'trim' );
	    $this->form_validation->set_rules( 'delivery_prefix', lang( 'delivery_prefix' ), 'trim' );
	    $this->form_validation->set_rules( 'payment_prefix', lang( 'payment_prefix' ), 'trim' );
	    $this->form_validation->set_rules( 'return_prefix', lang( 'return_prefix' ), 'trim' );
	    $this->form_validation->set_rules( 'expense_prefix', lang( 'expense_prefix' ), 'trim' );
	    $this->form_validation->set_rules( 'detect_barcode', lang( 'detect_barcode' ), 'trim|required' );
	    $this->form_validation->set_rules( 'theme', lang( 'theme' ), 'trim|required' );
	    $this->form_validation->set_rules( 'rows_per_page', lang( 'rows_per_page' ), 'trim|required' );
	    $this->form_validation->set_rules( 'accounting_method', lang( 'accounting_method' ), 'trim|required' );
	    $this->form_validation->set_rules( 'product_serial', lang( 'product_serial' ), 'trim|required' );
	    $this->form_validation->set_rules( 'product_discount', lang( 'product_discount' ), 'trim|required' );
	    $this->form_validation->set_rules( 'bc_fix', lang( 'bc_fix' ), 'trim|numeric|required' );
	    $this->form_validation->set_rules( 'protocol', lang( 'email_protocol' ), 'trim|required' );
	    if ( $this->input->post( 'protocol' ) == 'smtp' ) {
		    $this->form_validation->set_rules( 'smtp_host', lang( 'smtp_host' ), 'required' );
		    $this->form_validation->set_rules( 'smtp_user', lang( 'smtp_user' ), 'required' );
		    $this->form_validation->set_rules( 'smtp_pass', lang( 'smtp_pass' ), 'required' );
		    $this->form_validation->set_rules( 'smtp_port', lang( 'smtp_port' ), 'required' );
	    }
	    if ( $this->input->post( 'protocol' ) == 'sendmail' ) {
		    $this->form_validation->set_rules( 'mailpath', lang( 'mailpath' ), 'required' );
	    }
	    $this->form_validation->set_rules( 'decimals', lang( 'decimals' ), 'trim|required' );
	    $this->form_validation->set_rules( 'decimals_sep', lang( 'decimals_sep' ), 'trim|required' );
	    $this->form_validation->set_rules( 'thousands_sep', lang( 'thousands_sep' ), 'trim|required' );
	
	    $this->form_validation->set_rules('notification_email', lang('notification_email'), 'trim|required|valid_email');
	    $this->form_validation->set_rules( 'minimum_withdrawal', lang( 'minimum_withdrawal' ), 'trim|required|numeric' );
	    $this->form_validation->set_rules( 'referral_url', lang( 'referral_url' ), 'trim|required' );
	    $this->form_validation->set_rules( 'cookie_lifetime', lang( 'cookie_lifetime' ), 'trim|required|numeric' );
	    
	    if ( $this->Settings->indian_gst ) {
		    $this->form_validation->set_rules( 'state', lang( 'state' ), 'trim|required' );
	    }
	
	    if ( $this->form_validation->run() == true ) {
		    $language = $this->input->post( 'language' );
		
		    if ( ( file_exists( APPPATH . 'language' . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'rerp_lang.php' ) && is_dir( APPPATH . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $language ) ) || $language == 'english' ) {
                $lang = $language;
            } else {
			    $this->session->set_flashdata( 'error', lang( 'language_x_found' ) );
			    admin_redirect( 'system_settings' );
			    $lang = 'english';
            }
		
		    $tax1 = ( $this->input->post( 'tax_rate' ) != 0 ) ? 1 : 0;
		    $tax2 = ( $this->input->post( 'tax_rate2' ) != 0 ) ? 1 : 0;
		
		    $data = [
			    'site_name'            => DEMO ? 'RtailERP' : $this->input->post( 'site_name' ),
			    'rows_per_page'        => $this->input->post( 'rows_per_page' ),
			    'dateformat'           => $this->input->post( 'dateformat' ),
			    'timezone'             => DEMO ? 'Asia/Kuala_Lumpur' : $this->input->post( 'timezone' ),
			    'mmode'                => trim( $this->input->post( 'mmode' ) ),
			    'iwidth'               => $this->input->post( 'iwidth' ),
			    'iheight'              => $this->input->post( 'iheight' ),
			    'twidth'               => $this->input->post( 'twidth' ),
			    'theight'              => $this->input->post( 'theight' ),
			    'watermark'            => $this->input->post( 'watermark' ),
			    // 'reg_ver' => $this->input->post('reg_ver'),
			    // 'allow_reg' => $this->input->post('allow_reg'),
			    // 'reg_notification' => $this->input->post('reg_notification'),
			    'accounting_method'    => $this->input->post( 'accounting_method' ),
			    'default_email'        => DEMO ? 'noreply@retailpremier.com' : $this->input->post( 'email' ),
			    'language'             => $lang,
			    'default_warehouse'    => $this->input->post( 'warehouse' ),
			    'default_tax_rate'     => $this->input->post( 'tax_rate' ),
			    'default_tax_rate2'    => $this->input->post( 'tax_rate2' ),
			    'sales_prefix'         => $this->input->post( 'sales_prefix' ),
			    'quote_prefix'         => $this->input->post( 'quote_prefix' ),
			    'purchase_prefix'      => $this->input->post( 'purchase_prefix' ),
			    'transfer_prefix'      => $this->input->post( 'transfer_prefix' ),
			    'delivery_prefix'      => $this->input->post( 'delivery_prefix' ),
			    'payment_prefix'       => $this->input->post( 'payment_prefix' ),
			    'ppayment_prefix'      => $this->input->post( 'ppayment_prefix' ),
			    'qa_prefix'            => $this->input->post( 'qa_prefix' ),
			    'return_prefix'        => $this->input->post( 'return_prefix' ),
			    'returnp_prefix'       => $this->input->post( 'returnp_prefix' ),
			    'expense_prefix'       => $this->input->post( 'expense_prefix' ),
			    'auto_detect_barcode'  => trim( $this->input->post( 'detect_barcode' ) ),
			    'theme'                => trim( $this->input->post( 'theme' ) ),
			    'product_serial'       => $this->input->post( 'product_serial' ),
			    'customer_group'       => $this->input->post( 'customer_group' ),
			    'product_expiry'       => $this->input->post( 'product_expiry' ),
			    'product_discount'     => $this->input->post( 'product_discount' ),
			    'default_currency'     => $this->input->post( 'currency' ),
			    'bc_fix'               => $this->input->post( 'bc_fix' ),
			    'tax1'                 => $tax1,
			    'tax2'                 => $tax2,
			    'overselling'          => $this->input->post( 'restrict_sale' ),
			    'reference_format'     => $this->input->post( 'reference_format' ),
			    'racks'                => $this->input->post( 'racks' ),
			    'attributes'           => $this->input->post( 'attributes' ),
			    'restrict_calendar'    => $this->input->post( 'restrict_calendar' ),
			    'captcha'              => $this->input->post( 'captcha' ),
			    'item_addition'        => $this->input->post( 'item_addition' ),
			    'protocol'             => DEMO ? 'mail' : $this->input->post( 'protocol' ),
			    'mailpath'             => $this->input->post( 'mailpath' ),
			    'smtp_host'            => $this->input->post( 'smtp_host' ),
			    'smtp_user'            => $this->input->post( 'smtp_user' ),
			    'smtp_port'            => $this->input->post( 'smtp_port' ),
			    'smtp_crypto'          => $this->input->post( 'smtp_crypto' ) ? $this->input->post( 'smtp_crypto' ) : null,
			    'decimals'             => $this->input->post( 'decimals' ),
			    'decimals_sep'         => $this->input->post( 'decimals_sep' ),
			    'thousands_sep'        => $this->input->post( 'thousands_sep' ),
			    'default_biller'       => $this->input->post( 'biller' ),
			    'invoice_view'         => $this->input->post( 'invoice_view' ),
			    'rtl'                  => $this->input->post( 'rtl' ),
			    'each_spent'           => $this->input->post( 'each_spent' ) ? $this->input->post( 'each_spent' ) : null,
			    'ca_point'             => $this->input->post( 'ca_point' ) ? $this->input->post( 'ca_point' ) : null,
			    'each_sale'            => $this->input->post( 'each_sale' ) ? $this->input->post( 'each_sale' ) : null,
			    'sa_point'             => $this->input->post( 'sa_point' ) ? $this->input->post( 'sa_point' ) : null,
			    'sac'                  => $this->input->post( 'sac' ),
			    'qty_decimals'         => $this->input->post( 'qty_decimals' ),
			    'display_all_products' => $this->input->post( 'display_all_products' ),
			    'display_symbol'       => $this->input->post( 'display_symbol' ),
			    'symbol'               => $this->input->post( 'symbol' ),
			    'remove_expired'       => $this->input->post( 'remove_expired' ),
			    'barcode_separator'    => $this->input->post( 'barcode_separator' ),
			    'set_focus'            => $this->input->post( 'set_focus' ),
			    'disable_editing'      => $this->input->post( 'disable_editing' ),
			    'price_group'          => $this->input->post( 'price_group' ),
			    'barcode_img'          => $this->input->post( 'barcode_renderer' ),
			    'update_cost'          => $this->input->post( 'update_cost' ),
			    'apis'                 => $this->input->post( 'apis' ),
			    'pdf_lib'              => $this->input->post( 'pdf_lib' ),
			    'state'                => $this->input->post( 'state' ),
			    'use_code_for_slug'    => $this->input->post( 'use_code_for_slug' ),
		    ];
		    
		    if ( $this->input->post( 'smtp_pass' ) ) {
			    $data['smtp_pass'] = $this->input->post( 'smtp_pass' );
		    }
		    
		    // save commission, referral & affiliate related settings.
		    $commission = [
			    'notification_email' => $this->input->post( 'notification_email' ),
			    'minimum_withdrawal' => absint( $this->input->post( 'minimum_withdrawal' ) ),
			    'cookie_lifetime'    => absint( $this->input->post( 'cookie_lifetime' ) ),
			    'referral_url'       => $this->input->post( 'referral_url' ),
			    'affiliate_url'      => $this->input->post( 'minimum_withdrawal' ),
		    ];
		    
		    if ( ! $commission['cookie_lifetime'] ) {
		    	$commission['cookie_lifetime'] = 30;
		    }
		    
		    $this->Erp_Options->updateOption( '_commission', $commission, false );
		    $auto_delivery = absint( $this->input->post( 'auto_create_delivery' ) );
		    $this->Erp_Options->updateOption( '_auto_create_delivery', $auto_delivery ? 1 : 0, false );
        }
	
	    if ( $this->form_validation->run() == true && $this->settings_model->updateSetting( $data ) ) {
//		    if ( ! DEMO && TIMEZONE != $data['timezone'] ) {
//	            if ( ! $this->write_index( $data['timezone'] ) ) {
//		            $this->session->set_flashdata( 'error', lang( 'setting_updated_timezone_failed' ) );
//		            admin_redirect( 'system_settings' );
//                }
//            }
		
		    $this->session->set_flashdata( 'message', lang( 'setting_updated' ) );
		    admin_redirect( 'system_settings' );
        } else {
		    $this->data['auto_create_delivery']      = $this->Erp_Options->getOption( '_auto_create_delivery', 0 );
	    	
		    $this->data['error']           = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );
		    $this->data['billers']         = $this->site->getAllCompanies( 'biller' );
		    $this->data['settings']        = $this->settings_model->getSettings();
		    $this->data['commission']      = $this->get_commission_settings();
		    $this->data['currencies']      = $this->settings_model->getAllCurrencies();
		    $this->data['date_formats']    = $this->settings_model->getDateFormats();
		    $this->data['tax_rates']       = $this->settings_model->getAllTaxRates();
		    $this->data['customer_groups'] = $this->settings_model->getAllCustomerGroups();
		    $this->data['price_groups']    = $this->settings_model->getAllPriceGroups();
		    $this->data['warehouses']      = $this->settings_model->getAllWarehouses();
		    $this->data['themes']          = $this->get_installed_themes();
		    $bc                            = [
			    [ 'link' => base_url(), 'page' => lang( 'home' ) ],
			    [ 'link' => '#', 'page' => lang( 'system_settings' ) ],
		    ];
		    $meta                          = [
			    'page_title' => lang( 'system_settings' ),
			    'bc'         => $bc,
		    ];
            $this->page_construct('settings/index', $meta, $this->data);
        }
    }
    
    private function get_installed_themes() {
	    $_themes = glob( VIEWPATH . '*/theme.php' );
	    $themes = [
		    'default' => 'Default',
	    ];
	    if ( ! $_themes ) {
	    	return $themes;
	    }
	    
    	foreach( $_themes as $theme ) {
    		if ( ! is_file( $theme ) || ! is_readable( $theme ) ) {
    			continue;
		    }
		    // get the slug
		    $slug = basename( str_replace( 'theme.php', '', $theme ) );
		    /** @noinspection PhpIncludeInspection */
		    $theme = include $theme;
		    if ( ! is_array( $theme ) ) {
		    	continue;
		    }
		
		    $themes[ $slug ] = isset( $theme['name'] ) ? $theme['name'] : $slug;
	    }
    	
    	return $themes;
    }
	
    public function install_update( $file, $m_version, $version )
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
                admin_redirect('system_settings/updates');
            }
        }
        $this->db->update('settings', ['version' => $version, 'update' => 0], ['setting_id' => 1]);
        unlink('./files/updates/' . $file . '.zip');
        $this->session->set_flashdata('success', lang('update_done'));
        admin_redirect('system_settings/updates');
    }
	
    public function paypal()
    {
        $this->form_validation->set_rules('active', $this->lang->line('activate'), 'trim');
        $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'trim|valid_email');
        if ($this->input->post('active')) {
            $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'required');
        }
        $this->form_validation->set_rules('fixed_charges', $this->lang->line('fixed_charges'), 'trim');
        $this->form_validation->set_rules('extra_charges_my', $this->lang->line('extra_charges_my'), 'trim');
        $this->form_validation->set_rules('extra_charges_other', $this->lang->line('extra_charges_others'), 'trim');

        if ($this->form_validation->run() == true) {
            $data = ['active'         => $this->input->post('active'),
                'account_email'       => $this->input->post('account_email'),
                'fixed_charges'       => $this->input->post('fixed_charges'),
                'extra_charges_my'    => $this->input->post('extra_charges_my'),
                'extra_charges_other' => $this->input->post('extra_charges_other'),
            ];
        }

        if ($this->form_validation->run() == true && $this->settings_model->updatePaypal($data)) {
            $this->session->set_flashdata('message', $this->lang->line('paypal_setting_updated'));
            admin_redirect('system_settings/paypal');
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['paypal'] = $this->settings_model->getPaypalSettings();

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('paypal_settings')]];
            $meta = ['page_title' => lang('paypal_settings'), 'bc' => $bc];
            $this->page_construct('settings/paypal', $meta, $this->data);
        }
    }
    
    public function sslcommerz() {
        $this->form_validation->set_rules('active', $this->lang->line('activate'), 'trim');
        $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'trim|valid_email');
	    if ( $this->input->post( 'active' ) ) {
            $this->form_validation->set_rules('store_id', $this->lang->line('ssl_store_id'), 'required');
            $this->form_validation->set_rules('store_password', $this->lang->line('ssl_store_pass'), 'required');
            $this->form_validation->set_rules('merchant_id', $this->lang->line('ssl_merchant_id'), 'required');
            $this->form_validation->set_rules('account_email', $this->lang->line('ssl_merchant_email'), 'required');
        }
        
        $this->form_validation->set_rules('fixed_charges', $this->lang->line('fixed_charges'), 'trim');
        $this->form_validation->set_rules('extra_charges_my', $this->lang->line('extra_charges_my'), 'trim');
        $this->form_validation->set_rules('extra_charges_other', $this->lang->line('extra_charges_others'), 'trim');

        if ($this->form_validation->run() == true) {
            $data = [
	            'active'              => $this->input->post( 'active' ),
	            'store_id'            => $this->input->post( 'store_id' ),
	            'store_password'      => $this->input->post( 'store_password' ),
	            'merchant_id'         => $this->input->post( 'merchant_id' ),
	            'account_email'       => $this->input->post( 'account_email' ),
	            'fixed_charges'       => $this->input->post( 'fixed_charges' ),
	            'extra_charges_my'    => $this->input->post( 'extra_charges_my' ),
	            'extra_charges_other' => $this->input->post( 'extra_charges_other' ),
            ];
        }
	
	    if ( $this->form_validation->run() == true && $this->settings_model->updateSslcommerz( $data ) ) {
		    $this->session->set_flashdata( 'message', $this->lang->line( 'ssl_setting_updated' ) );
            admin_redirect('system_settings/sslcommerz');
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['sslcommerz'] = $this->settings_model->getSslcommerzSettings();

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('ssl_settings')]];
            $meta = ['page_title' => lang('ssl_settings'), 'bc' => $bc];
            $this->page_construct('settings/sslcommerz', $meta, $this->data);
        }
    }
    
    public function cod() {
        $this->form_validation->set_rules('active', $this->lang->line('activate'), 'trim');
        
//        $this->form_validation->set_rules('fixed_charges', $this->lang->line('fixed_charges'), 'trim');
//        $this->form_validation->set_rules('extra_charges_my', $this->lang->line('extra_charges_my'), 'trim');
//        $this->form_validation->set_rules('extra_charges_other', $this->lang->line('extra_charges_others'), 'trim');

        if ($this->form_validation->run() == true) {
            $data = [
	            'active'              => $this->input->post( 'active' ),
//	            'fixed_charges'       => $this->input->post( 'fixed_charges' ),
//	            'extra_charges_my'    => $this->input->post( 'extra_charges_my' ),
//	            'extra_charges_other' => $this->input->post( 'extra_charges_other' ),
            ];
        }
	    
	    if ( $this->form_validation->run() == true && $this->Erp_Options->updateOption( 'cash_on_delivery', $data ) ) {
		    $this->session->set_flashdata( 'message', $this->lang->line( 'cod_setting_updated' ) );
            admin_redirect('system_settings/cod');
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            
		    $this->data['cod'] = (object) ci_parse_args( $this->Erp_Options->getOption( 'cash_on_delivery', [] ), [ 'active' => 1 ] );

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('cod_settings')]];
            $meta = ['page_title' => lang('cod_settings'), 'bc' => $bc];
            $this->page_construct('settings/cod', $meta, $this->data);
        }
    }
    
    public function bank() {
        $this->form_validation->set_rules('active', $this->lang->line('activate'), 'trim');
	
	    if ( $this->input->post( 'active' ) ) {
		    $this->form_validation->set_rules('details', $this->lang->line('bank_details'), 'required');
	    }
        
        $this->form_validation->set_rules('fixed_charges', $this->lang->line('fixed_charges'), 'trim');
        $this->form_validation->set_rules('extra_charges_my', $this->lang->line('extra_charges_my'), 'trim');
        $this->form_validation->set_rules('extra_charges_other', $this->lang->line('extra_charges_others'), 'trim');

        if ($this->form_validation->run() == true) {
            $data = [
	            'active'              => absint( $this->input->post( 'active' ) ),
	            'details'             => $this->input->post( 'details', true ),
	            'fixed_charges'       => (float) $this->input->post( 'fixed_charges' ),
	            'extra_charges_my'    => (float) $this->input->post( 'extra_charges_my' ),
	            'extra_charges_other' => (float) $this->input->post( 'extra_charges_other' ),
            ];
            
            if ( $this->Erp_Options->updateOption( 'bank_payment', $data ) ) {
	            $this->session->set_flashdata( 'message', $this->lang->line( 'bank_setting_updated' ) );
            } else {
	            $this->session->set_flashdata( 'warning', $this->lang->line( 'setting_not_updated' ) );
            }
            admin_redirect('system_settings/bank');
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		
		    $this->data['bank'] = (object) ci_parse_args(
		        $this->Erp_Options->getOption( 'bank_payment', [] ),
			    [
				    'active'              => 1,
				    'details'             => '',
				    'fixed_charges'       => 0,
				    'extra_charges_my'    => 0,
				    'extra_charges_other' => 0,
			    ]
		    );

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('bank_settings')]];
            $meta = ['page_title' => lang('bank_settings'), 'bc' => $bc];
            $this->page_construct('settings/bank', $meta, $this->data);
        }
    }

    public function authorizedotnet() {
        $this->form_validation->set_rules('active', $this->lang->line('activate'), 'trim');

        $this->form_validation->set_rules('mode', $this->lang->line('mode'), 'trim');
        $this->form_validation->set_rules('api_login_id', $this->lang->line('api_login_id'), 'trim');
        $this->form_validation->set_rules('transaction_key', $this->lang->line('transaction_key'), 'trim');

        if ($this->form_validation->run() == true) {
            $data = [
	            'active'          => absint( $this->input->post( 'active' ) ),
	            'mode'            => $this->input->post( 'mode' ),
	            'api_login_id'    => $this->input->post( 'api_login_id' ),
	            'transaction_key' => $this->input->post( 'transaction_key' ),
            ];

            if ( $this->Erp_Options->updateOption( 'authorizedotnet_payment', $data ) ) {
	            $this->session->set_flashdata( 'message', $this->lang->line( 'authorizedotnet_setting_updated' ) );
            } else {
	            $this->session->set_flashdata( 'warning', $this->lang->line( 'setting_not_updated' ) );
            }
            admin_redirect('system_settings/authorizedotnet');
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		    $this->data['authorizedotnet'] = (object) ci_parse_args(
		        $this->Erp_Options->getOption( 'authorizedotnet_payment', [] ),
			    [
				    'active'          => 1,
				    'mode'            => 1,
				    'api_login_id'    => '',
				    'transaction_key' => '',
			    ]
		    );

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('authorizedotnet_settings')]];
            $meta = ['page_title' => lang('bank_settings'), 'bc' => $bc];
            $this->page_construct('settings/authorizedotnet', $meta, $this->data);
        }
    }
	
    public function permissions( $id = null )
    {
        $this->form_validation->set_rules('group', lang('group'), 'is_natural_no_zero');
        if ($this->form_validation->run() == true) {
            $data = [
                'products-index'             => $this->input->post('products-index'),
                'products-edit'              => $this->input->post('products-edit'),
                'products-add'               => $this->input->post('products-add'),
                'products-delete'            => $this->input->post('products-delete'),
                'products-cost'              => $this->input->post('products-cost'),
                'products-price'             => $this->input->post('products-price'),
                'customers-index'            => $this->input->post('customers-index'),
                'customers-edit'             => $this->input->post('customers-edit'),
                'customers-add'              => $this->input->post('customers-add'),
                'customers-delete'           => $this->input->post('customers-delete'),
                'suppliers-index'            => $this->input->post('suppliers-index'),
                'suppliers-edit'             => $this->input->post('suppliers-edit'),
                'suppliers-add'              => $this->input->post('suppliers-add'),
                'suppliers-delete'           => $this->input->post('suppliers-delete'),
                'sales-index'                => $this->input->post('sales-index'),
                'sales-edit'                 => $this->input->post('sales-edit'),
                'sales-add'                  => $this->input->post('sales-add'),
                'sales-delete'               => $this->input->post('sales-delete'),
                'sales-email'                => $this->input->post('sales-email'),
                'sales-pdf'                  => $this->input->post('sales-pdf'),
                'sales-deliveries'           => $this->input->post('sales-deliveries'),
                'sales-edit_delivery'        => $this->input->post('sales-edit_delivery'),
                'sales-add_delivery'         => $this->input->post('sales-add_delivery'),
                'sales-delete_delivery'      => $this->input->post('sales-delete_delivery'),
                'sales-email_delivery'       => $this->input->post('sales-email_delivery'),
                'sales-pdf_delivery'         => $this->input->post('sales-pdf_delivery'),
                'sales-gift_cards'           => $this->input->post('sales-gift_cards'),
                'sales-edit_gift_card'       => $this->input->post('sales-edit_gift_card'),
                'sales-add_gift_card'        => $this->input->post('sales-add_gift_card'),
                'sales-delete_gift_card'     => $this->input->post('sales-delete_gift_card'),
                'quotes-index'               => $this->input->post('quotes-index'),
                'quotes-edit'                => $this->input->post('quotes-edit'),
                'quotes-add'                 => $this->input->post('quotes-add'),
                'quotes-delete'              => $this->input->post('quotes-delete'),
                'quotes-email'               => $this->input->post('quotes-email'),
                'quotes-pdf'                 => $this->input->post('quotes-pdf'),
                'purchases-index'            => $this->input->post('purchases-index'),
                'purchases-edit'             => $this->input->post('purchases-edit'),
                'purchases-add'              => $this->input->post('purchases-add'),
                'purchases-delete'           => $this->input->post('purchases-delete'),
                'purchases-email'            => $this->input->post('purchases-email'),
                'purchases-pdf'              => $this->input->post('purchases-pdf'),
                'transfers-index'            => $this->input->post('transfers-index'),
                'transfers-edit'             => $this->input->post('transfers-edit'),
                'transfers-add'              => $this->input->post('transfers-add'),
                'transfers-delete'           => $this->input->post('transfers-delete'),
                'transfers-email'            => $this->input->post('transfers-email'),
                'transfers-pdf'              => $this->input->post('transfers-pdf'),
                'sales-return_sales'         => $this->input->post('sales-return_sales'),
                'reports-quantity_alerts'    => $this->input->post('reports-quantity_alerts'),
                'reports-expiry_alerts'      => $this->input->post('reports-expiry_alerts'),
                'reports-products'           => $this->input->post('reports-products'),
                'reports-daily_sales'        => $this->input->post('reports-daily_sales'),
                'reports-monthly_sales'      => $this->input->post('reports-monthly_sales'),
                'reports-payments'           => $this->input->post('reports-payments'),
                'reports-sales'              => $this->input->post('reports-sales'),
                'reports-purchases'          => $this->input->post('reports-purchases'),
                'reports-customers'          => $this->input->post('reports-customers'),
                'reports-suppliers'          => $this->input->post('reports-suppliers'),
                'sales-payments'             => $this->input->post('sales-payments'),
                'purchases-payments'         => $this->input->post('purchases-payments'),
                'purchases-expenses'         => $this->input->post('purchases-expenses'),
                'products-adjustments'       => $this->input->post('products-adjustments'),
                'bulk_actions'               => $this->input->post('bulk_actions'),
                'customers-deposits'         => $this->input->post('customers-deposits'),
                'customers-delete_deposit'   => $this->input->post('customers-delete_deposit'),
                'products-barcode'           => $this->input->post('products-barcode'),
                'purchases-return_purchases' => $this->input->post('purchases-return_purchases'),
                'reports-expenses'           => $this->input->post('reports-expenses'),
                'reports-daily_purchases'    => $this->input->post('reports-daily_purchases'),
                'reports-monthly_purchases'  => $this->input->post('reports-monthly_purchases'),
                'products-stock_count'       => $this->input->post('products-stock_count'),
                'edit_price'                 => $this->input->post('edit_price'),
                'returns-index'              => $this->input->post('returns-index'),
                'returns-edit'               => $this->input->post('returns-edit'),
                'returns-add'                => $this->input->post('returns-add'),
                'returns-delete'             => $this->input->post('returns-delete'),
                'returns-email'              => $this->input->post('returns-email'),
                'returns-pdf'                => $this->input->post('returns-pdf'),
                'reports-tax'                => $this->input->post('reports-tax'),
                'wallet-list'                => $this->input->post('wallet-list'),
                'wallet-withdrawal_list'     => $this->input->post('wallet-withdrawal_list'),
                'wallet-withdrawal_add'      => $this->input->post('wallet-withdrawal_add'),
                'wallet-withdrawal_accept'   => $this->input->post('wallet-withdrawal_accept'),
            ];

            if (POS) {
                $data['pos-index'] = $this->input->post('pos-index');
            }

            //$this->rerp->print_arrays($data);
        }

        if ($this->form_validation->run() == true && $this->settings_model->updatePermissions($id, $data)) {
            $this->session->set_flashdata('message', lang('group_permissions_updated'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['id']    = $id;
            $this->data['p']     = $this->settings_model->getGroupPermissions($id);
            $this->data['group'] = $this->settings_model->getGroupByID($id);

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('group_permissions')]];
            $meta = ['page_title' => lang('group_permissions'), 'bc' => $bc];
            $this->page_construct('settings/permissions', $meta, $this->data);
        }
    }
	
    public function price_groups()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('price_groups')]];
        $meta = ['page_title' => lang('price_groups'), 'bc' => $bc];
        $this->page_construct('settings/price_groups', $meta, $this->data);
    }
	
    public function product_group_price_actions( $group_id )
    {
        if (!$group_id) {
            $this->session->set_flashdata('error', lang('no_price_group_selected'));
            admin_redirect('system_settings/price_groups');
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'update_price') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->setProductPriceForPriceGroup($id, $group_id, $this->input->post('price' . $id));
                    }
                    $this->session->set_flashdata('message', lang('products_group_price_updated'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteProductGroupPrice($id, $group_id);
                    }
                    $this->session->set_flashdata('message', lang('products_group_price_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('tax_rates'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('product_code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('product_name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('price'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('group_name'));
                    $row   = 2;
                    $group = $this->settings_model->getPriceGroupByID($group_id);
                    foreach ($_POST['val'] as $id) {
                        $pgp = $this->settings_model->getProductGroupPriceByPID($id, $group_id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $pgp->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $pgp->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pgp->price);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $group->name);
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical('center');
                    $filename = 'price_groups_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_price_group_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
	
    public function restore_backup( $zipfile )
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $file = './files/backups/' . $zipfile . '.zip';
        $this->rerp->unzip($file, './');
        $this->session->set_flashdata('success', lang('files_restored'));
        admin_redirect('system_settings/backups');
        exit();
    }
	
    public function restore_database( $dbfile )
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $file = file_get_contents('./files/backups/' . $dbfile . '.txt');
        // $this->db->conn_id->multi_query($file);
        mysqli_multi_query($this->db->conn_id, $file);
        $this->db->conn_id->close();
        admin_redirect('logout/db');
    }
	
    public function skrill()
    {
        $this->form_validation->set_rules('active', $this->lang->line('activate'), 'trim');
        $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'trim|valid_email');
        if ($this->input->post('active')) {
            $this->form_validation->set_rules('account_email', $this->lang->line('paypal_account_email'), 'required');
        }
        $this->form_validation->set_rules('secret_word', $this->lang->line('secret_word'), 'trim');
        $this->form_validation->set_rules('fixed_charges', $this->lang->line('fixed_charges'), 'trim');
        $this->form_validation->set_rules('extra_charges_my', $this->lang->line('extra_charges_my'), 'trim');
        $this->form_validation->set_rules('extra_charges_other', $this->lang->line('extra_charges_others'), 'trim');

        if ($this->form_validation->run() == true) {
            $data = ['active'         => $this->input->post('active'),
                'secret_word'         => $this->input->post('secret_word'),
                'account_email'       => $this->input->post('account_email'),
                'fixed_charges'       => $this->input->post('fixed_charges'),
                'extra_charges_my'    => $this->input->post('extra_charges_my'),
                'extra_charges_other' => $this->input->post('extra_charges_other'),
            ];
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateSkrill($data)) {
            $this->session->set_flashdata('message', $this->lang->line('skrill_setting_updated'));
            admin_redirect('system_settings/skrill');
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['skrill'] = $this->settings_model->getSkrillSettings();

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('skrill_settings')]];
            $meta = ['page_title' => lang('skrill_settings'), 'bc' => $bc];
            $this->page_construct('settings/skrill', $meta, $this->data);
        }
    }
	
    public function tax_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteTaxRate($id);
                    }
                    $this->session->set_flashdata('message', lang('tax_rates_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('tax_rates'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('tax_rate'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('type'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $tax = $this->settings_model->getTaxRateByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $tax->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $tax->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $tax->rate);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, ($tax->type == 1) ? lang('percentage') : lang('fixed'));
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical('center');
                    $filename = 'tax_rates_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
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
	
    public function tax_rates()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('tax_rates')]];
        $meta = ['page_title' => lang('tax_rates'), 'bc' => $bc];
        $this->page_construct('settings/tax_rates', $meta, $this->data);
    }
	
    public function unit_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteUnit($id);
                    }
                    $this->session->set_flashdata('message', lang('units_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('categories'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('base_unit'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('operator'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('operation_value'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $unit = $this->site->getUnitByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $unit->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $unit->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $unit->base_unit);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $unit->operator);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $unit->operation_value);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical('center');
                    $filename = 'units_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
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
	
    public function units() {
	    $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata( 'error' );
	    $bc = [
		    [ 'link' => base_url(), 'page' => lang( 'home' ) ],
		    [ 'link' => admin_url( 'system_settings' ), 'page' => lang( 'system_settings' ) ],
		    [ 'link' => '#', 'page' => lang( 'units' ) ],
	    ];
	    $meta = [ 'page_title' => lang( 'units' ), 'bc' => $bc ];
	    $this->page_construct( 'settings/units', $meta, $this->data );
    }
	
    public function update_prices_csv( $group_id = null )
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('message', lang('disabled_in_demo'));
                admin_redirect('welcome');
            }

            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = true;
                $config['encrypt_name']  = true;
                $config['max_filename']  = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('system_settings/group_product_prices/' . $group_id);
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = ['code', 'price'];

                $final = [];

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {
                    if ($product = $this->site->getProductByCode(trim($csv_pr['code']))) {
                        $data[] = [
                            'product_id'     => $product->id,
                            'price'          => $csv_pr['price'],
                            'price_group_id' => $group_id,
                        ];
                    } else {
                        $this->session->set_flashdata('message', lang('check_product_code') . ' (' . $csv_pr['code'] . '). ' . lang('code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        admin_redirect('system_settings/group_product_prices/' . $group_id);
                    }
                    $rw++;
                }
            }
        } elseif ($this->input->post('update_price')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('system_settings/group_product_prices/' . $group_id);
        }

        if ($this->form_validation->run() == true && !empty($data)) {
            $this->settings_model->updateGroupPrices($data);
            $this->session->set_flashdata('message', lang('price_updated'));
            admin_redirect('system_settings/group_product_prices/' . $group_id);
        } else {
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['group']    = $this->site->getPriceGroupByID($group_id);
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'settings/update_price', $this->data);
        }
    }
	
    public function update_product_group_price( $group_id = null )
    {
        if (!$group_id) {
            $this->rerp->send_json(['status' => 0]);
        }

        $product_id = $this->input->post('product_id', true);
        $price      = $this->input->post('price', true);
        if (!empty($product_id) && !empty($price)) {
            if ($this->settings_model->setProductPriceForPriceGroup($product_id, $group_id, $price)) {
                $this->rerp->send_json(['status' => 1]);
            }
        }

        $this->rerp->send_json(['status' => 0]);
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
            $this->db->update('settings', ['purchase_code' => $this->input->post('purchase_code', true), 'envato_username' => $this->input->post('envato_username', true)], ['setting_id' => 1]);
            admin_redirect('system_settings/updates');
        } else {
            $fields = ['version' => $this->Settings->version, 'code' => $this->Settings->purchase_code, 'username' => $this->Settings->envato_username, 'site' => base_url()];
            $this->load->helper('update');
            $protocol              = is_https() ? 'https://' : 'http://';
            $updates               = get_remote_contents($protocol . 'api.retailpremier.com/v1/update/', $fields);
            $this->data['updates'] = json_decode($updates);
            $bc                    = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('updates')]];
            $meta                  = ['page_title' => lang('updates'), 'bc' => $bc];
            $this->page_construct('settings/updates', $meta, $this->data);
        }
    }
	
    public function user_groups()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('auth');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $this->data['groups'] = $this->settings_model->getGroups();
        $bc                   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('groups')]];
        $meta                 = ['page_title' => lang('groups'), 'bc' => $bc];
        $this->page_construct('settings/user_groups', $meta, $this->data);
    }
	
    public function variants()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('variants')]];
        $meta = ['page_title' => lang('variants'), 'bc' => $bc];
        $this->page_construct('settings/variants', $meta, $this->data);
    }
	
    public function warehouse_actions()
    {
        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->settings_model->deleteWarehouse($id);
                    }
                    $this->session->set_flashdata('message', lang('warehouses_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('warehouses'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('address'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('city'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $wh = $this->settings_model->getWarehouseByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $wh->code);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $wh->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $wh->address);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $wh->city);
                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical('center');
                    $filename = 'warehouses_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang('no_warehouse_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
	
    public function warehouses()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('system_settings'), 'page' => lang('system_settings')], ['link' => '#', 'page' => lang('warehouses')]];
        $meta = ['page_title' => lang('warehouses'), 'bc' => $bc];
        $this->page_construct('settings/warehouses', $meta, $this->data);
    }
	
    public function write_index( $timezone )
    {
        $template_path = FCPATH . 'assets/config_dumps/index.php';
        $output_path   = FCPATH . 'index.php';
        $index_file    = file_get_contents($template_path);
        $new           = str_replace('%TIMEZONE%', $timezone, $index_file);
        $handle        = fopen($output_path, 'w+');
        @chmod($output_path, 0777);

        if (is_writable($output_path)) {
            if (fwrite($handle, $new)) {
                @chmod($output_path, 0644);
                return true;
            }
            @chmod($output_path, 0644);
            return false;
        }
        @chmod($output_path, 0644);
        return false;
    }
}
