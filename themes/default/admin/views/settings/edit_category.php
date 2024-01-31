<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_category'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('system_settings/edit_category/' . $category->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('update_info'); ?></p>

            <div class="form-group">
                <?= lang('category_code', 'code'); ?>
                <?= form_input('code', set_value('code', $category->code), 'class="form-control" id="code" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('category_name', 'name'); ?>
                <?= form_input('name', set_value('name', $category->name), 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('slug', 'slug'); ?>
                <?= form_input('slug', set_value('slug', $category->slug), 'class="form-control tip" id="slug" required="required"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', set_value('description', $category->description), 'class="form-control tip" id="description"'); ?>
            </div>

            <div class="form-group">
                <?= lang('category_image', 'image') ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
	                    <?= lang('parent_category', 'parent') ?>
                        <select name="parent" class="form-control select" id="parent" style="width:100%">
                            <option value=""><?= lang('select') . ' ' . lang('parent_category'); ?></option>
		                    <?= $categories; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
	                    <?= lang('menu_order', 'menu_order'); ?>
	                    <?= form_input('menu_order', set_value('menu_order', $category->menu_order), 'class="form-control tip" id="menu_order"'); ?>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
	        <div class="row">
		        <div class="col-md-6 text-left">
			        <?= lang('mark_featured_category', 'featured') ?>
			        <?= form_checkbox( 'featured', 1, set_checkbox( 'featured', 1, 1 == $category->featured ), 'id="featured"' ); ?>
		        </div>
		        <div class="col-md-6">
			        <?php echo form_submit('edit_category', lang('edit_category'), 'class="btn btn-primary"'); ?>
		        </div>
	        </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
    $(document).ready(function() {
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'category');
        });
    });
</script>
