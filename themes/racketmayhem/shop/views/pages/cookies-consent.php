<?php if ( ! get_cookie( 'shop_use_cookie' ) && get_cookie( 'shop_use_cookie' ) != 'accepted' && ! empty( $shop_settings->cookie_message ) ) { ?>
	<div class="cookie-warning">
		<div class="message">
			<h6 class="heading"><?= lang( 'we_use_cookie' ); ?></h6>
			<p class="content"><?php echo $shop_settings->cookie_message; ?></p>
		</div>
		<div class="cookie-buttons">
			<a class="button accept" href="<?= site_url('main/cookie/accepted'); ?>" class="btn btn-sm btn-primary" style="float: right;"><?= lang('i_accept'); ?></a>
			<?php
			if ( ! empty( $shop_settings->cookie_link ) ) { ?>
				<a class="button more" href="<?= site_url('page/' . $shop_settings->cookie_link ); ?>"><?php echo lang('read_more'); ?></a>
			<?php } ?>
		</div>
	</div>
<?php } ?>
