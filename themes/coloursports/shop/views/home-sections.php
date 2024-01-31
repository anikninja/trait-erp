<?php
defined('BASEPATH') or exit('No direct script access allowed');

if ( isset( $theme_options['main'] ) && ! empty( $theme_options['main'] ) ) {
	foreach ( $theme_options['main'] as $section ) {
		if ( ! isset( $section['type'] ) ) {
			continue;
		}
		if ( $section['type'] == 'custom' ) {
			?>
			<!-- ============================================== WIDE PRODUCTS ============================================== -->
			<div class="wide-banners outer-bottom-xs">
				<div class="row"><?= html_entity_decode( $section['content'] ); ?></div>
			</div>
			<!-- /.wide-banners -->
			<?php
		}
		elseif ( $section['type'] == 'daily_deals' ) {
			if ( empty( $section['products'] ) ) {
				continue;
			}
			$sectionLabel = $section['label'] ? $section['label'] : '';
			?>
			<div class="module so-deals-ltr deals-layout1 daily_deals <?= $section['type']; ?>">
				<h3 class="modtitle"><span><?php echo $sectionLabel; ?></span></h3>
				<div class="modcontent">
					<div class="so-deal clearfix preset00-2 preset01-1 preset02-1 preset03-1 preset04-1  button-type2  style2">
						<div class="extraslider-inner products-list " data-effect="none">
							<?php foreach( $section['products'] as $product ) { ?>
								<div class="item">
									<?php get_single_product_loop_second_layout($product, true); ?>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		elseif ( $section['type'] == 'trending_products' ) {
			if ( empty( $section['products'] ) ) {
				continue;
			}
			?>
			<!-- ============================================== SCROLL TABS ============================================== -->
			<div class="scroll-tabs outer-top-vs">
				<div class="more-info-tab clearfix ">
					<?php if ( $section['label'] ) { ?>
					<h3 class="new-product-title pull-left"><?php echo $section['label']; ?></h3>
					<?php } ?>
					<ul class="nav nav-tabs nav-tab-line pull-right">
						<li class="active"><a data-transition-type="backSlide" href="#all-<?php $rand_all = rand(); echo $rand_all; ?>" data-toggle="tab">All</a></li>
						<?php
						$rand_array = array();
						foreach ( $categoriesToShow as $key => $category ) {
							$rand_array[$key] = rand();
							if ( empty( $category->products ) ) {
								continue;
							}
							?>
							<li>
								<a data-transition-type="backSlide" href="#category-<?php echo $category->slug . '-' . $rand_array[$key]; ?>" data-toggle="tab"><?php echo $category->name; ?></a>
							</li>
						<?php } ?>
					</ul>
					<!-- /.nav-tabs -->
				</div>
				<div class="tab-content outer-top-xs">
					<div class="tab-pane in active" id="all-<?php echo $rand_all; ?>">
						<div class="products">
							<div class="product-slider">
								<div class="owl-carousel home-owl-carousel custom-carousel owl-theme" data-item="5">
									<?php
									$new_products_chunk = array_chunk( $section['products'], 2 );
									foreach ( $new_products_chunk as $products ) { ?>
										<div class="item item-carousel">
											<?php
											foreach ( $products as $product ) {
												get_single_product_loop_layout( $product );
											}
											?>
										</div>
										<!-- /.item -->
									<?php } ?>
								</div>
								<!-- /.home-owl-carousel -->
							</div>
							<!-- /.product-slider -->
						</div>
						<!-- /.products -->
					</div>
					<!-- /.tab-pane -->
					<?php foreach ( $categoriesToShow as $key => $category ) {
						if ( empty( $category->products ) ) {
							continue;
						}
						?>
						<div class="tab-pane" id="category-<?php echo $category->slug . '-' . $rand_array[$key]; ?>">
							<div class="products">
								<div class="product-slider">
									<div class="owl-carousel home-owl-carousel custom-carousel owl-theme" data-item="5">
										<?php
										foreach ( $category->products as $product ) {
											echo '<div class="item item-carousel">';
											get_single_product_loop_layout( $product );
											echo '</div>';
										}
										?>
									</div>
									<!-- /.home-owl-carousel -->
								</div>
								<!-- /.product-slider -->
							</div>
							<!-- /.products -->
						</div>
						<!-- /.tab-pane -->
					<?php } ?>
				</div>
				<!-- /.tab-content -->
			</div>
			<!-- /.scroll-tabs -->
			<!-- ============================================== SCROLL TABS : END ============================================== -->
			<?php
		}
		elseif ( $section['type'] == 'new_products' ) {
			if ( empty( $section['products'] ) ) {
				continue;
			}
			$sectionLabel = $section['label'] ? $section['label'] : '';
			?>
			<!-- ============================================== SCROLL TABS ============================================== -->
			<div class="scroll-tabs outer-top-vs new_product_no_carousel">
				<div class="more-info-tab clearfix ">
					<h3 class="new-product-title pull-left"><?php echo $sectionLabel; ?></h3>
					<ul class="nav nav-tabs nav-tab-line pull-right">
						<li class="active"><a data-transition-type="backSlide" href="#all" data-toggle="tab">All</a></li>
						<?php foreach ( $categoriesToShow as $category ) {
							if ( empty( $category->products ) ) {
								continue;
							}
							?>
							<li>
								<a data-transition-type="backSlide" href="#category-<?php echo $category->slug; ?>" data-toggle="tab"><?php echo $category->name; ?></a>
							</li>
						<?php } ?>
					</ul>
					<!-- /.nav-tabs -->
				</div>
				<div class="tab-content outer-top-xs">
					<div class="tab-pane in active" id="all">
						<div class="products">
							<div class="product-slider">
								<div class="no-carousel" data-item="5">
									<?php $first_product = array_slice( $section['products'], 0, 1); ?>
									<div class="item item-carousel first_product">
										<?php get_single_product_loop_layout( $first_product[0] ); ?>
									</div>
									<?php
									$new_products_slice = array_slice( $section['products'], 1, 6 );
									$new_products_chunk = array_chunk( $new_products_slice, 2 );
									foreach ( $new_products_chunk as $products ) { ?>
										<div class="item item-carousel">
											<?php
											foreach ( $products as $product ) {
												get_single_product_loop_layout( $product );
											}
											?>
										</div>
										<!-- /.item -->
									<?php } ?>
								</div>
								<!-- /.home-owl-carousel -->
							</div>
							<!-- /.product-slider -->
						</div>
						<!-- /.products -->
					</div>
					<!-- /.tab-pane -->
					<?php foreach ( $categoriesToShow as $category ) {
						if ( empty( $category->products ) ) {
							continue;
						}
						?>
						<div class="tab-pane" id="category-<?php echo $category->slug; ?>">
							<div class="products">
								<div class="product-slider">
									<div class="no-carousel">
										<?php $first_product = array_slice( $category->products, 0, 1); ?>
										<div class="item item-carousel first_product">
											<?php get_single_product_loop_layout( $first_product[0] ); ?>
										</div>
										<?php
										$new_products_slice = array_slice( $category->products, 1, 6 );
										$new_products_chunk = array_chunk( $new_products_slice, 2 );
										foreach ( $new_products_chunk as $products ) { ?>
											<div class="item item-carousel">
												<?php
												foreach ( $products as $product ) {
													get_single_product_loop_layout( $product );
												}
												?>
											</div>
											<!-- /.item -->
										<?php } ?>
									</div>
									<!-- /.home-owl-carousel -->
								</div>
								<!-- /.product-slider -->
							</div>
							<!-- /.products -->
						</div>
						<!-- /.tab-pane -->
					<?php } ?>
				</div>
				<!-- /.tab-content -->
			</div>
			<!-- /.scroll-tabs -->
			<!-- ============================================== SCROLL TABS : END ============================================== -->
			<?php
		}
		elseif ( $section['type'] == 'categories' ) {
			if ( empty( $section['products'] ) ) {
				continue;
			}
		?>
			<!-- ============================================== CATEGORY PRODUCTS ============================================== -->
			<section class="section featured-product">
				<div class="row">
					<div class="col-lg-3">
						<h3 class="section-title"><?php echo $section['label']; ?></h3>
						<ul class="sub-cat">
							<?php
							$i = 0;
							if ( isset( $section['sub_cat'] ) ) {
								foreach ( (array) $section['sub_cat'] as $sub_categories ) {
									$i ++;
									if ( $i > 9 ) {
										break;
									}
									?>
									<li>
										<a href="<?php echo site_url( 'category/'. $section['category']->slug . '/' . $sub_categories->slug ); ?>"><?php echo $sub_categories->name; ?></a>
									</li>
									<?php
								}
							}
							?>
						</ul>
					</div>
					<div class="col-lg-9">
						<div class="owl-carousel homepage-owl-carousel custom-carousel owl-theme outer-top-xs products product-slider">
							<?php
							foreach ( $section['products'] as $product ) { ?>
								<div class="item item-carousel">
									<?php get_single_product_loop_layout( $product ); ?>
								</div>
								<!-- /.item -->
								<?php
							}
							?>
						</div>
					</div>
				</div>
				<!-- /.home-owl-carousel -->
			</section>
			<!-- ============================================== CATEGORY PRODUCTS : END ============================================== -->
			<?php
		}
		elseif ( $section['type'] == 'featured_products' ) {
			if ( empty( $section['products'] ) ) {
				continue;
			}
		?>
			<!-- ============================================== FEATURED PRODUCTS ============================================== -->
			<section class="section new-arriavls">
				<h3 class="section-title"><?php echo $section['label']; ?></h3>
				<div class="owl-carousel home-owl-carousel custom-carousel owl-theme outer-top-xs product-slider" data-item="5">
					<?php
					foreach ( $section['products'] as $product ) { ?>
						<div class="item item-carousel">
							<?php get_single_product_loop_layout( $product ); ?>
						</div>
						<!-- /.item -->
						<?php
					}
					?>
					<!-- /.item -->
				</div>
				<!-- /.home-owl-carousel -->
			</section>
			<!-- /.section -->
			<!-- ============================================== FEATURED PRODUCTS : END ============================================== -->
			<?php
		}
		elseif ( $section['type'] == 'products' ) {
			if ( empty( $section['products'] ) ) {
				continue;
			}
		?>
			<!-- ============================================== FEATURED PRODUCTS ============================================== -->
			<section class="section new-arriavls">
				<h3 class="section-title"><?php echo $section['label']; ?></h3>
				<div class="owl-carousel home-owl-carousel custom-carousel owl-theme outer-top-xs product-slider" data-item="5">
					<?php
					foreach ( $section['products'] as $product ) { ?>
						<div class="item item-carousel">
							<?php get_single_product_loop_layout( $product ); ?>
						</div>
						<!-- /.item -->
						<?php
					}
					?>
					<!-- /.item -->
				</div>
				<!-- /.home-owl-carousel -->
			</section>
			<!-- /.section -->
			<!-- ============================================== FEATURED PRODUCTS : END ============================================== -->
			<?php
		}
	}
}
// End of file.php.
