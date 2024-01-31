<?php defined('BASEPATH') or exit('No direct script access allowed');

?>
<script>
    $(document).ready(function () {
        var dss = <?= json_encode(['returned' => lang('returned'), 'pending' => lang('pending'), 'ready' => lang('ready'), 'completed' => lang('completed')]); ?>;
        oTable = $('#PickupAreaList').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('warehouse/getDeliveryAreaList/' . $warehouse->id) ?>',
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
            "aoColumns": [ {"bSortable": false, "mRender": checkbox}, null, null, { "bSortable": false } ]
        });
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= sprintf( lang('edit_x'), sprintf( lang( 'delivery_x' ), lang( 'area' ) ) ) .' '. lang('for') . ' ' . $warehouse->code .' ('. $warehouse->name . ')'; ?> </h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('enter_info'); ?></p>
				<?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
				echo admin_form_open_multipart('warehouse/edit_delivery_area/'.$warehouse->id, $attrib); ?>
                <div class="col-md-3">
                    <div class="form-group all">
		                <?= lang('area', 'area'); ?>
                        <select name="area" class="form-control select" id="area" style="width:100%" required="required" >
                            <option value= null ><?= lang( 'select_area' ); ?></option>
			                <?php
			                foreach ( $area_list as $valarea ) {
				                ?>
                                <option value="<?= $valarea['id']; ?>"><?= $valarea['area_name'] .  ' (' . $valarea['zone_name'] . ')'; ?></option>
				                <?php
			                }
			                ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
					<?php echo form_submit( 'add_area', sprintf( lang( 'add_x' ), lang( 'area' ) ), 'class="btn btn-primary"' ); ?>
                </div>
				<?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-list"></i><?= lang( 'delivery' ) . ' ' . lang('area') . ' ' . lang('list') .' '. lang('for') . ' ' . $warehouse->code .' ('. $warehouse->name . ')'; ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table id="PickupAreaList" class="table table-bordered table-hover table-striped reports-table">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang('area'); ?></th>
                            <th><?= lang('zone'); ?></th>
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
</div>
