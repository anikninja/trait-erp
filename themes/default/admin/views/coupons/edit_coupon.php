<?php defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );
/**
 * @var $coupon Erp_Coupon
 * @var string $assets
 * @var string $dp_lang
 * @var string $modal_js
 */
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
            <h4 class="modal-title" id="myModalLabel"><?= sprintf( lang( 'edit_x' ), lang( 'coupon' ) ); ?></h4>
        </div>
		<?php $attrib = [ 'data-toggle' => 'validator', 'role' => 'form' ];
		echo admin_form_open_multipart( 'coupons/edit_coupon/' . $coupon->getId(), $attrib ); ?>
        <div class="modal-body">
            <div class="form-group all">
                <?= lang( 'coupon_code', 'coupon_code' ); ?>
				<?= form_input( 'coupon_code', $coupon->getCouponCode(), 'class="form-control tip" id="coupon_code"' ); ?>
            </div>

            <div class="form-group all">
	            <?= lang('coupon_type', 'coupon_type'); ?>
                <select name="coupon_type" class="form-control select" id="coupon_type" style="width:100%" required>
                    <option value="fixed_cart" <?= selected( $coupon->getCouponType(), 'fixed_cart' ); ?>><?= lang( 'fixed_cart_discount' ); ?></option>
                    <option value="fixed_product" <?= selected( $coupon->getCouponType(), 'fixed_product' ); ?>><?= lang( 'fixed_product_discount' ); ?></option>
                    <option value="percentage" <?= selected( $coupon->getCouponType(), 'percentage' ); ?>><?= lang( 'percentage_discount' ); ?></option>
                </select>
            </div>

            <div class="form-group all">
	            <?= lang( 'coupon_amount', 'coupon_amount' ); ?>
				<?= form_input( 'coupon_amount', $coupon->getCouponAmount(), 'class="form-control tip" id="coupon_amount"' ); ?>
            </div>

            <div class="form-group">
	            <?= lang('description', 'description'); ?>
		        <?php echo form_textarea( [ 'name' => 'description', 'rows' => '', 'cols' => '' ], $coupon->getDescription(), 'rows="3" class="form-control skip" id="description"'); ?>
            </div>

            <div class="form-group">
	            <?= lang( 'start_date', 'start_date', 'required' ); ?>
				<?= form_input( 'start_date', set_value( 'start_date', $this->rerp->hrld($coupon->getStartDate()) ), 'class="form-control gen_slug" id="start_date" required="required"' ); ?>
            </div>

            <div class="form-group">
	            <?= lang( 'end_date', 'end_date', 'required' ); ?>
				<?= form_input( 'end_date', set_value( 'end_date', $this->rerp->hrld($coupon->getEndDate()) ), 'class="form-control gen_slug" id="end_date" required="required"' ); ?>
            </div>

            <div class="form-group all">
	            <?= lang('status', 'status'); ?>
                <select name="status" class="form-control select" id="status" style="width:100%" required>
                    <option value="active" <?= selected( $coupon->getStatus(), 'active' ); ?>><?= lang( 'active' ); ?></option>
                    <option value="inactive" <?= selected( $coupon->getStatus(), 'inactive' ); ?>><?= lang( 'inactive' ); ?></option>
                </select>
            </div>

        </div>
        <div class="modal-footer">
			<?php echo form_submit( 'update_coupon', lang( 'update_coupon' ), 'class="btn btn-primary"' ); ?>
        </div>
		<?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript" src="<?= $assets; ?>js/custom.js"></script>
<script type="text/javascript" charset="UTF-8">
    $.fn.datetimepicker.dates['rerp'] = <?= $dp_lang; ?>;
</script>
<?= $modal_js; ?>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        $.fn.datetimepicker.dates['rerp'] = <?= $dp_lang; ?>;
    });
</script>
