
<!-- ============================================================= FOOTER ============================================================= -->
<footer id="footer" class="footer color-bg" style="position: absolute;width: 100%">
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <?php if ( isset($theme_options['footer_main']['widgets']) && ! empty($theme_options['footer_main']['widgets']) ) {
                    foreach ( $theme_options['footer_main']['widgets'] as $widgets ) {
                        echo '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">';
                        foreach ( $widgets as $widget ) {
	                        if ( $widget['type'] == 'custom' ) {
		                        echo html_entity_decode( $widget['content'] );
	                        } elseif ( $widget['type'] == 'url_list' && ! empty( $widget['list'] ) ) {
		                        if ( $widget['label'] ) {
			                        printf( '<div class="module-heading"><h4 class="module-title">%s</h4></div>', $widget['label'] );
		                        }
		                        echo '<div class="module-body">';
		                        echo '<ul class="list-unstyled">';
		                        foreach ( $widget['list'] as $k => $link ) {
			                        if ( ! isset( $link['label'] ) || ! isset( $link['url'] ) ) {
				                        continue;
			                        }
			
			                        if ( '#' !== $link['url'] && false === strpos( $link['url'], 'http://' ) && false === strpos( $link['url'], 'https://' ) ) {
				                        $link['url'] = site_url( $link['url'] );
			                        }
			                        if ( 0 === $k ) {
				                        $class = 'first item';
			                        } elseif ( count( $widget['list'] ) - 1 === $k ) {
				                        $class = 'last item';
			                        } else {
				                        $class = 'item';
			                        }
			                        $link_class = strtolower( $link['label'] );
			                        $link_class = str_replace( [ ' ' ], '-', $link_class );
			                        printf(
				                        '<li class="%3$s"><a class="%4$s" href="%2$s" title="%1$s" target="%5$s">%1$s</a></li>',
				                        $link['label'], $link['url'], $class, $link_class, $link['target']
			                        );
		                        }
		                        echo '</ul>';
		                        echo '</div>';
	                        }
                        }
	                    echo '</div>';
                    }
                } ?>
            </div>
        </div>
    </div>
	<?php if ( isset( $theme_options['footer_bottom']['widgets'] ) && ! empty( $theme_options['footer_bottom']['widgets'] ) ) {
		$widgets = $theme_options['footer_bottom']['widgets'];
		$copyright = '';
		$paymentMethods = '';
		if ( isset( $widgets[0][0] ) && 'copyright' == $widgets[0][0]['type'] ) {
			$copyright = html_entity_decode( $widgets[0][0]['content'] );
			$copyright = trim( $copyright );
			$copyright = str_replace( '{year}', date( 'Y' ), $copyright );
			if ( $copyright ) {
				$copyright .= ' ';
			}
		}
		if ( isset( $widgets[1][0] ) && 'custom' == $widgets[1][0]['type'] ) {
			$paymentMethods = html_entity_decode( $widgets[1][0]['content'] );
		}
	?>
	<div class="copyright-bar">
		<div class="container">
			<div class="col-xs-12 col-sm-6 col-lg-8 copyright"><?= $copyright; ?>Developed and Maintained by <a href="https://innovizz.com/">Innovizz Technologies Limited</a></div>
			<div class="col-xs-12 col-sm-6 col-lg-4"><?= $paymentMethods; ?></div>
		</div>
	</div>
	<?php } ?>
</footer>
<!-- ============================================================= FOOTER : END============================================================= -->
	<?php include __DIR__ . '/pages/cart.php'; ?>
	<?php include __DIR__ . '/pages/product-details-modal.php'; ?>
	<?php include __DIR__ . '/user/login-modal.php'; ?>
	<script>
	var pageNow = '<?= $pageNow; ?>', m = '<?= $m; ?>', v = '<?= $v; ?>',
		filters = <?php echo isset( $filters ) && ! empty( $filters ) ? json_encode( $filters ) : '{}'; ?>,
		shop_color, shop_grid, sorting;
	var cart = <?php echo isset( $cart ) && ! empty( $cart ) ? json_encode( $cart ) : '{}' ?>;
	var site = {
		base_url: '<?= base_url(); ?>',
		site_url: '<?= site_url('/'); ?>',
		shop_url: '<?= shop_url(); ?>',
		csrf_token: '<?= $this->security->get_csrf_token_name() ?>',
		csrf_token_value: '<?= $this->security->get_csrf_hash() ?>',
		settings: {
			display_symbol: '<?= $Settings->display_symbol; ?>',
			symbol: '<?= $Settings->symbol; ?>',
			decimals: <?= $Settings->decimals; ?>,
			thousands_sep: '<?= $Settings->thousands_sep; ?>',
			decimals_sep: '<?= $Settings->decimals_sep; ?>',
			order_tax_rate: false,
			products_page: <?= $shop_settings->products_page ? 1 : 0; ?>
		},
		shop_settings: {
			private: <?= $shop_settings->private ? 1 : 0; ?>,
			hide_price: <?= $shop_settings->hide_price ? 1 : 0; ?>,
		},
		assets_url: '<?php echo $assets; ?>',
		countries: '<?= $this->loggedIn ? json_encode( $this->shop_model->getShippingCountries( true ) ) : ''; ?>',
	};
	var lang = {
		yes: '<?= lang( 'yes' ); ?>',
		no: '<?= lang( 'no' ); ?>',
		page_info : '<?= lang('page_info'); ?>',
		title : '<?= lang('title'); ?>',
		cart_empty : '<?= lang('empty_cart'); ?>',
		item : '<?= lang('item'); ?>',
		items : '<?= lang('items'); ?>',
		unique : '<?= lang('unique'); ?>',
		total_items : '<?= lang('total_items'); ?>',
		total_unique_items : '<?= lang('total_unique_items'); ?>',
		tax : '<?= lang('tax'); ?>',
		shipping : '<?= lang('shipping'); ?>',
		total_w_o_tax : '<?= lang('total_w_o_tax'); ?>',
		product_tax : '<?= lang('product_tax'); ?>',
		order_tax : '<?= lang('order_tax'); ?>',
		total : '<?= lang('total'); ?>',
		grand_total : '<?= lang('grand_total'); ?>',
		reset_pw : '<?= lang('forgot_password?'); ?>',
		type_email : '<?= lang('type_email_to_reset'); ?>',
		identity: '<?= lang( 'identity1' ); ?>',
		submit : '<?= lang('submit'); ?>',
		error : '<?= lang('error'); ?>',
		add_address : '<?= lang('add_address'); ?>',
		update_address : '<?= lang('update_address'); ?>',
		fill_form : '<?= lang('fill_form'); ?>',
		already_have_max_addresses : '<?= lang('already_have_max_addresses'); ?>',
		send_email_title : '<?= lang('send_email_title'); ?>',
		message_sent : '<?= lang('message_sent'); ?>',
		add_to_cart : '<?= lang('add_to_cart'); ?>',
		remove_from_wishlist : '<?= lang('remove_from_wishlist'); ?>',
		wishlist_count : '<?= lang('wishlist_count'); ?>',
		add_to_wishlist : '<?= lang('add_to_wishlist'); ?>',
		x_product : '<?= lang('x_product'); ?>',
		r_u_sure : '<?= lang('r_u_sure'); ?>',
		x_reverted_back: "<?= lang('x_reverted_back'); ?>",
		delete : '<?= lang('delete'); ?>',
		address : '<?= lang('address'); ?>',
		line1 : '<?= lang('line1'); ?>',
		line2 : '<?= lang('line2'); ?>',
		country : '<?= lang('country'); ?>',
		state : '<?= lang('state'); ?>',
		city : '<?= lang('city'); ?>',
		postal_code : '<?= lang('postal_code'); ?>',
		area : '<?= lang('area'); ?>',
		phone : '<?= lang('phone'); ?>',
		is_required : '<?= lang('is_required'); ?>',
		x_is_required : '<?= lang('x_is_required'); ?>',
		x_is_invalid : '<?= lang('x_is_invalid'); ?>',
		okay : '<?= lang('okay'); ?>',
		cancel : '<?= lang('cancel'); ?>',
		email_is_invalid : '<?= lang('email_is_invalid'); ?>',
		name : '<?= lang('name'); ?>',
		full_name : '<?= lang('full_name'); ?>',
		email : '<?= lang('email'); ?>',
		subject : '<?= lang('subject'); ?>',
		message : '<?= lang('message'); ?>',
		overview : '<?= lang('overview'); ?>',
		required_invalid : '<?= lang('required_invalid'); ?>',
		slots_for_x: '<?= lang( 'slots_for_x' ); ?>',
		available_options: '<?= lang( 'available_options' ); ?>',
		option_color: '<?= lang( 'option_color' ); ?>',
		option_size: '<?= lang( 'option_size' ); ?>',
		option_option: '<?= lang( 'option_option' ); ?>',
		select_option: '<?= lang( 'select_option' ); ?>',
		buy_now: '<?= lang( 'buy_now' ); ?>',
		share_message: '<?= lang( 'share_message' ); ?>',
		availability_: '<?= lang( 'availability_' ); ?>',
		sku_: '<?= lang( 'sku_' ); ?>',
		in_stock: '<?= lang( 'in_stock' ); ?>',
		backorder: '<?= lang( 'backorder' ); ?>',
		out_of_stock : '<?= lang('out_of_stock'); ?>',
		faq_full: '<?= lang( 'faq_full' ); ?>',
		_days_: '<?= lang( '_days_' ); ?>',
		_hours_: '<?= lang( '_hours_' ); ?>',
		_mins_: '<?= lang( '_mins_' ); ?>',
		_secs_: '<?= lang( '_secs_' ); ?>',
		promo_ends_in: '<?= lang( 'promo_ends_in' ); ?>',
		view_cart: '<?= lang( 'view_cart' ); ?>',
		sold_out: '<?= lang( 'sold_out' ); ?>',
		ajax_call_failed: '<?= lang( 'ajax_call_failed' ); ?>',
		select_x: '<?= lang( 'select_x' ); ?>',
		cash_back: '<?= lang( 'cash_back' ); ?>',
		phone_number: '<?= lang( 'phone_number' ); ?>',
	};
	var restoreHome = {
		url: '<?php echo site_url( '/' ); ?>',
		title: '<?php echo $home_page_title; ?>',
		description: '<?php echo $home_page_desc; ?>',
		image: '<?php echo base_url( 'assets/uploads/logos/' . $shop_settings->logo ); ?>',
	};
	var sys_alerts = [
		<?php if ( $message ) { ?>
		{
			t: '<?=lang('success'); ?>',
			m: '<?= trim( str_replace( [ "\r", "\n", "\r\n" ], '', addslashes( $reminder ) ) ); ?>',
			l: 'success',
			o: false,
		},
		<?php }
		if ( $reminder ) { ?>
		{
			t: '<?=lang('reminder'); ?>',
			m: '<?= trim( str_replace( [ "\r", "\n", "\r\n" ], '', addslashes( $reminder ) ) ); ?>',
			l: 'info',
			o: false,
		},
		<?php }
		if ( $warning ) { ?>
		{
			t: '<?=lang('warning'); ?>',
			m: '<?= trim( str_replace( [ "\r", "\n", "\r\n" ], '', addslashes( $warning ) ) ); ?>',
			l: 'warning',
			o: false,
		},
		<?php }
		if ( $error ) { ?>
		{
			t: '<?=lang('error'); ?>',
			m: '<?= trim( str_replace( [ "\r", "\n", "\r\n" ], '', addslashes( $error ) ) ); ?>',
			l: 'error',
			o: true,
		},
		<?php } ?>
	];
	var shipping = <?= json_encode( $shippingZone ); ?>;
	<?php if ( $view_product ) { ?>
	var viewProduct = <?php echo json_encode( $view_product );?>;
	<?php } else { ?>
	var viewProduct = false;
	<?php } ?>
	<?php if ( $color_mappings && is_array( $color_mappings ) ) { ?>
	var color_mappings = <?= json_encode( $color_mappings ); ?>
	<?php } else { ?>
	var color_mappings = false;
	<?php } ?>
</script>
	<?php
	$javaScripts = [
		'libs.min.js',
//		'wow.min.js',
		'jquery.easing-1.3.min.js',
		'echo.min.js',
//		'owl2.carousel.js',
		'owl.carousel.min.js',
		'slider.js',
		'bootstrap-slider.min.js',
//		'jquery.rateit.min.js',
//		'lightbox.min.js',
//		'bootstrap-select.min.js',
		'jquery-scrolltofixed-min.js',
//		'jquery.smartmenus.min.js',
//        'jquery.subscribe-better.min.js',
//		'chosen.jquery.js',
//		'jquery.autocomplete.min.js',
//		'jquery.elevateZoom-3.0.8.min.js',
		'datepicker.min.js',
		'select2.min.js',
	];
	
	if ( ( $pageNow == 'cart_ajax/checkout' || $pageNow == 'shop/orders' ) && ! empty( $authorize['api_login_id'] ) ) {
		$javaScripts[] = 'imask.min.js';
		$javaScripts[] = 'bank-cards.js';
	}
	$javaScripts[] = 'scripts.js';
	foreach ( $javaScripts as $js ) {
		printf( '<script defer src="%sjs/%s"></script>', $assets, $js );
	}
	?>
	<script><?= html_entity_decode( $custom_js ); ?></script>
</body>
</html>
