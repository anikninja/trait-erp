<?php defined( 'BASEPATH' ) or exit( 'No direct script access allowed' ); ?>
<?php if ( $this->loggedIn ) { ?>
	<hr>
<?php } ?>
<h5><strong><?= lang( 'payment_method' ); ?></strong></h5>
<?php if ( ! $this->loggedIn ) { ?>
	<hr>
<?php } ?>
<div class="checkbox bg">
	<?php if ( $sslcommerz->active ) { ?>
		<label style="display: inline-block; width: auto;">
			<input type="radio" name="payment_method" value="sslcommerz" id="sslcommerz" required="required">
			<span>
				<i class="fa fa-money margin-right-md"></i> <?= lang( 'sslcommerz' ) ?>
			</span>
		</label>
	<?php } ?>
	<?php if ( $paypal->active ) { ?>
		<label style="display: inline-block; width: auto;">
			<input type="radio" name="payment_method" value="paypal" id="paypal" required="required">
			<span>
				<i class="fa fa-paypal margin-right-md"></i> <?= lang( 'paypal' ) ?>
			</span>
		</label>
	<?php } ?>
	<?php if ( $skrill->active ) {?>
		<label style="display: inline-block; width: auto;">
			<input type="radio" name="payment_method" value="skrill" id="skrill" required="required">
			<span>
				<i class="fa fa-credit-card-alt margin-right-md"></i> <?= lang( 'skrill' ) ?>
			</span>
		</label>
	<?php } ?>
	<?php if ( $shop_settings->stripe ) { ?>
		<label style="display: inline-block; width: auto;">
			<input type="radio" name="payment_method" value="stripe" id="stripe" required="required">
			<span>
				<i class="fa fa-cc-stripe margin-right-md"></i> <?= lang('stripe') ?>
			</span>
		</label>
	<?php } ?>
	<label style="display: inline-block; width: auto;">
		<input type="radio" name="payment_method" value="bank" id="bank" required="required">
		<span>
			<i class="fa fa-bank margin-right-md"></i> <?= lang( 'bank_in' ) ?>
		</span>
	</label>
	<label style="display: inline-block; width: auto;">
		<input type="radio" name="payment_method" value="cod" id="cod" required="required">
		<span>
			<i class="fa fa-money margin-right-md"></i> <?= lang( 'cod' ) ?>
		</span>
	</label>
</div>
