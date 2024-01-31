<?php
defined('BASEPATH') or exit('No direct script access allowed');

$takaSign = '<span class="taka">' . $Settings->symbol . '</span>';
$filler = '<span class="taka" style="visibility:hidden;">' . $Settings->symbol . '</span>';
?><!DOCTYPE html>
<html>
	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <title><?= lang('invoice') . ' ' . $inv->reference_no; ?></title>
	    <link href="<?= base_url('themes/' . $Settings->theme . '/shop/assets/css/pdf/bootstrap.min.css'); ?>" rel="stylesheet">
        <style>
	        .pdf-wrapper * {
                color: #000;
            }
            .pdf-wrapper span.taka {
	            font-family: SolaimanLipi, DejaVu Sans !important;
            }
            .pdf-wrapper p {
                line-height: 18px !important;
            }
            .pdf-wrapper {
                font-family: "Times New Roman" !important;
            }
            .invoice-header .logo img {
                height: 55px;
                width: auto;
            }
            .invoice-bar-codes img {
                height: 55px;
                max-height: 55px;
                width: auto;
                display: inline-block;
            }
            .invoice-address {
                margin-top: 15px;
            }
            .invoice-number-date {
                margin-top: 30px;
            }
            .invoice-items .table th {
                text-align: center;
                padding: 5px;
            }
            .invoice-items .table td {
	            padding: 1px 3px;
	            min-height: 25px;
            }
            .invoice-items .table td,
            .invoice-items .table th {
                border-color: #000;
            }
            .invoice-items .table,
            .invoice-items .table tfoot tr td {
                border: none;
            }
            .invoice-items .table {
                border-top: 1px solid #000;
            }
            .invoice-items .table tfoot tr td.last-child {
                border: 1px solid #000;
            }
            .invoice-items .table tfoot tr td.first-child {
                font-weight: 700;
            }
        </style>
		<style>
			*, html {
				padding: 0;
				margin: 0;
			}
			
			tr,td,th {
				vertical-align:middle !important;
			}
			
			body {
				padding: 40px;
				margin: 0;
				height: 100%;
				background: #FFF !important;
			}
			
			body:before, body:after {
				display: none !important;
			}
			
			h1, h2, h3, h4, h5, h6 {
				margin: 10px 0 !important;
			}
			
			h1 {
				font-size: 32px;
				line-height: 32px;
				font-weight: bold;
			}
			
			h2 {
				font-size: 16px;
				line-height: 16px;
				font-weight: bold;
			}
			
			h3 {
				font-size: 15px;
				line-height: 15px;
			}
			
			h4 {
				font-size: 14px;
				line-height: 14px;
			}
			
			h5 {
				font-size: 13px;
				line-height: 13px;
			}
			
			h6 {
				font-size: 12px;
				line-height: 12px;
			}
			
			header, footer {
				text-align: center;
				padding: 5px 25px !important;
				background-color: #F9F9FF;
				height: 20px;
				color: #000;
				position: fixed;
				left: 0;
				right: 0;
				margin: 0;
			}
			
			header {
				top: 10px;
			}
			
			footer {
				bottom: 10px;
			}
			
			.title {
				text-transform: uppercase !important;
			}
			
			.order_barcodes {
				/* uncomment line below to hide the barcodes */
				/* display: none; */
				margin-top: 10px;
			}
			
			.barcode .bcimg {
				margin-top: 10px;
				margin-right: 10px;
			}
			
			.qrimg {
				display: none;
			}
			
			.page {
				page-break-after: always;
			}
			
			@page {
				margin: 100px 25px;
			}
			.pagenum:before {
				content: counter(page);
			}
		</style>
	</head>
    <body>
        <div class="pdf-wrapper">
            <div class="row invoice-header">
                <div class="col-xs-5">
                    <?php
                    $path   = base_url() . 'assets/uploads/logos/' . $biller->logo;
                    $type   = pathinfo( $path, PATHINFO_EXTENSION );
                    $data   = file_get_contents( $path );
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode( $data );
                    ?>
                    <div class="logo">
                        <img src="<?= $base64; ?>" alt="<?= $biller->company && $biller->company != '-' ? $biller->company : $biller->name; ?>">
                    </div>
                    <div class="invoice-address">
                        <p>
                            <?= $this->lang->line('to'); ?>:<br>
                            <?= $customer->name; ?><br>
                            <?= $customer->company && $customer->company != '-' ? $customer->company . '<br>' : ''; ?>
                            <?= $customer->address . '<br>'; ?>
                            <?= $customer->city . ' ' . $customer->postal_code . ' ' . $customer->state . '<br>'; ?>
                            <?= $customer->phone; ?>
                        </p>
                    </div>
                </div>
                <div class="col-xs-5 pull-right">
                    <div class="invoice-bar-codes">
                        <?php
                        $path   = admin_url('misc/barcode/' . $this->rerp->base64url_encode($inv->reference_no) . '/code128/74/0/1');
                        $type   = $Settings->barcode_img ? 'png' : 'svg+xml';
                        $data   = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        ?>
                        <table width="100%">
                            <tr>
                                <td height="55px"><img class="pull-right" src="<?= $base64; ?>" alt="<?= $inv->reference_no; ?>"></td>
                                <td width="55px" height="55px"><?php echo $this->rerp->qrcode( 'link', urlencode( shop_url( 'orders/' . $inv->id ) ), 2 ); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="invoice-number-date text-right">
                        <b>INVOICE# <?= $inv->reference_no; ?></b>
                        <p>DATE: <?= $this->rerp->hrld($inv->date); ?></p>
                    </div>
                </div>
	            <div class="clearfix"></div>
                <div class="col-xs-12">
                    <div class="comments">
	                    <?php if ($inv->note || $inv->note != '') { ?>
                            <b>COMMENTS OR SPECIAL INSTRUCTIONS:</b>
                            <div><?= $this->rerp->decode_html($inv->note); ?></div>
	                    <?php } ?>
                    </div>
                    <div class="payment-info">
                        <p>
                            <?= lang('payment_status'); ?>: <?= lang($inv->payment_status); ?><br>
                            Payment Method: <?= lang($inv->payment_method); ?><br>
	                        <?php if( isset($delivery_schedule) ){ ?>
		                        <?= lang('delivery_schedule'); ?>: <?= $delivery_schedule; ?>
	                        <?php } ?>
                        </p>
                    </div>
                </div>
            </div>
	        <div class="clearfix"></div>
            <div class="row invoice-items">
                <div class="col-xs-12">
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
	                $idx = 1;
	                ?>
                    <table class="table table-bordered text-right">
                        <thead>
                        <tr>
                            <th><?= lang('no'); ?></th>
                            <th><?= lang('description'); ?></th>
			                <?php if ($Settings->indian_gst) { ?>
                                <th><?= lang('hsn_code'); ?></th>
			                <?php } ?>
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
		                <?php foreach ( $rows as $row ) { ?>
                            <tr>
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $idx; ?></td>
                                <td style="vertical-align:middle; text-align: left;">
					                <?= $row->product_code . ' - ' . $row->product_name . ( $row->variant ? ' (' . $row->variant . ')' : '' ); ?>
					                <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
					                <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                                </td>
				                <?php if ( $Settings->indian_gst ) { ?>
                                    <td style="width: 85px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
				                <?php } ?>
                                <td style="width: 100px; text-align:center; vertical-align:middle;"><?= $this->rerp->formatQuantity( $row->unit_quantity ) . '&times;' . $row->product_unit_code; ?></td>
                                <td style="text-align:right; width:90px;"><?= $this->rerp->formatMoney( $row->real_unit_price, $takaSign ); ?></td>
				                <?php
				                if ( $Settings->tax1 && $inv->product_tax > 0 ) {
					                echo '<td style="width: 90px; text-align:right; vertical-align:middle;">'
					                     . ( $row->item_tax != 0 && $row->tax_code
							                ? '<small>(' . $row->tax_code . ')</small> '
							                : '' )
					                     . $this->rerp->formatMoney( $row->item_tax, $takaSign )
					                     . '</td>';
				                }
				                if ( $Settings->product_discount
				                     && $inv->product_discount != 0 ) {
					                echo '<td style="width: 90px; text-align:right; vertical-align:middle;">' . ( $row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '' ) . $this->rerp->formatMoney( $row->item_discount, $takaSign ) . '</td>';
				                }
				                ?>
                                <td style="vertical-align:middle; text-align:right; width:110px;"><?= $this->rerp->formatMoney( $row->subtotal, $takaSign ); ?></td>
                            </tr>
			                <?php
			                $idx++;
		                }
		                if ( $return_rows ) {
			                echo '<tr class="warning"><td colspan="' . ($col + 1) . '" class="no-border"><strong>' . lang('returned_items') . '</strong></td></tr>';
			                foreach ( $return_rows as $k => $row ) {
				                ?>
                                <tr class="warning">
                                    <td style="text-align:center; width:40px; vertical-align:middle;"><?= $idx; ?></td>
                                    <td style="text-align: left; vertical-align:middle;">
						                <?= $row->product_code . ' - ' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
						                <?= $row->details ? '<br>' . $row->details : ''; ?>
						                <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                                    </td>
					                <?php if ($Settings->indian_gst) {
						                ?>
                                        <td style="width: 85px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
						                <?php
					                } ?>
                                    <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->rerp->formatQuantity($row->quantity) . '&times;' . $row->product_unit_code; ?></td>
                                    <td style="text-align:right; width:90px;"><?= $this->rerp->formatMoney($row->real_unit_price, $takaSign); ?></td>
					                <?php
					                if ($Settings->tax1 && $inv->product_tax > 0) {
						                echo '<td style="text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 && $row->tax_code ? '<small>(' . $row->tax_code . ')</small>' : '') . ' ' . $this->rerp->formatMoney($row->item_tax, $takaSign) . '</td>';
					                }
					                if ($Settings->product_discount && $inv->product_discount != 0) {
						                echo '<td style="text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->rerp->formatMoney($row->item_discount, $takaSign) . '</td>';
					                } ?>
                                    <td style="text-align:right; width:110px;"><?= $this->rerp->formatMoney($row->subtotal, $takaSign); ?></td>
                                </tr>
				                <?php
				                $idx++;
			                }
		                }
		                ?>
                        </tbody>
                        <tfoot>
		                <?php if ( $inv->grand_total != $inv->total ) { ?>
                            <tr>
                                <td colspan="<?= $tcol; ?>" style="text-align:right; font-weight:bold;"><?= lang('total'); ?> (<?= $default_currency->code; ?>)</td>
				                <?php
				                if ( $Settings->tax1 && $inv->product_tax > 0 ) {
					                echo '<td style="text-align:right;">' . $this->rerp->formatMoney( $return_sale ? ( $inv->product_tax + $return_sale->product_tax ) : $inv->product_tax, $takaSign ) . '</td>';
				                }
				                if ($Settings->product_discount && $inv->product_discount != 0) {
					                echo '<td style="text-align:right;">' . $this->rerp->formatMoney( $return_sale ? ( $inv->product_discount + $return_sale->product_discount ) : $inv->product_discount, $takaSign ) . '</td>';
				                } ?>
                                <td style="text-align:right;" class="last-child"><?= $this->rerp->formatMoney( $return_sale ? ( ( $inv->total + $inv->product_tax ) + ( $return_sale->total + $return_sale->product_tax ) ) : ( $inv->total + $inv->product_tax ), $takaSign ); ?></td>
                            </tr>
		                <?php } ?>
		                <?php if ($Settings->indian_gst) {
			                if ($inv->cgst > 0) {
				                $cgst = $return_sale ? $inv->cgst + $return_sale->cgst : $inv->cgst;
				                echo '<tr><td colspan="' . $col . '" style="text-align:right; font-weight:bold;">' . lang('cgst') . ' (' . $default_currency->code . ')</td>';
				                echo '<td style="text-align:right;" class="last-child">' . ($Settings->format_gst ? $this->rerp->formatMoney($cgst, $takaSign) : $cgst) . '</td></tr>';
			                }
			                if ($inv->sgst > 0) {
				                $sgst = $return_sale ? $inv->sgst + $return_sale->sgst : $inv->sgst;
				                echo '<tr><td colspan="' . $col . '" style="text-align:right; font-weight:bold;">' . lang('sgst') . ' (' . $default_currency->code . ')</td>';
				                echo '<td style="text-align:right;" class="last-child">' . ($Settings->format_gst ? $this->rerp->formatMoney($sgst, $takaSign) : $sgst) . '</td></tr>';
			                }
			                if ($inv->igst > 0) {
				                $igst = $return_sale ? $inv->igst + $return_sale->igst : $inv->igst;
				                echo '<tr><td colspan="' . $col . '" style="text-align:right; font-weight:bold;">' . lang('igst') . ' (' . $default_currency->code . ')</td>';
				                echo '<td style="text-align:right;" class="last-child">' . ($Settings->format_gst ? $this->rerp->formatMoney($igst, $takaSign) : $igst) . '</td></tr>';
			                }
		                } ?>
		                <?php
		                if ($return_sale) {
			                echo '<tr><td colspan="' . $col . '" style="text-align:right; font-weight:bold;">' . lang('return_total') . ' (' . $default_currency->code . ')</td>';
			                echo '<td style="text-align:right; font-weight:bold;" class="last-child">' . $this->rerp->formatMoney($return_sale->grand_total, $takaSign) . '</td></tr>';
		                }
		                if ($inv->surcharge != 0) {
			                echo '<tr><td colspan="' . $col . '" style="text-align:right; font-weight:bold;">' . lang('return_surcharge') . ' (' . $default_currency->code . ')</td>';
			                echo '<td style="text-align:right;" class="last-child">' . $this->rerp->formatMoney($inv->surcharge, $takaSign) . '</td></tr>';
		                }
		                ?>
		                <?php if ($inv->order_discount != 0) {
			                echo '<tr><td colspan="' . $col . '" style="text-align:right; font-weight:bold;">' . lang('order_discount') . ' (' . $default_currency->code . ')</td>';
			                echo '<td style="text-align:right;" class="last-child">' . ($inv->order_discount_id ? '<small>(' . $inv->order_discount_id . ')</small> ' : '') . $this->rerp->formatMoney($return_sale ? ($inv->order_discount + $return_sale->order_discount) : $inv->order_discount, $takaSign) . '</td></tr>';
		                }
		                ?>
		                <?php if ($Settings->tax2 && $inv->order_tax != 0) {
			                echo '<tr><td colspan="' . $col . '" style="text-align:right; font-weight:bold;">' . lang('order_tax') . ' (' . $default_currency->code . ')</td>';
			                echo '<td style="text-align:right;" class="last-child">' . $this->rerp->formatMoney($return_sale ? ($inv->order_tax + $return_sale->order_tax) : $inv->order_tax, $takaSign) . '</td></tr>';
		                }
		                ?>
		                <?php if ($inv->shipping != 0) {
			                echo '<tr><td colspan="' . $col . '" style="text-align:right; font-weight:bold;">' . lang('shipping') . ' (' . $default_currency->code . ')</td>';
			                echo '<td style="text-align:right;" class="last-child">' . ( $inv->shipping ? $this->rerp->formatMoney($inv->shipping, $takaSign) : '-' . $filler ) . '</td></tr>';
		                }
		                ?>
                        <tr>
                            <td colspan="<?= $col; ?>" style="text-align:right; font-weight:bold;"><?= lang('total_amount'); ?> (<?= $default_currency->code; ?>)</td>
                            <td style="text-align:right;" class="last-child"><?= $this->rerp->formatMoney($return_sale ? ($inv->grand_total + $return_sale->grand_total) : $inv->grand_total, $takaSign); ?></td>
                        </tr>
                        <tr>
                            <td colspan="<?= $col; ?>" style="text-align:right; font-weight:bold;"><?= lang('paid'); ?> (<?= $default_currency->code; ?>)</td>
                            <td style="text-align:right;" class="last-child"><?php
	                            $paid = $return_sale ? ($inv->paid + $return_sale->paid) : $inv->paid;
	                            echo $paid > 0 ? $this->rerp->formatMoney( $paid, $takaSign) : '-' . $filler; ?></td>
                        </tr>
                        <tr>
                            <td colspan="<?= $col; ?>" style="text-align:right; font-weight:bold;"><?= lang('balance'); ?> (<?= $default_currency->code; ?>)</td>
                            <td style="text-align:right;" class="last-child"><?php
	                            $balance = ( $return_sale ? ( $inv->grand_total + $return_sale->grand_total ) : $inv->grand_total ) - ( $return_sale ? ( $inv->paid + $return_sale->paid ) : $inv->paid );
	                            echo $balance > 0 ? $this->rerp->formatMoney( $balance, $takaSign ) : '-' . $filler; ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
	        <div class="clearfix"></div>
            <div class="row invoice-footer">
                <div class="col-xs-12">
                    <p>
                        Thank you for ordering from <?= $Settings->site_name; ?>.<br>
                        If you have any complaint about this order, Please call us at <?= $biller->phone; ?> or email us at <?= $biller->email; ?>
                    </p>
                    <p class="text-center">
                        <b>THANK YOU FOR YOUR BUSINESS.</b>
                    </p>
                </div>
	            <div class="clearfix"></div>
            </div>
	        <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </body>
</html>
