<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<section class="page-contents">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">

                <div class="row">
                    <div class="col-sm-9 col-md-10">

                        <div class="panel panel-default margin-top-lg">
                            <div class="panel-heading text-bold">
                                <i class="fa fa-list-alt margin-right-sm"></i> <?= lang('view_quote') . ($inv ? ' (' . $inv->reference_no . ')' : ''); ?>
                                <a href="<?= shop_url('quotes'); ?>" class="pull-right"><i class="fa fa-share"></i> <?= lang('my_quotes'); ?></a>
                                <a href="<?= shop_url('quotes?download=' . $inv->id); ?>" class="pull-right" style="margin-right:10px;"><i class="fa fa-download"></i> <?= lang('download'); ?></a>
                            </div>
                            <div class="panel-body mprint">

                                <div class="text-center print" style="margin-bottom:20px;">
                                    <img src="<?= base_url() . 'assets/uploads/logos/' . $biller->logo; ?>"
                                    alt="<?= $biller->company && $biller->company != '-' ? $biller->company : $biller->name; ?>">
                                </div>


                                <div class="well well-sm">
                                    <div class="row bold">
                                        <div class="col-xs-5">
                                            <p class="bold">
                                                <?= lang('ref'); ?>: <?= $inv->reference_no; ?><br>
                                                <?= lang('date'); ?>: <?= $this->rerp->hrld($inv->date); ?><br>
                                                <?= lang('status'); ?>: <?= $inv->status; ?>
                                            </p>
                                        </div>
                                        <div class="col-xs-7 text-right order_barcodes">
                                            <img src="<?= admin_url('misc/barcode/' . $this->rerp->base64url_encode($inv->reference_no) . '/code128/74/0/1'); ?>" alt="<?= $inv->reference_no; ?>" class="bcimg" />
                                            <?= $this->rerp->qrcode('link', urlencode(shop_url('quotes/' . $inv->id)), 2); ?>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>

                                <div class="row" style="margin-bottom:15px;">
                                    <div class="col-xs-6">
                                        <?php echo $this->lang->line('to'); ?>:<br/>
                                        <h2 style="margin-top:10px;"><?= $customer->company ? $customer->company : $customer->name; ?></h2>
                                        <?= $customer->company ? '' : 'Attn: ' . $customer->name ?>

                                        <?php
                                        echo $customer->address . '<br>' . $customer->city . ' ' . $customer->postal_code . ' ' . $customer->state . '<br>' . $customer->country;

                                        echo '<p>';

                                        if ($customer->vat_no != '-' && $customer->vat_no != '') {
                                            echo '<br>' . lang('vat_no') . ': ' . $customer->vat_no;
                                        }
                                        if ($customer->cf1 != '-' && $customer->cf1 != '') {
                                            echo '<br>' . lang('ccf1') . ': ' . $customer->cf1;
                                        }
                                        if ($customer->cf2 != '-' && $customer->cf2 != '') {
                                            echo '<br>' . lang('ccf2') . ': ' . $customer->cf2;
                                        }
                                        if ($customer->cf3 != '-' && $customer->cf3 != '') {
                                            echo '<br>' . lang('ccf3') . ': ' . $customer->cf3;
                                        }
                                        if ($customer->cf4 != '-' && $customer->cf4 != '') {
                                            echo '<br>' . lang('ccf4') . ': ' . $customer->cf4;
                                        }
                                        if ($customer->cf5 != '-' && $customer->cf5 != '') {
                                            echo '<br>' . lang('ccf5') . ': ' . $customer->cf5;
                                        }
                                        if ($customer->cf6 != '-' && $customer->cf6 != '') {
                                            echo '<br>' . lang('ccf6') . ': ' . $customer->cf6;
                                        }

                                        echo '</p>';
                                        echo lang('tel') . ': ' . $customer->phone . '<br>' . lang('email') . ': ' . $customer->email;
                                        ?>
                                    </div>
                                    <div class="col-xs-6">
                                        <?php echo $this->lang->line('from'); ?>:
                                        <h2 style="margin-top:10px;"><?= $biller->company && $biller->company != '-' ? $biller->company : $biller->name; ?></h2>
                                        <?= $biller->company ? '' : 'Attn: ' . $biller->name ?>

                                        <?php
                                        echo $biller->address . '<br>' . $biller->city . ' ' . $biller->postal_code . ' ' . $biller->state . '<br>' . $biller->country;

                                        echo '<p>';

                                        if ($biller->vat_no != '-' && $biller->vat_no != '') {
                                            echo '<br>' . lang('vat_no') . ': ' . $biller->vat_no;
                                        }
                                        if ($biller->cf1 != '-' && $biller->cf1 != '') {
                                            echo '<br>' . lang('bcf1') . ': ' . $biller->cf1;
                                        }
                                        if ($biller->cf2 != '-' && $biller->cf2 != '') {
                                            echo '<br>' . lang('bcf2') . ': ' . $biller->cf2;
                                        }
                                        if ($biller->cf3 != '-' && $biller->cf3 != '') {
                                            echo '<br>' . lang('bcf3') . ': ' . $biller->cf3;
                                        }
                                        if ($biller->cf4 != '-' && $biller->cf4 != '') {
                                            echo '<br>' . lang('bcf4') . ': ' . $biller->cf4;
                                        }
                                        if ($biller->cf5 != '-' && $biller->cf5 != '') {
                                            echo '<br>' . lang('bcf5') . ': ' . $biller->cf5;
                                        }
                                        if ($biller->cf6 != '-' && $biller->cf6 != '') {
                                            echo '<br>' . lang('bcf6') . ': ' . $biller->cf6;
                                        }

                                        echo '</p>';
                                        echo lang('tel') . ': ' . $biller->phone . '<br>' . lang('email') . ': ' . $biller->email;
                                        ?>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped print-table order-table">

                                        <thead>

                                            <tr>
                                                <th><?= lang('no'); ?></th>
                                                <th><?= lang('description'); ?></th>
                                                <?php if ($Settings->indian_gst) {
                                            ?>
                                                    <th><?= lang('hsn_code'); ?></th>
                                                <?php
                                        } ?>
                                                <th><?= lang('quantity'); ?></th>
                                                <th><?= lang('unit_price'); ?></th>
                                                <?php
                                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                                    echo '<th>' . lang('tax') . '</th>';
                                                }
                                                if ($Settings->product_discount && $inv->product_discount != 0) {
                                                    echo '<th>' . lang('discount') . '</th>';
                                                }
                                                ?>
                                                <th><?= lang('subtotal'); ?></th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php $r     = 1;
                                            $tax_summary = [];
                                            foreach ($rows as $row):
                                                ?>
                                            <tr>
                                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                                <td style="vertical-align:middle;">
                                                    <?= $row->product_code . ' - ' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                                    <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                                    <?= $row->details ? '<br>' . $row->details : ''; ?>
                                                </td>
                                                <?php if ($Settings->indian_gst) {
                                                    ?>
                                                <td style="width: 85px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                                <?php
                                                } ?>
                                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->rerp->formatQuantity($row->unit_quantity) . ' ' . $row->product_unit_code; ?></td>
                                                <td style="text-align:right; width:100px;"><?= $this->rerp->formatMoney($row->unit_price); ?></td>
                                                <?php
                                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 && $row->tax_code ? '<small>(' . $row->tax_code . ')</small>' : '') . ' ' . $this->rerp->formatMoney($row->item_tax) . '</td>';
                                                }
                                                if ($Settings->product_discount && $inv->product_discount != 0) {
                                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->rerp->formatMoney($row->item_discount) . '</td>';
                                                }
                                                ?>
                                                <td style="text-align:right; width:120px;"><?= $this->rerp->formatMoney($row->subtotal); ?></td>
                                            </tr>
                                            <?php
                                            $r++;
                                            endforeach;
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <?php
                                            $col = $Settings->indian_gst ? 5 : 4;
                                            if ($Settings->product_discount && $inv->product_discount != 0) {
                                                $col++;
                                            }
                                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                                $col++;
                                            }
                                            if ($Settings->product_discount && $inv->product_discount != 0 && $Settings->tax1 && $inv->product_tax > 0) {
                                                $tcol = $col - 2;
                                            } elseif ($Settings->product_discount && $inv->product_discount != 0) {
                                                $tcol = $col - 1;
                                            } elseif ($Settings->tax1 && $inv->product_tax > 0) {
                                                $tcol = $col - 1;
                                            } else {
                                                $tcol = $col;
                                            }
                                            ?>
                                            <?php if ($inv->grand_total != $inv->total) {
                                                ?>
                                            <tr>
                                                <td colspan="<?= $tcol; ?>"
                                                    style="text-align:right; padding-right:10px;"><?= lang('total'); ?>
                                                    (<?= $default_currency->code; ?>)
                                                </td>
                                                <?php
                                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                                    echo '<td style="text-align:right;">' . $this->rerp->formatMoney($inv->product_tax) . '</td>';
                                                }
                                                if ($Settings->product_discount && $inv->product_discount != 0) {
                                                    echo '<td style="text-align:right;">' . $this->rerp->formatMoney($inv->product_discount) . '</td>';
                                                } ?>
                                                <td style="text-align:right;"><?= $this->rerp->formatMoney($inv->total + $inv->product_tax); ?></td>
                                            </tr>
                                            <?php
                                            } ?>
                                            <?php if ($Settings->indian_gst) {
                                                if ($inv->cgst > 0) {
                                                    echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('cgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->rerp->formatMoney($inv->cgst) : $inv->cgst) . '</td></tr>';
                                                }
                                                if ($inv->sgst > 0) {
                                                    echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('sgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->rerp->formatMoney($inv->sgst) : $inv->sgst) . '</td></tr>';
                                                }
                                                if ($inv->igst > 0) {
                                                    echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('igst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ($Settings->format_gst ? $this->rerp->formatMoney($inv->igst) : $inv->igst) . '</td></tr>';
                                                }
                                            } ?>

                                            <?php if ($inv->order_discount != 0) {
                                                echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang('order_discount') . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . ($inv->order_discount_id ? '<small>(' . $inv->order_discount_id . ')</small> ' : '') . $this->rerp->formatMoney($inv->order_discount) . '</td></tr>';
                                            }
                                            ?>
                                            <?php if ($Settings->tax2 && $inv->order_tax != 0) {
                                                echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang('order_tax') . ' (' . $default_currency->code . ')</td><td style="text-align:right;">' . $this->rerp->formatMoney($inv->order_tax) . '</td></tr>';
                                            }
                                            ?>
                                            <?php if ($inv->shipping != 0) {
                                                echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang('shipping') . ' (' . $default_currency->code . ')</td><td style="text-align:right;">' . $this->rerp->formatMoney($inv->shipping) . '</td></tr>';
                                            }
                                            ?>
                                            <tr>
                                                <td colspan="<?= $col; ?>"
                                                    style="text-align:right; font-weight:bold;"><?= lang('total_amount'); ?>
                                                    (<?= $default_currency->code; ?>)
                                                </td>
                                                <td style="text-align:right; font-weight:bold;"><?= $this->rerp->formatMoney($inv->grand_total); ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="row">
                                    <div class="col-xs-7">
                                        <?php
                                        if ($inv->note || $inv->note != '') {
                                            ?>
                                        <div class="well well-sm">
                                            <p class="bold"><?= lang('note'); ?>:</p>
                                            <div><?= $this->rerp->decode_html($inv->note); ?></div>
                                        </div>
                                        <?php
                                        } ?>
                                    </div>

                                    <div class="col-xs-5 pull-right">
                                        <div class="well well-sm">
                                            <p>
                                                <?= lang('created_by'); ?>: <?= $created_by->first_name . ' ' . $created_by->last_name; ?> <br>
                                                <?= lang('date'); ?>: <?= $this->rerp->hrld($inv->date); ?>
                                            </p>
                                            <?php if ($inv->updated_by) {
                                            ?>
                                            <p>
                                                <?= lang('updated_by'); ?>: <?= $updated_by->first_name . ' ' . $updated_by->last_name; ?><br>
                                                <?= lang('update_at'); ?>: <?= $this->rerp->hrld($inv->updated_at); ?>
                                            </p>
                                            <?php
                                        } ?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-2">
                        <?php include 'sidebar2.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
