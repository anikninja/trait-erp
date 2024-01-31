<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="side-menu animate-dropdown outer-bottom-xs sidebar-module-container">
	<div class="head"><i class="icon fa fa-braille fa-fw"></i> <?= lang( 'brands' ); ?></div>
	<nav class="sidebar-widget">
		<div class="sidebar-widget-body">
			<div class="accordion">
				<?php
				foreach ( $brands as $brand ) {
					if ( ! $brand->slug ) {
						continue;
					}
					$currentBrand = isset( $filters['brand'] ) && $filters['brand'] ? $filters['brand']->slug : '';
					?>
					<div class="accordion-group">
						<div class="accordion-heading no-sub-cat">
							<a href="<?php echo site_url( 'brand/' . $brand->slug ); ?>" class="accordion-toggle collapsed"><?php echo $brand->name; ?></a>
						</div>
					</div>
					<!-- /.accordion-group -->
					<?php
				}
				?>
			</div>
			<!-- /.accordion -->
		</div>
	</nav>
	<!-- /.sidebar-widget -->
</div>
