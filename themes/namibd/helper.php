<?php
/**
 * primetrading Theme Helper.
 *
 * @package RetailErp/primetrading
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
if ( ! function_exists( 'build_category_tree' ) ) {
	function build_category_tree( $list, $selected = [], $classes = [], $url_path = '', $dept = 0 ) {
		if ( empty( $classes ) ) {
			$classes = [
				'', 'subs',
				'', '',
			];
		}
		$ulClasses = $dept ? ( isset( $classes[1] ) ? $classes[1] : 'subs' ) : ( isset( $classes[0] ) ? $classes[0] : '' );
		$output = '<ul class="'. $ulClasses .'">';
		if ( empty( $url_path ) ) {
			$url_path = site_url( 'category' );
		}
		foreach ( $list as $idx => $item ) {
			$liClasses = $dept ? ( isset( $classes[3] ) ? $classes[3] : '' ) : ( isset( $classes[2] ) ? $classes[2] : '' );
			$liClasses .= ' depth-' . $dept;
			if ( isset( $selected[$dept] ) && $item->slug === $selected[$dept] ) {
				$liClasses .= ' active';
			}
			if ( isset( $item->has_children ) && $item->has_children ) {
				$output .= sprintf(
					'<li class="%s"><a href="%s">%s</a>%s</li>',
					$liClasses,
					$url_path . '/' . $item->slug,
					$item->name,
					build_category_tree( $item->subcategories, $selected, $classes, $url_path . '/' . $item->slug, $dept + 1 )
				);
			} else {
				$output .= sprintf(
					'<li class="%s"><a href="%s">%s</a></li>',
					$liClasses,
					$url_path . '/' . $item->slug,
					$item->name
				);
			}
		}
		$output .= '</ul>';
		return $output;
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
if ( ! function_exists( 'get_single_product_loop_layout' ) ) {
	function get_single_product_loop_layout ( $product, $show_promo_countdown = false ) {
		$prodClasses = get_single_product_classes( $product, $show_promo_countdown ? ' hot-deal-wrapper' : '' );
		if ( ! $product->inCart ) {
			$prodClasses .= ' not-added-in-cart';
		}
		if ( $show_promo_countdown ) {
			$prodClasses .= ' hot-deal-wrapper';
		}
		?>
		<div class="<?php echo $prodClasses; ?>" data-product='<?php echo safeJsonEncode( $product ); ?>'>
			<div class="product-info-wrap">
				<div class="product-image">
					<?= $show_promo_countdown ? get_product_promo_countdown( $product ) : ''; ?>
					<div class="image">
						<a class="view-product" href="<?= get_product_permalink( $product ); ?>">
							<img src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
						</a>
					</div>
					<!-- /.image -->
					<?php if ( $product->cash_back ) { ?>
					<div class="cash-back">
						<span class="cash-back-amount"><?= $product->cash_back_amount; ?></span>
						<span class="cash-back-text"><?= lang( 'cash_back' ); ?></span>
					</div>
					<?php } ?>
				</div>
				<!-- /.product-image -->
				<div class="product-info text-center">
					<h3 class="name">
						<a class="view-product" href="<?= get_product_permalink( $product ); ?>"><?php echo $product->name; ?></a>
					</h3>
					<div class="unit"><?php echo $product->unit_name; ?></div>
					<div class="product-price">
						<?php if ( '' !== $product->sale_price ) { ?>
							<span class="price"><?php echo $product->sale_price; ?></span>
							<span class="price-before-discount"><?php echo $product->regular_price; ?></span>
						<?php } else { ?>
							<span class="price"><?php echo $product->regular_price; ?></span>
						<?php } ?>
					</div>
				</div>
				<!-- /.product-info -->
				<div class="overlay-area">
					<div class="cart-details">
						<div class="cart-qty qty-inc cart-item-increase-btn-from-product"></div>
						<?php echo get_add_to_cart_button( $product, false ); ?>
						<div class="cart-item-and-total-price">
							<div class="cart-qty qty-inc cart-item-increase-btn-from-product"></div>
							<div class="cart-total-price">
								<p><span><?php echo $product->current_price; ?></span></p>
							</div>
							<div class="overlay-cart-count">
								<button class="cart-qty qty-desc cart-item-decrease-btn-from-product">-</button>
								<span class="cart-qty-count cart-item-qty-input-from-product"><?php echo $product->cartQty; ?></span>
								<button class="cart-qty qty-inc cart-item-increase-btn-from-product">+</button>
							</div>
							<p>in Cart</p>
						</div>
					</div>
					<div class="view-product-btn">
						<a class="view-product" href="<?= get_product_permalink( $product ); ?>">Details >></a>
					</div>
				</div>
				<!-- cart btn area -->
			</div>
			<!-- /.product-info-wrap -->
			<div class="add-to-cart-wrap">
				<div class="add-to-cart-count-content">
					<button class="cart-qty qty-desc cart-item-decrease-btn-from-product">-</button>
					<span class="cart-qty-count cart-item-qty-input-from-product"><?php echo $product->cartQty; ?></span>
					<button class="cart-qty qty-inc cart-item-increase-btn-from-product">+</button>
				</div>
				<?php echo get_add_to_cart_button( $product, false ); ?>
			</div>
			<!-- /cart btn area -->
		</div>
		<!-- /.product -->
		<?php
	}
}
// End of file helper.php.
