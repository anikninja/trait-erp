<?php defined('BASEPATH') or exit('No direct script access allowed');
if ( ! ( $zone instanceof Erp_Shipping_Zone ) ) {}
if ( ! ( $area instanceof Erp_Shipping_Zone_Area ) ) {}
$attrib = [ 'data-toggle' => 'validator', 'role' => 'form' ];
$action = 'system_settings/area_editor/'  . ( $edit ? $area->getId() : $zone->getId() . '/add' );
echo admin_form_open( $action, $attrib );
echo form_hidden( 'zone_id', $zone->getId() );
?>
<div class="box">
	<div class="box-header">
		<h2 class="blue"><i class="fa-fw fa fa-<?= $edit ? 'edit' : 'plus'; ?>"></i><?php printf( $edit ? lang( 'edit_x' ) : lang('add_x'), lang( 'zone_area' ) ); ?></h2>
		<div class="box-icon">
			<ul class="btn-tasks">
				<li class="dropdown">
					<a href="<?= admin_url( 'system_settings/zone_editor/' . $zone->getID() ); ?>" class="toggle_up tip" title="<?= sprintf( lang( 'go_back_to_x' ), lang( 'shipping_zone' ) ); ?>">
						<span><?= sprintf( lang( 'view_x' ), lang( 'shipping_zone' ) ); ?></span> <i class="icon fa fa-mail-reply"></i>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="box-content">
		<div class="row">
			<div class="col-lg-12">
				<p class="introtext"><?php echo lang('enter_info'); ?></p>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<?= lang( 'name', 'name' ); ?>
							<?= form_input( 'name', $area->getName(), 'class="form-control" id="name"' ); ?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<?= lang( 'cost_adjustment', 'cost_adjustment' ); ?>
							<?= form_input( [ 'type' => 'number', 'name' => 'cost_adjustment' ], $area->getCostAdjustment(), 'class="form-control" id="cost_adjustment" required="required"' ); ?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<?= lang( 'active', 'is_enabled' ); ?>
							<?= form_dropdown( 'is_enabled', [ '1' => lang( 'active' ), '0' => lang( 'disable' ) ], (int) $area->getIsEnabled(), 'class="form-control" id="is_enabled" required="required"' ); ?>
						</div>
					</div>
                    <div class="col-md-6">
                        <div class="form-group">
		                    <?= lang( 'active_delivery', 'delivery_enabled' ); ?>
		                    <?= form_dropdown( 'delivery_enabled', [ '1' => lang( 'active' ), '0' => lang( 'disable' ) ], (int) $area->getDeliveryEnabled(), 'class="form-control" id="delivery_enabled" required="required"' ); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
							<?= lang( 'active_pickup', 'pickup_enabled' ); ?>
							<?= form_dropdown( 'pickup_enabled', [ '1' => lang( 'active' ), '0' => lang( 'disable' ) ], (int) $area->getPickupEnabled(), 'class="form-control" id="pickup_enabled" required="required"' ); ?>
                        </div>
                    </div>
				</div>
			</div>
			<div class="col-lg-12"><?php echo form_submit('submit_zone_area', sprintf( $edit ? lang('update_x') : lang('add_x'), lang( 'zone_area' ) ), 'class="btn btn-primary"'); ?></div>
		</div>
	</div>
</div>
<?= form_close(); ?>
