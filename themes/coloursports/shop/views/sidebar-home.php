<?php defined('BASEPATH') or exit('No direct script access allowed');
if ( $showSidebar ) {
?>
<!-- ============================================== SIDEBAR ============================================== -->
<div class="col-lg-2 col-md-3 col-sm-4 sidebar main-left">
    <?php
    if ( $m == 'main' && $v == 'index' ) { ?>
        <div class="homepage-categories-menu"></div>
        <?php
    } ?>
    <div class="row">
    <?php
    if ( isset( $theme_options['sidebar'] ) && ! empty( $theme_options['sidebar'] ) ) {
        foreach ( $theme_options['sidebar'] as $section ) {
            if ( $section['type'] == 'custom' ) {
                echo html_entity_decode( $section['content'] );
            } elseif ( in_array( $section['type'], [ 'new_products', 'products', 'most_viewed', 'trending_products', 'daily_deals', 'featured_products'] ) ) {
	            if ( empty( $section['products'] ) ) {
		            continue;
	            }
            ?>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="module product-simple <?= $section['type']; ?>">
	                    <?php if ( $section['label'] ) { ?>
		                    <h3 class="modtitle">
			                    <span><?php echo $section['label']; ?></span>
		                    </h3>
	                    <?php } ?>
                        <div class="modcontent">
                            <div class="so-extraslider">
                                <!-- Begin extraslider-inner -->
                                <div class="extraslider-inner products-list sidebar-slider" data-effect="none">
                                <?php
                                $new_products_chunk = array_chunk( $section['products'], 4);
                                foreach ( $new_products_chunk as $products ) { ?>
                                    <div class="item ">
                                    <?php foreach ($products as $product) {
                                        get_single_product_loop_third_layout( $product );
                                    } ?>
                                    </div>
                                <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
        }
    }
    ?>
    </div>

</div>
<!-- /.sidemenu-holder -->
<!-- ============================================== SIDEBAR : END ============================================== -->
<?php
}
