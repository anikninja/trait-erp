<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Pay_model
 * @property Authorize_net $authorize_net
 */
class Pay_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addPayment($data)
    {
        if ($this->db->insert('payments', $data)) {
            $this->site->updateReference('pay');
            $this->site->syncSalePayments($data['sale_id']);
            return true;
        }
        return false;
    }

    public function getCompanyByID($id)
    {
        return $this->db->get_where('companies', ['id' => $id])->row();
    }

    public function getPaypalSettings()
    {
        return $this->db->get_where('paypal', ['id' => 1])->row();
    }

    public function getSslcommerzSettings()
    {
        return $this->db->get_where('sslcommerz', ['id' => 1])->row();
    }

    public function getSaleByID($id)
    {
        return $this->db->get_where('sales', ['id' => $id])->row();
    }

    public function getSaleItems($sale_id)
    {
        $this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.image, products.details as details, product_variants.name as variant')
            ->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
            ->where('sale_id', $sale_id)->group_by('sale_items.id')->order_by('id', 'asc');
        return $this->db->get('sale_items')->result();
    }

    public function getSettings()
    {
        return $this->db->get('settings')->row();
    }

    public function getSkrillSettings()
    {
        return $this->db->get_where('skrill', ['id' => 1])->row();
    }

    public function updateStatus($id, $status, $note = null)
    {
        $sale  = $this->getSaleByID($id);
        $items = $this->getSaleItems($id);
        if ($note) {
            $note = $sale->note . '<p>' . $note . '</p>';
        }
        $cost = [];
        if ($status == 'completed' && $status != $sale->sale_status) {
            foreach ($items as $item) {
                $items_array[] = (array) $item;
            }
            $cost = $this->site->costing($items_array);
        }

        if ($this->db->update('sales', ['sale_status' => $status, 'note' => $note], ['id' => $id])) {
	        if ( $status == 'completed' && $status != $sale->sale_status ) {
                foreach ($items as $item) {
                    $item = (array) $item;
                    if ($this->site->getProductByID($item['product_id'])) {
                        $item_costs = $this->site->item_costing($item);
                        foreach ($item_costs as $item_cost) {
                            $item_cost['sale_item_id'] = $item['id'];
                            $item_cost['sale_id']      = $id;
                            $item_cost['date']         = date('Y-m-d', strtotime($sale->date));
                            if (!isset($item_cost['pi_overselling'])) {
                                $this->db->insert('costing', $item_cost);
                            }
                        }
                    }
                }
            }
	
	        if ( ! empty( $cost ) ) {
		        $this->site->syncPurchaseItems( $cost );
            }
	        $this->site->syncQuantity( $id );
	        $this->rerp->update_award_points( $sale->grand_total, $sale->customer_id );
            return true;
        }
        return false;
    }
	
	public function pay_authorize( $payment ) {
		if ( $inv = $this->getSaleByID( $payment['sale_id'] ) ) {
			$authorize = $this->shop_model->getAuthorizeSettings();
			if ( ! empty( $authorize['api_login_id'] ) && ( ( $inv->grand_total - $inv->paid ) > 0 ) ) {
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
				$authorize_arr['x_last_name'] = $last_name;
				$pay_result = $this->authorize( $authorize_arr );
				if ( ! isset( $pay_result['error'] ) ) {
					if ( $inv = $this->pay_model->getSaleByID( $payment['sale_id'] ) ) {
						$mb_amount = $inv->grand_total - $inv->paid;
						$reference = $inv->reference_no;
						$payment_data = [
							'date' => date('Y-m-d H:i:s'),
							'sale_id' => $payment['sale_id'],
							'reference_no' => $inv->reference_no,
							'amount' => $mb_amount,
							'paid_by' => 'authorize',
							'transaction_id' => $pay_result['transaction_id'],
							'type' => 'received',
							'note' => $this->default_currency->code . ' ' . $mb_amount . ' had been paid for the Sale Reference No ' . $inv->reference_no,
						];
						if ( $this->addPayment( $payment_data ) ) {
							$customer = $this->site->getCompanyByID( $inv->customer_id );
							$this->updateStatus( $inv->id, 'completed' );
							
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
							$message = $this->parser->parse_string($msg, $parse_data);
							$this->rerp->log_payment('SUCCESS', 'Payment has been made for Sale Reference #' . $reference . ' via Authorize (' . $pay_result['transaction_id'] . ').', json_encode($payment_data));
							try {
								$this->rerp->send_email( ( $customer->email ), 'Payment has been made for Sale Reference # ' . $inv->reference_no, $message);
							} catch (Exception $e) {
								$this->rerp->log_payment( 'ERROR', 'Email Notification Failed: ' . $e->getMessage() );
							}
							$result['success'] = 1;
							$result['success_msg'] = lang('payment_added');
							if ($inv->shop) {
								$this->load->library('sms');
								$this->sms->paymentReceived($inv->id, $payment_data['reference_no'], $payment_data['amount']);
							}
						}
					} else {
						$this->rerp->log_payment('ERROR', 'Payment failed for via Authorize.', json_encode($pay_result['error']));
						$result['error'] = lang('payment_failed');
					}
				} else {
					$result['error'] = lang('payment_failed') . '<p class="text-danger">' . $pay_result['error'] . '</p>';
				}
				return $result;
			}
		}
	}
	
	public function authorize( $authorize_data ) {
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
				'error' => $this->authorize_net->getError(),
			];
		}
	}
}
