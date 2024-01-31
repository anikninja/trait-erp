<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include 'breadcrumb.php'; ?>
<div class="body-content">
    <div class="container">
        <div class="checkout-box">
            <div class="row">
                <?php
                include 'profile_navs.php';
                if ( ! empty( $orders ) ) {
                ?>
                    <div class="col-xs-12 col-sm-4 col-md-3">
                        <div class="panel-group">
                            <div class="panel panel-default order-panel my-order-panel">
                                <div class="panel-heading text-bold">
                                    My Orders
                                </div><!-- .panel-heading -->
                                <nav class="order-list">
                                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                        <?php
                                        foreach ( $orders as $order ) {?>
                                            <a class="nav-item nav-link <?php if($order->delivery_status=='delivered') echo 'delivered'?>" id="order-<?= $order->id ?>-tab" data-toggle="tab" href="#order-<?= $order->id ?>" role="tab" aria-controls="order-<?= $order->id ?>" aria-selected="true">
                                                <div class="order-box">
                                                    <div class="order-header">
                                                        <div class="order-status
                                                            <?php if (empty($order->delivery_status)){
                                                            if ($order->sale_status == 'returned'){
                                                                echo 'returned';
                                                            } else{
                                                                echo 'received';
                                                            }
                                                        } else {
                                                            echo $order->delivery_status;
                                                        } ?>">
	                                                        <?php if (empty($order->delivery_status)){
		                                                        if ($order->sale_status == 'returned'){
			                                                        echo 'returned';
		                                                        } else{
			                                                        echo 'received';
		                                                        }
	                                                        } else {
		                                                        echo $order->delivery_status;
	                                                        } ?>
                                                        </div>
                                                        <div class="order-number">Reference# <?= $order->reference_no ?></div>
                                                    </div><!-- .order-header-->
                                                    <div class="order-details">

                                                        <div class="order-date order-detail-single">
                                                            <p>Order Date:</p>
                                                            <p><?=$order->date?></p>
                                                        </div>

                                                        <?php if( !empty($this->sales_model->getDeliveryBySaleID($order->id)->date )): ?>
                                                            <div class="delivery-date order-detail-single">
                                                                <p>Delivery Date:</p>
                                                                <p><?= $this->sales_model->getDeliveryBySaleID($order->id)->date; ?></p>
                                                            </div>
                                                        <?php endif; ?>

                                                        <div class="order-total order-detail-single">
                                                            <p>Total:</p>
                                                            <p><?=$order->total?></p>
                                                        </div>
                                                    </div><!-- .order-details -->
                                                </div><!-- .order-box -->
                                            </a>
                                        <?php } ?>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content col-xs-12 col-sm-8 col-md-7" id="nav-tabContent">
	                    <?php
                        $i = 0;
	                    foreach ( $orders as $order ) {
		                    $delivery = $this->sales_model->getDeliveryBySaleID($order->id);
		                    $invoice = new Erp_Invoice($order->id);
	                        ?>
                            <div class="tab-pane <?php if ($i==0) echo 'active' ?>" id="order-<?=$order->id?>" role="tabpanel" aria-labelledby="order-<?=$order->id?>-tab">
                                <div class="panel-group">
                                    <div class="panel panel-default order-panel">
                                        <div class="order-heading-wrap">
                                            <div class="order-heading" style="position: relative">
                                                Order Details
                                                <div class="sub-heading" style="line-height: 5px;color: #353535;font-weight: 300">
                                                    <span style="font-size: 12px;" class="">Sale Reference : <?=$order->reference_no?></span>
	                                                <?php if(!empty($delivery->do_reference_no)){ ?>
                                                        <span style="font-size: 12px;margin-left: 8px" class="">Delivery Reference : <?=$delivery->do_reference_no?></span>
	                                                <?php } ?>
                                                </div>
                                            </div><!-- .panel-heading -->
                                            <div class="order-link">
                                                <a href="<?= shop_url('orders/' . $order->id); ?>" style="">View Invoice</a>
                                            </div>
                                        </div><!-- .order-heading-wrap -->
                                        <div class="ordered-product-details">

                                            <?php

                                            //echo '<pre>',print_r($invoice->getItems(),1),'</pre>';
                                            //echo '<pre>',print_r($invoice->getDeliveries(),1),'</pre>';
                                            //echo '<pre>',print_r($delivery,1),'</pre>';

                                            //echo '<pre>',print_r($order,1),'</pre>';

                                            ?>

                                            <div class="oredered-address hidden-xs">
                                                <?php
                                                if(!empty($delivery->address)){?>
                                                    <div class="address-heading">Delivery Address</div>
                                                    <div class="address-contne">
		                                                <?=$delivery->address?>
                                                    </div>
                                                <?php } ?>
                                            </div><!-- .ordered-address -->
                                            <div class="ordered-price">
                                                <div class="ordered-price-single subtotal">
                                                    <p>Subtotal:</p>
                                                    <p><?=$this->rerp->convertMoney($order->total)?></p>
                                                </div>
                                                <div class="ordered-price-single discount">
                                                    <p>Discount:</p>
                                                    <p><?=$order->total_discount?></p>
                                                </div>
                                                <div class="ordered-price-single discount">
                                                    <p>Tax:</p>
                                                    <p><?=$order->total_tax?></p>
                                                </div>
                                                <div class="ordered-price-single delivery-fee">
                                                    <p>Delivery Fee:</p>
                                                    <p><?=$order->shipping?></p>
                                                </div>
                                                <div class="ordered-price-single total">
                                                    <p>Total:</p>
                                                    <p><?=$order->grand_total?></p>
                                                </div>
                                            </div><!-- .ordered-price -->
                                        </div><!-- .ordered-product-details -->
                                        <div class="ordered-product-status">
                                            <div class="order-status-msg order-received <?php if(empty($order->delivery_status) || $order->delivery_status == 'packing' || $order->delivery_status == 'delivering' || $order->delivery_status == 'delivered') echo 'checked'?>">
                                                <p>Order Received</p>
                                            </div>
                                            <div class="order-status-msg order-packing <?php if($order->delivery_status == 'packing' || $order->delivery_status == 'delivering' || $order->delivery_status == 'delivered') echo 'checked' ?>">
                                                <p>Packing</p>
                                            </div>
                                            <div class="order-status-msg order-processing <?php if($order->delivery_status == 'delivering' || $order->delivery_status == 'delivered') echo 'checked' ?>">
                                                <p>Order on the Way</p>
                                            </div>
                                            <div class="order-status-msg order-delivered <?php if($order->delivery_status == 'delivered') echo 'checked' ?>">
                                                <p>Order Delivered</p>
                                            </div>
                                        </div><!-- end .ordered-product-status -->
                                        <div class="table-responsive">
                                            <table class="ordered-items ordered-products-wrap">
                                                <colgroup>
                                                    <col style="width: 250px; min-width: 250px;">
                                                    <col style="width: 100px; min-width: 100px;">
                                                    <col style="width: 100px; min-width: 100px;">
                                                </colgroup>
                                                <thead>
                                                <tr>
                                                    <th width="50%" style="min-width: 250px;">Items</th>
                                                    <th width="25%">Quantity</th>
                                                    <th width="25%">Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $items = $invoice->getItems();
                                                foreach ($items as $item){
                                                    $productImage = $item->getProductImage();
                                                    if ( ! $productImage ) {
                                                        $productImage = $assets . 'no_image.jpg';
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div class="ordered-product">
                                                                <img src="<?php echo $productImage; ?>">
                                                                <p class="name" title="<?=$item->getProductName()?>"><?= $item->getProductName()?></p>
                                                                <p class="quantity" title="<?= $item->getUnitQuantity()?>"><?= $item->getUnitQuantity()?></p>
                                                                <p class="price" title="<?=$item->getNetUnitPrice()?>"><?=$item->getNetUnitPrice()?></p>
                                                            </div>
                                                        </td>
                                                        <td><?= $item->getUnitQuantity()?></td>
                                                        <td><?=$item->getNetUnitPrice()?></td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table><!-- .ordered-proudct-wrap -->
                                        </div>
                                    </div><!-- .panel -->
                                </div><!-- panel-group -->
                            </div><!-- .tabe-pane -->
                        <?php $i++; } ?>
                    </div>
                <?php } else { ?>
                    <div class="col-xs-12 col-sm-9 col-md-10">
                        <div class="alert alert-info text-center" role="alert">
                            <img style="margin-bottom: 50px" src="<?= $assets . 'images/empty-cart.png' ?>"/>
                            <h2 class="alert-heading">Empty Order History</h2>
                            <p style="font-size: 20px">We are sorry to know you did not buy anything from us</p>
                            <p class="mb-0"></p>
                        </div>
                    </div>
                <?php } ?>
            </div><!-- /.row -->
        </div><!-- /.checkout-box -->
    </div><!-- /.container -->
</div><!-- /.body-content -->
