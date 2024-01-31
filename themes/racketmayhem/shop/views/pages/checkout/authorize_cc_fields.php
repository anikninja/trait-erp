<?php defined( 'BASEPATH' ) or exit( 'No direct script access allowed' ); ?>
<div class="pcc_1">
	<input type="hidden" id="cc_type" value="">
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
				<label for="name_on_card" class=""><?= lang( 'cc_holder' ); ?></label>
				<input name="cc_holder" type="text" id="name_on_card" class="form-control" placeholder="<?= lang( 'cc_holder' ); ?>" required>
			</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
				<label for="card_number" class=""><?= lang( 'cc_no' ); ?></label>
				<input name="cc_no" type="text" id="card_number" class="form-control" placeholder="<?= lang( 'cc_no' ); ?>" required>
				<svg id="ccicon" class="ccicon" width="750" height="471" viewBox="0 0 750 471" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"></svg>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="card_expiry" class=""><?= lang( 'expiration_date' ); ?></label>
				<input name="card_expiry" type="text" id="card_expiry" class="form-control" placeholder="mm/yy" required>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="security" class=""><?= lang( 'security_code' ); ?></label>
				<input name="security" type="text" id="security" class="form-control" placeholder="<?= lang( 'cvv2' ); ?>" required>
			</div>
		</div>
	</div>
</div>
