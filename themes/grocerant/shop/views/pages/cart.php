<?php
defined('BASEPATH') or exit('No direct script access allowed');
$shipping = $this->cart->shipping();
$free_hipping = absfloat($shop_settings->free_shipping);
?>
<!-- =============================== CART SIDEBAR : START =============================== -->
<div class="cart-sidebar-wrapper <?php echo ( $free_hipping > 0 ) ? 'has-shipping' : 'not-shipping'; ?>">
    <div class="cart-open-btn">
        <div class="icon">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.7344 9.75H24.7344L27.2344 31.375H4.73438L7.23438 9.75H19.7344Z" fill="white"/>
                <path d="M19.7344 13.5C20.425 13.5 20.9844 14.0593 20.9844 14.75C20.9844 15.4407 20.425 16 19.7344 16C19.0437 16 18.4844 15.4407 18.4844 14.75C18.4844 14.0593 19.0437 13.5 19.7344 13.5Z" fill="#575972"/>
                <path d="M12.2344 13.5C12.925 13.5 13.4844 14.0593 13.4844 14.75C13.4844 15.4407 12.925 16 12.2344 16C11.5437 16 10.9844 15.4407 10.9844 14.75C10.9844 14.0593 11.5437 13.5 12.2344 13.5Z" fill="#575972"/>
                <path d="M23.5 29.5C23.845 29.5 24.125 29.22 24.125 28.875C24.125 28.53 23.845 28.25 23.5 28.25C23.155 28.25 22.875 28.53 22.875 28.875C22.875 29.22 23.155 29.5 23.5 29.5Z" fill="black"/>
                <path d="M16 0C13.5877 0 11.625 1.96265 11.625 4.375V9.125H7.25003C6.93265 9.125 6.66556 9.36304 6.62918 9.67822L4.12918 31.3032C4.08646 31.6741 4.3765 32 4.75003 32H27.25C27.6233 32 27.9139 31.6743 27.8709 31.3032L25.3709 9.67822C25.3345 9.36304 25.0677 9.125 24.75 9.125H20.375V4.375C20.375 1.96094 18.4222 0 16 0ZM12.875 4.375C12.875 2.65186 14.2769 1.25 16 1.25C17.7244 1.25 19.125 2.64478 19.125 4.375V9.125H12.875V4.375ZM24.1932 10.375L26.5486 30.75H5.45145L7.80716 10.375H11.625V12.9824C10.8977 13.2405 10.375 13.9353 10.375 14.75C10.375 15.7839 11.2163 16.625 12.25 16.625C13.284 16.625 14.125 15.7839 14.125 14.75C14.125 13.9353 13.6026 13.2405 12.875 12.9824V10.375H19.125V12.9824C18.3977 13.2405 17.875 13.9353 17.875 14.75C17.875 15.7839 18.7163 16.625 19.75 16.625C20.784 16.625 21.625 15.7839 21.625 14.75C21.625 13.9353 21.1026 13.2405 20.375 12.9824V10.375H24.1932ZM12.25 14.125C12.5948 14.125 12.875 14.4053 12.875 14.75C12.875 15.0947 12.5948 15.375 12.25 15.375C11.9056 15.375 11.625 15.0947 11.625 14.75C11.625 14.4053 11.9056 14.125 12.25 14.125ZM19.75 14.125C20.0948 14.125 20.375 14.4053 20.375 14.75C20.375 15.0947 20.0948 15.375 19.75 15.375C19.4056 15.375 19.125 15.0947 19.125 14.75C19.125 14.4053 19.4056 14.125 19.75 14.125V14.125Z" fill="black"/>
                <path d="M21 28.25H8.5C8.15479 28.25 7.875 28.5298 7.875 28.875C7.875 29.2202 8.15479 29.5 8.5 29.5H21C21.3452 29.5 21.625 29.2202 21.625 28.875C21.625 28.5298 21.3452 28.25 21 28.25Z" fill="black"/>
            </svg>
        </div>
        <p class="total-cart-item"><?php echo $cart['total_items']; ?> <span class="sr-only"><?php echo lang( 'item' ); ?></span></p>
        <p class="total-cart-amount"><?php echo $cart['subtotal']; ?></p>
    </div>
    <div class="cart-sidebar-header">
        <div class="cart-items-count">
            <img src="<?php echo $assets; ?>images/bag.png" alt="bag">
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
        <?php if ( !empty($shop_settings->phone) ) { ?>
        <div class="alert alert-info" role="alert">
            <i class="fa fa-info-circle" aria-hidden="true"></i> For any problem call <a href="tel:<?php echo str_replace( ' ', '', $shop_settings->phone ); ?>"><?php echo !empty($shop_settings->phone) ? $shop_settings->phone : ''; ?></a>
        </div>
        <?php } ?>
	    <?php
	    if ( $shop_settings->minimum_order ) {
	    	$min_order = $this->rerp->convertMoney( $shop_settings->minimum_order, false, false );
	    ?>
            <div class="alert alert-warning cart-min-order" role="alert" data-min_order="<?= $min_order; ?>" style="display:<?= $cart['_total'] <= $min_order ? 'block': 'none'; ?>;">
	            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php printf( lang('minimum_order_amount_x'), $this->rerp->formatMoney( $min_order, $selected_currency->symbol ) ); ?></div>
	    <?php } ?>
        <div class="cart-checkout">
            <a href="<?php echo site_url( 'cart/checkout' ); ?>">
                <div class="checkout-text"><?php echo lang('checkout'); ?></div>
	            <div class="checkout-amount"><?= $this->rerp->formatMoney( '0.00', $selected_currency->symbol ); ?></div>
            </a>
        </div>
    </div>
</div>
