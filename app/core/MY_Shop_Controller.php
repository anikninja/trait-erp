<?php
defined('BASEPATH') or exit('No direct script access allowed');

if ( ! trait_exists( 'MY_Controller_Trait' ) ) {
	require 'MY_Controller_Trait.php';
}

/**
 * Class MY_Shop_Controller.
 *
 * @property string $theme
 */
class MY_Shop_Controller extends CI_Controller {
	
	use MY_Controller_Trait;
	
    public function __construct() {
        parent::__construct();
	
	    $this->setupCommonController( true, false );
	    
	    // Set referral id as set_cookie.
	    $refId = null;
	    
	    if ( $this->input->get( 'referral' ) !== null ) {
		    $refId = $this->input->get( 'referral' );
	    } elseif ( $this->input->get( 'ref' ) !== null ) {
		    $refId = $this->input->get( 'ref' );
	    }
	
	    if ( null !== $refId ) {
		    $expiration = (int) $this->commission_settings['cookie_lifetime'];
		    if ( ! $expiration ) {
			    $expiration = 30; // default is 1 month.
		    }
		    $expiration = ( 60 * 60 * 24 * $expiration );
		    // set ref id.
		    set_cookie( 'referral_id', $refId, $expiration );
	    }
    }
	
	/**
	 * View ...
	 *
	 * @param string $page
	 * @param array $data
	 */
    public function page_construct( $page, $data = [] ) {
	    if ( SHOP ) {
	    	
	    	if ( is_readable( $this->shopThemeDir . '/helper.php' ) ) {
			    /** @noinspection PhpIncludeInspection */
			    require_once $this->shopThemeDir . '/helper.php';
		    }
	    	
		    foreach ( $this->themeSettings as $key => $options ) {
		        $data[ $key ]  = $this->prepareDynamicOption( $options );
		    }
		    
		    $data['color_mappings'] = $this->Erp_Options->getOption( 'color_mappings', false );
		    $data['custom_css']    = $this->Erp_Options->getOption( 'custom_css' );;
		    $data['custom_js']     = $this->Erp_Options->getOption( 'custom_js' );;
	        $data['message']       = $data['message'] ?? $this->session->flashdata( 'message' );
	        $data['error']         = $data['error'] ?? $this->session->flashdata( 'error' );
	        $data['warning']       = $data['warning'] ?? $this->session->flashdata( 'warning' );
	        $data['reminder']      = $data['reminder'] ?? $this->session->flashdata( 'reminder' );
	        $data['view_product']  = $data['view_product'] ?? '';
            $data['Settings']      = $this->Settings;
            $data['shop_settings'] = $this->shop_settings;
            $data['currencies']    = $this->shop_model->getAllCurrencies();
            $data['pages']         = $this->shop_model->getAllPages();
            $data['brands']        = $this->shop_model->getAllBrands();
            $categories            = $this->shop_model->get_category_tree();
            
            $cats = [];
            foreach ($categories as $category) {
                $cat                = $category;
                $cat->products      = $this->shop_model->getProductsByCategory( $category->id );
	            array_map( [ $this, 'setup_product_data' ], $cat->products );
                $cats[]             = $cat;
            }
            
		    $data['new_products'] = $this->shop_model->getNewProducts();
		    array_map( [ $this, 'setup_product_data' ], $data['new_products'] );
		    
		    $data['most_viewed'] = $this->shop_model->getProducts( [ 'sorting' => 'views-desc', 'limit' => 16 ], 'object' );
	        array_map( [ $this, 'setup_product_data' ], $data['most_viewed'] );
		    
	        $data['promo_products'] = $this->shop_model->getPromoProducts();
	        array_map( [ $this, 'setup_product_data' ], $data['promo_products'] );
		    if ( 'grocerant' === $this->shopThemeName ) {
			    $data['promo_products'] = array_chunk( $data['promo_products'], 5 );
		    }
		
		    $data['categories']          = $cats;
		    $data['featured_categories'] = $this->shop_model->getFeaturedCategories();
            
            // special products.
		    // featured-product
		    if ( isset( $data['homeSettings']['special_products'] ) ) {
			    if ( ! is_array( $data['homeSettings']['special_products'] ) ) {
				    $data['homeSettings']['special_products'] = explode( ',', $data['homeSettings']['special_products'] );
			    }
			    $data['special_products'] = array_map( function ( $id ) {
				    if( is_numeric( $id ) ) {
					    $product = $this->shop_model->getProductByID($id);
					    if ( $product ) {
						    $this->setup_product_data($product);
						    return $product;
					    }
				    }
				    return null;
			    }, $data['homeSettings']['special_products'] );
			    $data['special_products'] = array_filter( $data['special_products'] );
			    $data['special_products'] = array_chunk( $data['special_products'], 3 );
		    } else {
			    $data['special_products'] = [];
		    }
			
            // Slider.
		    $data['home_slider'] = json_decode( $this->shop_settings->slider );
            
            // Cart data on all pages.
            $data['cart']       = $this->cart->cart_data(true);
		
		    if ( ! $this->loggedIn && $this->Settings->captcha ) {
			    $this->load->helper( 'captcha' );
			    $vals    = [
				    'img_path'    => './assets/captcha/',
				    'img_url'     => base_url( 'assets/captcha/' ),
				    'img_width'   => 210,
				    'img_height'  => 34,
				    'word_length' => 5,
				    'colors'      => [
					    'background' => [ 255, 255, 255 ],
					    'border'     => [ 204, 204, 204 ],
					    'text'       => [ 102, 102, 102 ],
					    'grid'       => [ 204, 204, 204 ],
				    ],
			    ];
			    $cap     = create_captcha( $vals );
			    $capdata = [
				    'captcha_time' => $cap['time'],
				    'ip_address'   => $this->input->ip_address(),
				    'word'         => $cap['word'],
			    ];
			    $query   = $this->db->insert_string( 'captcha', $capdata );
			    $this->db->query( $query );
			    $data['image']   = $cap['image'];
			    $data['captcha'] = [
				    'name'        => 'captcha',
				    'id'          => 'captcha',
				    'type'        => 'text',
				    'class'       => 'form-control',
				    'required'    => 'required',
				    'placeholder' => lang( 'type_captcha' ),
			    ];
            }

            $data['isPromo']       = $this->shop_model->isPromo();
            $data['isCashBack']    = $this->shop_model->isCashBack();
		    
		    if ( 'default' === $this->shopThemeName ) {
			    $data['side_featured'] = $this->shop_model->getFeaturedProducts(16, false);
			    $data['featured_products'] = $this->shop_model->getFeaturedProducts();
		    }
            $data['wishlist']      = $this->shop_model->getWishlist(true);
            $data['info']          = $this->shop_model->getNotifications();
            $data['ip_address']    = $this->input->ip_address();
		    $data['page_desc'] = isset( $data['page_desc'] ) && ! empty( $data['page_desc'] ) ? $data['page_desc'] : $this->shop_settings->description;
		
		    $data['home_page_title'] = $this->shop_settings->shop_name;
		    $data['home_page_desc']  = $this->shop_settings->description;
		    
		    $this->load->view( $this->theme . 'header', $data );
		    $this->load->view( $this->theme . $page, $data );
		    $this->load->view( $this->theme . 'footer' );
        }
    }
    
	protected function prepareDynamicOption( $theme_options ) {
		$prepared = [];
		if ( empty( $theme_options ) || ! is_array( $theme_options ) ) {
			return $prepared;
		}
		foreach ( $theme_options as $segment => $options ) {
			$firstKey = array_keys( $options );
			if ( isset( $options['widgets'] ) ) {
				$options['widgets'] = array_map( [ $this, 'prepare_sections' ], $options['widgets'] );
			} else if ( 'integer' === gettype( $firstKey[0] ) ) {
				$options = $this->prepare_sections( $options );
			} else {
				foreach ( $options as &$opts ) {
					if ( isset($opts['label']) ) {
						unset( $opts['label'] );
					}
					$_opts = $this->prepare_sections( [ 0 => $opts ] );
					if ( ! empty( $_opts ) ) {
						$opts = $_opts[0];
					}
				}
			}
			if ( 'copyright' === $segment || 'slider' === $segment ) {
				$options = $options[0];
			}
			$prepared[$segment] = $options;
		}
		
		return $prepared;
	}
	
	protected function prepare_sections( $sections ) {
		$output = [];
		if ( empty( $sections ) || ! is_array( $sections ) ) {
			return $output;
		}
		foreach ( $sections as $k => $section ) {
			if ( ! isset( $section['type'] ) ) {
				continue;
			}
			$type = $section['type'];
			
			$temp = [];
			if ( isset( $section['label']['visibility'], $section['label']['value'] ) && 'show' === $section['label']['visibility'] ) {
				$temp['label'] = strip_tags( $section['label']['value'] );
			}
			if ( isset( $section['subtitle'] ) ) {
				$temp['subtitle'] = strip_tags( $section['subtitle'] );
			}
			
			$max = isset( $section['max'] ) ? absint( $section['max'] ) : 16;
			// Get Data.
			if ( 'page' === $type && isset( $section['ids'] ) ) {
				$section['ids'] = explode( ',', $section['ids'] );
				if ( isset( $section['ids'][0] ) ) {
					$content = $this->shop_model->getPageById( absint( $section['ids'][0] ) );
					if ( $content && ! empty( $content->body ) ) {
						$temp['content'] = $content;
					}
				}
			}
			else if ( in_array( $type, [ 'text', 'custom', 'copyright' ] ) && isset( $section['content'] ) && ! empty( $section['content'] ) ) {
				$temp['content'] = $section['content'];
			}
			else if ( 'url_list' === $type ) {
				$temp['list'] = isset( $section['list'] )? $section['list'] : [];
			}
			else if ( 'categories' === $type  && isset( $section['ids'] ) ) {
				$ids = explode( ',', $section['ids'] );
				if ( isset( $ids[0] ) ) {
					$prods = $this->shop_model->getProductsByCategory( $ids[0], $max );
					if ( ! empty( $prods ) ) {
						$temp['category'] = $this->site->getCategoryByID( $ids[0] );
						if ( isset($section['show_sub']) && 'show' === $section['show_sub'] ) {
							$temp['sub_cat'] = $this->shop_model->getSubCategories( $ids[0] );
						}
						array_map( [ $this, 'setup_product_data' ], $prods );
						$temp['products'] = $prods;
					}
				}
			}
			else if ( 'slider' === $type || 'brand_slider' === $type ) {
				$temp['show'] = true;
			}
			else {
				// common product related..
				$products = [];
				if ( 'new_products' === $type ) {
					$products = $this->shop_model->getNewProducts( $max );
				}
				else if ( 'most_viewed' === $type ) {
					$products = $this->shop_model->getProducts( [ 'sorting' => 'views-desc', 'limit' => $max ], 'object' );
				}
				else if ( 'trending_products' === $type ) {
					$products= $this->shop_model->getTrendingProducts( $max );
				}
				else if ( 'daily_deals' === $type ) {
					$products = $this->shop_model->getPromoProducts( $max );
				}
				else if ( 'featured_products' === $type ) {
					$promo = isset( $section['promo'] ) && $section['promo'] == 'show' ? true : false;
					$products = $this->shop_model->getFeaturedProducts( $max, $promo );
				}
				else if ( 'products' === $type  && isset( $section['ids'] ) ) {
					$ids = explode( ',', $section['ids'] );
					$ids = array_map( 'absint', $ids );
					$ids = array_unique( array_filter( $ids ) );
					if ( empty( $ids ) ) {
						continue;
					}
					$products = $this->shop_model->getProductsByIds( $ids, $max );
				}
				if ( ! empty( $products ) ) {
					array_map( [ $this, 'setup_product_data' ], $products );
					$temp['products'] = $products;
				} else {
					$temp['products'] = [];
				}
			}
			
			if ( ! empty( $temp ) ) {
				$temp['type'] = $type;
				$output[] = $temp;
			}
		}
		return $output;
	}
}
