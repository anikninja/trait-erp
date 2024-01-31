<?php defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );
/**
 * @var $shipment Erp_Shipment
 */
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= sprintf( lang( 'add_x' ), lang( 'new_shipment' ) ); ?></h4>
        </div>
	    <?php $attrib = [ 'data-toggle' => 'validator', 'role' => 'form' ];
	    echo admin_form_open_multipart( 'delivery/add_shipment', $attrib ); ?>
        <div class="modal-body">
                <div class="form-group">
			        <?= lang( 'shipment_date', 'shipment_date', 'required' ); ?>
			        <?= form_input( 'shipment_date', set_value( 'shipment_date', $date ), 'class="form-control gen_slug" id="date" required="required"' ); ?>
                </div>

                <div class="form-group all">
			        <?= lang( 'shipment_no', 'shipment_no' ); ?>
			        <?= form_input( 'shipment_no', ( isset( $_POST['shipment_no'] ) ? $_POST['shipment_no'] : $shipment_reference_no ), 'class="form-control tip" id="shipment_no"' ); ?>
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
		        <?php echo form_submit( 'create_shipment', lang( 'create_a_new_shipment' ), 'class="btn btn-primary"' ); ?>
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

