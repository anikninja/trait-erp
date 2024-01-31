<?php defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );
/**
 * @var $pickup Erp_Shipment
 */
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= sprintf( lang( 'add_x' ), lang( 'new_pickup' ) ); ?></h4>
        </div>
	    <?php $attrib = [ 'data-toggle' => 'validator', 'role' => 'form' ];
	    echo admin_form_open_multipart( 'delivery/add_pickup', $attrib ); ?>
        <div class="modal-body">
                <div class="form-group">
			        <?= lang( 'pickup_date', 'pickup_date', 'required' ); ?>
			        <?= form_input( 'pickup_date', set_value( 'pickup_date', $date ), 'class="form-control gen_slug" id="date" required="required"' ); ?>
                </div>

                <div class="form-group all">
			        <?= lang( 'pickup_no', 'pickup_no' ); ?>
			        <?= form_input( 'pickup_no', ( isset( $_POST['pickup_no'] ) ? $_POST['pickup_no'] : $pickup_reference_no ), 'class="form-control tip" id="pickup_no"' ); ?>
                </div>

                <div class="well well-sm">
                    <div class="form-group" style="margin-bottom:0;">
                        <div class="input-group wide-tip">
					        <?= lang( 'do_reference_no', 'delivery_id' ); ?>
                            <select name="delivery_id" class="form-control select" id="delivery_id" style="width:100%" required>
                                <option value=0 <?= (!isset($id)) ? 'selected' : NULL; ?>><?= lang( 'select_a_delivery_reference_no' ); ?></option>
						        <?php
						        foreach ( $delivery_ref as $del_ref ) {
							        ?>
                                    <option value="<?= $del_ref->id; ?>" <?= (isset($id) && $id == $del_ref->id) ? 'selected' : NULL; ?> ><?= $del_ref->do_reference_no; ?></option>
							        <?php
						        }
						        ?>
                            </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
        </div>
        <div class="modal-footer">
		        <?php echo form_submit( 'create_pickup', lang( 'create_a_new_pickup' ), 'class="btn btn-primary"' ); ?>
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
        $("#date").datetimepicker({
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
<script type="text/javascript">
    $(document).ready(function (e) {
        $('#add-customer-form').bootstrapValidator({
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            }, excluded: [':disabled']
        });
        $('select.select').select2({minimumResultsForSearch: 7});
        fields = $('.modal-content').find('.form-control');
        $.each(fields, function () {
            var id = $(this).attr('id');
            var iname = $(this).attr('name');
            var iid = '#' + id;
            if (!!$(this).attr('data-bv-notempty') || !!$(this).attr('required')) {
                $("label[for='" + id + "']").append(' *');
                $(document).on('change', iid, function () {
                    $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', iname);
                });
            }
        });
    });
</script>

