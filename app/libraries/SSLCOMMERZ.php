<?php
/** @noinspection SpellCheckingInspection, PhpUnused */
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class SSLCOMMERZ
 * Test Credit Card Account Numbers
 *
 * VISA:
 *
 * Card Number: 4111111111111111
 * Exp: 12/25
 * CVV: 111
 * Mastercard:
 *
 * Card Number: 5111111111111111
 * Exp: 12/25
 * CVV: 111
 * American Express:
 *
 * Card Number: 371111111111111
 * Exp: 12/25
 * CVV: 111
 * Mobile OTP: 111111 or 123456
 */
class SSLCOMMERZ {
	
	const MODE_SANDBOX = 'sandbox';
	const MODE_LIVE = 'securepay';
	
	private $api  = 'sslcommerz.com';
	private $version  = 'v3';
	private $mode = 'sandbox';
	private $store_id = '';
	private $store_password = '';
	private $ssl = true;
	private $options = [];
	private $headers = [];
	
	private $http;
	
	/**
	 * SSLCOMMERZ constructor.
	 */
	public function __construct() {}
	
	/**
	 * SSLCOMMERZ constructor.
	 *
	 * @param string $store_id       [Mandatory] Your SSLCOMMERZ Store ID.
	 * @param string $store_password [Mandatory] Your SSLCOMMERZ Store Password (API Key/Secret).
	 * @param string $mode           Request Mode/Environment.
	 *                               Use the MODE Constant (MODE_SANDBOX/MODE_LIVE).
	 *
	 * @throws SSLCOMMERZ_Exception
	 */
	public function setCredentials( $store_id, $store_password, $mode = SSLCOMMERZ::MODE_SANDBOX ) {
		$this->mode = SSLCOMMERZ::MODE_LIVE === $mode ? SSLCOMMERZ::MODE_LIVE : SSLCOMMERZ::MODE_SANDBOX;
		$this->store_id = $store_id;
		$this->store_password = $store_password;
		
		if ( empty( $this->store_id ) ) {
			throw new SSLCOMMERZ_Exception( 'SSLCOMMERZ Store ID Is Missing.', 400 );
		}
		if ( empty( $this->store_password ) ) {
			throw new SSLCOMMERZ_Exception( 'SSLCOMMERZ Store Password (API Key/Secret) Is Missing.', 300 );
		}
	}
	
	/**
	 * Set Custom Options For HTTP Request.
	 *
	 * @param array $options Options
	 *
	 * @throws Exception
	 */
	public function setOptions( array $options ) {
		
		if ( isset( $options['headers'] ) ) {
			throw new Exception( 'Use SSLCOMMERZ::setHeaders' );
		}
		if (
			isset( $options['body'] ) ||
			isset( $options['multipart'] ) ||
			isset( $options['form_params'] )
		) {
			throw new Exception( 'Unable to set arbitrary data for the request. Please use appropriate method for the request' );
		}
		
		$this->options = $options;
	}
	
	/**
	 * Set Custom Headers For HTTP Request.
	 * @param array $headers Headers.
	 */
	public function setHeaders( array $headers ) {
		unset( $headers['User-Agent'] );
		unset( $headers['Accept'] );
		$this->headers = $headers;
	}
	
	/**
	 * Create New Payment Session for the user to pay.
	 *
	 * @param SSLCOMMERZ_Payment_Data $data Parameters that needs to be send to the SSLCOMMERZ API Server.}
	 *
	 * @return SSLCOMMERZ_Create_Session_Response
	 * @throws SSLCOMMERZ_Exception
	 */
	public function createSession( SSLCOMMERZ_Payment_Data $data ) {
		$response = $this->request( 'gwprocess', $data->to_array(), 'POST' );
		return new SSLCOMMERZ_Create_Session_Response( $response );
	}
	
	/**
	 * IPN Listener
	 * @return bool|SSLCOMMERZ_IPN_Request
	 */
	public function listenToIPN() {
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && count( $_POST ) ) {
			$request = new SSLCOMMERZ_IPN_Request( $_POST );
			$request->verified = $this->validateIPN_Request( $request );
			return $request;
		}
		return false;
	}
	
	/**
	 * Validate IPN.
	 *
	 * @see https://github.com/sslcommerz/SSLCommerz-PHP/blob/master/lib/SslCommerzNotification.php
	 *
	 * @param SSLCOMMERZ_IPN_Request $request IPN Data.
	 *
	 * @return bool
	 */
	private function validateIPN_Request( SSLCOMMERZ_IPN_Request $request ) {
		# Prepare the payload for generating signature hash.
		$payload = [];
		foreach ( $request->getVerifyKey( true ) as $value ) {
			if ( isset( $request->{$value} ) ) {
				$payload[ $value ] = $request->{$value};
			}
		}
		
		# MD5 value of store password.
		$payload['store_passwd'] = md5( $this->store_password );
		
		# SORT THE KEY AS BEFORE
		ksort( $payload );
		
		$signature = '';
		
		// Don't use http_build_query() as it's encode the values.
		foreach ( $payload as $key => $value ) {
			$signature .= "{$key}={$value}&";
		}
		
		$signature = rtrim( $signature, '&' );
		return ( md5( $signature ) === $request->verify_sign );
	}
	
	/**
	 * Order Validation
	 *
	 * @param string $validation_id [Mandatory] A Validation ID against the successful transaction which is provided by SSLCOMMERZ.
	 *
	 * @return SSLCOMMERZ_Order_Validation_Response
	 * @throws SSLCOMMERZ_Exception
	 */
	public function validateOrder( $validation_id ) {
		
		$response = $this->request( 'order_validation', [ 'val_id' => $validation_id ], 'GET' );
		return new SSLCOMMERZ_Order_Validation_Response( $response );
	}
	
	/**
	 * Initiate A Refund Request.
	 *
	 * @param string $trans_id [Mandatory] The transaction ID at Banks End.
	 * @param float  $amount [Mandatory] The amount will be refunded to card holder's account.
	 * @param string $remarks [Mandatory] The reason of refund.
	 * @param string $ref [Optional] You can provide any reference number of your system to reconcile.
	 *
	 * @return SSLCOMMERZ_Refund_Response
	 * @throws SSLCOMMERZ_Exception
	 */
	public function createRefund( $trans_id, $amount, $remarks, $ref = '' ) {
		$data = [
			'bank_tran_id'   => $trans_id,
			'refund_amount'  => $amount,
			'refund_remarks' => $remarks,
			'refe_id'        => $ref,
			'format'         => 'json',
			'v'              => 1,
		];
		$response = $this->request( 'refund', $data, 'GET' );
		return new SSLCOMMERZ_Refund_Response( $response );
	}
	
	/**
	 * Get Refund Status.
	 *
	 * @param string $refund_id [Mandatory] This parameter will be returned only when the request successfully initiates.
	 *
	 * @return SSLCOMMERZ_Query_Refund_Response
	 * @throws SSLCOMMERZ_Exception
	 */
	public function queryRefundStatus( $refund_id ) {
		$response = $this->request( 'refund_status', [ ' refund_ref_id' => $refund_id ], 'GET' );
		return new SSLCOMMERZ_Query_Refund_Response( $response );
	}
	
	/**
	 * Check the status of a transaction by the session id.
	 *
	 * @param string $session_key [Mandatory] The session id has been generated at the time of transaction initiated.
	 *
	 * @return SSLCOMMERZ_Query_Transaction_Response
	 * @throws SSLCOMMERZ_Exception
	 */
	public function queryTransactionStatusBySession( $session_key ) {
		$response = $this->request( 'transaction_query', [ 'sessionkey' => $session_key ], 'GET' );
		return new SSLCOMMERZ_Query_Transaction_Response( $response );
	}
	
	/**
	 * Check the status of a transaction by your transaction id.
	 * @param $transaction_id [Mandatory] Transaction ID (Unique) that was sent by you during initiation.
	 *
	 * @return SSLCOMMERZ_Query_Transactions_Response
	 * @throws SSLCOMMERZ_Exception
	 */
	public function queryTransactionStatus( $transaction_id ) {
		$response = $this->request( 'transaction_query', [ 'tran_id' => $transaction_id ], 'GET' );
		return new SSLCOMMERZ_Query_Transactions_Response( $response );
	}
	
	/**
	 * Sends The Request and return the response.
	 *
	 * @param string $path [Mandatory] Request Path.
	 * @param array  $data [Mandatory] Request Parameters
	 * @param string $method [Mandatory] Request Method (HTTP Method)
	 * @param array  $headers [Optional] Request Header
	 *
	 * @return array Response Data.
	 * @throws SSLCOMMERZ_Exception
	 */
	private function request( $path, $data, $method, $headers = [] ) {
		$endpoint = $this->get_endpoint( $path );
		try {
			
			if ( ! $this->http ) {
				$this->http = new GuzzleHttp\Client();
			}
			
			$options = [
				'verify'          => true,
				'connect_timeout' => 30,
				'debug'           => false,
				'http_errors'     => false,
				'headers'         => $headers,
			];
			
			$options = array_merge(
				$options,
				[
					'headers' => array_merge(
						[
							'User-Agent' => 'PxH/1.0 (SSLCOMMERZ/' . $this->version . ') xPHP/' . phpversion(),
							'Accept'     => 'application/json',
						],
						$this->headers
					)
				],
				$this->options
			);
			
			$data = array_merge(
				[
					'store_id'     => $this->store_id,
					'store_passwd' => $this->store_password,
					'format'       => 'json',
				],
				$data
			);
			
			if ( 'GET' === $method ) {
				$endpoint .= '?' . http_build_query( $data, null, '&' );
			}
			
			if ( 'POST' === $method ) {
				$options['form_params'] = $data;
			}
			
			$request  = $this->http->request( $method, $endpoint, $options );
			$response = (string) $request->getBody();
			
			// OK?
			if ( 200 === $request->getStatusCode() ) {
				return ! is_array( $response ) ? json_decode( $response, 'true' ) : $response;
			} else {
				throw new SSLCOMMERZ_Exception( strip_tags( $response ), 200 );
			}
		} catch ( Exception $e ) {
			throw new SSLCOMMERZ_Exception( strip_tags( $e->getMessage() ), 100, $e );
		}
	}
	
	/**
	 * Select Request URL Path.
	 *
	 * @param string $path [Required]
	 *
	 * @return string
	 * @throws SSLCOMMERZ_Exception
	 */
	private function get_endpoint( $path = '' ) {
		$url = ( $this->ssl ? 'https://' : 'http://' ) . $this->mode . '.' . $this->api;
		switch ( $path ) {
			case 'order_validation':
				return $url . '/validator/api/validationserverAPI.php';
				break;
			case 'transaction_query':
			case 'refund':
			case 'refund_status':
				return $url . '/validator/api/merchantTransIDvalidationAPI.php';
				break;
			case 'gwprocess':
				return $url . '/gwprocess/' . $this->version . '/api.php' ;
				break;
			default:
				throw new SSLCOMMERZ_Exception( 'Invalid Request Path.', 150 );
				break;
		}
	}
}

/**
 * Class SSLCOMMERZ_Exception
 * code 400 SSLCOMMERZ Store Pass
 * code 300 SSLCOMMERZ Store Pass
 * code 200 SSLCOMMERZ API Response with non 200 http status
 * code 100 GuzzleHttp Error
 */
class SSLCOMMERZ_Exception extends Exception {}

/**
 * Class SSLCOMMERZ_Response
 */
abstract class SSLCOMMERZ_Response {
	/**
	 * @var array
	 */
	protected $row_response;
	
	public function __construct( $data ) {
		$this->row_response = $data;
		if ( ! empty( $data ) ) {
			foreach ( $data as $k => $v ) {
				$this->{$k} = $v;
			}
		}
	}
	
	public function get_row_response() {
		return $this->row_response;
	}
	
	/**
	 * To Array.
	 *
	 * @return array
	 */
	public function to_array() {
		/**
		 * Direct call to get_object_vars return the protected (private as well) members too.
		 * But calling from outside only expose the public member.
		 *
		 * @see https://www.php.net/manual/en/function.get-object-vars.php#113435
		 */
		return call_user_func( 'get_object_vars', $this );
	}
}

/**
 * Class SSLCOMMERZ_Payment_Data
 */
class SSLCOMMERZ_Payment_Data {
	
	/**
	 * Mandatory - The amount which will process by SSLCOMMERZ.
	 * It shall be decimal value (10,2).
	 * Example : 55.40. The transaction amount must be from 10.00 BDT to 500000.00 BDT
	 *
	 * @var float
	 */
	public $total_amount;
	
	/**
	 * Mandatory - The currency type must be mentioned. It shall be three characters.
	 * Example : BDT, USD, EUR, SGD, INR, MYR, etc. If the transaction currency is not BDT, then it will be converted to BDT based on the current convert rate. Example : 1 USD = 82.22 BDT.
	 *
	 * @var string
	 */
	public $currency;
	
	/**
	 * Mandatory - Unique transaction ID to identify your order in both your end and SSLCOMMERZ
	 *
	 * @var string
	 */
	public $tran_id;
	
	/**
	 * Mandatory - Mention the product category. It is a open field.
	 * Example - clothing,shoes,watches,gift,health care,jewellery,top up,toys,baby care,pants,laptop,donation
	 *
	 * @var string
	 */
	public $product_category;
	
	/**
	 * Mandatory - It is the callback URL of your website where user will redirect after successful payment (Length: 255)
	 *
	 * @var string
	 */
	public $success_url;
	
	/**
	 * Mandatory - It is the callback URL of your website where user will redirect after any failure occure during payment (Length: 255)
	 *
	 * @var string
	 */
	public $fail_url;
	
	/**
	 * Mandatory - It is the callback URL of your website where user will redirect if user canceled the transaction (Length: 255)
	 *
	 * @var string
	 */
	public $cancel_url;
	
	/**
	 * Important! Not mandatory, however better to use to avoid missing any payment notification.
	 * It is the Instant Payment Notification (IPN) URL of your website where SSLCOMMERZ will send the transaction's status (Length: 255). The data will be communicated as SSLCOMMERZ Server to your Server. So, customer session will not work. IPN is very important feature to integrate with your site(s). Some transaction could be pending or customer lost his/her session, in such cases back-end IPN plays a very important role to update your backend office.
	 *
	 * @var string
	 */
	public $ipn_url;
	
	/**
	 * Do not Use! If you do not customize the gateway list - You can control to display the gateway
	 * list at SSLCOMMERZ gateway selection page by providing this parameters.
	 * brac_visa = BRAC VISA
	 * dbbl_visa = Dutch Bangla VISA
	 * city_visa = City Bank Visa
	 * ebl_visa = EBL Visa
	 * sbl_visa = Southeast Bank Visa
	 * brac_master = BRAC MASTER
	 * dbbl_master = MASTER Dutch-Bangla
	 * city_master = City Master Card
	 * ebl_master = EBL Master Card
	 * sbl_master = Southeast Bank Master Card
	 * city_amex = City Bank AMEX
	 * qcash = QCash
	 * dbbl_nexus = DBBL Nexus
	 * bankasia = Bank Asia IB
	 * abbank = AB Bank IB
	 * ibbl = IBBL IB and Mobile Banking
	 * mtbl = Mutual Trust Bank IB
	 * bkash = Bkash Mobile Banking
	 * dbblmobilebanking = DBBL Mobile Banking
	 * city = City Touch IB
	 * upay = Upay
	 * tapnpay = Tap N Pay Gateway
	 *
	 *
	 * GROUP GATEWAY
	 * internetbank = For all internet banking
	 * mobilebank = For all mobile banking
	 * othercard = For all cards except visa,master and amex
	 * visacard = For all visa
	 * mastercard = For All Master card
	 * amexcard = For Amex Card
	 *
	 * @var string
	 */
	public $multi_card_name;
	
	/**
	 * @var string
	 */
	public $allowed_bin;
	
	// EMI.
	/**
	 * Mandatory - This is mandatory if transaction is EMI enabled and Value must be 1/0.
	 * Here, 1 means customer will get EMI facility for this transaction
	 *
	 * @var int
	 */
	public $emi_option = 0;
	
	/**
	 * Max instalment Option, Here customer will get 3,6, 9 instalment at gateway page
	 *
	 * @var int
	 */
	public $emi_max_inst_option;
	
	/**
	 * Customer has selected from your Site, So no instalment option will be displayed at gateway page
	 *
	 * @var int
	 */
	public $emi_selected_inst;
	
	/**
	 * Value is 1/0, if value is 1 then only EMI transaction is possible, in payment page.
	 * No Mobile banking and internet banking channel will not display. This parameter depends on emi_option and emi_selected_inst
	 *
	 * @var int
	 */
	public $emi_allow_only;
	
	// Customer Information.
	
	/**
	 * Mandatory - Your customer name to address the customer in payment receipt email
	 *
	 * @var string
	 */
	public $cus_name;
	
	/**
	 * Mandatory - Valid email address of your customer to send payment receipt from SSLCOMMERZ end
	 *
	 * @var string
	 */
	public $cus_email;
	
	/**
	 * Mandatory - Address of your customer. Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $cus_add1;
	
	/**
	 * Address line 2 of your customer. Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $cus_add2;
	
	/**
	 * Mandatory - City of your customer. Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $cus_city;
	
	/**
	 * State of your customer. Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $cus_state;
	
	/**
	 * Mandatory - Postcode of your customer. Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $cus_postcode;
	
	/**
	 * Mandatory - Country of your customer. Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $cus_country;
	
	/**
	 * Mandatory - The phone/mobile number of your customer to contact if any issue arises
	 *
	 * @var string
	 */
	public $cus_phone;
	
	/**
	 * Fax number of your customer. Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $cus_fax;
	
	// Shipment Information.
	
	/**
	 * Mandatory - Shipping method of the order. Example: YES or NO or Courier
	 *
	 * @var string
	 */
	public $shipping_method;
	
	/**
	 * Mandatory - No of product will be shipped. Example: 1 or 2 or etc
	 *
	 * @var int
	 */
	public $num_of_item;
	
	/**
	 * Mandatory, if shipping_method is YES - Shipping Address of your order.
	 * Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $ship_name;
	
	/**
	 * Mandatory, if shipping_method is YES - Additional Shipping Address of your order.
	 * Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $ship_add1;
	
	/**
	 * Additional Shipping Address of your order. Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $ship_add2;
	
	/**
	 * Mandatory, if shipping_method is YES - Shipping city of your order.
	 * Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $ship_city;
	
	/**
	 * Shipping state of your order. Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $ship_state;
	
	/**
	 * Mandatory, if shipping_method is YES - Shipping postcode of your order.
	 * Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $ship_postcode;
	
	/**
	 * Mandatory, if shipping_method is YES - Shipping country of your order.
	 * Not mandatory but useful if provided
	 *
	 * @var string
	 */
	public $ship_country;
	
	// Product Information.
	
	/**
	 * Mandatory - Mention the product name briefly. Mention the product name by coma separate. Example: Computer,Speaker
	 *
	 * @var string
	 */
	public $product_name;
	
	/**
	 * [duplicate]
	 * Mandatory - Mention the product category. Example: Electronic or topup or bus ticket or air ticket
	 *
	 * @var string
	 */
	//public $product_category;
	
	/**
	 * Mandatory - Mention goods vertical. It is very much necessary for online transactions to avoid chargeback.
	 * Please use the below keys:
	 * general
	 * physical-goods
	 * non-physical-goods
	 * airline-tickets
	 * travel-vertical
	 * telecom-vertical
	 *
	 * @var string
	 */
	public $product_profile = 'general';
	
	/**
	 * Mandatory, if product_profile is airline-tickets.
	 * Provide the remaining time of departure of flight till at the time of purchasing the ticket. Example: 12 hrs or 36 hrs
	 *
	 * @var string
	 */
	public $hours_till_departure;
	
	/**
	 * Mandatory, if product_profile is airline-tickets.
	 * Provide the flight type. Example: Oneway or Return or Multistop
	 *
	 * @var string
	 */
	public $flight_type;
	
	/**
	 * Mandatory, if product_profile is airline-tickets - Provide the PNR.
	 *
	 * @var string
	 */
	public $pnr;
	
	/**
	 * Mandatory, if product_profile is airline-tickets - Provide the journey route. Example: DAC-CGP or DAC-CGP CGP-DAC
	 *
	 * @var string
	 */
	public $journey_from_to;
	
	/**
	 * Mandatory, if product_profile is airline-tickets - No/Yes. Whether the ticket has been taken from third party booking system.
	 *
	 * @var string
	 */
	public $third_party_booking;
	
	/**
	 * Mandatory, if product_profile is travel-vertical - Please provide the hotel name. Example: Sheraton
	 *
	 * @var string
	 */
	public $hotel_name;
	
	/**
	 * Mandatory, if product_profile is travel-vertical - How long stay in hotel. Example: 2 days
	 *
	 * @var string
	 */
	public $length_of_stay;
	
	/**
	 * Mandatory, if product_profile is travel-vertical - Checking hours for the hotel room. Example: 24 hrs
	 *
	 * @var string
	 */
	public $check_in_time;
	
	/**
	 * Mandatory, if product_profile is travel-vertical - Location of the hotel. Example: Dhaka
	 *
	 * @var string
	 */
	public $hotel_city;
	
	/**
	 * Mandatory, if product_profile is telecom-vertical - For mobile or any recharge, this information is necessary. Example: Prepaid or Postpaid
	 *
	 * @var string
	 */
	public $product_type;
	
	/**
	 * Mandatory, if product_profile is telecom-vertical - Provide the mobile number which will be recharged. Example: 8801700000000 or 8801700000000,8801900000000
	 *
	 * @var string
	 */
	public $topup_number;
	
	/**
	 * Mandatory, if product_profile is telecom-vertical - Provide the country name in where the service is given. Example: Bangladesh
	 *
	 * @var string
	 */
	public $country_topup;
	
	/**
	 * JSON data with two elements. product : Max 255 characters, quantity : Quantity in numeric value and amount : Decimal (12,2)
	 * Example:
	 * [{"product":"DHK TO BRS AC A1","quantity":"1","amount":"200.00"},{"product":"DHK TO BRS AC A2","quantity":"1","amount":"200.00"},{"product":"DHK TO BRS AC A3","quantity":"1","amount":"200.00"},{"product":"DHK TO BRS AC A4","quantity":"2","amount":"200.00"}]
	 *
	 * @var string
	 */
	public $cart;
	
	/**
	 * Product price which will be displayed in your merchant panel and will help you to reconcile the transaction. It shall be decimal value (10,2). Example : 50.40
	 *
	 * @var float
	 */
	public $product_amount;
	
	/**
	 * The VAT included on the product price which will be displayed in your merchant panel and will help you to reconcile the transaction. It shall be decimal value (10,2). Example : 4.00
	 *
	 * @var float
	 */
	public $vat;
	
	/**
	 * Discount given on the invoice which will be displayed in your merchant panel and will help you to reconcile the transaction. It shall be decimal value (10,2). Example : 2.00
	 *
	 * @var float
	 */
	public $discount_amount;
	
	/**
	 * Any convenience fee imposed on the invoice which will be displayed in your merchant panel and will help you to reconcile the transaction. It shall be decimal value (10,2). Example : 3.00
	 *
	 * @var float
	 */
	public $convenience_fee;
	
	// Customized or Additional Parameters.
	
	/**
	 * Extra parameter to pass your meta data if it is needed. Not mandatory
	 *
	 * @var string
	 */
	public $value_a;
	
	/**
	 * Extra parameter to pass your meta data if it is needed. Not mandatory
	 *
	 * @var string
	 */
	public $value_b;
	
	/**
	 * Extra parameter to pass your meta data if it is needed. Not mandatory
	 *
	 * @var string
	 */
	public $value_c;
	
	/**
	 * Extra parameter to pass your meta data if it is needed. Not mandatory
	 *
	 * @var string
	 */
	public $value_d;
	
	private $defaults = [
		'emi_option'      => 0,
		'shipping_method' => 'NO',
		'product_profile' => 'general',
	];
	
	/**
	 * List of required field.
	 * @var array
	 */
	private $required = [
		'total_amount'     => '',
		'currency'         => '',
		'tran_id'          => '',
		'product_category' => '',
		'success_url'      => '',
		'fail_url'         => '',
		'cancel_url'       => '',
		'emi_option'       => '',
		'cus_name'         => '',
		'cus_email'        => '',
		'cus_add1'         => '',
		'cus_city'         => '',
		'cus_state'        => '',
		'cus_postcode'     => '',
		'cus_country'      => '',
		'cus_phone'        => '',
		'shipping_method'  => [
			[
				'value'    => 'yes',
				'required' => [
					'ship_name',
					'ship_add1',
					'ship_city',
					'ship_postcode',
					'ship_country',
				],
			],
		],
		'num_of_item'      => '',
		'ship_name'        => '',
		'product_name'     => '',
		'product_profile'  => [
			[
				'value'    => 'airline-tickets',
				'required' => [
					'hours_till_departure',
					'flight_type',
					'pnr',
					'journey_from_to',
					'third_party_booking',
					'product_type',
					'topup_number',
					'country_topup',
				],
			],
			[
				'value'    => 'travel-vertical',
				'required' => [
					'hotel_name',
					'length_of_stay',
					'check_in_time',
					'hotel_city',
				],
			],
			[
				'value'    => 'telecom-vertical',
				'required' => [
					'hotel_name',
					'length_of_stay',
					'check_in_time',
					'hotel_city',
				],
			],
		],
	];
	
	/**
	 * SSLCOMMERZ_Payment_Data constructor.
	 *
	 * @param array $data
	 *
	 * @throws SSLCOMMERZ_Exception
	 */
	public function __construct( array $data = [] ) {
		
		// Set defaults if not set.
		foreach ( $this->defaults as $k => $v ) {
			if ( ! isset( $data[ $k ] ) ) {
				$data[ $k ] = $v;
			}
		}
		
		// Check for required data.
		foreach ( $this->required as $k => $v ) {
			if ( ! isset( $data[ $k ] ) || ( isset( $data[ $k ] ) && $this->empty( $data[ $k ] ) ) ) {
				throw new SSLCOMMERZ_Exception( sprintf( 'Missing Mandatory Parameter "%s"', $k ) );
			}
			if ( is_array( $v ) ) {
				foreach( $v as $required ) {
					if ( $data[ $k ] === $required['value'] ) {
						foreach( $required['required'] as $r ) {
							if ( ! isset( $data[ $r ] ) || ( isset( $data[ $r ] ) && $this->empty( $data[ $r ] ) ) ) {
								throw new SSLCOMMERZ_Exception( sprintf( 'Missing Mandatory Parameter "%s"', $r ) );
							}
						}
					}
				}
			}
		}
		
		if ( isset( $data['cart'] ) && is_array( $data['cart'] ) ) {
			$data['cart'] = json_encode( $data['cart'] );
		}
		
		foreach ( $data as $k => $v ) {
			$this->{$k} = $v;
		}
	}
	
	/**
	 * To Array.
	 *
	 * @return array
	 */
	public function to_array() {
		
		$output = call_user_func( 'get_object_vars', $this );
		/**
		 * Direct call to get_object_vars return the protected (private as well) members too.
		 * But calling from outside only expose the public member.
		 *
		 * @see https://www.php.net/manual/en/function.get-object-vars.php#113435
		 */
		foreach ( $output as $k => $v ) {
			if ( $this->empty( $v ) ) {
				unset( $output[ $k ] );
			}
		}
		
		unset( $output['defaults'] );
		unset( $output['required'] );
		
		return $output;
	}
	
	/**
	 * Check if input is really empty.
	 *
	 * @param mixed $input
	 * @param bool $check_null
	 *
	 * @return bool
	 */
	private function empty( $input, $check_null = true ) {
		
		if ( is_object( $input ) ) {
			return 0 === count( (array) $input );
		}
		if ( is_array( $input ) ) {
			return 0 === count( $input );
		}
		
		return ( $check_null && null === $input ) ? true : ( '' === $input );
	}
}

class SSLCOMMERZ_GW {
	public $name;
	public $type;
	public $logo;
	public $gw;
	public $r_flag;
	public $redirectGatewayURL;
	
	public function __construct( array $gw_data ) {
		foreach ( $gw_data as $k => $v ) {
			$this->{$k} = $v;
		}
	}
}

class SSLCOMMERZ_Create_Session_Response extends SSLCOMMERZ_Response {
	public $APIConnect;
	/**
	 * API connectivity status.
	 * SUCCESS/FAILED
	 *
	 * @var string
	 */
	public $status;
	public $failedreason;
	public $sessionkey;
	public $gw;
	public $GatewayPageURL; // < redirect to this.
	public $redirectGatewayURL;
	public $redirectGatewayURLFailed;
	public $storeBanner;
	public $storeLogo;
	/**
	 * @var SSLCOMMERZ_GW[]
	 */
	public $desc;
	public $is_direct_pay_enable;
	
	public function __construct( $data ) {
		parent::__construct( $data );
		$this->desc = [];
		if ( isset( $data['desc'] ) && ! empty( $data['desc'] ) ) {
			foreach ( $data['desc'] as $gw ) {
				$this->desc[] = new SSLCOMMERZ_GW( $gw );
			}
		}
	}
}

class SSLCOMMERZ_IPN_Request extends SSLCOMMERZ_Response {
	public $APIConnect;
	/**
	 * VALID : A successful transaction.
	 * FAILED : Transaction is declined by customer's Issuer Bank.
	 * CANCELLED : Transaction is cancelled by the customer.
	 * UNATTEMPTED : Customer did not choose to pay any channel.
	 * EXPIRED : Payment Timeout.
	 * @var string
	 */
	public $status;
	public $tran_date;
	public $tran_id;
	public $val_id;
	public $amount;
	public $store_amount;
	public $card_type;
	public $card_no;
	public $currency;
	public $bank_tran_id;
	public $card_issuer;
	public $card_brand;
	public $card_issuer_country;
	public $card_issuer_country_code;
	public $currency_type;
	public $currency_amount;
	public $currency_rate;
	public $value_a;
	public $value_b;
	public $value_c;
	public $value_d;
	public $verify_sign;
	public $verify_key;
	public $risk_level;
	public $risk_title;
	public $verified = false;
	public $error;
	
	/**
	 * @param bool $array
	 *
	 * @return string|string[]
	 */
	public function getVerifyKey( $array = true ) {
		return true === $array ? explode( ',', $this->verify_key ) : $this->verify_key;
	}
}

class SSLCOMMERZ_Order_Validation_Response extends SSLCOMMERZ_Response {
	public $APIConnect;
	/**
	 * Transaction Status. This parameter needs to be checked before update your database as a successful transaction.
	 * VALID : A successful transaction.
	 * VALIDATED : A successful transaction but called by your end more than one.
	 * INVALID_TRANSACTION : Invalid validation id (val_id).
	 *
	 * @var string
	 */
	public $status;
	public $tran_date;
	public $tran_id;
	public $val_id;
	public $amount;
	public $store_amount;
	public $card_type;
	public $card_no;
	public $currency;
	public $bank_tran_id;
	public $card_issuer;
	public $card_brand;
	public $card_issuer_country;
	public $card_issuer_country_code;
	public $currency_type;
	public $currency_amount;
	public $currency_rate;
	public $emi_instalment;
	public $emi_amount;
	public $emi_description;
	public $emi_issuer;
	public $account_details;
	public $discount_percentage;
	public $discount_remarks;
	public $value_a;
	public $value_b;
	public $value_c;
	public $value_d;
	public $risk_level;
	public $risk_title;
	public $validated_on;
	public $gw_version;
}

class SSLCOMMERZ_Query_Transaction_Response extends SSLCOMMERZ_Response {
	public $APIConnect;
	/**
	 * Transaction Status. This parameter needs to be checked before update your database as a successful transaction.
	 * VALID : A successful transaction.
	 * VALIDATED : A successful transaction but called by your end more than one.
	 * PENDING : The transaction is still not completed and waiting to get the status.
	 * FAILED : The transaction is failed.
	 *
	 * @var string
	 */
	public $status;
	public $sessionkey;
	public $tran_date;
	public $tran_id;
	public $val_id;
	public $amount;
	public $store_amount;
	public $card_type;
	public $card_no;
	public $currency;
	public $bank_tran_id;
	public $card_issuer;
	public $card_brand;
	public $card_issuer_country;
	public $card_issuer_country_code;
	public $currency_type;
	public $currency_amount;
	public $currency_rate;
	public $emi_instalment;
	public $emi_amount;
	public $emi_description;
	public $emi_issuer;
	public $account_details;
	public $discount_percentage;
	public $discount_remarks;
	public $value_a;
	public $value_b;
	public $value_c;
	public $value_d;
	public $risk_level;
	public $risk_title;
	public $validated_on;
	public $gw_version;
}

class SSLCOMMERZ_Query_Transactions_Response extends SSLCOMMERZ_Response {
	public $APIConnect;
	public $no_of_trans_found;
	/**
	 * @var SSLCOMMERZ_Query_Transaction_Response[]
	 */
	public $element;
	
	public function __construct( $data ) {
		parent::__construct( $data );
		$this->element = [];
		if ( isset( $data['element'] ) && ! empty( $data['element'] ) ) {
			foreach ( $data['element'] as $trans ) {
				$this->element[] = new SSLCOMMERZ_Query_Transaction_Response( $trans );
			}
		}
	}
}

class SSLCOMMERZ_Refund_Response extends SSLCOMMERZ_Response {
	public $APIConnect;
	/**
	 * Will be returned only when the authentication is success and the value will be as below.
	 * success : Refund request is initiated successfully
	 * failed : Refund request is failed to initiate
	 * processing : The refund has been initiated already
	 *
	 * @var string
	 */
	public $status;
	public $bank_tran_id;
	public $trans_id;
	public $refund_ref_id;
	public $errorReason;
}

class SSLCOMMERZ_Query_Refund_Response extends SSLCOMMERZ_Response {
	public $APIConnect;
	/**
	 * Will be return only when the Authentication is success and the value will be as below.
	 * refunded : Refund request has been proceeded successfully
	 * processing : Refund request is under processing
	 * cancelled : Refund request has been proceeded successfully
	 *
	 * @var string
	 */
	public $status;
	public $bank_tran_id;
	public $tran_id;
	public $initiated_on;
	public $refunded_on;
	public $refund_ref_id;
}

// End of file SslCommerz.php.
