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
    <?php
    $styleSheets = [
        'bootstrap.min.css',
        'banners.css',
        'header1-red-chosen-searchpro.css',
        'main.css',
        'blue.css',
        'owl.carousel.css',
        'owl2.carousel.css',
        'owl.transitions.css',
//		'animate.min.css',
        'rateit.css',
        'chosen.css',
        'bootstrap-select.min.css',
        'sweetalert2.min.css',
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
    if ( $m == 'main' && $v == 'index' ) {
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
    $storeNotice = sprintf( lang( 'welcome_to_x_site_name' ), $shop_settings->shop_name );
    $showSlider  = ( isset( $theme_options['main_banner']['slider']['show'] ) && $theme_options['main_banner']['slider']['show'] );
    $shoBannerSection = ( ( $m == 'main' && $v == 'index' ) && $showSlider );
    ?>
</head>
<body class="<?php echo implode( ' ', $bodyClasses ); ?>">
<!-- ============================================== HEADER ============================================== -->
<header class="cs-header">
    <div class="header-top">
        <div class="container">
            <?php if ( ! empty( $storeNotice ) ) { ?>
                <div class="pull-left hidden-xs">
                    <div class="site-notice"><?= $storeNotice; ?></div>
                    <!-- /.site-notice -->
                </div>
            <?php } ?>
            <div class="pull-right">
                <div class="nav-group-wrap">
                    <?php /*<div class="nav-group visible-xs">
						<div class="nav-item mini-cart">
							<a role="button" class="cart-open-btn">
								<i class="fa fa-shopping-bag"></i>
								<span class="count">0</span>
								<!-- /.count -->
							</a>
							<!-- /.cart-open-btn -->
						</div>
						<!-- /.nav-item mini-cart -->
					</div>
					<!-- /.nav-group -->*/ ?>
                    <?php if ( $showCurrencies || $showLanguages ) { ?>
                        <div class="nav-group">
                            <?php if ( $showCurrencies ) { ?>
                                <div class="nav-item has-dropdown currency">
                                    <a href="#" role="button" id="ht-currency-btn" data-toggle="dropdown" aria-controls="ht-currency-drop" aria-haspopup="true" aria-expanded="false">
                                        <span><?= $selected_currency->symbol . ' ' . $selected_currency->code; ?></span>
                                        <i class="fa fa-caret-down"></i>
                                        <i class="fa fa-caret-up"></i>
                                    </a>
                                    <ul class="dropdown-menu" id="ht-currency-drop" aria-label="<?= lang( 'select_language' ); ?>">
                                        <?php
                                        foreach ( $currencies as $currency ) {
                                            printf( '<li><a class="btn-block" href="%s">%s</a></li>', site_url('main/currency/' . $currency->code), $currency->symbol . ' ' . $currency->code );
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <!-- /.nav-item currency -->
                            <?php } ?>
                            <?php if ( $showLanguages ) { ?>
                                <div class="nav-item has-dropdown language">
                                    <a href="#" role="button" id="ht-lang-btn" data-toggle="dropdown" aria-controls="ht-lang-drop" aria-haspopup="true" aria-expanded="false">
                                        <img src="<?= base_url('assets/images/' . $Settings->user_language . '.png'); ?>" alt="<?php echo $currentLanguage; ?>">
                                        <span class="text"><?php echo $currentLanguage; ?></span>
                                        <i class="fa fa-caret-down"></i>
                                        <i class="fa fa-caret-up"></i>
                                    </a>
                                    <ul class="dropdown-menu" id="ht-lang-drop" aria-label="<?= lang( 'select_language' ); ?>">
                                        <?php foreach ( $languages as $language ) { ?>
                                            <li>
                                                <a class="btn-block language-select" href="<?= site_url('main/language/' . $language['slug'] ); ?>">
                                                    <img class="language-img" src="<?= base_url('assets/images/' . $language['slug'] . '.png'); ?>" alt="<?= $language['label']; ?>">
                                                    <span class="text"><?= $language['label']; ?></span>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <!-- /.nav-item language -->
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <!-- /.nav-group -->
                    <div class="nav-group">
                        <div class="nav-item wishlist">
                            <a href="<?= shop_url( 'wishlist' ); ?>">
                                <i class="fa fa-heart"></i>
                                <span class="text"><?= lang( 'wishlist' ); ?></span>
                            </a>
                        </div>
                        <!-- /.nav-item wishlist -->
                        <div class="nav-item has-dropdown account">
                            <a href="<?= site_url( 'profile' ); ?>" role="button" id="ht-ac-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user"></i>
                                <span class="text"><?= lang( 'account' ); ?></span>
                                <i class="fa fa-caret-down"></i>
                                <i class="fa fa-caret-up"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="ht-ac-btn">
                                <li>
                                    <?php if ( ! $loggedIn ) { ?>
                                        <span class="welcome"><?php printf( lang( 'welcome_to_x_site_name' ), $shop_settings->shop_name ) ?></span>
                                        <span class="flyout-bottons">
										<a class="join-btn" href="<?= site_url( 'login' ); ?>" rel="nofollow"><?= lang( 'sign_in_join' ); ?></a>
										<?php /*<a class="sign-btn" href="javaScript:void(0);" rel="nofollow"><?= lang( 'sign_in' ); ?></a>*/ ?>
									</span>
                                    <?php } else {
                                        $name = cs_get_user_display_name( $user );
                                        ?>
                                        <div class="flyout-logined">
                                            <i class="flyout-user-avatar"><?= cs_get_user_avatar( $user, true, [ 'alt' => $name ], true, 32 ); ?></i>
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
                                    if ( in_array( $id, [ 'login', 'logout', 'wishlist' ] ) ) {
                                        continue;
                                    }
                                    $class = 'item';
                                    $class .= ' ' . $id;
                                    if ( $nav['active'] ) $class .= ' active';
                                    if ( 'logout' === $id ) {
                                        ?>

                                        <?php
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
                        <!-- /.nav-item account -->
                    </div>
                    <!-- /.nav-group -->
                </div>
                <!-- /.nav-group-wrap -->
            </div>
        </div>
    </div>
    <!-- /.header-top -->
    <div class="clearfix"></div>
    <div class="header-main">
        <div class="container">
            <div class="row">
                <div class="col-sm-3 col-xs-4 logo-wrap">
                    <div class="site-logo">
                        <a href="<?= site_url(); ?>">
                            <img src="<?= base_url( 'assets/uploads/logos/' . $shop_settings->logo ); ?>" alt="<?= $shop_settings->shop_name; ?>">
                        </a>
                    </div>
                    <!-- /.site-logo -->
                    <div class="header-categories has-dropdown">
                        <a href="#" class="trigger" role="button" id="mh-categories-btn" data-toggle="dropdown" aria-controls="ht-currency-drop" aria-haspopup="true" aria-expanded="false" aria-label="<?= lang( 'all_categories' ); ?>">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <i class="fa fa-caret-down"></i>
                            <i class="fa fa-caret-up"></i>
                        </a>
                        <!-- /.trigger -->
                        <?php
                        $attributes = [ 'class' => 'dropdown-menu css-menu', 'id' => 'mh-categories-drop', 'aria-labelledby' => 'mh-categories-btn' ];
                        get_category_dropdown( $categories, $attributes );
                        ?>
                    </div>
                    <!-- /.header-categories -->
                </div>
                <div class="col-sm-9 col-xs-8 header-mini-wrap">
                    <div class="row">
                        <div class="site-search col-md-offset-0 col-sm-9 col-sm-offset-1">
                            <?= shop_form_open('products', 'id="product-search-form"'); ?>
                            <div class="search input-group form-group">
                                <div class="select_category filter_type hidden-xs hidden-sm">
                                    <select class="no-border chosen-select" style="display: none" name="__category">
                                        <option value="">All Category </option>
                                        <?php
                                        foreach ( $categories as $category ) {
                                            $selected = isset( $_POST['__category'] ) ? xss_clean( $_POST['__category'] ) : '';
                                            if ( $selected ) {
                                                $selected = selected( $selected, $category->id, false );
                                            }
                                            printf( '<option value="%s"%s>%s</option>', $category->id, $selected, $category->name );
                                            if ( ! empty( $category->subcategories ) ) {
                                                foreach ( $category->subcategories as $subcategory ) {
                                                    $selected = isset( $_POST['__category'] ) ? xss_clean( $_POST['__category'] ) : '';
                                                    if ( $selected ) {
                                                        $selected = selected( $selected, $category->id . '/' . $subcategory->id, false );
                                                    }
                                                    printf( '<option value="%s/%s"%s>&nbsp;&nbsp;&nbsp;%s</option>', $category->id, $subcategory->id, $selected, $subcategory->name );
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <input id="product-search" class="autocomplete autosearch-input form-control" type="text" value="<?= isset( $_POST['query'] ) ? xss_clean( $_POST['query'] ) : ''; ?>" size="50" autocomplete="off" placeholder="<?= lang('search'); ?>" name="query" aria-label="<?= lang('search'); ?>">
                                <div class="input-group-btn">
                                    <button type="submit" class="button-search btn btn-default btn-lg" aria-label="<?= lang('submit'); ?>">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <?= form_close(); ?>
                        </div>
                        <div class="header-mini col-sm-2 col-md-3 col-sx-6">
                            <div class="mini-cart">
                                <a role="button" class="cart-open-btn">
                                    <i class="fa fa-shopping-bag"></i>
	                                <span class="count cart-total-items"><?php echo $cart['total_items']; ?></span>
                                    <!-- /.count cart-total-items -->
                                </a>
                                <!-- /.cart-open-btn -->
	                            <div class="total-price-basket hidden-sm hidden-xs">
		                            <span class="lbl"><?= lang('shopping_cart') ?></span>
		                            <span class="value"><?php echo $cart['subtotal']; ?></span>
	                            </div>
                            </div>
                            <!-- /.mini-cart -->
                            <a href="#" role="button" class="icon-search hidden-lg hidden-md hidden-sm" aria-label="<?= lang( 'open-search-box' ); ?>">
                                <i class="fa fa-search"></i>
                            </a>
                            <!-- /.site-search -->
                        </div>
                        <div class="clearfix"></div>
                        <?php
                        if ( $categories ) {
                        $hotCategories = $categories;
                        shuffle( $hotCategories );
                        $hotCategories = array_slice( $hotCategories, 0, 5 );
                            if ( ! empty( $hotCategories ) ) {
                        ?>
	                    <div class="hot-words col-md-offset-0 col-sm-11 col-sm-offset-1 hidden-xs"><?php
		                    foreach ( $hotCategories as $category ) {
			                    printf( '<a href="%s">%s</a>', site_url( 'category/' . $category->slug ), $category->name );
		                    }
	                    ?></div>
	                    <?php
	                        }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.header-middle -->
</header>
<!-- /.cs-header -->
<?php if ( $shoBannerSection ) { ?>
    <div class="home-firstscreen">
        <div class="home-firstscreen-top-bar"></div>
        <div class="container">
            <div class="home-firstscreen-main">
                <div class="channel-entrance hidden-xs">
                    <a href="#">Flash Deals</a>
                    <a href="#">LIVE</a>
                    <a href="#">New User Zone</a>
                </div>
                <!-- /.channel-entrance -->
                <div class="categories hidden-xs">
                    <div class="categories-main showing">
                        <div class="categories-content-title" role="button" id="fs-categories-btn" data-toggle="dropdown" aria-controls="ht-currency-drop" aria-haspopup="true" aria-expanded="false" aria-label="<?= lang( 'categories' ); ?>">
                            <i class="fa fa-bars"></i>
                            <span class="text"><?= lang( 'categories' ); ?></span>
                        </div>
                        <!-- /.categories-content-title -->
                        <?php
                        $attributes = [ 'class' => 'categories-list-box dropdown-menu css-menu', 'id' => 'fs-categories-drop', 'aria-labelledby' => 'fs-categories-btn' ];
                        get_category_dropdown( $categories, $attributes, false );
                        ?>
                        <!-- /.categories-list-box -->
                    </div>
                    <!-- /.categories-main -->
                </div>
                <!-- /.categories -->
                <div class="row">
                    <div class="col-lg-9">
                        <div class="advertise-main <?= ( isset( $theme_options['slider-side'] ) && ! empty( $theme_options['slider-side'] ) ) ? '' : 'no-sidebar'; ?>">
                            <div class="key-visual-main ui-banner-slider">
                                <?php
                                foreach( $home_slider as $slide ) {
                                    if( empty( $slide->image ) ) {
                                        continue;
                                    }
                                    $link = ! empty ( $slide->link ) ? $slide->link : '#';
                                    $title = ! empty ( $slide->title ) ? $slide->title : '';
                                    ?>
                                    <div class="item">
                                        <a href="<?php echo $link; ?>">
                                            <img src="<?php echo base_url('assets/uploads/' . $slide->image) ?>"  alt="<?php echo $title; ?>" />
                                        </a>
                                        <?php if ( ! empty( $slide->title ) ) { ?>
                                            <div class="ui-banner-description">
                                                <h2><?php echo $title; ?></h2>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
	                        <?php /**
                            <div class="crowd-entrance hidden-sm hidden-xs">
                                <ul class="product-list products">
                                    <?php for( $i = 0; $i < 5; $i++ ) { ?>
                                        <li class="product-item product">
                                            <a href="#">
                                                <div class="crowd-container">
                                                    <div class="crowd-img">
                                                        <img src="http://ae01.alicdn.com/kf/H8090f04f6ce7462dac67e3344cdcb0b06.jpg_140x140.jpg_.webp">
                                                    </div>
                                                    <!-- /.crowd-img -->
                                                    <div class="crowd-note">
                                                        <div class="crowd-price">$222</div>
                                                        <!-- /.crowd-price -->
                                                    </div>
                                                    <!-- /.crowd-note -->
                                                    <div class="crowd-title">text</div>
                                                    <!-- /.crowd-title -->
                                                </div>
                                                <!-- /.crowd-container -->
                                            </a>
                                        </li>
                                        <!-- /.product-list product -->
                                    <?php } ?>
                                </ul>
                                <!-- /.product-list products -->
                            </div>
                            <!-- /.crowd-entrance -->
	                        */ ?>
                        </div>
                        <!-- /.advertise-main -->
                        <?php if ( isset( $theme_options['main_banner']['banner']['content'] ) ) { ?>
                    </div>
                    <div class="col-lg-3">
                        <div class="user-benefits"><?php echo html_entity_decode( $theme_options['main_banner']['banner']['content'] ); ?></div>
                    </div>
                    <!-- /.user-benefits -->
                    <?php } ?>
                </div>
            </div>
            <!-- /.home-firstscreen-main -->
        </div>
    </div>
    <!-- /.home-firstscreen -->
    <div class="clearfix"></div>
<?php } ?>
<!-- ============================================== HEADER : END ============================================== -->
