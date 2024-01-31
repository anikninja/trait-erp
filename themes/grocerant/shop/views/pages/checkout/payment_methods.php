<?php defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );
/**
 *
 * @var $wallet array|bool
 */
?>
<div class="payment_methods col-md-12">
	<?php if ( $wallet && $wallet['_usable'] ) { ?>
    <div class="wallet_balance payment-method-wrap">
        <label class="payment-method">
            <input type="checkbox" name="use_wallet_credit" value="1">
            <div class="method-thumb">
				<?php
				printf(
					'Use %s from my wallet. (Current balance: %s)',
					$wallet['usable'],
					$wallet['balance']
				);
				?>
            </div>
        </label>
    </div>
	<?php } ?>
	<?php if ( $sslcommerz->active ) { ?>
    <div class="sslcommerz payment-method-wrap">
		<label class="payment-method">
			<input type="radio" name="payment_method" value="sslcommerz" id="sslcommerz" required checked>
			<div class="method-thumb">
				<img src="<?php echo $assets; ?>images/payments/ssl-payment.png" alt="<?= lang( 'sslcommerz' ); ?>">
			</div>
		</label>
    </div>
	<?php } ?>
	<?php  if ( $paypal->active ) { ?>
    <div class="paypal payment-method-wrap">
		<label class="payment-method">
			<input type="radio" name="payment_method" value="paypal" id="paypal" required>
			<span>
				<i class="fa fa-paypal margin-right-md"></i> <?= lang( 'paypal' ) ?>
			</span>
		</label>
    </div>
	<?php } ?>
	<?php if ( $skrill->active ) {?>
    <div class="skrill payment-method-wrap">
		<label class="payment-method">
			<input type="radio" name="payment_method" value="skrill" id="skrill" required>
			<span>
				<i class="fa fa-credit-card-alt margin-right-md"></i> <?= lang( 'skrill' ) ?>
			</span>
		</label>
    </div>
	<?php } ?>
	<?php if ( $shop_settings->stripe ) { ?>
    <div class="stripe payment-method-wrap">
		<label class="payment-method">
			<input type="radio" name="payment_method" value="stripe" id="stripe" required>
			<span>
				<i class="fa fa-cc-stripe margin-right-md"></i> <?= lang('stripe') ?>
			</span>
		</label>
    </div>
	<?php } ?>
	<?php if ( ! empty( $shop_settings->bank_details ) ) { ?>
    <div class="bank payment-method-wrap">
		<label class="payment-method">
			<input type="radio" name="payment_method" value="bank" id="bank" required>
			<span>
				<i class="fa fa-bank margin-right-md"></i> <?= lang( 'bank_in' ) ?>
			</span>
		</label>
    </div>
	<?php } ?>
	<?php if ( $cod->active ) { ?>
    <div class="cod payment-method-wrap">
		<label class="payment-method">
			<input type="radio" name="payment_method" value="cod" id="cod" required>
			<div class="method-thumb">
				<img src="<?php echo $assets; ?>images/payments/cod-payment.png" alt="<?= lang( 'cod' ); ?>">
			</div>
		</label>
    </div>
	<?php } ?>
</div>
