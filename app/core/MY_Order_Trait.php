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
trait MY_Order_Trait {
	
	/**
	 * Process Order Items & Prepare data.
	 * @param array $cart_contents
	 * @param Erp_Company|bool $customer
	 * @param Erp_Company|bool $biller
	 * @param bool $return_errors
	 *
	 * @return array
	 */
	public function prepare_order_data( $cart_contents, $customer = false, $biller = false, $return_errors = false ) {
		$product_tax = 0;
		$total       = 0;
		$gst_data    = [];
		$products    = [];
		$error       = [];
		$total_cgst  = $total_sgst  = $total_igst  = 0;
		foreach ( $cart_contents as $item ) {
			$item_option = null;
			if ( $product_details = $this->shop_model->getProductForCart( $item['product_id'] ) ) {
				
				$price = ( $this->loggedIn && isset( $product_details->special_price ) ? $product_details->special_price : $product_details->price );
				$price = $this->rerp->setCustomerGroupPrice( $price, $this->customer_group );
				$price = $this->rerp->isPromo( $product_details ) ? $product_details->promo_price : $price;
				if ( $item['option'] ) {
					if ( $product_variant = $this->shop_model->getProductVariantByID( $item['option'] ) ) {
						$item_option = $product_variant->id;
						$price       = $product_variant->price + $price;
					}
				}
				
				$item_net_price = $unit_price = $price;
				$item_quantity  = $item_unit_quantity  = $item['qty'];
				$pr_item_tax    = $item_tax    = 0;
				$tax            = '';
				
				if ( ! empty( $product_details->tax_rate ) ) {
					$tax_details = $this->site->getTaxRateByID( $product_details->tax_rate );
					$ctax        = $this->site->calculateTax( $product_details, $tax_details, $unit_price );
					$item_tax    = $ctax['amount'];
					$tax         = $ctax['tax'];
					if ($product_details->tax_method != 1) {
						$item_net_price = $unit_price - $item_tax;
					}
					$pr_item_tax = ( $item_tax * $item_unit_quantity );
					if ( $this->Settings->indian_gst ) {
						if ( $biller && $customer ) {
							$gst_data = $this->gst->calculteIndianGST(
								$pr_item_tax,
								( $biller->getState() == $customer->getState() ),
								$tax_details
							);
							if ( ! empty( $gst_data ) ) {
								$total_cgst += $gst_data['cgst'];
								$total_sgst += $gst_data['sgst'];
								$total_igst += $gst_data['igst'];
							}
						}
					}
				}
				
				$product_tax += $pr_item_tax;
				$subtotal = ( ( $item_net_price * $item_unit_quantity ) + $pr_item_tax );
				$unit = $this->site->getUnitByID( $product_details->unit );
				$product = [
					'product_id'        => $product_details->id,
					'product_code'      => $product_details->code,
					'product_name'      => $product_details->name,
					'product_type'      => $product_details->type,
					'option_id'         => $item_option,
					'net_unit_price'    => $item_net_price,
					'unit_price'        => $this->rerp->formatDecimal($item_net_price + $item_tax),
					'quantity'          => $item_quantity,
					'product_unit_id'   => $unit ? $unit->id : null,
					'product_unit_code' => $unit ? $unit->code : null,
					'unit_quantity'     => $item_unit_quantity,
					'warehouse_id'      => $this->shop_settings->warehouse,
					'item_tax'          => $this->rerp->formatDecimal( $pr_item_tax, 4 ),
					'tax_rate_id'       => $product_details->tax_rate,
					'tax'               => $tax,
					'discount'          => null,
					'item_discount'     => 0,
					'subtotal'          => $this->rerp->formatDecimal($subtotal),
					'serial_no'         => null,
					'real_unit_price'   => $price,
				];
				
				$products[] = ( $product + $gst_data );
				$total += $this->rerp->formatDecimal( ( $item_net_price * $item_unit_quantity ), 4 );
			} else {
				if ( $return_errors ) {
					$error[] = sprintf( lang( 'product_x_not_available' ), $item['name'] );
				} else {
					$this->session->set_flashdata('error', sprintf( lang( 'product_x_not_available' ), $item['name'] ) );
					redirect( $_SERVER['HTTP_REFERER'] ?? 'cart' );
				}
			}
		}
		return [
			'products'    => $products,
			'total'       => $total,
			'product_tax' => $product_tax,
			'total_cgst'  => $total_cgst,
			'total_sgst'  => $total_sgst,
			'total_igst'  => $total_igst,
			'errors'      => empty( $error ) ? false : $error,
		];
	}
	
	/**
	 * Process wallet pay.
	 *
	 * @param int $sale_id
	 * @param array $data
	 * @param bool $return_error
	 *
	 * @return bool|int|string false no balance, string error, true success, 1 success & full paid
	 */
	public function process_wallet_pay( $sale_id, $data, $return_error = false ) {
		$order_page = shop_url( 'orders/' . $sale_id . '/' . ( $this->loggedIn ? '' : $data['hash'] ) );
		// get wallet balance.
		// if wallet balance < $data['grand_total']
		// issue an wallet wdl request.
		$wallet = Erp_Wallet::get_user_wallet( $this->session->userdata( 'user_id' ) );
		if ( $wallet->getAmount() > 0 ) {
			$wdl = new Erp_Wallet_Withdraw();
			// calculation part of wallet percentage per cart amount
			$max_uses = absfloat( $this->shop_settings->wallet_percentage_cart );
			$amount   = $data['grand_total'];
			if ( $max_uses > 0 ) {
				$max_uses = $this->rerp->formatDecimal( ( ( $data['grand_total'] * $max_uses ) / 100 ), 4 );
				$amount   = ( $max_uses > $wallet->getAmount() )  ? $wallet->getAmount() : $max_uses;
			}
			
			$wdl->setAmount( $amount );
			$wdl->setPurchaseWithdrawal();
			$wdl->setUserId( $this->session->userdata( 'user_id' ) );
			$wdl->setRequestBy( $this->session->userdata( 'user_id' ) );
			$wdl->setStatus( 'applied' );
			$wdl->setApprovedBy( 0 );
			$wdl->setDescription(
				sprintf(
					'purchase ref/bl wdl pre-approved by system, invoice no %s (%s)',
					$sale_id,
					$data['reference_no']
				)
			);
			// if wdl success (insert into db) then add payment.
			if ( $wdl->save() ) {
				$payment = new Erp_Payment();
				$payment->setDate( date( 'Y-m-d H:i:s' ) );
				$payment->setSaleId( $sale_id );
				$payment->setReferenceNo( $data['reference_no'] );
				$payment->setAmount( $wdl->getAmount() );
				$payment->setCurrency( $this->Settings->default_currency );
				$payment->setPaidBy( 'wallet' );
				$payment->setTransactionId( $wdl->getReferenceNo() );
				$payment->setType( 'received' );
				$payment->setNote( sprintf( 'payment from user\'s wallet balance. Wallet Withdraw ID – %s', $wdl->getId() ) );
				$payment->setCreatedBy( $this->session->userdata( 'user_id' ) );
				if ( $payment->save() ) {
					$wdl->setStatus( 'approved' );
					$wdl->save();
					if ( $wdl->getAmount() >= $data['grand_total'] ) {
						$this->pay_model->updateStatus( $sale_id, 'completed', 'Full paid with wallet balance.' );
						return 1;
					}
					return true;
				} else {
					$wdl->delete();
					$error = lang( 'unable_use_process_wallet_balance_try_again' );
					
				}
			} else {
				$error = lang( 'unable_use_process_wallet_balance_try_again' );
			}
			if ( $error ) {
				if ( ! $return_error ) {
					// Redirect user to order page with error. So user can try to pay again.
					$this->flash_response( $error, $order_page );
				}
				return $error;
			}
		}
		
		return false;
	}
	
	/**
	 * Send Order Notification Email.
	 *
	 * @param int    $id          sale/order id
	 * @param string $hash        sale/order hash
	 * @param bool   $flash_error set flash error?
	 *
	 * @return array|bool
	 */
	public function order_received( $id = null, $hash = null, $flash_error = true ) {
		if ( $inv = $this->shop_model->getOrder( [ 'id' => $id, 'hash' => $hash ] ) ) {
			$user     = $inv->created_by ? $this->site->getUser($inv->created_by) : null;
			$customer = $this->site->getCompanyByID($inv->customer_id);
			$biller   = $this->site->getCompanyByID($inv->biller_id);
			$this->load->library('parser');
			$parse_data = [
				'reference_number' => $inv->reference_no,
				'contact_person'   => $customer->name,
				'company'          => $customer->company && $customer->company != '-' ? '(' . $customer->company . ')' : '',
				'order_link'       => shop_url('orders/' . $id . '/' . ($this->loggedIn ? '' : $inv->hash)),
				'site_link'        => base_url(),
				'site_name'        => $this->Settings->site_name,
				'logo'             => '<img src="' . base_url() . 'assets/uploads/logos/' . $biller->logo . '" alt="' . ($biller->company && $biller->company != '-' ? $biller->company : $biller->name) . '"/>',
			];
			$msg     = file_get_contents('./themes/' . $this->Settings->adminTheme . 'email_templates/sale.html');
			$message = $this->parser->parse_string($msg, $parse_data);
			$this->load->model('pay_model');
			$paypal   = $this->pay_model->getPaypalSettings();
			$skrill   = $this->pay_model->getSkrillSettings();
			$btn_code = '<div id="payment_buttons" class="text-center margin010">';
			if ( ! empty( $this->shop_settings->bank_details ) ) {
				$btn_code .= '<div style="width:100%;">' . $this->shop_settings->bank_details . '</div><hr class="divider or">';
			}
			if ($paypal->active == '1' && $inv->grand_total != '0.00') {
				if (trim(strtolower($customer->country)) == $biller->country) {
					$paypal_fee = $paypal->fixed_charges + ($inv->grand_total * $paypal->extra_charges_my / 100);
				} else {
					$paypal_fee = $paypal->fixed_charges + ($inv->grand_total * $paypal->extra_charges_other / 100);
				}
				$btn_code .= '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=' . $paypal->account_email . '&item_name=' . $inv->reference_no . '&item_number=' . $inv->id . '&image_url=' . base_url() . 'assets/uploads/logos/' . $this->Settings->logo . '&amount=' . (($inv->grand_total - $inv->paid) + $paypal_fee) . '&no_shipping=1&no_note=1&currency_code=' . $this->default_currency->code . '&bn=BuyNow&rm=2&return=' . admin_url('sales/view/' . $inv->id) . '&cancel_return=' . admin_url('sales/view/' . $inv->id) . '&notify_url=' . admin_url('payments/paypalipn') . '&custom=' . $inv->reference_no . '__' . ($inv->grand_total - $inv->paid) . '__' . $paypal_fee . '"><img src="' . base_url('assets/images/btn-paypal.png') . '" alt="Pay by PayPal"></a> ';
			}
			if ($skrill->active == '1' && $inv->grand_total != '0.00') {
				if (trim(strtolower($customer->country)) == $biller->country) {
					$skrill_fee = $skrill->fixed_charges + ($inv->grand_total * $skrill->extra_charges_my / 100);
				} else {
					$skrill_fee = $skrill->fixed_charges + ($inv->grand_total * $skrill->extra_charges_other / 100);
				}
				$btn_code .= ' <a href="https://www.moneybookers.com/app/payment.pl?method=get&pay_to_email=' . $skrill->account_email . '&language=EN&merchant_fields=item_name,item_number&item_name=' . $inv->reference_no . '&item_number=' . $inv->id . '&logo_url=' . base_url() . 'assets/uploads/logos/' . $this->Settings->logo . '&amount=' . (($inv->grand_total - $inv->paid) + $skrill_fee) . '&return_url=' . admin_url('sales/view/' . $inv->id) . '&cancel_url=' . admin_url('sales/view/' . $inv->id) . '&detail1_description=' . $inv->reference_no . '&detail1_text=Payment for the sale invoice ' . $inv->reference_no . ': ' . $inv->grand_total . '(+ fee: ' . $skrill_fee . ') = ' . $this->rerp->formatMoney($inv->grand_total + $skrill_fee) . '&currency=' . $this->default_currency->code . '&status_url=' . admin_url('payments/skrillipn') . '"><img src="' . base_url('assets/images/btn-skrill.png') . '" alt="Pay by Skrill"></a>';
			}
			
			$btn_code .= '<div class="clearfix"></div></div>';
			$message    = $message . $btn_code;
			$attachment = $this->orders($id, $hash, true, 'S');
			$subject    = lang('new_order_received');
			$sent       = false;
			$error      = false;
			$cc         = [];
			$bcc        = [];
			if ($user) {
				$cc[] = $customer->email;
			}
			$cc[]      = $biller->email;
			$warehouse = $this->site->getWarehouseByID($inv->warehouse_id);
			if ($warehouse->email) {
				$cc[] = $warehouse->email;
			}
			try {
				$sent = $this->rerp->send_email( ( $user ? $user->email : $customer->email ), $subject, $message, null, null, $attachment, $cc, $bcc );
				if ( $sent ) {
					delete_files($attachment);
				}
			} catch (Exception $e) {
				$error = $e->getMessage();
			}
			
			if ( $flash_error && $error ) {
				$this->session->set_flashdata( 'error', $error );
			}
			
			return [ 'sent' => $sent, 'error' => $error ];
		}
		
		return false;
	}
}
