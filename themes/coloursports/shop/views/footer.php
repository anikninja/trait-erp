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
									    <div class="slider-brands">
										    <div class="contentslider" data-rtl="no" data-loop="yes" data-autoplay="yes" data-autoheight="no" data-autowidth="no" data-delay="4" data-speed="0.6" data-margin="0" data-items_column0="8" data-items_column1="6" data-items_column2="3" data-items_column3="2" data-items_column4="1" data-arrows="yes" data-pagination="no" data-lazyload="yes" data-hoverpause="yes">
											    <?php
											    foreach ( $brands as $brand ) {
												    if( empty($brand->slug) || empty($brand->image)  ) {
													    continue;
												    }
												    ?>
												    <div class="item">
													    <a href="<?php echo site_url( 'brand/' . $brand->slug ); ?>" class="image">
														    <img data-echo="<?php echo base_url( 'assets/uploads/' . $brand->image ); ?>" src="<?php echo base_url('assets/uploads/' . $brand->image); ?>" alt="<?php echo $brand->name; ?>">
													    </a>
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
								    <div class="module so-deals-ltr deals-layout1 col-xs-12">
									    <?php if ( $section['label'] ) { ?>
										    <h3 class="modtitle">
											    <span><?php echo $section['label']; ?></span>
										    </h3>
									    <?php } ?>
									    <div class="modcontent">
										    <div class="so-deal clearfix preset00-2 preset01-1 preset02-1 preset03-1 preset04-1  button-type2  style2">
											    <div class="most-viewed-carousel products-list " data-effect="none">
												    <?php foreach( $section['products'] as $product ) { ?>
													    <div class="item">
														    <?php get_single_product_loop_third_layout($product); ?>
													    </div>
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
														    <?= form_open( site_url( 'subscribe' ), 'id="subscribe" class="form-group form-inline signup send-mail" method="post"' ); ?>
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
			            foreach ( $theme_options['footer_main']['widgets'] as $widgets ) {
			        	    echo '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">';
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

	<!-- Product Modal Start -->
	<!-- Modal -->
	<div class="modal fade" id="productViewer" tabindex="-1" role="dialog" aria-labelledby="productName">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13 1.96814L1 13.9522" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
						<path d="M1 1.96814L13 13.9522" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
					</svg>
				</button>
				<div class="modal-body">
					<div class="main-content detail-block">
						<?php /*<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 gallery-holder">
								<div class="product-preview">
									<img src="<?php echo $assets; ?>images/products/product-1.jpg" alt="Product Thumbnail">
								</div><!-- /.single-product-gallery -->
							</div><!-- /.gallery-holder -->
							<div class="col-sm-12 col-md-6 col-lg-6 product-info-block">
								<div class="products">
									<div class="product">
										<div class="product-info" data-product-id="p101">
											<h1 class="name" id="productName">Fresh Chinigura Rice</h1>
											<p class="unit">1kg</p>
											<div class="product-price">
												<span class="price"> ৳109.99 </span>
												<span class="price-before-discount">৳120</span>
											</div>
											<div class="row">
												<div class="col-md-8">
													<div class="add-to-cart-count-content">
														<button class="cart-item-decrease-btn-from-product">-</button>
														<span class="cart-item-qty-input-from-product">1</span>
														<button class="cart-item-increase-btn-from-product">+</button>
													</div>
												</div>
												<div class="col-md-4">
													<div class="buy-now">
														<a href="#">Buy Now</a>
													</div>
												</div>
											</div>
											<hr>
											<div class="description-container m-t-20">
												<p>Eggs are an all-natural source of high-quality protein and a number of other nutrients, all for 70 calories an egg. 100% fresh and healthy.</p>
											</div><!-- /.description-container -->
										</div><!-- /.product-info -->
									</div>
									<!-- /.product -->
								</div>
								<!-- /.products -->
							</div><!-- /.col-sm-7 -->
						</div><!-- /.row -->*/ ?>
					</div>
				</div>
			</div>
		</div>
	</div>
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
	<!-- Product Modal End-->
	<div class="active" id="gsLoading">
		<div class="loading-ripple"><div></div><div></div></div>
	</div>
	<!-- /#gsLoading -->
	<!-- =============================== CART SIDEBAR : END =============================== -->
	<script>
		var m = '<?= $m; ?>', v = '<?= $v; ?>',
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
			assetss_url: '<?php echo $assets; ?>',
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
			out_of_stock : '<?= lang('out_of_stock'); ?>',
			x_product : '<?= lang('x_product'); ?>',
			r_u_sure : '<?= lang('r_u_sure'); ?>',
			x_reverted_back: "<?= lang('x_reverted_back'); ?>",
			delete : '<?= lang('delete'); ?>',
			line_1 : '<?= lang('line1'); ?>',
			line_2 : '<?= lang('line2'); ?>',
			city : '<?= lang('city'); ?>',
			state : '<?= lang('state'); ?>',
			postal_code : '<?= lang('postal_code'); ?>',
			country : '<?= lang('country'); ?>',
			phone : '<?= lang('phone'); ?>',
			is_required : '<?= lang('is_required'); ?>',
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
			available_options: '<?= lang( 'available_options' ); ?>',
			option_color: '<?= lang( 'option_color' ); ?>',
			option_size: '<?= lang( 'option_size' ); ?>',
			option_option: '<?= lang( 'option_option' ); ?>',
			select_option: '<?= lang( 'select_option' ); ?>',
			buy_now: '<?= lang( 'buy_now' ); ?>',
			share_message: '<?= lang( 'share_message' ); ?>',
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
			<?php } ?>
			<?php if ( $reminder ) { ?>
			{
				t: '<?=lang('reminder'); ?>',
				m: '<?= trim( str_replace( [ "\r", "\n", "\r\n" ], '', addslashes( $reminder ) ) ); ?>',
				l: 'info',
				o: false,
			},
			<?php } ?>
			<?php if ( $warning ) { ?>
			{
				t: '<?=lang('warning'); ?>',
				m: '<?= trim( str_replace( [ "\r", "\n", "\r\n" ], '', addslashes( $warning ) ) ); ?>',
				l: 'warning',
				o: false,
			},
			<?php } ?>
			<?php if ( $error ) { ?>
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
	</script>
	<?php
	$javaScripts = [
		'libs.min.js',
		'wow.min.js',
		'jquery.easing-1.3.min.js',
		'owl.carousel.min.js',
		'owl2.carousel.js',
		'echo.min.js',
		'bootstrap-slider.min.js',
//		'jquery.rateit.min.js',
//		'lightbox.min.js',
//		'bootstrap-select.min.js',
		'jquery-scrolltofixed-min.js',
		'chosen.jquery.js',
		'jquery.autocomplete.min.js',
		'scripts.js',
	];
	foreach ( $javaScripts as $js ) {
		printf( '<script defer src="%sjs/%s"></script>', $assets, $js );
	}
	?>
	<script><?= html_entity_decode( $custom_js ); ?></script>
</body>
</html>
