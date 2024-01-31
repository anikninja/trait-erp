<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?= admin_form_open('shop_settings/menu_actions', 'id="action-form"') ?>
<div class="box">
	<div class="box-header">
		<h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('menus'); ?></h2>
		
		<div class="box-icon">
			<ul class="btn-tasks">
				<li class="dropdown">
					<a data-toggle="dropdown" class="dropdown-toggle" href="#">
						<i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang('actions') ?>"></i>
					</a>
					<ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
						<li>
							<a href="<?php echo admin_url('shop_settings/menus/add'); ?>" data-toggle="modal" data-target="#myModal">
								<i class="fa fa-plus"></i> <?= lang('add_menu') ?>
							</a>
						</li>
						<li class="divider"></li>
						<li>
							<a href="#" id="delete" data-action="delete">
								<i class="fa fa-trash-o"></i> <?= lang('delete_menus') ?>
							</a>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
	<div class="box-content">
		<div class="row">
			<div class="col-lg-12">
				<!-- p class="introtext"><?= lang('list_results'); ?></p -->
				<div class="table-responsive">
					<table id="MenuTable" class="table table-bordered table-hover table-striped reports-table">
						<thead>
						<tr>
							<th style="min-width:30px; width: 30px; text-align: center;">
								<input class="checkbox checkth" type="checkbox" name="check"/>
							</th>
							<th><?= lang('label'); ?></th>
							<th><?= lang('slug'); ?></th>
							<?php if ( $this->themeInfos['sub_nav'] ) { ?>
							<th><?= lang('parent'); ?></th>
							<?php } ?>
							<th style="width:100px;"><?= lang('actions'); ?></th>
						</tr>
						</thead>
						<tbody>
						<?php if ( ! empty( $menus ) ) { ?>
							<?php foreach( $menus as $menu ) { ?>
								<tr>
									<td>
										<input class="checkbox multi-select input-xs" type="checkbox" name="val[]" value="<?php echo $menu->id; ?>">
									</td>
									<td><?php echo $menu->label; ?></td>
									<td><?php echo $menu->slug; ?></td>
									<?php if ( $this->themeInfos['sub_nav'] ) { ?>
									<td><?php echo $menu->parent; ?></td>
									<?php } ?>
									<td>
										<div class="text-center">
											<a href="<?php echo admin_url('shop_settings/menus/edit/' . $menu->id ); ?>" data-toggle='modal' data-target='#myModal' class='tip' title='Edit Menu'>
												<i class="fa fa-edit"></i>
											</a>
											<a href='<?php echo admin_url( 'shop_settings/menus/delete/'. $menu->id ); ?>' onclick="return confirm( 'Are you sure?' );" class='tip' title='Delete menu'>
												<i class="fa fa-trash-o"></i>
											</a>
										</div>
									</td>
								</tr>
							<?php } ?>
						<?php } else { ?>
						<tr>
							<td colspan="6" class="dataTables_empty">
								<?= lang('Nothing Found') ?>
							</td>
						</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div style="display: none;">
	<input type="hidden" name="form_action" value="" id="form_action"/>
	<?= form_submit('submit', 'submit', 'id="action-form-submit"') ?>
</div>
<?= form_close() ?>
<script language="javascript">
	$(document).ready(function () {
		$('#delete').click(function (e) {
			e.preventDefault();
			$('#form_action').val($(this).attr('data-action'));
			$('#action-form-submit').trigger('click');
		});
	});
</script>
