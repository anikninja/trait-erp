<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_user'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('commission/add_user/' . $group_id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="form-group all">
                <?= lang('user', 'user_id'); ?>
                <select name="user_id" class="form-control select" id="user_id" style="width:100%">
		            <option value=""><?= lang('select') . ' ' . lang('Username'); ?></option>
		            <?php foreach ( $user_list as $user ) { ?>
		             <option value="<?php echo $user['id']?>"><?= $user['username']; ?></option>
		            <?php } ?>
	            </select>
            </div>
            
            <div class="form-group all">
                <?= lang('rate', 'rate'); ?>
                <?= form_input( ['type' => 'number', 'name' => 'rate' ], set_value('rate'), 'step="0.1" min="0.1" max="100" class="form-control tip" id="rate" required'); ?>
            </div>
	
	        <div class="form-group all">
		        <?= lang('status', 'is_enabled'); ?>
		        <select name="is_enabled" class="form-control select" id="is_enabled" style="width:100%" required>
			        <option value="0"><?= lang( 'inactive' ); ?></option>
			        <option value="1" selected><?= lang( 'active' ); ?></option>
		        </select>
	        </div>
            
            <div class="form-group all">
                <?= lang('note', 'description'); ?>
                <?= form_textarea('description', set_value('description'), 'class="form-control tip skip" id="description"'); ?>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_user', lang('add_user'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
