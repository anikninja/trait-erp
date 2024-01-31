<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        var dss = <?= json_encode(['returned' => lang('returned'), 'pending' => lang('pending'), 'ready' => lang('ready'), 'completed' => lang('completed')]); ?>;
        function ds(x) {
            if (x == 'completed') {
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
        oTable = $('#PickupTable').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('delivery/getPickupList') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "pickup_link";
                return nRow;
            },
            "aoColumns": [ null, null, null, null, {"mRender": currencyFormat}, {"mRender": ds}, {"mRender": pay_status}, { "bSortable": false } ]
        });
    });
</script>
<?= admin_form_open('delivery/pickup_actions', 'id="action-form"') ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('pickup_list'); ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions') ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?php echo admin_url('delivery/add_pickup'); ?>" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> <?= sprintf( lang('add_x'), lang( 'new_pickup' ) ); ?></a>
                        </li>
                        <!--<li class="divider"></li>
                        <li>
                            <a href="#" id="delete" data-action="delete">
                                <i class="fa fa-trash-o"></i> <?/*= sprintf( lang('delete_x' ), lang( 'commission_groups' ) ); */?>
                            </a>
                        </li>-->
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table id="PickupTable" class="table table-bordered table-hover table-striped reports-table">
                        <thead>
                            <tr>
                                <th class="col-xs-2"><?= lang('date'); ?></th>
                                <th class="col-xs-2"><?= lang('pickup_reference_no'); ?></th>
                                <th class="col-xs-2"><?= lang('do_reference_no'); ?></th>
                                <th class="col-xs-2"><?= lang('sale_reference_no'); ?></th>
                                <th class="col-xs-1"><?= lang('sales_amount'); ?></th>
                                <th class="col-xs-1"><?= sprintf( lang('pickup_x'), lang('status') ); ?></th>
                                <th class="col-xs-1"><?= lang('payment_status'); ?></th>
                                <th class="col-xs-1"><?= lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
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
