<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_customer'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-customer-form'];
        echo admin_form_open_multipart('customers/add_manually', $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-md-12">

                    <div class="form-group person">
                        <?= lang('full_name', 'name'); ?>
                        <?php echo form_input('name', '', 'class="form-control tip" id="name" required="required"'); ?>
                    </div>
                    <div class="form-group">
		                <?php echo lang('gender', 'gender'); ?>
                        <div class="controls">
			                <?php $opts = ['male' => lang('male'), 'female' => lang('female')];
			                echo form_dropdown('gender', $opts, '', 'class="form-control select" id="gender" required="required"'); ?>
                        </div>
                    </div>
                    <div class="form-group">
		                <?= lang('phone', 'phone'); ?>
                        <input type="text" name="phone" class="form-control" required="required" id="phone" pattern="(\d){11,}"/>
                    </div><div class="form-group">
                        <?= lang('email_address', 'email_address'); ?>
                        <input type="email" name="email" class="form-control" required="required" id="email_address"/>
                    </div>
                    <div class="form-group">
		                <?= lang('address', 'address'); ?>
		                <?php echo form_input('address', '', 'class="form-control" id="address" required="required"'); ?>
                    </div>
                    <div class="form-group">
		                <?= lang('city', 'city'); ?>
		                <?php echo form_input('city', '', 'class="form-control" id="city" required="required"'); ?>
                    </div>
                    <div class="form-group">
		                <?= lang('state', 'state'); ?>
		                <?php
		                if ($Settings->indian_gst) {
			                $states = $this->gst->getIndianStates(true);
			                echo form_dropdown('state', $states, '', 'class="form-control select" id="state" required="required"');
		                } else {
			                echo form_input('state', '', 'class="form-control" id="state"');
		                }
		                ?>
                    </div>
                    <div class="form-group">
		                <?= lang('postal_code', 'postal_code'); ?>
		                <?php echo form_input('postal_code', '', 'class="form-control" id="postal_code"'); ?>
                    </div>
                    <div class="form-group">
		                <?= lang('country', 'country'); ?>
		                <?php echo form_input('country', '', 'class="form-control" id="country"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('referral_id', 'referral_id'); ?>
                        <?php echo form_input('referral_id', get_cookie('referral_id', TRUE), 'class="form-control" id="referral_id" placeholder="Referral ID (Optional)"'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_customer', lang('add_customer'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

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
