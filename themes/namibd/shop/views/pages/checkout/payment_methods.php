<?php defined( 'BASEPATH' ) or exit( 'No direct script access allowed' ); ?>
<div class="col-md-12">
	<?php
	$wallet = Erp_Wallet::get_user_wallet( $this->session->userdata( 'user_id' ) );
	if ( $wallet->getAmount() > 0 ) {
		$use_rate = (float) $this->shop_settings->wallet_percentage_cart;
		$checkout_amount = (float) str_replace( [ '৳', '$', ',' ], '', $cart['grand_total'] );
		$wallet_balance  = (float) $wallet->getAmount();
		$use_amount      = ( ( $checkout_amount * $use_rate ) / 100 );
		$amount = $use_amount > $wallet_balance  ? $wallet_balance : $use_amount;

	    ?>
    <div class="payment-method-wrap">
        <label class="payment-method">
            <input type="checkbox" name="use_wallet_credit" value="1">
            <div class="method-thumb">
				<?php printf( 'Use %s from my wallet. (Current balance: %s)', $this->rerp->convertMoney( $amount ), $this->rerp->convertMoney( $wallet_balance ) ); ?>
            </div>
        </label>
    </div>
	<?php } ?>
	<?php if ( $sslcommerz->active ) { ?>
    <div class="payment-method-wrap">
		<label class="payment-method">
			<input type="radio" name="payment_method" value="sslcommerz" id="sslcommerz" required checked>
			<div class="method-thumb">
				<img src="<?php echo $assets; ?>images/payments/ssl-payment.png" alt="<?= lang( 'sslcommerz' ); ?>">
			</div>
		</label>
    </div>
	<?php } ?>
	<?php  if ( $paypal->active ) { ?>
    <div class="payment-method-wrap">
		<label class="payment-method">
			<input type="radio" name="payment_method" value="paypal" id="paypal" required>
			<span>
				<i class="fa fa-paypal margin-right-md"></i> <?= lang( 'paypal' ) ?>
			</span>
		</label>
    </div>
	<?php } ?>
	<?php if ( $skrill->active ) {?>
    <div class="payment-method-wrap">
		<label class="payment-method">
			<input type="radio" name="payment_method" value="skrill" id="skrill" required>
			<span>
				<i class="fa fa-credit-card-alt margin-right-md"></i> <?= lang( 'skrill' ) ?>
			</span>
		</label>
    </div>
	<?php } ?>
	<?php if ( $shop_settings->stripe ) { ?>
    <div class="payment-method-wrap">
		<label class="payment-method">
			<input type="radio" name="payment_method" value="stripe" id="stripe" required>
			<span>
				<i class="fa fa-cc-stripe margin-right-md"></i> <?= lang('stripe') ?>
			</span>
		</label>
    </div>
	<?php }  ?>
	<?php if ( ! empty( $shop_settings->bank_details ) ) { ?>
    <div class="payment-method-wrap">
		<label class="payment-method">
			<input type="radio" name="payment_method" value="bank" id="bank" required>
			<span>
				<i class="fa fa-bank margin-right-md"></i> <?= lang( 'bank_in' ) ?>
			</span>
		</label>
    </div>
	<?php }  ?>
	<?php if ( $cod->active ) { ?>
    <div class="payment-method-wrap">
		<label class="payment-method">
			<input type="radio" name="payment_method" value="cod" id="cod" required>
			<div class="method-thumb">
				<img src="<?php echo $assets; ?>images/payments/cod-payment.png" alt="<?= lang( 'cod' ); ?>">
			</div>
		</label>
    </div>
	<?php }  ?>
</div>
