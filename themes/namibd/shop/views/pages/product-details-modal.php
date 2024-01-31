<!-- Product Modal Start -->
<div class="modal fade" id="productDetailsModal" tabindex="-1" role="dialog" aria-labelledby="productName">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M13 1.96814L1 13.9522" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
					<path d="M1 1.96814L13 13.9522" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
				</svg>
			</button>
			<div class="modal-body">
				<div class="main-content detail-block"></div>
				<div class="modal-footer company-details-block">
					<div class="row">
						<div class="col-md-7">
							<div class="company-content">
								<img src="<?php echo $assets; ?>images/logo123.png" alt="logo">
								<p><?php echo $shop_settings->description; ?></p>
							</div>
						</div>
						<div class="col-md-5">
							<div class="payment-area">
								<div class="payment-options">
									<span>Pay With </span> <img src="<?php echo $assets; ?>images/payment.png" alt="payment gateway">
								</div>
								<div class="support-area">
									<a href="tel:<?php echo !empty($shop_settings->phone) ? str_replace(' ', '', $shop_settings->phone) : ''; ?>"><img src="<?php echo $assets; ?>images/call-icon.png" alt="call icon"><?php echo !empty($shop_settings->phone) ? $shop_settings->phone : ''; ?></a>
									<p>or email <a href="mailto:<?php echo !empty($shop_settings->email) ? $shop_settings->email : ''; ?>"><?php echo !empty($shop_settings->email) ? $shop_settings->email : ''; ?></a></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Product Modal End-->
