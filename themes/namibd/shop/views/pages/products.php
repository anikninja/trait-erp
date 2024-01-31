<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<section class="page-contents product-page">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">

                <div class="row">
                    <div class='col-xs-12 col-sm-12 col-md-3 sidebar hidden-xs'>
                        <?php include 'category-widget.php'; ?>
                        <div class="sidebar-module-container">
                            <div class="sidebar-filter">
                                <!-- ============================================== SIDEBAR CATEGORY ============================================== -->
                                <div class="sidebar-widget">
                                    <h3 class="section-title">Shop by</h3>

                                <!-- /.sidebar-widget -->
                                <!-- ============================================== SIDEBAR CATEGORY : END ============================================== -->
								<?php $priceRange = ( $filters['min_price'] ?? $price_range->min ) . ',' . ( $filters['max_price'] ?? $price_range->max ); ?>
                                <!-- ============================================== PRICE SILDER============================================== -->

                                    <div class="widget-header">
                                        <h4 class="widget-title">Price Range</h4>
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

                                <!-- /.sidebar-widget -->
                                <!-- ============================================== PRICE SILDER : END ============================================== -->

                                <!-- ==================================== STOCK AVAILABILITY  ===================================== -->

                                    <div class="widget-header">
                                        <h4 class="widget-title"><?= lang('availability'); ?></h4>
                                    </div>
                                    <div class="sidebar-widget-body m-t-10">
                                        <div class="checkbox">
	                                        <label>
		                                        <input type="checkbox" class="filter" id="in-stock">
		                                        <span><?= lang('in_stock'); ?></span>
	                                        </label>
                                        </div>
                                    </div>
                                    <!-- /.sidebar-widget-body -->
                                    <div class="widget-header">
                                        <h4 class="widget-title"><?= lang('featured'); ?></h4>
                                    </div>
                                    <div class="sidebar-widget-body m-t-10">
                                        <div class="checkbox">
	                                        <label>
		                                        <input type="checkbox" class="filter" id="featured" <?= $this->input->get('featured') == 'yes' ? ' checked' : ''; ?>>
		                                        <span> <?= lang('featured'); ?></span>
	                                        </label>
                                        </div>
                                    </div>
	                                <?php if ( $isPromo ) { ?>
                                        <div class="widget-header">
                                            <h4 class="widget-title"><?= lang('promotions'); ?></h4>
                                        </div>
                                        <div class="sidebar-widget-body m-t-10">
                                            <div class="checkbox">
	                                            <label>
		                                            <input type="checkbox" class="filter" id="promotions" <?= $this->input->get('promo') == 'yes' ? ' checked' : ''; ?>>
		                                            <span><?= lang( 'promotions' ); ?></span>
	                                            </label>
                                            </div>
                                        </div>
                                        <!-- /.sidebar-widget-body -->
	                                <?php } ?>
	                                <?php if ( $isCashBack ) { ?>
		                                <div class="widget-header">
			                                <h4 class="widget-title"><?= lang( 'cashbacks' ); ?></h4>
		                                </div>
		                                <div class="sidebar-widget-body m-t-10">
			                                <div class="checkbox">
				                                <label>
					                                <input type="checkbox" class="filter" id="cashbacks" <?= $this->input->get( 'cashback' ) == 'yes' ? ' checked' : ''; ?>>
					                                <span><?= lang( 'cashbacks' ); ?></span>
				                                </label>
			                                </div>
		                                </div>
		                                <!-- /.sidebar-widget-body -->
	                                <?php } ?>
                                <!-- /.sidebar-widget -->
                                <!-- ===================================== STOCK AVAILABILITY : END  ============================== -->
                                </div>
                            </div>
                            <!-- /.sidebar-filter -->
                        </div>
                        <!-- /.sidebar-module-container -->
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-9 rht-col">
                        <?php include __DIR__ . '/mobile-filters.php'; ?>
                        <div class="clearfix filters-container hidden-xs">
                            <div class="row">
                                <div class="col col-sm-12 col-md-8 col-lg-8 hidden-sm">
                                    <div class="col col-sm-6 col-md-12 col-lg-6 no-padding">
                                        <div class="lbl-cnt">
                                            <span class="lbl">Sort by</span>
                                            <div class="fld inline">
                                                <div class="dropdown dropdown-small dropdown-med dropdown-white inline">
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
                                            <!-- /.fld -->
                                        </div>
                                        <!-- /.lbl-cnt -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col col-sm-6 col-md-6 col-lg-6 no-padding hidden-sm hidden-md">
                                        <span class="page-info"></span>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <div class="col col-sm-6 col-md-4 col-xs-12 col-lg-4 hidden-sm text-right">
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
