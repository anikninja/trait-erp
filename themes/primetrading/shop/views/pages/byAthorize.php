<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<section class="page-contents">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">

                <div class="row">

                    <div class="col-sm-3 col-md-3">
                        <?php //include 'sidebar1.php'; ?>

                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="panel panel-default margin-top-lg">
                            <div class="panel-heading text-bold">
                                <h4 class="panel-title" id="cmpanelLabel">Paid by Authorize.net</h4>
                            </div>
                            <?php echo form_open('pay/by_authorize/' . $inv_id, 'class="validate"'); ?>
                            <div class="panel-body" id="pr_popover_content">
                                <div class="row">
                                    <div class="col-sm-11">
                                        <div class="pcc_1" style="display:block;">
                                            <div class="row" id="paymentForm">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input name="cc_no" type="text" id="card_number" class="form-control" placeholder="<?= lang('cc_no') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input name="cc_holder" type="text" id="name_on_card" class="form-control" placeholder="<?= lang('cc_holder') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="cc_type" id="cc_type" class="form-control pcc_type" placeholder="<?= lang('card_type') ?>">
                                                            <option value="Visa"><?= lang('Visa'); ?></option>
                                                            <option value="MasterCard"><?= lang('MasterCard'); ?></option>
                                                            <option value="Amex"><?= lang('Amex'); ?></option>
                                                            <option value="Discover"><?= lang('Discover'); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input name="cc_month" type="text" id="expiry_month" class="form-control" placeholder="<?= lang('month') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input name="cc_year" type="text" id="expiry_year" class="form-control" placeholder="<?= lang('year') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input name="cc_cvv2" type="text" id="cvv" class="form-control" placeholder="<?= lang('cvv2') ?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <?php echo form_submit('primary', lang('submit'), 'class="btn btn-theme"'); ?>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-3">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        // $('#makePayment').on('shown', function () {
        $('#expiry_month').on('keyup', function () {
            $(this).val($(this).val().replace(/\D/g, ''));
            $(this).attr("maxlength", 2);
        });
        $('#expiry_year').on('keyup', function () {
            $(this).val($(this).val().replace(/\D/g, ''));
            $(this).attr("maxlength", 4);
        });
        $('#cvv').on('keyup', function () {
            $(this).val($(this).val().replace(/\D/g, ''));
            $(this).attr("maxlength", 4);
        });
        $('#card_number').on('keyup change blur', function () {
            $(this).val($(this).val().replace(/\D/g, ''));
            var cc_type = creditCardTypeFromNumber($(this).val());
            $("#cc_type").val(cc_type).change();
        });

        function creditCardTypeFromNumber(num) {
            // first, sanitize the number by removing all non-digit characters.
            num = num.replace(/[^\d]/g, '');
            // now test the number against some regexes to figure out the card type.
            if (num.match(/^5[1-5]\d{14}$/)) {
                $('#card_number').attr("maxlength", 16);
                return 'MasterCard';
            } else if (num.match(/^4\d{15}/) || num.match(/^4\d{12}/)) {
                $('#card_number').attr("maxlength", 15);
                return 'Visa';
            } else if (num.match(/^3[47]\d{13}/)) {
                $('#card_number').attr("maxlength", 13);
                return 'Amex';
            } else if (num.match(/^6011\d{12}/)) {
                $('#card_number').attr("maxlength", 16);
                return 'Discover';
            }
            return 'UNKNOWN';
        }
    });
    // });
</script>
