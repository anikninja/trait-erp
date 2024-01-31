<?php
/**
 * ColourSport Theme Helper.
 *
 * @package RetailErp/ColourSports
 * @version 1.0.0
 */
if ( ! defined( 'BASEPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die();
}

if ( ! function_exists( 'cs_get_my_account_navs' ) ) {
	/**
	 * @param string $currentController
	 * @param string $currentMethod
	 *
	 * @return array
	 */
	function cs_get_my_account_navs( $currentController = 'main', $currentMethod = 'index' ) {
		return [
			'profile' => [
				'link' => site_url('profile'),
				'label' => lang('profile'),
				'icon' => 'user',
				'active' => 'main' === $currentController && 'profile' === $currentMethod,
				'login' => true,
				'class' => '',
			],
			'addresses' => [
				'link' => shop_url('addresses'),
				'label' => lang('addresses'),
				'icon' => 'building',
				'active' => 'shop' === $currentController && 'addresses' === $currentMethod,
				'login' => true,
				'class' => '',
			],
			'wishlist' => [
				'link' => shop_url( 'wishlist' ),
				'label' => lang('wishlist'),
				'icon' => 'heart',
				'active' => 'shop' === $currentController && 'wishlist' === $currentMethod,
				'login' => true,
				'class' => '',
			],
			'orders' => [
				'link' => shop_url('orders'),
				'label' => lang('orders'),
				'icon' => 'truck',
				'active' => 'shop' === $currentController && 'orders' === $currentMethod,
				'login' => true,
				'class' => '',
			],
			'quotes' => [
				'link' => shop_url('quotes'),
				'label' => lang('quotes'),
				'icon' => 'wpforms',
				'active' => 'shop' === $currentController && 'quotes' === $currentMethod,
				'login' => true,
				'class' => '',
			],
			'login' => [
				'link' => site_url('login'),
				'label' => lang('login'),
				'icon' => 'sign-in',
				'active' => 'main' === $currentController && 'login' === $currentMethod,
				'login' => false,
				'class' => '',
			],
			'logout' => [
				'link' => site_url('logout'),
				'label' => lang('logout'),
				'icon' => 'sign-out',
				'active' => false,
				'login' => true,
				'class' => 'order-logout-btn',
			]
		];
	}
}
if ( ! function_exists( 'cs_get_user_display_name' ) ) {
	function cs_get_user_display_name( $user = null ) {
		if ( ! $user ) {
			return '';
		}
		if ( ! empty( $user->last_name ) && false !== preg_match( '/[a-z]/i', $user->first_name ) ) {
			$ln = explode( ' ', $user->last_name );
			$name = ucfirst( $user->first_name[0] ) . '. ' . $ln[0];
		} else if ( ! empty( $user->last_name ) ) {
			$name = $user->last_name;
		} else {
			$name = $user->first_name;
		}
		
		return $name;
	}
}
if ( ! function_exists( 'cs_get_user_avatar' ) ) {
	function cs_get_user_avatar( $user = null, $img = true, $atts = [], $gravater = false, $gs = 150, $gd = 'mp', $gr = 'g' ) {
		$fallback = 'assets/images/male.png';
		if ( ! $user || ! ( is_object( $user ) || is_array( $user ) ) ) {
			$url = base_url( $fallback );
		} else {
			$avatar = '';
			$user = (array) $user;
			if ( isset( $user['avatar'] ) && ! empty( $user['avatar'] ) ) {
				$avatar = 'assets/uploads/avatars/thumbs/' . $user['avatar'];
			} else if ( isset( $user['gender'] ) && ! empty( $user['gender'] ) ) {
				$avatar = 'assets/images/' . $user['gender'] . '.png';
			} else if ( isset( $user[0] ) && is_string( $user[0] ) && false !== preg_match( '/\.(png|jpg|gif|bmp|jpeg)$/i', $user[0] ) ) {
				$avatar = 'assets/uploads/avatars/thumbs/' . $user[0];
			}
			
			if ( ! empty( $avatar ) && is_readable( BASEPATH . '/' . $avatar ) ) {
				$url = base_url( $avatar );
			} else {
				if ( isset( $user['email'] ) && ! empty( $user['email'] ) && $gravater ) {
					$validTypes = [ 'mp', 'identicon', 'monsterid', 'wavatar', 'retro', 'robohash', 'blank' ];
					$gd = strtolower( $gd );
					if ( ! in_array( $gd, $validTypes ) ) {
						$gd = 'mp';
					}
					$url = 'https://www.gravatar.com/avatar/';
					$url .= md5( strtolower( trim( $user['email'] ) ) );
					$url .= "?s=$gs&d=$gd&r=$gr";
				} else {
					$url = base_url( $fallback );
				}
			}
		}
		
		if ( $img ) {
			if ( ! isset( $atts['alt'] ) ) {
				$atts['alt'] = cs_get_user_display_name( (object) $user );
			}
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}
}
if ( ! function_exists( 'get_category_permalink' ) ) {
	function get_category_permalink( $category, $subCategory = null ) {
		$link = '';
		if ( is_object( $category ) && isset( $category->slug ) ) {
			$link .= $category->slug;
		} else if ( is_string( $category ) ) {
			$link .= $category;
		}
		if ( ! empty( $link ) && ! is_null( $subCategory ) ) {
			if ( is_object( $subCategory ) && isset( $subCategory->slug ) ) {
				$link .= '/' . $subCategory->slug;
			} else if ( is_string( $subCategory ) ) {
				$link .= '/' . $subCategory;
			}
		}
		if ( ! empty( $link ) ) {
			return site_url( 'category/' . $link );
		}
		return false;
	}
}
if ( ! function_exists( 'get_category_dropdown' ) ) {
	function get_category_dropdown( $categories, $wrapperAttributes, $showCaret = true, $show_sub_cat = true, $useColumn = true, $maxItemPerList = 10 ) {
		$attributes = ' ';
		foreach ( $wrapperAttributes as $k => $v ) {
			$attributes .= " {$k}=\"{$v}\"";
		}
		?>
		<ul<?= $attributes; ?>>
			<?= $showCaret ? '<li class="has-caret fa fa-caret-up"></li>' : '' ?>
			<?php
			foreach( $categories as $category ) {
				$catClasses = 'category';
				if ( ! empty( $category->subcategories ) ) {
					$catClasses .= ' has-submenu';
				}
				?>
				<li class="<?php echo $catClasses; ?>">
					<a href="<?php echo get_category_permalink( $category ); ?>">
						<i class="cat-con cat-<?= $category->slug; ?>"></i>
						<span class="text"><?php echo $category->name; ?></span>
						<?php if ( ! empty( $category->subcategories ) ) { ?>
							<b class="fa fa-angle-right"></b>
						<?php } ?>
					</a>
					<?php
					if ( $show_sub_cat && ! empty( $category->subcategories ) ) {
						$scTotal = count( $category->subcategories );
						if ( $useColumn && $scTotal >= ceil( $maxItemPerList +( $maxItemPerList/2 ) ) ) {
							$columns = array_chunk( $category->subcategories, $maxItemPerList );
							$cc      = count( $columns );
							echo '<ul class="css-submenu has-columns">';
							echo '<li class="css-submenu-column-wrap col-size-' . ( $cc > 4 ? 4 : $cc ) . '">';
							foreach ( $columns as $column ) {
								echo '<ul class="css-submenu-column">';
								foreach ( $column as $subcategory ) {
									printf(
										'<li><a href="%s">%s</a></li>',
										get_category_permalink( $category, $subcategory ),
										$subcategory->name
									);
								}
								echo '</ul>';
							}
							echo '</li>';
							echo '</ul>';
						} else {
							echo '<ul class="css-submenu">';
							foreach ( $category->subcategories as $subcategory ) {
								printf(
									'<li><a href="%s">%s</a></li>',
									get_category_permalink( $category, $subcategory ),
									$subcategory->name
								);
							}
							echo '</ul>';
						}
					}
					?>
				</li>
			<?php }
			?>
		</ul>
		<?php
	}
}
if ( ! function_exists( 'get_all_lang_lines' ) ) {
	function get_all_lang_lines() {
		return get_instance()->lang->language;
	}
}
if ( ! function_exists( 'get_product_promo_countdown' ) ) {
	function get_product_promo_countdown( $product ) {
		try {
			$now = new DateTime();
			$end = new DateTime( $product->end_date . ' 00:00:00' );
			$end_on = $now->diff($end);
			$template = '<div class="box-wrapper"><div class="date box"><span class="key">%d</span> <span class="value">DAYS</span></div></div><div class="box-wrapper"><div class="date box"><span class="key">%h</span> <span class="value">HRS</span></div></div><div class="box-wrapper"><div class="date box"><span class="key">%m</span> <span class="value">MINS</span></div></div><div class="box-wrapper"><div class="date box"><span class="key">%s</span> <span class="value">SEC</span></div></div>';
			$start = date( 'M d, Y H:i:s' );
			$end = date( 'M d, Y H:i:s', strtotime( $product->end_date ) );
			ob_start();
			?>
			<div class="timing-wrapper" data-data-leading_zero="true" data-countdown="{'stop':'<?php echo $end; ?>', 'now':'<?php echo $start; ?>'}" data-format='<?php echo $template; ?>'>
				<div class="box-wrapper">
					<div class="date box"> <span class="key"><?php echo $end_on->days; ?></span> <span class="value">DAYS</span> </div>
				</div>
				<div class="box-wrapper">
					<div class="hour box"> <span class="key"><?php echo $end_on->h; ?></span> <span class="value">HOURS</span> </div>
				</div>
				<div class="box-wrapper">
					<div class="minutes box"> <span class="key"><?php echo $end_on->i; ?></span> <span class="value">MINS</span> </div>
				</div>
				<div class="box-wrapper">
					<div class="seconds box"> <span class="key"><?php echo $end_on->s; ?></span> <span class="value">SECS</span> </div>
				</div>
			</div>
			<?php
			return ob_get_clean();
		} catch ( Exception $e ) {
			return false;
		}
	}
}
if ( ! function_exists( 'get_product_color_opts' ) ) {
	function get_product_color_opts( $product ) {
		$colorOpts = '';
		
		if ( $product->isVariable && isset( $product->attributes['opts']['Color'] ) ) {
			$i = 0;
			foreach ( $product->attributes['opts']['Color'] as $color ) {
				/** @noinspection HtmlUnknownTarget */
				$colorOpts .= sprintf(
					'<a class="view-product" href="%s" data-color="%s" style="--delay: %s;"><span style="background: %s;" title="%s"></span></a>',
					$product->link,
					$color,
					$i,
					str_replace( [ ' ' ], '-', strtolower( $color ) ),
					$color
				);
				$i++;
			}
			if ( ! empty( $colorOpts ) ) {
				$colorOpts = '<div class="product-color-btn">' . $colorOpts . '</div>';
			}
		}
		return $colorOpts;
	}
}
if ( ! function_exists( 'get_single_product_loop_layout' ) ) {
	function get_single_product_loop_layout ( $product, $show_promo_countdown = false ) {
		$prodClasses = get_single_product_classes( $product, $show_promo_countdown ? ' hot-deal-wrapper' : '' );
		?>
		<div class="<?php echo $prodClasses; ?>" data-product='<?php echo safeJsonEncode( $product ); ?>'>
			<div class="product-info-wrap product-single">
				<div class="product-image">
					<div class="product-all-btn-wrap">
						<div class="product-btn-wrap">
							<?php echo get_add_to_cart_button( $product );
							if ( $product->inWishList ) { ?>
								<a class="remove-wishlist" data-id="<?= $product->id; ?>" href="<?= site_url( 'cart/remove_wishlist/' . $product->id ); ?>"><i class="fa fa-heart"></i><span><?= lang( 'remove_from_wishlist' ); ?></span></a>
							<?php } else { ?>
								<a class="add-to-wishlist" data-id="<?= $product->id; ?>" href="<?= site_url( 'cart/add_wishlist/' . $product->id ); ?>"><i class="fa fa-heart-o"></i><span><?= lang( 'add_to_wishlist' ); ?></span></a>
							<?php } ?>
							<a class="view-product" href="<?= get_product_permalink( $product ); ?>"><i class="fa fa-eye"></i><span>Quick View</span></a>
						</div>
						<?= get_product_color_opts( $product ); ?>
					</div>
					<?= $show_promo_countdown ? get_product_promo_countdown( $product ) : ''; ?>
					<div class="image">
						<a class="view-product" href="<?= get_product_permalink( $product ); ?>">
							<img src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
						</a>
					</div>
					<!-- /.image -->
				</div>
				<!-- /.product-image -->
				<div class="product-info text-center">
					<h3 class="name">
						<a class="view-product" href="<?= get_product_permalink( $product ); ?>" title="<?php echo $product->name; ?>"><?php echo $product->name; ?></a>
					</h3>
					<div class="product-price">
						<?php if ( '' !== $product->sale_price ) { ?>
							<span class="price"><?php echo $product->sale_price; ?></span>
							<span class="price-before-discount"><?php echo $product->regular_price; ?></span>
						<?php } else { ?>
							<span class="price"><?php echo $product->regular_price; ?></span>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<!-- /.product -->
		<?php
	}
}
if ( ! function_exists( 'get_single_product_loop_second_layout' ) ) {
	function get_single_product_loop_second_layout ( $product, $show_promo_countdown = false ) {
		$prodClasses = get_single_product_classes( $product, $show_promo_countdown ? ' hot-deal-wrapper' : '' );
		?>
		<div class="<?php echo $prodClasses; ?> second-layout" data-product='<?php echo safeJsonEncode( $product ); ?>'>
			<div class="product-info-wrap product-single">
				<div class="row">
					<div class="item-left col-lg-6 col-md-5 col-sm-5 col-xs-12">
						<div class="product-image">
							<div class="product-all-btn-wrap">
								<div class="product-btn-wrap">
									<?php echo get_add_to_cart_button( $product );
									if ( $product->inWishList ) { ?>
										<a class="remove-wishlist" data-id="<?= $product->id; ?>" href="<?= site_url( 'cart/remove_wishlist/' . $product->id ); ?>"><i class="fa fa-heart"></i><span><?= lang( 'remove_from_wishlist' ); ?></span></a>
									<?php } else { ?>
										<a class="add-to-wishlist" data-id="<?= $product->id; ?>" href="<?= site_url( 'cart/add_wishlist/' . $product->id ); ?>"><i class="fa fa-heart-o"></i><span><?= lang( 'add_to_wishlist' ); ?></span></a>
									<?php } ?>
									<a class="view-product" href="<?= get_product_permalink( $product ); ?>"><i class="fa fa-eye"></i><span>Quick View</span></a>
								</div>
								<?= get_product_color_opts( $product ); ?>
							</div>
							<div class="image">
								<a class="view-product" href="<?= get_product_permalink( $product ); ?>">
									<img src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
								</a>
							</div>
							<!-- /.image -->
						</div>
						<!-- /.product-image -->
					</div>
					<div class="item-right col-lg-6 col-md-7 col-sm-7 col-xs-12">
						<div class="product-info">
							<h3 class="name">
								<a class="view-product" href="<?= get_product_permalink( $product ); ?>" title="<?php echo $product->name; ?>"><?php echo $product->name; ?></a>
							</h3>
							<div class="product-price">
								<?php if ( '' !== $product->sale_price ) { ?>
									<span class="price"><?php echo $product->sale_price; ?></span>
									<span class="price-before-discount"><?php echo $product->regular_price; ?></span>
								<?php } else { ?>
									<span class="price"><?php echo $product->regular_price; ?></span>
								<?php } ?>
							</div>
                            <p class="desc"><?php echo character_limiter( $product->custom_data['short_description'], 100 ); ?></p>
							<?= $show_promo_countdown ? get_product_promo_countdown( $product ) : ''; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /.product -->
		<?php
	}
}
if ( ! function_exists( 'get_single_product_loop_third_layout' ) ) {
	function get_single_product_loop_third_layout ( $product ) {
		$prodClasses = get_single_product_classes( $product );
		?>
		<div class="<?php echo $prodClasses; ?> third-layout" data-product='<?php echo safeJsonEncode( $product ); ?>'>
			<div class="product-info-wrap product-single">
				<div class="product-image">
					<div class="image">
						<a class="view-product" href="<?= get_product_permalink( $product ); ?>">
							<img src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
						</a>
					</div>
					<!-- /.image -->
				</div>
				<!-- /.product-image -->
				<div class="product-info">
					<h3 class="name">
						<a class="view-product" href="<?= get_product_permalink( $product ); ?>" title="<?php echo $product->name; ?>"><?php echo $product->name; ?></a>
					</h3>
					<div class="product-price">
						<?php if ( '' !== $product->sale_price ) { ?>
							<span class="price"><?php echo $product->sale_price; ?></span>
							<span class="price-before-discount"><?php echo $product->regular_price; ?></span>
						<?php } else { ?>
							<span class="price"><?php echo $product->regular_price; ?></span>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<!-- /.product -->
		<?php
	}
}
// End of file helper.php.
