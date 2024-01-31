<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- =============================== CART SIDEBAR : START =============================== -->
<?php $shipping = $this->cart->shipping(); ?>
<div class="cart-sidebar-wrapper <?php echo ( (int) $shipping ) ? 'has-shipping' : 'not-shipping'; ?>">
    <div class="cart-open-btn">
        <div class="icon">
            <i class="fa fa-shopping-bag"></i>
        </div>
        <p class="total-cart-item"><?php echo $cart['total_items']; ?> <span class="sr-only"><?php echo lang( 'item' ); ?></span></p>
        <p class="total-cart-amount"><?php echo $cart['subtotal']; ?></p>
    </div>
    <div class="cart-sidebar-header">
        <div class="cart-items-count">
            <i class="fa fa-shopping-bag"></i>
            <p><span class="cart-total-item-count">0 <?php echo lang( 'item' ); ?></span></p>
        </div>
        <div class="cart-close-btn">
            <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1.96814L1 13.9522" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M1 1.96814L13 13.9522" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
	<?php
	if ( (int) $shipping ) {
		$shipping = $this->rerp->convertMoney($shipping, false, false)
		?>
        <div class="cart-delivery-cost">
			<?php printf( lang('shipping_charge_is_x'), $this->rerp->formatMoney($shipping, $selected_currency->symbol) ); ?>
        </div>
		<?php
	}
	?>
    <div class="cart-sidebar-content cart-empty">
		<?php /*<div class="single-cart-item" data-product-id="p101">
            <div class="cart-qty-input">
                <button class="cart-item-increase-btn">+</button>
                <span class="cart-item-qty-input">1</span>
                <button class="cart-item-decrease-btn">-</button>
            </div>
            <div class="cart-item-photo">
                <a href="#">
                    <img src="<?php echo $assets; ?>images/products/cart-item-1.jpg" alt="product">
                </a>
            </div>
            <div class="cart-item-details">
                <div class="cart-item-title"><a href="#">Fresh Rice</a></div>
                <div class="cart-item-price">$<span>2.6</span></div>
                <div class="cart-item-qty-with-item-unit">
                    <p><span class="cart-item-qty">1</span> X <span class="cart-item-unit">2 lb</span></p>
                </div>
            </div>
            <div class="cart-item-amount">
                <p>$<span>2.6</span></p>
            </div>
            <div class="cart-item-remove-btn">
                <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 1.96814L1 13.9522" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M1 1.96814L13 13.9522" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>*/ ?>
    </div>
    <div class="cart-sidebar-footer">
	    <?php
	    if ( isset( $theme_options['cart_settings']['min_order']['content'] ) ) {
		    $minOrder = absint( $theme_options['cart_settings']['min_order']['content'] );
		    if ( $minOrder > 0 ) {
			    ?>
                <div class="alert alert-warning cart-min-order" role="alert" data-min_order="<?= $minOrder; ?>" style="display:<?= $cart['_total'] <= $minOrder ? 'block': 'none'; ?>;">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php
				    printf(lang('minimum_order_amount_x'), $this->rerp->formatMoney( $theme_options['cart_settings']['min_order']['content'], $selected_currency->symbol ) );
				    ?></div>
			    <?php
		    }
	    } ?>
        <div class="cart-checkout">
            <a href="<?php echo site_url( 'cart/checkout' ); ?>">
                <div class="checkout-text"><?php echo lang('checkout'); ?></div>
                <div class="checkout-amount">$0.00</div>
            </a>
        </div>
    </div>
</div>