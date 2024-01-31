<?php
defined('BASEPATH') or exit('No direct script access allowed');
$shipping = $this->cart->shipping();
$free_hipping = absfloat($shop_settings->free_shipping);
?>
<!-- =============================== CART SIDEBAR : START =============================== -->
<div class="cart-sidebar-wrapper <?php echo ( $free_hipping > 0 ) ? 'has-shipping' : 'not-shipping'; ?>">
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
	if ( $free_hipping ) {
//		$shipping = $shipping['cost'] ? $this->rerp->convertMoney( $shipping['cost'], false, false ) : '';
		$freeShipping = $this->rerp->convertMoney( $shop_settings->free_shipping, false, false );
		$remaining    = $freeShipping - absfloat( $cart['_total'] );
		if ( $remaining < 0 ) {
			$remaining = 0;
		}
		$progress = 0;
		if ( $remaining > 0 ) {
			$progress = round( 100 - ( ( $remaining / $shop_settings->free_shipping ) * 100 ), 2 );
		} else {
			$progress = 100;
		}
		?>
		<style>
			.free_shipping {
				position: relative;
				display: block;
			}
			.free_shipping .progress {
				position: absolute;
				background: #acdadb;
				width: 0%;
				display: block;
				top: 1px;
				left: 0;
				height: 29px;
			}
			.free_shipping .free,
			.free_shipping .status {
				z-index: 1;
				position: relative;
				display: block;
                line-height: 30px;
			}
			.free_shipping .free.hide,
			.free_shipping .status.hide {
				display: none !important;
			}
			.cart-sidebar-footer .cart-checkout a.disabled {
				background: #cadede;
				cursor: not-allowed;
			}
		</style>
		<div class="cart-delivery-cost free_shipping" data-free_shipping="<?= $shop_settings->free_shipping; ?>" data-remaining="<?= $freeShipping ?>">
			<span class="progress" style="width:<?= $progress; ?>%;"></span>
			<span class="status <?= $progress === 100 ? 'hide' : 'show'; ?>"><?= sprintf( lang( 'ship_x_more_to_get_free_shipping' ), $this->rerp->formatMoney( $remaining, $selected_currency->symbol ) ); ?></span>
			<span class="free <?= $progress === 100 ? 'show' : 'hide'; ?>"><?= lang( 'your_shipping_is_free' ); ?></span>
		</div>
		<?php
	}
	?>
	<div class="cart-sidebar-content cart-empty"></div>
    <div class="cart-sidebar-footer">
	    <?php
	    if ( $shop_settings->minimum_order ) {
		    $min_order = $this->rerp->convertMoney( $shop_settings->minimum_order, false, false );
	    ?>
		    <div class="alert alert-warning cart-min-order" role="alert" data-min_order="<?= $min_order; ?>" style="display:<?= $cart['_total'] <= $min_order ? 'block': 'none'; ?>;">
			    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php
			    printf( lang('minimum_order_amount_x'), $this->rerp->formatMoney( $min_order, $selected_currency->symbol ) ); ?></div>
	    <?php } ?>
	    <div class="cart-checkout">
            <a href="<?php echo site_url( 'cart/checkout' ); ?>">
                <div class="checkout-text"><?php echo lang('checkout'); ?></div>
                <div class="checkout-amount"><?= $this->rerp->formatMoney( '0.00', $selected_currency->symbol ); ?></div>
            </a>
        </div>
    </div>
</div>
