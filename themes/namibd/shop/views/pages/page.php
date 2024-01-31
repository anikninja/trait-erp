<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="breadcrumb">
    <div class="container">
        <div class="breadcrumb-inner">
            <ul class="list-inline list-unstyled">
                <li><a href="<?= shop_url() ?>">Shop</a></li>
                <li>Page</li>
                <li class='active'><?= $page->name; ?></li>
            </ul>
        </div><!-- /.breadcrumb-inner -->
    </div><!-- /.container -->
</div><!-- /.breadcrumb -->

<div class="body-content">
    <div class="container">
        <div class="checkout-box">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="panel-group">
                        <div class="panel panel-default page-panel">
                            <div class="panel-heading text-bold">
                                <?= $page->name; ?>
                            </div><!-- .panel-heading -->
                            <div class="page-content body-content">
                                <span class="title-tag">Last Updated on <?= $page->updated_at; ?></span>
                                <?= $this->rerp->decode_html($page->body); ?>
                                <?php
                                if ($page->slug == $shop_settings->contact_link) {
                                    echo '<p><button type="button" class="btn btn-primary email-modal">Send us email</button></p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.row -->
        </div><!-- /.checkout-box -->
    </div><!-- /.container -->
</div><!-- /.body-content -->