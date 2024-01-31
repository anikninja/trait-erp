<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @var $shipment Erp_Shipment
 */
?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= sprintf( lang('edit_x'), lang( 'shipment' ) ); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('enter_info'); ?></p>
				<?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
				echo admin_form_open_multipart('delivery/edit_shipment/'.$shipment->getId(), $attrib); ?>
                <div class="col-md-12">
                    <div class="form-group">
						<?= lang('shipment_date', 'shipment_date'); ?>
						<?= form_input('shipment_date', set_value('shipment_date', $this->rerp->hrld($shipment->shipment_date) ), 'class="form-control gen_slug" id="date" required="required"'); ?>
                    </div>

                    <div class="form-group all">
						<?= lang('shipment_no', 'shipment_no'); ?>
						<?= form_input('shipment_no', set_value('shipment_no', $shipment->shipment_no ), 'class="form-control tip" id="shipment_no"'); ?>
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
                        <?php
                        $del_re_val = $sale->shipping - $shipping_cost;
                        $del_limit = ( $shipment->cost_adjustment + ($sale->shipping - $shipping_cost) );
                        ?>
		                <?= lang('shipping_cost_adjustment', 'cost_adjustment'); ?>
		                <?= form_input('cost_adjustment', set_value('cost_adjustment', $shipment->cost_adjustment ), 'min="0" max="'.$this->rerp->formatDecimal( ($sale->shipping > 0) ? $del_limit : absfloat( $this->shop_settings->shipping ) ).'" class="form-control tip" id="cost_adjustment"'); ?>
                        <?php if ( $sale->shipping > 0) {?>
                            <code>Remaining delivery charge is <?= $this->rerp->formatMoney( $del_re_val ) ?> for this sales. <?php if ($del_limit == 0) { ?>You cannot set shipping cost adjustment value<?php } else { ?>Please input shipping cost adjustment value less than or equal to <?= $this->rerp->formatDecimal( $del_limit ) ?><?php } ?> for this shipment.</code>
                        <?php } else { ?>
                            <code>Please input shipping cost adjustment value less than or equal to <?= $this->rerp->formatDecimal( absfloat( $this->shop_settings->shipping ) ) ?> for this shipment.</code>
                        <?php } ?>
                    </div>

                    <div class="form-group all">
						<?= lang('status', 'status'); ?>
                        <select name="status" class="form-control select" id="status" style="width:100%" required>
                            <option value="pending" <?= ($shipment->status == 'pending') ? 'selected' : null; ?> > <?= lang( 'pending' ); ?></option>
                            <option value="ready" <?= ($shipment->status == 'ready') ? 'selected' : null; ?> > <?= lang( 'ready_for_shipment' ); ?></option>
                            <option value="completed" <?= ($shipment->status == 'completed') ? 'selected' : null; ?> > <?= lang( 'shipment_completed' ); ?></option>
                            <option value="returned" <?= ($shipment->status == 'returned') ? 'selected' : null; ?> > <?= lang( 'shipment_returned' ); ?></option>
                        </select>
                    </div>

                </div>
                <div class="col-md-12">
					<?php echo form_submit( 'edit_shipment', sprintf( lang( 'edit_x' ), lang( 'shipment' ) ), 'class="btn btn-primary"' ); ?>
                </div>
				<?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-list"></i><?= lang( 'shipment_package_info' ); ?></h2>
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

