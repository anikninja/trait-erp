<?php defined('BASEPATH') or exit('No direct script access allowed');
$new_password = [
	'name'                   => 'new',
	'id'                     => 'new',
	'type'                   => 'password',
	'class'                  => 'form-control',
	'required'               => 'required',
	'pattern'                => '.{6,}',
	'data-fv-regexp-message' => lang( 'pasword_hint1' ),
	'placeholder'            => 'Create New Password For Your Grocerant Account',
];
$new_password_confirm = [
	'name'                      => 'new_confirm',
	'id'                        => 'new_confirm',
	'type'                      => 'password',
	'class'                     => 'form-control',
	'required'                  => 'required',
	'data-fv-identical'         => 'true',
	'data-fv-identical-field'   => 'new',
	'data-fv-identical-message' => lang( 'pw_not_same' ),
	'placeholder'               => 'Confirm Your New Password',
];
?>
<div class="breadcrumb">
	<div class="container">
		<div class="breadcrumb-inner">
			<ul class="list-inline list-unstyled">
				<li><a href="<?= site_url(); ?>">Home</a></li>
				<li><a href="<?= site_url('login') ?>">Login/Register</a></li>
				<li class='active'>Reset password</li>
			</ul>
		</div><!-- /.breadcrumb-inner -->
	</div><!-- /.container -->
</div><!-- /.breadcrumb -->
<div class="body-content">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3 col-xs-12 sign-in-page" style="margin-bottom: 20px">
				<div class="row">
					<div class="col-xs-12 sign-in">
						<h4><?= sprintf( lang( 'reset_password_email' ), $identity_label ); ?></h4>
						<?php
						if ($error) {
							?>
							<div class="alert alert-danger">
								<button data-dismiss="alert" class="close" type="button">×</button>
								<ul class="list-group"><?= $error; ?></ul>
							</div>
							<?php
						}
						if ($message) {
							?>
							<div class="alert alert-success">
								<button data-dismiss="alert" class="close" type="button">×</button>
								<ul class="list-group"><?= $message; ?></ul>
							</div>
							<?php
						}
						?>
						<?= form_open( 'reset_password/' . $code, 'class="validate"' ); ?>
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-key"></i></span>
								<?= form_input( $new_password ); ?>
							</div>
							<span class="help-block"><?= lang('pasword_hint1') ?></span>
						</div>
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-key"></i></span>
								<?= form_input( $new_password_confirm ); ?>
							</div>
						</div>
						<?= form_input( $user_id ); ?>
						<div class="form-action clearfix">
							<a class="btn btn-success pull-left login_link text-white" href="<?= site_url('login') ?>">
								<i class="fa fa-arrow-left"></i> <?= lang('back_to_login') ?>
							</a>
							<?= form_submit('reset_password', lang('reset_password'), 'class="btn-upper btn btn-primary checkout-page-button pull-right"'); ?>
						</div>
						<?= form_close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
