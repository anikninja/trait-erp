<?php defined( 'BASEPATH' ) or exit( 'No direct script access allowed' ); ?>
<div class="col-md-8">
	<?php if ( $sslcommerz->active ) { ?>
		<label class="payment-method">
			<input type="radio" name="payment_method" value="sslcommerz" id="sslcommerz" required checked>
			<div class="method-thumb">
				<img src="<?php echo $assets; ?>images/payments/ssl-payment.png" alt="Pay via SSLCOMMERZ">
			</div>
		</label>
	<?php } ?>
	<?php  if ( $paypal->active ) { ?>
		<label class="payment-method">
			<input type="radio" name="payment_method" value="paypal" id="paypal" required>
			<span>
				<i class="fa fa-paypal margin-right-md"></i> <?= lang( 'paypal' ) ?>
			</span>
		</label>
	<?php } ?>
	<?php if ( $skrill->active ) {?>
		<label class="payment-method">
			<input type="radio" name="payment_method" value="skrill" id="skrill" required>
			<span>
				<i class="fa fa-credit-card-alt margin-right-md"></i> <?= lang( 'skrill' ) ?>
			</span>
		</label>
	<?php } ?>
	<?php if ( $shop_settings->stripe ) { ?>
		<label class="payment-method">
			<input type="radio" name="payment_method" value="stripe" id="stripe" required>
			<span>
				<i class="fa fa-cc-stripe margin-right-md"></i> <?= lang('stripe') ?>
			</span>
		</label>
	<?php }  ?>
	<label class="payment-method">
		<input type="radio" name="payment_method" value="bank" id="bank" required>
		<span>
			<i class="fa fa-bank margin-right-md"></i> <?= lang( 'bank_in' ) ?>
		</span>
	</label>
	<label class="payment-method">
		<input type="radio" name="payment_method" value="cod" id="cod" required>
		<div class="method-thumb">
			<img src="<?php echo $assets; ?>images/payments/cod-payment.png" alt="Cash On Delivery">
		</div>
	</label>
</div>
