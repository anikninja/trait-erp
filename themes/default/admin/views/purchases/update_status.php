<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('update_status'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('purchases/update_status/' . $inv->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= lang('purchase_details'); ?>
                </div>
                <div class="panel-body">
                    <table class="table table-condensed table-striped table-borderless" style="margin-bottom:0;">
                        <tbody>
                            <tr>
                                <td><?= lang('reference_no'); ?></td>
                                <td><?= $inv->reference_no; ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('supplier'); ?></td>
                                <td><?= $inv->supplier; ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('warehouse'); ?></td>
                                <td><?= $inv->warehouse_id; ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('status'); ?></td>
                                <td><strong><?= lang($inv->status); ?></strong></td>
                            </tr>
                            <tr>
                                <td><?= lang('payment_status'); ?></td>
                                <td><?= lang($inv->payment_status); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if ($returned) {
            ?>
                <h4><?= lang('purchase_x_action'); ?></h4>
            <?php
        } else {
            ?>
            <div class="form-group">
                <?= lang('status', 'status'); ?>
                <?php
                $opts = ['received' => lang('received'), 'pending' => lang('pending'), 'ordered' => lang('ordered')]; ?>
                <?= form_dropdown('status', $opts, $inv->status, 'class="form-control" id="status" required="required" style="width:100%;"'); ?>
            </div>

            <div class="form-group">
                <?= lang('note', 'note'); ?>
                <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : $this->rerp->decode_html($inv->note)), 'class="form-control" id="note"'); ?>
            </div>
            <?php
        } ?>

        </div>
        <?php if (!$returned) {
            ?>
        <div class="modal-footer">
            <?php echo form_submit('update', lang('update'), 'class="btn btn-primary"'); ?>
        </div>
        <?php
        } ?>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
