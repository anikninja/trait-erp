<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('packaging'); ?></h4>
        </div>
        <div class="modal-body">
        <div class="well well-sm">
            <?= lang('biller') . ': ' . $sale->biller; ?><br>
            <?= lang('reference') . ': ' . $sale->reference_no; ?><br>
            <?= lang('warehouse') . ': ' . $warehouse->name . ' (' . $warehouse->code . ')'; ?>
        </div>
        <div class="table-responsive">
        <?= $full_table; ?>
        </div>


        </div>
    </div>
</div>
