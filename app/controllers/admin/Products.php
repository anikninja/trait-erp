<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Products
 * @property CI_Image_lib $image_lib
 * @property Products_model $products_model
 */
class Products extends MY_Controller {
	
    public function __construct() {
        parent::__construct();
	    if ( ! $this->loggedIn ) {
		    $this->session->set_userdata( 'requested_page', $this->uri->uri_string() );
		    $this->rerp->md( 'login' );
	    }
	    $this->lang->admin_load( 'products', $this->Settings->user_language );
	    $this->load->library( 'form_validation' );
	    $this->load->admin_model( 'products_model' );
    }

    /* ------------------------------------------------------- */
	
	public function add( $id = null ) {
		// Init.
		
		$this->rerp->checkPermissions();
		$this->load->helper('security');
		$warehouses = $this->site->getAllWarehouses();
		
		$data               = [];
		$items              = [];
		$warehouse_qty      = [];
		$product_attributes = [];
		$photos             = [];
		
        // Prepare validation rules.
        $this->form_validation->set_rules('category', lang('category'), 'required|is_natural_no_zero');
        
        if ($this->input->post('type') == 'standard') {
            $this->form_validation->set_rules('cost', lang('product_cost'), 'required');
            $this->form_validation->set_rules('unit', lang('product_unit'), 'required');
        }
        
        $this->form_validation->set_rules('code', lang('product_code'), 'is_unique[products.code]|alpha_dash');
		
		if ( SHOP ) {
            $this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[products.slug]|alpha_dash');
        }
        
        $this->form_validation->set_rules('weight', lang('weight'), 'numeric');
        $this->form_validation->set_rules('product_image', lang('product_image'), 'xss_clean');
        $this->form_validation->set_rules('digital_file', lang('digital_file'), 'xss_clean');
        $this->form_validation->set_rules('userfile', lang('product_gallery_images'), 'xss_clean');
		
		if ( $this->input->post( 'promotion' ) ) {
			$this->form_validation->set_rules( 'promo_price', lang( 'promo_price' ), 'required' );
		}

		if ( $this->input->post( 'cash_back' ) ) {
			if ( $this->input->post( 'cash_back_percentage_on' ) ) {
				$this->form_validation->set_rules( 'cash_back_percentage', lang( 'cash_back_percentage' ), 'required|greater_than[0]|less_than_equal_to[100]' );

			} else {
				$this->form_validation->set_rules( 'cash_back_amount', lang( 'cash_back_amount' ), 'required|greater_than[0]' );

			}
		}
		
        // Prepare and validate data.
		if ( $this->form_validation->run() == true ) {
	
	        $tax_rate = $this->input->post( 'tax_rate' ) ? $this->site->getTaxRateByID( $this->input->post( 'tax_rate' ) ) : null;
            $data     = [
	            'code'              => $this->input->post( 'code' ),
	            'barcode_symbology' => $this->input->post( 'barcode_symbology' ),
	            'name'              => $this->input->post( 'name' ),
	            'type'              => $this->input->post( 'type' ),
	            'brand'             => $this->input->post( 'brand' ),
	            'category_id'       => $this->input->post( 'category' ),
	            'subcategory_id'    => $this->input->post( 'subcategory' ) ? $this->input->post( 'subcategory' ) : null,
	            'cost'              => $this->rerp->formatDecimal( $this->input->post( 'cost' ) ),
	            'price'             => $this->rerp->formatDecimal( $this->input->post( 'price' ) ),
	            'unit'              => $this->input->post( 'unit' ),
	            'sale_unit'         => $this->input->post( 'default_sale_unit' ),
	            'purchase_unit'     => $this->input->post( 'default_purchase_unit' ),
	            'tax_rate'          => $this->input->post( 'tax_rate' ),
	            'tax_method'        => $this->input->post( 'tax_method' ),
	            'alert_quantity'    => $this->input->post( 'alert_quantity' ),
	            'track_quantity'    => $this->input->post( 'track_quantity' ) ? $this->input->post( 'track_quantity' ) : '0',
	            'product_details'   => $this->input->post( 'product_details' ),
	            'details'           => $this->input->post( 'details' ),
	            'supplier1'         => $this->input->post( 'supplier' ),
	            'supplier1price'    => $this->rerp->formatDecimal( $this->input->post( 'supplier_price' ) ),
	            'supplier2'         => $this->input->post( 'supplier_2' ),
	            'supplier2price'    => $this->rerp->formatDecimal( $this->input->post( 'supplier_2_price' ) ),
	            'supplier3'         => $this->input->post( 'supplier_3' ),
	            'supplier3price'    => $this->rerp->formatDecimal( $this->input->post( 'supplier_3_price' ) ),
	            'supplier4'         => $this->input->post( 'supplier_4' ),
	            'supplier4price'    => $this->rerp->formatDecimal( $this->input->post( 'supplier_4_price' ) ),
	            'supplier5'         => $this->input->post( 'supplier_5' ),
	            'supplier5price'    => $this->rerp->formatDecimal( $this->input->post( 'supplier_5_price' ) ),
	            'cf1'               => $this->input->post( 'cf1' ),
	            'cf2'               => $this->input->post( 'cf2' ),
	            'cf3'               => $this->input->post( 'cf3' ),
	            'cf4'               => $this->input->post( 'cf4' ),
	            'cf5'               => $this->input->post( 'cf5' ),
	            'cf6'               => $this->input->post( 'cf6' ),
	            'promotion'         => $this->input->post( 'promotion' ),
	            'promo_price'       => $this->rerp->formatDecimal( $this->input->post( 'promo_price' ) ),
	            'start_date'        => null,
	            'end_date'          => null,
	            'cash_back'            => $this->input->post( 'cash_back' ),
	            'cash_back_amount'     => $this->rerp->formatDecimal( $this->input->post( 'cash_back_amount' ) ),
	            'cash_back_start_date' => null,
	            'cash_back_end_date'   => null,
	            'cash_back_percentage' => $this->input->post( 'cash_back_percentage' ),
	            'supplier1_part_no' => $this->input->post( 'supplier_part_no' ),
	            'supplier2_part_no' => $this->input->post( 'supplier_2_part_no' ),
	            'supplier3_part_no' => $this->input->post( 'supplier_3_part_no' ),
	            'supplier4_part_no' => $this->input->post( 'supplier_4_part_no' ),
	            'supplier5_part_no' => $this->input->post( 'supplier_5_part_no' ),
	            'file'              => $this->input->post( 'file_link' ),
	            'slug'              => $this->input->post( 'slug' ),
	            'weight'            => $this->input->post( 'weight' ),
	            'featured'          => $this->input->post( 'featured' ),
	            'hsn_code'          => $this->input->post( 'hsn_code' ),
	            'hide'              => $this->input->post( 'hide' ) ? $this->input->post( 'hide' ) : 0,
	            'second_name'       => $this->input->post( 'second_name' ),
            ];
            
            if ( $this->input->post( 'end_date' ) ) {
            	$data['end_date'] = $this->input->post( 'end_date' ) ? $this->rerp->fsd( $this->input->post( 'end_date' ) ) : null;
            }
            
            if ( $data['end_date'] ) {
	            $data['start_date'] = $this->input->post( 'start_date' ) ? $this->rerp->fsd( $this->input->post( 'start_date' ) ) : null;
	            if ( ! $data['start_date'] ) {
		            $data['start_date'] = date( 'Y-m-d' );
	            }
            }
			
			if ( $this->input->post( 'cash_back_end_date' ) ) {
				$data['cash_back_end_date'] = $this->input->post( 'cash_back_end_date' ) ? $this->rerp->fsd( $this->input->post( 'cash_back_end_date' ) ) : null;
			}
			
			if ( $data['cash_back_end_date'] ) {
				$data['cash_back_start_date'] = $this->input->post( 'cash_back_start_date' ) ? $this->rerp->fsd( $this->input->post( 'cash_back_start_date' ) ) : null;
				if ( ! $data['start_date'] ) {
					$data['start_date'] = date( 'Y-m-d' );
				}
			}

			//calculation of cash_back_percentage
			if ( $data['cash_back_percentage'] > 0 && $data['cash_back_percentage'] <= 100 ){
				$data['cash_back_amount'] = $this->rerp->formatDecimal(( $data['price'] * $data['cash_back_percentage'] ) / 100 );
			}
			elseif ( $data['cash_back_amount'] > 0 ) {
				$data['cash_back_percentage'] = $this->rerp->formatDecimal( ( $data['cash_back_amount'] / $data['price'] ) * 100 );
			}
			else {
				$data['cash_back_percentage'] = 0;
			}

            $warehouse_qty      = null;
            $product_attributes = null;
            
            $this->load->library('upload');
			
			if ( $this->input->post( 'type' ) == 'standard' ) {
				$wh_total_quantity = 0;
				$pv_total_quantity = 0;
				for ( $s = 2; $s > 5; $s ++ ) {
					$data[ 'suppliers' . $s ]           = $this->input->post( 'supplier_' . $s );
					$data[ 'suppliers' . $s . 'price' ] = $this->input->post( 'supplier_' . $s . '_price' );
				}
				foreach ( $warehouses as $warehouse ) {
					if ( $this->input->post( 'wh_qty_' . $warehouse->id ) ) {
						$warehouse_qty[]   = [
							'warehouse_id' => $this->input->post( 'wh_' . $warehouse->id ),
							'quantity'     => $this->input->post( 'wh_qty_' . $warehouse->id ),
							'rack'         => $this->input->post( 'rack_' . $warehouse->id ) ? $this->input->post( 'rack_' . $warehouse->id ) : null,
						];
						$wh_total_quantity += $this->input->post( 'wh_qty_' . $warehouse->id );
					}
				}
				
				if ( $this->input->post( 'attributes' ) ) {
					$a = sizeof( $_POST['attr_name'] );
					for ( $r = 0; $r <= $a; $r ++ ) {
						if ( isset( $_POST['attr_name'][ $r ] ) ) {
							$pv_total_quantity    += $_POST['attr_quantity'][ $r ];
							$product_attributes[] = [
								'name'         => $_POST['attr_name'][ $r ],
								'warehouse_id' => $_POST['attr_warehouse'][ $r ],
								'quantity'     => $_POST['attr_quantity'][ $r ],
								'price'        => $_POST['attr_price'][ $r ],
							];
						}
					}
				} else {
					$product_attributes = null;
				}
				
				if ( $wh_total_quantity != $pv_total_quantity && $pv_total_quantity != 0 ) {
					$this->form_validation->set_rules( 'wh_pr_qty_issue', 'wh_pr_qty_issue', 'required' );
					$this->form_validation->set_message( 'required', lang( 'wh_pr_qty_issue' ) );
				}
            }
	
	        if ( $this->input->post( 'type' ) == 'service' ) {
                $data['track_quantity'] = 0;
	        } elseif ( $this->input->post( 'type' ) == 'combo' ) {
                $total_price = 0;
                $c           = sizeof($_POST['combo_item_code']) - 1;
		        for ( $r = 0; $r <= $c; $r ++ ) {
			        if ( isset( $_POST['combo_item_code'][ $r ] ) && isset( $_POST['combo_item_quantity'][ $r ] ) && isset( $_POST['combo_item_price'][ $r ] ) ) {
				        $items[] = [
					        'item_code'  => $_POST['combo_item_code'][ $r ],
					        'quantity'   => $_POST['combo_item_quantity'][ $r ],
					        'unit_price' => $_POST['combo_item_price'][ $r ],
				        ];
			        }
			        $total_price += $_POST['combo_item_price'][ $r ] * $_POST['combo_item_quantity'][ $r ];
		        }
		        if ( $this->rerp->formatDecimal( $total_price ) != $this->rerp->formatDecimal( $this->input->post( 'price' ) ) ) {
			        $this->form_validation->set_rules( 'combo_price', 'combo_price', 'required' );
			        $this->form_validation->set_message( 'required', lang( 'pprice_not_match_ciprice' ) );
		        }
                $data['track_quantity'] = 0;
	        } elseif ( $this->input->post( 'type' ) == 'digital' ) {
		        if ( $_FILES['digital_file']['size'] > 0 ) {
			        $this->upload->initialize( [
				        'upload_path'   => $this->digital_upload_path,
				        'allowed_types' => $this->digital_file_types,
				        'max_size'      => $this->allowed_file_size,
				        'overwrite'     => false,
				        'encrypt_name'  => true,
				        'max_filename'  => 25,
			        ] );
			        if ( ! $this->upload->do_upload( 'digital_file' ) ) {
				        $error = $this->upload->display_errors();
				        $this->session->set_flashdata( 'error', $error );
				        admin_redirect( 'products/add' );
			        }
			        $file         = $this->upload->file_name;
			        $data['file'] = $file;
		        } else {
			        if ( ! $this->input->post( 'file_link' ) ) {
				        $this->form_validation->set_rules( 'digital_file', lang( 'digital_file' ), 'required' );
			        }
		        }
		        $data['track_quantity'] = 0;
	        }
	        
	        if ( ! isset( $items ) ) {
		        $items = null;
	        }
			
			if ( $_FILES['product_image']['size'] > 0 ) {
    
				$this->upload->initialize( [
					'upload_path'   => $this->upload_path,
					'allowed_types' => $this->image_types,
					'max_size'      => $this->allowed_file_size,
					'max_width'     => $this->Settings->iwidth,
					'max_height'    => $this->Settings->iheight,
					'overwrite'     => false,
					'max_filename'  => 25,
					'encrypt_name'  => true,
				] );
	            if ( ! $this->upload->do_upload( 'product_image' ) ) {
		            $error = $this->upload->display_errors();
		            $this->session->set_flashdata( 'error', $error );
		            admin_redirect( 'products/add' );
	            }
                $photo         = $this->upload->file_name;
                $data['image'] = $photo;
                $this->load->library('image_lib');
                
                $this->image_lib->clear();
				$this->image_lib->initialize( [
					'image_library'  => 'gd2',
					'source_image'   => $this->upload_path . $photo,
					'new_image'      => $this->thumbs_path . $photo,
					'maintain_ratio' => true,
					'width'          => $this->Settings->twidth,
					'height'         => $this->Settings->theight,
				] );
				if ( ! $this->image_lib->resize() ) {
                    echo $this->image_lib->display_errors();
                }
				if ( $this->Settings->watermark ) {
                    $this->image_lib->clear();
					$this->image_lib->initialize( [
						'source_image'     => $this->upload_path . $photo,
						'wm_text'          => 'Copyright ' . date( 'Y' ) . ' - ' . $this->Settings->site_name,
						'wm_type'          => 'text',
						'wm_font_path'     => 'system/fonts/texb.ttf',
						'quality'          => '100',
						'wm_font_size'     => '16',
						'wm_font_color'    => '999999',
						'wm_shadow_color'  => 'CCCCCC',
						'wm_vrt_alignment' => 'top',
						'wm_hor_alignment' => 'left',
						'wm_padding'       => '10',
					] );
					$this->image_lib->watermark();
                }
                $this->image_lib->clear();
            }
			
			if ( $_FILES['userfile']['name'][0] != '' ) {
				
				$files                   = $_FILES;
				$cpt                     = count( $_FILES['userfile']['name'] );
				
				for ( $i = 0; $i < $cpt; $i ++ ) {
					$_FILES['userfile']['name']     = $files['userfile']['name'][ $i ];
					$_FILES['userfile']['type']     = $files['userfile']['type'][ $i ];
					$_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][ $i ];
					$_FILES['userfile']['error']    = $files['userfile']['error'][ $i ];
					$_FILES['userfile']['size']     = $files['userfile']['size'][ $i ];
					
					$this->upload->initialize( [
						'upload_path'   => $this->upload_path,
						'allowed_types' => $this->image_types,
						'max_size'      => $this->allowed_file_size,
						'max_width'     => $this->Settings->iwidth,
						'max_height'    => $this->Settings->iheight,
						'overwrite'     => false,
						'encrypt_name'  => true,
						'max_filename'  => 25,
					] );
					
					if ( ! $this->upload->do_upload() ) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata( 'error', $error );
						admin_redirect( 'products/add' );
					} else {
						$pho = $this->upload->file_name;
						
						$photos[] = $pho;
						
						$this->load->library( 'image_lib' );
						
						$this->image_lib->initialize( [
							'image_library'  => 'gd2',
							'source_image'   => $this->upload_path . $pho,
							'new_image'      => $this->thumbs_path . $pho,
							'maintain_ratio' => true,
							'width'          => $this->Settings->twidth,
							'height'         => $this->Settings->theight,
						] );
						
						if ( ! $this->image_lib->resize() ) {
							echo $this->image_lib->display_errors();
						}
						
						if ( $this->Settings->watermark ) {
							$this->image_lib->clear();
							$this->image_lib->initialize( [
								'source_image'     => $this->upload_path . $pho,
								'wm_text'          => 'Copyright ' . date( 'Y' ) . ' - ' . $this->Settings->site_name,
								'wm_type'          => 'text',
								'wm_font_path'     => 'system/fonts/texb.ttf',
								'quality'          => '100',
								'wm_font_size'     => '16',
								'wm_font_color'    => '999999',
								'wm_shadow_color'  => 'CCCCCC',
								'wm_vrt_alignment' => 'top',
								'wm_hor_alignment' => 'left',
								'wm_padding'       => '10',
							] );
							$this->image_lib->watermark();
						}
						
						$this->image_lib->clear();
					}
				}
            
            } else {
                $photos = null;
            }
            $data['quantity'] = $wh_total_quantity ?? 0;
        }
		
		// Save or show the input form.
		if ( $this->form_validation->run() == true && ! empty( $data ) ) {
			$product_id = $this->products_model->addProduct( $data, $items, $warehouse_qty, $product_attributes, $photos );
			if ( $product_id ) {
				$this->save_all_meta( $product_id );
				ci_delete_category_caches();
				$this->session->set_flashdata( 'message', lang( 'product_added' ) );
			} else {
				$this->session->set_flashdata( 'message', lang( 'product_not_added' ) );
			}
            admin_redirect('products');
        } else {
	        $this->data['error']               = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
	        $selectedCat = $this->input->post( 'category', true );
	        $this->data['categories']          = $this->build_category_dropdown_options( null,  $selectedCat );
//	        $this->data['categories']          = $this->site->getAllCategories();
			$tax_rates                         = $this->site->getAllTaxRates();
			$_tax_rates[''] = '';
			foreach ( $tax_rates as $tax ) {
				$_tax_rates[$tax->id] = $tax->name;
			}
			$this->data['tax_rates']           = $_tax_rates;
			$brands              = $this->site->getAllBrands();
			$_brands[''] = '';
			foreach ( $brands as $brand ) {
				$_brands[ $brand->id ] = $brand->name;
			}
			$this->data['brands']              = $_brands;
	        $this->data['base_units']          = $this->site->getAllBaseUnits();
	        $this->data['warehouses']          = $warehouses;
	        $this->data['warehouses_products'] = $id ? $this->products_model->getAllWarehousesWithPQ( $id ) : null;
	        $this->data['product']             = $id ? $this->products_model->getProductByID( $id ) : null;
	        $this->data['variants']            = $this->products_model->getAllVariants();
	        $this->data['combo_items']         = ( $id && $this->data['product']->type == 'combo' ) ? $this->products_model->getProductComboItems( $id ) : null;
	        $this->data['product_options']     = $id ? $this->products_model->getProductOptionsWithWH( $id ) : null;
			$this->data['product_images']      = $this->products_model->getProductPhotos( $id );
	        $bc                                = [
		        [
			        'link' => base_url(),
			        'page' => lang( 'home' ),
		        ],
		        [ 'link' => admin_url( 'products' ), 'page' => lang( 'products' ) ],
		        [ 'link' => '#', 'page' => lang( 'add_product' ) ],
	        ];
	        $meta                              = [
		        'page_title' => lang( 'add_product' ),
		        'bc'         => $bc,
	        ];
			
			$this->data['custom_fields'] = [];
			$this->data['custom_values'] = [];
			if ( isset( $this->themeInfos['product_custom'], $this->themeInfos['product_custom']['fields'] ) ) {
				$this->data['custom_fields'] = $this->themeInfos['product_custom']['fields'];
				foreach ( $this->data['custom_fields'] as $field ) {
					if ( ! isset( $field['id'] ) ) {
						continue;
					}
					$metaData = $this->input->post( $field['id'], true );
					$this->data['custom_values'][ $field['id'] ] = $metaData;
				}
			}
	        
	        $this->page_construct( 'products/add', $meta, $this->data );
        }
    }
    
    public function import_image_csv() {
	    $this->rerp->checkPermissions('csv');
	    $this->load->helper('security');
	    $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');
	    if ( $this->form_validation->run() == true ) {
	    	
		    if ( isset( $_FILES['userfile'] ) ) {
			    $this->load->library( 'upload' );
			    $this->upload->initialize( [
				    'upload_path'   => $this->digital_upload_path,
				    'allowed_types' => 'csv',
				    'max_size'      => $this->allowed_file_size,
				    'overwrite'     => true,
				    'encrypt_name'  => true,
				    'max_filename'  => 25,
			    ] );
			    if ( ! $this->upload->do_upload() ) {
				    $error = $this->upload->display_errors();
				    $this->session->set_flashdata( 'error', $error );
				    admin_redirect( 'products/import_csv' );
			    }
			    $csv = $this->upload->file_name;
			    $handle = fopen( $this->digital_upload_path . $csv, 'r' );
			    if ( $handle ) {
			    	// Ignore header row.
				    fgetcsv( $handle );
				    $updated = '';
				    while ( ( $row = fgetcsv( $handle ) ) !== false ) {
					    $row = array_filter( $row );
					    if ( count( $row ) < 2 ) {
					    	continue;
					    }
					    $product = $this->products_model->getProductByCode( $row[0] );
					    if ( ! $product || ! isset( $product->id ) || ( isset( $product->id ) && ! $product->id ) ) {
					    	continue;
					    }
					    unset( $row[0] );
					    $i = 0;
					    $ic = 0;
					    foreach ( $row as $img ) {
						    $filename = $this->import_product_image( $img );
						    if ( ! $filename ) {
						    	continue;
						    }
						    // remove old file for main image.
						    if ( 0 === $i && ! empty( $product->image ) ) {
							    $imagePath = $this->upload_path . $product->image;
							    if ( file_exists( $imagePath ) ) {
							    	unlink( $imagePath );
							    }
							    $imagePath = $this->thumbs_path . $product->image;
							    if ( file_exists( $imagePath ) ) {
								    unlink( $imagePath );
							    }
						    }
						    if ( $this->insert_product_image( $product->id, $filename, $i > 0 ) ) {
							    $ic++;
						    }
						    $i++;
					    }
					    if ( $ic ) {
						    $updated .= '<p>' . ( $ic > 1 ? $ic . ' images' : $ic . ' image' ) . ' uploaded to ' . $product->code . '</p>';
					    }
				    }
				    fclose( $handle );
				    if ( $updated ) {
					    $this->session->set_flashdata('message', $updated );
				    } else {
					    $this->session->set_flashdata('error', 'Nothing uploaded' );
				    }
			    } else {
				    $this->session->set_flashdata('error', 'Unable to read the uploaded csv file.' );
			    }
		    } else {
			    $this->session->set_flashdata('error', 'Invalid Request.' );
		    }
		    admin_redirect( 'products' );
	    } else {
		    $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
		    $this->data['userfile'] = [
			    'name'  => 'userfile',
			    'id'    => 'userfile',
			    'type'  => 'text',
			    'value' => $this->form_validation->set_value( 'userfile' ),
		    ];
		    $bc   = [
			    [ 'link' => base_url(), 'page' => lang( 'home' ) ],
			    [ 'link' => admin_url( 'products' ), 'page' => lang( 'products' ) ],
			    [ 'link' => '#', 'page' => lang( 'import_products_by_csv' ) ],
		    ];
		    $this->data['image_valid'] = [
			    'type'   => explode( '|', $this->image_types ),
			    'size'   => $this->allowed_file_size,
			    'width'  => $this->Settings->iwidth,
			    'height' => $this->Settings->iheight,
		    ];
		    $meta = [ 'page_title' => lang( 'import_product_images_by_csv' ), 'bc' => $bc ];
		    $this->page_construct('products/import_image_csv', $meta, $this->data);
	    }
    }
	
	/**
	 * Import image from remote server.
	 *
	 * @param string $url
	 *
	 * @return bool|string
	 */
    protected function import_product_image( $url ) {
	    $img = file_get_contents( $url );
	    $fileSize = ( strlen( $img ) / 1024 );
	    if ( $fileSize < 0 || $fileSize > $this->allowed_file_size ) {
	    	return false;
	    }
	    $size = getimagesizefromstring( $img );
	    if ( ! $size ) {
		    return false;
	    }
	    list( $width, $height ) = $size;
	    if ( $width > $this->Settings->iwidth || $height > $this->Settings->iheight ) {
	    	return false;
	    }
	    $ext = strtolower( str_replace( 'image/', '', $size['mime'] ) );
	    if ( ! in_array( $ext, explode( '|', $this->image_types ) ) ) {
		    return false;
	    }
	    if ( 'jpeg' === $ext ) $ext = 'jpg';
	    $filename = md5( uniqid( mt_rand() ) ) . '.' . $ext;
	
	    if ( memory_get_usage() && ( $memory_limit = ini_get( 'memory_limit' ) ) > 0 ) {
		    $memory_limit = str_split( $memory_limit, strspn( $memory_limit, '1234567890' ) );
		    if ( ! empty( $memory_limit[1] ) ) {
			    switch ( $memory_limit[1][0] ) {
				    case 'g':
				    case 'G':
					    $memory_limit[0] *= 1024 * 1024 * 1024;
					    break;
				    case 'm':
				    case 'M':
					    $memory_limit[0] *= 1024 * 1024;
					    break;
				    default:
					    break;
			    }
		    }
		    $memory_limit = (int) ceil( $fileSize + $memory_limit[0] );
		    ini_set( 'memory_limit', $memory_limit );
	    }
	    
	    // check xss
	    if ( ! ! preg_match('/<(a|body|head|html|img|plaintext|pre|script|table|title)[\s>]/i', substr( $img, 0, 256 ) ) ) {
	    	return false;
	    }
	    $imagePath = $this->upload_path . $filename;
	    if ( ! file_put_contents( $imagePath, $img ) ) {
	    	// can't write file.
	    	return false;
	    }
	    $this->load->library( 'image_lib' );
	    $this->image_lib->clear();
	    $this->image_lib->initialize( [
		    'image_library'  => 'gd2',
		    'source_image'   => $imagePath,
		    'new_image'      => $this->thumbs_path . $filename,
		    'maintain_ratio' => true,
		    'width'          => $this->Settings->twidth,
		    'height'         => $this->Settings->theight,
	    ] );
	    if ( ! $this->image_lib->resize() ) {
		    // can't resize.
		    unlink( $imagePath );
		    return false;
	    }
	    if ( $this->Settings->watermark ) {
		    $this->image_lib->clear();
		    $this->image_lib->initialize( [
			    'source_image'     => $imagePath,
			    'wm_text'          => 'Copyright ' . date( 'Y' ) . ' - ' . $this->Settings->site_name,
			    'wm_type'          => 'text',
			    'wm_font_path'     => 'system/fonts/texb.ttf',
			    'quality'          => '100',
			    'wm_font_size'     => '16',
			    'wm_font_color'    => '999999',
			    'wm_shadow_color'  => 'CCCCCC',
			    'wm_vrt_alignment' => 'top',
			    'wm_hor_alignment' => 'left',
			    'wm_padding'       => '10',
		    ] );
		    if ( ! $this->image_lib->watermark() ) {
			    $this->image_lib->clear();
			    // can't watermark.
			    unlink( $imagePath );
			    return false;
		    }
	    }
	    // return the file name.
	    return $filename;
    }
	
	/**
	 * Set Product image.
	 *
	 * @param int    $id         Product id.
	 * @param string $image      Image file name.
	 * @param bool   $additional add the image as main or additional image.
	 *
	 * @return bool
	 */
    protected function insert_product_image( $id, $image, $additional = false ) {
		if ( ! $additional ) {
			return true === $this->products_model->updateProductImage( $id, $image  );
		} else {
			return true === $this->products_model->addProductAdditionalImage( $id, $image );
		}
    }
    
    protected function remove_product_image( $id, $image = null ) {
        $product = $this->products_model->getProductByID( $id );
        if ( ! $product || ! isset( $product->id ) ) {
        	return false;
        }
    	if ( ! $image ) {
    		// if product doesn't have image. nothing to remove then.
    		if ( $product->image ) {
    			if ( file_exists( $this->upload_path . $product->image ) ) {
    				unlink( $this->upload_path . $product->image );
			    }
    			if ( file_exists( $this->thumbs_path . $product->image ) ) {
    				unlink( $this->thumbs_path . $product->image );
			    }
			    return $this->products_model->updateProductImage( $id, '' );
		    }
	    }
    	// additional image.
    	if ( $image && is_numeric( $image ) ) {
    		return $this->products_model->deleteProductAdditionalImage( $id, $image );
	    }
    	return false;
    }
	
	public function add_adjustment( $count_id = null ) {
        $this->rerp->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->rerp->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');
            $note         = $this->rerp->clear_tags($this->input->post('note'));

            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $product_id = $_POST['product_id'][$r];
                $type       = $_POST['type'][$r];
                $quantity   = $_POST['quantity'][$r];
                $serial     = $_POST['serial'][$r];
                $variant    = isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : null;

                if (!$this->Settings->overselling && $type == 'subtraction' && !$count_id) {
                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $products[] = [
                    'product_id'   => $product_id,
                    'type'         => $type,
                    'quantity'     => $quantity,
                    'warehouse_id' => $warehouse_id,
                    'option_id'    => $variant,
                    'serial_no'    => $serial,
                ];
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('products'), 'required');
            } else {
                krsort($products);
            }

            $data = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
                'count_id'     => $this->input->post('count_id') ? $this->input->post('count_id') : null,
            ];

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $this->upload->initialize( [
	                'upload_path'   => $this->digital_upload_path,
	                'allowed_types' => $this->digital_file_types,
	                'max_size'      => $this->allowed_file_size,
	                'overwrite'     => false,
	                'encrypt_name'  => true,
                ] );
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->rerp->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->addAdjustment($data, $products)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/quantity_adjustments');
        } else {
            if ($count_id) {
                $stock_count = $this->products_model->getStouckCountByID($count_id);
                $items       = $this->products_model->getStockCountItems($count_id);
                foreach ($items as $item) {
                    $c = sha1(uniqid(mt_rand(), true));
                    if ($item->counted != $item->expected) {
                        $product     = $this->site->getProductByID($item->product_id);
                        $row         = json_decode('{}');
                        $row->id     = $item->product_id;
                        $row->code   = $product->code;
                        $row->name   = $product->name;
                        $row->qty    = $item->counted - $item->expected;
                        $row->type   = $row->qty > 0 ? 'addition' : 'subtraction';
                        $row->qty    = $row->qty > 0 ? $row->qty : (0 - $row->qty);
                        $options     = $this->products_model->getProductOptions($product->id);
                        $row->option = $item->product_variant_id ? $item->product_variant_id : 0;
                        $row->serial = '';
                        $ri          = $this->Settings->item_addition ? $product->id : $c;

                        $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                            'row'        => $row, 'options' => $options, ];
                        $c++;
                    }
                }
            }
            $this->data['adjustment_items'] = $count_id ? json_encode($pr) : false;
            $this->data['warehouse_id']     = $count_id ? $stock_count->warehouse_id : false;
            $this->data['count_id']         = $count_id;
            $this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses']       = $this->site->getAllWarehouses();
            $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('add_adjustment')]];
            $meta                           = ['page_title' => lang('add_adjustment'), 'bc' => $bc];
            $this->page_construct('products/add_adjustment', $meta, $this->data);
        }
    }

    public function add_adjustment_by_csv()
    {
        $this->rerp->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->rerp->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');
            $note         = $this->rerp->clear_tags($this->input->post('note'));
            $data         = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
                'count_id'     => null,
            ];

            if ($_FILES['csv_file']['size'] > 0) {
                $this->load->library('upload');
	
                $this->upload->initialize( [
	                'upload_path'   => $this->digital_upload_path,
	                'allowed_types' => 'csv',
	                'max_size'      => $this->allowed_file_size,
	                'overwrite'     => false,
	                'encrypt_name'  => true,
                ] );
                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv                = $this->upload->file_name;
                $data['attachment'] = $csv;

                $arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys   = ['code', 'quantity', 'variant'];
                $final  = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                // $this->rerp->print_arrays($final);
                $rw = 2;
                foreach ($final as $pr) {
                    if ($product = $this->products_model->getProductByCode(trim($pr['code']))) {
                        $csv_variant = trim($pr['variant']);
                        $variant     = !empty($csv_variant) ? $this->products_model->getProductVariantID($product->id, $csv_variant) : false;

                        $csv_quantity = trim($pr['quantity']);
                        $type         = $csv_quantity > 0 ? 'addition' : 'subtraction';
                        $quantity     = $csv_quantity > 0 ? $csv_quantity : (0 - $csv_quantity);

                        if (!$this->Settings->overselling && $type == 'subtraction') {
                            if ($variant) {
                                if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                                    if ($op_wh_qty->quantity < $quantity) {
                                        $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                        redirect($_SERVER['HTTP_REFERER']);
                                    }
                                } else {
                                    $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER['HTTP_REFERER']);
                                }
                            }
                            if ($wh_qty = $this->products_model->getProductQuantity($product->id, $warehouse_id)) {
                                if ($wh_qty['quantity'] < $quantity) {
                                    $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER['HTTP_REFERER']);
                                }
                            } else {
                                $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        }

                        $products[] = [
                            'product_id'   => $product->id,
                            'type'         => $type,
                            'quantity'     => $quantity,
                            'warehouse_id' => $warehouse_id,
                            'option_id'    => $variant,
                        ];
                    } else {
                        $this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                    $rw++;
                }
            } else {
                $this->form_validation->set_rules('csv_file', lang('upload_file'), 'required');
            }

            // $this->rerp->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->addAdjustment($data, $products)) {
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/quantity_adjustments');
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('add_adjustment')]];
            $meta                     = ['page_title' => lang('add_adjustment_by_csv'), 'bc' => $bc];
            $this->page_construct('products/add_adjustment_by_csv', $meta, $this->data);
        }
    }

    public function addByAjax()
    {
        if (!$this->mPermissions('add')) {
            exit(json_encode(['msg' => lang('access_denied')]));
        }
        if ($this->input->get('token') && $this->input->get('token') == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $product = $this->input->get('product');
            if (!isset($product['code']) || empty($product['code'])) {
                exit(json_encode(['msg' => lang('product_code_is_required')]));
            }
            if (!isset($product['name']) || empty($product['name'])) {
                exit(json_encode(['msg' => lang('product_name_is_required')]));
            }
            if (!isset($product['category_id']) || empty($product['category_id'])) {
                exit(json_encode(['msg' => lang('product_category_is_required')]));
            }
            if (!isset($product['unit']) || empty($product['unit'])) {
                exit(json_encode(['msg' => lang('product_unit_is_required')]));
            }
            if (!isset($product['price']) || empty($product['price'])) {
                exit(json_encode(['msg' => lang('product_price_is_required')]));
            }
            if (!isset($product['cost']) || empty($product['cost'])) {
                exit(json_encode(['msg' => lang('product_cost_is_required')]));
            }
            if ($this->products_model->getProductByCode($product['code'])) {
                exit(json_encode(['msg' => lang('product_code_already_exist')]));
            }
            if ($row = $this->products_model->addAjaxProduct($product)) {
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                $pr       = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'qty' => 1, 'cost' => $row->cost, 'name' => $row->name, 'tax_method' => $row->tax_method, 'tax_rate' => $tax_rate, 'discount' => '0'];
                $this->rerp->send_json(['msg' => 'success', 'result' => $pr]);
            } else {
                exit(json_encode(['msg' => lang('failed_to_add_product')]));
            }
        } else {
            json_encode(['msg' => 'Invalid token']);
        }
    }

    public function adjustment_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $this->rerp->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->products_model->deleteAdjustment($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line('adjustment_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('quantity_adjustments');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('warehouse'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('created_by'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('note'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('items'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $adjustment = $this->products_model->getAdjustmentByID($id);
                        $created_by = $this->site->getUser($adjustment->created_by);
                        $warehouse  = $this->site->getWarehouseByID($adjustment->warehouse_id);
                        $items      = $this->products_model->getAdjustmentItems($id);
                        $products   = '';
                        if ($items) {
                            foreach ($items as $item) {
                                $products .= $item->product_name . '(' . $this->rerp->formatQuantity($item->type == 'subtraction' ? -$item->quantity : $item->quantity) . ')' . "\n";
                            }
                        }

                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->rerp->hrld($adjustment->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $adjustment->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $warehouse->name);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $created_by->first_name . ' ' . $created_by->last_name);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->rerp->decode_html($adjustment->note));
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $products);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
                    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical('center');
                    $filename = 'quantity_adjustments_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line('no_record_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function barcode($product_code = null, $bcs = 'code128', $height = 40)
    {
        if ($this->Settings->barcode_img) {
            header('Content-Type: image/png');
        } else {
            header('Content-type: image/svg+xml');
        }
        echo $this->rerp->barcode($product_code, $bcs, $height, true, false, true);
    }

    public function count_stock($page = null)
    {
        $this->rerp->checkPermissions('stock_count');
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');
        $this->form_validation->set_rules('type', lang('type'), 'required');

        if ($this->form_validation->run() == true) {
            $warehouse_id = $this->input->post('warehouse');
            $type         = $this->input->post('type');
            $categories   = $this->input->post('category') ? $this->input->post('category') : null;
            $brands       = $this->input->post('brand') ? $this->input->post('brand') : null;
            $this->load->helper('string');
            $name     = random_string('md5') . '.csv';
            $products = $this->products_model->getStockCountProducts($warehouse_id, $type, $categories, $brands);
            $pr       = 0;
            $rw       = 0;
            foreach ($products as $product) {
                if ($variants = $this->products_model->getStockCountProductVariants($warehouse_id, $product->id)) {
                    foreach ($variants as $variant) {
                        $items[] = [
                            'product_code' => $product->code,
                            'product_name' => $product->name,
                            'variant'      => $variant->name,
                            'expected'     => $variant->quantity,
                            'counted'      => '',
                        ];
                        $rw++;
                    }
                } else {
                    $items[] = [
                        'product_code' => $product->code,
                        'product_name' => $product->name,
                        'variant'      => '',
                        'expected'     => $product->quantity,
                        'counted'      => '',
                    ];
                    $rw++;
                }
                $pr++;
            }
            if (!empty($items)) {
                $csv_file = fopen('./files/' . $name, 'w');
                fprintf($csv_file, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($csv_file, [lang('product_code'), lang('product_name'), lang('variant'), lang('expected'), lang('counted')]);
                foreach ($items as $item) {
                    fputcsv($csv_file, $item);
                }
                // file_put_contents('./files/'.$name, $csv_file);
                // fwrite($csv_file, $txt);
                fclose($csv_file);
            } else {
                $this->session->set_flashdata('error', lang('no_product_found'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            if ($this->Owner || $this->Admin) {
                $date = $this->rerp->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }
            $category_ids   = '';
            $brand_ids      = '';
            $category_names = '';
            $brand_names    = '';
            if ($categories) {
                $r = 1;
                $s = sizeof($categories);
                foreach ($categories as $category_id) {
                    $category = $this->site->getCategoryByID($category_id);
                    if ($r == $s) {
                        $category_names .= $category->name;
                        $category_ids   .= $category->id;
                    } else {
                        $category_names .= $category->name . ', ';
                        $category_ids   .= $category->id . ', ';
                    }
                    $r++;
                }
            }
            if ($brands) {
                $r = 1;
                $s = sizeof($brands);
                foreach ($brands as $brand_id) {
                    $brand = $this->site->getBrandByID($brand_id);
                    if ($r == $s) {
                        $brand_names .= $brand->name;
                        $brand_ids   .= $brand->id;
                    } else {
                        $brand_names .= $brand->name . ', ';
                        $brand_ids   .= $brand->id . ', ';
                    }
                    $r++;
                }
            }
            $data = [
                'date'           => $date,
                'warehouse_id'   => $warehouse_id,
                'reference_no'   => $this->input->post('reference_no'),
                'type'           => $type,
                'categories'     => $category_ids,
                'category_names' => $category_names,
                'brands'         => $brand_ids,
                'brand_names'    => $brand_names,
                'initial_file'   => $name,
                'products'       => $pr,
                'rows'           => $rw,
                'created_by'     => $this->session->userdata('user_id'),
            ];
        }

        if ($this->form_validation->run() == true && $this->products_model->addStockCount($data)) {
            $this->session->set_flashdata('message', lang('stock_count_intiated'));
            admin_redirect('products/stock_counts');
        } else {
            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['brands']     = $this->site->getAllBrands();
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('count_stock')]];
            $meta                     = ['page_title' => lang('count_stock'), 'bc' => $bc];
            $this->page_construct('products/count_stock', $meta, $this->data);
        }
    }

    /* ------------------------------------------------------------------------------- */
	
	public function delete( $id = null ) {
        $this->rerp->checkPermissions(null, true);
		
		if ( $this->input->get( 'id' ) ) {
            $id = $this->input->get('id');
        }
		
		if ( $this->products_model->deleteProduct( $id ) ) {
			ci_delete_category_caches();
			if ( $this->input->is_ajax_request() ) {
                $this->rerp->send_json(['error' => 0, 'msg' => lang('product_deleted')]);
            }
            $this->session->set_flashdata('message', lang('product_deleted'));
            admin_redirect('welcome');
        }
    }

    public function delete_adjustment($id = null)
    {
        $this->rerp->checkPermissions('delete', true);

        if ($this->products_model->deleteAdjustment($id)) {
            $this->rerp->send_json(['error' => 0, 'msg' => lang('adjustment_deleted')]);
        }
    }

    public function delete_image($id = null)
    {
        $this->rerp->checkPermissions('edit', true);
        if ($id && $this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            $this->db->delete('product_photos', ['id' => $id]);
            $this->rerp->send_json(['error' => 0, 'msg' => lang('image_deleted')]);
        }
        $this->rerp->send_json(['error' => 1, 'msg' => lang('ajax_error')]);
    }

    /* -------------------------------------------------------- */
	
	public function edit( $id = null ) {
		
        $this->rerp->checkPermissions();
        $this->load->helper('security');
		
		if ( $this->input->post( 'id' ) ) {
			$id = $this->input->post( 'id' );
		}
		
		$warehouses          = $this->site->getAllWarehouses();
		$warehouses_products = $this->products_model->getAllWarehousesWithPQ( $id );
		$product             = $this->site->getProductByID( $id );
		
		if ( ! $id || ! $product ) {
			$this->session->set_flashdata( 'error', lang( 'prduct_not_found' ) );
			redirect( $_SERVER['HTTP_REFERER'] );
        }
		$this->form_validation->set_rules( 'category', lang( 'category' ), 'required|is_natural_no_zero' );
		
		if ($this->input->post('type') == 'standard') {
            $this->form_validation->set_rules('cost', lang('product_cost'), 'required');
            $this->form_validation->set_rules('unit', lang('product_unit'), 'required');
        }
		
        $this->form_validation->set_rules('code', lang('product_code'), 'alpha_dash');
		
        if ($this->input->post('code') !== $product->code) {
	        $this->form_validation->set_rules( 'code', lang( 'product_code' ), 'is_unique[products.code]' );
        }
		
		if ( SHOP ) {
            $this->form_validation->set_rules('slug', lang('slug'), 'required|alpha_dash');
			if ( $this->input->post( 'slug' ) !== $product->slug ) {
                $this->form_validation->set_rules('slug', lang('slug'), 'required|is_unique[products.slug]|alpha_dash');
            }
        }
		
		$this->form_validation->set_rules( 'weight', lang( 'weight' ), 'numeric' );
		$this->form_validation->set_rules( 'product_image', lang( 'product_image' ), 'xss_clean' );
		$this->form_validation->set_rules( 'digital_file', lang( 'digital_file' ), 'xss_clean' );
		$this->form_validation->set_rules( 'userfile', lang( 'product_gallery_images' ), 'xss_clean' );
		
		if ( $this->input->post( 'promotion' ) ) {
			$this->form_validation->set_rules( 'promo_price', lang( 'promo_price' ), 'required' );
		}
		
		if ( $this->input->post( 'cash_back' ) ) {
			if ( $this->input->post( 'cash_back_percentage_on' ) ) {
				$this->form_validation->set_rules( 'cash_back_percentage', lang( 'cash_back_percentage' ), 'required|greater_than[0]|less_than_equal_to[100]' );

			} else {
				$this->form_validation->set_rules( 'cash_back_amount', lang( 'cash_back_amount' ), 'required|greater_than[0]' );

			}
		}
		
		if ( $this->form_validation->run( 'products/add' ) == true ) {
			
			$data = [
				'code'                 => $this->input->post( 'code' ),
				'barcode_symbology'    => $this->input->post( 'barcode_symbology' ),
				'name'                 => $this->input->post( 'name' ),
				'type'                 => $this->input->post( 'type' ),
				'brand'                => $this->input->post( 'brand' ),
				'category_id'          => $this->input->post( 'category' ),
				'subcategory_id'       => $this->input->post( 'subcategory' ) ? $this->input->post( 'subcategory' ) : null,
				'cost'                 => $this->rerp->formatDecimal( $this->input->post( 'cost' ) ),
				'price'                => $this->rerp->formatDecimal( $this->input->post( 'price' ) ),
				'unit'                 => $this->input->post( 'unit' ),
				'sale_unit'            => $this->input->post( 'default_sale_unit' ),
				'purchase_unit'        => $this->input->post( 'default_purchase_unit' ),
				'tax_rate'             => $this->input->post( 'tax_rate' ),
				'tax_method'           => $this->input->post( 'tax_method' ),
				'alert_quantity'       => $this->input->post( 'alert_quantity' ),
				'track_quantity'       => $this->input->post( 'track_quantity' ) ? $this->input->post( 'track_quantity' ) : '0',
				'product_details'      => $this->input->post( 'product_details' ),
				'details'              => $this->input->post( 'details' ),
				'supplier1'            => $this->input->post( 'supplier' ),
				'supplier1price'       => $this->rerp->formatDecimal( $this->input->post( 'supplier_price' ) ),
				'supplier2'            => $this->input->post( 'supplier_2' ),
				'supplier2price'       => $this->rerp->formatDecimal( $this->input->post( 'supplier_2_price' ) ),
				'supplier3'            => $this->input->post( 'supplier_3' ),
				'supplier3price'       => $this->rerp->formatDecimal( $this->input->post( 'supplier_3_price' ) ),
				'supplier4'            => $this->input->post( 'supplier_4' ),
				'supplier4price'       => $this->rerp->formatDecimal( $this->input->post( 'supplier_4_price' ) ),
				'supplier5'            => $this->input->post( 'supplier_5' ),
				'supplier5price'       => $this->rerp->formatDecimal( $this->input->post( 'supplier_5_price' ) ),
				'cf1'                  => $this->input->post( 'cf1' ),
				'cf2'                  => $this->input->post( 'cf2' ),
				'cf3'                  => $this->input->post( 'cf3' ),
				'cf4'                  => $this->input->post( 'cf4' ),
				'cf5'                  => $this->input->post( 'cf5' ),
				'cf6'                  => $this->input->post( 'cf6' ),
				'promotion'            => $this->input->post( 'promotion' ),
				'promo_price'          => $this->rerp->formatDecimal( $this->input->post( 'promo_price' ) ),
				'start_date'           => null,
				'end_date'             => null,
				'cash_back'            => $this->input->post( 'cash_back' ),
				'cash_back_amount'     => $this->rerp->formatDecimal( $this->input->post( 'cash_back_amount' ) ),
				'cash_back_start_date' => null,
				'cash_back_end_date'   => null,
				'cash_back_percentage' => $this->input->post( 'cash_back_percentage' ),
				'supplier1_part_no'    => $this->input->post( 'supplier_part_no' ),
				'supplier2_part_no'    => $this->input->post( 'supplier_2_part_no' ),
				'supplier3_part_no'    => $this->input->post( 'supplier_3_part_no' ),
				'supplier4_part_no'    => $this->input->post( 'supplier_4_part_no' ),
				'supplier5_part_no'    => $this->input->post( 'supplier_5_part_no' ),
				'slug'                 => $this->input->post( 'slug' ),
				'weight'               => $this->input->post( 'weight' ),
				'featured'             => $this->input->post( 'featured' ),
				'hsn_code'             => $this->input->post( 'hsn_code' ),
				'hide'                 => $this->input->post( 'hide' ) ? $this->input->post( 'hide' ) : 0,
				'hide_pos'             => $this->input->post( 'hide_pos' ) ? $this->input->post( 'hide_pos' ) : 0,
				'second_name'          => $this->input->post( 'second_name' ),
			];
            
			if ( $this->input->post( 'end_date' ) ) {
				$data['end_date'] = $this->input->post( 'end_date' ) ? $this->rerp->fsd( $this->input->post( 'end_date' ) ) : null;
			}
			
			if ( $data['end_date'] ) {
				$data['start_date'] = $this->input->post( 'start_date' ) ? $this->rerp->fsd( $this->input->post( 'start_date' ) ) : null;
				if ( ! $data['start_date'] ) {
					$data['start_date'] = date( 'Y-m-d' );
				}
			}
			
			if ( $this->input->post( 'cash_back_end_date' ) ) {
				$data['cash_back_end_date'] = $this->input->post( 'cash_back_end_date' ) ? $this->rerp->fsd( $this->input->post( 'cash_back_end_date' ) ) : null;
			}
			
			if ( $data['cash_back_end_date'] ) {
				$data['cash_back_start_date'] = $this->input->post( 'cash_back_start_date' ) ? $this->rerp->fsd( $this->input->post( 'cash_back_start_date' ) ) : null;
				if ( ! $data['start_date'] ) {
					$data['start_date'] = date( 'Y-m-d' );
				}
			}

			//calculation of cash_back_percentage
			if ( $data['cash_back_percentage'] > 0 && $data['cash_back_percentage'] <= 100 ){
				$data['cash_back_amount'] = $this->rerp->formatDecimal(( $data['price'] * $data['cash_back_percentage'] ) / 100 );
			}
			elseif ( $data['cash_back_amount'] > 0 ) {
				$data['cash_back_percentage'] = $this->rerp->formatDecimal( ( $data['cash_back_amount'] / $data['price'] ) * 100 );
			}
			else {
				$data['cash_back_percentage'] = 0;
			}

            $warehouse_qty      = null;
            $product_attributes = null;
            $update_variants    = [];
            $this->load->library('upload');
            if ($this->input->post('type') == 'standard') {
                if ($product_variants = $this->products_model->getProductOptions($id)) {
                    foreach ($product_variants as $pv) {
                        $update_variants[] = [
                            'id'    => $this->input->post('variant_id_' . $pv->id),
                            'name'  => $this->input->post('variant_name_' . $pv->id),
                            'cost'  => $this->input->post('variant_cost_' . $pv->id),
                            'price' => $this->input->post('variant_price_' . $pv->id),
                        ];
                    }
                }
                for ($s = 2; $s > 5; $s++) {
                    $data['suppliers' . $s]           = $this->input->post('supplier_' . $s);
                    $data['suppliers' . $s . 'price'] = $this->input->post('supplier_' . $s . '_price');
                }
                foreach ($warehouses as $warehouse) {
                    $warehouse_qty[] = [
                        'warehouse_id' => $this->input->post('wh_' . $warehouse->id),
                        'rack'         => $this->input->post('rack_' . $warehouse->id) ? $this->input->post('rack_' . $warehouse->id) : null,
                    ];
                }

                if ($this->input->post('attributes')) {
                    $a = sizeof($_POST['attr_name']);
                    for ($r = 0; $r <= $a; $r++) {
                        if (isset($_POST['attr_name'][$r])) {
                            if ($product_variatnt = $this->products_model->getPrductVariantByPIDandName($id, trim($_POST['attr_name'][$r]))) {
                                $this->form_validation->set_message('required', lang('product_already_has_variant') . ' (' . $_POST['attr_name'][$r] . ')');
                                $this->form_validation->set_rules('new_product_variant', lang('new_product_variant'), 'required');
                            } else {
                                $product_attributes[] = [
                                    'name'         => $_POST['attr_name'][$r],
                                    'warehouse_id' => $_POST['attr_warehouse'][$r],
                                    'quantity'     => $_POST['attr_quantity'][$r],
                                    'price'        => $_POST['attr_price'][$r],
                                ];
                            }
                        }
                    }
                } else {
                    $product_attributes = null;
                }
            }

            if ($this->input->post('type') == 'service') {
                $data['track_quantity'] = 0;
            } elseif ($this->input->post('type') == 'combo') {
                $total_price = 0;
                $c           = sizeof($_POST['combo_item_code']) - 1;
                for ($r = 0; $r <= $c; $r++) {
                    if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r]) && isset($_POST['combo_item_price'][$r])) {
                        $items[] = [
                            'item_code'  => $_POST['combo_item_code'][$r],
                            'quantity'   => $_POST['combo_item_quantity'][$r],
                            'unit_price' => $_POST['combo_item_price'][$r],
                        ];
                    }
                    $total_price += $_POST['combo_item_price'][$r] * $_POST['combo_item_quantity'][$r];
                }
                if ($this->rerp->formatDecimal($total_price) != $this->rerp->formatDecimal($this->input->post('price'))) {
                    $this->form_validation->set_rules('combo_price', 'combo_price', 'required');
                    $this->form_validation->set_message('required', lang('pprice_not_match_ciprice'));
                }
                $data['track_quantity'] = 0;
            } elseif ($this->input->post('type') == 'digital') {
                if ($this->input->post('file_link')) {
                    $data['file'] = $this->input->post('file_link');
                }
                if ($_FILES['digital_file']['size'] > 0) {
	                
                    $this->upload->initialize( [
	                    'upload_path'   => $this->digital_upload_path,
	                    'allowed_types' => $this->digital_file_types,
	                    'max_size'      => $this->allowed_file_size,
	                    'overwrite'     => false,
	                    'encrypt_name'  => true,
	                    'max_filename'  => 25,
                    ] );
                    if (!$this->upload->do_upload('digital_file')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        admin_redirect('products/add');
                    }
                    $file         = $this->upload->file_name;
                    $data['file'] = $file;
                }
                
                $data['track_quantity'] = 0;
            }
			if ( ! isset( $items ) ) {
				$items = null;
			}
			
            if ($_FILES['product_image']['size'] > 0) {
            	
	            $this->upload->initialize( [
		            'upload_path'   => $this->upload_path,
		            'allowed_types' => $this->image_types,
		            'max_size'      => $this->allowed_file_size,
		            'max_width'     => $this->Settings->iwidth,
		            'max_height'    => $this->Settings->iheight,
		            'overwrite'     => false,
		            'encrypt_name'  => true,
		            'max_filename'  => 25,
	            ] );
	            if ( ! $this->upload->do_upload( 'product_image' ) ) {
		            $error = $this->upload->display_errors();
		            $this->session->set_flashdata( 'error', $error );
		            admin_redirect( 'products/edit/' . $id );
	            }
                $photo = $this->upload->file_name;
                
                $this->load->library('image_lib');
	            
                $this->image_lib->clear();
                $this->image_lib->initialize( [
	                'image_library'  => 'gd2',
	                'source_image'   => $this->upload_path . $photo,
	                'new_image'      => $this->thumbs_path . $photo,
	                'maintain_ratio' => true,
	                'width'          => $this->Settings->twidth,
	                'height'         => $this->Settings->theight,
                ] );
	            if ( ! $this->image_lib->resize() ) {
	            	// frontend will have some issue if thumb not there.
	            	unlink( $this->upload_path . $photo );
                    echo $this->image_lib->display_errors();
                } else {
		            $data['image'] = $photo;
		            if ( $this->Settings->watermark ) {
			            $this->image_lib->clear();
			            
			            $this->image_lib->initialize( [
				            'source_image'     => $this->upload_path . $photo,
				            'wm_text'          => 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name,
				            'wm_type'          => 'text',
				            'wm_font_path'     => 'system/fonts/texb.ttf',
				            'quality'          => '100',
				            'wm_font_size'     => '16',
				            'wm_font_color'    => '999999',
				            'wm_shadow_color'  => 'CCCCCC',
				            'wm_vrt_alignment' => 'top',
				            'wm_hor_alignment' => 'left',
				            'wm_padding'       => '10',
			            ] );
			            $this->image_lib->watermark();
		            }
	            }
                $this->image_lib->clear();
            }
			
			if ( $_FILES['userfile']['name'][0] != '' ) {
				$files                   = $_FILES;
                $cpt                     = count($_FILES['userfile']['name']);
                for ($i = 0; $i < $cpt; $i++) {
                    $_FILES['userfile']['name']     = $files['userfile']['name'][$i];
                    $_FILES['userfile']['type']     = $files['userfile']['type'][$i];
                    $_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
                    $_FILES['userfile']['error']    = $files['userfile']['error'][$i];
                    $_FILES['userfile']['size']     = $files['userfile']['size'][$i];
	
	                $this->upload->initialize( [
		                'upload_path'   => $this->upload_path,
		                'allowed_types' => $this->image_types,
		                'max_size'      => $this->allowed_file_size,
		                'max_width'     => $this->Settings->iwidth,
		                'max_height'    => $this->Settings->iheight,
		                'overwrite'     => false,
		                'encrypt_name'  => true,
		                'max_filename'  => 25,
	                ] );
	                
	                if ( ! $this->upload->do_upload() ) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        admin_redirect('products/edit/' . $id);
                    } else {
                        $pho = $this->upload->file_name;

                        $photos[] = $pho;

                        $this->load->library('image_lib');
                        
                        $this->image_lib->initialize( [
	                        'image_library'  => 'gd2',
	                        'source_image'   => $this->upload_path . $pho,
	                        'new_image'      => $this->thumbs_path . $pho,
	                        'maintain_ratio' => true,
	                        'width'          => $this->Settings->twidth,
	                        'height'         => $this->Settings->theight,
                        ] );

                        if (!$this->image_lib->resize()) {
                            echo $this->image_lib->display_errors();
                        }

                        if ($this->Settings->watermark) {
                            $this->image_lib->clear();
                            $this->image_lib->initialize( [
	                            'source_image'     => $this->upload_path . $pho,
	                            'wm_text'          => 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name,
	                            'wm_type'          => 'text',
	                            'wm_font_path'     => 'system/fonts/texb.ttf',
	                            'quality'          => '100',
	                            'wm_font_size'     => '16',
	                            'wm_font_color'    => '999999',
	                            'wm_shadow_color'  => 'CCCCCC',
	                            'wm_vrt_alignment' => 'top',
	                            'wm_hor_alignment' => 'left',
	                            'wm_padding'       => '10',
                            ] );
                            $this->image_lib->watermark();
                        }

                        $this->image_lib->clear();
                    }
                }
                
            } else {
                $photos = null;
            }
            
            $data['quantity'] = $wh_total_quantity ?? 0;
            // $this->rerp->print_arrays($data, $warehouse_qty, $update_variants, $product_attributes, $photos, $items);
        }
		
		if ( $this->form_validation->run() == true && $this->products_model->updateProduct( $id, $data, $items, $warehouse_qty, $product_attributes, $photos, $update_variants ) ) {
			$this->save_all_meta( $id );
			ci_delete_category_caches();
			$this->session->set_flashdata( 'message', lang( 'product_updated' ) );
			admin_redirect( 'products' );
        } else {
            $this->data['error']               = ( validation_errors() ? validation_errors() : $this->session->flashdata( 'error' ) );
			$selectedCat                       = $this->input->post( 'category', true );
			if ( ! $selectedCat ) {
				$selectedCat = $product->category_id;
			}
			
			$this->data['categories']          = $this->build_category_dropdown_options( null, null,  $selectedCat );
			$tax_rates                         = $this->site->getAllTaxRates();
			$_tax_rates[''] = '';
            foreach ( $tax_rates as $tax ) {
	            $_tax_rates[$tax->id] = $tax->name;
            }
			$this->data['tax_rates']           = $_tax_rates;
			$brands              = $this->site->getAllBrands();
			$_brands[''] = '';
			foreach ( $brands as $brand ) {
				$_brands[ $brand->id ] = $brand->name;
			}
			$this->data['brands']              = $_brands;
			$this->data['base_units']          = $this->site->getAllBaseUnits();
			$this->data['warehouses']          = $warehouses;
			$this->data['warehouses_products'] = $warehouses_products;
			$this->data['product']             = $product;
			$this->data['product_images']      = $this->products_model->getProductPhotos( $product->id );
			$this->data['variants']            = $this->products_model->getAllVariants();
			$this->data['subunits']            = $this->site->getUnitsByBUID( $product->unit );
			$this->data['product_variants']    = $this->products_model->getProductOptions( $id );
			$this->data['combo_items']         = $product->type == 'combo' ? $this->products_model->getProductComboItems( $product->id ) : null;
			$this->data['product_options']     = $id ? $this->products_model->getProductOptionsWithWH( $id ) : null;
			
			$bc   = [
				[ 'link' => base_url(), 'page' => lang( 'home' ) ],
				[ 'link' => admin_url( 'products' ), 'page' => lang( 'products' ) ],
				[ 'link' => '#', 'page' => lang( 'edit_product' ) ],
			];
			$meta = [
				'page_title' => lang( 'edit_product' ),
				'bc'         => $bc,
			];
			
			$this->data['custom_fields'] = [];
			$this->data['custom_values'] = [];
			if ( isset( $this->themeInfos['product_custom'], $this->themeInfos['product_custom']['fields'] ) ) {
				$this->data['custom_fields'] = $this->themeInfos['product_custom']['fields'];
				foreach ( $this->data['custom_fields'] as $field ) {
					if ( ! isset( $field['id'] ) ) {
						continue;
					}
					$metaData = $this->input->post( $field['id'], true );
					if ( ! $metaData ) {
						$metaData = $this->products_model->getProductMeta( $id, $field['id'], true );
					}
					$this->data['custom_values'][ $field['id'] ] = $metaData;
				}
			}
			
			$this->page_construct( 'products/edit', $meta, $this->data );
        }
    }
    
    public function ajax_remove_product_image() {
	    if( ! $this->input->is_ajax_request() ) {
		    redirect( $_SERVER['HTTP_REFERER'] );
	    }
	
	    $this->rerp->checkPermissions();
	    $this->load->helper('security');
	    $this->form_validation->set_rules( 'id', lang( 'product_id' ), 'required|numeric' );
	    $id = $image_id = false;
	    if ( $this->input->post( 'id' ) ) {
	    	$id = $this->input->post( 'id' );
	    }
	    if ( $this->input->post( 'img_id' ) ) {
		    $this->form_validation->set_rules( 'img_id', lang( 'additional_image_id' ), 'required|numeric' );
	    	$image_id = $this->input->post( 'img_id' );
	    }
	    if ( $this->form_validation->run() == true ) {
	    	if ( $this->remove_product_image( $id, $image_id ) ) {
			    $this->rerp->send_json( [
				    'status'  => 'success',
				    'message' => '1 image removed.',
			    ] );
		    } else {
			    $this->rerp->send_json( [
				    'status'  => 'error',
				    'message' => 'Unable to remove the image.',
			    ] );
		    }
		    
	    } elseif ( $this->input->is_ajax_request() ) {
		    $this->rerp->send_json( [ 'status' => 'error', 'message' => validation_errors() ] );
	    } else {
		    redirect( $_SERVER['HTTP_REFERER'] );
	    }
    }
	
	public function remove_additional_image() {
		
		if( ! $this->input->is_ajax_request() ) {
			redirect( $_SERVER['HTTP_REFERER'] );
		}
		
	    $this->rerp->checkPermissions();
	    $this->load->helper('security');
	    $this->form_validation->set_rules( 'id', lang( 'product_id' ), 'required|numeric' );
	    $this->form_validation->set_rules( 'image', lang( 'image_id' ), 'required|numeric' );
	    
	    $image_id = $this->input->get( 'image_id' );
	    if ( $this->form_validation->run() === true ) {
	        $this->rerp->send_json();
	    } elseif ( $this->input->is_ajax_request() ) {
		    $this->rerp->send_json( [ 'status' => 'error', 'message' => validation_errors() ] );
	    } else {
	    	redirect( $_SERVER['HTTP_REFERER'] );
	    }
    }

    public function edit_adjustment($id)
    {
        $this->rerp->checkPermissions('adjustments', true);
        $adjustment = $this->products_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->rerp->md();
        }
        $this->form_validation->set_rules('warehouse', lang('warehouse'), 'required');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->rerp->fld($this->input->post('date'));
            } else {
                $date = $adjustment->date;
            }

            $reference_no = $this->input->post('reference_no');
            $warehouse_id = $this->input->post('warehouse');
            $note         = $this->rerp->clear_tags($this->input->post('note'));

            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $product_id = $_POST['product_id'][$r];
                $type       = $_POST['type'][$r];
                $quantity   = $_POST['quantity'][$r];
                $serial     = $_POST['serial'][$r];
                $variant    = isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : null;

                if (!$this->Settings->overselling && $type == 'subtraction') {
                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER['HTTP_REFERER']);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $products[] = [
                    'product_id'   => $product_id,
                    'type'         => $type,
                    'quantity'     => $quantity,
                    'warehouse_id' => $warehouse_id,
                    'option_id'    => $variant,
                    'serial_no'    => $serial,
                ];
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang('products'), 'required');
            } else {
                krsort($products);
            }

            $data = [
                'date'         => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
            ];

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $this->upload->initialize( [
	                'upload_path'   => $this->digital_upload_path,
	                'allowed_types' => $this->digital_file_types,
	                'max_size'      => $this->allowed_file_size,
	                'overwrite'     => false,
	                'encrypt_name'  => true,
                ] );
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo              = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->rerp->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->updateAdjustment($id, $data, $products)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang('quantity_adjusted'));
            admin_redirect('products/quantity_adjustments');
        } else {
            $inv_items = $this->products_model->getAdjustmentItems($id);
            // krsort($inv_items);
            foreach ($inv_items as $item) {
                $c           = sha1(uniqid(mt_rand(), true));
                $product     = $this->site->getProductByID($item->product_id);
                $row         = json_decode('{}');
                $row->id     = $item->product_id;
                $row->code   = $product->code;
                $row->name   = $product->name;
                $row->qty    = $item->quantity;
                $row->type   = $item->type;
                $options     = $this->products_model->getProductOptions($product->id);
                $row->option = $item->option_id ? $item->option_id : 0;
                $row->serial = $item->serial_no ? $item->serial_no : '';
                $ri          = $this->Settings->item_addition ? $product->id : $c;

                $pr[$ri] = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'        => $row, 'options' => $options, ];
                $c++;
            }

            $this->data['adjustment']       = $adjustment;
            $this->data['adjustment_items'] = json_encode($pr);
            $this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses']       = $this->site->getAllWarehouses();
            $bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('edit_adjustment')]];
            $meta                           = ['page_title' => lang('edit_adjustment'), 'bc' => $bc];
            $this->page_construct('products/edit_adjustment', $meta, $this->data);
        }
    }

    public function finalize_count($id)
    {
        $this->rerp->checkPermissions('stock_count');
        $stock_count = $this->products_model->getStouckCountByID($id);
        if (!$stock_count || $stock_count->finalized) {
            $this->session->set_flashdata('error', lang('stock_count_finalized'));
            admin_redirect('products/stock_counts');
        }

        $this->form_validation->set_rules('count_id', lang('count_stock'), 'required');

        if ($this->form_validation->run() == true) {
            if ($_FILES['csv_file']['size'] > 0) {
                $note = $this->rerp->clear_tags($this->input->post('note'));
                $data = [
                    'updated_by' => $this->session->userdata('user_id'),
                    'updated_at' => date('Y-m-d H:s:i'),
                    'note'       => $note,
                ];

                $this->load->library('upload');
                
                $this->upload->initialize( [
	                'upload_path'   => $this->digital_upload_path,
	                'allowed_types' => 'csv',
	                'max_size'      => $this->allowed_file_size,
	                'overwrite'     => false,
	                'encrypt_name'  => true,
                ] );
                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys   = ['product_code', 'product_name', 'product_variant', 'expected', 'counted'];
                $final  = [];
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                // $this->rerp->print_arrays($final);
                $rw          = 2;
                $differences = 0;
                $matches     = 0;
                foreach ($final as $pr) {
                    if ($product = $this->products_model->getProductByCode(trim($pr['product_code']))) {
                        $pr['counted'] = !empty($pr['counted']) ? $pr['counted'] : 0;
                        if ($pr['expected'] == $pr['counted']) {
                            $matches++;
                        } else {
                            $pr['stock_count_id']     = $id;
                            $pr['product_id']         = $product->id;
                            $pr['cost']               = $product->cost;
                            $pr['product_variant_id'] = empty($pr['product_variant']) ? null : $this->products_model->getProductVariantID($pr['product_id'], $pr['product_variant']);
                            $products[]               = $pr;
                            $differences++;
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['product_code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        admin_redirect('products/finalize_count/' . $id);
                    }
                    $rw++;
                }

                $data['final_file']  = $csv;
                $data['differences'] = $differences;
                $data['matches']     = $matches;
                $data['missing']     = $stock_count->rows - ($rw - 2);
                $data['finalized']   = 1;
            }

            // $this->rerp->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->finalizeStockCount($id, $data, $products)) {
            $this->session->set_flashdata('message', lang('stock_count_finalized'));
            admin_redirect('products/stock_counts');
        } else {
            $this->data['error']       = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['stock_count'] = $stock_count;
            $this->data['warehouse']   = $this->site->getWarehouseByID($stock_count->warehouse_id);
            $bc                        = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => admin_url('products/stock_counts'), 'page' => lang('stock_counts')], ['link' => '#', 'page' => lang('finalize_count')]];
            $meta                      = ['page_title' => lang('finalize_count'), 'bc' => $bc];
            $this->page_construct('products/finalize_count', $meta, $this->data);
        }
    }

    public function get_suggestions()
    {
        $term = $this->input->get('term', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }
        $term = addslashes($term);
        $rows = $this->products_model->getProductsForPrinting($term);
        if ($rows) {
            foreach ($rows as $row) {
                $variants = $this->products_model->getProductOptions($row->id);
                $pr[]     = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1, 'variants' => $variants];
            }
            $this->rerp->send_json($pr);
        } else {
            $this->rerp->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
    }

    public function getadjustments($warehouse_id = null)
    {
        $this->rerp->checkPermissions('adjustments');

        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('delete_adjustment') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('products/delete_adjustment/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('adjustments')}.id as id, date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, attachment")
            ->from('adjustments')
            ->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left')
            ->join('users', 'users.id=adjustments.created_by', 'left')
            ->group_by('adjustments.id');
        if ($warehouse_id) {
            $this->datatables->where('adjustments.warehouse_id', $warehouse_id);
        }
        $this->datatables->add_column('Actions', "<div class='text-center'><a href='" . admin_url('products/edit_adjustment/$1') . "' class='tip' title='" . lang('edit_adjustment') . "'><i class='fa fa-edit'></i></a> " . $delete_link . '</div>', 'id');

        echo $this->datatables->generate();
    }

    public function getCounts($warehouse_id = null)
    {
        $this->rerp->checkPermissions('stock_count', true);

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }
        $detail_link = anchor('admin/products/view_count/$1', '<label class="label label-primary pointer">' . lang('details') . '</label>', 'class="tip" title="' . lang('details') . '" data-toggle="modal" data-target="#myModal"');

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('stock_counts')}.id as id, date, reference_no, {$this->db->dbprefix('warehouses')}.name as wh_name, type, brand_names, category_names, initial_file, final_file")
            ->from('stock_counts')
            ->join('warehouses', 'warehouses.id=stock_counts.warehouse_id', 'left');
        if ($warehouse_id) {
            $this->datatables->where('warehouse_id', $warehouse_id);
        }

        $this->datatables->add_column('Actions', '<div class="text-center">' . $detail_link . '</div>', 'id');
        echo $this->datatables->generate();
    }

    public function getProducts($warehouse_id = null)
    {
        $this->rerp->checkPermissions('index', true);
        $supplier = $this->input->get('supplier') ? $this->input->get('supplier') : null;

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }
        $detail_link = anchor('admin/products/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('product_details'));
        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('delete_product') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete1' id='a__$1' href='" . admin_url('products/delete/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_product') . '</a>';
        $single_barcode = anchor('admin/products/print_barcodes/$1', '<i class="fa fa-print"></i> ' . lang('print_barcode_label'));
        // $single_label = anchor_popup('products/single_label/$1/' . ($warehouse_id ? $warehouse_id : ''), '<i class="fa fa-print"></i> ' . lang('print_label'), $this->popup_attributes);
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li><a href="' . admin_url('products/add/$1') . '"><i class="fa fa-plus-square"></i> ' . lang('duplicate_product') . '</a></li>
            <li><a href="' . admin_url('products/edit/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit_product') . '</a></li>';
        if ($warehouse_id) {
            $action .= '<li><a href="' . admin_url('products/set_rack/$1/' . $warehouse_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-bars"></i> '
                . lang('set_rack') . '</a></li>';
        }
        $action .= '<li><a href="' . base_url() . 'assets/uploads/$2" data-type="image" data-toggle="lightbox"><i class="fa fa-file-photo-o"></i> '
            . lang('view_image') . '</a></li>
            <li>' . $single_barcode . '</li>
            <li class="divider"></li>
            <li>' . $delete_link . '</li>
            </ul>
        </div></div>';
        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
            ->select($this->db->dbprefix('products') . ".id as productid, {$this->db->dbprefix('products')}.image as image, {$this->db->dbprefix('products')}.code as code, {$this->db->dbprefix('products')}.name as name, {$this->db->dbprefix('brands')}.name as brand, {$this->db->dbprefix('categories')}.name as cname, cost as cost, price as price, COALESCE(wp.quantity, 0) as quantity, {$this->db->dbprefix('units')}.code as unit, wp.rack as rack, alert_quantity", false)
            ->from('products');
            if ($this->Settings->display_all_products) {
                $this->datatables->join("( SELECT product_id, quantity, rack from {$this->db->dbprefix('warehouses_products')} WHERE warehouse_id = {$warehouse_id}) wp", 'products.id=wp.product_id', 'left');
            } else {
                $this->datatables->join('warehouses_products wp', 'products.id=wp.product_id', 'left')
                ->where('wp.warehouse_id', $warehouse_id)
                ->where('wp.quantity !=', 0);
            }
            $this->datatables->join('categories', 'products.category_id=categories.id', 'left')
            ->join('units', 'products.unit=units.id', 'left')
            ->join('brands', 'products.brand=brands.id', 'left');
        // ->group_by("products.id");
        } else {
            $this->datatables
                ->select($this->db->dbprefix('products') . ".id as productid, {$this->db->dbprefix('products')}.image as image, {$this->db->dbprefix('products')}.code as code, {$this->db->dbprefix('products')}.name as name, {$this->db->dbprefix('brands')}.name as brand, {$this->db->dbprefix('categories')}.name as cname, cost as cost, price as price, COALESCE(quantity, 0) as quantity, {$this->db->dbprefix('units')}.code as unit, '' as rack, alert_quantity", false)
                ->from('products')
                ->join('categories', 'products.category_id=categories.id', 'left')
                ->join('units', 'products.unit=units.id', 'left')
                ->join('brands', 'products.brand=brands.id', 'left')
                ->group_by('products.id');
        }
        if (!$this->Owner && !$this->Admin) {
            if (!$this->session->userdata('show_cost')) {
                $this->datatables->unset_column('cost');
            }
            if (!$this->session->userdata('show_price')) {
                $this->datatables->unset_column('price');
            }
        }
        if ($supplier) {
            $this->datatables->where('supplier1', $supplier)
            ->or_where('supplier2', $supplier)
            ->or_where('supplier3', $supplier)
            ->or_where('supplier4', $supplier)
            ->or_where('supplier5', $supplier);
        }
        $this->datatables->add_column('Actions', $action, 'productid, image, code, name');
        echo $this->datatables->generate();
    }

    public function getSubCategories($category_id = null)
    {
        if ($rows = $this->products_model->getSubCategories($category_id)) {
            $data = json_encode($rows);
        } else {
            $data = false;
        }
        echo $data;
    }

    public function getSubUnits($unit_id)
    {
        // $unit = $this->site->getUnitByID($unit_id);
        // if ($units = $this->site->getUnitsByBUID($unit_id)) {
        //     array_push($units, $unit);
        // } else {
        //     $units = array($unit);
        // }
        $units = $this->site->getUnitsByBUID($unit_id);
        $this->rerp->send_json($units);
    }

    /* ---------------------------------------------------------------- */

    public function import_csv()
    {
        $this->rerp->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (isset($_FILES['userfile'])) {
                $this->load->library('upload');
	            
                $this->upload->initialize( [
	                'upload_path'   => $this->digital_upload_path,
	                'allowed_types' => 'csv',
	                'max_size'      => $this->allowed_file_size,
	                'overwrite'     => true,
	                'encrypt_name'  => true,
	                'max_filename'  => 25,
                ] );

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('products/import_csv');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles  = array_shift($arrResult);
                $updated = 0;
                $items   = [];
                foreach ($arrResult as $key => $value) {
                    $supplier_name = isset($value[24]) ? trim($value[24]) : '';
                    $supplier      = $supplier_name ? $this->products_model->getSupplierByName($supplier_name) : false;

                    $item = [
                        'name'              => isset($value[0]) ? trim($value[0]) : '',
                        'code'              => isset($value[1]) ? trim($value[1]) : '',
                        'barcode_symbology' => isset($value[2]) ? mb_strtolower(trim($value[2]), 'UTF-8') : '',
                        'brand'             => isset($value[3]) ? trim($value[3]) : '',
                        'category_code'     => isset($value[4]) ? trim($value[4]) : '',
                        'unit'              => isset($value[5]) ? trim($value[5]) : '',
                        'sale_unit'         => isset($value[6]) ? trim($value[6]) : '',
                        'purchase_unit'     => isset($value[7]) ? trim($value[7]) : '',
                        'cost'              => isset($value[8]) ? trim($value[8]) : '',
                        'price'             => isset($value[9]) ? trim($value[9]) : '',
                        'alert_quantity'    => isset($value[10]) ? trim($value[10]) : '',
                        'tax_rate'          => isset($value[11]) ? trim($value[11]) : '',
                        'tax_method'        => isset($value[12]) ? (trim($value[12]) == 'exclusive' ? 1 : 0) : '',
                        'image'             => isset($value[13]) ? trim($value[13]) : '',
                        'subcategory_code'  => isset($value[14]) ? trim($value[14]) : '',
                        'variants'          => isset($value[15]) ? trim($value[15]) : '',
                        'cf1'               => isset($value[16]) ? trim($value[16]) : '',
                        'cf2'               => isset($value[17]) ? trim($value[17]) : '',
                        'cf3'               => isset($value[18]) ? trim($value[18]) : '',
                        'cf4'               => isset($value[19]) ? trim($value[19]) : '',
                        'cf5'               => isset($value[20]) ? trim($value[20]) : '',
                        'cf6'               => isset($value[21]) ? trim($value[21]) : '',
                        'hsn_code'          => isset($value[22]) ? trim($value[22]) : '',
                        'second_name'       => isset($value[23]) ? trim($value[23]) : '',
                        'supplier1'         => $supplier ? $supplier->id : null,
                        'supplier1_part_no' => isset($value[25]) ? trim($value[25]) : '',
                        'supplier1price'    => isset($value[26]) ? trim($value[26]) : '',
                        'slug'              => $this->rerp->slug($value[0]),
                    ];

                    if ($catd = $this->products_model->getCategoryByCode($item['category_code'])) {
                        $tax_details   = $this->products_model->getTaxRateByName($item['tax_rate']);
                        $prsubcat      = $this->products_model->getCategoryByCode($item['subcategory_code']);
                        $brand         = $this->products_model->getBrandByName($item['brand']);
                        $unit          = $this->products_model->getUnitByCode($item['unit']);
                        $base_unit     = $unit ? $unit->id : null;
                        $sale_unit     = $base_unit;
                        $purcahse_unit = $base_unit;
                        if ($base_unit) {
                            $units = $this->site->getUnitsByBUID($base_unit);
                            foreach ($units as $u) {
                                if ($u->code == $item['sale_unit']) {
                                    $sale_unit = $u->id;
                                }
                                if ($u->code == $item['purchase_unit']) {
                                    $purcahse_unit = $u->id;
                                }
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('check_unit') . ' (' . $item['unit'] . '). ' . lang('unit_code_x_exist') . ' ' . lang('line_no') . ' ' . ($key + 1));
                            admin_redirect('products/import_csv');
                        }

                        unset($item['category_code'], $item['subcategory_code']);
                        $item['unit']           = $base_unit;
                        $item['sale_unit']      = $sale_unit;
                        $item['category_id']    = $catd->id;
                        $item['purchase_unit']  = $purcahse_unit;
                        $item['brand']          = $brand ? $brand->id : null;
                        $item['tax_rate']       = $tax_details ? $tax_details->id : null;
                        $item['subcategory_id'] = $prsubcat ? $prsubcat->id : null;

                        if ($product = $this->products_model->getProductByCode($item['code'])) {
                            if ($product->type == 'standard') {
	                            $variants = [];
                                if ($item['variants']) {
                                    $vs = explode('|', $item['variants']);
                                    foreach ($vs as $v) {
                                        if (!empty(trim($v))) {
                                            $variants[] = ['product_id' => $product->id, 'name' => trim($v)];
                                        }
                                    }
                                }
                                unset($item['variants']);
                                if ($this->products_model->updateProduct($product->id, $item, null, null, null, null, $variants)) {
                                    $updated++;
                                }
                            }
                            $item = false;
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('check_category_code') . ' (' . $item['category_code'] . '). ' . lang('category_code_x_exist') . ' ' . lang('line_no') . ' ' . ($key + 1));
                        admin_redirect('products/import_csv');
                    }

                    if ($item) {
                        $items[] = $item;
                    }
                }
            }

            // $this->rerp->print_arrays($items);
        }

        if ($this->form_validation->run() == true && !empty($items)) {
            if ($this->products_model->add_products($items)) {
                $updated = $updated ? '<p>' . sprintf(lang('products_updated'), $updated) . '</p>' : '';
                $this->session->set_flashdata('message', sprintf(lang('products_added'), count($items)) . $updated);
                admin_redirect('products');
            }
        } else {
            if (isset($items) && empty($items)) {
                if ($updated) {
                    $this->session->set_flashdata('message', sprintf(lang('products_updated'), $updated));
                    admin_redirect('products');
                } else {
                    $this->session->set_flashdata('warning', lang('csv_issue'));
                }
                admin_redirect('products/import_csv');
            }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];

            $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('import_products_by_csv')]];
            $meta = ['page_title' => lang('import_products_by_csv'), 'bc' => $bc];
            $this->page_construct('products/import_csv', $meta, $this->data);
        }
    }

    public function index($warehouse_id = null)
    {
        $this->rerp->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses']   = null;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $this->data['supplier'] = $this->input->get('supplier') ? $this->site->getCompanyByID($this->input->get('supplier')) : null;
        $bc                     = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('products')]];
        $meta                   = ['page_title' => lang('products'), 'bc' => $bc];
        $this->page_construct('products/index', $meta, $this->data);
    }

    /* --------------------------------------------------------------------------------------------- */

    public function modal_view($id = null)
    {
        $this->rerp->checkPermissions('index', true);

        $pr_details = $this->site->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            $this->rerp->md();
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->products_model->getProductComboItems($id);
        }
        $this->data['product']     = $pr_details;
        $this->data['unit']        = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand']       = $this->site->getBrandByID($pr_details->brand);
        $this->data['images']      = $this->products_model->getProductPhotos($id);
        $this->data['category']    = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : null;
        $this->data['tax_rate']    = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : null;
        $this->data['warehouses']  = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['options']     = $this->products_model->getProductOptionsWithWH($id);
        $this->data['variants']    = $this->products_model->getProductOptions($id);

        $this->load->view($this->theme . 'products/modal_view', $this->data);
    }

    public function pdf($id = null, $view = null)
    {
        $this->rerp->checkPermissions('index');

        $pr_details = $this->products_model->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->products_model->getProductComboItems($id);
        }
        $this->data['product']          = $pr_details;
        $this->data['unit']             = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand']            = $this->site->getBrandByID($pr_details->brand);
        $this->data['images']           = $this->products_model->getProductPhotos($id);
        $this->data['category']         = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory']      = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : null;
        $this->data['tax_rate']         = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : null;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses']       = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['options']          = $this->products_model->getProductOptionsWithWH($id);
        $this->data['variants']         = $this->products_model->getProductOptions($id);

        $name = $pr_details->code . '_' . str_replace('/', '_', $pr_details->name) . '.pdf';
        if ($view) {
            $this->load->view($this->theme . 'products/pdf', $this->data);
        } else {
            $html = $this->load->view($this->theme . 'products/pdf', $this->data, true);
            if (!$this->Settings->barcode_img) {
                $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
            }
            $this->rerp->generate_pdf($html, $name);
        }
    }

    public function print_barcodes($product_id = null)
    {
        $this->rerp->checkPermissions('barcode', true);

        $this->form_validation->set_rules('style', lang('style'), 'required');

        if ($this->form_validation->run() == true) {
            $style      = $this->input->post('style');
            $bci_size   = ($style == 10 || $style == 12 ? 50 : ($style == 14 || $style == 18 ? 30 : 20));
            $currencies = $this->site->getAllCurrencies();
            $s          = isset($_POST['product']) ? sizeof($_POST['product']) : 0;
            if ($s < 1) {
                $this->session->set_flashdata('error', lang('no_product_selected'));
                admin_redirect('products/print_barcodes');
            }
            for ($m = 0; $m < $s; $m++) {
                $pid            = $_POST['product'][$m];
                $quantity       = $_POST['quantity'][$m];
                $product        = $this->products_model->getProductWithCategory($pid);
                $product->price = $this->input->post('check_promo') ? ($product->promotion ? $product->promo_price : $product->price) : $product->price;
	            if ( $this->input->post( 'unit' ) ) {
		            $unit = $this->products_model->getUnitById( $product->unit );
		            if ( $unit ) {
			            $unit = $unit->name;
		            }
	            } else {
		            $unit = false;
	            }
                if ($variants = $this->products_model->getProductOptions($pid)) {
                    foreach ($variants as $option) {
                        if ($this->input->post('vt_' . $product->id . '_' . $option->id)) {
                        	
                            $barcodes[] = [
                                'site'    => $this->input->post('site_name') ? $this->Settings->site_name : false,
                                'name'    => $this->input->post('product_name') ? $product->name . ' - ' . $option->name : false,
                                'image'   => $this->input->post('product_image') ? $product->image : false,
                                'barcode' => $product->code . $this->Settings->barcode_separator . $option->id,
                                'bcs'     => 'code128',
                                'bcis'    => $bci_size,
                                // 'barcode' => $this->product_barcode($product->code . $this->Settings->barcode_separator . $option->id, 'code128', $bci_size),
                                'price'      => $this->input->post('price') ? $this->rerp->formatMoney($option->price != 0 ? ($product->price + $option->price) : $product->price, 'none') : false,
                                'rprice'     => $this->input->post('price') ? ($option->price != 0 ? ($product->price + $option->price) : $product->price) : false,
                                'unit'       => $unit,
                                'category'   => $this->input->post('category') ? $product->category : false,
                                'currencies' => $this->input->post('currencies'),
                                'variants'   => $this->input->post('variants') ? $variants : false,
                                'quantity'   => $quantity,
                            ];
                        }
                    }
                } else {
                	
                    $barcodes[] = [
                        'site'  => $this->input->post('site_name') ? $this->Settings->site_name : false,
                        'name'  => $this->input->post('product_name') ? $product->name : false,
                        'image' => $this->input->post('product_image') ? $product->image : false,
                        // 'barcode' => $this->product_barcode($product->code, $product->barcode_symbology, $bci_size),
                        'barcode'    => $product->code,
                        'bcs'        => $product->barcode_symbology,
                        'bcis'       => $bci_size,
                        'price'      => $this->input->post('price') ? $this->rerp->formatMoney($product->price, 'none') : false,
                        'rprice'     => $this->input->post('price') ? $product->price : false,
                        'unit'       => $unit,
                        'category'   => $this->input->post('category') ? $product->category : false,
                        'currencies' => $this->input->post('currencies'),
                        'variants'   => false,
                        'quantity'   => $quantity,
                    ];
                }
            }
            $this->data['barcodes']   = $barcodes;
            $this->data['currencies'] = $currencies;
            $this->data['style']      = $style;
            $this->data['items']      = false;
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('print_barcodes')]];
            $meta                     = ['page_title' => lang('print_barcodes'), 'bc' => $bc];
            $this->page_construct('products/print_barcodes', $meta, $this->data);
        } else {
            if ($this->input->get('purchase') || $this->input->get('transfer')) {
                if ($this->input->get('purchase')) {
                    $purchase_id = $this->input->get('purchase', true);
                    $items       = $this->products_model->getPurchaseItems($purchase_id);
                } elseif ($this->input->get('transfer')) {
                    $transfer_id = $this->input->get('transfer', true);
                    $items       = $this->products_model->getTransferItems($transfer_id);
                }
                if ($items) {
                    foreach ($items as $item) {
                        if ($row = $this->products_model->getProductByID($item->product_id)) {
                            $selected_variants = false;
                            if ($variants = $this->products_model->getProductOptions($row->id)) {
                                foreach ($variants as $variant) {
                                    $selected_variants[$variant->id] = isset($pr[$row->id]['selected_variants'][$variant->id]) && !empty($pr[$row->id]['selected_variants'][$variant->id]) ? 1 : ($variant->id == $item->option_id ? 1 : 0);
                                }
                            }
                            $pr[$row->id] = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $item->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants];
                        }
                    }
                    $this->data['message'] = lang('products_added_to_list');
                }
            }

            if ($product_id) {
                if ($row = $this->site->getProductByID($product_id)) {
                    $selected_variants = false;
                    if ($variants = $this->products_model->getProductOptions($row->id)) {
                        foreach ($variants as $variant) {
                            $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                        }
                    }
                    $pr[$row->id] = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants];

                    $this->data['message'] = lang('product_added_to_list');
                }
            }

            if ($this->input->get('category')) {
                if ($products = $this->products_model->getCategoryProducts($this->input->get('category'))) {
                    foreach ($products as $row) {
                        $selected_variants = false;
                        if ($variants = $this->products_model->getProductOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants];
                    }
                    $this->data['message'] = lang('products_added_to_list');
                } else {
                    $pr = [];
                    $this->session->set_flashdata('error', lang('no_product_found'));
                }
            }

            if ($this->input->get('subcategory')) {
                if ($products = $this->products_model->getSubCategoryProducts($this->input->get('subcategory'))) {
                    foreach ($products as $row) {
                        $selected_variants = false;
                        if ($variants = $this->products_model->getProductOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants];
                    }
                    $this->data['message'] = lang('products_added_to_list');
                } else {
                    $pr = [];
                    $this->session->set_flashdata('error', lang('no_product_found'));
                }
            }

            $this->data['items'] = isset($pr) ? json_encode($pr) : false;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('print_barcodes')]];
            $meta                = ['page_title' => lang('print_barcodes'), 'bc' => $bc];
            $this->page_construct('products/print_barcodes', $meta, $this->data);
        }
    }

    public function product_actions($wh = null)
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'sync_quantity') {
                    foreach ($_POST['val'] as $id) {
                        $this->site->syncQuantity(null, null, null, $id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line('products_quantity_sync'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'delete') {
                    $this->rerp->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->products_model->deleteProduct($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line('products_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                } elseif ($this->input->post('form_action') == 'labels') {
                    foreach ($_POST['val'] as $id) {
                        $row               = $this->products_model->getProductByID($id);
                        $selected_variants = false;
                        if ($variants = $this->products_model->getProductOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants];
                    }

                    $this->data['items'] = isset($pr) ? json_encode($pr) : false;
                    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                    $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('print_barcodes')]];
                    $meta                = ['page_title' => lang('print_barcodes'), 'bc' => $bc];
                    $this->page_construct('products/print_barcodes', $meta, $this->data);
                } elseif ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Products');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('barcode_symbology'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('brand'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('category_code'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('sale') . ' ' . lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('H1', lang('purchase') . ' ' . lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('I1', lang('cost'));
                    $this->excel->getActiveSheet()->SetCellValue('J1', lang('price'));
                    $this->excel->getActiveSheet()->SetCellValue('K1', lang('alert_quantity'));
                    $this->excel->getActiveSheet()->SetCellValue('L1', lang('tax_rate'));
                    $this->excel->getActiveSheet()->SetCellValue('M1', lang('tax_method'));
                    $this->excel->getActiveSheet()->SetCellValue('N1', lang('image'));
                    $this->excel->getActiveSheet()->SetCellValue('O1', lang('subcategory_code'));
                    $this->excel->getActiveSheet()->SetCellValue('P1', lang('product_variants'));
                    $this->excel->getActiveSheet()->SetCellValue('Q1', lang('pcf1'));
                    $this->excel->getActiveSheet()->SetCellValue('R1', lang('pcf2'));
                    $this->excel->getActiveSheet()->SetCellValue('S1', lang('pcf3'));
                    $this->excel->getActiveSheet()->SetCellValue('T1', lang('pcf4'));
                    $this->excel->getActiveSheet()->SetCellValue('U1', lang('pcf5'));
                    $this->excel->getActiveSheet()->SetCellValue('V1', lang('pcf6'));
                    $this->excel->getActiveSheet()->SetCellValue('W1', lang('hsn_code'));
                    $this->excel->getActiveSheet()->SetCellValue('X1', lang('second_name'));
                    $this->excel->getActiveSheet()->SetCellValue('Y1', lang('supplier_name'));
                    $this->excel->getActiveSheet()->SetCellValue('Z1', lang('supplier_part_no'));
                    $this->excel->getActiveSheet()->SetCellValue('AA1', lang('supplier_price'));
                    $this->excel->getActiveSheet()->SetCellValue('AB1', lang('quantity'));
                    $this->excel->getActiveSheet()->SetCellValue('AC1', lang('details'));
                    $this->excel->getActiveSheet()->SetCellValue('AD1', lang('product_details'));
	                $this->excel->getActiveSheet()->SetCellValue('AE1', lang('cash_back_amount'));
	                $this->excel->getActiveSheet()->SetCellValue('AF1', lang('cash_back_percentage'));
	                $this->excel->getActiveSheet()->SetCellValue('AG1', lang('cash_back_start_date'));
	                $this->excel->getActiveSheet()->SetCellValue('AH1', lang('cash_back_end_date'));

	                $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $product   = $this->products_model->getProductDetail($id);
                        $brand     = $this->site->getBrandByID($product->brand);
                        $base_unit = $sale_unit = $purchase_unit = '';
                        if ($units = $this->site->getUnitsByBUID($product->unit)) {
                            foreach ($units as $u) {
                                if ($u->id == $product->unit) {
                                    $base_unit = $u->code;
                                }
                                if ($u->id == $product->sale_unit) {
                                    $sale_unit = $u->code;
                                }
                                if ($u->id == $product->purchase_unit) {
                                    $purchase_unit = $u->code;
                                }
                            }
                        }
                        $variants         = $this->products_model->getProductOptions($id);
                        $product_variants = '';
                        if ($variants) {
                            $i = 1;
                            $v = count($variants);
                            foreach ($variants as $variant) {
                                $product_variants .= trim($variant->name) . ($i != $v ? '|' : '');
                                $i++;
                            }
                        }
                        $quantity = $product->quantity;
                        if ($wh) {
                            if ($wh_qty = $this->products_model->getProductQuantity($id, $wh)) {
                                $quantity = $wh_qty['quantity'];
                            } else {
                                $quantity = 0;
                            }
                        }
                        $supplier = false;
                        if ($product->supplier1) {
                            $supplier = $this->site->getCompanyByID($product->supplier1);
                        }
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $product->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $product->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $product->barcode_symbology);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, ($brand ? $brand->name : ''));
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $product->category_code);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $base_unit);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sale_unit);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $purchase_unit);
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_cost')) {
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $product->cost);
                        }
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_price')) {
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $product->price);
                        }
                        $this->excel->getActiveSheet()->SetCellValue('K' . $row, $product->alert_quantity);
                        $this->excel->getActiveSheet()->SetCellValue('L' . $row, $product->tax_rate_name);
                        $this->excel->getActiveSheet()->SetCellValue('M' . $row, $product->tax_method ? lang('exclusive') : lang('inclusive'));
                        $this->excel->getActiveSheet()->SetCellValue('N' . $row, $product->image);
                        $this->excel->getActiveSheet()->SetCellValue('O' . $row, $product->subcategory_code);
                        $this->excel->getActiveSheet()->SetCellValue('P' . $row, $product_variants);
                        $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $product->cf1);
                        $this->excel->getActiveSheet()->SetCellValue('R' . $row, $product->cf2);
                        $this->excel->getActiveSheet()->SetCellValue('S' . $row, $product->cf3);
                        $this->excel->getActiveSheet()->SetCellValue('T' . $row, $product->cf4);
                        $this->excel->getActiveSheet()->SetCellValue('U' . $row, $product->cf5);
                        $this->excel->getActiveSheet()->SetCellValue('V' . $row, $product->cf6);
                        $this->excel->getActiveSheet()->SetCellValue('W' . $row, $product->hsn_code);
                        $this->excel->getActiveSheet()->SetCellValue('X' . $row, $product->second_name);
                        $this->excel->getActiveSheet()->SetCellValue('Y' . $row, $supplier ? $supplier->name : '');
                        $this->excel->getActiveSheet()->SetCellValue('Z' . $row, $supplier ? $product->supplier1_part_no : '');
                        $this->excel->getActiveSheet()->SetCellValue('AA' . $row, $supplier ? $product->supplier1price : '');
                        $this->excel->getActiveSheet()->SetCellValue('AB' . $row, $quantity);
                        $this->excel->getActiveSheet()->SetCellValue('AC' . $row, $product->details);
                        $this->excel->getActiveSheet()->SetCellValue('AD' . $row, $product->product_details);
	                    $this->excel->getActiveSheet()->SetCellValue('AE' . $row, $product->cash_back_amount);
	                    $this->excel->getActiveSheet()->SetCellValue('AF' . $row, $product->cash_back_percentage);
	                    $this->excel->getActiveSheet()->SetCellValue('AG' . $row, $product->cash_back_start_date);
	                    $this->excel->getActiveSheet()->SetCellValue('AH' . $row, $product->cash_back_end_date);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(40);
                    $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('AC')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('AD')->setWidth(40);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical('center');
                    $filename = 'products_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line('no_product_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER'] ?? 'admin/products');
        }
    }

    public function qa_suggestions()
    {
        $term = $this->input->get('term', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed  = $this->rerp->analyze_term($term);
        $sr        = $analyzed['term'];
        $option_id = $analyzed['option_id'];
        $sr        = addslashes($sr);

        $rows = $this->products_model->getQASuggestions($sr);
        if ($rows) {
            foreach ($rows as $row) {
                $row->qty    = 1;
                $options     = $this->products_model->getProductOptions($row->id);
                $row->option = $option_id;
                $row->serial = '';
                $c           = sha1(uniqid(mt_rand(), true));
                $pr[]        = ['id' => $c, 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')',
                    'row'            => $row, 'options' => $options, ];
            }
            $this->rerp->send_json($pr);
        } else {
            $this->rerp->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
    }

    /* ----------------------------------------------------------------------------- */

    public function quantity_adjustments($warehouse_id = null)
    {
        $this->rerp->checkPermissions('adjustments');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse']  = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('quantity_adjustments')]];
        $meta                = ['page_title' => lang('quantity_adjustments'), 'bc' => $bc];
        $this->page_construct('products/quantity_adjustments', $meta, $this->data);
    }

    public function set_rack($product_id = null, $warehouse_id = null)
    {
        $this->rerp->checkPermissions('edit', true);

        $this->form_validation->set_rules('rack', lang('rack_location'), 'trim|required');

        if ($this->form_validation->run() == true) {
            $data = ['rack'    => $this->input->post('rack'),
                'product_id'   => $product_id,
                'warehouse_id' => $warehouse_id,
            ];
        } elseif ($this->input->post('set_rack')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('products/' . $warehouse_id);
        }

        if ($this->form_validation->run() == true && $this->products_model->setRack($data)) {
            $this->session->set_flashdata('message', lang('rack_set'));
            admin_redirect('products/' . $warehouse_id);
        } else {
            $this->data['error']        = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['product']      = $this->site->getProductByID($product_id);
            $wh_pr                      = $this->products_model->getProductQuantity($product_id, $warehouse_id);
            $this->data['rack']         = $wh_pr['rack'];
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'products/set_rack', $this->data);
        }
    }

    public function stock_counts($warehouse_id = null)
    {
        $this->rerp->checkPermissions('stock_count');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses']   = null;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => lang('stock_counts')]];
        $meta = ['page_title' => lang('stock_counts'), 'bc' => $bc];
        $this->page_construct('products/stock_counts', $meta, $this->data);
    }

    public function suggestions()
    {
        $term = $this->input->get('term', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }
        $term = addslashes($term);
        $rows = $this->products_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = ['id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1];
            }
            $this->rerp->send_json($pr);
        } else {
            $this->rerp->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
    }

    /* ------------------------------------------------------------------ */

    public function update_price()
    {
        $this->rerp->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
	        if ( DEMO ) {
                $this->session->set_flashdata('message', lang('disabled_in_demo'));
                admin_redirect('welcome');
            }
	
	        if ( isset( $_FILES['userfile'] ) ) {
                $this->load->library('upload');
                
                $this->upload->initialize( [
	                'upload_path'   => $this->digital_upload_path,
	                'allowed_types' => 'csv',
	                'max_size'      => $this->allowed_file_size,
	                'overwrite'     => true,
	                'encrypt_name'  => true,
	                'max_filename'  => 25,
                ] );
		
		        if ( ! $this->upload->do_upload() ) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('products');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen($this->digital_upload_path . $csv, 'r');
		        if ( $handle ) {
			        while ( ( $row = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
				        $arrResult[] = $row;
			        }
			        fclose( $handle );
		        }
                $titles = array_shift($arrResult);

                $keys = ['code', 'price'];

                $final = [];

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
		        foreach ( $final as $csv_pr ) {
			        if ( ! $this->products_model->getProductByCode( trim( $csv_pr['code'] ) ) ) {
                        $this->session->set_flashdata('message', lang('check_product_code') . ' (' . $csv_pr['code'] . '). ' . lang('code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        admin_redirect('products');
                    }
                    $rw++;
                }
            }
        }

        if ($this->form_validation->run() == true && !empty($final)) {
            $this->products_model->updatePrice($final);
            $this->session->set_flashdata('message', lang('price_updated'));
            admin_redirect('products');
        } else {
            $this->data['userfile'] = ['name' => 'userfile',
                'id'                          => 'userfile',
                'type'                        => 'text',
                'value'                       => $this->form_validation->set_value('userfile'),
            ];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'products/update_price', $this->data);
        }
    }

    public function view($id = null)
    {
        $this->rerp->checkPermissions('index');

        $pr_details = $this->products_model->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->products_model->getProductComboItems($id);
        }
        $this->data['product']          = $pr_details;
        $this->data['unit']             = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand']            = $this->site->getBrandByID($pr_details->brand);
        $this->data['images']           = $this->products_model->getProductPhotos($id);
        $this->data['category']         = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory']      = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : null;
        $this->data['tax_rate']         = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : null;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses']       = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['options']          = $this->products_model->getProductOptionsWithWH($id);
        $this->data['variants']         = $this->products_model->getProductOptions($id);
        $this->data['sold']             = $this->products_model->getSoldQty($id);
        $this->data['purchased']        = $this->products_model->getPurchasedQty($id);

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('products'), 'page' => lang('products')], ['link' => '#', 'page' => $pr_details->name]];
        $meta = ['page_title' => $pr_details->name, 'bc' => $bc];
        $this->page_construct('products/view', $meta, $this->data);
    }

    public function view_adjustment($id)
    {
        $this->rerp->checkPermissions('adjustments', true);

        $adjustment = $this->products_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->rerp->md();
        }

        $this->data['inv']        = $adjustment;
        $this->data['rows']       = $this->products_model->getAdjustmentItems($id);
        $this->data['created_by'] = $this->site->getUser($adjustment->created_by);
        $this->data['updated_by'] = $this->site->getUser($adjustment->updated_by);
        $this->data['warehouse']  = $this->site->getWarehouseByID($adjustment->warehouse_id);
        $this->load->view($this->theme . 'products/view_adjustment', $this->data);
    }

    public function view_count($id)
    {
        $this->rerp->checkPermissions('stock_count', true);
        $stock_count = $this->products_model->getStouckCountByID($id);
        if (!$stock_count->finalized) {
            $this->rerp->md('admin/products/finalize_count/' . $id);
        }

        $this->data['stock_count']       = $stock_count;
        $this->data['stock_count_items'] = $this->products_model->getStockCountItems($id);
        $this->data['warehouse']         = $this->site->getWarehouseByID($stock_count->warehouse_id);
        $this->data['adjustment']        = $this->products_model->getAdjustmentByCountID($id);
        $this->load->view($this->theme . 'products/view_count', $this->data);
    }
	
	
	/**
	 * @param int $product_id
	 *
	 * @return bool
	 */
	protected function save_all_meta( $product_id ) {
		$product_id = absint( $product_id );
		if ( ! $product_id ) {
			return false;
		}
		$fields = $this->themeInfos['product_custom']['fields'];
		foreach ( $fields as $field ) {
			if ( ! isset( $field['id'] ) ) {
				continue;
			}
			$id = xss_clean( $field['id'] );
			$meta = $this->input->post( $id, true );
			if ( is_array( $meta ) ) {
				$meta = ci_array_values_multi( $meta );
			}
			$this->products_model->updateProductMeta( $product_id, $id, $meta );
		}
		return true;
	}
}
