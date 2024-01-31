<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        $('#bank_form').bootstrapValidator({
            message: 'Please enter/select a value',
            submitButtons: 'input[type="submit"]'
        });
        // var required_fields = '#details'
        // $('#active').change(function () {
        //     var v = $(this).val();
        //     if (v == 1) {
        //         $(required_fields).attr('required', 'required');
        //         $('#bank_form').bootstrapValidator('addField', 'details');
        //     } else {
	    //         $(required_fields).removeAttr('required');
        //         $('#bank_form').bootstrapValidator('removeField', 'details');
        //     }
        // });
        //var v = <?= $bank->active; ?>;
        // if (v == 1) {
	    //     $(required_fields).attr('required', 'required');
        //     $('#bank_form').bootstrapValidator('addField', 'details');
        // } else {
	    //     $(required_fields).removeAttr('required');
        //     $('#bank_form').bootstrapValidator('removeField', 'details');
        // }
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('bank_settings'); ?></h2>
	    <?php require_once 'payment_method_nav.php'; ?>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php $attrib = ['role' => 'form', 'id="bank_form"'];
                echo admin_form_open('system_settings/bank', $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang('activate', 'active'); ?>
                            <?php echo form_dropdown('active', [ '1' => 'Yes', '0' => 'No' ], $bank->active, 'class="form-control tip" required="required" id="active"'); ?>
                        </div>
	                    <div class="form-group">
		                    <?= lang('bank_details', 'details'); ?>
		                    <?php echo form_textarea('details', $bank->details, 'class="form-control tip" id="details"'); ?>
		                    <small class="help-block"><?= lang('bank_details_tip'); ?></small>
	                    </div>
	                    <div class="form-group">
		                    <?= lang('fixed_charges', 'fixed_charges'); ?>
		                    <?php echo form_input('fixed_charges', $bank->fixed_charges, 'class="form-control tip" id="fixed_charges"'); ?>
		                    <small class="help-block"><?= lang('fixed_charges_tip'); ?></small>
	                    </div>
	                    <div class="form-group">
		                    <?= lang('extra_charges_my', 'extra_charges_my'); ?>
		                    <?php echo form_input('extra_charges_my', $bank->extra_charges_my, 'class="form-control tip" id="extra_charges_my"'); ?>
		                    <small class="help-block"><?= lang('extra_charges_my_tip'); ?></small>
	                    </div>
	                    <div class="form-group">
		                    <?= lang('extra_charges_others', 'extra_charges_other'); ?>
		                    <?php echo form_input('extra_charges_other', $bank->extra_charges_other, 'class="form-control tip" id="extra_charges"'); ?>
		                    <small class="help-block"><?= lang('extra_charges_others_tip'); ?></small>
	                    </div>
                    </div>
                </div>
                <div style="clear: both; height: 10px;"></div>
                <div class="form-group">
                    <?php echo form_submit('update_settings', lang('update_settings'), 'class="btn btn-primary"'); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
