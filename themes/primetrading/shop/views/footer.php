	<!-- ============================================================= FOOTER ============================================================= -->
    <footer id="footer" class="footer-container footer typefooter-1">
	    <div class="footer-top">
	    <?php
		    if ( isset( $theme_options['footer_top'] ) && ! empty( $theme_options['footer_top'] ) ) {
			    foreach ( $theme_options['footer_top'] as $section ) {
				    if ( ! isset( $section['type'] ) ) {
					    continue;
				    }
				    if ( 'custom' === $section['type'] ) {
					    echo html_entity_decode( $section['content'] );
				    }
				    else if ( 'brand_slider' === $section['type'] ) {
					    ?>
					    <div id="brands-carousel" class="logo-slider">
						    <div class="container">
							    <div class="row">
								    <div class="col-md-12">
									    <div class="product-brands">
										    <div class="brand-slider">
										    <?php foreach ( $brands as $brand ) {
											    $brand->image = get_image_url( $brand->image, false, true );
											    if ( empty( $brand->slug ) || ! $brand->image ) {
												    continue;
											    }
										    ?>
											    <div class="item">
												    <a href="<?php echo site_url( 'brand/' . $brand->slug ); ?>" class="image"><?php echo cs_lazy_image( $brand->image, [ 'alt'   => $brand->name, 'class' => 'owl2-lazy', 'lazy'  => 'src', ] ); ?></a>
											    </div>
										    <?php } ?>
										    </div>
										    <!-- /.owl-carousel #logo-slider -->
									    </div>
								    </div>
							    </div>
						    </div>
					    </div>
					    <!-- /.logo-slider -->
					    <?php
				    }
				    else if ( in_array( $section['type'], ['products', 'most_viewed', 'trending_products', 'daily_deals', 'featured_products'] ) ) {
					    if ( empty( $section['products'] ) ) {
						    continue;
					    }
					    ?>
					    <div class="footer-product-slider products-<?= $section['type'] ?>">
						    <div class="container">
							    <div class="row">
								    <div class="module deals-layout1 col-xs-12">
									    <?php if ( $section['label'] ) { ?>
										    <h3 class="modtitle">
											    <span><?php echo $section['label']; ?></span>
										    </h3>
									    <?php } ?>
									    <div class="modcontent">
										    <div class="so-deal clearfix preset00-2 preset01-1 preset02-1 preset03-1 preset04-1  button-type2  style2">
											    <div class="most-viewed-carousel products-list">
											    <?php foreach( $section['products'] as $product ) { ?>
												    <div class="item"><?php get_single_product_loop_third_layout( $product, true ); ?></div>
											    <?php } ?>
											    </div>
										    </div>
									    </div>
								    </div>
							    </div>
						    </div>
					    </div>
					    <!-- /.footer-product-slider -->
					    <?php
				    }
				    else if ( 'mailchimp' === $section['type'] ) {
				    	?>
					    <div class="mailchimp">
						    <div class="container">
							    <div class="row row_3vy7">
								    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ftop">
									    <div class="row row_1olj  ">
										    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
											    <div class="module newsletter-footer1">
												    <div class="newsletter" style="width:100%;background-color:#fff;">
													    <div class="title-block">
														    <div class="page-heading font-title"><?= lang( 'sign_newsletter' ); ?></div>
														    <div class="promotext"><?= lang( 'newsletter_privacy' ); ?></div>
													    </div>
													    <div class="block_content">
														    <?= form_open( site_url( 'subscribe' ), 'id="subscribe" class="form-group signup subscribe-form" method="post"' ); ?>
														    <div class="form-group">
															    <div class="input-box">
																    <input autocomplete="off" type="email" placeholder="<?= lang( 'your_email__' ); ?>" value="" class="form-control" id="subs_email" name="subs_email" required>
															    </div>
															    <div class="subscribe">
																    <button class="btn btn-primary btn-default font-title" type="submit" name="submit"><?= lang( 'subscribe' ); ?></button>
															    </div>
														    </div>
														    <?= form_close(); ?>
													    </div> <!--/.modcontent-->
												    </div>
											    </div>
										    </div>
										    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
											    <ul class="socials">
												    <?php if ( $shop_settings->facebook ) { ?>
													    <li class="facebook">
														    <a class="_blank" href="<?= $shop_settings->facebook ?>" rel="noopener noreferrer"  target="_blank"><i class="fa fa-facebook"></i><span>Facebook</span></a>
													    </li>
												    <?php } ?>
												    <?php if ( $shop_settings->twitter ) { ?>
													    <li class="twitter">
														    <a class="_blank" href="<?= $shop_settings->twitter ?>" rel="noopener noreferrer" target="_blank"><i class="fa fa-twitter"></i><span>Twitter</span></a>
													    </li>
												    <?php } ?>
												    <?php if ( $shop_settings->instagram ) { ?>
													    <li class="instagram">
														    <a class="_blank" href="<?= $shop_settings->instagram ?>" rel="noopener noreferrer" target="_blank"><i class="fa fa-instagram"></i><span>Instagram</span></a>
													    </li>
												    <?php } ?>
											    </ul>
										    </div>
									    </div>
								    </div>
							    </div>
						    </div>
					    </div>
					    <!-- /.mailchimp -->
					    <?php
				    }
			    }
		    }
	    ?>
	    </div>
        <div class="footer-main">
	        <div class="container">
		        <div class="row">
		        <?php
			        if ( isset($theme_options['footer_main']['widgets']) && ! empty($theme_options['footer_main']['widgets']) ) {
			            $i = 0;
			            foreach ( $theme_options['footer_main']['widgets'] as $widgets ) {
			                $class = $i === 0 ? 'col-lg-3 col-md-3 col-sm-12 col-xs-12' : 'col-lg-3 col-md-3 col-sm-4 col-xs-12';
			                $i++;
			        	    echo '<div class="'. $class .'">';
				            foreach ( $widgets as $widget ) {
					            if ( $widget['type'] == 'custom' ) {
						            echo html_entity_decode( $widget['content'] );
					            } elseif ( $widget['type'] == 'url_list' && ! empty( $widget['list'] ) ) {
						            echo '<div class="box-information box-footer">';
						                echo '<div class="module clearfix">';
						            if ( $widget['label'] ) {
							            printf( '<h4 class="modtitle">%s</h4>', $widget['label'] );
						            }
						                    echo '<div class="modcontent">';
						                        echo '<ul class="menu">';
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
						                echo '</div>';
						            echo '</div>';
					            }
				            }
				            echo '</div>';
			            }
			        }
		        ?>
		        </div>
	        </div>
        </div>
	    <?php if ( isset( $theme_options['footer_bottom'] ) && ! empty( $theme_options['footer_bottom'] ) ) {
		    $footer_bottom = $theme_options['footer_bottom'];
        ?>
	    <div class="footer-bottom">
	    <?php if ( ! empty( $footer_bottom['bottom_links']['list'] ) ) { ?>
	        <div class="container ">
				    <div class="row">
					    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						    <ul class="footer-links font-title">
							    <?php
							    foreach ( $footer_bottom['bottom_links']['list'] as $link ) {
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
							    ?>
						    </ul>
					    </div>
				    </div>
			    </div>
	    <?php } ?>
	    <?php if ( isset( $footer_bottom['bottom_custom']['content'] ) && $footer_bottom['bottom_custom']['content'] ) { ?>
		    <div class="container"><?= html_entity_decode( $footer_bottom['bottom_custom']['content'] ); ?></div>
        <?php } ?>
	    <?php
	    if ( isset( $footer_bottom['copyright']['content'] ) && $footer_bottom['copyright']['content'] ) {
		    $copyright = html_entity_decode( $footer_bottom['copyright']['content'] );
		    $copyright = trim( $copyright );
		    $copyright = str_replace( '{year}', date( 'Y' ), $copyright );
		    if ( $copyright ) {
			    $copyright .= ' ';
		    }
        ?>
		    <div class="copyright-w">
			    <div class="container">
				    <div class="copyright"><?= $copyright; ?>Developed and Maintained by <a href="https://pixelaar.com" rel="noopener noreferrer" target="_blank">Pixelaar FZC LLC</a></div>
			    </div>
		    </div>
        <?php } ?>
	    </div>
	    <?php } ?>
	    <a href="#" class="back-to-top text-center">
		    <i class="fa fa-angle-up"></i>
	    </a>
    </footer>
	<!-- ============================================================= FOOTER : END============================================================= -->
    <?php include __DIR__ . '/pages/cart.php'; ?>
    <?php include __DIR__ . '/pages/product-details-modal.php'; ?>
	<?php include __DIR__ . '/user/login-modal.php'; ?>
	<?php include __DIR__ . '/pages/cookies-consent.php'; ?>
	<?php //include __DIR__ . '/pages/subscription-modal.php'; ?>
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
//		'jquery.easing-1.3.min.js',
//		'owl.carousel.min.js',
		'echo.min.js',
		'owl2.carousel.js',
		'slider.js',
		'bootstrap-slider.min.js',
//		'jquery.rateit.min.js',
//		'lightbox.min.js',
//		'bootstrap-select.min.js',
		'jquery-scrolltofixed-min.js',
		'jquery.smartmenus.min.js',
//        'jquery.subscribe-better.min.js',
		'chosen.jquery.js',
		'jquery.autocomplete.min.js',
		'jquery.elevateZoom-3.0.8.min.js',
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
	<?php if ( ! empty( $custom_js ) ) { ?>
	<script><?= html_entity_decode( $custom_js ); ?></script>
	<?php } ?>
</body>
</html>
