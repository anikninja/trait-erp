<?php defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );
/**
 *
 */
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= sprintf( lang( 'add_x' ), lang( 'new_coupon' ) ); ?></h4>
        </div>
		<?php $attrib = [ 'data-toggle' => 'validator', 'role' => 'form' ];
		echo admin_form_open_multipart( 'coupons/add_coupon', $attrib ); ?>
        <div class="modal-body">
            <div class="form-group all">
				<?= lang( 'coupon_code', 'coupon_code' ); ?>
				<?= form_input( 'coupon_code', ( isset( $_POST['coupon_code'] ) ? $_POST['coupon_code'] : $coupon_code ), 'class="form-control tip" id="coupon_code"' ); ?>
            </div>

            <div class="form-group all">
		        <?= lang('coupon_type', 'coupon_type'); ?>
                <select name="coupon_type" class="form-control select" id="coupon_type" style="width:100%" required>
                    <option value="fixed"> <?= lang( 'fixed_cart_discount' ); ?></option>
                    <option value="percentage"> <?= lang( 'percentage_discount' ); ?></option>
                </select>
            </div>

            <div class="form-group all">
				<?= lang( 'coupon_amount', 'coupon_amount' ); ?>
				<?= form_input( 'coupon_amount', ( isset( $_POST['coupon_amount'] ) ? $_POST['coupon_amount'] : null ), 'class="form-control tip" id="coupon_amount"' ); ?>
            </div>

            <div class="form-group">
                <?= lang('description', 'description'); ?>
		        <?php echo form_textarea('description', '', 'rows="3" class="form-control" id="description" required="required"'); ?>
            </div>

            <div class="form-group">
				<?= lang( 'start_date', 'start_date', 'required' ); ?>
				<?= form_input( 'start_date', set_value( 'start_date', $date ), 'class="form-control gen_slug" id="start_date" required="required"' ); ?>
            </div>

            <div class="form-group">
				<?= lang( 'end_date', 'end_date', 'required' ); ?>
				<?= form_input( 'end_date', set_value( 'end_date', $date ), 'class="form-control gen_slug" id="end_date" required="required"' ); ?>
            </div>

            <div class="form-group all">
		        <?= lang('status', 'status'); ?>
                <select name="status" class="form-control select" id="status" style="width:100%" required>
                    <option value="active"> <?= lang( 'active' ); ?></option>
                    <option value="inactive"> <?= lang( 'inactive' ); ?></option>
                </select>
            </div>

        </div>
        <div class="modal-footer">
			<?php echo form_submit( 'create_coupon', lang( 'create_a_new_coupon' ), 'class="btn btn-primary"' ); ?>
        </div>
		<?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" charset="UTF-8">
    $.fn.datetimepicker.dates['rerp'] = <?=$dp_lang?>;
</script>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        $.fn.datetimepicker.dates['rerp'] = <?=$dp_lang?>;
        $("#start_date").datetimepicker({
            format: site.dateFormats.js_ldate,
            fontAwesome: true,
            language: 'rerp',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0
        }).datetimepicker('update', new Date());
    });
</script>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        $.fn.datetimepicker.dates['rerp'] = <?=$dp_lang?>;
        $("#end_date").datetimepicker({
            format: site.dateFormats.js_ldate,
            fontAwesome: true,
            language: 'rerp',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0
        }).datetimepicker('update', new Date());
    });
</script>


