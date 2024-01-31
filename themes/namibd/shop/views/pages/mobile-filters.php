<div class="clearfix mobile-filters-container visible-xs">
	<div class="mobile-filter-trigger">
		<ul>
			<li>
				<a href="#" data-id="#category-filter"><i class="fa fa-list" aria-hidden="true"></i><?php echo lang('category'); ?></a>
			</li>
            <li>
                <a href="#" data-id="#other-filter"><i class="fa fa-filter" aria-hidden="true"></i><?php echo lang('filters'); ?></a>
            </li>
			<li>
				<a href="#"data-id="#sort-filter"><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php echo lang('sort'); ?></a>
			</li>
		</ul>
	</div>
	<div class="mobile-filters-body">
		<div class="single-filter-body" id="category-filter">
<!--            <ul class="mobile-sub-category-list">-->
            <?php
            echo build_category_tree( $categories, isset( $selected_categories ) ? $selected_categories : [], [ 'mobile-sub-category-list' ] );
            /**
                $subcats = $this->shop_model->getSubCategories(47);
                foreach ($subcats as $subcat) {
                ?>
                    <li><a href="<?php echo site_url( 'category/' . $subcat->slug ); ?>"><?php echo $subcat->name; ?></a></li>
                <?php
                }
             **/
            ?>
<!--            </ul>-->
		</div>
		<div class="single-filter-body" id="sort-filter">
            <div class="dropdown dropdown-small dropdown-med dropdown-white">
                <select name="sorting" id="sorting" class="selectpicker dropdown-menu" data-style="btn-sm" data-width="150px">
                    <option value="name-asc"><?= lang('name_asc'); ?></option>
                    <option value="name-desc"><?= lang('name_desc'); ?></option>
                    <option value="price-asc"><?= lang('price_asc'); ?></option>
                    <option value="price-desc"><?= lang('price_desc'); ?></option>
                    <option value="id-desc"><?= lang('id_desc'); ?></option>
                    <option value="id-asc"><?= lang('id_asc'); ?></option>
                    <option value="views-desc"><?= lang('views_desc'); ?></option>
                    <option value="views-asc"><?= lang('views_asc'); ?></option>
                </select>
            </div>
		</div>
		<div class="single-filter-body" id="other-filter">
			<!-- ============================================== PRICE SILDER============================================== -->

			<div class="widget-header">
				<h4 class="widget-title">Price Slider</h4>
			</div>
			<div class="sidebar-widget-body m-t-10">
				<div class="price-range-holder">
                    <span class="min-max">
                        <span class="pull-left"><?php echo $this->rerp->convertMoney( $price_range->min ); ?></span>
                        <span class="pull-right"><?php echo $this->rerp->convertMoney( $price_range->max ); ?></span>
                    </span>
					<input type="text" name="price_range" class="price-range price-slider" value="<?php echo $priceRange ?>" data-min="<?php echo round( $price_range->min, 2 ); ?>" data-max="<?php echo round( $price_range->max, 2 ); ?>" data-value="[<?php echo $priceRange; ?>]">
				</div>
				<!-- /.price-range-holder -->
			</div>
			<!-- /.sidebar-widget-body -->

			<!-- /.sidebar-widget -->
			<!-- ============================================== PRICE SILDER : END ============================================== -->

			<!-- ==================================== STOCK AVAILABILITY  ===================================== -->

			<div class="widget-header">
				<h4 class="widget-title"><?= lang('availability'); ?></h4>
			</div>
			<div class="sidebar-widget-body m-t-10">
				<div class="checkbox"><label><input type="checkbox" id="in-stock"><span> <?= lang('in_stock'); ?></span></label></div>
			</div>
			<!-- /.sidebar-widget-body -->
			<div class="widget-header">
				<h4 class="widget-title"><?= lang('featured'); ?></h4>
			</div>
			<div class="sidebar-widget-body m-t-10">
				<div class="checkbox"><label><input type="checkbox" id="featured"<?= $this->input->get('featured') == 'yes' ? ' checked' : ''; ?>><span> <?= lang('featured'); ?></span></label></div>
			</div>
			<?php if ( $isPromo ) { ?>
				<div class="widget-header">
					<h4 class="widget-title"><?= lang( 'promotions' ); ?></h4>
				</div>
				<div class="sidebar-widget-body m-t-10">
					<div class="checkbox"><label><input type="checkbox" id="promotions" <?= $this->input->get('promo') == 'yes' ? ' checked' : ''; ?>><span> <?= lang( 'promotions' ); ?></span></label></div>
				</div>
				<!-- /.sidebar-widget-body -->
			<?php } ?>
			<?php if ( $isCashBack ) { ?>
				<div class="widget-header">
					<h4 class="widget-title"><?= lang( 'cashbacks' ); ?></h4>
				</div>
				<div class="sidebar-widget-body m-t-10">
					<div class="checkbox"><label><input type="checkbox" id="cashbacks" <?= $this->input->get( 'cashback' ) == 'yes' ? ' checked' : ''; ?>><span> <?= lang( 'cashbacks' ); ?></span></label></div>
				</div>
				<!-- /.sidebar-widget-body -->
			<?php } ?>
			<!-- /.sidebar-widget -->
			<!-- ===================================== STOCK AVAILABILITY : END  ============================== -->
		</div>
	</div>
</div>
