<!-- checkout-step-04  -->
<div class="panel panel-default checkout-step-03">
	<div class="checkout-step-header-wrap">
		<div class="checkout-step-header">
			<div class="step-num"><?= $step; ?></div>
			<?= lang('comment_any', 'comment'); ?>
		</div>
	</div>
	<div class="checkout-step-content-wrap">
		<div class="comment-wrap">
			<?= form_textarea('comment', set_value('comment'), 'class="form-control" id="comment" style="height:100px;"'); ?>
		</div>
	</div>
</div>
<!-- checkout-step-04  -->
