<!-- checkout-step-01  -->
<div class="checkout-step-header-wrap">
    <div class="checkout-step-header">Delivery Address</div>
	<?php if ( count( $addresses ) < 6 && ! $this->Staff ) { ?>
        <div class="checkout-step-btn">
            <a href="#" id="add-new-address" class="update-address">+ Add Address</a>
        </div>
	<?php } ?>
</div>
<div class="panel panel-default checkout-step-01">
	<div class="checkout-step-content-wrap">
		<div class="row address-wrap">
			<?php
			if (!empty($addresses)) {
				foreach ($addresses as $address) {
					?>
					<div class="address col-sm-6 col-md-4 address-<?php echo $address->id; ?>" data-address='<?php echo json_encode($address) ?>'>
						<label>
							<input type="radio" name="address" value="<?php echo $address->id; ?>">
							<div class="checkout-page-content">
								<div class="content-header"><?php echo $address->title; ?></div>
								<p class="content">

									<?= ! empty( $address->line1 ) ? $address->line1 . '<br>' : ''; ?>
									<?= ! empty( $address->line2 ) ? $address->line2 . '<br>' : ''; ?>
									<?= isset( $address->area_name ) ? $address->area_name . '<br>' : ''; ?>
									<?= ! empty( $address->city ) ? $address->city . ',' : ''; ?>
									<?= $address->country && $address->state ? ci_get_states( $address->country, $address->state ) : ''; ?>
									<?= ( $address->country && $address->state ) && $address->postal_code ? ' - ' : ''; ?>
									<?= ! empty( $address->postal_code ) ? $address->postal_code . '<br>' : ''; ?>
									<?= ci_get_countries( $address->country ); ?>
									<?= ! empty( $address->phone ) ? '<br>' . $address->phone : ''; ?>
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
<!-- checkout-step-01  -->
