<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include 'breadcrumb.php'; ?>
<div class="body-content">
    <div class="container">
        <div class="checkout-box">
            <div class="row">
	            <?php include 'profile_navs.php'; ?>
                <div class="col-xs-12 col-sm-12 col-md-10">
                    <div class="panel-group">
                        <div class="panel panel-default address-panel">
                            <div class="panel-heading text-bold">
                                <?= lang('my_addresses'); ?>
                            </div><!-- .panel-heading -->
                            <div class="checkout-step-content-wrap">
                                <div class="row address-wrap">
		                            <?php
		                            $phones = '';
		                            if (!empty($addresses)) {
			                            foreach ($addresses as $address) {
				                            $phones .= sprintf(
					                            '<div class="phone col-sm-6 col-md-4 phone-%1$s">
                                                        <label>
                                                            <input type="radio" disabled name="phone" value="%1$s">
                                                            <div class="checkout-page-content">
                                                                <div class="content-header">%2$s</div>
                                                                <p class="content">%3$s</p>
                                                            </div>
                                                        </label>
                                                    </div>', $address->id, $address->title, $address->phone
				                            );
				                            ?>
                                            <div class="address col-sm-6 col-md-4 address-<?php echo $address->id; ?>" data-address='<?php echo json_encode($address) ?>'>
                                                <label>
                                                    <input type="radio" name="address" value="<?php echo $address->id; ?>">
                                                    <div class="checkout-page-content">
                                                        <div class="content-header"><?php echo $address->title; ?></div>
                                                        <p class="content">
								                            <?= $address->line1; ?><br>
								                            <?= $address->line2; ?><br>
								                            <?= $address->city; ?>,
								                            <?= $address->state; ?> -
								                            <?= $address->postal_code; ?><br> <?= $address->country; ?>
                                                        </p>
                                                        <div class="checkout-step-content-edit">
                                                            <a class="edit" href="#"><img src="<?php echo $assets; ?>images/edit-icon.png"></a>
                                                            <a class="remove" href="#"><img src="<?php echo $assets; ?>images/times.png"></a>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
				                            <?php
			                            }
		                            }
		                            ?>
                                </div>
                            </div>
                            <?php if ( count($addresses) < 6 ) { ?>
                                <a href="#" id="add-new-address" class="update-address btn btn-primary">+ Add Address</a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-group">
                        <div class="panel panel-default address-panel">
                            <div class="panel-heading text-bold">
                                My Phone Numbers
                            </div><!-- .panel-heading -->
                            <div class="checkout-step-content-wrap">
                                <div class="row phone-wrap">
		                            <?php
		                                echo $phones;
		                            ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.row -->
        </div><!-- /.checkout-box -->
    </div><!-- /.container -->
</div><!-- /.body-content -->
