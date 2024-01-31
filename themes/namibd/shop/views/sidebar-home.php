<?php defined('BASEPATH') or exit('No direct script access allowed');
if ( $showSidebar ) {
?>
<!-- ============================================== SIDEBAR ============================================== -->
<div class="col-xs-12 col-sm-12 col-md-3 sidebar">
	<?php
        include 'pages/category-widget.php';

        if ( isset( $theme_options['sidebar'] ) && ! empty( $theme_options['sidebar'] ) ) {
            foreach ( $theme_options['sidebar'] as $section ) {
                if ( $section['type'] == 'custom' ) {
                    echo html_entity_decode( $section['content'] );
                } elseif ( in_array( $section['type'], [ 'products', 'daily_deals' ] ) ) {
                    if ( empty( $section['products'] ) ) {
                        continue;
                    }

	                $class = '';
                    $promo = false;
                    if ( $section['type'] === 'daily_deals' ) {
                        $class = "hot-deals";
	                    $promo = true;
                    } elseif ( $section['type'] === 'products' ) {
	                    $class = "special-product";
                    }
                    ?>
                    <div class="sidebar-widget hot-deals outer-bottom-small">
	                    <?php if ( $section['label'] ) { ?>
                            <h3 class="section-title"><?php echo $section['label']; ?></h3>
	                    <?php } ?>
                        <div class="sidebar-widget-body outer-top-xs">
                            <div class="owl-carousel sidebar-carousel special-offer custom-carousel owl-theme outer-top-xs">
	                            <?php
	                            $new_products_chunk = array_chunk( $section['products'], 4);
	                            foreach ( $new_products_chunk as $products ) { ?>
                                    <div class="item">
                                        <div class="<?php echo $class; ?>">
                                            <?php foreach ($products as $product) {
                                                get_single_product_loop_layout( $product, $promo );
                                            } ?>
                                        </div>
                                    </div>
	                            <?php } ?>
                            </div>
                        </div>
                        <!-- /.sidebar-widget-body -->
                    </div>
                    <?php
                }
            }
        }
	?>

</div>
<!-- /.sidemenu-holder -->
<!-- ============================================== SIDEBAR : END ============================================== -->
<?php
}