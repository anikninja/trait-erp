<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- ================================== TOP NAVIGATION ================================== -->
<div class="side-menu animate-dropdown outer-bottom-xs sidebar-module-container">
	<div class="head">Categories</div>
	<nav class="sidebar-widget">
		<div class="sidebar-widget-body">
			<div class="accordion">
				<?php
				foreach ( $categories as $category ) {
					$currentCat    = isset( $filters['category'] ) && $filters['category'] ? $filters['category']->slug : '';
					$currentSubCat = isset( $filters['subcategory'] ) && $filters['subcategory'] ? $filters['subcategory']->slug : '';
					$show          = $category->slug === $currentCat ? ' in' : false;
					?>
					<div class="accordion-group">
						<?php
						if ( count($category->subcategories) > 0 ) { ?>
							<div class="accordion-heading">
								<a href="<?php echo site_url( 'category/'. $category->slug ) ?>#cat-filter-<?php echo $category->slug; ?>" data-toggle="collapse" class="accordion-toggle<?php echo false === $show ? ' collapsed' : ''; ?>"><?php echo $category->name; ?></a>
							</div>
							<!-- /.accordion-heading -->
							<div class="accordion-body collapse<?php echo $show; ?>" id="cat-filter-<?php echo $category->slug; ?>" style="<?php echo $show ? '' : 'height: 0px;'; ?>">
								<div class="accordion-inner">
									<?= build_category_tree( $category->subcategories ); ?>
								</div>
								<!-- /.accordion-inner -->
							</div>
							<!-- /.accordion-body -->
							<?php
						} else { ?>
							<div class="accordion-heading no-sub-cat">
								<a href="<?php echo site_url('category/'. $category->slug ); ?>" class="accordion-toggle collapsed"><?php echo $category->name; ?></a>
							</div>
							<?php
						}
						?>
					</div>
					<!-- /.accordion-group -->
					<?php
				}
				?>
			</div>
			<!-- /.accordion -->
		</div>
	</nav>
	<!-- /.megamenu-horizontal -->
</div>
<!-- /.side-menu -->
<!-- ================================== TOP NAVIGATION : END ================================== -->
