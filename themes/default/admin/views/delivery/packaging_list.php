<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        var dss = <?= json_encode(['returned' => lang('returned'), 'pending' => lang('pending'), 'ready' => lang('ready'), 'delivered' => lang('delivered')]); ?>;
        function ds(x) {
            if (x == 'delivered') {
                return '<div class="text-center"><span class="label label-success">'+(dss[x] ? dss[x] : x)+'</span></div>';
            } else if (x == 'ready') {
                return '<div class="text-center"><span class="label label-primary">'+(dss[x] ? dss[x] : x)+'</span></div>';
            } else if (x == 'pending') {
                return '<div class="text-center"><span class="label label-warning">'+(dss[x] ? dss[x] : x)+'</span></div>';
            } else if (x == 'returned') {
                return '<div class="text-center"><span class="label label-danger">'+(dss[x] ? dss[x] : x)+'</span></div>';
            }
            return x;
            return (x != null) ? (dss[x] ? dss[x] : x) : x;
        }
        oTable = $('#PackagingTable').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('delivery/getPackagingList') ?>',
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
            "aoColumns": [ null, null, null, null, null, {"mRender": ds}, { "bSortable": false } ]
        });
    });
</script>
<?= admin_form_open('delivery/packaging_menu_actions', 'id="action-form"') ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('packaging_list'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table id="PackagingTable" class="table table-bordered table-hover table-striped reports-table">
                        <thead>
                            <tr>
                                <th><?= lang('packaging_reference_no'); ?></th>
                                <th><?= lang('shipment_reference_no'); ?></th>
                                <th><?= lang('do_reference_no'); ?></th>
                                <th><?= lang('sale_reference_no'); ?></th>
                                <th><?= lang('total_items'); ?></th>
                                <th><?= lang('status'); ?></th>
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

<div style="display: none;">
    <input type="hidden" name="form_action" value="" id="form_action"/>
    <?= form_submit('submit', 'submit', 'id="action-form-submit"') ?>
</div>
<?= form_close() ?>
<script language="javascript">
    $(document).ready(function () {

        $('#delete').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });

        $('#excel').click(function (e) {
            e.preventDefault();
            $('#form_action').val($(this).attr('data-action'));
            $('#action-form-submit').trigger('click');
        });
    });
</script>
