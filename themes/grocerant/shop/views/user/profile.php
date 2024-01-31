<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="breadcrumb">
    <div class="container">
        <div class="breadcrumb-inner">
            <ul class="list-inline list-unstyled">
                <li><a href="<?= shop_url() ?>">Shop</a></li>
                <li class='active'>Profile</li>
            </ul>
        </div><!-- /.breadcrumb-inner -->
    </div><!-- /.container -->
</div><!-- /.breadcrumb -->

<div class="body-content">
    <div class="container">
        <div class="checkout-box">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-2">
                    <div class="panel-group">
                        <div class="panel panel-default order-nav-panel">
                            <ul class="orders-nav">
                                <li><a class="active" href="<?= site_url('profile'); ?>"><i class="mi fa fa-user"></i> <?= lang('profile'); ?></a></li>
                                <li><a href="<?= shop_url('addresses'); ?>"><i class="mi fa fa-building"></i> <?= lang('addresses'); ?></a></li>
                                <li><a href="<?= site_url('referral'); ?>"><i class="mi fa fa-building"></i> <?= lang('referral'); ?></a></li>
                                <li><a href="<?= site_url('wallet'); ?>"><i class="mi fa fa-building"></i> <?= lang('wallet'); ?></a></li>
                                <li><a class="" href="<?= shop_url('orders'); ?>"><i class="mi fa fa-heart"></i> <?= lang('orders'); ?></a></li>
                                <li><a href="<?= shop_url('quotes'); ?>"><i class="mi fa fa-heart-o"></i> <?= lang('quotes'); ?></a></li>
                                <li><a href="<?= site_url('logout'); ?>"><i class="mi fa fa-sign-out"></i> <?= lang('logout'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
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
                                        <?= form_input('first_name', set_value('first_name', $user->first_name), 'class="form-control tip" id="first_name" required="required" placeholder="Your First Name"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('last_name', 'last_name'); ?>
                                        <?= form_input('last_name', set_value('last_name', $user->last_name), 'class="form-control tip" id="last_name" required="required" placeholder="Your Last Name"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('phone', 'phone'); ?>
                                        <?= form_input('phone', set_value('phone', $customer->phone), 'class="form-control tip" id="phone" required="required" placeholder="Your Phone Number"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('email', 'email'); ?>
                                        <?= form_input('email', set_value('email', $customer->email), 'class="form-control tip" id="email" required="required" placeholder="Your Email Address"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('company', 'company'); ?>
                                        <?= form_input('company', set_value('company', $customer->company), 'class="form-control tip" id="company" placeholder="Your Company"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('vat_no', 'vat_no'); ?>
                                        <?= form_input('vat_no', set_value('vat_no', $customer->vat_no), 'class="form-control tip" id="vat_no" placeholder="Your Vat No."'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('billing_address', 'address'); ?>
                                        <?= form_input('address', set_value('address', $customer->address), 'class="form-control tip" id="address" required="required" placeholder="Your Address"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('city', 'city'); ?>
                                        <?= form_input('city', set_value('city', $customer->city), 'class="form-control tip" id="city" required="required" placeholder="City You Live In"'); ?>
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
                                            echo form_dropdown('state', $states, set_value('state', $customer->state), 'class="form-control selectpicker mobile-device" id="state" required="required"  placeholder="State/District You Live In"');
                                        } else {
                                            echo form_input('state', set_value('state', $customer->state), 'class="form-control" id="state" placeholder="State/District You Live In"');
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('postal_code', 'postal_code'); ?>
                                        <?= form_input('postal_code', set_value('postal_code', $customer->postal_code), 'class="form-control tip" id="postal_code" required="required" placeholder="Postal Code Of Your Residence"'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('country', 'country'); ?>
                                        <?= form_input('country', set_value('country', $customer->country), 'class="form-control tip" id="country" required="required" placeholder="Country You Live In"'); ?>
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
                                        <?= form_password('old_password', set_value('old_password'), 'class="form-control tip" id="old_password" placeholder="Type Your Old Password" required="required"'); ?>
                                    </div>
                                </div>
                            </div><!-- .row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('new_password', 'new_password'); ?>
                                        <?= form_password('new_password', set_value('new_password'), 'class="form-control tip" id="new_password" required="required" pattern="{6,}" placeholder="Create New Password For Your Grocerant Account"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('confirm_password', 'new_password_confirm'); ?>
                                        <?= form_password('new_password_confirm', set_value('new_password_confirm'), 'class="form-control tip" id="new_password_confirm" required="required" pattern="{6,}" placeholder="Confirm Your New Password" data-fv-identical="true" data-fv-identical-field="new_password" data-fv-identical-message="' . lang('pw_not_same') . '"'); ?>
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

<!-- Modal -->
<div class="modal fade" id="checkout-popup" tabindex="-1" role="dialog" aria-labelledby="checkout-popup" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="checkout-popup-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

