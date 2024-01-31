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
        <div class="sign-in-page" style="margin-bottom: 20px">
            <div class="row">
                <!-- Sign-in -->
                <div class="col-md-6 col-sm-6 sign-in">
                    <h4 class="">Sign in</h4>
                    <div class="social-sign-in outer-top-xs">
                        <a href="<?= site_url( 'social_auth/login/Facebook' ); ?>" class="fb-sign-in">
                            <img src="<?= $assets; ?>images/social-auth-button/fb-button-short-normal.png" alt="<?= lang( 'login_with_facebook' ); ?>">
                        </a>
                        <a href="<?= site_url( 'social_auth/login/Google' ); ?>" class="google-sign-in">
                            <img src="<?= $assets; ?>images/social-auth-button/google-button-short-normal.png" alt="<?= lang( 'sign_in_with_google' ); ?>">
                            <img src="<?= $assets; ?>images/social-auth-button/google-button-short-pressed.png" alt="<?= lang( 'sign_in_with_google' ); ?>">
                        </a>
                    </div>
	                <?php include 'login_form.php'; ?>
                </div>
                <!-- Sign-in -->

                <!-- create a new account -->
                <div class="col-md-6 col-sm-6 create-new-account">
                    <h4 class="checkout-subtitle">Create a new account</h4>
	                <?php $attrib = ['class' => 'register-form outer-top-xs', 'role' => 'form'];
	                echo form_open('register', $attrib); ?>
		                <div class="form-group">
			                <label class="info-title" for="name"><?= lang('full_name' ); ?><span>*</span></label>
			                <?= form_input('name', '', 'class="form-control unicase-form-control text-input" id="name" required="required"'); ?>
		                </div>
                        <!-- div class="form-group">
                            <label class="info-title" for="first_name"><?= lang('first_name'); ?><span>*</span></label>
	                        <?= form_input('first_name', '', 'class="form-control unicase-form-control text-input" id="first_name" required="required" pattern=".{3,10}"'); ?>
                        </div>
                        <div class="form-group">
                            <label class="info-title" for="last_name"><?= lang('last_name'); ?><span>*</span></label>
	                        <?= form_input('last_name', '', 'class="form-control unicase-form-control text-input" id="last_name" required="required" pattern=".{3,10}"'); ?>
                        </div>
                        <div class="form-group">
                            <label class="info-title" for="company"><?= lang('company'); ?><span>*</span></label>
	                        <?= form_input('company', '', 'class="form-control unicase-form-control text-input" id="company" required="required" pattern=".{3,10}"'); ?>
                        </div -->
                        <div class="form-group">
                            <label class="info-title" for="phone"><?= lang('phone'); ?><span>*</span></label>
	                        <?= form_input('phone', '', 'class="form-control unicase-form-control text-input" id="phone" required="required" pattern="(\d){11,}"'); ?>
                        </div>
                        <div class="form-group">
                            <label class="info-title" for="email"><?= lang('email'); ?><span>*</span></label>
	                        <?= form_input( [ 'type' => 'email', 'name' => 'email' ], '', 'class="form-control unicase-form-control text-input" id="email" required="required"'); ?>
                        </div>
                        <!--div class="form-group">
                            <label class="info-title" for="reg_username"><?= lang('username'); ?><span>*</span></label>
	                        <?= form_input('username', '', 'class="form-control unicase-form-control text-input" id="reg_username" required="required" pattern=".{3,10}"'); ?>
                        </div -->
                        <div class="form-group">
                            <label class="info-title" for="reg_password"><?= lang('password'); ?><span>*</span></label>
	                        <?= form_password('password', '', 'class="form-control unicase-form-control text-input" id="reg_password" required="required" pattern=".{6,}" placeholder="Create New Password For Your Grocerant Account"'); ?>
                            <span class="help-block"><?= lang('pasword_hint1'); ?></span>
                        </div>
                        <div class="form-group">
                            <label class="info-title" for="confirm_password"><?= lang('confirm_password'); ?><span>*</span></label>
	                        <?= form_password('password_confirm', '', 'class="form-control unicase-form-control text-input" id="password_confirm" required="required" pattern=".{6,}" placeholder="Confirm Your New Password" data-bv-identical="true" data-bv-identical-field="password" data-bv-identical-message="' . lang('pw_not_same') . '"'); ?>
                        </div>
                        <div class="form-group">
                            <label class="info-title" for="referral_id"><?= lang('referral_id'); ?></label>
	                        <?= form_input('referral_id', get_cookie('referral_id', TRUE), 'class="form-control unicase-form-control text-input" id="referral_id" placeholder="Referral ID (Optional)"' ); ?>
                        </div>
	                    <?= form_submit('register', lang('register'), 'class="btn-upper btn btn-primary checkout-page-button"'); ?>
                    <?= form_close(); ?>
                </div>
                <!-- create a new account -->
            </div><!-- /.row -->
        </div>
    </div>
</div>
