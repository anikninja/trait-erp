<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @var $group Erp_Commission_Group
 */
?>
<div class="box">
	<div class="box-header">
		<h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= sprintf( lang('add_x'), lang( 'commission_group' ) ); ?></h2>
	</div>
	<div class="box-content">
		<div class="row">
			<div class="col-lg-12">
				<p class="introtext"><?php echo lang('enter_info'); ?></p>
				<?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
				echo admin_form_open_multipart('commission/add_group', $attrib); ?>
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('group_name', 'name'); ?>
						<?= form_input('name', set_value('name', $group->getName() ), 'class="form-control gen_slug" id="name" required="required"'); ?>
					</div>
					
					<div class="form-group all">
						<?= lang('rate', 'rate'); ?>
						<?= form_input('rate', set_value('rate', $group->getRate() ), 'class="form-control tip" id="rate"'); ?>
					</div>
					
					<div class="form-group all">
						<?= lang('category', 'category_id'); ?>
						<select name="category_id" class="form-control select" id="category_id" style="width:100%">
							<option value=""><?= sprintf( lang( 'select_x' ), lang( 'category' ) ); ?></option>
							<?= $categories; ?>
						</select>
					</div>
					
					<div class="form-group all">
						<?= lang('status', 'is_enabled'); ?>
						<select name="is_enabled" class="form-control select" id="is_enabled" style="width:100%" required>
							<option value="0"><?= lang( 'inactive' ); ?></option>
							<option value="1" selected><?= lang( 'active' ); ?></option>
						</select>
					</div>
					
					<div class="form-group all">
						<?= lang('description', 'description'); ?>
						<?= form_textarea('description', set_value('description', $group->getDescription() ), 'class="form-control tip" id="description"'); ?>
					</div>
				</div>
				<div class="col-md-12">
					<?php echo form_submit( 'add_group', sprintf( lang( 'add_x' ), lang( 'group' ) ), 'class="btn btn-primary"' ); ?>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
