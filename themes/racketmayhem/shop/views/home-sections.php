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
			<div class="wide-banners">
				<div class="row"><?= html_entity_decode( $section['content'] ); ?></div>
			</div>
			<!-- /.wide-banners -->
			<?php
		}
		elseif ( $section['type'] == 'trending_products' ) {
			if ( empty( $section['products'] ) ) {
				continue;
			}
			?>
            <section class="section trending_products_section">
				<?php if ( isset($section['label']) ) { ?>
                    <h3 class="section-title"><?php echo $section['label']; ?></h3>
				<?php } if ( isset($section['subtitle']) ) { ?>
                    <h4 class="section-subtitle"><?php echo $section['subtitle']; ?></h4>
				<?php } ?>
                <div class="owl-carousel home-owl-carousel custom-carousel owl-theme outer-top-xs product-slider" data-item="5">
					<?php
					foreach ( $section['products'] as $product ) { ?>
                        <div class="item item-carousel">
							<?php get_single_product_loop_layout( $product, false, true ); ?>
                        </div>
                        <!-- /.item -->
						<?php
					}
					?>
                    <!-- /.item -->
                </div>
                <!-- /.home-owl-carousel -->
            </section>
			<?php
		}
		elseif ( $section['type'] == 'products' ) {
			if ( empty( $section['products'] ) ) {
				continue;
			}
		?>
			<!-- ============================================== FEATURED PRODUCTS ============================================== -->
			<section class="section products_section">
				<?php if ( isset($section['label']) ) { ?>
                    <h3 class="section-title"><?php echo $section['label']; ?></h3>
				<?php } if ( isset($section['subtitle']) ) { ?>
                    <h4 class="section-subtitle"><?php echo $section['subtitle']; ?></h4>
				<?php } ?>
				<div class="owl-carousel home-owl-carousel custom-carousel owl-theme outer-top-xs product-slider" data-item="5">
					<?php
					foreach ( $section['products'] as $product ) { ?>
						<div class="item item-carousel">
							<?php get_single_product_loop_layout( $product, false, true ); ?>
						</div>
						<!-- /.item -->
						<?php
					}
					?>
					<!-- /.item -->
				</div>
				<!-- /.home-owl-carousel -->
			</section>
			<?php
		}
		else if ( $section['type'] == 'brand_slider' ) {
			?>
            <div id="brands-carousel" class="logo-slider">
                <?php if ( isset($section['label']) ) { ?>
                    <h3 class="section-title"><?php echo $section['label']; ?></h3>
                <?php } if ( isset($section['subtitle']) ) { ?>
                    <h4 class="section-subtitle"><?php echo $section['subtitle']; ?></h4>
                <?php } ?>
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
            <!-- /.logo-slider -->
			<?php
		}
	}
}
// End of file.php.
