<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_user'); ?></h4>
        </div>
        <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
        echo admin_form_open_multipart('commission/edit_user/' . $com_user->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('update_info'); ?></p>

            <div class="form-group all">
                <?= lang('Groupname', 'group_id'); ?>
                <select name="group_id" class="form-control select" id="group_id" style="width:100%">
		            <option value=""><?= lang('select') . ' ' . lang('Groupname'); ?></option>
		            <?php 
		            foreach ($group_list as $list){
		            ?>
		             <option value="<?php echo $list['id']?>" <?php echo ($list['id'] == $com_user->group_id) ? "selected" : null; ?> ><?= $list['name']; ?></option>
		            <?php }?>
	            </select>
            </div>
            
            <div class="form-group all">
                <?= lang('Username', 'user_id'); ?>
                <select name="user_id" class="form-control select" id="user_id" style="width:100%">
		            <option value=""><?= lang('select') . ' ' . lang('Username'); ?></option>
		            <?php 
		            foreach ($user_list as $user){
		            ?>
		             <option value="<?php echo $user['id']; ?>" <?php echo ($user['id'] == $com_user->user_id) ? "selected" : null; ?> ><?= $user['username']; ?></option>
		            <?php }?>
	            </select>
            </div>
            
            <div class="form-group all">
                <?= lang('rate', 'rate'); ?>
                <?= form_input('rate', set_value('rate', $com_user->rate), 'class="form-control tip" id="rate"'); ?>
            </div>
            
            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_textarea('description', set_value('description', $com_user->description), 'class="form-control tip" id="description"'); ?>
            </div>
            
        </div>
        
         <div class="modal-footer">
            <?php echo form_submit('edit_user', lang('edit_user'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
    $(document).ready(function() {
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'groups');
        });
    });
</script>
