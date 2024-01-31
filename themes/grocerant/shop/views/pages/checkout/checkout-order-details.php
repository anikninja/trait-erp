<?php
if ( ! defined( 'BASEPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die();
}
/**
 * @var object $Settings
 * @var array $cart
 */
?>
    <div class="col-xs-12 col-sm-4 col-md-4">
        <!-- checkout-progress-sidebar -->
        <div class="checkout-progress-sidebar ">
            <div class="panel-group">
                <div class="order-details">
                    <div class="order-heading">
                        <h4 class="unicase-checkout-title"><?= lang( 'your_order' ); ?></h4>
                    </div>
                    <div class="checkout-step-total-wrap">
                        <div class="checkout-step-right-single">
                            <div class="checkout-step-total-left"><?= lang( 'total_w_o_tax' ); ?></div>
                            <div class="checkout-step-total-right subtotal"><?= $cart['subtotal']; ?></div>
                        </div>
                        <div class="checkout-step-right-single">
                            <div class="checkout-step-total-left"><?= lang( 'product_tax' ); ?></div>
                            <div class="checkout-step-total-right total_item_tax"><?= $cart['total_item_tax']; ?></div>
                        </div>
                        <div class="checkout-step-right-single">
                            <div class="checkout-step-total-left"><?= lang( 'total' ); ?></div>
                            <div class="checkout-step-total-right cart_total"><?= $cart['total']; ?></div>
                        </div>
						<?php if ($Settings->tax2 !== false) { ?>
                            <div class="checkout-step-right-single">
                                <div class="checkout-step-total-left"><?= lang( 'order_tax' ); ?></div>
                                <div class="checkout-step-total-right order_tax"><?= $cart['order_tax']; ?></div>
                            </div>
                        <?php } ?>
                        <div class="checkout-step-right-single delivery-fee">
                            <div class="checkout-step-total-left shipping_label" data-label="<?= lang( 'shipping_label' ); ?>"><?= sprintf( lang( 'shipping_label' ), $cart['shipping']['name'] ); ?></div>
                            <div class="checkout-step-total-right shipping_cost"><?= $cart['shipping']['cost']; ?></div>
                        </div>
                        <div class="cart-discounts">
                            <?php if ( isset( $cart['coupons'] ) && ! empty( $cart['coupons'] ) ) { ?>
                                <?php foreach ( $cart['coupons'] as $hash => $coupon ) { ?>
                                    <div class="cart-discount" id="coupon-<?= $hash; ?>">
                                        <div class="checkout-step-total-left coupon_label">
                                            Coupon: <strong><?= $coupon['code']; ?></strong>
                                            <a href="<?= site_url( 'cart/remove_coupon/' . $hash ); ?>" data-hash="<?= $hash; ?>" class="remove-coupon" title="Remove “<?= $coupon['code']; ?>” Coupon">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                        <div class="checkout-step-total-right coupon_discount">
                                            <span class="amount"><?= $coupon['discount_price']; ?></span>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <div class="checkout-step-right-single total">
                            <div class="checkout-step-total-left"><?= lang( 'grand_total' ); ?></div>
                            <div class="checkout-step-total-right grand_total"><?= $cart['grand_total']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-group">
                <div class="coupon">
                    <div id="checkout_coupon_accordion" role="tablist" aria-multiselectable="true">
                        <a class="coupon-toggle collapsed" id="checkout_coupon_accordion_toggle" role="button" data-toggle="collapse" data-parent="#checkout_coupon_accordion" href="#checkout_coupon_form" aria-expanded="false" aria-controls="checkout_coupon_form">
                            <span class="icon">
                                <svg width="10px" height="10px" viewBox="0 0 100 100">
                                    <path transform="translate(0 -952.36)" d="m31.918 1045.4l36.164-31.684 12.918-11.316-12.918-11.316-36.164-31.684-12.918 11.316 36.168 31.684-36.168 31.684zm0 0" stroke="#000" stroke-linecap="round" stroke-width="2"></path>
                                </svg>
                            </span>
                            <span>Have A Coupon?</span>
                        </a>
                        <div id="checkout_coupon_form" class="panel-collapse collapse" role="tabpanel" aria-labelledby="checkout_coupon_accordion_toggle">
                            <div class="fields">
                                <label for="coupon_code" class="sr-only">Coupon:</label>
                                <input class="coupon_code form-control" id="coupon_code" type="text" placeholder="<?= lang( 'coupon_code' ); ?>">
                                <a href="#" class="btn btn-primary apply_coupon"><?= lang( 'apply_coupon' ); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.checkout-progress-sidebar -->
    </div>
    <script type="text/html" id="tmpl-cart-discount">
        <div class="cart-discount">
            <div class="cart-discount" id="coupon-__hash__">
                <div class="checkout-step-total-left coupon_label">
                    Coupon: <strong>__code__</strong>
                    <a href="<?= site_url( 'cart/remove_coupon/__hash__' ); ?>" data-hash="__hash__" class="remove-coupon" title="Remove “__code__” Coupon">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="checkout-step-total-right coupon_discount">
                    <span class="amount">__discount_price__</span>
                </div>
            </div>
        </div>
    </script>
<?php
// End of file checkout-order-details.php.
