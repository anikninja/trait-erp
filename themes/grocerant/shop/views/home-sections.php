<?php
defined('BASEPATH') or exit('No direct script access allowed');
if ( isset( $theme_options['main'] ) && ! empty( $theme_options['main'] ) ) {
	foreach ( $theme_options['main'] as $section ) {
		if ( $section['type'] == 'custom' ) { ?>
		<?php echo html_entity_decode( $section['content'] ); ?>
		<?php } elseif ( $section['type'] == 'categories' ) {
			if ( empty( $section['products'] ) ) {
				continue;
			}
		?>
		<!-- ============================================== CATEGORY PRODUCTS ============================================== -->
		<section class="section featured-product category-section">
			<div class="row">
				<div class="col-md-4 col-lg-3">
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
				<div class="col-md-8 col-lg-9">
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
		<?php } elseif ( in_array( $section['type'], ['products', 'most_viewed', 'trending_products', 'daily_deals', 'featured_products'] ) ) {
			if ( empty( $section['products'] ) ) {
				continue;
			}
		?>
		<!-- ============================================== FEATURED PRODUCTS ============================================== -->
		<section class="section new-arriavls">
			<?php if ( $section['label'] ) { ?>
			<h3 class="section-title"><?php echo $section['label']; ?></h3>
			<?php } ?>
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
