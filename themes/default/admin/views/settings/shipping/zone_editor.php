<?php /** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpUndefinedVariableInspection */
defined('BASEPATH') or exit('No direct script access allowed');
echo admin_form_open( 'system_settings/zone_editor/' . $zone->getId(), [ 'data-toggle' => 'validator', 'role' => 'form' ] );
?>
<!--suppress HtmlUnknownTarget -->
<!--suppress HtmlUnknownTag -->
<div class="box">
	<div class="box-header">
		<h2 class="blue"><i class="fa-fw fa fa-<?= $edit ? 'edit' : 'plus'; ?>"></i><?= $edit ? lang( 'edit_zone' ) : lang('add_zone'); ?></h2>
	</div>
	<div class="box-content">
		<div class="row">
			<div class="col-lg-12">
				<p class="introtext"><?php echo lang('enter_info'); ?></p>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<?= lang( 'name', 'name' ); ?>
							<?= form_input( 'name', $zone->getName(), 'class="form-control" id="name"' ); ?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<?= lang( 'continent', 'continent' ); ?>
							<?php
							$continents = ci_get_continents();
							$_continents = [ '' => '' ];
							foreach( $continents as $k => $v ) {
								$_continents[$k] = $v['name'];
							}
							?>
							<?= form_dropdown( 'continent', $_continents, $zone->getContinent(), 'class="form-control select" placeholder="'.sprintf( lang( 'select_x' ), lang('continent' ) ).'" id="continent" required="required"' ); ?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<?= lang( 'country', 'country' ); ?>
							<?= form_dropdown( 'country', $zone->getContinent() ? [ '' => '' ] + ci_get_continents( $zone->getContinent() ) : [ sprintf( lang( 'select_x' ), lang( 'continent' ) ) ], $zone->getCountry(), 'class="form-control select" placeholder="'.sprintf( lang( 'select_x' ), lang('country' ) ).'" id="country" required="required"' ); ?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<?= lang( 'state', 'state' ); ?>
							<?php
							$states = false;
							if ( $zone->getCountry() ) {
								$states = ci_get_states( $zone->getCountry() );
								if ( $states ) {
									$states = [ '' => '' ] + $states;
								}
							}
							if ( false === $states ) {
								$states = [ '' => sprintf( lang( 'select_x' ), lang( 'country' ) ) ];
							}
							echo form_dropdown( 'state', $states, $zone->getState(), 'class="form-control select" placeholder="'.sprintf( lang( 'select_x' ), lang('state' ) ).'" id="state"' ); ?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<?= lang( 'city', 'city' ); ?>
							<?= form_input( 'city', $zone->getCity(), 'class="form-control" id="city"' ); ?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<?= lang( 'zip_code', 'zip' ); ?>
							<?= form_input( 'zip', $zone->getZip(), 'class="form-control" id="zip"' ); ?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<?= lang( 'active', 'is_enabled' ); ?>
							<?= form_dropdown( 'is_enabled', [ '1' => lang( 'active' ), '0' => lang( 'disable' ) ], (int) $zone->getIsEnabled(), 'class="form-control" id="is_enabled" required="required"' ); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-12"><?php echo form_submit('submit_zone', $edit ? lang('update_zone') : lang('add_zone'), 'class="btn btn-primary"'); ?></div>
		</div>
	</div>
</div>
<?php if ( $edit ) {
	// @TODO use the same table as index.php & slot_index.php for both shipping method and area.
?>
<div class="box">
	<div class="box-header">
		<h2 class="blue"><i class="fa-fw fa fa-edit"></i><?= sprintf( lang( 'manage_x' ), lang( 'shipping_methods' ) ); ?></h2>
		<div class="box-icon">
			<ul class="btn-tasks">
				<li class="dropdown">
					<a href="<?= admin_url( 'system_settings/shipping_method_editor/' . $zone->getId() ); ?>/add" class="toggle_up tip" title="<?= sprintf( lang( 'add_x' ), lang( 'shipping_method' ) ); ?>">
						<i class="icon fa fa-plus"></i>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="box-content">
		<div class="row">
			<div class="col-lg-12">
				<table id="zoneMethods" class="table table-bordered table-hover table-striped reports-table">
					<thead>
					<th><?= lang( 'shipping_method_title' ); ?></th>
					<th><?= lang( 'description' ); ?></th>
					<th><?= lang( 'actions' ); ?></th>
					</thead>
					<tbody>
					<?php
					if ( $zone->getShippingMethods() ) {
						foreach ( $zone->getShippingMethods() as $k => $method ) { ?>
						<tr data-id="<?= $method->getId(); ?>">
							<td><h3><?= $method->getName(); ?></h3></td>
							<td><?= $method->getDescription(); ?></td>
							<td>
								<a class="btn btn-primary" href="<?= admin_url( 'system_settings/shipping_method_editor/' . $method->getId() ); ?>" class="edit"><?= lang( 'edit' ); ?></a>
								<a class="btn btn-danger" href="<?= admin_url( 'system_settings/delete_shipping_method/' . $method->getId() ); ?>" class="remove"><?= lang( 'delete' ); ?></a>
							</td>
						</tr>
					<?php } ?>
					<?php } else { ?>
						<td colspan="3" class="dataTables_empty"><?= lang('no_data_available') ?></td>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-header">
		<h2 class="blue"><i class="fa-fw fa fa-edit"></i><?= sprintf( lang( 'manage_x' ), lang( 'zone_area' ) ); ?></h2>
		<div class="box-icon">
			<ul class="btn-tasks">
				<li class="dropdown">
					<a href="<?= admin_url( 'system_settings/area_editor/' . $zone->getID() ); ?>/add" class="toggle_up tip" title="<?= sprintf( lang( 'add_x' ), lang( 'zone_area') ); ?>">
						<i class="icon fa fa-plus"></i>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="box-content">
		<div class="row">
			<div class="col-lg-12">
				<table id="zoneMethods" class="table table-bordered table-hover table-striped reports-table">
					<thead>
					<th><?= lang( 'area_title' ); ?></th>
					<th><?= lang( 'total_slots' ); ?></th>
					<th><?= lang( 'active_slots' ); ?></th>
					<th><?= lang( 'actions' ); ?></th>
					</thead>
					<tbody>
					<?php
					if ( $zone->getAreas() ) {
						foreach ( $zone->getAreas() as $k => $area ) { ?>
						<tr data-id="<?= $area->getId(); ?>">
							<td><h3><?= $area->getName(); ?></h3></td>
							<td><?= $area->getCountSlots(); ?></td>
							<td><?= $area->getCountSlotsActive(); ?></td>
							<td>
								<a class="btn btn-primary" href="<?= admin_url( 'system_settings/view_slots/' . $area->getId() ); ?>" class="view"><?= sprintf( lang( 'delivery_x' ), lang( 'slots' ) ); ?></a>
								<a class="btn btn-primary" href="<?= admin_url( 'system_settings/pickup_slots/' . $area->getId() ); ?>" class="view"><?= sprintf( lang( 'pickup_x' ), lang( 'slots' ) ); ?></a>
								<a class="btn btn-primary" href="<?= admin_url( 'system_settings/area_editor/' . $area->getId() ); ?>" class="edit"><?= sprintf( lang( 'edit_x' ), lang('area') ); ?></a>
								<a class="btn btn-danger" href="<?= admin_url( 'system_settings/delete_zone_area/' . $area->getId() ); ?>" class="remove"><?= lang( 'delete' ); ?></a>
							</td>
						</tr>
					<?php
						}
					} else { ?>
						<tr>
							<td colspan="4" class="dataTables_empty"><?= lang('no_data_available') ?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<?= form_close(); ?>
<!--suppress ES6ConvertVarToLetConst -->
<script>
	(function($){
		$(document).on( 'ready', function() {
			var cc = $( '#country' ),
				cc_placeholder = '<?php printf( lang( 'select_x' ), lang('continent' ) ); ?>',
				sc = $( '#state' ),
				sc_placeholder = '<?php printf( lang( 'select_x' ), lang('country' ) ); ?>';
			$('#continent').on( 'change', function() {
				var self = $(this);
				if ( self.val() ) {
					cc.prop( 'disabled', true );
					cc.html( '<option value="">'+cc_placeholder+'</option>' );
					$.getJSON( site.url + 'admin/system_settings/world_data/?which=country&continent=' + self.val(), function( items ) {
						var placeholder = cc.attr( 'placeholder' ) ?? '-- Select --';
						cc.html( '' );
						cc.html( '<option value="" disabled>'+placeholder+'</option>' );
						for ( var key in items ) {
							if ( key && items.hasOwnProperty( key ) ) {
								cc.append( '<option value="'+key+'">'+items[key]+'</option>' );
							}
						}
						cc.prop( 'disabled', false );
					} );
				}
			} );
			cc.on( 'change', function() {
				var self = $(this);
				if ( self.val() ) {
					sc.prop( 'disabled', true );
					sc.html( '<option value="">'+sc_placeholder+'</option>' );
					$.getJSON( site.url + 'admin/system_settings/world_data/?which=states&cc=' + self.val(), function( items ) {
						var placeholder = sc.attr( 'placeholder' ) ?? '-- Select --';
						sc.html( '' );
						sc.html( '<option value="" disabled>'+placeholder+'</option>' );
						for ( var key in items ) {
							if ( key && items.hasOwnProperty( key ) ) {
								sc.append( '<option value="'+key+'">'+items[key]+'</option>' );
							}
						}
						sc.prop( 'disabled', false );
					} );
				}
			} );
		} );
	})(jQuery);
</script>
