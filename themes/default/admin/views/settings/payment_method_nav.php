<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="box-icon">
	<ul class="btn-tasks">
		<li class="dropdown">
			<a href="<?= admin_url('pos/settings#authorize_con') ?>" class="toggle_up">
				<i class="icon fa fa-money"></i><span class="padding-right-10"><?= lang('Authorize.Net' ); ?></span>
			</a>
		</li>
		<li class="dropdown">
			<a href="<?= admin_url('system_settings/bank') ?>" class="toggle_up">
				<i class="icon fa fa-money"></i><span class="padding-right-10"><?= lang('bank' ); ?></span>
			</a>
		</li>
		<li class="dropdown">
			<a href="<?= admin_url('system_settings/cod') ?>" class="toggle_up">
				<i class="icon fa fa-money"></i><span class="padding-right-10"><?= lang('cod' ); ?></span>
			</a>
		</li>
		<li class="dropdown">
			<a href="<?= admin_url('system_settings/sslcommerz') ?>" class="toggle_up">
				<i class="icon fa fa-money"></i><span class="padding-right-10"><?= lang('sslcommerz' ); ?></span>
			</a>
		</li>
		<li class="dropdown">
			<a href="<?= admin_url('system_settings/paypal') ?>" class="toggle_up">
				<i class="icon fa fa-paypal"></i><span class="padding-right-10"><?= lang('paypal'); ?></span>
			</a>
		</li>
		<li class="dropdown">
			<a href="<?= admin_url('system_settings/skrill') ?>" class="toggle_down">
				<i class="icon fa fa-bank"></i><span class="padding-right-10"><?= lang('skrill'); ?></span>
			</a>
		</li>
	</ul>
</div>
