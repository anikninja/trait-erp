<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <?php if ($logo) {
    ?>
                <div class="text-center" style="margin-bottom:20px;">
                    <img src="<?= base_url() . 'assets/uploads/logos/' . $biller->logo; ?>"
                         alt="<?= $biller->company && $biller->company != '-' ? $biller->company : $biller->name; ?>">
                </div>
            <?php
} ?>
            <div class="table-responsive">
                <table class="table table-bordered">

                    <tbody>
                    <tr>
                        <td width="30%"><?= lang('date'); ?></td>
                        <td width="70%"><?= $this->rerp->hrld($delivery->date); ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('do_reference_no'); ?></td>
                        <td><?= $delivery->do_reference_no; ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('sale_reference_no'); ?></td>
                        <td><?= $delivery->sale_reference_no; ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('customer'); ?></td>
                        <td><?= $delivery->customer; ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('address'); ?></td>
                        <td><?= $delivery->address; ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('status'); ?></td>
                        <td><?= lang($delivery->status); ?></td>
                    </tr>
                    <?php if ($delivery->note) {
        ?>
                        <tr>
                            <td><?= lang('note'); ?></td>
                            <td><?= $this->rerp->decode_html($delivery->note); ?></td>
                        </tr>
                    <?php
    } ?>
                    </tbody>

                </table>
            </div>
            <div class="table-responsive">
                <?= $full_table; ?>
            </div>
            <div class="clearfix"></div>

            <?php if ( $delivery->status == 'delivered' ) {
                        ?>
            <div class="row">
                <div class="col-xs-4">
                    <p><?= lang('prepared_by'); ?>:<br> <?= $user->first_name . ' ' . $user->last_name; ?> </p>
                </div>
                <div class="col-xs-4">
                    <p><?= lang('delivered_by'); ?>:<br> <?= $delivery->delivered_by; ?></p>
                </div>
                <div class="col-xs-4">
                    <p><?= lang('received_by'); ?>:<br> <?= $delivery->received_by; ?></p>
                </div>
            </div>
            <?php
                    } else {
                        ?>
            <div class="row">
                <div class="col-xs-4">
                    <p style="height:80px;"><?= lang('prepared_by'); ?>
                        : <?= $user->first_name . ' ' . $user->last_name; ?> </p>
                    <hr>
                    <p><?= lang('stamp_sign'); ?></p>
                </div>
                <div class="col-xs-4">
                    <p style="height:80px;"><?= lang('delivered_by'); ?>: </p>
                    <hr>
                    <p><?= lang('stamp_sign'); ?></p>
                </div>
                <div class="col-xs-4">
                    <p style="height:80px;"><?= lang('received_by'); ?>: </p>
                    <hr>
                    <p><?= lang('stamp_sign'); ?></p>
                </div>
            </div>
            <?php
                    } ?>

        </div>
    </div>
</div>

