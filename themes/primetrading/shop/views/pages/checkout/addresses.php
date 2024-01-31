<?php if ( empty( $addresses ) ) { ?>
	<div class="alert alert-warning alert-dismissable">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<p><span class="fa fa-warning"></span> <?php echo lang( 'please_add_address_first' ); ?></p>
	</div>
<?php } ?>
<div class="panel panel-default checkout-step-01">
	<div class="checkout-step-header-wrap">
		<div class="checkout-step-header"><div class="step-num"><?= $step; ?></div>Delivery Address</div>
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
			if ( ! empty( $addresses ) ) {
				foreach ( $addresses as $address ) {
					if ( preg_match( '/^(\+\d)(\d{3})(\d{3})(\d{4})$/', $address->phone, $matches ) ) {
						$address->phone = $matches[1] . ' (' . $matches[2] . ') ' . $matches[3] . '-' . $matches[4];
					}
					
					$phones .= sprintf(
						'<div class="phone col-sm-6 col-md-4 phone-%1$s">
							<label>
                                <input type="radio" disabled name="phone" value="%1$s">
                                <div class="checkout-page-content">
                                    <div class="content-header">%2$s</div>
                                    <p class="content">%3$s</p>
                                </div>
                            </label>
                        </div>',
						$address->id, $address->title, $address->phone
					);
					?>
					<div class="address col-sm-6 col-md-4 address-<?php echo $address->id; ?>" data-address='<?php echo json_encode($address) ?>'>
						<label>
							<input type="radio" name="address" value="<?php echo $address->id; ?>">
							<div class="checkout-page-content">
								<div class="content-header"><?php echo $address->title; ?></div>
								<p class="content">
									<?php
									$address->line1 = trim( $address->line1 );
									$address->line2 = trim( $address->line2 );
									$address->area_name = isset( $address->area_name ) ? trim( $address->area_name ) : false;
									$address->city = trim( $address->city );
									$address->country = trim( $address->country );
									$address->state = trim( $address->state );
									$address->postal_code = trim( $address->postal_code );
									
									if ( $address->line1 ) echo $address->line1 . '<br>';
									if ( $address->line2 ) echo $address->line2 . '<br>';
									if ( $address->area_name ) echo $address->area_name . '<br>';
									if ( $address->city ) echo $address->city . ',';
									if ( $address->state && $address->country ) echo ci_get_states($address->country, $address->state) . '-';
									if ( $address->postal_code ) echo $address->postal_code . '<br>';
									if ( $address->country ) echo ci_get_countries($address->country);
									?>
								</p>
								<div class="checkout-step-content-edit">
									<a class="edit" href="#"><img src="<?php echo $assets; ?>images/edit-icon.png" alt="<?= lang( 'edit_address' ); ?>"></a>
									<a class="remove" href="#"><img src="<?php echo $assets; ?>images/times.png"  alt="<?= lang( 'delete_address' ); ?>"></a>
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
