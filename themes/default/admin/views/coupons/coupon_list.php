<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        var dss = <?= json_encode(['inactive' => lang('inactive'), 'active' => lang('active')]); ?>;
        function ds(x) {
            if (x == 'active') {
                return '<div class="text-center"><span class="label label-success">'+(dss[x] ? dss[x] : x)+'</span></div>';
            } else if (x == 'inactive') {
                return '<div class="text-center"><span class="label label-danger">'+(dss[x] ? dss[x] : x)+'</span></div>';
            }
            return (x != null) ? (dss[x] ? dss[x] : x) : x;
        }
        var tss = <?= json_encode(['fixed_cart' => lang('fixed_cart_discount'), 'fixed_product' => lang('fixed_product_discount'), 'percentage' => lang('percentage_discount')]); ?>;
        function tp(x) {
            if (x === 'fixed_cart') {
                return '<div>'+(tss[x] ? tss[x] : x)+'</div>';
            } else if (x === 'fixed_product') {
                return '<div>'+(tss[x] ? tss[x] : x)+'</div>';
            } else if (x === 'percentage') {
                return '<div>'+(tss[x] ? tss[x] : x)+'</div>';
            }
            return (x != null) ? (tss[x] ? tss[x] : x) : x;
        }
        oTable = $('#CouponTable').dataTable({
            "aaSorting": [[4, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('coupons/getCouponList') ?>',
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
                nRow.className = "coupon_link";
                return nRow;
            },
            "aoColumns": [ {"bSortable": false,"mRender": checkbox}, null, {"mRender": tp}, {"mRender": formatQuantity2}, null, null, null, {"mRender": ds}, { "bSortable": false } ]
        });
    });
</script>
<?= admin_form_open('coupons/coupon_actions', 'id="action-form"') ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('coupon_list'); ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions') ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li>
                            <a href="<?php echo admin_url('coupons/add_coupon'); ?>" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> <?= sprintf( lang('add_x'), lang( 'new_coupon' ) ); ?></a>
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
                    <table id="CouponTable" class="table table-bordered table-hover table-striped reports-table">
                        <thead>
                            <tr>
                                <th style="min-width:30px; width: 30px; text-align: center;">
                                    <input class="checkbox checkft" type="checkbox" name="check"/>
                                </th>
                                <th class="col-xs-2"><?= lang('coupon_code'); ?></th>
                                <th class="col-xs-2"><?= lang('coupon_type'); ?></th>
                                <th class="col-xs-1"><?= lang('amount'); ?></th>
                                <th class="col-xs-3"><?= lang('description'); ?></th>
                                <th class="col-xs-2"><?= lang('start_date'); ?></th>
                                <th class="col-xs-2"><?= lang('end_date'); ?></th>
                                <th class="col-xs-1"><?= lang('status'); ?></th>
                                <th class="col-xs-1"><?= lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="100%" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
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
