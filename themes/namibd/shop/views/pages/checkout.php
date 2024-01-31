<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="breadcrumb">
	<div class="container">
		<div class="breadcrumb-inner">
			<ul class="list-inline list-unstyled">
				<li><a href="<?php echo site_url(); ?>">Home</a></li>
				<li class='active'>Checkout</li>
			</ul>
		</div><!-- /.breadcrumb-inner -->
	</div><!-- /.container -->
</div><!-- /.breadcrumb -->
<?php
if( ! $this->loggedIn ) {
	include __DIR__ . "/../user/login.php";
} else {
	?>
	<div class="body-content">
		<div class="container">
			<div class="checkout-box narrow-screen">
				<?php if ( ! $this->Staff ) { ?>
					<?php echo shop_form_open('order', 'class="validate"'); ?>
					
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12">
							<ul class='progress-step'>
								<li class="active current">Step 1</li>
								<li>Step 2</li>
								<li>Step 3</li>
							</ul>
						</div>
						<div class="col-xs-12 col-sm-8 col-md-8">
							<?php if ( empty( $addresses ) ) { ?>
								<div class="alert alert-warning alert-dismissable">
									<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
									<p><span class="fa fa-warning"></span> <?php echo lang( 'please_add_address_first' ); ?></p>
								</div>
							<?php } ?>
							<div class="panel-group checkout-steps">
								<fieldset id="step1">
									<?php include 'checkout/addresses.php'; ?>
									<div class="panel panel-default checkout-step-01">
										<div class="checkout-submit">
											<input type='button' name='next' class='next-button custom-button' value="Next">
										</div>
									</div>
								</fieldset>
								<fieldset id="step2">
									<?php include 'checkout/slot.php'; ?>
									<?php include 'checkout/shipping-methods.php'; ?>
									<div class="panel panel-default checkout-step-02">
										<div class="checkout-submit">
											<input type='button' name='previous' class='prev-button custom-button' value="Back">
											<input type='button' name='next' class='next-button custom-button' value="Next">
										</div>
									</div>
								</fieldset>
								<fieldset id="step3">
									<div class="checkout-step-header-wrap">
										<div class="checkout-step-header">Payment & Confirm</div>
									</div>
									<div class="panel panel-default checkout-step-03">
										<div class="checkout-step-content-wrap">
											<div class="comment-wrap">
												<?= form_textarea('comment', set_value('comment'), 'class="form-control" id="comment" style="height:100px;" placeholder="Any Special Instruction"'); ?>
											</div>
										</div>
										<div class="checkout-step-content-wrap">
											<div class="row">
												<?php include 'checkout/payment_methods.php'; ?>
												<div class="col-md-12 checkout-submit"><?php echo form_submit( 'add_order', lang( 'confirm_order' ) ); ?></div>
											</div>
										</div>
									</div>
									<!-- checkout-step-05  -->
									<div class="panel panel-default checkout-step-03">
										<div class="checkout-submit">
											<input type='button' name='previous' class='prev-button custom-button' value="Back">
										</div>
									</div>
								</fieldset>
							</div><!-- /.checkout-steps -->
						
						</div>
						<div class="col-xs-12 col-sm-4 col-md-4">
							<!-- checkout-progress-sidebar -->
							<div class="checkout-progress-sidebar ">
								<div class="panel-group">
									<div class="order-details">
										<div class="order-heading">
											<h4 class="unicase-checkout-title"><?= lang( 'your_order' ); ?></h4>
										</div>
										<div class="checkout-step-total-wrap">
											<div class="checkout-step-right-single">
												<div class="checkout-step-total-left"><?= lang( 'total_w_o_tax' ); ?></div>
												<div class="checkout-step-total-right subtotal"><?= $cart['subtotal']; ?></div>
											</div>
											<div class="checkout-step-right-single">
												<div class="checkout-step-total-left"><?= lang( 'product_tax' ); ?></div>
												<div class="checkout-step-total-right total_item_tax"><?= $cart['total_item_tax']; ?></div>
											</div>
											<div class="checkout-step-right-single">
												<div class="checkout-step-total-left"><?= lang( 'total' ); ?></div>
												<div class="checkout-step-total-right cart_total"><?= $cart['total']; ?></div>
											</div>
											<?php if ($Settings->tax2 !== false) { ?>
												<div class="checkout-step-right-single delivery-fee">
													<div class="checkout-step-total-left"><?= lang( 'order_tax' ); ?></div>
													<div class="checkout-step-total-right order_tax"><?= $cart['order_tax']; ?></div>
												</div>
												<?php
											} ?>
											<div class="checkout-step-right-single delivery-fee">
												<div class="checkout-step-total-left shipping_label" data-label="<?= lang( 'shipping_label' ); ?>"><?= sprintf( lang( 'shipping_label' ), $cart['shipping']['name'] ); ?></div>
												<div class="checkout-step-total-right shipping_cost"><?= $cart['shipping']['cost']; ?></div>
											</div>
											<div class="checkout-step-right-single total">
												<div class="checkout-step-total-left"><?= lang( 'grand_total' ); ?></div>
												<div class="checkout-step-total-right grand_total"><?= $cart['grand_total']; ?></div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- checkout-progress-sidebar -->
						</div>
					</div><!-- /.row -->
					<?php form_close(); ?>
				<?php } else { ?>
					<div class="alert alert-warning text-center" role="alert">
						<img style="margin-bottom: 50px" src="<?= $assets . 'images/purchase-not-allowed.png' ?>"/>
						<h2 class="alert-heading"><?= lang( 'staff_not_allowed' ); ?></h2>
						<p style="font-size: 18px"><?= lang( 'staff_not_allowed_details' ); ?></p>
						<p class="mb-0"></p>
					</div>
				<?php }
				?>
			</div><!-- /.checkout-box -->
		</div><!-- /.container -->
	</div><!-- /.body-content -->
	<?php
}
?>
<!-- Modal -->
<div class="modal fade" id="checkout-popup" tabindex="-1" role="dialog" aria-labelledby="checkout-popup" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="checkout-popup-content">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<div class="modal-body"></div>
		</div>
	</div>
</div>
