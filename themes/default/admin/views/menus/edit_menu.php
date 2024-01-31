<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
			</button>
			<h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_Menu'); ?></h4>
		</div>
		<?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
		echo admin_form_open_multipart('shop_settings/menus/edit/' . $menu->id, $attrib );
		?>
		<div class="modal-body">
			<p><?= lang('enter_info'); ?></p>
			<div class="form-group">
				<?= lang('Menu_Label', 'label'); ?>
				<?= form_input('label', set_value('label', $menu->label ), 'class="form-control" id="label" required="required"'); ?>
			</div>
			<div class="form-group all">
				<?= lang('Path', 'slug'); ?>
				<div>
			        <span style="width: 215px;">
				        <input type="text" value="<?php echo site_url( '/' ); ?>" class="form-control" disabled readonly aria-readonly="true">
			        </span>
					<span style="width: calc( 100% - 220px );">
				        <?= form_input('slug', set_value('slug', $menu->slug ), 'class="form-control tip" id="slug" required="required"'); ?>
			        </span>
				</div>
			</div>
			<?php if ( $this->themeInfos['sub_nav'] ) { ?>
			<div class="form-group all">
				<?= lang('Parent', 'parent'); ?>
				<select name="parent" class="form-control select" id="parent" style="width:100%">
					<option value=""><?= lang('select') . ' ' . lang('parent_menu'); ?></option>
					<?= $parent_dropdown; ?>
				</select>
			</div>
			<?php } ?>
			<div class="form-group row all">
				<div class="col-md-6">
					<?= lang('Tip', 'tip'); ?>
					<?= form_input('tip', set_value('tip', $menu->tip ), 'class="form-control tip" id="tip"'); ?>
				</div>
				<div class="col-md-3">
					<?= lang('CSS_Class', 'class'); ?>
					<?= form_input('class', set_value('class', $menu->class ), 'class="form-control tip" id="class"'); ?>
				</div>
				<div class="col-md-3">
					<?= lang('Order/Position', 'sort'); ?>
					<?= form_input('sort', set_value('sort', $menu->order ), 'class="form-control tip" id="sort"'); ?>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<?php echo form_submit('edit_menu', lang('Edit_Menu'), 'class="btn btn-primary"'); ?>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
