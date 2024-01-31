<?php
defined('BASEPATH') or exit('No direct script access allowed');
include 'breadcrumb.php';
$checkoutSteps = [];
if ( $this->loggedIn ) {
	$checkoutSteps = [
		'',
		'checkout/addresses.php',
		'checkout/contact-numbers.php',
	];
} else {
	$checkoutSteps = [
		'checkout/login.php',
		'checkout/guest.php'
	];
}
$checkoutSteps = array_merge( $checkoutSteps, [
		'checkout/slot.php',
		'checkout/shipping-methods.php',
		'checkout/order-note.php',
] );
?>
<div class="body-content">
	<div class="container">
		<div class="checkout-box narrow-screen checkout <?= $this->loggedIn ? '' : 'guest-checkout'; ?>">
			<?php if ( $this->Staff ) {
				include 'checkout/staff.php';
			} else { ?>
				<?php echo shop_form_open( 'order', 'class="checkout"' ); ?>
				<div class="row">
					<div class="col-xs-12 col-sm-8 col-md-8">
						<div class="panel-group checkout-steps">
							<?php foreach ( $checkoutSteps as $step => $template ) {
								if ( $template ) {
									/** @noinspection PhpIncludeInspection */
									include $template;
								}
							} ?>
							<div class="panel panel-default checkout-payment">
								<div class="checkout-step-header-wrap">
									<div class="checkout-step-header"><div class="step-num"><?= $step + 1; ?></div>Payment Option</div>
								</div>
								<div class="checkout-step-content-wrap">
									<?php include 'checkout/payment_methods.php'; ?>
									<div class="row">
										<div class="col-md-4 col-md-offset-8 checkout-submit m-t-15">
											<?php if( $this->loggedIn ) {
												echo form_submit( 'add_order', lang( 'submit_order' ) );
											} else {
												echo form_submit('guest_order', lang('submit') );
											} ?>
											<?php ; ?>
										</div>
									</div>
								</div>
							</div>
						</div><!-- /.checkout-steps -->
					</div>
					<?php include "checkout/checkout-order-details.php"; ?>
				</div><!-- /.row -->
				<?php echo form_close(); ?>
			<?php }
			?>
		</div><!-- /.checkout-box -->
	</div><!-- /.container -->
</div><!-- /.body-content -->
