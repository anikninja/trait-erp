<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- ================================== TOP NAVIGATION ================================== -->
<div class="side-menu animate-dropdown outer-bottom-xs sidebar-module-container">
	<div class="head"><i class="icon fa fa-align-justify fa-fw"></i> <?= lang( 'categories' ); ?></div>
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
								<a href="<?php echo get_category_permalink( $category ); ?>#cat-filter-<?php echo $category->slug; ?>" data-toggle="collapse" class="accordion-toggle<?php echo false === $show ? ' collapsed' : ''; ?>"><?php echo $category->name; ?></a>
							</div>
							<!-- /.accordion-heading -->
							<div class="accordion-body collapse<?php echo $show; ?>" id="cat-filter-<?php echo $category->slug; ?>" style="<?php echo $show ? '' : 'height: 0px;'; ?>">
								<div class="accordion-inner">
									<ul>
										<li><a class="primary<?php echo $currentCat === $category->slug && ! $currentSubCat ? ' active' : ''; ?>"  href="<?php echo get_category_permalink( $category ); ?>"><?php echo lang( 'all' ); ?></a></li>
										<?php
										foreach( $category->subcategories as $sub_category ) { ?>
											<li><a class="secondary<?php echo $currentSubCat === $sub_category->slug ? ' active' : ''; ?>" href="<?php echo get_category_permalink( $category, $sub_category ); ?>"><?php echo $sub_category->name; ?></a></li>
											<?php
										}
										?>
									</ul>
								</div>
								<!-- /.accordion-inner -->
							</div>
							<!-- /.accordion-body -->
							<?php
						} else { ?>
							<div class="accordion-heading no-sub-cat">
								<a href="<?php echo get_category_permalink( $category ); ?>" class="accordion-toggle collapsed"><?php echo $category->name; ?></a>
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
	<!-- /.sidebar-widget -->
</div>
<!-- /.side-menu -->
<!-- ================================== TOP NAVIGATION : END ================================== -->
