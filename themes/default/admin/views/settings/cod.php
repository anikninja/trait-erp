<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        $('#cod_form').bootstrapValidator({
            message: 'Please enter/select a value',
            submitButtons: 'input[type="submit"]'
        });
        // var required_fields = ''
        // $('#active').change(function () {
        //     var v = $(this).val();
        //     if (v == 1) {
        //         $(required_fields).attr('required', 'required');
        //         $('#ssl_form').bootstrapValidator('addField', 'account_email');
        //     } else {
	    //         $(required_fields).removeAttr('required');
        //         $('#ssl_form').bootstrapValidator('removeField', 'account_email');
        //     }
        // });
        //var v = <?= $cod->active; ?>;
        //if (v == 1) {
	    //    $(required_fields).attr('required', 'required');
        //    $('#ssl_form').bootstrapValidator('addField', 'account_email');
        //} else {
	    //    $(required_fields).removeAttr('required');
        //    $('#ssl_form').bootstrapValidator('removeField', 'account_email');
        //}
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('cod_settings'); ?></h2>
	    <?php require_once 'payment_method_nav.php'; ?>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php $attrib = ['role' => 'form', 'id="cod_form"'];
                echo admin_form_open('system_settings/cod', $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang('activate', 'active'); ?>
                            <?php
                            $yn = ['1' => 'Yes', '0' => 'No'];
                            echo form_dropdown('active', $yn, $cod->active, 'class="form-control tip" required="required" id="active"');
                            ?>
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
