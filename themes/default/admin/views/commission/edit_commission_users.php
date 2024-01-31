<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @var $group Erp_Commission_Group
 * @var $refCom Erp_Referral_Commission
 * @var $shopperCom Erp_Shopper_Commission
 * @var $shopper_com bool
 */
$refCom = $group->getReferralCommission();
$shopperCom = $group->getShopperCommission();
?>
<div class="form-group">
	<div class="col-md-12 table-responsive">
		<table class="table table-bordered table-hover table-striped reports-table">
			<thead>
			<tr>
				<th><?= lang( 'user' ); ?></th>
				<th><?= sprintf( lang( 'x_rate' ), lang( 'commission' ) ); ?></th>
				<th><?= lang( 'note' ); ?></th>
				<th><?= lang( 'status' ); ?></th>
				<th><i class="fa fa-trash-o"></i></th>
			</tr>
			</thead>
			<tbody>
			<tr id="referral-commission">
				<th><label for="ref-rate"><?= lang( 'referral_commission' ); ?></label></th>
				<td>
					<input class="form-control" id="ref-rate" type="number" name="referral[rate]" step="0.1" min="0" max="100" value="<?= $refCom->getRate(); ?>" required>
				</td>
				<td>
					<label for="ref-note" class="sr-only"><?= lang( 'note' ); ?></label>
					<input class="form-control" type="text" id="ref-note" name="referral[note]" value="<?= $refCom->getDescription(); ?>">
				</td>
				<td class="text-center">
					<label for="ref-is_enabled" class="sr-only"><?= lang( 'status' ); ?></label>
					<select name="referral[is_enabled]" class="form-control select" id="ref-is_enabled" style="width:90px" required>
						<option value="0"<?php selected( $refCom->getIsEnabled(), 1); ?>><?= lang( 'inactive' ); ?></option>
						<option value="1"<?php selected( $refCom->getIsEnabled(), 1); ?>><?= lang( 'active' ); ?></option>
					</select>
				</td>
				<td></td>
			</tr>
			<?php if( $shopper_com ) { ?>
			<tr id="shopper-commission">
				<th><label for="shopper-rate"><?= lang( 'shopper_commission' ); ?></label></th>
				<td>
					<input class="form-control" id="shopper-rate" type="number" name="shopper[rate]" step="0.1" min="0" max="100" value="<?= $shopperCom->getRate(); ?>" required>
				</td>
				<td>
					<label for="shopper-note" class="sr-only"><?= lang( 'note' ); ?></label>
					<input class="form-control" type="text" id="shopper-note" name="shopper[note]" value="<?= $shopperCom->getDescription(); ?>">
				</td>
				<td class="text-center">
					<label for="shopper-is_enabled" class="sr-only"><?= lang( 'status' ); ?></label>
					<select name="shopper[is_enabled]" class="form-control select" id="shopper-is_enabled" style="width:90px" required>
						<option value="0"<?php selected( $shopperCom->getIsEnabled(), 1); ?>><?= lang( 'inactive' ); ?></option>
						<option value="1"<?php selected( $shopperCom->getIsEnabled(), 1); ?>><?= lang( 'active' ); ?></option>
					</select>
				</td>
				<td></td>
			</tr>
			<?php } ?>
			<?php
			foreach ( $group->getUsers() as $idx => $comUser ) {
				if ( ! $comUser->getUser() ) {
					continue;
				}
				$user = $comUser->getUser();
				$name = $user->getDisplayName();
				?>
				<tr id="ugc-<?= $comUser->getId(); ?>">
					<th>
						<a href="<?= admin_url( '/auth/profile/' . $user->getId() )?>">
							<span style="background: transparent;width: 44px;height: 44px;padding: 0;border-radius: 100%;border: transparent;overflow: hidden;">
								<?= $user->getAvatarImage( true, [ 'class' => 'img-responsive' ] ); ?>
							</span>
							<span style="line-height: 44px;vertical-align: text-bottom;"><?= $name; ?></span>
						</a>
					</th>
					<td>
						<label for="user-com-rate-<?= $comUser->getId(); ?>" class="sr-only"><?= sprintf( lang( 'x_rate' ), lang( 'commission' ) ); ?></label>
						<input type="hidden" name="users[<?= $idx; ?>][id]" value="<?= $comUser->getId(); ?>">
						<input type="hidden" name="users[<?= $idx; ?>][user_id]" value="<?= $comUser->getUserId(); ?>">
						<input class="form-control" type="number" id="user-com-rate-<?= $comUser->getId(); ?>" name="users[<?= $idx; ?>][rate]" step="0.1" min="0" max="100" value="<?= $comUser->getRate(); ?>" required>
					</td>
					<td>
						<label for="user-com-note-<?= $comUser->getId(); ?>" class="sr-only"><?= lang( 'note' ); ?></label>
						<input class="form-control" type="text" id="user-com-note-<?= $comUser->getId(); ?>" name="users[<?= $idx; ?>][note]" value="<?= $comUser->getDescription(); ?>">
					</td>
					<td class="text-center">
						<label for="<?= $comUser->getId(); ?>-is_enabled" class="sr-only"><?= lang( 'status' ); ?></label>
						<select name="users[<?= $idx; ?>][is_enabled]" class="form-control select" id="<?= $comUser->getId(); ?>-is_enabled" style="width:90px" required>
							<option value="0"<?php selected( $comUser->getIsEnabled(), 1); ?>><?= lang( 'inactive' ); ?></option>
							<option value="1"<?php selected( $comUser->getIsEnabled(), 1); ?>><?= lang( 'active' ); ?></option>
						</select>
					</td>
					<td>
						<div class="text-center">
							<a href="#" class="tip user-delete" title="<?= sprintf( lang( 'remove_x'), $name ) ?>"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
						</div>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="4">
					<a href="<?= admin_url( 'commission/add_user/' . $group->getId() ); ?>"data-toggle="modal" data-target="#myModal" class="pull-right btn btn-info add"><i class="fa fa-plus"></i> <?= lang( 'add_user' ); ?></a>
				</td>
			</tr>
			</tfoot>
		</table>
	</div>
</div>
<script>(function($){
	var __CSRF__ = {
		"<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>"
	};
	
	$(document).on( 'click', '.user-delete', function( e ) {
		e.preventDefault();
		var row = $(this).closest( 'tr' ),
			id = row.attr('id').replace( 'ugc-', '' );
		$.post( '<?= admin_url( 'commission/delete_group_user/' ); ?>', $.extend( { 'group_id': <?= $group->getId(); ?>, 'user_com_id': id }, __CSRF__ ), function( resp ) {
			if ( ! resp.error ) {
				row.remove();
			}
			alert( resp.msg );
		} );
	} );
})(jQuery);</script>
