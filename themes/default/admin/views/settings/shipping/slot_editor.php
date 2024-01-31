<?php defined('BASEPATH') or exit('No direct script access allowed');
if ( ! ( $zone instanceof Erp_Shipping_Zone ) ) {}
if ( ! ( $area instanceof Erp_Shipping_Zone_Area ) ) {}
if ( ! ( $slot instanceof Erp_Shipping_Zone_Area_Slot ) ) {}
$attrib = [ 'data-toggle' => 'validator', 'role' => 'form' ];
$action = 'system_settings/slot_editor/'  . ( $edit ? $slot->getId() : $area->getId() . '/add' );
echo admin_form_open( $action, $attrib );
echo form_hidden( 'area_id', $area->getId() );
?>
<div class="box">
	<div class="box-header">
		<h2 class="blue"><i class="fa-fw fa fa-<?= $edit ? 'edit' : 'plus'; ?>"></i><?php printf( $edit ? lang( 'edit_x' ) : lang('add_x'), lang( 'slot' ) ); ?></h2>
	</div>
	<div class="box-content">
		<div class="row">
			<div class="col-lg-12">
				<p class="introtext"><?php echo lang('enter_info'); ?></p>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<?= lang( 'name', 'name' ); ?>
							<?= form_input( 'name', $slot->getName(), 'class="form-control" id="name" required="required"' ); ?>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<?= lang( 'start_at', 'start_at' ); ?>
							<?= form_input( [ 'type' => 'time', 'name' => 'start_at' ], $slot->getStartAt(), 'class="form-control" id="start_at" required="required"' ); ?>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<?= lang( 'end_at', 'end_at' ); ?>
							<?= form_input( [ 'type' => 'time', 'name' => 'end_at' ], $slot->getEndAt(), 'class="form-control" id="end_at" required="required"' ); ?>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<?= lang( 'close_before', 'close_before' ); ?>
							<?= form_input( [ 'type' => 'time', 'name' => 'close_before' ], $slot->getCloseBefore(), 'class="form-control" id="close_before" required="required"' ); ?>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<?= lang( 'cost_adjustment', 'cost_adjustment' ); ?>
							<?= form_input( [ 'type' => 'number', 'name' => 'cost_adjustment' ], $slot->getCostAdjustment(), 'class="form-control" id="cost_adjustment" required="required"' ); ?>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<?= lang( 'max_order', 'max_order' ); ?>
							<?= form_input( [ 'type' => 'number', 'name' => 'max_order' ], $slot->getMaxOrder(), 'class="form-control" id="max_order" required="required"' ); ?>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<?= lang( 'active', 'is_enabled' ); ?>
							<?= form_dropdown( 'is_enabled', [ '1' => lang( 'active' ), '0' => lang( 'disable' ) ], (int) $slot->getIsEnabled(), 'class="form-control" id="is_enabled" required="required"' ); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-12"><?php echo form_submit('submit_slot', sprintf( $edit ? lang('update_x') : lang('add_x'), lang( 'slot' ) ), 'class="btn btn-primary"'); ?></div>
		</div>
	</div>
</div>
<?= form_close(); ?>
