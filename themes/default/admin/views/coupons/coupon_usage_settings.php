<?php defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );
/**
 * @var $assets string
 * @var $modal_js string
 * @var $dp_lang string
 * @var $coupon Erp_Coupon
 */
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title"
                id="myModalLabel"><?= sprintf( lang( 'edit_x' ), lang( 'coupon' ) ) . ': ' . $coupon->getCouponCode(); ?></h4>
        </div>
		<?php $attrib = [ 'data-toggle' => 'validator', 'role' => 'form' ];
		echo admin_form_open_multipart( 'coupons/edit_coupon_usage/' . $coupon->getId(), $attrib ); ?>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group all">
						<?= lang( 'minimum_spend', 'minimum_spend' ); ?>
						<?= form_input( 'minimum_spend', $coupon->getMinimumSpend(), 'class="form-control tip" id="minimum_spend"' ); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group all">
						<?= lang( 'maximum_spend', 'maximum_spend' ); ?>
						<?= form_input( 'maximum_spend', $coupon->getMaximumSpend(), 'class="form-control tip" id="maximum_spend"' ); ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="notify"><?= lang( 'exclude_sale_items' ) ?></label>
                <label class="checkbox" for="notify">
                    <input type="checkbox" name="exclude_sale_items" id="exclude_sale_items" <?= ( $coupon->getExcludeSaleItems() ) ? 'checked="checked"' : NULL; ?> />
                    <span style="font-weight: normal">Coupons do not work if a sale item is added afterward.</span>
                </label>
            </div>

            <div class="form-group all">
                <?= lang( 'products', 'products' ); ?>
                <div class="form-group">
					<?php echo form_input( 'products', '', 'class="" id="products" placeholder="' . lang( 'select' ) . ' ' . lang( 'products' ) . '" style="width:100%;"' ); ?>
                </div>
                <span style="font-weight: normal">Products that the coupon will be applied to, or that need to be in the cart in order for the fixed or percentage discount to be applied.</span>
            </div>

            <div class="form-group all">
				<?= lang( 'exclude_products', 'exclude_products' ); ?>
				<?= form_input( 'exclude_products', '', 'class="" id="exclude_products" placeholder="' . lang( 'select' ) . ' ' . lang( 'products' ) . '" style="width:100%;"' ); ?>
                <span style="font-weight: normal">Products that the coupon will not be applied to, or that cannot be in the cart in order for the “Fixed cart discount” to be applied.</span>
            </div>

            <div class="form-group all">
				<?= lang( 'product_categories', 'product_categories' ); ?>
	            <?= form_input( 'product_categories', '', 'class="" id="product_categories" placeholder="' . lang( 'select' ) . ' ' . lang( 'category' ) . '" style="width:100%;"' ); ?>
                <span style="font-weight: normal">Product categories that the coupon will be applied to, or that need to be in the cart in order for the fixed or percentage discount to be applied.</span>
            </div>

            <div class="form-group all">
				<?= lang( 'exclude_categories', 'exclude_categories' ); ?>
				<?= form_input( 'exclude_categories', '', 'class="" id="exclude_categories" placeholder="' . lang( 'select' ) . ' ' . lang( 'category' ) . '" style="width:100%;"' ); ?>
                <span style="font-weight: normal">Product categories that the coupon will not be applied to, or that cannot be in the cart in order for the “Fixed cart discount” to be applied.</span>
            </div>

            <div class="form-group all">
				<?= lang( 'allowed_emails', 'allowed_emails' ); ?>
				<?= form_input( 'allowed_emails', '', 'class="" id="allowed_emails" autocomplete="off" placeholder="' . lang( 'select' ) . ' ' . lang( 'emails' ) . '" style="width:100%;"' ); ?>
                <span style="font-weight: normal">Email address or addresses that can use a coupon. Verified against customer’s billing email.</span>
            </div>

            <div class="form-group all">
				<?= lang( 'usage_limit_per_coupon', 'usage_limit_per_coupon' ); ?>
				<?= form_input( 'usage_limit_per_coupon', $coupon->getUsageLimitPerCoupon(), 'class="form-control tip" id="usage_limit_per_coupon"' ); ?>
                <span style="font-weight: normal">How many times a coupon can be used by all customers before being invalid.</span>
            </div>

            <div class="form-group all">
				<?= lang( 'limit_usage_to_x_items', 'limit_usage_items' ); ?>
				<?= form_input( 'limit_usage_items', $coupon->getLimitUsageItems(), 'class="form-control tip" id="limit_usage_items"' ); ?>
                <span style="font-weight: normal">How many items the coupon can be applied to before being invalid. This field is only displayed if there is one or more products that the coupon can be used with, and is configured under the Usage Restrictions.</span>
            </div>

            <div class="form-group all">
				<?= lang( 'usage_limit_per_user', 'usage_limit_per_user' ); ?>
				<?= form_input( 'usage_limit_per_user', $coupon->getUsageLimitPerUser(), 'class="form-control tip" id="usage_limit_per_user"' ); ?>
                <span style="font-weight: normal">How many times a coupon can be used by each customer before being invalid for that customer.</span>
            </div>
        </div>
        <div class="modal-footer">
			<?php echo form_submit( 'update_coupon', lang( 'update_coupon' ), 'class="btn btn-primary"' ); ?>
        </div>
		<?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript" src="<?= $assets; ?>js/custom.js"></script>
<script type="text/javascript" charset="UTF-8">
    $.fn.datetimepicker.dates['rerp'] = <?= $dp_lang; ?>;
</script>
<?= $modal_js ?>
<script type="text/javascript" charset="UTF-8">
    $(document).ready(function () {
        $.fn.datetimepicker.dates['rerp'] = <?= $dp_lang; ?>;
    });
</script>
<!--suppress ES6ConvertVarToLetConst -->
<script>
    var select_items = JSON.parse('<?= $coupon->getProductSelect2(); ?>'),
        exclude_products = JSON.parse('<?= $coupon->getExcludeProductsSelect2(); ?>'),
        category_items = JSON.parse('<?= $coupon->getProductCategoriesSelect2(); ?>'),
        ex_category_items = JSON.parse('<?= $coupon->getExcludeCategoriesSelect2(); ?>'),
        selected_allowed_emails = JSON.parse('<?= $coupon->getAllowedEmailsSelect2(); ?>');
    $(document).ready(function () {
        var ajaxOptions = {
                url: site.base_url + "coupons/productSuggestions",
                dataType: 'json',
                quietMillis: 15,
                data: function ( term ) {
                    return {
                        term: term,
                        limit: 10
                    };
                },
                results: function ( data ) {
                    if (data.length > 0) {
                        return {results: data};
                    } else {
                        return {
                            results: [
                                {
                                    id: '',
                                    text: 'No Match Found',
                                    disabled: true
                                }
                            ]
                        };
                    }
                }
            },
            selectOptions = {
                minimumInputLength: 1,
                //tags: products,
                tokenSeparators: [","],
                multiple: true,
                data: [],
                initSelection: function (element, callback) {
                    element.val('');
                    callback(select_items);
                },
                ajax: ajaxOptions
            },
            products = $('#products'),
            exclude_pro = $('#exclude_products'),
            product_categories = $('#product_categories'),
            exclude_categories = $('#exclude_categories'),
            allowed_emails = $('#allowed_emails');

        // products ajax search
        products.select2(selectOptions);
        if (select_items.length) {
            products.val('-').trigger('change');
        }

        // exclude_products ajax search
        exclude_pro.select2($.fn.extend({}, selectOptions, {
            initSelection: function (element, callback) {
                element.val('');
                callback(exclude_products);
            },
        }));
        if (exclude_products.length) {
            exclude_pro.val('-').trigger('change');
        }

        // categorySuggestions ajax search
        product_categories.select2($.fn.extend({}, selectOptions, {
            initSelection: function (element, callback) {
                element.val('');
                callback(category_items);
            },
            ajax: $.fn.extend({}, ajaxOptions, {
                url: site.base_url + "coupons/categorySuggestions",
            })
        }));
        if (category_items.length) {
            product_categories.val('-').trigger('change');
        }

        // exclude categorySuggestions ajax search
        exclude_categories.select2($.fn.extend({}, selectOptions, {
            initSelection: function (element, callback) {
                element.val('');
                callback(ex_category_items);
            },
            ajax: $.fn.extend({}, ajaxOptions, {
                url: site.base_url + "coupons/categorySuggestions",
            })
        }));
        if (ex_category_items.length) {
            exclude_categories.val('-').trigger('change');
        }

        // allowed_emails ajax search
        allowed_emails.select2($.fn.extend({}, selectOptions, {
            initSelection: function (element, callback) {
                element.val('');
                callback(selected_allowed_emails);
            },
            ajax: $.fn.extend({}, ajaxOptions, {
                url: site.base_url + "coupons/emailSuggestions",
            })
        }));
        if (selected_allowed_emails.length) {
            allowed_emails.val('-').trigger('change');
        }
    });
</script>
