<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include 'breadcrumb.php'; ?>
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
							<!-- checkout-step-01  -->
							<div class="panel panel-default checkout-step-01">
								<div class="checkout-step-header-wrap">
									<div class="checkout-step-header"><div class="step-num">1</div>Delivery Address</div>
									<?php if ( count( $addresses ) < 6 && ! $this->Staff ) { ?>
									<div class="checkout-step-btn">
										<a href="#" id="add-new-address" class="update-address">+ Add Address</a>
									</div>
									<?php } ?>
								</div>
								<div class="checkout-step-content-wrap">
									<div class="row address-wrap">
										<?php
										$phones = '';
										if (!empty($addresses)) {
											foreach ($addresses as $address) {
												$phones .= sprintf(
													'<div class="phone col-sm-6 col-md-4 phone-%1$s">
                                                        <label>
                                                            <input type="radio" disabled name="phone" value="%1$s">
                                                            <div class="checkout-page-content">
                                                                <div class="content-header">%2$s</div>
                                                                <p class="content">%3$s</p>
                                                            </div>
                                                        </label>
                                                    </div>', $address->id, $address->title, $address->phone
												);
												?>
												<div class="address col-sm-6 col-md-4 address-<?php echo $address->id; ?>" data-address='<?php echo json_encode($address) ?>'>
													<label>
														<input type="radio" name="address" value="<?php echo $address->id; ?>">
														<div class="checkout-page-content">
															<div class="content-header"><?php echo $address->title; ?></div>
															<p class="content">
																<?= $address->line1; ?><br>
																<?= $address->line2; ?><br>
																<?= $address->city; ?>,
																<?= $address->state; ?> -
																<?= $address->postal_code; ?><br> <?= $address->country; ?>
															</p>
															<div class="checkout-step-content-edit">
																<a class="edit" href="#"><img src="<?php echo $assets; ?>images/edit-icon.png"></a>
																<a class="remove" href="#"><img src="<?php echo $assets; ?>images/times.png"></a>
															</div>
														</div>
													</label>
												</div>
												<?php
											}
										}
										?>
									</div>
								</div>
							</div>
							<!-- /checkout-step-01  -->
							<!-- checkout-step-02  -->
							<div class="panel panel-default checkout-step-02">
								<div class="checkout-step-header-wrap">
									<div class="checkout-step-header"><div class="step-num">2</div>Contact Number</div>
								</div>
								<div class="checkout-step-content-wrap">
									<div class="row phone-wrap"><?php echo $phones; ?></div>
								</div>
							</div>
							<!-- /checkout-step-02  -->
                            <!-- checkout-step-03  -->
                            <div class="panel panel-default checkout-step-03">
                                <div class="checkout-step-header-wrap">
                                    <div class="checkout-step-header">
                                        <div class="step-num">3</div>
										<?= lang('comment_any', 'comment'); ?>
                                    </div>
                                </div>
                                <div class="checkout-step-content-wrap">
                                    <div class="row comment-wrap">
										<?= form_textarea('comment', set_value('comment'), 'class="form-control" id="comment" style="height:100px;"'); ?>
                                    </div>
                                </div>
                            </div>
                            <!-- /checkout-step-03  -->
							<!-- checkout-step-04  -->
							<div class="panel panel-default checkout-step-04">
								<div class="checkout-step-header-wrap">
									<div class="checkout-step-header"><div class="step-num">4</div>Payment Option</div>
								</div>
								<div class="checkout-step-content-wrap">
									<div class="row">
										<?php include 'payment_methods.php'; ?>
										<div class="col-md-4 checkout-submit">
											<?php echo form_submit( 'add_order', lang( 'submit_order' ) ); ?>
										</div>
									</div>
								</div>
							</div>
							<!-- checkout-step-04  -->
						</div><!-- /.checkout-steps -->
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<!-- checkout-progress-sidebar -->
						<div class="checkout-progress-sidebar ">
							<div class="panel-group">
								<div class="order-details">
									<div class="order-heading">
										<h4 class="unicase-checkout-title">Your Order</h4>
									</div>
									<div class="checkout-step-total-wrap">
										<!--<div class="checkout-step-right-single product-price">
											<div class="checkout-step-total-left">1 x Apples | 2 lb</div>
											<div class="checkout-step-total-right">$1.6</div>
										</div>
	
										<div class="checkout-step-right-single product-price">
											<div class="checkout-step-total-left">1 x Apples | 2 lb</div>
											<div class="checkout-step-total-right">$1.6</div>
										</div>-->
										<?php
										$total     = $this->rerp->convertMoney($this->cart->total(), false, false);
										$shipping  = $this->rerp->convertMoney($this->cart->shipping(), false, false);
										$order_tax = $this->rerp->convertMoney($this->cart->order_tax(), false, false);
										?>
										<div class="checkout-step-right-single"><!-- add .sub-total when you will add cart item in above area -->
											<div class="checkout-step-total-left"><?php echo lang('total_w_o_tax'); ?></div>
											<div class="checkout-step-total-right"><?php echo $this->rerp->convertMoney($this->cart->total() - $this->cart->total_item_tax()); ?></div>
										</div>
										<div class="checkout-step-right-single">
											<div class="checkout-step-total-left"><?= lang('product_tax'); ?></div>
											<div class="checkout-step-total-right"><?= $this->rerp->convertMoney($this->cart->total_item_tax()); ?></div>
										</div>
										<div class="checkout-step-right-single">
											<div class="checkout-step-total-left"><?= lang('total'); ?></div>
											<div class="checkout-step-total-right"><?= $this->rerp->formatMoney( $total, $selected_currency->symbol ); ?></div>
										</div>
										<?php if ($Settings->tax2 !== false) { ?>
											<div class="checkout-step-right-single delivery-fee">
												<div class="checkout-step-total-left"><?php echo lang('order_tax'); ?></div>
												<div class="checkout-step-total-right"><?php echo $this->rerp->formatMoney($order_tax, $selected_currency->symbol); ?></div>
											</div>
											<?php
										} ?>
										<div class="checkout-step-right-single delivery-fee">
											<div class="checkout-step-total-left"><?= lang('shipping'); ?></div>
											<div class="checkout-step-total-right"><?= $this->rerp->formatMoney($shipping, $selected_currency->symbol); ?></div>
										</div>
										<div class="checkout-step-right-single total">
											<div class="checkout-step-total-left"><?= lang('grand_total'); ?></div>
											<div class="checkout-step-total-right"><?= $this->rerp->formatMoney(($this->rerp->formatDecimal($total) + $this->rerp->formatDecimal($order_tax) + $this->rerp->formatDecimal($shipping)), $selected_currency->symbol); ?></div>
										</div>
										<div class="checkout-step-right-single checkout-submit">
											<?php echo form_submit( 'add_order', lang( 'submit_order' ) ); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- checkout-progress-sidebar -->
					</div>
				</div><!-- /.row -->
				<?php echo form_close(); ?>
			<?php } else { ?>
				<div class="alert alert-warning text-center" role="alert">
					<img style="margin-bottom: 50px" src="<?= $assets . 'images/purchase-not-allowed.png' ?>"/>
					<h2 class="alert-heading">Staff not allowed to purchase</h2>
					<p style="font-size: 18px">Seems you are trying to buy from staff account. If you really want to buy from e-commerce portal then you have to register & login as customer</p>
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
