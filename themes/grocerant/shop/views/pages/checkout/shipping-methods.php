<?php if ( $hasShippingMethod ) { ?>
    <div class="checkout-step-header-wrap">
        <div class="checkout-step-header">
            Shipping Method
        </div>
    </div>
	<div class="alert alert-info" role="alert">
		<i class="fa fa-info-circle" aria-hidden="true"></i> Urgent Delivery Please Call Helpline No - <a href="tel:01944-996633">01944-996633</a>/<a href="tel:01944-663399">01944-663399</a></div>
    <div class="panel panel-default checkout-step-03">
		<div class="checkout-step-content-wrap">
			<div class="row shipping-method-wrap">
				<div class="shipping-method"></div>
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
