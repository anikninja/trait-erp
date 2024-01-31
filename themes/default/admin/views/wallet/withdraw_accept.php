<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @var $user Erp_User
 * @var $wdl Erp_Wallet_Withdraw
 * @var $statuses array
 */
?>
<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
			</button>
			<h4 class="modal-title" id="myModalLabel"><?= sprintf( lang('wallet_withdrawal_request_for_x'), $user->getDisplayName() ); ?></h4>
		</div>
		<?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
		echo admin_form_open_multipart('wallet/withdrawal_accept/' . $wdl->getId(), $attrib); 
		?>
		<div class="modal-body">
			<p><?= lang('accept_wallet_withdrawal_request'); ?></p>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<?= lang('date', '_date'); ?>
						<?= form_input('date', $wdl->getRequestDate(), 'class="form-control _datetime" id="_date" required="required" readonly'); ?>
					</div>
					<div class="form-group">
						<?= lang('type', 'type'); ?>
						<?= form_dropdown('type', $types, $wdl->getType(), 'class="form-control" id="type" required="required" style="width:100%;" '. readonly_disabled($wdl->getStatus() == 'approved', true, false ) ); ?>
					</div>
					<div class="form-group">
						<?= lang('amount', 'amount'); ?>
						<?= form_input( 'amount', $wdl->getAmount(), 'class="form-control" id="amount" required="required" readonly'); ?>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
						<?= lang('wd_reference_no', 'wd_reference_no'); ?>
						<?= form_input('wd_reference_no', $wdl->getReferenceNo(), 'class="form-control tip" id="wd_reference_no" readonly diabled'); ?>
					</div>
					<div class="form-group">
						<?= lang('status', 'status'); ?>
						<?= form_dropdown('status', $statuses, $wdl->getStatus(), 'class="form-control" id="status" required="required" style="width:100%;" '. readonly_disabled($wdl->getStatus() == 'approved', true, false ) ); ?>
					</div>
					<div class="form-group">
						<?= lang('note', 'note'); ?>
						<?= form_textarea('note', $wdl->getDescription(), 'class="form-control" id="note"'); ?>
					</div>
				</div>
				
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('payment_details', 'payment_details'); ?>
						<?= form_textarea('payment_details', $wdl->getPaymentDetail(), 'class="form-control" id="payment_details" required="required"'); ?>
					</div>
					
					<div class="form-group">
						<?php if ( $wdl->getStatus() != 'approved' ) { ?>
						<?= lang('attachment', 'attachment') ?>
						<input id="attachment" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
						<?php } ?>
						<?php if ( $wdl->getAttachment() ) { ?>
							<p>
								<code>Uploading another attachment will delete current file.</code>
							</p>
							<a href="<?= admin_url( 'welcome/download/' . $wdl->getAttachment() ); ?>" class="btn btn-primary"><?= lang( 'download' ); ?></a>
						<?php } ?>
					</div>
				</div>
			</div>
		
		</div>
		<div class="modal-footer">
			<?php
			if ( $wdl->getStatus() !== 'approved' ) {
				echo form_submit('withdrawal_accept', lang( 'update' ), 'class="btn btn-primary"');
			}
			?>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
