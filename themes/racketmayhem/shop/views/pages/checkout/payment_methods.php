<?php defined( 'BASEPATH' ) or exit( 'No direct script access allowed' ); ?>
<div class="row">
	<div class="col-md-12">
		<?php if ( $sslcommerz->active ) { ?>
			<label class="payment-method">
				<input type="radio" name="payment_method" value="sslcommerz" id="sslcommerz" required checked>
				<div class="method-thumb"><img src="<?php echo $assets; ?>images/payments/ssl-payment.png" alt="Pay via SSLCOMMERZ"></div>
			</label>
		<?php } ?>
		<?php  if ( $paypal->active ) { ?>
			<label class="payment-method">
				<input type="radio" name="payment_method" value="paypal" id="paypal" required>
				<span><i class="fa fa-paypal margin-right-md"></i> <?= lang( 'paypal' ) ?></span>
			</label>
		<?php } ?>
		<?php if ( $skrill->active ) {?>
			<label class="payment-method">
				<input type="radio" name="payment_method" value="skrill" id="skrill" required>
				<span><i class="fa fa-credit-card-alt margin-right-md"></i> <?= lang( 'skrill' ) ?></span>
			</label>
		<?php } ?>
		<?php if ( $shop_settings->stripe ) { ?>
			<label class="payment-method">
				<input type="radio" name="payment_method" value="stripe" id="stripe" required>
				<span><i class="fa fa-cc-stripe margin-right-md"></i> <?= lang('stripe') ?></span>
			</label>
		<?php }  ?>
		<?php if ( ! empty( $shop_settings->bank_details ) ) { ?>
			<label class="payment-method">
				<input type="radio" name="payment_method" value="bank" id="bank" required>
				<span><i class="fa fa-bank margin-right-md"></i> <?= lang( 'bank_in' ) ?></span>
			</label>
		<?php }  ?>
		<?php if ( $cod->active ) { ?>
			<label class="payment-method">
				<input type="radio" name="payment_method" value="cod" id="cod" required>
				<span class="method-thumb"><img src="<?php echo $assets; ?>images/payments/cod-payment.png" alt="Cash On Delivery"></span>
			</label>
		<?php }  ?>
		<?php if ( ! empty( $authorize['api_login_id'] ) ) { ?>
			<label class="payment-method">
				<input type="radio" name="payment_method" value="authorize" id="authorize" required>
				<span class="method-thumb"><img src="<?= $assets . '/images/auth-logo.png' ?>" alt="Pay by Authorize"></span>
			</label>
		<?php } ?>
	</div>
	<div class="col-md-12 gateway-extras">
		<?php if ( ! empty( $authorize['api_login_id'] ) ) { ?>
		<div id="authorize_extra" class="clearfix authorize-extra gateway-extra" style="display: none;">
			<div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 well well-sm well_1" style="padding: 10px 15px 20px;">
				<?php include 'authorize_cc_fields.php'; ?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
<div class="clearfix"></div>
