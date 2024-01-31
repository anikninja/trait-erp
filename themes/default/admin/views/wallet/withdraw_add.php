<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @var $user Erp_User
 * @var $wallet Erp_Wallet
 * @var $statuses array
 */
?>
<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>
			<h4 class="modal-title" id="myModalLabel"><?= sprintf( lang('wallet_withdrawal_request_for_x'), $user->getDisplayName() ); ?></h4>
		</div>
		<?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
		echo admin_form_open_multipart('wallet/withdrawal_add/' . $user->getId(), $attrib); ?>
		<div class="modal-body">
			<p><?= lang('create_wallet_withdrawal_request'); ?></p>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<?= lang('date', '_date'); ?>
						<?= form_input('date', date( 'Y-m-d H:i:s' ), 'class="form-control _datetime" id="_date" required="required" readonly'); ?>
					</div>
					<div class="form-group">
						<?= lang('type', 'type'); ?>
						<?= form_dropdown('type', $types, 'applied', 'class="form-control" id="type" style="width:100%;" required'); ?>
					</div>
					<div class="form-group">
						<?= lang('amount', 'amount'); ?>
						<?= form_input( 'amount', $wallet->getAmount(), 'class="form-control" id="amount" disabled readonly required'); ?>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
						<?= lang('wd_reference_no', 'wd_reference_no'); ?>
						<?= form_input('wd_reference_no', '', 'class="form-control tip" id="wd_reference_no" disabled readonly'); ?>
					</div>
					<div class="form-group">
						<?= lang('status', 'status'); ?>
						<?= form_dropdown('status', $statuses, 'applied', 'class="form-control" id="status" disabled readonly style="width:100%;"'); ?>
					</div>
					<div class="form-group">
						<?= lang('note', 'note'); ?>
						<?= form_textarea('note', '', 'class="form-control" id="note"'); ?>
					</div>
				</div>
				
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('payment_details', 'payment_details'); ?>
						<?= form_textarea('payment_details', '', 'class="form-control" id="payment_details" required="required"'); ?>
					</div>
				</div>
			</div>
		
		</div>
		<div class="modal-footer">
			<?= form_submit('withdrawal_add', lang('withdraw_add'), 'class="btn btn-primary"'); ?>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
