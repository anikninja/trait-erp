<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        $('#ssl_form').bootstrapValidator({
            message: 'Please enter/select a value',
            submitButtons: 'input[type="submit"]'
        });
        var required_fields = '#store_id,#store_password,#merchant_id,#account_email'
        $('#active').change(function () {
            var v = $(this).val();
            if (v == 1) {
                $(required_fields).attr('required', 'required');
                $('#ssl_form').bootstrapValidator('addField', 'account_email');
            } else {
	            $(required_fields).removeAttr('required');
                $('#ssl_form').bootstrapValidator('removeField', 'account_email');
            }
        });
        var v = <?=$sslcommerz->active;?>;
        if (v == 1) {
	        $(required_fields).attr('required', 'required');
            $('#ssl_form').bootstrapValidator('addField', 'account_email');
        } else {
	        $(required_fields).removeAttr('required');
            $('#ssl_form').bootstrapValidator('removeField', 'account_email');
        }
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('ssl_settings'); ?></h2>
	    <?php require_once 'payment_method_nav.php'; ?>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php $attrib = ['role' => 'form', 'id="ssl_form"'];
                echo admin_form_open('system_settings/sslcommerz', $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang('activate', 'active'); ?>
                            <?php
                            $yn = ['1' => 'Yes', '0' => 'No'];
                            echo form_dropdown('active', $yn, $sslcommerz->active, 'class="form-control tip" required="required" id="active"');
                            ?>
                        </div>
                        <div class="form-group">
                            <?= lang('ssl_store_id', 'store_id'); ?>
                            <?php echo form_input('store_id', $sslcommerz->store_id, 'class="form-control tip" id="store_id"'); ?>
                            <small class="help-block"><?= lang('ssl_store_id_tip'); ?></small>
                        </div>
                        <div class="form-group">
                            <?= lang('ssl_store_pass', 'store_password'); ?>
                            <?php echo form_input('store_password', $sslcommerz->store_password, 'class="form-control tip" id="store_password"'); ?>
                            <small class="help-block"><?= lang('ssl_store_pass_tip'); ?></small>
                        </div>
                        <div class="form-group">
                            <?= lang('ssl_merchant_id', 'merchant_id'); ?>
                            <?php echo form_input('merchant_id', $sslcommerz->merchant_id, 'class="form-control tip" id="merchant_id"'); ?>
                            <small class="help-block"><?= lang('ssl_merchant_id_tip'); ?></small>
                        </div>
                        <div class="form-group">
                            <?= lang('ssl_merchant_email', 'account_email'); ?>
                            <?php echo form_input('account_email', $sslcommerz->account_email, 'class="form-control tip" id="account_email"'); ?>
                            <small class="help-block"><?= lang('ssl_account_email_tip'); ?></small>
                        </div>
                        <div class="form-group">
                            <?= lang('fixed_charges', 'fixed_charges'); ?>
                            <?php echo form_input('fixed_charges', $sslcommerz->fixed_charges, 'class="form-control tip" id="fixed_charges"'); ?>
                            <small class="help-block"><?= lang('fixed_charges_tip'); ?></small>
                        </div>
                        <div class="form-group">
                            <?= lang('extra_charges_my', 'extra_charges_my'); ?>
                            <?php echo form_input('extra_charges_my', $sslcommerz->extra_charges_my, 'class="form-control tip" id="extra_charges_my"'); ?>
                            <small class="help-block"><?= lang('extra_charges_my_tip'); ?></small>
                        </div>
                        <div class="form-group">
                            <?= lang('extra_charges_others', 'extra_charges_other'); ?>
                            <?php echo form_input('extra_charges_other', $sslcommerz->extra_charges_other, 'class="form-control tip" id="extra_charges"'); ?>
                            <small class="help-block"><?= lang('extra_charges_others_tip'); ?></small>
                        </div>
                        <!--<div class="form-group">
                            <label><?= lang('ipn_link'); ?></label>
                            <span class="form-control" id="ipn_link"><?= admin_url('paypalipn'); ?></span>
                            <small class="help-block"><?= lang('ipn_link_tip'); ?></small>
                        </div>-->
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
