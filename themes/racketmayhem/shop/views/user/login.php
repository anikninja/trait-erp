<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if ( 'main' === $m && 'login' === $v ) { ?>
    <div class="breadcrumb">
        <div class="container">
            <div class="breadcrumb-inner">
                <ul class="list-inline list-unstyled">
                    <li><a href="<?php echo site_url(); ?>">Home</a></li>
                    <li class='active'>Login/Register</li>
                </ul>
            </div><!-- /.breadcrumb-inner -->
        </div><!-- /.container -->
    </div><!-- /.breadcrumb -->
<?php } ?>
<div class="body-content">
    <div class="container">
        <div class="sign-in-page">
            <div class="row">
                <!-- Sign-in -->
                <div class="col-md-6 col-sm-6 sign-in">
                    <h4 class="">Sign in</h4>
                    <div class="social-sign-in outer-top-xs">
                        <a href="<?= site_url( 'social_auth/login/Facebook' ); ?>" class="fb-sign-in">
                            <img src="<?= $assets; ?>images/social-auth-button/fb-button-short-normal.png" alt="">
                        </a>
                        <a href="<?= site_url( 'social_auth/login/Google' ); ?>" class="google-sign-in">
                            <img src="<?= $assets; ?>images/social-auth-button/google-button-short-normal.png" alt="">
                            <img src="<?= $assets; ?>images/social-auth-button/google-button-short-pressed.png" alt="">
                        </a>
                    </div>
                    <?php include 'login_form.php'; ?>
                </div>
                <!-- Sign-in -->

                <!-- create a new account -->
                <div class="col-md-6 col-sm-6 create-new-account">
                    <h4 class="checkout-subtitle">Create a new account</h4>
                    <?php $attrib = ['class' => 'validate register-form outer-top-xs', 'role' => 'form'];
                    echo form_open('register', $attrib); ?>
                    <div class="form-group">
                        <label class="info-title" for="first_name"><?= lang('first_name'); ?><span>*</span></label>
                        <?= form_input('first_name', set_value( 'first_name' ), 'class="form-control unicase-form-control text-input" id="first_name" required="required" pattern=".{3,10}"'); ?>
                    </div>
                    <div class="form-group">
                        <label class="info-title" for="last_name"><?= lang('last_name'); ?><span>*</span></label>
                        <?= form_input('last_name', set_value( 'last_name' ), 'class="form-control unicase-form-control text-input" id="last_name" required="required" pattern=".{3,10}"'); ?>
                    </div>
                    <div class="form-group">
                        <label class="info-title" for="company"><?= lang('company'); ?></label>
                        <?= form_input('company', set_value( 'company' ), 'class="form-control unicase-form-control text-input" id="company" pattern=".{3,10}"'); ?>
                    </div>
                    <div class="form-group">
                        <label class="info-title" for="phone"><?= lang('phone'); ?><span>*</span></label>
                        <?= form_input('phone', set_value( 'phone' ), 'class="form-control unicase-form-control text-input" id="phone" required="required" pattern=".{3,10}"'); ?>
                    </div>
                    <div class="form-group">
                        <label class="info-title" for="email"><?= lang('email'); ?><span>*</span></label>
                        <?= form_input('email', set_value( 'email' ), 'class="form-control unicase-form-control text-input" id="email" required="required" pattern=".{3,10}"'); ?>
                    </div>
                    <div class="form-group">
                        <label class="info-title" for="reg_username"><?= lang('username'); ?><span>*</span></label>
                        <?= form_input('username', set_value( 'username' ), 'class="form-control unicase-form-control text-input" id="reg_username" required="required" pattern=".{3,10}"'); ?>
                    </div>
                    <div class="form-group">
                        <label class="info-title" for="reg_password"><?= lang('password'); ?><span>*</span></label>
                        <?= form_password('password', '', 'class="form-control unicase-form-control text-input" id="reg_password" required="required" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"'); ?>
                        <span class="help-block"><?= lang('pasword_hint'); ?></span>
                    </div>
                    <div class="form-group">
                        <label class="info-title" for="confirm_password"><?= lang('confirm_password'); ?><span>*</span></label>
                        <?= form_password('password_confirm', '', 'class="form-control unicase-form-control text-input" id="password_confirm" required="required" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" data-bv-identical="true" data-bv-identical-field="password" data-bv-identical-message="' . lang('pw_not_same') . '"'); ?>
                    </div>
                    <?= form_submit('register', lang('register'), 'class="btn-upper btn btn-primary checkout-page-button"'); ?>
                    <?= form_close(); ?>
                </div>
                <!-- create a new account -->
            </div><!-- /.row -->
        </div>
    </div>
</div>
