<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('count_stock'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'stForm'];
                echo admin_form_open_multipart('warehouse/count_stock', $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <?php if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) {
                    ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('warehouse', 'warehouse'); ?>
                                    <?php
                                    $wh[] = '';
                    foreach ($warehouses as $warehouse) {
                        $wh[$warehouse->id] = $warehouse->name;
                    }
                    echo form_dropdown('warehouse', $wh, ($_POST['warehouse'] ?? $Settings->default_warehouse), 'id="warehouse" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('warehouse') . '" required="required" style="width:100%;" '); ?>
                                </div>
                            </div>
                        <?php
                } else {
                    $warehouse_input = [
                        'type'  => 'hidden',
                        'name'  => 'warehouse',
                        'id'    => 'warehouse',
                        'value' => $this->session->userdata('warehouse_id'),
                    ];

                    echo form_input($warehouse_input);
                } ?>

                        <?php if ($Owner || $Admin) {
                    ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('date', 'date'); ?>
                                    <?php echo form_input('date', ($_POST['date'] ?? $this->rerp->hrld(date('Y-m-d H:i:s'))), 'class="form-control input-tip" id="date" required="required"'); ?>
                                </div>
                            </div>
                        <?php
                } ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang('reference', 'ref'); ?>
                                <?php echo form_input('reference_no', ($_POST['reference_no'] ?? ''), 'class="form-control input-tip" id="ref"'); ?>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <label><?= lang('type'); ?> *</label>
                            <div class="form-group">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-6 col-sm-2">
                                                <input type="radio" class="checkbox type" value="full" name="type" id="full" <?= $this->input->post('type') ? 'checked="checked"' : ''; ?> required="required">
                                                <label for="full" class="padding05">
                                                    <?= lang('full'); ?>
                                                </label>
                                            </div>
                                            <div class="col-xs-6 col-sm-2">
                                                <input type="radio" class="checkbox type" value="partial" name="type" id="partial" <?= $this->input->post('type') ? 'checked="checked"' : ''; ?>>
                                                <label for="partial" class="padding05">
                                                    <?= lang('partial'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="col-md-12 partials" style="display:none;">
                        <div class="well well-sm">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('brands', 'brand'); ?>
                                        <?php
                                        $bs = [];
                                        foreach ($brands as $brand) {
                                            $bs[$brand->id] = $brand->name;
                                        }
                                        echo form_dropdown('brand[]', $bs, ($_POST['brand'] ?? 0), 'id="brand" class="form-control input-tip select" data-placeholder="' . lang('select') . ' ' . lang('brand') . '" style="width:100%;" multiple');
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('categories', 'category'); ?>
                                        <?php
                                        $cs = [];
                                        foreach ($categories as $category) {
                                            $cs[$category->id] = $category->name;
                                        }
                                        echo form_dropdown('category[]', $cs, ($_POST['category'] ?? 0), 'id="category" class="form-control input-tip select" data-placeholder="' . sprintf( lang( 'select_x' ), lang( 'category' ) ) . '" style="width:100%;" multiple');
                                        ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-md-12">
                            <div class="fprom-group">
                                <?= form_submit('count_stock', lang('submit'), 'id="count_stock" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#brand option[value=''], #category option[value='']").remove();
        $('.type').on('ifChecked', function(e){
            var type_opt = $(this).val();
            if (type_opt == 'partial')
                $('.partials').slideDown();
            else
                $('.partials').slideUp();
            $('#stForm').bootstrapValidator('revalidateField', $(this));
        });
        $("#date").datetimepicker({format: site.dateFormats.js_ldate, fontAwesome: true, language: 'rerp', weekStart: 1, todayBtn: 1, autoclose: 1, todayHighlight: 1, startView: 2, forceParse: 0, startDate: "<?= $this->rerp->hrld(date('Y-m-d H:i:s')); ?>"});

    });
</script>
