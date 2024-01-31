<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @var $pickup Erp_Pickup
 */
?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= sprintf( lang('edit_x'), lang( 'pickup' ) ); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('enter_info'); ?></p>
				<?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
				echo admin_form_open_multipart('delivery/edit_pickup/'.$pickup->getId(), $attrib); ?>
                <div class="col-md-12">
                    <div class="form-group">
						<?= lang('pickup_date', 'pickup_date'); ?>
						<?= form_input('pickup_date', set_value('pickup_date', $this->rerp->hrld($pickup->pickup_date) ), 'class="form-control gen_slug" id="date" required="required"'); ?>
                    </div>

                    <div class="form-group all">
						<?= lang('pickup_no', 'pickup_no'); ?>
						<?= form_input('pickup_no', set_value('pickup_no', $pickup->pickup_no ), 'class="form-control tip" id="pickup_no"'); ?>
                    </div>

                    <div class="well well-sm">
                        <div class="form-group" style="margin-bottom:0;">
                            <div class="input-group wide-tip">
				                <?= lang('sale_reference_no', 'sale_reference_no'); ?>
				                <?php echo form_input('sale_reference_no', $sale->reference_no, 'class="form-control input-lg" id="add_item_sale" readonly placeholder="' . lang('sale_reference_no') . '"'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="well well-sm">
                        <div class="form-group" style="margin-bottom:0;">
                            <div class="input-group wide-tip">
				                <?= lang('do_reference_no', 'do_reference_no'); ?>
				                <?php echo form_input('do_reference_no', $delivery->do_reference_no, 'class="form-control input-lg" id="add_item_delivery" readonly placeholder="' . lang('add_delivery_reference_no') . '"'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group all">
						<?= lang('status', 'status'); ?>
                        <select name="status" class="form-control select" id="status" style="width:100%" required>
                            <option value="pending" <?= ($pickup->status == 'pending') ? 'selected' : null; ?> > <?= lang( 'pending' ); ?></option>
                            <option value="ready" <?= ($pickup->status == 'ready') ? 'selected' : null; ?> > <?= lang( 'ready_for_pickup' ); ?></option>
                            <option value="completed" <?= ($pickup->status == 'completed') ? 'selected' : null; ?> > <?= lang( 'pickup_completed' ); ?></option>
                            <option value="returned" <?= ($pickup->status == 'returned') ? 'selected' : null; ?> > <?= lang( 'pickup_returned' ); ?></option>
                        </select>
                    </div>

                </div>
                <div class="col-md-12">
					<?php echo form_submit( 'edit_pickup', sprintf( lang( 'edit_x' ), lang( 'pickup' ) ), 'class="btn btn-primary"' ); ?>
                </div>
				<?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-list"></i><?= lang( 'pickup_package_info' ); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
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
            </div>
        </div>
    </div>
</div>
<div style="display: none;">
	<?= admin_form_open('delivery/pickup_actions', 'id="other_action-form"') ?>
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

