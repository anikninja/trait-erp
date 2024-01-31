<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @var $user Erp_User
 * @var $wallet Erp_Wallet
 * @var $commission_settings array
 * @var $can_withdraw bool
 * @var $has_pending_request bool
 */
?>
<script>
    $(document).ready(function () {
        oTable = $('#CategoryTable').dataTable({
            "aaSorting": [[1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('wallet/geMyWalletDetails/'.$user->getId() ) ?>',
            'fnServerData': function ( sSource, aoData, fnCallback ) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax( { 'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback } );
            },
	        aoColumns: [
		        { mRender: fld },
		        null,
		        { mRender: currencyFormat },
		        { mRender: currencyFormat },
	        ],
        });
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('my_wallet_details');?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h2 class="blue" style="margin-top:0"><?= lang( 'transaction_summery' ); ?></h2>
	            <p class="introtext" style="margin: -20px 0 20px 0;"></p>
                <div class="panel-group">
	                <div class="panel panel-default">
                        <div class="panel-heading text-bold"><?php echo lang( 'my_wallet_balance' ); ?></div>
                        <div class="checkout-step-content-wrap">
                            <div class="panel-body">
	                            <?php
	                            if ( $wallet->has_pending_withdrawal() ) {
		                            bs_alert( lang( 'your_withdrawal_request_is_pending' ), 'info', false );
	                            }
	                            ?>
                                <div class="row">
	                                <div class="col-md-6">
		                                <p><?= sprintf( lang( 'your_balance_x' ), $this->rerp->convertMoney( $wallet->getAmount() ) ); ?></p>
		                                <?php if( $commission_settings['minimum_withdrawal'] > 0 ) { ?>
			                                <p class="small align-right"><code><?= sprintf( lang( 'minimum_withdrawal_amount_is_x' ), $this->rerp->convertMoney( $commission_settings['minimum_withdrawal'] ) ); ?></code></p>
		                                <?php } ?>
	                                </div>
	                                <?php if( $can_withdraw ) { ?>
	                                <div class="col-md-6">
		                                <a href="<?= admin_url( 'wallet/withdrawal_add/' . $user->getId()  ); ?>" style="text-align: left;" class='btn btn-primary pull-right' data-toggle="modal" data-target="#myModal"><i class="fa fa-money"></i> <?= lang( 'withdrawal_request' ); ?></a>
	                                </div>
	                                <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
	        <div class="clearfix"></div>
	        <div class="col-md-12">
		        <h2 class="blue"><?= lang( 'transaction_history' ); ?></h2>
		        <p class="introtext" style="margin: -20px 0 20px 0;"></p>
		        <div class="panel-group">
			        <div class="table-responsive">
				        <table id="CategoryTable" class="table table-bordered table-hover table-striped reports-table">
					        <thead>
					        <tr>
						        <th><?= lang('date'); ?></th>
						        <th><?= lang('description'); ?></th>
						        <th><?= lang('debit'); ?></th>
						        <th><?= lang('credit'); ?></th>
					        </tr>
					        </thead>
					        <tbody>
					        <tr><td colspan="5" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td></tr>
					        </tbody>
				        </table>
			        </div>
		        </div>
	        </div>
        </div>
    </div>
</div>
