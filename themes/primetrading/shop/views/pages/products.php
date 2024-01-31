<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include 'breadcrumb.php'; ?>
<section class="page-contents product-page">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
	                <?php
	                $priceRange = ( $filters['min_price'] ?? $price_range->min ) . ',' . ( $filters['max_price'] ?? $price_range->max );
                    if ( ! ci_is_mobile() ) {
                    ?>
                    <div class='col-xs-12 col-sm-12 col-md-3 sidebar products-page-sidebar'>
                        <?php include 'category-widget.php'; ?>
                        <?php include 'brand-widget.php'; ?>
                        <div class="sidebar-module-container">
                            <div class="sidebar-filter">
                                <!-- ============================================== SIDEBAR CATEGORY ============================================== -->
                                <h3 class="section-title"><?= lang( 'shop_by' ); ?></h3>
                                <div class="sidebar-widget">
                                    <div class="widget-header">
                                        <h4 class="widget-title"><?= lang( 'price_range' ); ?></h4>
                                    </div>
                                    <div class="sidebar-widget-body m-t-10">
                                        <div class="price-range-holder">
	                                        <span class="min-max">
		                                        <span class="pull-left"><?php echo $this->rerp->convertMoney( $price_range->min ); ?></span>
		                                        <span class="pull-right"><?php echo $this->rerp->convertMoney( $price_range->max ); ?></span>
	                                        </span>
                                            <input type="text" id="price-range" name="price_range" class="price-slider" value="<?php echo $priceRange ?>" data-min="<?php echo round( $price_range->min, 2 ); ?>" data-max="<?php echo round( $price_range->max, 2 ); ?>" data-value="[<?php echo $priceRange; ?>]">
                                        </div>
                                        <!-- /.price-range-holder -->
                                    </div>
                                    <!-- /.sidebar-widget-body -->
                                    <div class="widget-header">
                                        <h4 class="widget-title"><?= lang('availability'); ?></h4>
                                    </div>
                                    <div class="sidebar-widget-body">
                                        <div class="checkbox"><label><input type="checkbox" id="in-stock"><span> <?= lang('in_stock'); ?></span></label></div>
                                    </div>
                                    <!-- /.sidebar-widget-body -->
                                    <div class="widget-header">
                                        <h4 class="widget-title"><?= lang('featured'); ?></h4>
                                    </div>
                                    <div class="sidebar-widget-body">
                                        <div class="checkbox"><label><input type="checkbox" id="featured"<?= $this->input->get('featured') == 'yes' ? ' checked' : ''; ?>><span> <?= lang('featured'); ?></span></label></div>
                                    </div>
                                    <div class="widget-header">
                                        <h4 class="widget-title"><?= lang('promotions'); ?></h4>
                                    </div>
                                    <div class="sidebar-widget-body">
                                        <div class="checkbox"><label><input type="checkbox" id="promotions"<?= $this->input->get('promo') == 'yes' ? ' checked' : ''; ?>><span> <?= lang('promotions'); ?></span></label></div>
                                    </div>
                                    <!-- /.sidebar-widget-body -->
                                <!-- /.sidebar-widget -->
                                </div>
                            </div>
                            <!-- /.sidebar-filter -->
                        </div>
                        <!-- /.sidebar-module-container -->
                    </div>
	                <?php } ?>
                    <div class="col-xs-12 col-sm-12 col-md-9 rht-col">
	                    <?php
	                    if ( ci_is_mobile() ) {
		                    include __DIR__ . '/mobile-filters.php';
	                    }
	                    ?>
	                    <div class="clearfix filters-container hidden-xs">
                            <div class="row">
                                <div class="col col-sm-5 hidden-xs">
                                    <div class="no-padding">
                                        <div class="lbl-cnt">
                                            <label for="sorting" class="sr-only">Sort by</label>
	                                        <div class="sorting inline">
		                                        <select name="sorting" id="sorting" class="chosen-select">
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
                                            <!-- /.inline -->
                                        </div>
                                        <!-- /.lbl-cnt -->
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <div class="col col-sm-7 col-xs-12 text-right">
                                    <span class="page-info"></span>
                                    <!-- /.page-info -->
                                    <div class="page-pagination pagination-container">
                                    </div>
                                    <!-- /.pagination-container --> </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <div class="search-result-container category-list">
	                        <div class="row">
		                        <div id="results"></div>
	                        </div>
	                        <!-- /.row -->
                            <!-- /.category-product -->
                            <div class="clearfix filters-container bottom-row">
                                <div class="text-right">
                                    <div class="page-pagination pagination-container">
                                    </div>
                                    <!-- /.pagination-container -->
                                </div>
                                <!-- /.text-right -->

                            </div>
                            <!-- /.filters-container -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
