<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-xs-12 col-sm-12 col-md-2">
	<div class="panel-group">
		<div class="panel panel-default order-nav-panel my-account-nav">
			<ul class="orders-nav">
				<?php
				foreach ( cs_get_my_account_navs( $m, $v ) as $nav ) {
					$class = 'item';
					if ( $nav['active'] ) $class .= ' active';
					if ( $nav['class'] ) $class .= ' ' . $nav['class'];
					if ( ! $nav['login'] ) {
						continue;
					}
					?>
					<li><a class="<?= $class; ?>" href="<?= $nav['link']; ?>"><i class="fa fa-<?= $nav['icon'] ?>"></i> <?= $nav['label']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
