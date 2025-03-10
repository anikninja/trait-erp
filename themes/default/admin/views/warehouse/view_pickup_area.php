<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_warehouse'); ?></h4>
            </div>
			<?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
			echo admin_form_open_multipart('warehouse/edit_warehouse/' . $id, $attrib); ?>
            <div class="modal-body">
                <p><?= lang('enter_info'); ?></p>

                <div class="form-group">
                    <label class="control-label" for="code"><?php echo $this->lang->line('code'); ?></label>
					<?php echo form_input('code', $warehouse->code, 'class="form-control" id="code" required="required"'); ?>
                </div>
                <div class="form-group">
                    <label class="control-label" for="name"><?php echo $this->lang->line('name'); ?></label>
					<?php echo form_input('name', $warehouse->name, 'class="form-control" id="name" required="required"'); ?>
                </div>
                <div class="form-group">
                    <label class="control-label" for="price_group"><?php echo $this->lang->line('price_group'); ?></label>
					<?php
					$pgs[''] = lang('select') . ' ' . lang('price_group');
					foreach ($price_groups as $price_group) {
						$pgs[$price_group->id] = $price_group->name;
					}
					echo form_dropdown('price_group', $pgs, $warehouse->price_group_id, 'class="form-control tip select" id="price_group" style="width:100%;"');
					?>
                </div>
                <div class="form-group">
                    <label class="control-label" for="phone"><?php echo $this->lang->line('phone'); ?></label>
					<?php echo form_input('phone', $warehouse->phone, 'class="form-control" id="phone"'); ?>
                </div>
                <div class="form-group">
                    <label class="control-label" for="email"><?php echo $this->lang->line('email'); ?></label>
					<?php echo form_input('email', $warehouse->email, 'class="form-control" id="email"'); ?>
                </div>
                <div class="form-group">
                    <label class="control-label" for="address"><?php echo $this->lang->line('address'); ?></label>
					<?php echo form_textarea('address', $warehouse->address, 'class="form-control" id="address" required="required"'); ?>
                </div>

                <div class="form-group all">
					<?= lang('zone', 'zone'); ?>
                    <select name="zone" class="form-control select" id="zone" style="width:100%" required="required" >
                        <option><?= lang( 'select_zone' ); ?></option>
						<?php
						foreach ( $zone as $val ) {
							?>
                            <option value="<?= $val->id; ?>" <?= ( $warehouse->zone == $val->id ) ? 'selected' : null; ?>><?= $val->name; ?></option>
							<?php
						}
						?>
                    </select>
                </div>

                <div class="form-group all">
					<?= lang('area', 'area'); ?>
                    <select name="area" class="form-control select" id="area" style="width:100%" required="required" >
                        <option><?= lang( 'select_area' ); ?></option>
						<?php
						foreach ( $area as $valarea ) {
							?>
                            <option value="<?= $valarea->id; ?>" <?= ( $warehouse->area == $valarea->id ) ? 'selected' : null; ?>><?= $valarea->name; ?></option>
							<?php
						}
						?>
                    </select>
                </div>

                <div class="form-group">
					<?= lang('warehouse_map', 'image') ?>
                    <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                           class="form-control file">
                </div>
            </div>
            <div class="modal-footer">
				<?php echo form_submit('edit_warehouse', lang('edit_warehouse'), 'class="btn btn-primary"'); ?>
            </div>
        </div>
		<?php echo form_close(); ?>
    </div>
    <script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>