<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="breadcrumb">
	<div class="container">
		<div class="breadcrumb-inner">
			<ul class="list-inline list-unstyled">
				<li><a href="<?= site_url() ?>"><?= lang( 'home' ); ?></a></li>
				<?php
				$name = '';
				if ( 'shop' === $m && 'page' === $v ) {
					echo '<li>' . lang( 'page' ) . '</li>';
					echo '<li class="active">' . $page->name . '</li>';
				} else if ( 'shop' === $m && 'product' === $v && isset( $product ) && isset( $product->name ) ) {
					echo '<li><a href="' . shop_url( 'products' ) . '">' . lang( 'shop' ) . '</a></li>';
					$name = $product->name;
				} else if ( 'shop' === $m && 'products' === $v ) {
					echo '<li class="active">' . lang( 'shop' ) . '</li>';
				} else {
					$name = isset( $page_title ) ? $page_title : '';
					if ( 'shop' == $m ) {
						echo '<li><a href="' . shop_url( 'products' ) . '">' . lang( 'shop' ) . '</a></li>';
					}
					echo '<li class="active">' . $name . '</li>';
				}
				?>
			</ul>
		</div><!-- /.breadcrumb-inner -->
	</div><!-- /.container -->
</div><!-- /.breadcrumb -->
