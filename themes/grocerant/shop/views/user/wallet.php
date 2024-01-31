<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @var $walletHistory Erp_Transaction_History
 * @var $wallet        Erp_Wallet
 * @var $transactions  Erp_Transaction[]
 * @var $pagination    string
 * @var $this          MY_Shop_Controller
 */


?>
<div class="breadcrumb">
    <div class="container">
        <div class="breadcrumb-inner">
            <ul class="list-inline list-unstyled">
                <li><a href="<?= shop_url() ?>"><?= lang( 'shop' ); ?></a></li>
                <li class='active'><?= lang( 'wallet' ); ?></li>
            </ul>
        </div><!-- /.breadcrumb-inner -->
    </div><!-- /.container -->
</div><!-- /.breadcrumb -->

<div class="body-content">
    <div class="container">
        <div class="checkout-box">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-2">
                    <div class="panel-group">
                        <div class="panel panel-default order-nav-panel">
                            <ul class="orders-nav">
                                <li><a href="<?= site_url('profile'); ?>"><i class="mi fa fa-user"></i> <?= lang('profile'); ?></a></li>
                                <li><a href="<?= shop_url('addresses'); ?>"><i class="mi fa fa-building"></i> <?= lang('addresses'); ?></a></li>
                                <li><a href="<?= site_url('referral'); ?>"><i class="mi fa fa-building"></i> <?= lang('referral'); ?></a></li>
                                <li><a class="active" href="<?= site_url('wallet'); ?>"><i class="mi fa fa-building"></i> <?= lang('wallet'); ?></a></li>
                                <li><a href="<?= shop_url('orders'); ?>"><i class="mi fa fa-heart"></i> <?= lang('orders'); ?></a></li>
                                <li><a href="<?= shop_url('quotes'); ?>"><i class="mi fa fa-heart-o"></i> <?= lang('quotes'); ?></a></li>
                                <li><a href="<?= site_url('logout'); ?>"><i class="mi fa fa-sign-out"></i> <?= lang('logout'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-10">
                    <div class="panel-group">
                        <div class="panel panel-default address-panel">
                            <div class="panel-heading text-bold">
                                My Wallet
                            </div>
                            <!-- .panel-heading -->
                            <div class="checkout-step-content-wrap">
                                <div class="row phone-wrap">
                                    <div class="phone col-sm-6 col-md-4">
                                        <label>
                                            <div class="checkout-page-content">
                                                <div class="content-header"><?= lang( 'total_balance' ); ?></div>
                                                <p class="content"><?php echo $this->rerp->convertMoney( $wallet->getAmount() ); ?></p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
	                <div class="panel-group">
		                <div class="panel panel-default address-panel">
			                <div class="panel-heading text-bold"><?= lang( 'transaction_history' ); ?></div>
			                <div class="table-responsive">
				                <table class="table ordered-products-wrap">
					                <thead>
					                <tr>
						                <th><?= lang( 'transaction_date' ); ?></th>
						                <th><?= lang( 'description' ); ?></th>
						                <th><?= lang( 'debit' ); ?></th>
						                <th><?= lang( 'credit' ); ?></th>
					                </tr>
					                </thead>
					                <tbody>
					                <?php foreach ( $transactions as $transaction ) { ?>
						                <tr>
							                <td><?php echo $transaction->getTransactionDate(); ?></td>
							                <td><?php echo $transaction->getDescription(); ?></td>
							                <td><?php echo $this->rerp->convertMoney( $transaction->getDebit() ); ?></td>
							                <td><?php echo $this->rerp->convertMoney( $transaction->getCredit() ); ?></td>
						                </tr>
					                <?php } ?>
					                </tbody>
					                <tfoot>
					                <tr>
						                <td colspan="4"><?= $pagination ?></td>
					                </tr>
					                </tfoot>
				                </table>
			                </div>
		                </div>
                    </div>
                </div>
            </div><!-- /.row -->
        </div><!-- /.checkout-box -->
    </div><!-- /.container -->
</div><!-- /.body-content -->

<!-- Modal -->
<div class="modal fade" id="checkout-popup" tabindex="-1" role="dialog" aria-labelledby="checkout-popup" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="checkout-popup-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
