<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Trait MY_Controller_Trait
 * @property MY_Loader $load
 * @property Site $site
 * @property Rerp $rerp
 * @property CI_DB_mysql_driver|CI_DB_mysqli_driver $db
 * @property CI_Input $input
 * @property CI_Config $config
 * @property MY_Lang $lang
 *
 * @property bool|object $Settings
 *
 * @property string $theme
 * @property array $themeSettings
 *
 * @property bool $loggedIn
 * @property Erp_Menus $Erp_Menus
 * @property bool|object $default_currency
 * @property bool|object $selected_currency
 * @property CI_Form_validation $form_validation
 * @property CI_Parser $parser
 * @property Shop_model $shop_model
 * @property Settings_model $settings_model
 * @property CI_Session $session
 * @property CI_Router $router
 * @property Tec_cart $cart
 * @property Sms $sms
 *
 * @property bool $Owner
 * @property bool $Customer
 * @property bool $Supplier
 * @property bool $Admin
 * @property bool|null $Staff
 * @property object $loggedInUser
 * @property bool|object $shop_settings
 * @property object $customer
 * @property object $customer_group
 * @property object $warehouse
 * @property array $dateFormats
 * @property array $data
 *
 * @property string $m Current Class Being loaded by the router.
 * @property string $v Current Method (of $m class) Being loaded by the router.
 *
 * @property string $shopThemeName
 * @property string $shopTheme
 * @property string $shopThemeDir
 * @property string $shopAssets
 * @property string $shopThemeURL
 * @property string $shopAssetsURL
 * @property array  $themeInfos
 *
 * @property string $adminThemeName
 * @property string $adminTheme
 * @property string $adminThemeDir
 * @property string $adminAssets
 * @property string $adminThemeURL
 * @property string $adminAssetsURL
 * @property CI_URI $uri
 * @property bool $doingAjax
 * @property bool $doingREST
 * @property CI_Output $output
 * @property Erp_Options $Erp_Options
 * @property CI_Upload $upload
 * @property Gst $gst
 * @property object $_payment_methods
 * @property object $payment_methods
 * @property array $commission_settings
 * @property CI_Migration $migration
 * @property Datatables $datatables
 * @property array|false $GP
 */
trait MY_Controller_Trait {
	
	/**
	 * @var array
	 */
	protected $_payment_methods;
	
	/**
	 * @var array
	 */
	protected $payment_methods = [];
	
	public $digital_upload_path = 'files/';
	public $upload_path = 'assets/uploads/';
	public $thumbs_path = 'assets/uploads/thumbs/';
	public $image_types = 'gif|jpg|jpeg|png|tif';
	public $digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
	public $allowed_file_size = '1024';
	public $popup_attributes = [
		'width'       => '900',
		'height'      => '600',
		'window_name' => 'rerp_popup',
		'menubar'     => 'yes',
		'scrollbars'  => 'yes',
		'status'      => 'no',
		'resizable'   => 'yes',
		'screenx'     => '0',
		'screeny'     => '0',
	];
	
	/**
	 * Setup the theme path.
	 */
	public function setThemeDir() {
	
//		if ( is_dir( VIEWPATH . $this->Settings->theme . DIRECTORY_SEPARATOR . 'shop' . DIRECTORY_SEPARATOR . 'assets' ) ) {
//			$this->data['assets'] = base_url() . 'themes/' . $this->Settings->theme . '/shop/assets/';
//		} else {
//			$this->data['assets'] = base_url() . 'themes/default/shop/assets/';
//		}
//		if (is_dir(VIEWPATH . $this->Settings->theme . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR)) {
//			$this->data['assets'] = base_url() . 'themes/' . $this->Settings->theme . '/admin/assets/';
//		} else {
//			$this->data['assets'] = base_url() . 'themes/default/admin/assets/';
//		}
		
		if ( file_exists( VIEWPATH . $this->Settings->theme . DIRECTORY_SEPARATOR . 'shop' . DIRECTORY_SEPARATOR ) ) {
			$this->shopThemeName = $this->Settings->theme;
			$this->shopTheme     = $this->Settings->theme . '/shop/views/';
			$this->shopAssets    = $this->Settings->theme . '/shop/assets/';
		} else {
			$this->shopThemeName = 'default';
			$this->shopTheme     = 'default/shop/views/';
			$this->shopAssets    = 'default/shop/assets/';
		}
		
		if ( file_exists( VIEWPATH . $this->Settings->theme . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR ) ) {
			$this->adminThemeName = $this->Settings->theme;
			$this->adminTheme     = $this->Settings->theme . '/admin/views/';
			$this->adminAssets    = $this->Settings->theme . '/admin/assets/';
		} else {
			$this->adminThemeName = 'default';
			$this->adminTheme     = 'default/admin/views/';
			$this->adminAssets    = 'default/admin/assets/';
		}
		
		$this->shopThemeDir  = realpath( VIEWPATH . $this->shopThemeName );
		$this->adminThemeDir = realpath( VIEWPATH . $this->adminThemeName );
		$infoFile = $this->shopThemeDir . '/theme.php';
		/** @noinspection PhpIncludeInspection */
		$this->themeInfos = file_exists( $infoFile ) && is_readable( $infoFile ) ? include $infoFile : [];
		$this->themeInfos['sub_nav'] = isset( $this->themeInfos['sub_nav'] ) ? (bool) $this->themeInfos['sub_nav'] : false;
		
		$this->shopThemeURL   = base_url( 'themes/' . $this->shopThemeName . '/shop' );
		$this->adminThemeURL  = base_url( 'themes/' . $this->adminThemeName . '/admin' );
		$this->shopAssetsURL  = base_url( 'themes/' . $this->shopAssets );
		$this->adminAssetsURL = base_url( 'themes/' . $this->adminAssets );
		
		// update the setting variable.
		$this->Settings->shopThemeName  = $this->shopThemeName;
		$this->Settings->adminThemeName = $this->adminThemeName;
		$this->Settings->shopTheme      = $this->shopTheme;
		$this->Settings->shopAssetsURL  = $this->shopAssetsURL;
		$this->Settings->adminTheme     = $this->adminTheme;
		$this->Settings->adminAssetsURL = $this->adminAssetsURL;
		$this->Settings->noImage        = $this->shopAssetsURL . 'no_image.jpg';
	}
	
	public function getCurrentThemeName( $shop = true ) {
		if ( $shop ) {
			return $this->shopThemeName;
		}
		return $this->adminThemeName;
	}
	
	/**
	 * Get theme file/directory path.
	 *
	 * @param string $path     path to get.
	 * @param bool   $raw_path concat 'themes/' before. should be false if using with
	 *                         $this->load->view() method.
	 * @param bool   $shop     shop or admin.
	 *
	 * @return string Path without trailing slash.
	 */
	public function getCurrentThemeViews( $path = '', $raw_path = false, $shop = true ) {
		$_path = $raw_path ? 'themes/' : '';
		$path = ltrim( $path, '/\\' );
		$path = rtrim( $path, '/\\' );
		if ( $shop ) {
			return $_path . $this->shopTheme . $path;
		}
		return $_path . $this->adminTheme . $path;
	}
	
	public function getCurrentThemeAssets( $shop = true, $url = true ) {
		if ( $shop ) {
			if ( $url ) {
				return $this->shopAssetsURL;
			}
			return $this->shopAssets;
		}
		if ( $url ) {
			return $this->adminAssetsURL;
		}
		return $this->adminAssets;
	}
	
	public function setConstants() {
		if (file_exists(APPPATH . 'controllers' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'Pos.php')) {
			define('POS', 1);
		} else {
			define('POS', 0);
		}
		if (file_exists(APPPATH . 'controllers' . DIRECTORY_SEPARATOR . 'shop' . DIRECTORY_SEPARATOR . 'Shop.php')) {
			define('SHOP', 1);
		} else {
			define('SHOP', 0);
		}
	}
	
	public function setupCommonController( $setMenu = false, $admin = false ) {
		$this->setConstants();
		// Doing Ajax?
		$this->doingAjax = $this->input->is_ajax_request();
		// set sql mode.
        $this->db->query('SET SESSION sql_mode = ""');
		// load common models.
		$this->load->model( [
			'Erp_Coupon',
			'Erp_Options',
			'Erp_Shipping_Zone',
			'Erp_Shipping_Method',
			'Erp_Shipping_Zone_Area',
			'Erp_Shipping_Zone_Area_Slot',
			'Erp_Pickup_Area_Slot',
			'Erp_Delivery_Schedule',
			'Erp_Product',
			'Erp_Product_Category',
			'Erp_Product_Brand',
			'Erp_Menus',
			'Erp_Company',
			'Erp_User',
			'Erp_Address',
			'Erp_Gateway_sslcommerz',
			'Erp_Invoice_Item',
			'Erp_Payment',
			'Erp_Invoice',
			'Erp_Delivery',
			'Erp_Commission_Group',
			'Erp_Commission_User',
			'Erp_Referral_Commission',
			'Erp_Shopper_Commission',
			'Erp_Referral',
			'Erp_Wallet',
			'Erp_Wallet_Withdraw',
			'Erp_Transaction',
			'Erp_Pickup_Area',
			'Erp_Delivery_Area',
			'Erp_Pickup',
		] );
        // load shop model.
		$this->load->shop_model( 'shop_model' );
		// get common site settings.
		$this->Settings         = $this->site->get_setting();
		$this->shop_settings    = $this->shop_model->getShopSettings();
		$this->data['Settings'] = $this->Settings;
		$this->loggedIn         = $this->rerp->logged_in();
		$this->loggedInUser     = $this->site->getUser();
		
		// @TODO add a setup function that will type cast & validate settings.
		$this->shop_settings->minimum_order = absfloat( $this->shop_settings->minimum_order );

		// Commission Settings.
		$this->commission_settings         = $this->get_commission_settings();
		$this->data['commission_settings'] = $this->commission_settings;
		
		// setup directory path and urls.
		$this->setThemeDir();
		// theme options
		$settingsKeys = array_keys( $this->themeInfos['settings'] );
		$this->themeSettings = [];
		foreach ( $settingsKeys as $key ) {
			$this->themeSettings[ $key ] = $this->Erp_Options->getOption( $this->shopThemeName . '_' . $key, [] );
		}
		
		if ( ! $admin ) {
			$this->load->library('Tec_cart', '', 'cart');
			$this->theme = $this->shopTheme;
			$this->data['assets'] = $this->shopAssetsURL;
			
			$this->customer = $this->warehouse = $this->customer_group = false;
			if ( $this->session->userdata( 'company_id' ) ) {
				$this->customer = $this->site->getCompanyByID( $this->session->userdata( 'company_id' ) );
				if ( isset( $this->customer->customer_group_id ) && $this->customer->customer_group_id ) {
					$this->customer_group = $this->shop_model->getCustomerGroup( $this->customer->customer_group_id );
				}
			} elseif ( $this->shop_settings->warehouse ) {
				$this->warehouse = $this->site->getWarehouseByID( $this->shop_settings->warehouse );
			}
		} else {
			$this->theme = $this->adminTheme;
			$this->data['assets'] = $this->adminAssetsURL;
		}
		
		if ( $selected_currency = get_cookie( 'shop_currency', true ) ) {
			$this->Settings->selected_currency = $selected_currency;
		} else {
			$this->Settings->selected_currency = $this->Settings->default_currency;
		}
		
		$this->default_currency          = $this->shop_model->getCurrencyByCode( $this->Settings->default_currency );
		$this->data['default_currency']  = $this->default_currency;
		$this->selected_currency         = $this->shop_model->getCurrencyByCode( $this->Settings->selected_currency );
		$this->data['selected_currency'] = $this->selected_currency;
		
		$this->data['loggedIn']     = $this->loggedIn;
		$this->data['loggedInUser'] = $this->loggedInUser;
		$this->data['user']         = $this->loggedInUser;
		$this->Staff                = null;
		$this->data['Staff']        = $this->Staff;
		
		if ( $this->loggedIn ) {
			$this->Owner            = $this->rerp->in_group( 'owner' ) ? true : null;
			$this->data['Owner']    = $this->Owner;
			$this->Customer         = $this->rerp->in_group( 'customer' ) ? true : null;
			$this->data['Customer'] = $this->Customer;
			$this->Supplier         = $this->rerp->in_group( 'supplier' ) ? true : null;
			$this->data['Supplier'] = $this->Supplier;
			$this->Staff            = ( ! $this->rerp->in_group( 'customer' ) && ! $this->rerp->in_group( 'supplier' ) ) ? true : null;
			$this->data['Staff']    = $this->Staff;
			$this->Admin            = $this->rerp->in_group( 'admin' ) ? true : null;
			$this->data['Admin']    = $this->Admin;

			if ( ! $this->Owner && ! $this->Admin ) {
				$gp               = $this->site->checkPermissions();
				$this->GP         = ! empty( $gp ) ? $gp[0] : false;
				$this->data['GP'] = ! empty( $gp ) ? $gp[0] : false;
			} else {
				$this->data['GP'] = null;
			}
		} else {
			$this->config->load('hybridauthlib');
		}
		
		if ( $rtl_support = $this->input->cookie( 'rerp_rtl_support', true ) ) {
			$this->Settings->user_rtl = $rtl_support;
		} else {
			$this->Settings->user_rtl = $this->Settings->rtl;
		}
		
		// Set Current User
		// set language.
		$languageCookie = $admin ? 'rerp_language' : 'shop_language';
		$language = get_cookie( $languageCookie, true );
		if ( ! $language ) {
			$language = $this->Settings->language;
		}
		$this->Settings->user_language = $language;
		$this->config->set_item( 'language', $language );
		$this->lang->admin_load( 'rerp', $language );
		if ( ! $admin ) {
			$this->lang->shop_load( 'shop', $language );
		} else {
			$this->load->language('calendar');
		}

		// request
		$this->m                    = strtolower( $this->router->class );
		$this->v                    = strtolower( $this->router->method );
		$this->data['m']            = $this->m;
		$this->data['v']            = $this->v;
		$this->data['pageNow']      = $this->m . '/' . $this->v;
		
		if ( $sd = $this->site->getDateFormat( $this->Settings->dateformat ) ) {
			$dateFormats = [
				'js_sdate'    => $sd->js,
				'php_sdate'   => $sd->php,
				'mysq_sdate'  => $sd->sql,
				'js_ldate'    => $sd->js . ' hh:ii',
				'php_ldate'   => $sd->php . ' H:i',
				'mysql_ldate' => $sd->sql . ' %H:%i',
			];
		} else {
			$dateFormats = [
				'js_sdate'    => 'mm-dd-yyyy',
				'php_sdate'   => 'm-d-Y',
				'mysq_sdate'  => '%m-%d-%Y',
				'js_ldate'    => 'mm-dd-yyyy hh:ii:ss',
				'php_ldate'   => 'm-d-Y H:i:s',
				'mysql_ldate' => '%m-%d-%Y %T',
			];
		}
		
		$this->dateFormats         = $dateFormats;
		$this->data['dateFormats'] = $dateFormats;
		
		// indian gst.
		$this->Settings->indian_gst = false;
		if ( $this->Settings->invoice_view > 0 ) {
			$this->Settings->indian_gst = $this->Settings->invoice_view == 2 ? true : false;
			$this->Settings->format_gst = true;
			$this->load->library( 'gst' );
		}
		
		// shipping
		$this->data['shippingZone'] = $this->shop_model->getShippingZoneData();
		$this->data['hasZoneArea'] = $this->shop_model->getShippingZoneData();
		$this->data['hasShippingMethod'] = $this->shop_model->hasShippingMethod();
		$this->data['hasShippingMethod'] = $this->shop_model->hasShippingMethod();
		$this->data['shop_main_menus']   = false;
		// prepare the menu.
		if ( $setMenu ) {
			$menus = $this->Erp_Menus->getMenus( ( isset( $this->themeInfos['sub_nav'] ) ? $this->themeInfos['sub_nav'] : false ) );
			$req = array_keys( $_REQUEST );
			reset( $req );
			$req = isset( $req[0] ) ? rtrim( $req[0], '/\\') : '';
			$req = ltrim( $req, '/\\' );
			if ( empty( $menus ) ) {
				$menus = [
					new Erp_Menu_Item( [ 'id' => 1, 'label' => lang( 'home' ), 'slug' => '/' ] ),
					new Erp_Menu_Item( [ 'id' => 2, 'label' => lang( 'shop' ), 'slug' => 'shop/products', 'tip' => 'New', 'class' => 'new' ] ),
					new Erp_Menu_Item( [ 'id' => 3, 'label' => lang( 'cart' ), 'slug' => 'cart/' ] ),
					new Erp_Menu_Item( [ 'id' => 4, 'label' => lang( 'checkout' ), 'slug' => 'cart/checkout' ] ),
					new Erp_Menu_Item( [ 'id' => 5, 'label' => lang( 'contact_us' ), 'slug' => '#', 'class' => 'contact-us' ] ),
					new Erp_Menu_Item( [ 'id' => 5, 'label' => lang( 'about_us' ), 'slug' => '#' ] ),
				];
			}
			
			$this->data['shop_main_menus'] = $this->build_menu_list( $menus, $req );
		}
	}
	
	public function requireAdminLogin() {
		if ( ! $this->loggedIn ) {
			$this->session->set_userdata( 'requested_page', $this->uri->uri_string() );
			// don't use admin/login
			// real admin user will be redirect to dashboard after login.
			$this->rerp->md( 'login' );
		}
		
		if ( $this->Customer || $this->Supplier ) {
			$this->session->set_flashdata( 'warning', lang( 'access_denied' ) );
			redirect( '/' );
		}
		
	}
	
	public function onlyOwnerAllowed() {
		
		$this->requireAdminLogin();
		
		if ( ! $this->Owner ) {
			$this->session->set_flashdata( 'warning', lang( 'access_denied' ) );
			redirect( 'admin' );
		}
	}
	
	public function requireLogin( $admin = false, $message = '', $type = 'error', $use_js = true ) {
		if ( ! $this->loggedIn ) {
			if ( $message ) {
				if ( ! in_array( $type, [ 'error', 'warning', 'success', 'message', 'info' ] ) ) {
					$type = 'error';
				}
				$this->session->set_flashdata( $type, $message );
			}
			
			$this->session->set_userdata( 'requested_page', $this->uri->uri_string() );
			
			if ( $use_js ) {
				$this->rerp->md( $admin ? 'admin/login' : 'login' );
			} else {
				if ( $admin ) {
					admin_redirect( 'login' );
				} else {
					redirect( 'login' );
				}
			}
		}
	}
	
	/**
	 * This only output the option tag.
	 *
	 * @param Erp_Menu_Item[] $menus  Menu list
	 * @param string   $req         Req.
	 * @param int      $level       dept level. for tracking.
	 * @param int      $maxDept     Max Dept to display.
	 *
	 * @return string
	 */
	protected function build_menu_list( $menus = null, $req = '', $level = 1, $maxDept = 6 ) {
		$output = '';
		if ( null === $menus ) {
			$menus = $this->Erp_Menus->getMenus( false );
		}
		foreach( $menus as $menu ) {
			$slug = ltrim( $menu->slug, '/\\' );
			$slug = rtrim( $slug, '/\\' );
			$url = site_url( $slug );
			$classes = [ 'menu-item', 'menu-item-' . $menu->id, $menu->class ];
			if ( $req === $slug ) {
				$classes[] = 'active';
			}
			$topClass = [ 'menu-label', 'hidden-xs', ];
			if ( strpos( $menu->class, 'hot' ) !== false ) {
				$topClass[] = 'hot-menu';
			}
			if ( strpos( $menu->class, 'new' ) !== false ) {
				$topClass[] = 'new-menu';
			}
			$tip = empty( $menu->tip ) ? '' : sprintf( '<span class="%s">%s</span>', implode( ' ', $topClass ), $menu->tip );
			array_filter( $classes );
			$classes = array_unique( $classes );
			$subMenus = '';
			if ( $level <= $maxDept && $this->themeInfos['sub_nav'] ) {
				$subsItems = $this->Erp_Menus->getChildren( $menu->id );
				if ( $subsItems ) {
					$subMenus .= $this->build_menu_list( $subsItems, $req, $level + 1, $maxDept );
					if ( ! empty( $subMenus ) ) {
						$subMenus = '<ul>' . $subMenus . '</ul>';
					}
				}
			}
			/** @noinspection HtmlUnknownTarget */
			$output .= sprintf(
				'<li class="%1$s"><a href="%2$s">%3$s%4$s</a>%5$s</li>',
				implode( ' ', $classes ),
				$url,
				$menu->label,
				$tip,
				$subMenus
			);
		}
		return $output;
	}
	
	protected function setup_payment_methods() {
		$this->_payment_methods               = [];
		$this->_payment_methods['bank']       = $this->shop_model->getBankSettings();
		$this->_payment_methods['sslcommerz'] = $this->shop_model->getSslcommerzSettings();
		$this->_payment_methods['paypal']     = $this->shop_model->getPaypalSettings();
		$this->_payment_methods['authorize']  = $this->shop_model->getAuthorizeSettings();
		$this->_payment_methods['skrill']     = $this->shop_model->getSkrillSettings();
		$this->_payment_methods['stripe']     = $this->shop_settings->stripe;
		$this->_payment_methods['cod']        = (object) ci_parse_args(
			$this->Erp_Options->getOption( 'cash_on_delivery', [] ),
			[ 'active' => 1 ]
		);
	}
	
	protected function get_available_payment_methods() {
		if ( ! $this->payment_methods ) {
			$this->shop_settings->bank_details = '';
			$this->payment_methods             = [];
			if ( $this->_payment_methods['sslcommerz']->active ) {
				$this->payment_methods['sslcommerz'] = [
					'type' => 'sslcommerz',
					'name' => lang( 'sslcommerz' ),
				];
			}
			if ( $this->_payment_methods['paypal']->active ) {
				$this->payment_methods['paypal'] = [
					'type' => 'paypal',
					'name' => lang( 'paypal' ),
				];
			}
			if ( $this->_payment_methods['skrill']->active ) {
				$this->payment_methods['skrill'] = [
					'type' => 'skrill',
					'name' => lang( 'skrill' ),
				];
			}
			if ( $this->_payment_methods['stripe'] ) {
				$this->payment_methods['stripe'] = [
					'type' => 'stripe',
					'name' => lang( 'stripe' ),
				];
			}
			if ( ! empty( $this->_payment_methods['authorize']['api_login_id'] ) ) {
				$this->payment_methods['authorize'] = [
					'type' => 'authorize',
					'name' => lang( 'authorize' ),
				];
			}
			if ( $this->_payment_methods['bank']->active && ! empty( $this->_payment_methods['bank']->details ) ) {
				$this->shop_settings->bank_details = $this->_payment_methods['bank']->details;
				$this->payment_methods['bank'] = [
					'type' => 'bank',
					'name' => lang( 'bank_in' ),
				];
			}
			if ( $this->_payment_methods['cod']->active ) {
				$this->payment_methods['cod'] = [
					'type' => 'cod',
					'name' => lang( 'cod' ),
				];
			}
		}
		
		return $this->payment_methods;
	}
	
	/**
	 * @param object $product
	 * @param bool $getAll
	 * @return object
	 */
	protected function setup_product_data( &$product, $getAll = false ) {
		$isArray = is_array( $product );
		if ( $isArray ) {
			$product = (object) $product;
		}
		// main images.
		$img            = get_image_url( $product->image, false, true );
		$thumb          = get_image_url( $product->image, true, true );
		$product->image = $img ? $img : $this->Settings->noImage;
		$product->thumb = $thumb ? $thumb : $product->image;
		// links
		$product->link        = get_product_permalink( $product );
		$product->add_to_cart = get_add_to_cart_link( $product );
		//--->
		$photos = $this->shop_model->getProductPhotos( $product->id );
		$product->gallery = [];
		if ( $photos ) {
			foreach ( $photos as $photo ) {
				if ( $photo->photo ) {
					$image = get_image_url( $photo->photo, false, true );
					if ( $image ) {
						$thumb = get_image_url( $photo->photo, true, true );
						$product->gallery[] = [
							'photo' => $image,
							'thumb' => $thumb ? $thumb : $image,
						];
					}
				}
			}
		}
		// product description
		if ( ! isset( $product->details ) || isset( $product->details ) && empty( $product->details ) ) {
			if ( isset( $product->product_details ) ) {
				$product->details = $product->product_details;
			} else {
				$product->details = '';
			}
		}
		// custom data.
		$product->custom_data = [];
		if ( $getAll ) {
			if ( isset( $this->themeInfos['product_custom'], $this->themeInfos['product_custom']['fields'] ) ) {
				$fields = $this->themeInfos['product_custom']['fields'];
				foreach ( $fields as $field ) {
					if ( ! isset( $field['id'] ) ) {
						continue;
					}
					$id = $field['id'];
					$meta = $this->shop_model->getProductMeta( $product->id, $id, true );
					if ( 'video_url' === $id ) {
						if ( false !== strpos( $meta, 'youtu.be' ) ) {
							$parts = explode( '/', $meta );
							$vid   = end( $parts );
							$meta  = [
								'id'       => $vid,
								'src'      => sprintf( 'https://www.youtube.com/embed/%s?modestbranding=1&rel=0', $vid ),
								'provider' => '',
							];
						} else if ( false !== strpos( $meta, 'youtube.com' ) ) {
							$parts = explode( 'v=', $meta );
							$vid   = end( $parts );
							$meta  = [
								'id'       => $vid,
								'src'      => sprintf( 'https://www.youtube.com/embed/%s?modestbranding=1&rel=0', $vid ),
								'provider' => '',
							];
						} else {
							$meta  = [
								'id'       => '',
								'src'      => $meta,
								'provider' => '',
							];
						}
					}
					if ( 'short_description' === $id && ! empty( $meta ) ) {
						$product->meta_description = $meta;
					}
					$product->custom_data[ $id ] = $meta;
				}
			}
			// seo meta.
			if ( isset( $product->custom_data['seo_meta_title'] ) && $product->custom_data['seo_meta_title'] ) {
				$product->meta_title = $product->custom_data['seo_meta_title'];
			} else {
				$product->meta_title = $product->code . ' - ' . $product->name;
			}
			if ( isset( $product->custom_data['seo_meta_description'] ) && $product->custom_data['seo_meta_description'] ) {
				$product->meta_description = $product->custom_data['seo_meta_description'];
			} else {
				$product->meta_description = character_limiter( strip_tags( $product->details ), 160 );
			}
			$product->meta_image  = $img ? $img : base_url( 'assets/uploads/logos/' . $this->shop_settings->logo );
		}
		if ( ! isset( $product->custom_data['short_description'] ) ) {
			$product->custom_data['short_description'] = $this->shop_model->getProductMeta( $product->id, 'short_description', true );
		}
		// prices.
		$product->saved         = '';
		$product->sale_price    = '';
		$product->regular_price = '';
		$product->onSale = false;
		$product->promo = $this->rerp->isPromo( $product );
		/*******************************************************************************************
		// special price can replace regular price
		// promo price can replace regular price.
		// special price doesn't mean that product is on sale,
		// it's just take over the price (regular price).
		//
		// Legend:
		// sep : special price, rp : regular price, p : price,
		// sp : sale price, cgp : customer group percentage,
		// cp : current price.
		// Psudo.
		// if has spe
		//      then rp <<< spe
		//  else rp <<< p
		// fi
		// if customer logged in && has cg && cg percent <> ''
		//      then rp <<< rp+cgp
		// fi.
		// if promo && pp
		//      then sp <<< pp
		// fi
		// if sp <> ''
		//      then cp <<< sp
		// else cp <<< rp
		// fi.
		 *******************************************************************************************/
		if ( $this->shop_settings->hide_price ) {
			$product->price         = '';
			$product->promo_price   = '';
			$product->special_price = '';
		} else {
			//$this->money_format( $product->price )
			$product->price = $this->apply_groupPrice( $product->price );
			if ( isset( $product->special_price ) && ! empty( $product->special_price ) ) {
				$product->regular_price =
				$product->special_price = $this->apply_groupPrice( $product->special_price );
			} else {
				$product->regular_price = $product->price;
				$product->special_price = '';
			}
			if ( $product->promo ) {
				$product->sale_price = $product->promo_price;
				$product->onSale     = true;
				$product->saved      = $this->calculate_saved( $product->regular_price, $product->promo_price );
			}
		}
		
		$product->promo_starts  = false;
		$product->promo_ends    = false;
		$product->promo_ends_on = false;
		if ( $product->promo ) {
			//"start_date": null,
			//      "end_date": "2020-12-16",
			try {
				// promo will ends on the end of end_date which is the day after end_date or end_date+1 at 12:00:00:am
				// or end_date at 11:59:59 pm which is 23:59:59
				// $endsOn = strtotime( '+1day', strtotime( $product->end_date . ' 00:00:00' ) );
				$endsOn = strtotime( $product->end_date . ' 23:59:59' );
				$now    = new DateTime();
				$product->promo_ends = new DateTime( '@' . $endsOn );
				$product->promo_ends_on = $now->diff( $product->promo_ends );
				$product->promo_starts = $product->start_date ? $product->start_date . ' 00:00:00' : date( 'M d, Y H:i:s' );
				$product->promo_ends = date( 'M d, Y H:i:s', $endsOn );
			} catch( Exception $e ) {
				$product->xPromo = $e->getMessage();
			}
		}
		$product->cash_back = (bool) $product->cash_back;
		if ( $product->cash_back ) {
			$today = date( 'Y-m-d' );
			
			$cb_start = ( ! $product->cash_back_start_date ) ? $today : $product->cash_back_start_date;
			$cb_end   = ( ! $product->cash_back_end_date ) ? $today : $product->cash_back_end_date;
			$product->cash_back_amount = absfloat( $product->cash_back_amount );
			
			if ( ( $cb_start <= $today && $cb_end >= $today ) && $product->cash_back_amount > 0 ) {
				$product->cash_back_amount = $this->rerp->convertMoney( $product->cash_back_amount );
				$product->cash_back = true;
			} else {
				$product->cash_back = false;
			}
		}
		
		$product->current_price = $product->sale_price ? $product->sale_price : $product->regular_price;
		
		$warehouse             = $this->shop_model->getAllWarehouseWithPQ( $product->id );
		if( !$warehouse || !isset( $warehouse->quantity ) ){
			$warehouse = new stdClass();
			$warehouse->quantity = 0;
		}
		$product->variations   = $this->shop_model->getProductOptionsWithWH( $product->id );
		//$variants  = $this->shop_model->getProductOptions( $product->id );

		$product->stock_status = '';
		$product->stock = $warehouse->quantity;
		$product->stock_formatted = $this->rerp->formatQuantity( $warehouse->quantity );
		if ( $product->type != 'standard' || $warehouse->quantity > 0 ) {
			$product->stock_status = 'in_stock';
		} else {
			$product->stock_status = 'out_of_stock';
		}
		if ( 'out_of_stock' === $product->stock_status && $this->Settings->overselling ) {
			$product->stock_status = 'backorder';
		}
		
		$product->max_min_sale = [];
		$product->max_min_regular = [];
		$product->max_min_current = [];
		
		$product->isVariable = false;
		if ( ! $product->variations ) {
			$product->variations = [];
		} else {
			$product->attributes = [
				'dataMap'  => [],
				'optMap' => [],
				'opts'   => [],
			];
			foreach ( $product->variations as $variation ) {
				if ( false === strpos( $variation->name, ':' ) ) {
					$product->attributes['opts']['Options'][] = $variation->name;
				} else {
					$options = explode( '|', $variation->name );
					list( $fn, $fv ) = explode( ':', $options[0] );
					$j = 0;
					foreach ( $options as $option ) {
						list( $n, $v ) = explode( ':', $option );
						if ( ! isset( $product->attributes['opts'][$n] ) ) {
							$product->attributes['opts'][$n] = [];
						}
						if ( ! in_array( $v, $product->attributes['opts'][$n] ) ) {
							$product->attributes['opts'][$n][] = $v;
						}
						if ( $j > 0 ) {
							$product->attributes['optMap'][$fn][$fv][$n][]=$v;
						}
						$j++;
					}
				}
				$sale    = $product->sale_price ? ( $product->sale_price + $variation->price ) : '';
				$regular = $product->regular_price ? ( $product->regular_price + $variation->price ) : '';
				$current = $product->current_price ? ( $product->current_price + $variation->price ) : '';
				
				if ( $product->type != 'standard' || $variation->wh_qty > 0 ) {
					$stock_status = 'in_stock';
				} else {
					$stock_status = 'out_of_stock';
				}
				if ( 'out_of_stock' === $stock_status && $this->Settings->overselling ) {
					$stock_status = 'backorder';
				}
				
				$dataMap = [
					'id'             => $variation->id,
					//'price_addition' => $variation->price,
					'sale_price'     => $sale ? $this->money_format( $sale ) : '',
					'regular_price'  => $regular ? $this->money_format( $regular ) : '',
					'current_price'  => $current ? $this->money_format( $current ) : '',
					'quantity'       => $variation->wh_qty,
					'stock'          => $variation->wh_qty,
					'stock_status'   => $stock_status,
					'saved'          => $this->calculate_saved( $regular, $sale ),
				];
				
				$product->attributes['dataMap'][$variation->name] = $dataMap;
				$product->max_min_sale[] = $sale;
				$product->max_min_regular[] = $regular;
				$product->max_min_current[] = $current;
			}
			$product->isVariable = true;
			$product->max_min_sale = $this->getMinMax( $product->max_min_sale );
			$product->max_min_regular = $this->getMinMax( $product->max_min_regular );
			$product->max_min_current = $this->getMinMax( $product->max_min_current );
		}
		
		if ( $product->current_price ) {
			$product->current_price = $this->money_format( $product->current_price );
		}
		if ( $product->regular_price ) {
			$product->regular_price = $this->money_format( $product->regular_price );
		}
		if ( $product->sale_price ) {
			$product->sale_price = $this->money_format( $product->sale_price );
		}
		
		if ( $product->isVariable ) {
			// make sure color comes first.
			uksort( $product->attributes['opts'], function( $a ) {
				return 'color' !== strtolower( $a );
			} );
		}
		
		$product->inWishList = false;
		if ( $this->loggedInUser ) {
			$product->inWishList = ! ! $this->shop_model->getWishlistItem( $product->id, $this->loggedInUser->id );
		}
		$quickCart = in_array( $this->shopThemeName, [ 'grocerant', 'namibd'] );
		if ( $quickCart ) {
			$cart = $this->cart->getItems( $product->id );
			if ( $cart ) {
				$product->inCart  = true;
				$product->rowId   = $cart['rowId'];
				$product->cartQty = $cart['qty'];
			} else {
				$product->inCart  = false;
				$product->rowId   = false;
				$product->cartQty = 0;
			}
		}
		
		unset( $product->supplier1 );
		unset( $product->supplier1price );
		unset( $product->supplier2 );
		unset( $product->supplier2price );
		unset( $product->supplier3 );
		unset( $product->supplier3price );
		unset( $product->supplier4 );
		unset( $product->supplier4price );
		unset( $product->supplier5 );
		unset( $product->supplier5price );
		unset( $product->variations );
		unset( $product->subcategory_id );
		unset( $product->cf1 );
		unset( $product->cf2 );
		unset( $product->cf3 );
		unset( $product->cf4 );
		unset( $product->cf5 );
		unset( $product->cf6 );
		
		if ( $isArray ) {
			$product = (array) $product;
		}
		
		return $product;
	}
	
	protected function calculate_saved( $high, $low, $return_zero = false ) {
		$high  = floatval( $high );
		$low   = floatval( $low );
		$_diff = $high - $low;
		if ( $_diff != 0 ) { // be safe.. no zero... human error...
			$_diff = round( ( $_diff / $high ) * 100, 2 );
			return $_diff ? $_diff : ( $return_zero ? 0 : '' );
		} else {
			return $return_zero ? 0 : '';
		}
	}
	
	/**
	 * @param float[] $prices
	 *
	 * @return array
	 */
	protected function getMinMax( $prices ) {
		$prices = array_filter( $prices );
		$prices = array_unique( $prices );
		sort( $prices );
		$min = array_shift( $prices );
		$max = end( $prices );
		$out = [
			'min' => $this->money_format( $min ),
			'max' => false,
		];
		if ( $max ) {
			$out['max'] = $this->money_format( $max );
		}
		return $out;
	}
	
	/**
	 *
	 * @param int|float $number
	 *
	 * @return string
	 */
	protected function money_format( $number ) {
		return $this->rerp->convertMoney( $number, true, true );
	}
	
	/**
	 * @param int|float $number
	 *
	 * @return string
	 */
	protected function apply_groupPrice( $number ) {
		return $this->rerp->setCustomerGroupPrice( $number, $this->customer_group, false );
	}
	
	/**
	 * Get Thumb image with fallback to noImage
	 * @param string $image Image file name, uploaded to assets/uploads.
	 *
	 * @return bool|string
	 */
	protected function getThumb( $image ) {
		return get_image_url_thumb_first( $image, $this->Settings->noImage );
	}
	
	
	/**
	 * This only output the option tag.
	 *
	 * @param object[] $categories  category list
	 * @param int      $currentItem selected category id.
	 * @param int      $selected    selected category id.
	 * @param int      $level       dept level. for tracking.
	 * @param int      $maxDept     Max Dept to display.
	 *
	 * @return string
	 */
	protected function build_category_dropdown_options( $categories = null, $currentItem = null, $selected = 0, $level = 1, $maxDept = 6 ) {
	    $this->load->admin_model( 'settings_model' );
	    $output = '';
	    if ( null === $categories ) {
	        $categories = $this->settings_model->getParentCategories();
	    }
	    foreach( $categories as $category ) {
	        if ( $currentItem == $category->id ) {
	            continue;
	        }
	        $output .= sprintf(
	            '<option class="level-%s" value="%s"%s>%s</option>',
	            $level,
	            $category->id,
	            $selected == $category->id ? ' selected' : '',
	            str_repeat( '–', $level ) . '' . $category->name
            );
	        
	        if ( $level <= $maxDept ) {
	            $subs = $this->settings_model->getCategoryChildren( $category->id );
	            if ( $subs ) {
	                $output .= $this->build_category_dropdown_options( $subs, $currentItem, $selected, $level + 1, $maxDept );
	            }
	        }
	    }
	    return $output;
	}
	
	/**
	 * Create New Referral Data for user.
	 *
	 * @param int $user_id     Current User id that being registered.
	 * @param int $referral_id The Referral ID.
	 * @param string $note Note to add to the record.
	 *
	 * @return bool Success or failure.
	 */
	protected function add_referral( $user_id, $referral_id, $note = '' ) {
		$user_id = absint( $user_id );
		$referral_id = sanitize_formatted_id( $referral_id );
		if ( ! $user_id || ! $referral_id || $referral_id === $user_id ) {
			return false;
		}
		
		$this->load->model( 'Erp_Referral' );
		
		$referral = new Erp_Referral();
		$referral->setUserId( $user_id );
		$referral->setReferralId( $referral_id );
		if ( $note ) {
			$referral->setDescription( $note );
		}
		// by system.
		$referral->setCreatedBy( 0 );
		$referral->setModifiedBy( 0 );
		
		return $referral->save();
	}
	
	public function get_commission_settings() {
		
		$commission = $this->Erp_Options->getOption( '_commission', [] );
		return ci_parse_args( $commission,
			[
				'notification_email' => $this->Settings->default_email,
				'minimum_withdrawal' => 0,
				'cookie_lifetime'    => 0,
				'referral_url'       => site_url(),
				'affiliate_url'      => site_url(),
			]
		);
	}
	
	public function flash_response( $message, $page = '', $type = 'error', $method = 'auto', $code = NULL ) {
		$types = [ 'warning', 'error', 'message', 'remainder', 'success', 'info' ];
		if ( ! in_array( $type, $types ) ) {
			$type = 'info';
		}
		$this->session->set_flashdata( $type, lang( $message ) );
		$page = empty( $page ) ? ( isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : site_url() ) : $page;
		redirect( $page, $method, $code );
	}
}
