<?php
$billing_cc     = set_value( 'billing_country' );
$billing_sc     = set_value( 'billing_state' );
$billing_city   = set_value( 'billing_city' );
$billing_zip    = set_value( 'billing_postal_code' );
$billing_states = [];
$billing_cities = [];
if ( ! $billing_cc ) {
	$billing_cc = ! empty( $countries ) && count( $countries ) == 1 ? array_key_first( $countries ) : '';
}
//if ( $billing_cc ) {
//	$billing_states = $this->shop_model->getShippingStates( $billing_cc, true );
//}
//if ( ! $billing_sc ) {
//	$billing_sc = ! empty( $billing_states ) && count( $billing_states ) == 1 ? array_key_first( $billing_states ) : '';
//}
//if ( $billing_cc && $billing_sc ) {
//	$billing_cities = $this->shop_model->getShippingCities( $billing_cc, $billing_sc );
//}
//if ( ! $billing_city ) {
//	$billing_city = ! empty( $billing_cities ) && count( $billing_cities ) == 1 ? $billing_cities[0] : '';
//}

$billing_zone  = set_value( 'billing_zone' );
$billing_area  = set_value( 'billing_area' );
$billing_areas = [];
//if ( $billing_cc && $billing_sc && $billing_city ) {
//	$zone = $this->shop_model->getShippingZones( $billing_cc, $billing_sc, $billing_city, $billing_zip, 'id' );
//	$billing_zone = $zone ? $zone[0]->id : '';
//	$_billing_areas = $this->shop_model->getShippingAreas( $billing_cc, $billing_sc, $billing_city, $billing_zip );
//	if ( ! empty( $_billing_areas ) ) {
//		foreach ( $_billing_areas as $area ) {
//			$billing_areas[ $area->id ] = $area->name;
//		}
//	}
//
//}
//if ( ! $billing_area ) {
//	$billing_area = ! empty( $billing_areas ) && count( $billing_areas ) == 1 ? array_key_first( $billing_areas ) : '';
//}

$shipping_cc     = set_value( 'shipping_country' );
$shipping_sc     = set_value( 'shipping_state' );
$shipping_city   = set_value( 'shipping_city' );
$shipping_zip    = set_value( 'shipping_postal_code' );
$shipping_states = [];
$shipping_cities = [];
if ( ! $shipping_cc ) {
	$shipping_cc = ! empty( $countries ) && count( $countries ) == 1 ? array_key_first( $countries ) : '';
}
//if ( $shipping_cc ) {
//	$shipping_states = $this->shop_model->getShippingStates( $shipping_cc, true );
//}
//if ( ! $shipping_sc ) {
//	$shipping_sc = ! empty( $shipping_states ) ? array_key_first( $shipping_states ) : '';
//}
//if ( $shipping_cc && $shipping_sc ) {
//	$shipping_cities = $this->shop_model->getShippingCities( $shipping_cc, $shipping_sc );
//}
//if ( ! $shipping_city ) {
//	$shipping_city = ! empty( $shipping_cities ) ? $shipping_cities[0] : '';
//}

$shipping_zone  = set_value( 'shipping_zone' );
$shipping_area  = set_value( 'shipping_area' );
$shipping_areas = [];
//if ( $shipping_cc && $shipping_sc && $shipping_city ) {
//	$zone = $this->shop_model->getShippingZones( $shipping_cc, $shipping_sc, $shipping_city, $shipping_zip, 'id' );
//	$shipping_zone = $zone ? $zone[0]->id : '';
//	$_shipping_areas = (array) $this->shop_model->getShippingAreas( $shipping_cc, $shipping_sc, $shipping_city, $shipping_zip );
//	if ( ! empty( $_shipping_areas ) ) {
//		foreach ( $_shipping_areas as $area ) {
//			$shipping_areas[ $area->id ] = $area->name;
//		}
//	}
//}
//if ( ! $shipping_area ) {
//	$shipping_area = ! empty( $shipping_areas ) && count( $shipping_areas ) == 1 ? array_key_first( $shipping_areas ) : '';
//}
?>
<div class="panel panel-default guest-step-01">
	<div class="checkout-step-header-wrap">
		<div class="checkout-step-header"><div class="step-num"><?= $step; ?></div>Guest Checkout Form</div>
	</div>
	<div class="checkout-step-content-wrap">
		<div class="address-wrap">
			<div class="row">
				<input type="hidden" value="1" name="guest_checkout">
				<div class="col-sm-12">
					<div class="form-group">
						<?= lang('name', 'name'); ?> *
						<?= form_input('name', set_value('name'), 'class="form-control" id="name" required="required"'); ?>
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
						<?= form_input('email', set_value('email'), 'class="form-control" id="email" required="required"'); ?>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<?= lang('phone', 'phone'); ?> *
						<?= form_input('phone', set_value('phone'), 'class="form-control" id="phone" required="required"'); ?>
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
						<?= form_input('billing_line1', set_value('billing_line1'), 'class="form-control" id="billing_line1" required="required"'); ?>
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
						<?= form_dropdown( 'billing_country', $countries, $billing_cc, 'class="form-control" id="billing_country" required="required"' ); ?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<?= lang('state', 'billing_state'); ?>
						<?php
						echo form_dropdown('billing_state', $billing_states, '', 'data-selected="' . $billing_sc . '" class="form-control mobile-device" id="billing_state" placeholder="Select State" required="required"');
//						if ($Settings->indian_gst) {
//							$billing_states = $this->gst->getIndianStates();
//							echo form_dropdown('billing_state', $billing_states, '', 'class="form-control selectpicker mobile-device" id="billing_state" title="Select" required="required"');
//						} else {
//							echo form_input('billing_state', '', 'class="form-control" id="billing_state"');
//						}
						?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<?= lang('city', 'billing_city'); ?> *
						<?= form_dropdown('billing_city', $billing_cities, '', 'data-selected="' . $billing_city . '" class="form-control mobile-device" id="billing_city" placeholder="Select City" required="required"');
						//form_input('billing_city', set_value('billing_city'), 'class="form-control" id="billing_city" required="required"'); ?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<?= lang('zip_code', 'billing_postal_code'); ?>
						<?= form_input('billing_postal_code', set_value('billing_postal_code'), 'class="form-control" id="billing_postal_code"'); ?>
					</div>
				</div>
				<div class="col-sm-12 address-area-wrap">
					<div class="form-group">
						<?= lang('area', 'billing_area'); ?>
						<?= form_dropdown('billing_area', $billing_areas, '', 'data-selected="' . $billing_area . '" class="form-control" placeholder="Billing Area" id="billing_area"'); ?>
						<input type="hidden" name="billing_zone" id="billing_zone" value="<?= $billing_zone; ?>">
					</div>
				</div>
				<div class="col-sm-12 address-form-heading">
					<div class="checkbox bg pull-right" style="margin-top: 0; margin-bottom: 0;">
						<label>
							<input type="checkbox" name="same" value="1" id="same_as_billing">
							<span><?= lang('same_as_billing') ?></span>
						</label>
					</div>
					<h5><strong><?= lang('shipping_address'); ?></strong></h5>
					<input type="hidden" value="new" name="address">
					<hr>
				</div>
				<div class="guest-shipping-address">
					<div class="col-sm-12">
						<div class="form-group">
							<?= lang('line1', 'shipping_line1'); ?> *
							<?= form_input('shipping_line1', set_value('shipping_line1'), 'class="form-control" id="shipping_line1" required="required"'); ?>
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
							<?= form_dropdown( 'shipping_country', $countries, $shipping_cc, 'class="form-control" id="shipping_country" required="required"' ); ?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<?= lang('state', 'shipping_state'); ?>
							<?php
							echo form_dropdown('shipping_state', $shipping_states, '', 'data-selected="' . $shipping_sc . '" class="form-control mobile-device" id="shipping_state" placeholder="Select State" required="required"');
//							if ($Settings->indian_gst) {
//								$billing_states = $this->gst->getIndianStates();
//								echo form_dropdown('shipping_state', $billing_states, '', 'class="form-control selectpicker mobile-device" id="shipping_state" title="Select" required="required"');
//							} else {
//								echo form_input('shipping_state', '', 'class="form-control" id="shipping_state"');
//							}
							?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<?= lang('city', 'shipping_city'); ?> *
							<?= form_dropdown('shipping_city', $shipping_cities, '', 'data-selected="' . $shipping_city . '" class="form-control mobile-device" id="shipping_city" placeholder="Select City" required="required"');
//							<?= form_input('shipping_city', set_value('shipping_city'), 'class="form-control" id="shipping_city" required="required"'); ?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<?= lang('zip_code', 'shipping_postal_code'); ?>
							<?= form_input('shipping_postal_code', set_value('shipping_postal_code'), 'class="form-control" id="shipping_postal_code"'); ?>
						</div>
					</div>
					<div class="col-sm-12 address-area-wrap">
						<div class="form-group">
							<?= lang('area', 'shipping_area'); ?>
							<?= form_dropdown('shipping_area', $shipping_areas, '', 'data-selected="' . $shipping_area . '" class="form-control" placeholder="Shipping Area" id="shipping_area"'); ?>
							<input type="hidden" name="shipping_zone" id="shipping_zone" value="<?= $shipping_zone; ?>">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<?= lang('phone', 'shipping_phone'); ?> *
							<?= form_input('shipping_phone', set_value('shipping_phone'), 'class="form-control" id="shipping_phone" required="required"'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
