<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Stripe\Charge;
use Stripe\Stripe;

/**
 * Class Pay
 * @property Pay_model $pay_model
 * @property Site $site
 * @property SSLCOMMERZ $ssl_com
 */
class Pay extends MY_Shop_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('pay_model');
	}
	
	public function index() {
		if (!SHOP) {
			redirect('admin');
		}
		redirect();
	}
	
	public function by_authorize( $sale_id ) {
		if ( $inv = $this->pay_model->getSaleByID( $sale_id ) ) {
			$user = $inv->created_by ? $this->site->getUser( $inv->created_by ) : null;
			$authorize = $this->getAuthorizeSettings();
			if ( ! empty( $authorize['api_login_id'] ) && ( ( $inv->grand_total - $inv->paid ) > 0 ) ) {
				$card_expiry = $this->input->post('card_expiry');
				$card_expiry = explode( '/', $card_expiry );
				if ( count( $card_expiry ) < 2 ) {
					$card_expiry[] = '';
				}
				$payment = [
					'sale_id' => $sale_id,
					'cc_no' => $this->input->post('cc_no'),
					'cc_holder' => $this->input->post('cc_holder'),
					'cc_month' => $card_expiry[0],
					'cc_year' => $card_expiry[1],
					'cc_type' => $this->input->post('cc_type'),
					'cc_cvv2' => $this->input->post('security'),
					'hash' => $inv->hash,
				];
				
				$authorize_arr = [
					'x_card_num'    => $payment['cc_no'],
					'x_exp_date'    => ( $payment['cc_month'] . '/' . $payment['cc_year'] ),
					'x_card_code'   => $payment['cc_cvv2'],
					'x_amount'      => $inv->grand_total - $inv->paid,
					'x_invoice_num' => $inv->id,
					'x_description' => 'Sale Ref ' . $inv->reference_no,
				];
				$cc_holder = explode( ' ', $payment['cc_holder'], 2 );
				if ( ! empty( $cc_holder[1] ) ) {
					list( $first_name, $last_name ) = $cc_holder;
				} else {
					$first_name = $cc_holder[0];
					$last_name  = '';
				}
				$authorize_arr['x_first_name'] = $first_name;
				$authorize_arr['x_last_name']  = $last_name;
				$pay_result                    = $this->authorize( $authorize_arr );
				if ( isset( $pay_result['success'] ) ) {
					if ( $inv = $this->pay_model->getSaleByID( $sale_id ) ) {
						$mb_amount    = $inv->grand_total - $inv->paid;
						$reference    = $inv->reference_no;
						$payment_data = [
							'date'           => date( 'Y-m-d H:i:s' ),
							'sale_id'        => $sale_id,
							'reference_no'   => $inv->reference_no,
							'amount'         => $mb_amount,
							'paid_by'        => 'authorize',
							'transaction_id' => $pay_result['transaction_id'],
							'type'           => 'received',
							'note'           => $this->default_currency->code . ' ' . $mb_amount . ' had been paid for the Sale Reference No ' . $inv->reference_no,
						];
						if ( $this->pay_model->addPayment( $payment_data ) ) {
							$customer = $this->site->getCompanyByID( $inv->customer_id );
							$this->pay_model->updateStatus( $inv->id, 'completed' );
							
							$this->load->library( 'parser' );
							$parse_data = [
								'reference_number' => $reference,
								'contact_person'   => $customer->name,
								'company'          => $customer->company,
								'site_link'        => base_url(),
								'site_name'        => $this->Settings->site_name,
								'logo'             => '<img src="' . base_url( 'assets/uploads/logos/' . $this->Settings->logo ) . '" alt="' . $this->Settings->site_name . '"/>',
							];
							
							$msg     = file_get_contents( './themes/' . $this->Settings->adminTheme . 'email_templates/payment.html' );
							$message = $this->parser->parse_string( $msg, $parse_data );
							$this->rerp->log_payment( 'SUCCESS', 'Payment has been made for Sale Reference #' . $reference . ' via Authorize (' . $pay_result['transaction_id'] . ').', json_encode( $payment_data ) );
							try {
								$this->rerp->send_email( ( $user ? $user->email : $customer->email ), 'Payment made for sale ' . $inv->reference_no, $message );
							}
							catch ( Exception $e ) {
								$this->rerp->log_payment( 'ERROR', 'Email Notification Failed: ' . $e->getMessage() );
							}
							$this->session->set_flashdata( 'success', lang( 'payment_added' ) );
							if ( $inv->shop ) {
								$this->load->library( 'sms' );
								$this->sms->paymentReceived( $inv->id, $payment_data['reference_no'], $payment_data['amount'] );
							}
						}
					} else {
						$this->rerp->log_payment( 'ERROR', 'Payment failed for via Authorize.', json_encode( $pay_result['error'] ) );
						$this->session->set_flashdata( 'error', lang( 'payment_failed' ) );
					}
				} else {
					$this->session->set_flashdata( 'error', lang( 'payment_failed' ) );
				}
			}
		} else {
			$this->session->set_flashdata( 'error', lang( 'sale_x_found' ) );
		}
		redirect( $_SERVER['HTTP_REFERER'] );
	}
	
	public function byAuthorize( $id ) {
		$this->data['inv_id']     = $id;
		$this->data['page_title'] = lang( 'paying_by_auth_net' );
		$this->data['page_desc']  = '';
		$this->page_construct( 'pages/byAthorize', $this->data );
	}
	
	private function authorize( $authorize_data ) {
		$this->load->library( 'authorize_net' );
		// $authorize_data = array( 'x_card_num' => '4111111111111111', 'x_exp_date' => '12/20', 'x_card_code' => '123', 'x_amount' => '25', 'x_invoice_num' => '15454', 'x_description' => 'References');
		$this->authorize_net->setData( $authorize_data );
		
		if ( $this->authorize_net->authorizeAndCapture() ) {
			return [
				'transaction_id' => $this->authorize_net->getTransactionId(),
				'approval_code'  => $this->authorize_net->getApprovalCode(),
				'created_at'     => date( $this->dateFormats['php_ldate'] ),
				'success'        => true,
			];
		} else {
			return [
				'error' => $this->authorize_net->getError()
			];
		}
	}
	
	private function getAuthorizeSettings()
	{
		$this->config->load('payment_gateways', true);
		$payment_config = $this->config->item('payment_gateways');
		return $payment_config['authorize'];
	}
	
	public function sslcommerz( $id, $path = '', $hash = '' ) {
		
		$sslcommerz = $this->pay_model->getSslcommerzSettings();
		$invoice    = new Erp_Invoice( $id );
		$customer   = new Erp_Company( $invoice->getCustomerId() );
		if ( ! $invoice ) {
			if ( 'ipn' === $path ) {
				set_status_header( 404 );
				die( 'Invoice Not Found' );
			}
			$this->set_flash_Message( 'error', sprintf( lang( 'sale_x_found' ), $id ) );
			redirect( '/' );
		}
		if ( ! $sslcommerz || ( $sslcommerz && ! $sslcommerz->active ) ) {
			if ( 'ipn' === $path ) {
				set_status_header( 400 );
				die( 'Invalid Settings' );
			}
			$this->set_flash_Message( 'error', lang( 'invalid_payment_gateway' ) );
			redirect( '/' );
		}
		try {
			$this->load->library( 'SSLCOMMERZ', [ $sslcommerz->store_id, $sslcommerz->store_password ], 'ssl_com' );
			$this->ssl_com->setCredentials( $sslcommerz->store_id, $sslcommerz->store_password, DEMO ? SSLCOMMERZ::MODE_SANDBOX : SSLCOMMERZ::MODE_LIVE );
			
			if ( ( empty( $path ) || 'init' === $path ) ) {
				if ( $invoice->needsPayment() ) {
					try {
						// set as billing if user doesn't have country.
						if ( ! $customer->getAddress() || ! $customer->getCity() || ! $customer->getCountry() ) {
							$shippingAddress = $invoice->getShippingAddress();
							$customer->setAddress( $shippingAddress->getLine() );
							$customer->setCity( $shippingAddress->getCity() );
							$customer->setPostalCode( $shippingAddress->getPostalCode() );
							$customer->setState( $shippingAddress->getState() );
							$customer->setCountry( $shippingAddress->getCountry() );
							$customer->save();
						}
						$additionalFee = $this->get_fee( $invoice, $customer, $sslcommerz );
						$paymentData   = new SSLCOMMERZ_Payment_Data( [
							'total_amount'     => $this->get_payable( $invoice, $additionalFee ),
							'tran_id'          => $invoice->getId() . '-' . $invoice->getReferenceNo(),
							
							'success_url'      => site_url( 'pay/sslcommerz/' . $invoice->getId() . '/success/' . $invoice->getHash() ),
							'fail_url'         => site_url( 'pay/sslcommerz/' . $invoice->getId() . '/failed/' . $invoice->getHash() ),
							'cancel_url'       => site_url( 'pay/sslcommerz/' . $invoice->getId() . '/canceled/' . $invoice->getHash() ),
							'ipn_url'          => site_url( 'pay/sslcommerz/' . $invoice->getId() . '/ipn/' . $invoice->getHash() ),
							
							'cus_name'         => $customer->getName(),
							'cus_add1'         => $customer->getAddress( true, ', ' ),
							'cus_country'      => $customer->getCountry(),
							'cus_state'        => $customer->getCity(),
							'cus_city'         => $customer->getState(),
							'cus_postcode'     => $customer->getPostalCode(),
							'cus_phone'        => $customer->getPhone(),
							'cus_email'        => $customer->getEmail(),
							
							'ship_name'        => $customer->getName(),
							'ship_add1'        => $invoice->getShippingAddress()->getLine(),
							'ship_country'     => $invoice->getShippingAddress()->getCountry(),
							'ship_state'       => $invoice->getShippingAddress()->getState(),
							'ship_city'        => $invoice->getShippingAddress()->getCity(),
							'ship_postcode'    => $invoice->getShippingAddress()->getPostalCode(),
							'currency'         => $this->default_currency->code,
							
							'product_category' => 'ecommerce',
							'shipping_method'  => 'yes',
							'num_of_item'      => $invoice->getTotalItems(),
							'product_name'     => implode( ', ', array_map( function ( Erp_Invoice_Item $item ) { return $item->getProductName(); }, $invoice->getItems() ) ),
							'product_profile'  => 'general', // general, physical-goods, non-physical-goods, airline-tickets, travel-vertical, telecom-vertical
						] );
						if ( $paymentData->total_amount < 10 ) {
							$this->logError( '[SSLCOMMERZ] Unable create payment session. ' . lang( 'sslcommerz_min_pay' ), print_r( $paymentData, true ) );
							$this->set_flash_Message( 'error', sprintf( lang( 'unable_to_process_request_for' ), lang( 'sslcommerz_min_pay' ) ) );
							if ( $this->loggedIn ) {
								redirect( site_url( 'shop/orders/' . $invoice->getId() ) );
							} else {
								redirect( '/' );
							}
						}
						$gwSession = $this->ssl_com->createSession( $paymentData );
						if ( 'SUCCESS' == $gwSession->status && $gwSession->GatewayPageURL ) {
							$this->logInfo( '[SSLCOMMERZ] Session created successfully. Redirecting user to gateway.', print_r( $gwSession, true ) );
							redirect( $gwSession->GatewayPageURL ); // easy pay...
						} else {
							$this->logError( '[SSLCOMMERZ] Unable create payment session.', print_r( $gwSession, true ) );
							$this->set_flash_Message( 'error', sprintf( lang( 'unable_to_process_request_for' ), $gwSession->failedreason ) );
						}
					} catch ( SSLCOMMERZ_Exception $e ) {
						$this->logError( '[SSLCOMMERZ] Unable to verify payment', $e->getMessage() );
						$this->set_flash_Message( 'error', lang( 'unable_to_process_request' ) );
					}
				} else {
					$this->set_flash_Message( 'message', sprintf( lang( 'sale_x_already_paid' ), $invoice->getId() ) );
				}
				if ( $this->loggedIn ) {
					redirect( site_url( 'shop/orders/' . $invoice->getId() ) );
				} else {
					redirect( '/' );
				}
			} else {
				if ( ! $invoice->match_hash( $hash ) ) {
					if ( 'ipn' === $path ) {
						set_status_header( 400 );
						die( 'Invalid Token' );
					} else {
						set_status_header( 404 );
						show_404();
					}
				}
				
				// ipn.
				$response = $this->ssl_com->listenToIPN();
				
				if ( $response && in_array( $path, [ 'success', 'failed', 'canceled', 'ipn', ] ) ) {
					if ( 'success' === $path || 'ipn' === $path ) {
						$ipnStatus  = false;
						$validation = false;
						if ( 'VALID' === $response->status && $response->verified ) {
							$validation = $this->ssl_com->validateOrder( $response->val_id );
							if ( in_array( $validation->status, [ 'VALID', 'VALIDATED' ] ) && $validation->amount ) {
								if ( $validation->currency == $this->Settings->default_currency ) {
									$amount = $validation->amount;
								} else {
									//$amount = ( $validation->amount * ( 1 / $validation->currency_rate ) );
									$amount = $validation->currency_amount;
								}
								$note = sprintf(
									'%s %s had been paid for the Sale Reference No %s',
									$validation->currency,
									$validation->amount,
									$invoice->getReferenceNo()
								);
								$gateway = [
									'name'    => 'SSLCOMMERZ',
									'tran_id' => $validation->bank_tran_id,
									'account_email' => $sslcommerz->account_email
								];
								$ipnStatus = $this->handle_success( $invoice, $customer, $amount, $note, $gateway, $response );
							} else {
								$this->set_flash_Message( 'error', 'Unable to verify payment data.' );
								$this->logError( sprintf( '[SSLCOMMERZ] Unable to verify payment. %s', print_r( $validation->to_array(), true ) ) );
							}
						} else {
							$this->set_flash_Message( 'error', 'Unable to verify payment data.' );
							$this->logError( '[SSLCOMMERZ] Unable to verify payment', print_r( $response, true ) );
						}
						$webViewUA = preg_match( '/^RetailERP\/[0-9.]{3,}\s[\w]+\sv[0-9.]{3,}$/i', $_SERVER['HTTP_USER_AGENT'] );
						$status    = $ipnStatus ? 'success' : 'failed';
						if ( false !== $webViewUA && $invoice->getApi() ) {
							$webview = '#webview?payment=' . $status;
							if ( $validation ) {
								$webview .= '&status=' . strtolower( $validation->status );
							} else {
								$webview .= '&status=' . strtolower( $response->status );
							}
							shop_redirect( 'orders/' . $invoice->getId() . '/' . ( $this->loggedIn ? '' : $invoice->getHash() ) . $webview );
						}
						if ( $invoice->getShop() ) {
							$this->flash_response( lang( 'payment_added' ), 'orders/' . $invoice->getId() . '/' . ( $this->loggedIn ? '' : $invoice->getHash() ) );
						}
						redirect( SHOP ? '/' : site_url( 'notify/payment_' . $status ) );
					} else {
						
						if ( 'failed' === $path ) {
							$this->set_flash_Message( 'error', sprintf( 'Payment Failed (%s).', $response->error ) );
							$this->logError( sprintf( '[SSLCOMMERZ] Payment Failed (%s)', $response->error ), print_r( $response, true ));
						}
						
						if ( 'canceled' === $path ) {
							$this->set_flash_Message( 'error', 'Your payment got canceled!' );
							$this->logError( sprintf( '[SSLCOMMERZ] Payment Canceled By The User' ));
						}
						
						$invoice->setPaymentStatus( $path );
						$invoice->save();
						redirect(SHOP ? '/' : site_url('notify/payment_failed', '' ));
					}
				} else {
					set_status_header( 404 );
					show_404();
				}
			}
		} catch( SSLCOMMERZ_Exception $e ) {
			$this->set_flash_Message( 'error', lang( 'payment_failed' ) );
			$this->logError( '[SSLCOMMERZ] Payment Failed.', $e->getMessage() );
		}
	}
	
	/**
	 * Handle Successful payment.
	 *
	 * @param Erp_Invoice $invoice
	 * @param Erp_Company $customer
	 * @param float       $amount
	 * @param string      $note
	 * @param array       $gateway {
	 *      @type string       $name
	 *      @type string       $tran_id
	 *      @type string       $account_email
	 * }
	 *
	 * @param SSLCOMMERZ_IPN_Request $payment_data
	 *
	 * @return bool
	 */
	protected function handle_success( $invoice, $customer, $amount, $note, $gateway, $payment_data ) {
		printf(
			'<h3 style="text-align: center;margin: 2em auto;">%s</h3>',
			lang( 'processing_payment_data' )
		);
		$is_risky = 1 === (int) $payment_data->risk_level;
		$this->logInfo( sprintf( '%s PAYMENT VERIFIED and flagged as %s', strtoupper( $gateway['name'] ), $payment_data->risk_title ) );
		$ref = $this->site->getReference( 'pay' );
		if ( $payment = $this->createPayment( $invoice, $amount, $ref, $note, $gateway ) ) {
			$this->update_invoice_status( $invoice, $is_risky ? 'pending' : 'completed' );
			// @TODO add payment status with sending mail customer if ricky true.
			if ( $is_risky ) {
				$invoice->setPaymentStatus( 'on-hold' );
				$invoice->save();
			}
			$this->load->library('parser');
			$parse_data = [
				'reference_number' => $invoice->getReferenceNo(),
				'contact_person'   => $customer->getName(),
				'company'          => $customer->getCompany(),
				'site_link'        => base_url( '/' ),
				'site_name'        => $this->Settings->site_name,
				'logo'             => '<img src="' . base_url('assets/uploads/logos/' . $this->Settings->logo) . '" alt="' . $this->Settings->site_name . '"/>',
			];
			$msg     = file_get_contents( './themes/' . $this->Settings->adminTheme . 'email_templates/payment.html' );
			$message = $this->parser->parse_string($msg, $parse_data);
			$log = sprintf(
				'Payment has been made for Sale Reference #%s via %s (%s) and payment is %s.',
				$invoice->getReferenceNo(),
				strtoupper( $gateway['name'] ),
				$gateway['tran_id'],
				$payment_data->risk_title
			);
			
			$this->logSuccess( $log, json_encode( $payment_data->to_array() ) );
			try {
				$this->rerp->send_email( $gateway['account_email'], 'Payment made for sale #' . $invoice->getReferenceNo(), $message );
			} catch (Exception $e) {
				$this->logError( 'Email Notification Failed: ', $e->getMessage() );
			}
			$this->set_flash_Message( 'message', lang( 'payment_added' ) );
			if ( $invoice->getShop() ) {
				$this->load->library( 'sms' );
				$this->sms->paymentReceived( $invoice->getID(), $payment->getReferenceNo(), $amount );
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Create Payment Entry.
	 *
	 * @param Erp_Invoice $invoice
	 * @param float $amount
	 * @param string $ref
	 * @param string $note
	 * @param array $gateway
	 *
	 * @return bool|Erp_Payment
	 */
	protected function createPayment( $invoice, $amount, $ref, $note, $gateway ) {
		
		$payment = new Erp_Payment();
		$payment->setDate( date('Y-m-d H:i:s') );
		$payment->setSaleId( $invoice->getID() );
		$payment->setReferenceNo( $ref );
		$payment->setAmount( $amount );
		$payment->setCurrency( $this->Settings->default_currency );
		$payment->setPaidBy( strtolower( $gateway['name'] ) );
		$payment->setTransactionId( $gateway['tran_id'] );
		$payment->setType( 'received' );
		$payment->setNote( $note );
		$payment->setCreatedBy( $this->session->userdata( 'user_id' ) );
		return $payment->save() ? $payment: false;
	}
	
	/**
	 * @param Erp_Invoice $invoice Invoice.
	 * @param string $sales_status sales_status
	 * @param string $note sales_note
	 */
	protected function update_invoice_status( $invoice, $sales_status, $note = null ) {
		$this->pay_model->updateStatus( $invoice->getId(), $sales_status, $note );
	}
	
	public function paypal( $id ) {
		if ($inv = $this->pay_model->getSaleByID($id)) {
			$paypal = $this->pay_model->getPaypalSettings();
			if ($paypal->active && (($inv->grand_total - $inv->paid) > 0)) {
				$customer = $this->pay_model->getCompanyByID($inv->customer_id);
				$biller   = $this->pay_model->getCompanyByID($inv->biller_id);
				if (trim(strtolower($customer->country)) == $biller->country) {
					$paypal_fee = $paypal->fixed_charges + ($inv->grand_total * $paypal->extra_charges_my / 100);
				} else {
					$paypal_fee = $paypal->fixed_charges + ($inv->grand_total * $paypal->extra_charges_other / 100);
				}
				$data = [
					'rm'            => 2,
					'no_note'       => 1,
					'no_shipping'   => 1,
					'bn'            => 'BuyNow',
					'item_number'   => $inv->id,
					'item_name'     => $inv->reference_no,
					'return'        => urldecode(site_url('pay/pipn')),
					'notify_url'    => urldecode(site_url('pay/pipn')),
					'currency_code' => $this->default_currency->code,
					'cancel_return' => urldecode(site_url('pay/pipn')),
					'amount'        => (($inv->grand_total - $inv->paid) + $paypal_fee),
					'image_url'     => base_url() . 'assets/uploads/logos/' . $this->Settings->logo,
					'business'      => (DEMO ? 'saleem-facilitator@retailpremier.com' : $paypal->account_email),
					'custom'        => $inv->reference_no . '__' . ($inv->grand_total - $inv->paid) . '__' . $paypal_fee,
				];
				$query = http_build_query($data, null, '&');
				redirect('https://www' . (DEMO ? '.sandbox' : '') . '.paypal.com/cgi-bin/webscr?cmd=_xclick&' . $query);
			} else {
				$this->set_flash_Message( 'error', lang( 'invalid_payment_gateway' ) );
				redirect('/');
			}
		}
		$this->set_flash_Message( 'error', sprintf( lang( 'sale_x_found' ), $id ) );
		redirect('/');
	}
	
	public function pipn() {
		$paypal = $this->pay_model->getPaypalSettings();
		$this->rerp->log_payment( 'INFO', 'Paypal IPN called' );
		$ipnstatus = false;
		
		$req = 'cmd=_notify-validate';
		foreach ( $_POST as $key => $value ) {
			$value = urlencode( stripslashes( $value ) );
			$req   .= "&$key=$value";
		}
		
		$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= 'Host: www' . ( DEMO ? '.sandbox' : '' ) . ".paypal.com\r\n";
		// $header .= "Host: www.paypal.com\r\n";
		$header .= 'Content-Length: ' . strlen( $req ) . "\r\n";
		$header .= "Connection: close\r\n\r\n";
		
		$fp = fsockopen( 'ssl://www' . ( DEMO ? '.sandbox' : '' ) . '.paypal.com', 443, $errno, $errstr, 30 );
		// $fp = fsockopen('ssl://www.paypal.com', 443, $errno, $errstr, 30);
		
		if ( ! $fp ) {
			$this->rerp->log_payment( 'ERROR', 'Paypal Payment Failed (IPN HTTP ERROR)', $errstr );
			$this->set_flash_Message( 'error', lang( 'payment_failed' ) );
		} else {
			fputs( $fp, $header . $req );
			while ( ! feof( $fp ) ) {
				$res = fgets( $fp, 1024 );
				//log_message('error', 'Paypal IPN - fp handler -'.$res);
				if ( stripos( $res, 'VERIFIED' ) !== false ) {
					$this->rerp->log_payment( 'INFO', 'Paypal IPN - VERIFIED' );
					
					$custom      = explode( '__', $_POST['custom'] );
					$payer_email = $_POST['payer_email'];
					$invoice_no  = $_POST['item_number'];
					$reference   = $_POST['item_name'];
					
					if ( ( $_POST['payment_status'] == 'Completed' || $_POST['payment_status'] == 'Processed' || $_POST['payment_status'] == 'Pending' ) && ( $_POST['business'] == $paypal->account_email || $_POST['business'] == 'saleem-facilitator@retailpremier.com' ) ) {
						if ( $_POST['mc_currency'] == $this->Settings->default_currency ) {
							$amount = $_POST['mc_gross'];
						} else {
							$currency = $this->site->getCurrencyByCode( $_POST['mc_currency'] );
							$amount   = $_POST['mc_gross'] * ( 1 / $currency->rate );
						}
						if ( $inv = $this->pay_model->getSaleByID( $invoice_no ) ) {
							$payment = [
								'date'           => date( 'Y-m-d H:i:s' ),
								'sale_id'        => $invoice_no,
								'reference_no'   => $this->site->getReference( 'pay' ),
								'amount'         => $amount,
								'paid_by'        => 'paypal',
								'transaction_id' => $_POST['txn_id'],
								'type'           => 'received',
								'note'           => $_POST['mc_currency'] . ' ' . $_POST['mc_gross'] . ' had been paid for the Sale Reference No ' . $inv->reference_no,
							];
							if ( $this->pay_model->addPayment( $payment ) ) {
								$customer = $this->pay_model->getCompanyByID( $inv->customer_id );
								$this->pay_model->updateStatus( $inv->id, 'completed' );
								
								$this->load->library( 'parser' );
								$parse_data = [
									'reference_number' => $reference,
									'contact_person'   => $customer->name,
									'company'          => $customer->company,
									'site_link'        => base_url(),
									'site_name'        => $this->Settings->site_name,
									'logo'             => '<img src="' . base_url( 'assets/uploads/logos/'
									                                  . $this->Settings->logo ) . '" alt="' . $this->Settings->site_name . '"/>',
								];
								
								$msg     = file_get_contents( './themes/' . $this->Settings->adminTheme . 'email_templates/payment.html' );
								$message = $this->parser->parse_string( $msg, $parse_data );
								$this->rerp->log_payment( 'SUCCESS', 'Payment has been made for Sale Reference #' . $_POST['item_name'] . ' via Paypal (' . $_POST['txn_id'] . ').', json_encode( $_POST ) );
								try {
									$this->rerp->send_email( $paypal->account_email, 'Payment made for sale ' . $inv->reference_no, $message );
								} catch ( Exception $e ) {
									$this->rerp->log_payment( 'Email Notification Failed: ' . $e->getMessage() );
								}
								$this->set_flash_Message( 'message', lang( 'payment_added' ) );
								$ipnstatus = true;
								if ( $inv->shop ) {
									$this->load->library( 'sms' );
									$this->sms->paymentReceived( $inv->id, $payment['reference_no'], $payment['amount'] );
								}
							}
						}
					} else {
						$this->rerp->log_payment( 'ERROR', 'Payment failed for Sale Reference #' . $reference . ' via Paypal (' . $_POST['txn_id'] . ').', json_encode( $_POST ) );
						$this->set_flash_Message( 'error', lang( 'payment_failed' ) );
					}
				} elseif ( stripos( $res, 'INVALID' ) !== false ) {
					$this->rerp->log_payment( 'ERROR', 'INVALID response from Paypal. Payment failed via Paypal.', json_encode( $_POST ) );
					$this->set_flash_Message( 'error', lang( 'payment_failed' ) );
				}
			}
			fclose( $fp );
		}
		
		if ( $inv->shop ) {
			shop_redirect( 'orders/' . $inv->id . '/' . ( $this->loggedIn ? '' : $inv->hash ) );
		}
		
		redirect( SHOP ? '/' : site_url( $ipnstatus ? 'notify/payment_success' : 'notify/payment_failed' ) );
		exit();
	}
	
	public function sipn() {
		$skrill = $this->pay_model->getSkrillSettings();
		$this->rerp->log_payment('INFO', 'Skrill IPN called', json_encode($_POST));
		$ipnstatus = false;
		
		if (isset($_POST['merchant_id']) && isset($_POST['md5sig'])) {
			$concatFields = $_POST['merchant_id'] . $_POST['transaction_id'] . strtoupper(md5($skrill->secret_word)) . $_POST['mb_amount'] . $_POST['mb_currency'] . $_POST['status'];
			
			if (strtoupper(md5($concatFields)) == $_POST['md5sig'] && $_POST['status'] == 2 && $_POST['pay_to_email'] == $skrill->account_email) {
				$invoice_no = $_POST['item_number'];
				$reference  = $_POST['item_name'];
				if ($_POST['mb_currency'] == $this->Settings->default_currency) {
					$amount = $_POST['mb_amount'];
				} else {
					$currency = $this->site->getCurrencyByCode($_POST['mb_currency']);
					$amount   = $_POST['mb_amount'] * (1 / $currency->rate);
				}
				if ($inv = $this->pay_model->getSaleByID($invoice_no)) {
					$payment = [
						'date'           => date('Y-m-d H:i:s'),
						'sale_id'        => $invoice_no,
						'reference_no'   => $this->site->getReference('pay'),
						'amount'         => $amount,
						'paid_by'        => 'skrill',
						'transaction_id' => $_POST['mb_transaction_id'],
						'type'           => 'received',
						'note'           => $_POST['mb_currency'] . ' ' . $_POST['mb_amount'] . ' had been paid for the Sale Reference No ' . $reference,
					];
					if ($this->pay_model->addPayment($payment)) {
						$customer = $this->site->getCompanyByID($inv->customer_id);
						$this->pay_model->updateStatus($inv->id, 'completed');
						
						$this->load->library('parser');
						$parse_data = [
							'reference_number' => $reference,
							'contact_person'   => $customer->name,
							'company'          => $customer->company,
							'site_link'        => base_url(),
							'site_name'        => $this->Settings->site_name,
							'logo'             => '<img src="' . base_url('assets/uploads/logos/' . $this->Settings->logo) . '" alt="' . $this->Settings->site_name . '"/>',
						];
						
						$msg     = file_get_contents('./themes/' . $this->Settings->adminTheme . 'email_templates/payment.html');
						$message = $this->parser->parse_string($msg, $parse_data);
						$this->rerp->log_payment('SUCCESS', 'Payment has been made for Sale Reference #' . $_POST['item_name'] . ' via Skrill (' . $_POST['mb_transaction_id'] . ').', json_encode($_POST));
						try {
							$this->rerp->send_email($skrill->account_email, 'Payment made for sale ' . $inv->reference_no, $message);
						} catch (Exception $e) {
							$this->rerp->log_payment('Email Notification Failed: ' . $e->getMessage());
						}
						$this->set_flash_Message('message', lang('payment_added'));
						$ipnstatus = true;
						if ($inv->shop) {
							$this->load->library('sms');
							$this->sms->paymentReceived($inv->id, $payment['reference_no'], $payment['amount']);
						}
					}
				}
			} else {
				$this->rerp->log_payment('ERROR', 'Payment failed for via Skrill.', json_encode($_POST));
				$this->set_flash_Message('error', lang('payment_failed'));
			}
		} else {
			redirect('notify/payment');
		}
		
		if ($inv->shop) {
			shop_redirect('orders/' . $inv->id . '/' . ($this->loggedIn ? '' : $inv->hash));
		}
		
		redirect(SHOP ? '/' : site_url($ipnstatus ? 'notify/payment_success' : 'notify/payment_failed'));
		exit();
	}
	
	public function skrill($id) {
		if ($inv = $this->pay_model->getSaleByID($id)) {
			$skrill = $this->pay_model->getSkrillSettings();
			if ($skrill->active && (($inv->grand_total - $inv->paid) > 0)) {
				$customer = $this->pay_model->getCompanyByID($inv->customer_id);
				$biller   = $this->pay_model->getCompanyByID($inv->biller_id);
				if (trim(strtolower($customer->country)) == $biller->country) {
					$skrill_fee = $skrill->fixed_charges + ($inv->grand_total * $skrill->extra_charges_my / 100);
				} else {
					$skrill_fee = $skrill->fixed_charges + ($inv->grand_total * $skrill->extra_charges_other / 100);
				}
				redirect('https://www.moneybookers.com/app/payment.pl?method=get&pay_to_email=' . $skrill->account_email . '&language=EN&merchant_fields=item_name,item_number&item_name=' . $inv->reference_no . '&item_number=' . $inv->id . '&logo_url=' . base_url() . 'assets/uploads/logos/' . $this->Settings->logo . '&amount=' . (($inv->grand_total - $inv->paid) + $skrill_fee) . '&return_url=' . shop_url('orders/' . $inv->id) . '&cancel_url=' . site_url('/') . '&detail1_description=' . $inv->reference_no . '&detail1_text=Payment for the sale invoice ' . $inv->reference_no . ': ' . $inv->grand_total . '(+ fee: ' . $skrill_fee . ') = ' . $this->rerp->formatMoney($inv->grand_total + $skrill_fee) . '&currency=' . $this->default_currency->code . '&status_url=' . site_url('pay/sipn'));
			} else {
				$this->set_flash_Message( 'error', lang( 'invalid_payment_gateway' ) );
				redirect('/');
			}
		}
		$this->set_flash_Message( 'error', sprintf( lang( 'sale_x_found' ), $id ) );
		redirect('/');
	}
	
	public function stripe( $id = null ) {
		$reference = '';
		$stripeToken = $this->input->post('stripeToken');
		$stripeEmail = $this->input->post('stripeEmail');
		if (!$id || !$stripeToken) {
			show_404();
		}
		
		$this->config->load('payment_gateways');
		$inv         = $this->pay_model->getSaleByID($id);
		$description = lang('sale') . ' ' . lang('no.') . ' ' . $id;
		$grand_total = ($inv->grand_total - $inv->paid);
		$amount      = ($grand_total * 100);
		if ($stripeToken) {
			Stripe::setApiKey($this->config->item('stripe_secret_key'));
			try {
				$charge = Charge::create([
					'amount'      => $amount,
					'card'        => $stripeToken,
					'description' => $description,
					'currency'    => $this->default_currency->code,
				]);
				// return $charge;
				if (strtolower($charge->currency) == strtolower($this->default_currency->code)) {
					$payment = [
						'date'           => date('Y-m-d H:i:s'),
						'sale_id'        => $inv->id,
						'reference_no'   => $this->site->getReference('pay'),
						'amount'         => ($charge->amount / 100),
						'paid_by'        => 'stripe',
						'transaction_id' => $charge->id,
						'type'           => 'received',
						'note'           => $charge->currency . ' ' . ($charge->amount / 100) . ' had been paid by Stripe for the Sale Reference No ' . $inv->reference_no,
					];
					if ($this->pay_model->addPayment($payment)) {
						$customer = $this->pay_model->getCompanyByID($inv->customer_id);
						$this->pay_model->updateStatus($inv->id, 'completed');
						
						$this->load->library('parser');
						$parse_data = [
							'reference_number' => $reference,
							'contact_person'   => $customer->name,
							'company'          => $customer->company,
							'site_link'        => base_url(),
							'site_name'        => $this->Settings->site_name,
							'logo'             => '<img src="' . base_url('assets/uploads/logos/' . $this->Settings->logo) . '" alt="' . $this->Settings->site_name . '"/>',
						];
						
						$msg     = file_get_contents('./themes/' . $this->Settings->adminTheme . 'email_templates/payment.html');
						$message = $this->parser->parse_string($msg, $parse_data);
						$this->rerp->log_payment('SUCCESS', 'Payment has been made for Sale Reference #' . $inv->reference_no . ' via Stripe (' . $charge->id . ').', json_encode($_POST));
						try {
							$this->rerp->send_email($customer->email, 'Payment made for sale ' . $inv->reference_no, $message);
						} catch (Exception $e) {
							$this->rerp->log_payment('Email Notification Failed: ' . $e->getMessage());
						}
						$this->set_flash_Message('message', lang('payment_added'));
						$payments_received = true;
						if ($inv->shop) {
							$this->load->library('sms');
							$this->sms->paymentReceived($inv->id, $inv->reference_no, ($charge->amount / 100));
						}
					}
				}
			} catch (Exception $e) {
				$this->set_flash_Message('error', $e->getMessage());
				$this->rerp->log_payment('ERROR', 'Payment failed for via Stripe.', json_encode($_POST));
				shop_redirect('orders/' . $inv->id . '/' . ($this->loggedIn ? '' : $inv->hash));
			}
		} else {
			redirect('notify/payment');
		}
		
		if ($inv->shop) {
			shop_redirect('orders/' . $inv->id . '/' . ($this->loggedIn ? '' : $inv->hash));
		}
		
		redirect(SHOP ? '/' : site_url($payments_received ? 'notify/payment_success' : 'notify/payment_failed'));
		exit();
	}
	
	/**
	 * @param Erp_Invoice $invoice
	 * @param Erp_Company $customer
	 * @param object $config
	 */
	protected function get_fee( $invoice, $customer, $config ) {
		
		$biller = new Erp_Company( $invoice->getBillerId() );
		
		if ( trim( strtolower( $customer->getCountry() ) )
		     == trim( strtolower( $biller->getCountry() ) ) ) {
			$paypal_fee = $config->fixed_charges + ( $invoice->getGrandTotal() * $config->extra_charges_my / 100 );
		} else {
			$paypal_fee = $config->fixed_charges + ( $invoice->getGrandTotal() * $config->extra_charges_other / 100 );
		}
	}
	
	/**
	 * @param Erp_Invoice $invoice
	 * @param float|int $fee
	 * @return float
	 */
	protected function get_payable( $invoice, $fee = 0 ) {
		return ( ( ( $invoice->getGrandTotal() - $invoice->getTotalDiscount() ) - $invoice->getPaid() ) + $fee );
	}
	
	/**
	 * Set Flash Message for current User.
	 *
	 * @param string $type
	 * @param string $message
	 */
	protected function set_flash_Message( $type, $message ) {
		$this->session->set_flashdata( $type, $message );
	}
	
	/**
	 * Log Message.
	 * @param string $message Message.
	 * @param mixed $val
	 *
	 * @return bool
	 */
	protected function logSuccess( $message, $val = null ) {
		return $this->rerp->log_payment( 'SUCCESS', $message, $val );
	}
	
	/**
	 * Log Message.
	 * @param string $message
	 * @param mixed $val
	 *
	 * @return bool
	 */
	protected function logInfo( $message, $val = null ) {
		return $this->rerp->log_payment( 'INFO', $message, $val );
	}
	
	/**
	 * Log Message.
	 *
	 * @param string $message Message
	 * @param mixed $val.
	 *
	 * @return bool
	 */
	protected function logError( $message, $val = null ) {
		return $this->rerp->log_payment( 'ERROR', $message , $val );
	}
	
	protected function get_request_method() {
		return $this->input->server( 'REQUEST_METHOD' );
	}
	
	protected function get_request_referrer() {
		return $this->input->server( 'HTTP_REFERER' );
	}
	
	protected function is_request_method( $method ) {
		return $this->get_request_method() === strtoupper( $method );
	}
	
	protected function is_get() {
		return $this->is_request_method( 'get' );
	}
	
	protected function is_post() {
		return $this->is_request_method( 'post' );
	}
}
// End of file Pay.php.
