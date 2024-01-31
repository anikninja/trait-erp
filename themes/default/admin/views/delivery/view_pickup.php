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
                        <td width="25%"><?= lang('delivery_date'); ?></td>
                        <td width="25%"><?= $this->rerp->hrld($delivery->date); ?></td>
                        <td width="25%"><?= lang('do_reference_no'); ?></td>
                        <td width="25%"><?= $delivery->do_reference_no; ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('customer'); ?></td>
                        <td><?= $delivery->customer; ?></td>
                        <td><?= lang('sale_reference_no'); ?></td>
                        <td><?= $delivery->sale_reference_no; ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('address'); ?></td>
                        <td colspan="3"><?= $delivery->address; ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('pickup_reference_no'); ?></td>
                        <td><?= $pickup->pickup_no; ?></td>
                        <td><?= lang('pickup_status'); ?></td>
                        <td><?= $pickup->status; ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('pickup_date'); ?></td>
                        <td colspan="3"><?= $this->rerp->hrld($pickup->pickup_date); ?></td>
                    </tr>
                    </tbody>

                </table>
            </div>
            <div class="well well-sm">
		        <?= lang('warehouse') . ': ' . $warehouse->name . ' (' . $warehouse->code . ')'; ?>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th style="text-align: left;"><?= lang('no.'); ?></th>
                        <th><?= lang('name'); ?></th>
                        <th><?= lang('quantity'); ?></th>

                    </tr>
                    </thead>
                    <tbody>

			        <?php
                    $i = 0;
                    foreach ($packaging as $item) {
				        echo '<tr>';
	                    echo '<td>' . ++$i . '</td>';
				        echo '<td>' . $item['name'] . '</td>';
				        echo '<td>' . $this->rerp->formatQuantity($item['quantity']) . ' ' . $item['unit'] . '</td>';

				        echo '</tr>';
			        }
                    if ( $returned ) {
	                    echo '<tr class="warning"><td colspan="100%" class="no-border"><strong>' . lang('returned_items') . '</strong></td></tr>';
	                    foreach ($return_packaging as $item) {
		                    echo '<tr class="warning">';
		                    echo '<td>' . ++$i . '</td>';
		                    echo '<td>' . $item['name'] . '</td>';
		                    echo '<td>' . $this->rerp->formatQuantity($item['quantity']) . ' ' . $item['unit'] . '</td>';

		                    echo '</tr>';
	                    }
                    }
			        ?>
                    </tbody>
                </table>
            </div>

            <div class="clearfix"></div>

            <?php if ($delivery->status == 'delivered') {
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

