<?php if ( $hasShippingMethod ) { ?>
	<!-- checkout step 04 -->
	<div class="panel panel-default checkout-step-03">
		<div class="checkout-step-header-wrap">
			<div class="checkout-step-header"><div class="step-num"><?= $step; ?></div>Shipping Method</div>
		</div>
		<div class="checkout-step-content-wrap">
			<div class="row shipping-method-wrap">
				<div class="shipping-method">
					<div class="no-shipping-method"><span><?php
						if ( $this->loggedIn ) {
							echo 'Add/Select An Address';
						} else {
							echo 'Update Billing/Shipping Address';
						}
					?></span></div>
				</div>
				<script type="text/html" id="shipping_method-tmpl">
					<div class="shipping_method col-sm-6 col-md-4">
						<label>
							<input type="radio" name="shipping_method" required>
							<div class="checkout-page-content">
								<p class="content"></p>
							</div>
						</label>
					</div>
				</script>
				<div class="shipping-method-desc col-sm-12"></div>
			</div>
		</div>
	</div>
	<!-- checkout step 04 -->
<?php } ?>
