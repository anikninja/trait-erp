<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        var dss = <?= json_encode(['returned' => lang('returned'), 'pending' => lang('pending'), 'ready' => lang('ready'), 'completed' => lang('completed')]); ?>;
        oTable = $('#PackageItemTable').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('delivery/getPackageItemList/' . $package->id) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = '$1';
                nRow.className = "packaging_link";
                return nRow;
            },
            "aoColumns": [ null, null, null, { "bSortable": false } ]
        });
    });
</script>
<div class="box">
	<div class="box-header">
		<h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= sprintf( lang('edit_x'), lang( 'package' ) ); ?></h2>
	</div>
	<div class="box-content">
		<div class="row">
			<div class="col-lg-12">
				<p class="introtext"><?php echo lang('enter_info'); ?></p>
				<?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
				echo admin_form_open_multipart('delivery/edit_package/'.$package->id, $attrib); ?>
				<div class="col-md-12">
                    <div class="well well-sm">
                        <div class="form-group" style="margin-bottom:0;">
                            <div class="input-group wide-tip">
	                            <?= lang('shipment_no', 'shipment_no'); ?>
								<?php echo form_input('shipment_no', set_value('shipment_no', $shipment->shipment_no ), 'class="form-control input-lg" id="shipment_no" readonly placeholder="' . lang('sale_reference_no') . '"'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

					<div class="well well-sm">
						<div class="form-group" style="margin-bottom:0;">
							<div class="input-group wide-tip">
								<?= lang('sale_reference_no', 'sale_reference_no'); ?>
								<?php echo form_input('sale_reference_no', $sale->reference_no, 'class="form-control input-lg" id="sale_reference_no" readonly placeholder="' . lang('sale_reference_no') . '"'); ?>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>

					<div class="well well-sm">
						<div class="form-group" style="margin-bottom:0;">
							<div class="input-group wide-tip">
								<?= lang('do_reference_no', 'do_reference_no'); ?>
								<?php echo form_input('do_reference_no', $delivery->do_reference_no, 'class="form-control input-lg" id="do_reference_no" readonly placeholder="' . lang('add_delivery_reference_no') . '"'); ?>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>



					<div class="form-group all">
						<?= lang('status', 'status'); ?>
						<select name="status" class="form-control select" id="status" style="width:100%" required>
							<option value="pending" <?= ($package->status == 'pending') ? 'selected' : null; ?> > <?= lang( 'pending' ); ?></option>
							<option value="ready" <?= ($package->status == 'ready') ? 'selected' : null; ?> > <?= lang( 'package_ready' ); ?></option>
							<option value="delivered" <?= ($package->status == 'delivered') ? 'selected' : null; ?> > <?= lang( 'package_delivered' ); ?></option>
							<option value="returned" <?= ($package->status == 'returned') ? 'selected' : null; ?> > <?= lang( 'package_returned' ); ?></option>
						</select>
					</div>

				</div>
				<div class="col-md-12">
					<?php echo form_submit( 'edit_package', sprintf( lang( 'edit_x' ), lang( 'package' ) ), 'class="btn btn-primary"' ); ?>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-header">
		<h2 class="blue"><i class="fa-fw fa fa-list"></i><?= lang( 'package_reference_no' ) . ': ' . $package->package_no; ?></h2>
	</div>
	<div class="box-content">
		<div class="row">
			<div class="col-lg-12">
				<div class="table-responsive">
					<table id="PackageItemTable" class="table table-bordered table-hover table-striped reports-table">
						<thead>
						<tr>
							<th><?= lang('product_code'); ?></th>
							<th><?= lang('product_name'); ?></th>
							<th><?= lang('quantity'); ?></th>
							<th style="width:100px;"><?= lang('actions'); ?></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td colspan="6" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="box-content">
		<div class="row">
			<div class="col-lg-12">
				<div class="form-group">
					<div class="col-md-12 table-responsive">
						<?= $full_table; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div style="display: none;">
	<?= admin_form_open('delivery/shipment_actions', 'id="other_action-form"') ?>
	<input type="hidden" name="other_action" value="" id="other_action"/>
	<input type="hidden" name="package_id" value="" id="package_id"/>
	<?= form_submit('submit', 'submit', 'id="other_action-form-submit"') ?>
	<?= form_close() ?>
</div>

<script language="javascript">
    $(document).ready(function () {
        $('#delete').click(function (e) {
            e.preventDefault();
            $('#other_action').val($(this).attr('data-action'));
            $('#package_id').val($(this).attr('data-id'));
            $('#other_action-form-submit').trigger('click');
        });

        $('#edit').click(function (e) {
            e.preventDefault();
            $('#other_action').val($(this).attr('data-action'));
            $('#package_id').val($(this).attr('data-id'));
            $('#other_action-form-submit').trigger('click');
        });

    });
</script>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" charset="UTF-8">
    $.fn.datetimepicker.dates['rerp'] = <?=$dp_lang?>;
</script>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        $.fn.datetimepicker.dates['rerp'] = <?=$dp_lang?>;
    });
</script>

