<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function () {
        $('#authorizedotnet_form').bootstrapValidator({
            message: 'Please enter/select a value',
            submitButtons: 'input[type="submit"]'
        });
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('authorizedotnet_settings'); ?></h2>
	    <?php require_once 'payment_method_nav.php'; ?>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php $attrib = ['role' => 'form', 'id="authorizedotnet_form"'];
                echo admin_form_open('system_settings/authorizedotnet', $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang('activate', 'active'); ?>
                            <?php echo form_dropdown('active', [ '1' => 'Yes', '0' => 'No' ], $authorizedotnet->active, 'class="form-control tip" required="required" id="active"'); ?>
                        </div>
                        <div class="form-group">
		                    <?= lang('mode', 'mode'); ?>
		                    <?php echo form_dropdown('mode', [ 'sandbox' => 'Sandbox', 'live' => 'Live' ], $authorizedotnet->mode, 'class="form-control tip" required="required" id="mode"'); ?>
                        </div>
	                    <div class="form-group">
		                    <?= lang('api_login_id', 'api_login_id'); ?>
		                    <?php echo form_input('api_login_id', $authorizedotnet->api_login_id, 'class="form-control tip" id="api_login_id"'); ?>
	                    </div>
	                    <div class="form-group">
		                    <?= lang('transaction_key', 'transaction_key'); ?>
		                    <?php echo form_input('transaction_key', $authorizedotnet->transaction_key, 'class="form-control tip" id="transaction_key"'); ?>
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
