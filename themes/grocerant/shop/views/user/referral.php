<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="breadcrumb">
    <div class="container">
        <div class="breadcrumb-inner">
            <ul class="list-inline list-unstyled">
                <li><a href="<?= shop_url() ?>">Shop</a></li>
                <li class='active'>Referral</li>
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
                                <li><a href="<?= shop_url('addresses'); ?>"><i class="mi fa fa-building"></i> <?= lang('addresses'); ?></a></li>
                                <li><a class="active" href="<?= site_url('referral'); ?>"><i class="mi fa fa-building"></i> <?= lang('referral'); ?></a></li>
                                <li><a href="<?= site_url('wallet'); ?>"><i class="mi fa fa-building"></i> <?= lang('wallet'); ?></a></li>
                                <li><a class="" href="<?= shop_url('orders'); ?>"><i class="mi fa fa-heart"></i> <?= lang('orders'); ?></a></li>
                                <li><a href="<?= shop_url('quotes'); ?>"><i class="mi fa fa-heart-o"></i> <?= lang('quotes'); ?></a></li>
                                <li><a href="<?= site_url('logout'); ?>"><i class="mi fa fa-sign-out"></i> <?= lang('logout'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-10">
                    <div class="panel-group">
                        <div class="panel panel-default profile-panel">
                            <div class="panel-heading text-bold">
                                <?= lang('referral_information'); ?>
                            </div><!-- .panel-heading -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('referral_id', 'referral_id'); ?>
                                        <?= form_input('referral_id', set_value('referral_id', '0000-'.$this->session->userdata('user_id')), 'class="form-control tip" id="referral_id" readonly = "readonly" disabled="disabled"'); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= lang('referral_url', 'referral_url'); ?>
                                        <?php
                                        $ref_url = $commission_settings['referral_url'];
                                        if ( empty( $ref_url )  ) {
	                                        $ref_url = site_url();
                                        }
                                        if ( false === strpos( $ref_url, 'http' ) ) {
	                                        $ref_url = site_url( $ref_url );
                                        }
                                        $_site_url = substr( site_url(), strpos( site_url(), '//' ) );
                                        $_ref_url  = substr( $ref_url, strpos( $ref_url, '//' ) );
                                        if ( false === strpos( $_ref_url, $_site_url ) ) {
	                                        $ref_url = site_url();
                                        }
                                        $ref_url .= '?referral=0000-' . $this->session->userdata('user_id');
                                        ?>
                                        <?= form_input('referral_url', $ref_url, 'class="form-control tip" id="last_name" required="required" readonly disabled onclick="select(this);" style="cursor:default;"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.row -->
        </div><!-- /.checkout-box -->
    </div><!-- /.container -->
</div><!-- /.body-content -->

<!-- Modal -->
<div class="modal fade" id="checkout-popup" tabindex="-1" role="dialog" aria-labelledby="checkout-popup" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="checkout-popup-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

