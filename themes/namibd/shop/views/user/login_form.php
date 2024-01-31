<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
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

<?= form_open('login', 'class="validate register-form outer-top-xs"'); ?>
    <div class="form-group">
        <label for="username" class="info-title control-label"><?= lang('identity1'); ?> <span>*</span></label>
        <input type="text" name="identity" id="username" class="form-control unicase-form-control text-input" value="" required placeholder="<?= lang('identity1'); ?>">
    </div>
    <div class="form-group">
        <label for="password" class="info-title control-label"><?= lang('password'); ?> <span>*</span></label>
        <input type="password" id="password" name="password" class="form-control unicase-form-control text-input" placeholder="<?= lang('password'); ?>" value="" required>
    </div>
    <div class="radio outer-xs">
        <label class="checkbox-inline">
            <input type="checkbox" name="remember_me" value="1">Remember me!
        </label>
        <a href="#" data-modal="forgot-password" class="pull-right">Forgot your Password?</a>
    </div>
    <button type="submit" value="login" name="login" class="btn-upper btn btn-primary checkout-page-button"><?= lang('login'); ?></button>
<?= form_close(); ?>
