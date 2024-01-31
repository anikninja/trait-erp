<?php
if ( ! get_cookie( 'hide_subscribe_popup' ) && get_cookie( 'hide_subscribe_popup' ) != 'yes' ) {
	$subscription_pop = ci_parse_args( isset( $theme_options['subscription_pop'] ) ? $theme_options['subscription_pop'] : [], [
		'image_link' => [ 'content' => $assets.'images/subscription.jpg' ],
		'content_top' => [ 'content' => '' ],
		'content_bottom' => [ 'content' => '' ],
	] );
	?>
	<div class="subscribe-me" style="display: none;">
		<div class="sb-wrap">
			<div class="sb-img">
				<img src="<?= $subscription_pop['image_link']['content']; ?>">
			</div>
			<div class="sb-details">
				<a href="#close" class="sb-close-btn">&times;</a>
				<?= html_entity_decode( $subscription_pop['content_top']['content'] ); ?>
				<?= form_open( site_url( 'subscribe' ), 'id="subscribe_popup" class="form-group signup subscribe-form" method="post"' ); ?>
				<div class="form-group">
					<div class="input-box">
						<input autocomplete="off" type="email" placeholder="<?= lang( 'your_email__' ); ?>" value="" class="form-control" id="subs_email_popup" name="subs_email" required>
					</div>
					<div class="subscribe">
						<button class="btn btn-primary btn-default font-title" type="submit" name="submit"><?= lang( 'subscribe' ); ?></button>
					</div>
					<?= html_entity_decode( $subscription_pop['content_bottom']['content'] ); ?>
				</div>
				<?= form_close(); ?>
			</div>
		</div>
	</div>
<?php }
