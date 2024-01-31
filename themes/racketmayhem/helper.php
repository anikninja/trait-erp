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

if ( ! function_exists( 'cs_lazy_image' ) ) {
	function cs_lazy_image( $image, $attrs = null, $placeholder = null ) {
		$attrs = ci_parse_args( $attrs, [ 'class' => '', 'lazy' => 'echo', 'alt' => '' ] );
		if ( ! $placeholder ) {
			$placeholder = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
		}
		$_attrs = [
			'src'                    => $placeholder,
			'class'                  => $attrs['class'],
			'data-' . $attrs['lazy'] => $image,
			'alt'                    => $attrs['alt'],
		];
		$img = '<img ';
		foreach ( $_attrs as $prop => $val ) {
			if ( $val ) {
				if ( is_array( $val ) ) {
					$val = implode( ' ', $val );
				}
				$img .= "{$prop}=\"{$val}\"";
			}
		}
		$img .= '>';

		return $img;
	}
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
if ( ! function_exists( 'build_category_option_tree' ) ) {
	function build_category_option_tree( $list, $selected = '', $dept = 0 ) {
		$output = '';
		if ( empty( $url_path ) ) {
			$url_path = site_url( 'category' );
		}
		foreach ( $list as $idx => $item ) {
			$indent = str_pad( '', $dept * 4, ' ' );
			$indent = str_replace( [ ' ' ], '&nbsp;', $indent );
			$output .= sprintf(
				'<option value="%s"%s>%s%s</option>',
				$item->id,
				selected( $selected, $item->id, false ),
				$indent,
				$item->name
			);
			if ( isset( $item->has_children ) && $item->has_children ) {
				$output .= build_category_option_tree( $item->subcategories, $selected, $dept + 1 );
			}
		}

		return $output;
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
		if ( $product->promo_ends && $product->promo_ends_on ) {
			ob_start();
			?>
            <h3 class="promo-inds-in"><?= lang('promo_ends_in'); ?></h3>
            <div class="timing-wrapper gs_countdown" data-countdown="{'stop':'<?php echo $product->promo_ends; ?>', 'now':'<?php echo $product->promo_starts; ?>'}">
                <div class="box-wrapper">
                    <div class="date box"> <span class="key"><?php echo $product->promo_ends_on->days; ?></span> <span class="value"><?= lang( '_days_' ); ?></span> </div>
                </div>
                <div class="box-wrapper">
                    <div class="hour box"> <span class="key"><?php echo $product->promo_ends_on->h; ?></span> <span class="value"><?= lang( '_hours_' ); ?></span> </div>
                </div>
                <div class="box-wrapper">
                    <div class="minutes box"> <span class="key"><?php echo $product->promo_ends_on->i; ?></span> <span class="value"><?= lang( '_mins_' ); ?></span> </div>
                </div>
                <div class="box-wrapper">
                    <div class="seconds box"> <span class="key"><?php echo $product->promo_ends_on->s; ?></span> <span class="value"><?= lang( '_secs_' ); ?></span> </div>
                </div>
            </div>
			<?php
			return ob_get_clean();
		} else {
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
	function get_single_product_loop_layout ( $product, $show_promo_countdown = false, $owl = false ) {
		$prodClasses = get_single_product_classes( $product, $show_promo_countdown ? ' hot-deal-wrapper' : '' );
		$imgArgs = [ 'alt' => $product->name ];
		if ( $owl ) {
			$imgArgs['lazy'] = 'src';
			$imgArgs['class'] = 'owl2-lazy';
		}
		?>
        <div class="<?php echo $prodClasses; ?>" data-product='<?php echo safeJsonEncode( $product ); ?>'>
            <div class="product-info-wrap product-single">
                <div class="product-image">
                    <div class="image">
                        <a class="view-product" href="<?= get_product_permalink( $product ); ?>">
							<?= cs_lazy_image( $product->thumb, $imgArgs ); ?>
                        </a>
                    </div>
                    <!-- /.image -->
                </div>
                <!-- /.product-image -->
                <div class="product-info text-center">
                    <h3 class="name">
                        <a class="view-product" href="<?= get_product_permalink( $product ); ?>" title="<?php echo $product->name; ?>">
                            <?php echo $product->name;
                            if ( '' !== $product->sale_price ) { ?>
                                <span class="price"><?php echo $product->isVariable ? $product->max_min_sale['min'] :  $product->sale_price; ?></span>
	                        <?php } else { ?>
                                <span class="price"><?php echo $product->isVariable ? $product->max_min_regular['min'] :  $product->regular_price; ?></span>
	                        <?php } ?>
                        </a>
                    </h3>
                </div>
            </div>
        </div>
        <!-- /.product -->
		<?php
	}
}
if ( ! function_exists( 'get_single_product_loop_second_layout' ) ) {
	function get_single_product_loop_second_layout ( $product, $show_promo_countdown = false, $owl = false ) {
		$prodClasses = get_single_product_classes( $product, $show_promo_countdown ? ' hot-deal-wrapper' : '' );
		$imgArgs = [ 'alt' => $product->name ];
		if ( $owl ) {
			$imgArgs['lazy'] = 'src';
			$imgArgs['class'] = 'owl2-lazy';
		}
		?>
        <div class="<?php echo $prodClasses; ?> second-layout" data-product='<?php echo safeJsonEncode( $product ); ?>'>
            <div class="product-info-wrap product-single">
                <div class="row align-items-center">
                    <div class="item-left col-lg-6 col-md-5 col-sm-5 col-xs-12">
                        <div class="product-image">
                            <div class="image">
                                <a class="view-product" href="<?= get_product_permalink( $product ); ?>">
									<?= cs_lazy_image( $product->thumb, $imgArgs ); ?>
                                </a>
								<?php
								if ( $product->stock_status === 'out_of_stock' ) {
									echo '<div class="sold-out">'. lang('sold_out') .'</div>';
								}
								if ( $product->onSale ) {
									echo '<div class="on-sale">' . sprintf( lang( 'saved_x' ), $product->saved ) . '</div>';
								}
								?>
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
                                    <span class="price"><?php echo $product->isVariable ? $product->max_min_sale['min'] :  $product->sale_price; ?></span>
                                    <span class="price-before-discount"><?php echo $product->isVariable ? $product->max_min_regular['min'] :  $product->regular_price; ?></span>
								<?php } else { ?>
                                    <span class="price"><?php echo $product->isVariable ? $product->max_min_regular['min'] :  $product->regular_price; ?></span>
								<?php } ?>
                            </div>
							<?= $show_promo_countdown ? get_product_promo_countdown( $product ) : ''; ?>
                            <div class="button-group">
								<?php
								if ( $product->inWishList ) { ?>
                                    <a class="remove-wishlist" data-id="<?= $product->id; ?>" href="<?= site_url( 'cart/remove_wishlist/' . $product->id ); ?>"><i class="fa fa-heart"></i></a>
								<?php } else { ?>
                                    <a class="add-to-wishlist" data-id="<?= $product->id; ?>" href="<?= site_url( 'cart/add_wishlist/' . $product->id ); ?>"><i class="fa fa-heart-o"></i></a>
								<?php } ?>
                                <a class="buynow-btn <?= ( $product->stock_status === 'out_of_stock' || $product->isVariable ) ? 'view-product' : 'add-to-cart add-to-cart-btn'; ?>" href="<?= ( $product->stock_status === 'out_of_stock' || $product->isVariable) ? get_product_permalink( $product ) : get_add_to_cart_link( $product ); ?>" data-id="<?= $product->id; ?>"><span>Buy Now</span></a>
                            </div>
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
	function get_single_product_loop_third_layout ( $product, $owl = false ) {
		$prodClasses = get_single_product_classes( $product );
		$imgArgs = [ 'alt' => $product->name ];
		if ( $owl ) {
			$imgArgs['lazy'] = 'src';
			$imgArgs['class'] = 'owl2-lazy';
		}
		?>
        <div class="<?php echo $prodClasses; ?> third-layout" data-product='<?php echo safeJsonEncode( $product ); ?>'>
            <div class="product-info-wrap product-single">
                <div class="product-image">
                    <div class="image">
                        <a class="view-product" href="<?= get_product_permalink( $product ); ?>">
							<?= cs_lazy_image( $product->thumb, $imgArgs ); ?>
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
                            <span class="price"><?php echo $product->isVariable ? $product->max_min_sale['min'] :  $product->sale_price; ?></span>
                            <span class="price-before-discount"><?php echo $product->isVariable ? $product->max_min_regular['min'] :  $product->regular_price; ?></span>
						<?php } else { ?>
                            <span class="price"><?php echo $product->isVariable ? $product->max_min_regular['min'] :  $product->regular_price; ?></span>
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
