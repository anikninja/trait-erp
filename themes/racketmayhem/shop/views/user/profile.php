<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include __DIR__ . '/../pages/breadcrumb.php'; ?>
<div class="body-content">
    <div class="container">
        <div class="checkout-box">
            <div class="row">
	            <?php include __DIR__ .'/../pages/profile_navs.php'; ?>
                <div class="col-xs-12 col-sm-12 col-md-10">
                    <div class="panel-group">
                        <div class="panel panel-default profile-panel">
                            <div class="panel-heading text-bold">
                                <?= lang('fill_form'); ?>
                            </div><!-- .panel-heading -->
                            <?= form_open('profile/user', 'class="validate"'); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('first_name', 'first_name'); ?>
                                        <?= form_input('first_name', set_value('first_name', $user->first_name), 'class="form-control tip" id="first_name" required="required"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('last_name', 'last_name'); ?>
                                        <?= form_input('last_name', set_value('last_name', $user->last_name), 'class="form-control tip" id="last_name" required="required"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('phone', 'phone'); ?>
                                        <?= form_input('phone', set_value('phone', $customer->phone), 'class="form-control tip" id="phone" required="required"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('email', 'email'); ?>
                                        <?= form_input('email', set_value('email', $customer->email), 'class="form-control tip" id="email" required="required"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('company', 'company'); ?>
                                        <?= form_input('company', set_value('company', $customer->company), 'class="form-control tip" id="company"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('vat_no', 'vat_no'); ?>
                                        <?= form_input('vat_no', set_value('vat_no', $customer->vat_no), 'class="form-control tip" id="vat_no"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('billing_address', 'address'); ?>
                                        <?= form_input('address', set_value('address', $customer->address), 'class="form-control tip" id="address" required="required"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('city', 'city'); ?>
                                        <?= form_input('city', set_value('city', $customer->city), 'class="form-control tip" id="city" required="required"'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('state', 'state'); ?>
                                        <?php
                                        if ($Settings->indian_gst) {
                                            $states = $this->gst->getIndianStates(true);
                                            echo form_dropdown('state', $states, set_value('state', $customer->state), 'class="form-control selectpicker mobile-device" id="state" required="required"');
                                        } else {
                                            echo form_input('state', set_value('state', $customer->state), 'class="form-control" id="state"');
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('postal_code', 'postal_code'); ?>
                                        <?= form_input('postal_code', set_value('postal_code', $customer->postal_code), 'class="form-control tip" id="postal_code" required="required"'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('country', 'country'); ?>
                                        <?= form_input('country', set_value('country', $customer->country), 'class="form-control tip" id="country" required="required"'); ?>
                                    </div>
                                </div>
                            </div>

                            <?= form_submit('billing', lang('update'), 'class="btn btn-primary"'); ?>
                            <?php echo form_close(); ?>
                        </div>
                        <div class="panel panel-default profile-panel">
                            <div class="panel-heading text-bold">
                                <?= lang('update_password'); ?>
                            </div><!-- .panel-heading -->
                            <?= form_open('profile/password', 'class="validate"'); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('current_password', 'old_password'); ?>
                                        <?= form_password('old_password', set_value('old_password'), 'class="form-control tip" id="old_password" required="required"'); ?>
                                    </div>
                                </div>
                            </div><!-- .row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('new_password', 'new_password'); ?>
                                        <?= form_password('new_password', set_value('new_password'), 'class="form-control tip" id="new_password" required="required" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" data-fv-regexp-message="' . lang('pasword_hint') . '"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('confirm_password', 'new_password_confirm'); ?>
                                        <?= form_password('new_password_confirm', set_value('new_password_confirm'), 'class="form-control tip" id="new_password_confirm" required="required" data-fv-identical="true" data-fv-identical-field="new_password" data-fv-identical-message="' . lang('pw_not_same') . '"'); ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <?= form_submit('change_password', lang('change_password'), 'class="btn btn-primary"'); ?>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.row -->
        </div><!-- /.checkout-box -->
    </div><!-- /.container -->
</div><!-- /.body-content -->

