<?php defined('BASEPATH') or exit('No direct script access allowed');?><!DOCTYPE html>
<html class="loading no_js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<script type="text/javascript">document.getElementsByClassName('no_js')[0].classList.remove('no_js')</script>
    <script type="text/javascript">if (parent.frames.length !== 0) { top.location = '<?= site_url(); ?>'; }</script>
    <title><?= $page_title; ?></title>
    <meta name="description" content="<?= $page_desc; ?>">
    <link rel="shortcut icon" href="<?= $assets; ?>images/icon.png">
    
	<style>
		/*===============*/
		/* Loading Animation.
		/* @see https://loading.io/css/
		/*===============*/
		
		html:not(.no_js).loading {
			overflow: hidden;
		}
		#gsLoading {
			position: fixed;
			display: none;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			z-index: 99999999;
			background: rgba(123, 148, 18, 0.24);
		}
		#gsLoading.preloader{
			background: #ffffff;
		}
		#gsLoading.active {
			display: block;
		}
		html.no_js #gsLoading {
			display: none !important;
		}
		.loading-ripple {
			position: fixed;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			display: inline-block;
			width: 80px;
			height: 80px;
		}
		.loading-ripple div {
			position: absolute;
			border: 4px solid #d66b31;
			opacity: 1;
			border-radius: 50%;
			animation: loading-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
		}
		.loading-ripple div:nth-child(2) {
			animation-delay: -0.5s;
		}
		@keyframes loading-ripple {
			0% {
				top: 36px;
				left: 36px;
				width: 0;
				height: 0;
				opacity: 1;
			}
			100% {
				top: 0;
				left: 0;
				width: 72px;
				height: 72px;
				opacity: 0;
			}
		}
	</style>
	<!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="<?php echo $assets; ?>css/bootstrap.min.css">

    <!-- Customizable CSS -->
    <link rel="stylesheet" href="<?php echo $assets; ?>css/main.css">
    <link rel="stylesheet" href="<?php echo $assets; ?>css/blue.css">
    <link rel="stylesheet" href="<?php echo $assets; ?>css/owl.carousel.css">
    <link rel="stylesheet" href="<?php echo $assets; ?>css/owl.transitions.css">
    <link rel="stylesheet" href="<?php echo $assets; ?>css/animate.min.css">
    <link rel="stylesheet" href="<?php echo $assets; ?>css/rateit.css">
    <link rel="stylesheet" href="<?php echo $assets; ?>css/bootstrap-select.min.css">
    <link rel="stylesheet" href="<?php echo $assets; ?>css/sweetalert2.min.css">
    <link rel="stylesheet" href="<?php echo $assets; ?>css/select2.min.css">
    <!-- Icons/Glyphs -->
    <link rel="stylesheet" href="<?php echo $assets; ?>css/font-awesome.css">

    <!-- Fonts -->
    <?php /*<link href="https://fonts.googleapis.com/css?family=Barlow:200,300,300i,400,400i,500,500i,600,700,800" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,400italic,600,600italic,700,700italic,800' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>*/ ?>

    <link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500&family=Montserrat:wght@400;700&family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,400;1,600;1,700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <link href="<?= base_url('assets/custom/shop.css') ?>" rel="stylesheet">
	<style type="text/css" media="all"><?= html_entity_decode( $custom_css ); ?></style>

    <meta property="og:url" content="<?= isset($product) && !empty($product) ? site_url('product/' . $product->slug) : site_url(); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?= $page_title; ?>" />
    <meta property="og:description" content="<?= $page_desc; ?>" />
    <meta property="og:image" content="<?= isset($product) && !empty($product) ? $product->image : base_url( 'assets/uploads/logos/' . $shop_settings->logo ); ?>" />
	<?php
	$bodyClasses = [
		'cnt-home',
		'currency-' . strtolower( $Settings->selected_currency ),
	];
	$p = 'page-'. $m;
	if ( $v !== 'index' ) {
		$p .= '-' . $v;
	}
	$bodyClasses[] = $p;
	if ( $m == 'main' && $v == 'index' || $m == 'shop' && $v == 'product' ) {
		$bodyClasses[] = 'common-home';
	}
	if ( $loggedIn ) {
		$bodyClasses[] = 'user-logged-in';
		if ( $Staff ) {
			$bodyClasses[] = 'user-staff';
		}
		if ( $Customer ) {
			$bodyClasses[] = 'user-customer';
		}
		if ( $Supplier ) {
			$bodyClasses[] = 'user-supplier';
		}
		if ( $Owner ) {
			$bodyClasses[] = 'user-owner';
		}
	}
	?>
</head>
<body class="<?php echo implode( ' ', $bodyClasses ); ?>">
<div class="active preloader" id="gsLoading">
	<div class="loading-ripple"><div></div><div></div></div>
</div>
<!-- /#gsLoading -->
<!-- ============================================== HEADER ============================================== -->
<header class="header-style-1">
    <!-- ============================================== TOP MENU ============================================== -->
    <div class="top-bar animate-dropdown">
        <div class="container">
            <div class="header-top-inner">
                <div class="cnt-account">
                    <ul class="list-unstyled">
	                    <?= $loggedIn && $Staff ? '<li class="hidden-xs"><a href="' . admin_url() . '"><i class="fa fa-dashboard"></i> ' . lang('admin_area') . '</a></li>' : ''; ?>
	                    <?php if (!$shop_settings->hide_price) { ?>
                            <li class="check <?= $m == 'cart_ajax' && $v == 'checout' ? 'active' : ''; ?>"><a href="<?= site_url('cart/checkout'); ?>"><?= lang('checkout'); ?></a></li>
	                    <?php } ?>
                        <?php if ($loggedIn) {?>
                            <li class="myaccount"><a href="<?= site_url('profile'); ?>"><i class="mi fa fa-user"></i> <?= lang('myaccount'); ?></a></li>
                            <li class="logout"><a href="<?= site_url('logout'); ?>"><i class="mi fa fa-lock"></i> <?= lang('logout'); ?></a></li>
                        <?php } else{?>
                            <li class="login"><a class="join-btn" href="<?= site_url('login'); ?>"><span><?= lang('login') ?></span></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="cnt-block">
                    <ul class="list-unstyled list-inline"></ul>
                    <!-- /.list-unstyled -->
                </div>
            </div>
            <!-- /.header-top-inner -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.header-top -->
    <!-- ============================================== TOP MENU : END ============================================== -->
    <div class="main-header">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-3 logo-holder">
                    <!-- ============================================================= LOGO ============================================================= -->
                    <div class="logo"> <a href="<?= site_url(); ?>"> <img alt="<?= $shop_settings->shop_name; ?>" src="<?= base_url( 'assets/uploads/logos/' . $shop_settings->logo ); ?>" class="img-responsive" /> </a> </div>
                    <!-- /.logo -->
                    <!-- ============================================================= LOGO : END ============================================================= --> </div>
                    <!-- /.logo-holder -->

                <div class="col-lg-7 col-md-6 col-sm-8 col-xs-12 top-search-holder">
                    <!-- /.contact-row -->
                    <!-- ============================================================= SEARCH AREA ============================================================= -->
                    <div class="search-area search-box">
	                    <?= shop_form_open('products', 'id="product-search-form"'); ?>
                            <div class="control-group">
                                <input name="query" type="text" class="search-field" id="product-search" aria-label="Search..." placeholder="<?= lang('search'); ?>"/>
                                <button type="submit" class="search-button"></button> </div>
                            </div>
	                     <?= form_close(); ?>
                    <!-- /.search-area -->
                    <!-- ============================================================= SEARCH AREA : END ============================================================= --> </div>
                <!-- /.top-search-holder -->

                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12 animate-dropdown top-cart-row">
                    <!-- ============================================================= SHOPPING CART DROPDOWN ============================================================= -->

                    <div class="dropdown-cart cart-open-btn">
	                    <a href="<?php echo site_url( 'cart/checkout/' ); ?>" class="lnk-cart">
                            <div class="items-cart-inner">
                                <div class="basket">
                                    <div class="basket-item-count">
	                                    <span class="count cart-total-items"><?php echo $cart['total_items']; ?></span>
                                    </div>
                                    <div class="total-price-basket">
	                                    <span class="lbl"><?= lang('shopping_cart') ?></span>
	                                    <span class="value"><?php echo $cart['subtotal']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- /.dropdown-cart -->

                    <!-- ============================================================= SHOPPING CART DROPDOWN : END============================================================= --> </div>
                <!-- /.top-cart-row -->
            </div>
            <!-- /.row -->

        </div>
        <!-- /.container -->

    </div>
    <!-- /.main-header -->
	
	<!-- ============================================== NAVBAR ============================================== -->
    <div class="header-nav animate-dropdown">
        <div class="container">
            <div class="yamm navbar navbar-default" role="navigation">
                <div class="navbar-header">
                    <button data-target="#grocerant-main-menu-collapse" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                        <span class="sr-only">Toggle navigation</span>
	                    <span class="icon-bar"></span>
	                    <span class="icon-bar"></span>
	                    <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="nav-bg-class">
                    <div class="navbar-collapse collapse" id="grocerant-main-menu-collapse">
                        <div class="nav-outer">
                            <ul class="nav navbar-nav"><?= $shop_main_menus; ?></ul>
                            <!-- /.navbar-nav -->
                            <div class="clearfix"></div>
                        </div>
                        <!-- /.nav-outer -->
                    </div>
                    <!-- /.navbar-collapse -->
                </div>
                <!-- /.nav-bg-class -->
            </div>
            <!-- /.navbar-default -->
        </div>
        <!-- /.container-class -->
    </div>
    <!-- /.header-nav -->
    <!-- ============================================== NAVBAR : END ============================================== -->
</header>
<!-- ============================================== HEADER : END ============================================== -->
