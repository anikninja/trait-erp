<?php
$countries = array_merge( [ '' => 'Select Country' ], $countries );
?>
<div class="panel panel-default guest-step-01">
	<div class="checkout-step-header-wrap">
		<div class="checkout-step-header"><div class="step-num"><?= $step; ?></div><?= lang( 'guest_checkout' ); ?></div>
	</div>
	<div class="checkout-step-content-wrap">
		<input type="hidden" name="billing_zone" id="billing_zone" value="">
		<input type="hidden" name="billing_area" id="billing_area" value="">
		<input type="hidden" name="shipping_zone" id="shipping_zone" value="">
		<input type="hidden" name="shipping_area" id="shipping_area" value="">
		<div class="address-wrap">
			<div class="row">
				<input type="hidden" value="1" name="guest_checkout">
				<div class="col-sm-12">
					<div class="form-group">
						<?= lang('name', 'name'); ?> *
						<?= form_input('name', set_value('name'), 'class="form-control" id="name"'); ?>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<?= lang('company', 'company'); ?>
						<?= form_input('company', set_value('company'), 'class="form-control" id="company"'); ?>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<?= lang('email', 'email'); ?> *
						<?= form_input('email', set_value('email'), 'class="form-control" id="email"'); ?>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<?= lang('phone', 'phone'); ?> *
						<?= form_input('phone', set_value('phone'), 'class="form-control" id="phone"'); ?>
					</div>
				</div>
				<div class="col-sm-12 address-form-heading">
					<h5><strong><?= lang('billing_address'); ?></strong></h5>
					<input type="hidden" value="new" name="address">
					<hr>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<?= lang('line1', 'billing_line1'); ?> *
						<?= form_input('billing_line1', set_value('billing_line1'), 'class="form-control" id="billing_line1"'); ?>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<?= lang('line2', 'billing_line2'); ?>
						<?= form_input('billing_line2', set_value('billing_line2'), 'class="form-control" id="billing_line2"'); ?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<?= lang('country', 'billing_country'); ?> *
						<?= form_dropdown( 'billing_country', $countries, set_value( 'billing_country' ), 'class="form-control" id="billing_country"' ); ?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<?= lang('state', 'billing_state'); ?>
						<?php
						echo form_dropdown('billing_state', [], '', 'class="form-control mobile-device" id="billing_state" placeholder="Select State"');
//						if ($Settings->indian_gst) {
//							$billing_states = $this->gst->getIndianStates();
//							echo form_dropdown('billing_state', $billing_states, '', 'class="form-control selectpicker mobile-device" id="billing_state" title="Select"');
//						} else {
//							echo form_input('billing_state', '', 'class="form-control" id="billing_state"');
//						}
						?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<?= lang('city', 'billing_city'); ?> *
						<?= form_input('billing_city', set_value('billing_city'), 'class="form-control" id="billing_city"');
						//form_dropdown('billing_city', [], '', 'class="form-control mobile-device" id="billing_city" placeholder="Select City"');
						?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<?= lang('zip_code', 'billing_postal_code'); ?>
						<?= form_input('billing_postal_code', set_value('billing_postal_code'), 'class="form-control" id="billing_postal_code"'); ?>
					</div>
				</div>
				<!-- div class="col-sm-12 address-area-wrap">
					<div class="form-group">
						<?= lang('area', 'billing_area'); ?>
						<?= form_dropdown('billing_area', [], '', 'class="form-control" placeholder="Area" id="billing_area"'); ?>
					</div>
				</div -->
				<div class="col-sm-12 address-form-heading">
					<div class="checkbox bg pull-right" style="margin-top: 0; margin-bottom: 0;">
						<label>
							<input type="checkbox" name="same" value="1" id="same_as_billing" checked>
							<span><?= lang('same_as_billing') ?></span>
						</label>
					</div>
					<h5><strong><?= lang('shipping_address'); ?></strong></h5>
					<input type="hidden" value="new" name="address">
					<hr>
				</div>
				<div class="guest-shipping-address" style="display: none;">
					<div class="col-sm-12">
						<div class="form-group">
							<?= lang('line1', 'shipping_line1'); ?> *
							<?= form_input('shipping_line1', set_value('shipping_line1'), 'class="form-control" id="shipping_line1"'); ?>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<?= lang('line2', 'shipping_line2'); ?>
							<?= form_input('shipping_line2', set_value('shipping_line2'), 'class="form-control" id="shipping_line2"'); ?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<?= lang('country', 'shipping_country'); ?> *
							<?= form_dropdown( 'shipping_country', $countries, set_value('shipping_country'), 'class="form-control" id="shipping_country"' ); ?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<?= lang('state', 'shipping_state'); ?>
							<?php
							echo form_dropdown('shipping_state', [], '', 'class="form-control mobile-device" id="shipping_state" placeholder="Select State"');
//							if ($Settings->indian_gst) {
//								$billing_states = $this->gst->getIndianStates();
//								echo form_dropdown('shipping_state', $billing_states, '', 'class="form-control selectpicker mobile-device" id="shipping_state" title="Select"');
//							} else {
//								echo form_input('shipping_state', '', 'class="form-control" id="shipping_state"');
//							}
							?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<?= lang('city', 'shipping_city'); ?> *
							<?= form_input('shipping_city', set_value('shipping_city'), 'class="form-control" id="shipping_city"');
							//form_dropdown('shipping_city', [], '', 'class="form-control mobile-device" id="shipping_city" placeholder="Select City"');
							?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<?= lang('zip_code', 'shipping_postal_code'); ?>
							<?= form_input('shipping_postal_code', set_value('shipping_postal_code'), 'class="form-control" id="shipping_postal_code"'); ?>
						</div>
					</div>
					<!-- div class="col-sm-12 address-area-wrap">
						<div class="form-group">
							<?= lang('area', 'shipping_area'); ?>
							<?= form_dropdown('shipping_area', [], '', 'class="form-control" placeholder="Shipping Area" id="shipping_area"'); ?>
						</div>
					</div -->
					<div class="col-sm-12">
						<div class="form-group">
							<?= lang('phone', 'shipping_phone'); ?> *
							<?= form_input('shipping_phone', set_value('shipping_phone'), 'class="form-control" id="shipping_phone"'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
