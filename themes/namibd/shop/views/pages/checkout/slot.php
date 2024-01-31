<div class="checkout-step-header-wrap">
    <div class="checkout-step-header"><?= lang( 'delivery_slot' ); ?></div>
</div>
<div class="panel panel-default checkout-step-03">
    <div class="checkout-step-content-wrap">
        <div class="row delivery-slot-wrap">
            <div class="deliver-slot-form">
                <div class="col-md-4"><input id="delivery-slot-date" type="text" class="form-control" value="<?= date( 'Y-m-d'); ?>"><i class="fa fa-calendar"></i></div>
                <div class="col-md-8">
                    <div class="selected-slot"><?= lang( 'selected_slot' ); ?><span>&mdash;</span></div>
	                <input type="hidden" id="delivery_schedule" name="delivery_schedule" value="">
	                <input type="hidden" id="delivery_area" name="delivery_area" value="">
                </div>
            </div>
            <div class="delivery-slot-container"></div>
	        <script type="text/html" id="delivery-slots-tmpl">
		        <div class="slots-dates">
			        <div class="col-md-12">
			            <div class="h3 date-label"></div>
		            </div>
			        <div class="slots"></div>
		        </div>
	        </script>
	        <script type="text/html" id="delivery-slot-tmpl">
		        <div class="delivery-slot col-sm-6 col-md-4 delivery-slot-1">
			        <label>
				        <input type="radio" name="delivery_slot" required>
				        <div class="checkout-page-content">
					        <div class="content-header"></div>
					        <p class="content"></p>
				        </div>
				        <input type="radio" name="delivery_date" required>
			        </label>
		        </div>
	        </script>
        </div>
    </div>
</div>
<!-- checkout-step-03  -->
