<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="breadcrumb">
    <div class="container">
        <div class="breadcrumb-inner">
            <ul class="list-inline list-unstyled">
                <li><a href="<?= shop_url() ?>">Shop</a></li>
                <li class='active'>Address</li>
            </ul>
        </div><!-- /.breadcrumb-inner -->
    </div><!-- /.container -->
</div><!-- /.breadcrumb -->

<div class="body-content">
    <div class="container">
        <div class="checkout-box">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-2">
                    <div class="panel-group">
                        <div class="panel panel-default order-nav-panel">
                            <ul class="orders-nav">
                                <li><a href="<?= site_url('profile'); ?>"><i class="mi fa fa-user"></i> <?= lang('profile'); ?></a></li>
                                <li><a class="active" href="<?= shop_url('addresses'); ?>"><i class="mi fa fa-building"></i> <?= lang('addresses'); ?></a></li>
                                <li><a href="<?= site_url('referral'); ?>"><i class="mi fa fa-building"></i> <?= lang('referral'); ?></a></li>
                                <li><a href="<?= site_url('wallet'); ?>"><i class="mi fa fa-building"></i> <?= lang('wallet'); ?></a></li>
                                <li><a href="<?= shop_url('orders'); ?>"><i class="mi fa fa-heart"></i> <?= lang('orders'); ?></a></li>
                                <li><a href="<?= shop_url('quotes'); ?>"><i class="mi fa fa-heart-o"></i> <?= lang('quotes'); ?></a></li>
                                <li><a href="<?= site_url('logout'); ?>"><i class="mi fa fa-sign-out"></i> <?= lang('logout'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-10">
                    <div class="panel-group">
                        <div class="panel panel-default address-panel">
                            <div class="panel-heading text-bold">
                                <?= lang('my_addresses'); ?>
                            </div><!-- .panel-heading -->
                            <div class="checkout-step-content-wrap">
                                <div class="row address-wrap">
		                            <?php
		                            if (!empty($addresses)) {
			                            foreach ($addresses as $address) {
				                            ?>
                                            <div class="address col-sm-6 col-md-4 address-<?php echo $address->id; ?>" data-address='<?php echo json_encode($address) ?>'>
                                                <label>
                                                    <div class="checkout-page-content">
                                                        <div class="content-header"><?php echo $address->title; ?></div>
                                                        <p class="content">
								                            <?= $address->line1; ?><br>
								                            <?= $address->line2; ?><br>
	                                                        <?= $address->area; ?><br>
								                            <?= $address->city; ?>,
								                            <?= ci_get_states($address->country, $address->state); ?> -
								                            <?= $address->postal_code; ?><br>
                                                            <?= ci_get_countries($address->country); ?>
                                                            <?= '<br>' . $address->phone; ?>
                                                        </p>
                                                        <div class="checkout-step-content-edit">
                                                            <a class="edit" href="#"><img src="<?php echo $assets; ?>images/edit-icon.png"></a>
                                                            <a class="remove" href="#"><img src="<?php echo $assets; ?>images/times.png"></a>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
				                            <?php
			                            }
		                            }
		                            ?>
                                </div>
                            </div>
                            <?php if ( count($addresses) < 6 ) { ?>
                                <a href="#" id="add-new-address" class="update-address btn btn-primary">+ Add Address</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div><!-- /.row -->
        </div><!-- /.checkout-box -->
    </div><!-- /.container -->
</div><!-- /.body-content -->