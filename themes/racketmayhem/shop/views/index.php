<?php
defined('BASEPATH') or exit('No direct script access allowed');

$showSidebar = ( isset( $theme_options['sidebar'] ) && ! empty( $theme_options['sidebar'] ) );
$categoriesToShow = array_slice( $categories, 0, 4 );
?>
<div class="body-content outer-top-vs" id="top-banner-and-menu">
    <div class="container">
        <div class="row">
            <?php include 'sidebar-home.php'; ?>
            <!-- ============================================== CONTENT ============================================== -->
            <div class="col-xs-12 <?php echo $showSidebar ? 'col-lg-10 col-md-9 col-sm-8 homebanner-holder has-sidebar' : 'col-lg-12 col-md-12 col-sm-12 no-sidebar';?>">
                <div class="featured-categories-area">
                    <div class="row">
	                    <?php
	                    $featured_categories = array_slice($featured_categories, 0, 8);
	                    foreach ($featured_categories as $featured_category ) {
		                    $cat_image = ( $featured_category['image'] ) ? base_url() . 'assets/uploads/' . $featured_category['image'] : $assets . 'images/icon.png';
		                    ?>
                            <div class="col-md-3 col-sm-4">
                                <a href="<?php echo site_url( 'category/' . $featured_category['slug'] ); ?>">
                                    <img src="<?= $cat_image; ?>" alt="<?= $featured_category['name'] ?>">
                                    <span><?= $featured_category['name'] ?></span>
                                </a>
                            </div>
	                    <?php } ?>
                    </div>
                </div>
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
