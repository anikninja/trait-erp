<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('import_product_images_by_csv'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <?php
                $attrib = ['class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('products/import_image_csv', $attrib)
                ?>
                <div class="row">
                    <div class="col-md-12">

                        <div class="well well-small">
                            <a href="<?php echo base_url(); ?>assets/csv/sample_product_images.csv" class="btn btn-primary pull-right"><i class="fa fa-download"></i> <?= lang('download_sample_file') ?></a>
                            <p>
                                <span class="text-warning"><?= lang('csv1'); ?></span>
	                            <br/><?= lang('csv2'); ?> <span class="text-info">(<?= 'Product Code, Main Image, Additional_1, Additional_2, Additional_3, Additional_4, Additional_5, Additional_6, Additional_7, Additional_8, Additional_9, Additional_10'; ?> )</span> <?= lang('csv3'); ?>
                            </p>
	                        <span class="text-warning">Only the following image types are allowed: <code><?php echo implode( '</code>,<code>', $image_valid['type'] ); ?></code>.</span><br/>
	                        <span class="text-warning">Max File Size Per Image: <code><?php echo $image_valid['size']; ?>kb</code>.</span><br/>
	                        <span class="text-warning">Allowed Image Dimension: <code><?php echo $image_valid['width']; ?>x<?php echo $image_valid['height']; ?>px</code>.</span><br/>
	                        <br/>
                            <span class="text-primary">System will check if the code belong to any product then will update images for the product other wise it will ignore the images.</span>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="csv_file"><?= lang('upload_file'); ?></label>
                                <input type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" class="form-control file" data-show-upload="false" data-show-preview="false" id="csv_file" required="required"/>
                            </div>

                            <div class="form-group">
                                <?php echo form_submit('import', $this->lang->line('import'), 'class="btn btn-primary"'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
