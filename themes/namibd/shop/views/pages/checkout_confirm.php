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
				<?php echo shop_form_open( 'order', 'class="validate"' ); ?>
				<div class="row">
					<div class="col-xs-12 col-sm-8 col-md-8">
						<?php if ( empty( $addresses ) ) { ?>
						<div class="alert alert-warning alert-dismissable">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<p><span class="fa fa-warning"></span> <?php echo lang( 'please_add_address_first' ); ?></p>
						</div>
						<?php } ?>
						<div class="panel-group checkout-steps">
							<?php include 'checkout/addresses.php'; ?>
							<?php include 'checkout/contact-numbers.php'; ?>
							<?php include 'checkout/slot.php'; ?>
							<?php include 'checkout/shipping-methods.php'; ?>
							<?php include 'checkout/order-note.php'; ?>
							<!-- checkout-step-05  -->
                            <div class="panel panel-default checkout-step-04">
                                <div class="checkout-step-header-wrap">
                                    <div class="checkout-step-header"><div class="step-num">6</div>Payment Option</div>
                                </div>
                                <div class="checkout-step-content-wrap">
                                    <div class="row">
										<?php include 'checkout/payment_methods.php'; ?>
                                        <div class="col-md-4 checkout-submit"><?php echo form_submit( 'add_order', lang( 'submit_order' ) ); ?></div>
                                    </div>
                                </div>
                            </div>
							<!-- checkout-step-05  -->
						</div><!-- /.checkout-steps -->
					</div>
					<?php require 'checkout/checkout-order-details.php'?>
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
