<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="breadcrumb">
    <div class="container">
        <div class="breadcrumb-inner">
            <ul class="list-inline list-unstyled">
                <li><a href="<?= shop_url() ?>">Shop</a></li>
                <li class='active'>quotes</li>
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
                                <li><a href="<?= site_url('referral'); ?>"><i class="mi fa fa-building"></i> <?= lang('referral'); ?></a></li>
                                <li><a href="<?= site_url('wallet'); ?>"><i class="mi fa fa-building"></i> <?= lang('wallet'); ?></a></li>
                                <li><a href="<?= shop_url('orders'); ?>"><i class="mi fa fa-heart"></i> <?= lang('orders'); ?></a></li>
                                <li><a class="active" href="<?= shop_url('quotes'); ?>"><i class="mi fa fa-heart-o"></i> <?= lang('quotes'); ?></a></li>
                                <li><a href="<?= site_url('logout'); ?>"><i class="mi fa fa-sign-out"></i> <?= lang('logout'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-10">
                    <div class="panel-group">
                        <div class="panel panel-default quote-panel">
                            <div class="panel-heading text-bold">
                                <?= lang('my_quotes'); ?>
                            </div><!-- .panel-heading -->
                            <?php
                            if (!empty($orders)) { ?>
                                <table class="quotes-table order-details-table">
                                    <thead>
                                        <tr>
                                            <th><?= lang('id') ?></th>
                                            <th><?= lang('date') ?></th>
                                            <th><?= lang('ref') ?></th>
                                            <th><?= lang('status') ?></th>
                                            <th><?= lang('amount') ?></th>
                                            <th><?= lang('comment') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach ($orders as $order) { ?>
                                                <tr>
                                                    <td><a href="<?= shop_url('quotes/' . $order->id); ?>"><?= $order->id ?></a></td>
                                                    <td><?= $this->rerp->hrld($order->date); ?></td>
                                                    <td><?= $order->reference_no; ?></td>
                                                    <td><?= $order->status; ?></td>
                                                    <td><?= $this->rerp->formatMoney($order->grand_total, $this->default_currency->symbol); ?></td>
                                                    <td><?= $this->rerp->decode_html($order->note); ?></td>
                                                </tr>
                                            <?php
                                            }
                                        ?>
                                    </tbody>
                                </table>

                                <div class="row" style="margin-top:32px;">
                                    <div class="col-md-6">
                                            <span class="page-info line-height-xl hidden-xs hidden-sm">
                                                <?= str_replace(['_page_', '_total_'], [$page_info['page'], $page_info['total']], lang('page_info')); ?>
                                            </span>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="pagination" class="pagination-right"><?= $pagination; ?></div>
                                    </div>
                                </div>

                            <?php
                            }else {
                                echo '<p class="no-quote-text">' . lang('no_data_to_display') . '</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div><!-- /.row -->
        </div><!-- /.checkout-box -->
    </div><!-- /.container -->
</div><!-- /.body-content -->