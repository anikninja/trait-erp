<?php defined('BASEPATH') or exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <base href="<?= site_url() ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= $Settings->site_name ?></title>
<!--    <link href="https://fonts.googleapis.com/css?family=Titillium+Web:300,700&display=swap" rel="stylesheet">-->
    <link rel="shortcut icon" href="<?= $admin_icon; ?>"/>
    <link href="<?= $assets ?>styles/themify-icons.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/LineIcons.min.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/theme.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-migrate-1.2.1.min.js"></script>
    <!--[if lt IE 9]>
    <script src="<?= $assets ?>js/jquery.js"></script>
    <![endif]-->
    <noscript><style type="text/css">#loading { display: none; }</style></noscript>
	<?php if ($Settings->user_rtl) {
		?>
        <link href="<?= $assets ?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>
        <link href="<?= $assets ?>styles/style-rtl.css" rel="stylesheet"/>
        <script type="text/javascript">
            $(document).ready(function () { $('.pull-right, .pull-left').addClass('flip'); });
        </script>
		<?php
	} ?>
    <script type="text/javascript">
        $(window).load(function () {
            $("#loading").fadeOut("slow");
        });
    </script>
</head>
<body>
<noscript>
    <div class="global-site-notice noscript">
        <div class="notice-inner">
            <p><strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in your browser to utilize the functionality of this website.</p>
        </div>
    </div>
</noscript>
<div id="loading"></div>
<div id="app_wrapper">
    <div class="container" id="container">
        <div class="row" id="main-con">
            <table class="lt">
	            <tr>
		            <td class="sidebar-con">
                        <div id="sidebar-left">
                            <div class="sidebar-nav nav-collapse collapse navbar-collapse" id="sidebar_menu">
                                <ul class="nav main-menu">
                                    <li class="brand-logo">
                                        <a class="brand-logo-link" href="<?= admin_url() ?>">
	                                        <span class="brand-logo-span"><img src="<?= $admin_logo ?>"></span>
                                        </a>
                                    </li>
                                    <li class="mm_welcome">
                                        <a href="<?= admin_url() ?>">
                                            <i class="lni-dashboard"></i>
                                            <span class="text"> <?= lang('dashboard'); ?></span>
                                        </a>
                                    </li>
									<?php if ( $Owner || $Admin ) { ?>
                                    <li class="mm_products">
                                        <a class="dropmenu" href="#">
                                            <i class="lni-package"></i>
                                            <span class="text"> <?= lang('products' ); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                        <ul>
                                            <li id="products_index">
                                                <a class="submenu" href="<?= admin_url('products' ); ?>">
                                                    <span class="text"> <?= lang('list_products'); ?></span>
                                                </a>
                                            </li>
                                            <li id="products_add">
                                                <a class="submenu" href="<?= admin_url('products/add'); ?>">
                                                    <span class="text"> <?= lang('add_product'); ?></span>
                                                </a>
                                            </li>
                                            <li id="products_import_csv">
                                                <a class="submenu" href="<?= admin_url('products/import_csv'); ?>">
                                                    <span class="text"> <?= lang('import_products'); ?></span>
                                                </a>
                                            </li>
                                            <li id="images_import_csv">
                                                <a class="submenu" href="<?= admin_url('products/import_image_csv'); ?>">
                                                    <span class="text"> <?= lang('import_product_images'); ?></span>
                                                </a>
                                            </li>
                                            <li id="products_print_barcodes">
                                                <a class="submenu" href="<?= admin_url('products/print_barcodes'); ?>">
                                                    <span class="text"> <?= lang('print_barcode_label'); ?></span>
                                                </a>
                                            </li>
                                            <li id="products_quantity_adjustments">
                                                <a class="submenu" href="<?= admin_url('products/quantity_adjustments'); ?>">
                                                    <span class="text"> <?= lang('quantity_adjustments'); ?></span>
                                                </a>
                                            </li>
                                            <li id="products_add_adjustment">
                                                <a class="submenu" href="<?= admin_url('products/add_adjustment'); ?>">
                                                    <span class="text"> <?= lang('add_adjustment'); ?></span>
                                                </a>
                                            </li>
                                            <li id="products_stock_counts">
                                                <a class="submenu" href="<?= admin_url('products/stock_counts'); ?>">
                                                    <span class="text"> <?= lang('stock_counts'); ?></span>
                                                </a>
                                            </li>
                                            <li id="products_count_stock">
                                                <a class="submenu" href="<?= admin_url('products/count_stock'); ?>">
                                                    <span class="text"> <?= lang('count_stock'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="mm_sales <?= strtolower($this->router->fetch_method()) == 'sales' ? 'mm_pos' : '' ?>">
                                        <a class="dropmenu" href="#">
                                            <i class="lni-investment"></i>
                                            <span class="text"> <?= lang('sales'); ?></span>
                                            <span class="chevron closed"></span>
                                        </a>
                                        <ul>
                                            <li id="sales_index">
                                                <a class="submenu" href="<?= admin_url('sales'); ?>">
                                                    <span class="text"> <?= lang('list_sales'); ?></span>
                                                </a>
                                            </li>
											<?php if (POS) {
												?>
                                                <li id="pos_sales">
                                                    <a class="submenu" href="<?= admin_url('pos/sales'); ?>">
                                                        <span class="text"> <?= lang('pos_sales'); ?></span>
                                                    </a>
                                                </li>
												<?php
											} ?>
                                            <li id="sales_add">
                                                <a class="submenu" href="<?= admin_url('sales/add'); ?>">
                                                    <span class="text"> <?= lang('add_sale'); ?></span>
                                                </a>
                                            </li>
                                            <li id="sales_sale_by_csv">
                                                <a class="submenu" href="<?= admin_url('sales/sale_by_csv'); ?>">
                                                    <span class="text"> <?= lang('add_sale_by_csv'); ?></span>
                                                </a>
                                            </li>
                                            <li id="sales_deliveries">
                                                <a class="submenu" href="<?= admin_url('sales/deliveries'); ?>">
                                                    <span class="text"> <?= lang('deliveries'); ?></span>
                                                </a>
                                            </li>
                                            <li id="sales_gift_cards">
                                                <a class="submenu" href="<?= admin_url('sales/gift_cards'); ?>">
                                                    <span class="text"> <?= lang('list_gift_cards'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="mm_commission">
                                        <a class="dropmenu" href="#">
                                            <i class="lni-offer"></i>
                                            <span class="text"> <?= lang('commission'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                        <ul>
                                            <li id="commission_groups">
                                                <a class="submenu" href="<?= admin_url('commission/groups'); ?>">
                                                    <span class="text"> <?= lang('commission_groups'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="mm_referral">
										<a href="<?= admin_url('referral'); ?>">
											<i class="lni-money-protection"></i>
											<span class="text"> <?= lang('referral'); ?></span>
										</a>
									</li>
									
									<li class="mm_wallet">
                                        <a class="dropmenu" href="#">
                                            <i class="lni-wallet"></i>
                                            <span class="text"> <?= lang('wallet'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                        <ul>
                                            <li id="wallet_index">
                                                <a class="submenu" href="<?= admin_url('wallet'); ?>">
                                                    <span class="text"> <?= lang('my_wallet'); ?></span>
                                                </a>
                                            </li>
                                            <li id="wallet_list">
                                                <a class="submenu" href="<?= admin_url('wallet/list'); ?>">
                                                    <span class="text"> <?= lang('users_wallet_list'); ?></span>
                                                </a>
                                            </li>
                                            <li id="wallet_withdrawal">
                                                <a class="submenu" href="<?= admin_url('wallet/withdrawal'); ?>">
                                                    <span class="text"> <?= lang('withdrawal_request'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="mm_coupons">
                                        <a class="dropmenu" href="#">
                                            <i class="lni lni-offer"></i>
                                            <span class="text"> <?= lang('coupons'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                        <ul>
                                            <!--<li id="coupons_index">
                                                <a class="submenu" href="<?/*= admin_url('coupons'); */?>">
                                                    <span class="text"> <?/*= lang('coupons_dashboard'); */?></span>
                                                </a>
                                            </li>-->
                                            <li id="coupons_list">
                                                <a class="submenu" href="<?= admin_url('coupons/list'); ?>">
                                                    <span class="text"> <?= lang('coupons_list'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="mm_delivery">
                                            <a class="dropmenu" href="#">
                                                <i class="lni-delivery"></i>
                                                <span class="text"> <?= lang('delivery'); ?> </span>
                                                <span class="chevron closed"></span>
                                            </a>
                                            <ul>
                                                <li id="delivery_index">
                                                    <a class="submenu" href="<?= admin_url('delivery'); ?>">
                                                        <span class="text"> <?= lang('delivery_dashboard'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="delivery_delivery_list">
                                                    <a class="submenu" href="<?= admin_url('delivery/delivery_list'); ?>">
                                                        <span class="text"> <?= lang('delivery_list'); ?></span>
                                                    </a>
                                                </li>
                                                <!--<li id="delivery_add_delivery">
                                                    <a class="submenu" href="<?/*= admin_url('delivery/add_delivery'); */?>">
                                                        <span class="text"> <?/*= lang('add_delivery'); */?></span>
                                                    </a>
                                                </li>-->
                                                <li id="delivery_shipment_list">
                                                    <a class="submenu" href="<?= admin_url('delivery/shipment_list'); ?>">
                                                        <span class="text"> <?= lang('shipment_list'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="delivery_pickup_list">
                                                    <a class="submenu" href="<?= admin_url('delivery/pickup_list'); ?>">
                                                        <span class="text"> <?= lang('pickup_list'); ?></span>
                                                    </a>
                                                </li>
                                                <!--<li id="delivery_add_shipment">
                                                    <a class="submenu" href="<?/*= admin_url('delivery/add_shipment'); */?>" data-toggle="modal" data-target="#myModal">
                                                        <span class="text"> <?/*= lang('add_shipment'); */?></span>
                                                    </a>
                                                </li>-->
                                                <li id="delivery_packaging_list">
                                                    <a class="submenu" href="<?= admin_url('delivery/packaging_list'); ?>">
                                                        <span class="text"> <?= lang('packaging_list'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="delivery_delayed_delivery">
                                                    <a class="submenu" href="<?= admin_url('delivery_delayed_delivery'); ?>">
                                                        <span class="text"> <?= lang('delayed_delivery'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="delivery_sales_not_delivered">
                                                    <a class="submenu" href="<?= admin_url('#'); ?>">
                                                        <span class="text"> <?= lang('sales_not_delivered'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="delivery_schedules_index">
                                                    <a href="<?= admin_url('delivery_schedules'); ?>">
                                                        <span class="text"> <?= lang('delivery_schedules'); ?></span>
                                                    </a>
                                                </li>
                                            </ul>
                                    </li>
                                    <li class="mm_warehouse">
                                            <a class="dropmenu" href="#">
                                                <i class="lni lni-dropbox-original"></i>
                                                <span class="text"> <?= lang('warehouse'); ?> </span>
                                                <span class="chevron closed"></span>
                                            </a>
                                            <ul>
                                                <li id="warehouse_list">
                                                    <a class="submenu" href="<?= admin_url('warehouse/list'); ?>">
                                                        <span class="text"> <?= lang('warehouse_list'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="warehouse_stock">
                                                    <a class="submenu" href="<?= admin_url('warehouse/stock'); ?>">
                                                        <span class="text"> <?= lang('product_stock'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="warehouse_stock_adjustments">
                                                    <a class="submenu" href="<?= admin_url('warehouse/stock_adjustments'); ?>">
                                                        <span class="text"> <?= lang('stock_adjustments'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="warehouse_stock_counts">
                                                    <a class="submenu" href="<?= admin_url('warehouse/stock_counts'); ?>">
                                                        <span class="text"> <?= lang('stock_counts'); ?></span>
                                                    </a>
                                                </li>
                                            </ul>
                                    </li>
                                    <li class="mm_quotes">
                                        <a class="dropmenu" href="#">
                                            <i class="lni-heart"></i>
                                            <span class="text"> <?= lang('quotes'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                        <ul>
                                            <li id="quotes_index">
                                                <a class="submenu" href="<?= admin_url('quotes'); ?>">
                                                    <span class="text"> <?= lang('list_quotes'); ?></span>
                                                </a>
                                            </li>
                                            <li id="quotes_add">
                                                <a class="submenu" href="<?= admin_url('quotes/add'); ?>">
                                                    <span class="text"> <?= lang('add_quote'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="mm_purchases">
                                        <a class="dropmenu" href="#">
                                            <i class="lni-plus"></i>
                                            <span class="text"> <?= lang('purchases'); ?></span>
                                            <span class="chevron closed"></span>
                                        </a>
                                        <ul>
                                            <li id="purchases_index">
                                                <a class="submenu" href="<?= admin_url('purchases'); ?>">
                                                    <span class="text"> <?= lang('list_purchases'); ?></span>
                                                </a>
                                            </li>
                                            <li id="purchases_add">
                                                <a class="submenu" href="<?= admin_url('purchases/add'); ?>">
                                                    <span class="text"> <?= lang('add_purchase'); ?></span>
                                                </a>
                                            </li>
                                            <li id="purchases_purchase_by_csv">
                                                <a class="submenu" href="<?= admin_url('purchases/purchase_by_csv'); ?>">
                                                    <span class="text"> <?= lang('add_purchase_by_csv'); ?></span>
                                                </a>
                                            </li>
                                            <li id="purchases_expenses">
                                                <a class="submenu" href="<?= admin_url('purchases/expenses'); ?>">
                                                    <span class="text"> <?= lang('list_expenses'); ?></span>
                                                </a>
                                            </li>
                                            <li id="purchases_add_expense">
                                                <a class="submenu" href="<?= admin_url('purchases/add_expense'); ?>" data-toggle="modal" data-target="#myModal">
                                                    <span class="text"> <?= lang('add_expense'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="mm_transfers">
                                        <a class="dropmenu" href="#">
                                            <i class="lni-bi-cycle"></i>
                                            <span class="text"> <?= lang('transfers'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                        <ul>
                                            <li id="transfers_index">
                                                <a class="submenu" href="<?= admin_url('transfers'); ?>">
                                                    <span class="text"> <?= lang('list_transfers'); ?></span>
                                                </a>
                                            </li>
                                            <li id="transfers_add">
                                                <a class="submenu" href="<?= admin_url('transfers/add'); ?>">
                                                    <span class="text"> <?= lang('add_transfer'); ?></span>
                                                </a>
                                            </li>
                                            <li id="transfers_purchase_by_csv">
                                                <a class="submenu" href="<?= admin_url('transfers/transfer_by_csv'); ?>">
                                                    <span class="text"> <?= lang('add_transfer_by_csv'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="mm_returns">
                                        <a class="dropmenu" href="#">
                                            <i class="lni-caravan"></i>
                                            <span class="text"> <?= lang('returns'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                        <ul>
                                            <li id="returns_index">
                                                <a class="submenu" href="<?= admin_url('returns'); ?>">
                                                    <span class="text"> <?= lang('list_returns'); ?></span>
                                                </a>
                                            </li>
                                            <li id="returns_add">
                                                <a class="submenu" href="<?= admin_url('returns/add'); ?>">
                                                    <span class="text"> <?= lang('add_return'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="mm_auth mm_customers mm_suppliers mm_billers">
                                        <a class="dropmenu" href="#">
                                            <i class="lni-users"></i>
                                            <span class="text"> <?= lang('people'); ?> </span>
                                            <span class="chevron closed"></span>
                                        </a>
                                        <ul>
											<?php if ($Owner) { ?>
                                            <li id="auth_users">
                                                <a class="submenu" href="<?= admin_url('users'); ?>">
                                                    <span class="text"> <?= lang('list_users'); ?></span>
                                                </a>
                                            </li>
                                            <li id="auth_create_user">
                                                <a class="submenu" href="<?= admin_url('users/create_user'); ?>">
                                                    <span class="text"> <?= lang('new_user'); ?></span>
                                                </a>
                                            </li>
                                            <li id="billers_index">
                                                <a class="submenu" href="<?= admin_url('billers'); ?>">
                                                    <span class="text"> <?= lang('list_billers'); ?></span>
                                                </a>
                                            </li>
                                            <li id="billers_index">
                                                <a class="submenu" href="<?= admin_url('billers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                                    <span class="text"> <?= lang('add_biller'); ?></span>
                                                </a>
                                            </li>
											<?php } ?>
                                            <li id="customers_index">
                                                <a class="submenu" href="<?= admin_url('customers'); ?>">
                                                    <span class="text"> <?= lang('list_customers'); ?></span>
                                                </a>
                                            </li>
                                           <?php
                                           /*
                                            <li id="customers_index">
                                                <a class="submenu" href="<?= admin_url('customers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                                    <span class="text"> <?= lang('add_customer'); ?></span>
                                                </a>
                                            </li>
                                           */
                                           ?>
                                            <li id="customers_index">
                                                <a class="submenu" href="<?= admin_url('customers/add_manually'); ?>" data-toggle="modal" data-target="#myModal">
                                                    <span class="text"> <?= lang('add_customer_manually'); ?></span>
                                                </a>
                                            </li>
                                            <li id="suppliers_index">
                                                <a class="submenu" href="<?= admin_url('suppliers'); ?>">
                                                    <span class="text"> <?= lang('list_suppliers'); ?></span>
                                                </a>
                                            </li>
                                            <li id="suppliers_index">
                                                <a class="submenu" href="<?= admin_url('suppliers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                                    <span class="text"> <?= lang('add_supplier'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="mm_notifications">
                                        <a class="submenu" href="<?= admin_url('notifications'); ?>">
                                            <i class="lni-alarm"></i>
                                            <span class="text"> <?= lang('notifications'); ?></span>
                                        </a>
                                    </li>
                                    <li class="mm_calendar">
                                            <a class="submenu" href="<?= admin_url('calendar'); ?>">
                                                <i class="lni-calendar"></i>
                                                <span class="text"> <?= lang('calendar'); ?></span>
                                            </a>
                                        </li>
										<?php if ( $Owner ) {?>
                                    <li class="mm_system_settings <?= strtolower($this->router->fetch_method()) == 'sales' ? '' : 'mm_pos' ?>">
                                                <a class="dropmenu" href="#">
                                                    <i class="lni-cog"></i>
                                                    <span class="text"> <?= lang('settings'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="system_settings_index">
                                                        <a href="<?= admin_url('system_settings') ?>">
                                                            <span class="text"> <?= lang('system_settings'); ?></span>
                                                        </a>
                                                    </li>
													<?php if (POS) {
														?>
                                                        <li id="pos_settings">
                                                            <a href="<?= admin_url('pos/settings') ?>">
                                                                <span class="text"> <?= lang('pos_settings'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="promos_index">
                                                            <a href="<?= admin_url('promos') ?>">
                                                                <span class="text"> <?= lang('promos'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="pos_printers">
                                                            <a href="<?= admin_url('pos/printers') ?>">
                                                                <span class="text"> <?= lang('list_printers'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="pos_add_printer">
                                                            <a href="<?= admin_url('pos/add_printer') ?>">
                                                                <span class="text"> <?= lang('add_printer'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
                                                    <li id="system_settings_change_logo">
                                                        <a href="<?= admin_url('system_settings/change_logo') ?>" data-toggle="modal" data-target="#myModal">
                                                            <span class="text"> <?= lang('change_logo'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_currencies">
                                                        <a href="<?= admin_url('system_settings/currencies') ?>">
                                                            <span class="text"> <?= lang('currencies'); ?></span>
                                                        </a>
                                                    </li>
	                                                <li id="system_settings_shipping_zones">
		                                                <a href="<?= admin_url('system_settings/shipping_zones') ?>">
			                                                <span class="text"> <?= lang('shipping_zones'); ?></span>
		                                                </a>
	                                                </li>
                                                    <li id="system_settings_customer_groups">
                                                        <a href="<?= admin_url('system_settings/customer_groups') ?>">
                                                            <span class="text"> <?= lang('customer_groups'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_price_groups">
                                                        <a href="<?= admin_url('system_settings/price_groups') ?>">
                                                            <span class="text"> <?= lang('price_groups'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_categories">
                                                        <a href="<?= admin_url('system_settings/categories') ?>">
                                                            <span class="text"> <?= lang('categories'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_expense_categories">
                                                        <a href="<?= admin_url('system_settings/expense_categories') ?>">
                                                            <span class="text"> <?= lang('expense_categories'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_units">
                                                        <a href="<?= admin_url('system_settings/units') ?>">
                                                            <span class="text"> <?= lang('units'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_brands">
                                                        <a href="<?= admin_url('system_settings/brands') ?>">
                                                            <span class="text"> <?= lang('brands'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_variants">
                                                        <a href="<?= admin_url('system_settings/variants') ?>">
                                                            <span class="text"> <?= lang('variants'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_tax_rates">
                                                        <a href="<?= admin_url('system_settings/tax_rates') ?>">
                                                            <span class="text"> <?= lang('tax_rates'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_warehouses">
                                                        <a href="<?= admin_url('system_settings/warehouses') ?>">
                                                            <span class="text"> <?= lang('warehouses'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_email_templates">
                                                        <a href="<?= admin_url('system_settings/email_templates') ?>">
                                                            <span class="text"> <?= lang('email_templates'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_user_groups">
                                                        <a href="<?= admin_url('system_settings/user_groups') ?>">
                                                            <span class="text"> <?= lang('group_permissions'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_backups">
                                                        <a href="<?= admin_url('system_settings/backups') ?>">
                                                            <span class="text"> <?= lang('backups'); ?></span>
                                                        </a>
                                                    </li>
                                                    <!-- <li id="system_settings_updates">
                                            <a href="<?= admin_url('system_settings/updates') ?>">
                                                <i class="fa fa-upload"></i><span class="text"> <?= lang('updates'); ?></span>
                                            </a>
                                        </li> -->
                                                </ul>
                                            </li>
										<?php } ?>
                                    <li class="mm_reports">
                                            <a class="dropmenu" href="#">
                                                <i class="lni-bar-chart"></i>
                                                <span class="text"> <?= lang('reports'); ?> </span>
                                                <span class="chevron closed"></span>
                                            </a>
                                            <ul>
                                                <li id="reports_index">
                                                    <a href="<?= admin_url('reports') ?>">
                                                        <span class="text"> <?= lang('overview_chart'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_warehouse_stock">
                                                    <a href="<?= admin_url('reports/warehouse_stock') ?>">
                                                        <span class="text"> <?= lang('warehouse_stock'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_best_sellers">
                                                    <a href="<?= admin_url('reports/best_sellers') ?>">
                                                        <span class="text"> <?= lang('best_sellers'); ?></span>
                                                    </a>
                                                </li>
												<?php if (POS) {
													?>
                                                    <li id="reports_register">
                                                        <a href="<?= admin_url('reports/register') ?>">
                                                            <span class="text"> <?= lang('register_report'); ?></span>
                                                        </a>
                                                    </li>
													<?php
												} ?>
                                                <li id="reports_quantity_alerts">
                                                    <a href="<?= admin_url('reports/quantity_alerts') ?>">
                                                        <span class="text"> <?= lang('product_quantity_alerts'); ?></span>
                                                    </a>
                                                </li>
												<?php if ($Settings->product_expiry) {
													?>
                                                    <li id="reports_expiry_alerts">
                                                        <a href="<?= admin_url('reports/expiry_alerts') ?>">
                                                            <span class="text"> <?= lang('product_expiry_alerts'); ?></span>
                                                        </a>
                                                    </li>
													<?php
												} ?>
                                                <li id="reports_products">
                                                    <a href="<?= admin_url('reports/products') ?>">
                                                        <span class="text"> <?= lang('products_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_adjustments">
                                                    <a href="<?= admin_url('reports/adjustments') ?>">
                                                        <span class="text"> <?= lang('adjustments_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_categories">
                                                    <a href="<?= admin_url('reports/categories') ?>">
                                                        <span class="text"> <?= lang('categories_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_brands">
                                                    <a href="<?= admin_url('reports/brands') ?>">
                                                        <span class="text"> <?= lang('brands_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_daily_sales">
                                                    <a href="<?= admin_url('reports/daily_sales') ?>">
                                                        <span class="text"> <?= lang('daily_sales'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_monthly_sales">
                                                    <a href="<?= admin_url('reports/monthly_sales') ?>">
                                                        <span class="text"> <?= lang('monthly_sales'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_sales">
                                                    <a href="<?= admin_url('reports/sales') ?>">
                                                        <span class="text"> <?= lang('sales_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_payments">
                                                    <a href="<?= admin_url('reports/payments') ?>">
                                                        <span class="text"> <?= lang('payments_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_tax">
                                                    <a href="<?= admin_url('reports/tax') ?>">
                                                        <span class="text"> <?= lang('tax_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_profit_loss">
                                                    <a href="<?= admin_url('reports/profit_loss') ?>">
                                                        <span class="text"> <?= lang('profit_and_loss'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_daily_purchases">
                                                    <a href="<?= admin_url('reports/daily_purchases') ?>">
                                                        <span class="text"> <?= lang('daily_purchases'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_monthly_purchases">
                                                    <a href="<?= admin_url('reports/monthly_purchases') ?>">
                                                        <span class="text"> <?= lang('monthly_purchases'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_purchases">
                                                    <a href="<?= admin_url('reports/purchases') ?>">
                                                        <span class="text"> <?= lang('purchases_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_expenses">
                                                    <a href="<?= admin_url('reports/expenses') ?>">
                                                        <span class="text"> <?= lang('expenses_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_customer_report">
                                                    <a href="<?= admin_url('reports/customers') ?>">
                                                        <span class="text"> <?= lang('customers_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_supplier_report">
                                                    <a href="<?= admin_url('reports/suppliers') ?>">
                                                        <span class="text"> <?= lang('suppliers_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_staff_report">
                                                    <a href="<?= admin_url('reports/users') ?>">
                                                        <span class="text"> <?= lang('staff_report'); ?></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
										<?php if ( $Owner && file_exists( APPPATH . 'controllers' . DIRECTORY_SEPARATOR . 'shop' . DIRECTORY_SEPARATOR . 'Shop.php' ) ) { ?>
                                    <li class="mm_shop_settings mm_api_settings">
                                                <a class="dropmenu" href="#">
                                                    <i class="lni-cart-full"></i><span class="text"> <?= lang('front_end'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="shop_settings_index">
                                                        <a href="<?= admin_url('shop_settings') ?>">
                                                            <span class="text"> <?= lang('shop_settings'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="shop_settings_slider">
                                                        <a href="<?= admin_url('shop_settings/slider') ?>">
                                                            <span class="text"> <?= lang('slider_settings'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="shop_settings_menus">
                                                        <a href="<?= admin_url('shop_settings/menus') ?>">
                                                            <span class="text"> <?= lang('menu_settings'); ?></span>
                                                        </a>
                                                    </li>
	                                                <li id="shop_settings_theme_settings">
		                                                <a href="<?= admin_url('shop_settings/theme_settings') ?>">
			                                                <span class="text"> <?= lang('theme_settings'); ?></span>
		                                                </a>
	                                                </li>
	                                                <li id="shop_settings_color_mappings">
		                                                <a href="<?= admin_url('shop_settings/color_mappings') ?>">
			                                                <span class="text"> <?= lang('color_mappings'); ?></span>
		                                                </a>
	                                                </li>
													<?php if ($Settings->apis) {
														?>
                                                        <li id="api_settings_index">
                                                            <a href="<?= admin_url('api_settings') ?>">
                                                                <span class="text"> <?= lang('api_keys'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
                                                    <li id="shop_settings_pages">
                                                        <a href="<?= admin_url('shop_settings/pages') ?>">
                                                            <span class="text"> <?= lang('list_pages'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="shop_settings_pages">
                                                        <a href="<?= admin_url('shop_settings/add_page') ?>">
                                                            <span class="text"> <?= lang('add_page'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="shop_settings_sms_settings">
                                                        <a href="<?= admin_url('shop_settings/sms_settings') ?>">
                                                            <span class="text"> <?= lang('sms_settings'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="shop_settings_send_sms">
                                                        <a href="<?= admin_url('shop_settings/send_sms') ?>">
                                                            <span class="text"> <?= lang('send_sms'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="shop_settings_sms_log">
                                                        <a href="<?= admin_url('shop_settings/sms_log') ?>">
                                                            <span class="text"> <?= lang('sms_log'); ?></span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
										<?php } ?>
									<?php } else { // not owner and not admin ?>
										<?php if ( $GP['products-index'] || $GP['products-add'] || $GP['products-barcode'] || $GP['products-adjustments'] || $GP['products-stock_count'] ) { ?>
                                    <li class="mm_products">
                                                <a class="dropmenu" href="#">
                                                    <i class="lni-package"></i>
                                                    <span class="text"> <?= lang('products'); ?></span>
	                                                <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="products_index">
                                                        <a class="submenu" href="<?= admin_url('products'); ?>">
                                                            <span class="text"> <?= lang('list_products'); ?></span>
                                                        </a>
                                                    </li>
													<?php if ($GP['products-add']) {
														?>
                                                        <li id="products_add">
                                                            <a class="submenu" href="<?= admin_url('products/add'); ?>">
                                                                <span class="text"> <?= lang('add_product'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
													<?php if ($GP['products-barcode']) {
														?>
                                                        <li id="products_sheet">
                                                            <a class="submenu" href="<?= admin_url('products/print_barcodes'); ?>">
                                                                <span class="text"> <?= lang('print_barcode_label'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
													<?php if ($GP['products-adjustments']) {
														?>
                                                        <li id="products_quantity_adjustments">
                                                            <a class="submenu" href="<?= admin_url('products/quantity_adjustments'); ?>">
                                                                <span class="text"> <?= lang('quantity_adjustments'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="products_add_adjustment">
                                                            <a class="submenu" href="<?= admin_url('products/add_adjustment'); ?>">
                                                                <span class="text"> <?= lang('add_adjustment'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
													<?php if ($GP['products-stock_count']) {
														?>
                                                        <li id="products_stock_counts">
                                                            <a class="submenu" href="<?= admin_url('products/stock_counts'); ?>">
                                                                <span class="text"> <?= lang('stock_counts'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="products_count_stock">
                                                            <a class="submenu" href="<?= admin_url('products/count_stock'); ?>">
                                                                <span class="text"> <?= lang('count_stock'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
                                                </ul>
                                            </li>
										<?php } ?>
										<?php if ( $GP['sales-index'] || $GP['sales-add'] || $GP['sales-deliveries'] || $GP['sales-gift_cards'] ) { ?>
                                    <li class="mm_sales <?= strtolower($this->router->fetch_method()) == 'sales' ? 'mm_pos' : '' ?>">
                                                <a class="dropmenu" href="#">
                                                    <i class="lni-investment"></i>
                                                    <span class="text"> <?= lang('sales'); ?></span>
	                                                <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="sales_index">
                                                        <a class="submenu" href="<?= admin_url('sales'); ?>">
                                                            <span class="text"> <?= lang('list_sales'); ?></span>
                                                        </a>
                                                    </li>
													<?php if (POS && $GP['pos-index']) {
														?>
                                                        <li id="pos_sales">
                                                            <a class="submenu" href="<?= admin_url('pos/sales'); ?>">
                                                                <span class="text"> <?= lang('pos_sales'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
													<?php if ($GP['sales-add']) {
														?>
                                                        <li id="sales_add">
                                                            <a class="submenu" href="<?= admin_url('sales/add'); ?>">
                                                                <span class="text"> <?= lang('add_sale'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['sales-deliveries']) {
														?>
                                                        <li id="sales_deliveries">
                                                            <a class="submenu" href="<?= admin_url('sales/deliveries'); ?>">
                                                                <span class="text"> <?= lang('deliveries'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['sales-gift_cards']) {
														?>
                                                        <li id="sales_gift_cards">
                                                            <a class="submenu" href="<?= admin_url('sales/gift_cards'); ?>">
                                                                <span class="text"> <?= lang('gift_cards'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
                                                </ul>
                                            </li>
										<?php } ?>
										<?php if ( $GP['quotes-index'] || $GP['quotes-add'] ) {?>
                                    <li class="mm_quotes">
                                                <a class="dropmenu" href="#">
                                                    <i class="lni-heart"></i>
                                                    <span class="text"> <?= lang('quotes'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="sales_index">
                                                        <a class="submenu" href="<?= admin_url('quotes'); ?>">
                                                            <span class="text"> <?= lang('list_quotes'); ?></span>
                                                        </a>
                                                    </li>
													<?php if ($GP['quotes-add']) {
														?>
                                                        <li id="sales_add">
                                                            <a class="submenu" href="<?= admin_url('quotes/add'); ?>">
                                                                <span class="text"> <?= lang('add_quote'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
                                                </ul>
                                            </li>
										<?php } ?>
										<?php if ( $GP['purchases-index'] || $GP['purchases-add'] || $GP['purchases-expenses'] ) { ?>
                                    <li class="mm_purchases">
                                                <a class="dropmenu" href="#">
                                                    <i class="lni-plus"></i>
                                                    <span class="text"> <?= lang('purchases'); ?></span>
	                                                <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="purchases_index">
                                                        <a class="submenu" href="<?= admin_url('purchases'); ?>">
                                                            <span class="text"> <?= lang('list_purchases'); ?></span>
                                                        </a>
                                                    </li>
													<?php if ($GP['purchases-add']) {
														?>
                                                        <li id="purchases_add">
                                                            <a class="submenu" href="<?= admin_url('purchases/add'); ?>">
                                                                <span class="text"> <?= lang('add_purchase'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
													<?php if ($GP['purchases-expenses']) {
														?>
                                                        <li id="purchases_expenses">
                                                            <a class="submenu" href="<?= admin_url('purchases/expenses'); ?>">
                                                                <span class="text"> <?= lang('list_expenses'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="purchases_add_expense">
                                                            <a class="submenu" href="<?= admin_url('purchases/add_expense'); ?>"
                                                               data-toggle="modal" data-target="#myModal">
                                                                <span class="text"> <?= lang('add_expense'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
                                                </ul>
                                            </li>
										<?php } ?>
										<?php if ( $GP['transfers-index'] || $GP['transfers-add'] ) { ?>
                                    <li class="mm_transfers">
                                                <a class="dropmenu" href="#">
                                                    <i class="lni-bi-cycle"></i>
                                                    <span class="text"> <?= lang('transfers'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="transfers_index">
                                                        <a class="submenu" href="<?= admin_url('transfers'); ?>">
                                                            <span class="text"> <?= lang('list_transfers'); ?></span>
                                                        </a>
                                                    </li>
													<?php if ($GP['transfers-add']) {
														?>
                                                        <li id="transfers_add">
                                                            <a class="submenu" href="<?= admin_url('transfers/add'); ?>">
                                                                <span class="text"> <?= lang('add_transfer'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
                                                </ul>
                                            </li>
										<?php } ?>
										<?php if ( $GP['returns-index'] || $GP['returns-add'] ) { ?>
                                    <li class="mm_returns">
                                                <a class="dropmenu" href="#">
                                                    <i class="lni-caravan"></i>
                                                    <span class="text"> <?= lang('returns'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="returns_index">
                                                        <a class="submenu" href="<?= admin_url('returns'); ?>">
                                                            <span class="text"> <?= lang('list_returns'); ?></span>
                                                        </a>
                                                    </li>
													<?php if ($GP['returns-add']) {
														?>
                                                        <li id="returns_add">
                                                            <a class="submenu" href="<?= admin_url('returns/add'); ?>">
                                                                <span class="text"> <?= lang('add_return'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
                                                </ul>
                                            </li>
										<?php } ?>
										<?php if ( $GP['customers-index'] || $GP['customers-add'] || $GP['suppliers-index'] || $GP['suppliers-add'] ) { ?>
                                    <li class="mm_auth mm_customers mm_suppliers mm_billers">
                                                <a class="dropmenu" href="#">
                                                    <i class="lni-users"></i>
                                                    <span class="text"> <?= lang('people'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
													<?php if ($GP['customers-index']) {
														?>
                                                        <li id="customers_index">
                                                            <a class="submenu" href="<?= admin_url('customers'); ?>">
                                                                <span class="text"> <?= lang('list_customers'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['customers-add']) {
														?>
                                                        <li id="customers_index">
                                                            <a class="submenu" href="<?= admin_url('customers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                                                <span class="text"> <?= lang('add_customer'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['suppliers-index']) {
														?>
                                                        <li id="suppliers_index">
                                                            <a class="submenu" href="<?= admin_url('suppliers'); ?>">
                                                                <span class="text"> <?= lang('list_suppliers'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['suppliers-add']) {
														?>
                                                        <li id="suppliers_index">
                                                            <a class="submenu" href="<?= admin_url('suppliers/add'); ?>" data-toggle="modal" data-target="#myModal">
                                                                <span class="text"> <?= lang('add_supplier'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
                                                </ul>
                                            </li>
										<?php } ?>
										<?php if ( $GP['reports-quantity_alerts'] || $GP['reports-expiry_alerts'] || $GP['reports-products'] || $GP['reports-monthly_sales'] || $GP['reports-sales'] || $GP['reports-payments'] || $GP['reports-purchases'] || $GP['reports-customers'] || $GP['reports-suppliers'] || $GP['reports-expenses'] ) { ?>
                                    <li class="mm_reports">
                                                <a class="dropmenu" href="#">
                                                    <i class="lni-bar-chart"></i>
                                                    <span class="text"> <?= lang('reports'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
													<?php if ($GP['reports-quantity_alerts']) {
														?>
                                                        <li id="reports_quantity_alerts">
                                                            <a href="<?= admin_url('reports/quantity_alerts') ?>">
                                                                <span class="text"> <?= lang('product_quantity_alerts'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['reports-expiry_alerts']) {
														?>
														<?php if ($Settings->product_expiry) {
															?>
                                                            <li id="reports_expiry_alerts">
                                                                <a href="<?= admin_url('reports/expiry_alerts') ?>">
                                                                    <span class="text"> <?= lang('product_expiry_alerts'); ?></span>
                                                                </a>
                                                            </li>
															<?php
														} ?>
														<?php
													}
													if ($GP['reports-products']) {
														?>
                                                        <li id="reports_products">
                                                            <a href="<?= admin_url('reports/products') ?>">
                                                                <span class="text"> <?= lang('products_report'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="reports_adjustments">
                                                            <a href="<?= admin_url('reports/adjustments') ?>">
                                                                <span class="text"> <?= lang('adjustments_report'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="reports_categories">
                                                            <a href="<?= admin_url('reports/categories') ?>">
                                                                <i class="fa fa-folder-open"></i><span class="text"> <?= lang('categories_report'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="reports_brands">
                                                            <a href="<?= admin_url('reports/brands') ?>">
                                                                <span class="text"> <?= lang('brands_report'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['reports-daily_sales']) {
														?>
                                                        <li id="reports_daily_sales">
                                                            <a href="<?= admin_url('reports/daily_sales') ?>">
                                                                <span class="text"> <?= lang('daily_sales'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['reports-monthly_sales']) {
														?>
                                                        <li id="reports_monthly_sales">
                                                            <a href="<?= admin_url('reports/monthly_sales') ?>">
                                                                <span class="text"> <?= lang('monthly_sales'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['reports-sales']) {
														?>
                                                        <li id="reports_sales">
                                                            <a href="<?= admin_url('reports/sales') ?>">
                                                                <span class="text"> <?= lang('sales_report'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['reports-payments']) {
														?>
                                                        <li id="reports_payments">
                                                            <a href="<?= admin_url('reports/payments') ?>">
                                                                <span class="text"> <?= lang('payments_report'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['reports-tax']) {
														?>
                                                        <li id="reports_tax">
                                                            <a href="<?= admin_url('reports/tax') ?>">
                                                                <span class="text"> <?= lang('tax_report'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['reports-daily_purchases']) {
														?>
                                                        <li id="reports_daily_purchases">
                                                            <a href="<?= admin_url('reports/daily_purchases') ?>">
                                                                <span class="text"> <?= lang('daily_purchases'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['reports-monthly_purchases']) {
														?>
                                                        <li id="reports_monthly_purchases">
                                                            <a href="<?= admin_url('reports/monthly_purchases') ?>">
                                                                <span class="text"> <?= lang('monthly_purchases'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['reports-purchases']) {
														?>
                                                        <li id="reports_purchases">
                                                            <a href="<?= admin_url('reports/purchases') ?>">
                                                                <span class="text"> <?= lang('purchases_report'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['reports-expenses']) {
														?>
                                                        <li id="reports_expenses">
                                                            <a href="<?= admin_url('reports/expenses') ?>">
                                                                <span class="text"> <?= lang('expenses_report'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['reports-customers']) {
														?>
                                                        <li id="reports_customer_report">
                                                            <a href="<?= admin_url('reports/customers') ?>">
                                                                <span class="text"> <?= lang('customers_report'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													}
													if ($GP['reports-suppliers']) {
														?>
                                                        <li id="reports_supplier_report">
                                                            <a href="<?= admin_url('reports/suppliers') ?>">
                                                                <span class="text"> <?= lang('suppliers_report'); ?></span>
                                                            </a>
                                                        </li>
														<?php
													} ?>
                                                </ul>
                                            </li>
										<?php } ?>
									<?php } ?>
                                </ul>
                            </div>
                        </div>
                    </td>
		            <td class="content-con">
                        <header id="header" class="navbar">
                            <div class="container">
                                <div class="btn-group visible-xs pull-right btn-visible-sm">
                                    <button class="navbar-toggle btn" type="button" data-toggle="collapse" data-target="#sidebar_menu">
                                        <span class="ti ti-layout-menu-v"></span>
                                    </button>
	                                <?php if ( SHOP ) { ?>
                                    <a href="<?= site_url('/') ?>" class="btn">
                                        <span class="lni-cart-full"></span>
                                    </a>
									<?php } ?>
                                    <a href="<?= admin_url('calendar') ?>" class="btn">
                                        <span class="lni-calendar"></span>
                                    </a>
                                    <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id')); ?>" class="btn">
                                        <span class="lni-user"></span>
                                    </a>
                                    <a href="<?= admin_url('logout'); ?>" class="btn">
                                        <span class="lni-lock"></span>
                                    </a>
                                </div>
                                <div class="header-nav">
                                    <ul class="nav navbar-nav pull-left">
	                                    <?php if ( SHOP ) { ?>
                                        <li class="dropdown hidden-xs"><a class="btn" title="<?= lang('shop') ?>" data-placement="bottom" href="<?= base_url() ?>"><i class="lni-cart-full"></i><?= lang('shop') ?></a></li>
										<?php } ?>
                                        <li class="dropdown hidden-xs">
                                            <a class="btn" title="<?= lang('calculator') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                                                <i class="lni-calculator"></i><?= lang('calculator') ?>
                                            </a>
                                            <ul class="dropdown-menu pull-right calc">
                                                <li class="dropdown-content">
                                                    <span id="inlineCalc" style="width: 100%"></span>
                                                </li>
                                            </ul>
                                        </li>
	                                    <?php if ( $info ) { ?>
                                        <li class="dropdown hidden-sm">
                                            <a class="btn" title="<?= lang('notifications') ?>" data-placement="bottom" href="#" data-toggle="dropdown">
                                                <i class="lni-alarm"></i><?= lang('notifications') ?>
                                                <span class="number black"><?= sizeof($info) ?></span>
                                            </a>
                                            <ul class="dropdown-menu pull-right content-scroll">
                                                <li class="dropdown-header"><i class="lni-alarm"></i> <?= lang('notifications'); ?></li>
                                                <li class="dropdown-content">
                                                    <div class="scroll-div">
                                                        <div class="top-menu-scroll">
                                                            <ol class="oe">
	                                                            <?php foreach ( $info as $n ) {
																	echo '<li>' . $n->comment . '</li>';
																} ?>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>
										<?php } ?>
										<?php /* if ($Owner && $Settings->update) { ?>
                    <li class="dropdown hidden-sm">
                        <a class="btn blightOrange tip" title="<?= lang('update_available') ?>"
                            data-placement="bottom" data-container="body" href="<?= admin_url('system_settings/updates') ?>">
                            <i class="fa fa-download"></i>
                        </a>
                    </li>
                        <?php } */ ?>
	                                    <?php if ( ( $Owner || $Admin || $GP['reports-quantity_alerts'] || $GP['reports-expiry_alerts'] ) && ( $qty_alert_num > 0 || $exp_alert_num > 0 || $shop_sale_alerts ) ) { ?>
                                        <li class="dropdown hidden-sm">
                                            <a class="btn" title="<?= lang('alerts') ?>"
                                               data-placement="left" data-toggle="dropdown" href="#">
                                                <i class="lni-alarm"></i><?= lang('alerts') ?>
                                                <span class="number bred black"><?= $qty_alert_num + (($Settings->product_expiry) ? $exp_alert_num : 0) + $shop_sale_alerts + $shop_payment_alerts; ?></span>
                                            </a>
                                            <ul class="dropdown-menu pull-right">
												<?php if ($qty_alert_num > 0) {
													?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/quantity_alerts') ?>" class="">
                                                            <span class="label label-danger pull-right" style="margin-top:3px;"><?= $qty_alert_num; ?></span>
                                                            <span style="padding-right: 35px;"><?= lang('quantity_alerts') ?></span>
                                                        </a>
                                                    </li>
													<?php
												} ?>
												<?php if ($Settings->product_expiry) {
													?>
                                                    <li>
                                                        <a href="<?= admin_url('reports/expiry_alerts') ?>" class="">
                                                            <span class="label label-danger pull-right" style="margin-top:3px;"><?= $exp_alert_num; ?></span>
                                                            <span style="padding-right: 35px;"><?= lang('expiry_alerts') ?></span>
                                                        </a>
                                                    </li>
													<?php
												} ?>
												<?php if ($shop_sale_alerts) {
													?>
                                                    <li>
                                                        <a href="<?= admin_url('sales?shop=yes&delivery=no') ?>" class="">
                                                            <span class="label label-danger pull-right" style="margin-top:3px;"><?= $shop_sale_alerts; ?></span>
                                                            <span style="padding-right: 35px;"><?= lang('sales_x_delivered') ?></span>
                                                        </a>
                                                    </li>
													<?php
												} ?>
												<?php if ($shop_payment_alerts) {
													?>
                                                    <li>
                                                        <a href="<?= admin_url('sales?shop=yes&attachment=yes') ?>" class="">
                                                            <span class="label label-danger pull-right" style="margin-top:3px;"><?= $shop_payment_alerts; ?></span>
                                                            <span style="padding-right: 35px;"><?= lang('manual_payments') ?></span>
                                                        </a>
                                                    </li>
													<?php
												} ?>
                                            </ul>
                                        </li>
										<?php } ?>
	                                    <?php if ( POS ) { ?>
                                        <li class="dropdown hidden-xs">
                                            <a class="btn" title="<?= lang('pos') ?>" data-placement="bottom" href="<?= admin_url('pos') ?>">
                                                <i class="lni-ticket-alt"></i><?= lang('pos') ?>
                                            </a>
                                        </li>
										<?php } ?>
	                                    <?php if ( $Owner ) { ?>
                                        <li class="dropdown">
                                            <a class="btn" id="today_profit" title="<span><?= lang('today_profit') ?></span>"
                                               data-placement="bottom" data-html="true" href="<?= admin_url('reports/profit') ?>"
                                               data-toggle="modal" data-target="#myModal">
                                                <i class="lni-pulse"></i><?= lang('today_profit') ?>
                                            </a>
                                        </li>
										<?php } ?>
	                                    <?php if ( $Owner || $Admin ) { ?>
		                                    <?php if ( POS ) { ?>
                                        <li class="dropdown hidden-xs">
                                            <a class="btn" title="<?= lang('list_open_registers') ?>" data-placement="bottom" href="<?= admin_url('pos/registers') ?>">
                                                <i class="lni-clipboard"></i><?= lang('list_open_registers') ?>
                                            </a>
                                        </li>
											<?php } ?>
                                        <li class="dropdown hidden-xs">
                                            <a class="btn" title="<?= lang('clear_ls') ?>" data-placement="bottom" id="clearLS" href="#">
                                                <i class="lni-trash"></i><?= lang('clear_ls') ?>
                                            </a>
                                        </li>
										<?php } ?>
                                    </ul>
                                    <ul class="nav navbar-nav pull-right">
                                        <li class="dropdown">
                                            <a class="btn account dropdown-toggle" data-toggle="dropdown" href="#">
	                                            <?= ci_get_user_avatar( $this->session->userdata, true, [ 'class' => 'mini_avatar img-rounded' ], true ); ?>
                                            </a>
                                            <ul class="dropdown-menu pull-right">
                                                <li>
                                                    <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id')); ?>"><i class="ti ti-user"></i> <?= lang('profile'); ?></a>
                                                </li>
                                                <li>
                                                    <a href="<?= admin_url('users/profile/' . $this->session->userdata('user_id') . '/#cpassword'); ?>"><i class="ti ti-lock"></i> <?= lang('change_password'); ?></a>
                                                </li>
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="<?= admin_url('logout'); ?>"><i class="ti ti-unlock"></i> <?= lang('logout'); ?></a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </header>
                        <div id="content">
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <ul class="breadcrumb">
										<?php foreach ( $bc as $b ) {
											if ( $b['link'] === '#' ) {
												echo '<li class="active">' . $b['page'] . '</li>';
											} else {
												echo '<li><a href="' . $b['link'] . '">' . $b['page'] . '</a></li>';
											}
										} ?>
                                        <li class="right_log hidden-xs">
	                                        <?= lang( 'your_ip' ) . ' ' . $ip_address . " <span class='hidden-sm'>( " . lang( 'last_login_at' ) . ': ' . date( $dateFormats['php_ldate'], $this->session->userdata( 'old_last_login' ) ) . ' ' . ( $this->session->userdata( 'last_ip' ) != $ip_address ? lang( 'ip:' ) . ' ' . $this->session->userdata( 'last_ip' ) : '' ) . ' ) </span>'; ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
	                            <div class="col-lg-12 bs-alerts"></div>
                                <div class="col-lg-12">
	                                <?php
	                                $types = [
                                        'message' => 'success',
                                        'success' => 'success',
                                        'error' => 'success',
	                                ];
	                                foreach ( $alert_messages as $type => $message ) {
	                                	if ( empty( $message ) ) {
	                                		continue;
		                                }
	                                	$type = 'message' === $type ? 'success' : $type;
	                                	$type = 'error' === $type ? 'danger' : $type;
	                                	
		                                bs_alert( $message, $type, true );
                                    }
                                    
									if ( $info ) {
										foreach ( $info as $n ) {
											if ( ! $this->session->userdata( 'hidden' . $n->id ) ) {
												?>
                                                <div class="alert alert-info" role="alert" id="notif-<?= $n->id; ?>">
                                                    <a href="#" id="<?= $n->id ?>" aria-controls="notif-<?= $n->id; ?>" aria-label="<?= lang( 'dismiss_alert' ); ?>" class="close hideComment external" data-dismiss="alert">&times;</a>
													<div class="alert-body"><?= $n->comment; ?></div>
                                                </div>
												<?php
											}
										}
									}
									?>
                                    <div class="alerts-con"></div>
