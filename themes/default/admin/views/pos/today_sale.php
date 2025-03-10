<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('today_sale'); ?></h4>
        </div>
        <div class="modal-body">
            <table width="100%" class="stable">
                <tr>
                    <td style="border-bottom: 1px solid #EEE;"><h4><?= lang('cash_in_hand'); ?>:</h4></td>
                    <td style="text-align:right; border-bottom: 1px solid #EEE;"><h4>
                            <span><?= $this->rerp->formatMoney($this->session->userdata('cash_in_hand')); ?></span></h4>
                    </td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #EEE;"><h4><?= lang('cash_sale'); ?>:</h4></td>
                    <td style="text-align:right; border-bottom: 1px solid #EEE;"><h4>
                            <span><?= $this->rerp->formatMoney($cashsales->paid ? $cashsales->paid : '0.00') . ' (' . $this->rerp->formatMoney($cashsales->total ? $cashsales->total : '0.00') . ')'; ?></span>
                        </h4></td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #EEE;"><h4><?= lang('ch_sale'); ?>:</h4></td>
                    <td style="text-align:right;border-bottom: 1px solid #EEE;"><h4>
                            <span><?= $this->rerp->formatMoney($chsales->paid ? $chsales->paid : '0.00') . ' (' . $this->rerp->formatMoney($chsales->total ? $chsales->total : '0.00') . ')'; ?></span>
                        </h4></td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #DDD;"><h4><?= lang('cc_sale'); ?>:</h4></td>
                    <td style="text-align:right;border-bottom: 1px solid #DDD;"><h4>
                            <span><?= $this->rerp->formatMoney($ccsales->paid ? $ccsales->paid : '0.00') . ' (' . $this->rerp->formatMoney($ccsales->total ? $ccsales->total : '0.00') . ')'; ?></span>
                        </h4></td>
                </tr>
                <?php if ($pos_settings->paypal_pro) {
    ?>
                    <tr>
                        <td style="border-bottom: 1px solid #DDD;"><h4><?= lang('paypal_pro'); ?>:</h4></td>
                        <td style="text-align:right;border-bottom: 1px solid #DDD;"><h4>
                                <span><?= $this->rerp->formatMoney($pppsales->paid ? $pppsales->paid : '0.00') . ' (' . $this->rerp->formatMoney($pppsales->total ? $pppsales->total : '0.00') . ')'; ?></span>
                            </h4></td>
                    </tr>
                <?php
} ?>
                <?php if ($pos_settings->authorize) {
        ?>
                    <tr>
                        <td style="border-bottom: 1px solid #DDD;"><h4><?= lang('stripe'); ?>:</h4></td>
                        <td style="text-align:right;border-bottom: 1px solid #DDD;"><h4>
                                <span><?= $this->rerp->formatMoney($authorizesales->paid ? $authorizesales->paid : '0.00') . ' (' . $this->rerp->formatMoney($authorizesales->total ? $authorizesales->total : '0.00') . ')'; ?></span>
                            </h4></td>
                    </tr>
                <?php
    } ?>
                <?php if ($pos_settings->stripe) {
        ?>
                    <tr>
                        <td style="border-bottom: 1px solid #DDD;"><h4><?= lang('stripe'); ?>:</h4></td>
                        <td style="text-align:right;border-bottom: 1px solid #DDD;"><h4>
                                <span><?= $this->rerp->formatMoney($stripesales->paid ? $stripesales->paid : '0.00') . ' (' . $this->rerp->formatMoney($stripesales->total ? $stripesales->total : '0.00') . ')'; ?></span>
                            </h4></td>
                    </tr>
                <?php
    } ?>
                <tr>
                    <td width="300px;"><h4 style="font-weight:bold;"><?= lang('total_sales'); ?>:</h4></td>
                    <td width="200px;" style="text-align:right;"><h4 style="font-weight:bold;">
                            <span><?= $this->rerp->formatMoney($totalsales->total ? $totalsales->total : '0.00'); ?></span>
                        </h4></td>
                </tr>
                <tr>
                    <td style="border-top: 1px solid #DDD;"><h4><?= lang('refunds'); ?>:</h4></td>
                    <td style="text-align:right;border-top: 1px solid #DDD;"><h4>
                            <span><?= $this->rerp->formatMoney($refunds->returned ? $refunds->returned : '0.00') . ' (' . $this->rerp->formatMoney($refunds->total ? $refunds->total : '0.00') . ')'; ?></span>
                        </h4></td>
                </tr>
                <tr>
                    <td style="border-top: 1px solid #DDD;"><h4><?= lang('returns'); ?>:</h4></td>
                    <td style="text-align:right;border-top: 1px solid #DDD;"><h4>
                            <span><?= $this->rerp->formatMoney($returns->total ? '-' . $returns->total : 0); ?></span>
                        </h4></td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #DDD;"><h4><?= lang('expenses'); ?>:</h4></td>
                    <td style="text-align:right;border-bottom: 1px solid #DDD;"><h4>
                            <span><?php $expense = $expenses ? $expenses->total : 0; echo $this->rerp->formatMoney($expense) . ' (' . $this->rerp->formatMoney($expense) . ')'; ?></span>
                        </h4></td>
                </tr>
                <tr>
                    <td width="300px;" style="font-weight:bold;"><h4><strong><?= lang('total_cash'); ?></strong>:</h4>
                    </td>
                    <td style="text-align:right;"><h4>
                            <span><strong><?= $cashsales->paid ? $this->rerp->formatMoney(($cashsales->paid + ($this->session->userdata('cash_in_hand'))) - $expense + ($refunds->returned ? $refunds->returned : 0) - ($returns->total ? $returns->total : 0)) : $this->rerp->formatMoney($this->session->userdata('cash_in_hand') - $expense - ($returns->total ? $returns->total : 0)); ?></strong></span>
                        </h4></td>
                </tr>
            </table>
        </div>
    </div>

</div>
