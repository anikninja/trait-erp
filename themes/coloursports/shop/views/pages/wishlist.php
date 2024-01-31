<div class="breadcrumb">
	<div class="container">
		<div class="breadcrumb-inner">
			<ul class="list-inline list-unstyled">
				<li><a href="<?= site_url() ?>"><?= lang( 'shop' ) ?></a></li>
				<li class='active'><?= lang( $v ); ?></li>
			</ul>
		</div><!-- /.breadcrumb-inner -->
	</div><!-- /.container -->
</div><!-- /.breadcrumb -->

<div class="body-content">
	<div class="container">
		<div class="checkout-box">
			<div class="row">
				<?php
				include 'profile_navs.php';
				if ( ! empty( $items ) ) {
				?>
				<div class="col-xs-12 col-sm-12 col-md-10">
					<div class="panel-group">
						<div class="panel panel-default quote-panel">
							<div class="panel-heading text-bold"><?= lang('wishlist'); ?></div><!-- .panel-heading -->
							<div class="wishlist-wrap table-responsive">
								<table class="wishlist-items text-center">
									<thead>
									<tr>
										<th style="width: 80px"><?= lang('photo') ?></th>
										<th style="min-width: 120px;"><?= lang('description') ?></th>
										<th><?= lang('price') ?></th>
										<th style="min-width: 85px"><?= lang('in_stock') ?></th>
										<th style="width: 80px;"><?= lang('actions') ?></th>
									</tr>
									</thead>
									<tbody>
									<?php foreach ( $items as $item ) {
										$prodClasses = get_single_product_classes( $item );
										?>
										<tr class="<?php echo $prodClasses; ?>" data-product='<?php echo safeJsonEncode( $item ); ?>'>
											<td class="">
												<a class="view-product" href="<?= $item->link; ?>">
													<img src="<?= $item->image; ?>" alt="<?= $item->name; ?>" class="img-responsive">
												</a>
											</td>
											<td>
												<a class="view-product" href="<?= $item->link; ?>"><?= $item->name; ?></a>
												<p><?= $item->custom_data['short_description'] ?></p>
											</td>
											<td><?php
												if ( $item->onSale ) {
													echo '<del class="text-red">' . $item->regular_price . '</del><br>';
													echo $item->sale_price;
												} else {
													echo $item->regular_price;
												}
												?></td>
											<td><?= $item->quantity > 0 ? lang('yes') : lang('no'); ?></td>
											<td>
												<div class="btn-group btn-group-justified" role="group">
													<div class="btn-group" role="group">
														<a href="<?= $item->add_to_cart; ?>" class="tip btn btn-sm btn-theme add-to-cart" data-id="<?= $item->id; ?>" title="<?= lang('add_to_cart'); ?>"><i class="fa fa-shopping-cart"></i></a>
													</div>
													<div class="btn-group" role="group">
														<a href="<?= shop_url( 'cart/remove_wishlist/' . $item->id ); ?>" class="tip btn btn-sm btn-danger remove-wishlist" data-id="<?= $item->id; ?>" title="<?= lang( 'remove_from_wishlist' ); ?>"><i class="fa fa-trash-o"></i></a>
													</div>
												</div>
											</td>
										</tr>
									<?php } ?>
									</tbody>
								</table>
							</div>
							<!-- /.wishlist-wrap -->
						</div>
					</div>
				</div>
				<?php } else { ?>
				<div class="col-xs-12 col-sm-9 col-md-10">
					<div class="alert alert-info text-center" role="alert">
						<img style="margin-bottom: 50px" src="<?= $assets . 'images/empty-cart.png' ?>"/>
						<h2 class="alert-heading">Empty Wishlist</h2>
						<p style="font-size: 20px">We are sorry to know you did not add anything to wishlist</p>
						<p class="mb-0"></p>
					</div>
				</div>
				<?php } ?>
			</div>
			<!-- /.row -->
		</div>
		<!-- /.checkout-box -->
	</div>
	<!-- /.container -->
</div>
<!-- /.body-content -->
