<?php
defined('BASEPATH') or exit('No direct script access allowed');

$showSidebar = ( isset( $theme_options['sidebar'] ) && ! empty( $theme_options['sidebar'] ) );
$showSlider  = ( isset( $theme_options['slider'] ) && $theme_options['slider'] );
$categoriesToShow = array_slice( $categories, 0, 4 );
?>
<div class="body-content outer-top-vs hidden-xs" id="top-banner-and-menu">
    <div class="container">
        <div class="row">
            <?php include 'sidebar-home.php'; ?>
            <!-- ============================================== CONTENT ============================================== -->
            <div class="col-xs-12 col-sm-12 <?php echo $showSidebar ? 'col-md-9' : 'col-md-12';?> homebanner-holder">
	            <?php include 'home-slider.php'; ?>
	            <?php if ( ! empty( $new_products ) ) { ?>
                <!-- ============================================== SCROLL TABS ============================================== -->
                <div id="product-tabs-slider" class="scroll-tabs outer-top-vs">
                    <div class="more-info-tab clearfix ">
                        <h3 class="new-product-title pull-left">New Products</h3>
                        <ul class="nav nav-tabs nav-tab-line pull-right" id="new-products-1">
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
	                                <div class="owl-carousel home-owl-carousel custom-carousel owl-theme" data-item="5">
	                                <?php foreach ( $new_products as $product ) { ?>
                                        <div class="item item-carousel">
	                                        <?php get_single_product_loop_layout( $product ); ?>
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
	            <?php } ?>
                <?php include 'home-sections.php'; ?>
            </div>
            <!-- /.homebanner-holder -->
            <!-- ============================================== CONTENT : END ============================================== -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
</div>
<!-- /#top-banner-and-menu -->

<?php if ( isset( $theme_options['footer_top'] ) && ! empty( $theme_options['footer_top'] ) ) { ?>
<div class="footer-top">
<?php
foreach ( $theme_options['footer_top'] as $section ) {
	if ( ! isset( $section['type'] ) ) continue;
	if ( 'brand_slider' === $section['type'] ) {
	?>
	<!-- ============================================== BRANDS CAROUSEL ============================================== -->
	<div id="brands-carousel" class="logo-slider hidden-xs">
		<div class="container">
			<div class="row">
				<div class="logo-slider-inner">
					<div id="brand-slider" class="owl-carousel brand-slider custom-carousel owl-theme">
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
				<!-- /.logo-slider-inner -->
			</div>
		</div>
	</div>
	<!-- /.logo-slider -->
	<!-- ============================================== BRANDS CAROUSEL : END ============================================== -->
	<?php
	}
	if ( 'custom' === $section['type'] && isset( $section['content'] ) ) {
		echo html_entity_decode( $section['content'] );
	}
}
?>
</div>
<!-- /.footer-top -->
<?php } ?>
<!-- start mobile body -->
<div class="visible-xs mobile-category-list text-center">
    <div class="container">
        <div class="row">
        <?php
            foreach($categories as $category) {
                $cat_image = ($category->image) ? base_url() . 'assets/uploads/' . $category->image : $assets . 'images/icon.png';
                ?>
                <div class="col-xs-4">
                    <div class="single-cat-item">
                        <a href="<?php echo site_url( 'category/' . $category->slug ); ?>">
                            <div class="cat-item-image">
                                <img src="<?php echo $cat_image; ?>" alt="<?php echo $category->name; ?>">
                            </div>
                            <h4><?php echo $category->name; ?></h4>
                        </a>
                    </div>
                </div>
        <?php
            }
        ?>
        </div>
    </div>
</div>
<!-- end mobile body -->
