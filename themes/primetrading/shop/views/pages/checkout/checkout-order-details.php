<?php
if ( ! defined( 'BASEPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die();
}
?>
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
							<div class="checkout-step-total-left"><?= lang( 'total_items' ); ?></div>
							<div class="checkout-step-total-right subtotal"><?= $cart['subtotal']; ?></div>
						</div>
						<div class="checkout-step-right-single">
							<div class="checkout-step-total-left"><?= lang( 'sales_tax' ); ?></div>
							<div class="checkout-step-total-right total_item_tax"><?= $cart['total_item_tax']; ?></div>
						</div>
						<div class="checkout-step-right-single">
							<div class="checkout-step-total-left"><?= lang( 'total' ); ?></div>
							<div class="checkout-step-total-right cart_total"><?= $cart['total']; ?></div>
						</div>
						<?php if ($Settings->tax2 != false) { ?>
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
						<div class="checkout-step-right-single checkout-submit">
							<?= form_submit( 'add_order', lang( 'submit_order' ) ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- checkout-progress-sidebar -->
	</div>
<?php
// End of file checkout-order-details.php.
