<?php defined('BASEPATH') or exit('No direct script access allowed');?><!DOCTYPE html>
<html class="loading no_js" lang="<?= lang( 'html_lang' ); ?>" dir="<?= lang( 'html_dir' ); ?>">
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
		
		#gsLoading {
			position: fixed;
			display: none;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			z-index: 99999999;
			background: rgba(6, 5, 33, 0.3);
		}
		#gsLoading.preloader{
			background: #ffffff;
		}
		#gsLoading.active {
			display: block;
		}
		html.no_js #gsLoading {
			display: none;
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
			border: 4px solid #FFA07A;
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
    <?php
    $styleSheets = [
        'bootstrap.min.css',
        'banners.css',
        'header1-red-chosen-searchpro.css',
        'main.css',
//        'owl.carousel.css',
        'owl2.carousel.css',
//        'owl.transitions.css',
//		'animate.min.css',
//        'rateit.css',
        'chosen.css',
//        'bootstrap-select.min.css',
        'sweetalert2.min.css',
        'select2.min.css',
        'font-awesome.css',
        'rrssb.css',
    ];
//    $assetsDir = $this->shopThemeDir . '/shop/assets/css/';
//    $t = file_exists( $assetsDir . $css ) ? filemtime( $assetsDir . $css ) : false;
    foreach ( $styleSheets as $css ) {
//		printf( '<link rel="preload" as="style" href="%scss/%s" onload="this.rel=\'stylesheet\'">', $assets, $css );
//		printf( '<link rel="preload" as="style" href="%scss/%s">', $assets, $css );
		printf( '<link rel="stylesheet" href="%scss/%s">', $assets, $css );
    }
    $GFs = [
        /*'Barlow:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500',
        'Montserrat:wght@400;700',
        'Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,400;1,600;1,700',*/
        'Roboto:wght@300;400;500;700',
    ];
    $GFs = implode( '&family=', $GFs );
    $GFs = 'https://fonts.googleapis.com/css2?family=' . $GFs;
    ?>
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="<?php echo $GFs; ?>&display=swap" rel="preload" as="style">
    <link href="<?php echo $GFs; ?>&display=swap" media="print" onload="this.media='all'" rel="stylesheet">
    <noscript>
        <link href="<?php echo $GFs; ?>&display=swap" rel="stylesheet">
    </noscript>

    <link href="<?= base_url('assets/custom/shop.css') ?>" rel="preload" as="style">
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
        'banner-effect-7',
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

    $currentLanguage = ucwords( str_replace( '-', ' ', $Settings->user_language ) );
    $languages = array_map(
        function ( $path ) {
            $langauge = basename( $path );
            if ( file_exists(APPPATH . 'language' . DIRECTORY_SEPARATOR . $langauge . DIRECTORY_SEPARATOR . 'shop' . DIRECTORY_SEPARATOR . 'shop_lang.php') ) {
                return [
                    'slug'  => $langauge,
                    'label' => ucwords( str_replace( '-', ' ', $langauge ) ),
                ];
            }
            return null;
        },
        glob( APPPATH . 'language/*', GLOB_ONLYDIR )
    );
    $languages = array_filter( $languages );

    $showCurrencies = ! $shop_settings->hide_price && ! empty( $currencies );
    $showLanguages = $currentLanguage && ! empty( $languages );
    $storeFlag = isset( $theme_options['header']['top']['content'] ) ? html_entity_decode( $theme_options['header']['top']['content'] ) : false;
    $headerTopMiddle = isset( $theme_options['header']['top_middle']['content'] ) ? html_entity_decode( $theme_options['header']['top_middle']['content'] ) : false;
    $showSlider  = ( isset( $theme_options['main_banner']['slider']['show'] ) && $theme_options['main_banner']['slider']['show'] );
    $shoBannerSection = ( ( $m == 'main' && $v == 'index' ) && $showSlider );
?>
</head>
<body class="<?php echo implode( ' ', $bodyClasses ); ?>">
<div class="active preloader" id="gsLoading">
	<div class="loading-ripple"><div></div><div></div></div>
</div>
<!-- /#gsLoading -->
<!-- ============================================== HEADER ============================================== -->
<header class="cs-header">
	<div class="header-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-sm-5 col-xs-12">
                    <?= $storeFlag; ?>
                </div>
                <div class="col-lg-4 col-sm-5 col-xs-12">
                    <?= $headerTopMiddle; ?>
                </div>
                <div class="col-lg-1 col-sm-2 col-xs-12 col-lg-push-2">
                    <div class="header-mini">
                        <div class="mini-cart">
                            <a role="button" class="cart-open-btn">
                                <div class="cart-icon"></div>
                                <span class="count cart-total-items"><?php echo $cart['total_items']; ?></span>
                                <!-- /.count cart-total-items -->
                                <div class="total-price-basket hidden-sm hidden-xs">
                                    <span class="lbl"><?= $cart['subtotal']; ?></span>
                                </div>
                            </a>
                            <!-- /.cart-open-btn -->
                        </div>
                        <!-- /.mini-cart -->
                        <a href="#" role="button" class="icon-search hidden-lg hidden-md hidden-sm" aria-label="<?= lang( 'open-search-box' ); ?>">
                            <i class="fa fa-search"></i>
                        </a>
                        <!-- /.site-search -->
                    </div>
                </div>
                <div class="col-lg-2 col-sm-12 col-xs-12 col-lg-pull-1">
                    <div class="site-search">
                        <?= shop_form_open('products', 'id="product-search-form"'); ?>
                        <div class="search input-group form-group">
                            <input id="product-search" class="autocomplete autosearch-input form-control" type="text" value="<?= isset( $_POST['query'] ) ? xss_clean( $_POST['query'] ) : ''; ?>" size="50" autocomplete="off" placeholder="<?= lang('search'); ?>" name="query" aria-label="<?= lang('search'); ?>">
                            <div class="input-group-btn">
                                <button type="submit" class="button-search btn btn-default btn-lg" aria-label="<?= lang('submit'); ?>">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
	</div>
	<!-- /.header-top -->
    <div class="header-main">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-8 col-xs-8 logo-wrap">
                    <div class="site-logo">
                        <a href="<?= site_url(); ?>">
                            <img src="<?= base_url( 'assets/uploads/logos/' . $shop_settings->logo ); ?>" alt="<?= $shop_settings->shop_name; ?>">
                        </a>
                    </div>
                    <!-- /.site-logo -->
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-right">
                    <?php if ( $loggedIn ) : ?>
                    <div class="header-account nav-item has-dropdown account">
                        <a href="#">
                            <a href=""><img src="<?= $assets; ?>images/account-icon.png"></a>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="ht-ac-btn">
                            <li>
                                <?php
                                $name = ci_get_user_display_name( $user );
                                if ( ! $loggedIn ) { ?>
                                    <span class="welcome"><?php printf( lang( 'welcome_to_x_site_name' ), $shop_settings->shop_name ) ?></span>
                                    <span class="flyout-bottons">
                        <a class="join-btn" href="<?= site_url( 'login' ); ?>" rel="nofollow"><?= lang( 'sign_in_join' ); ?></a>
                        <?php /*<a class="sign-btn" href="javaScript:void(0);" rel="nofollow"><?= lang( 'sign_in' ); ?></a>*/ ?>
                    </span>
                                <?php } else {
                                    ?>
                                    <div class="flyout-logined">
                                        <i class="flyout-user-avatar"><?= ci_get_user_avatar( $user, true, [ 'alt' => $name ], true, 32 ); ?></i>
                                        <p class="flyout-welcome"><?php printf( lang( 'welcome_back_x_user' ), $name ); ?></p>
                                    </div>
                                <?php } ?>
                            </li>
                            <?php if ( $loggedIn ) { ?>
                                <li>
                                    <a href="<?= site_url('logout'); ?>"><?= lang( 'sign_out' ); ?></a>
                                </li>
                                <li role="separator" class="divider"></li>
                                <?= $Staff ? '<li><a href="' . admin_url() . '"><i class="fa fa-dashboard"></i> ' . lang('admin_area') . '</a></li>' : ''; ?>
                            <?php } ?>
                            <?php
                            foreach ( cs_get_my_account_navs( $m, $v ) as $id => $nav ) {
                                if ( ! $loggedIn && $nav['login'] ) {
                                    continue;
                                }
                                if ( $loggedIn && ! $nav['login'] ) {
                                    continue;
                                }
                                if ( in_array( $id, [ 'login', 'logout' ] ) ) {
                                    continue;
                                }
                                $class = 'item';
                                $class .= ' ' . $id;
                                if ( $nav['active'] ) $class .= ' active';
                                if ( 'logout' === $id ) {
                                }
                                ?>
                                <li>
                                    <a class="<?= $class; ?>" href="<?= $nav['link']; ?>">
                                        <i class="fa fa-<?= $nav['icon'] ?>"></i>
                                        <span class="text"><?= $nav['label']; ?></span>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php else: ?>
                    <div class="header-account nav-item account">
                        <a class="join-btn" href="<?= site_url( 'login' ); ?>"><img src="<?= $assets; ?>images/account-icon.png"></a>
                    </div>
                    <?php endif; ?>
                    <div class="wishlist">
                        <a class="<?= $loggedIn ? '' : 'join-btn'; ?>" href="<?= shop_url( 'wishlist' ); ?>"><img src="<?= $assets; ?>images/wishlist-icon.png"></a>
                    </div>
                </div>
                <div class="col-lg-12 col-xs-12">
                    <nav class="main-nav" role="navigation">
                        <!-- Mobile menu toggle button (hamburger/x icon) -->
                        <input id="main-menu-state" type="checkbox" />
                        <label class="main-menu-btn" for="main-menu-state"> <span class="main-menu-btn-icon"></span> Toggle main menu visibility </label>
                        <h2 class="nav-brand"><a href="#">Menu</a></h2>
                        <!-- Sample menu definition -->
                        <ul id="main-menu" class="sm sm-clean"><?= $shop_main_menus; ?></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- /.cs-header -->
<?php if ( $shoBannerSection ) { ?>
<div class="home-firstscreen">
    <div class="container-fluid banner-wrap">
	    <div class="row"><?php include  'home-slider.php'; ?></div>
    </div>
</div>
<!-- /.home-firstscreen -->
<div class="clearfix"></div>
<?php } ?>
<!-- ============================================== HEADER : END ============================================== -->
