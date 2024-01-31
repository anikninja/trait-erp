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
            <div class="col-xs-12 <?php echo $showSidebar ? 'col-lg-10 col-md-9 col-sm-8' : 'col-lg-12 col-md-12 col-sm-12';?> homebanner-holder main-right">
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
