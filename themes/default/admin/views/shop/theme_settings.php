<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
	.has-collapse { cursor: pointer; }
	.has-collapse > .fa-compress,
	.has-collapse > .fa-expand {
		position: relative;
		border-left: 1px solid #dbdee0;
		padding: 12px 0;
		height: 40px;
		width: 40px;
		display: inline-block;
		text-align: center;
		font-size: 16px;
		float: right;
	}
	.has-collapse.collapsed > .fa-compress,
	.has-collapse:not(.collapsed) > .fa-expand {
		display: none;
	}
	.segment-label {
		color: #34383c;
		font-size: 16px;
		background: #f7f7f8;
		border-bottom: 1px solid #dbdee0;
		width: 100%;
		float: left;
		padding: 8px;
	}
	.segment-label a, .segment-label h3 {
		margin: 0;
		display: inline-block;
	}
	.segment-label h3 {
		margin-left: 10px;
		font-weight: 600;
	}
	.static-segment .remove-segment {
		display: none !important;
	}
	.static-segment .segment-label h3 {
		margin-left: 0 !important;
	}
	.static-segment .segment-label,
	.static-segment .common-label {
		display: none !important;
	}
</style>
<div class="box" id="theme-settings">
	<div class="box-header">
		<h2 class="blue"><i class="fa-fw fa fa-cogs"></i><?= lang('theme_settings'); ?></h2>
	</div>
	<div class="box-content">
		<div class="row">
			<div class="col-lg-12">
				<?php echo admin_form_open_multipart( 'shop_settings/theme_settings', ['role' => 'form'] ); ?>
				<div class="row">
					<div class="col-md-12">
					<?php
					foreach ( $this->themeInfos['settings'] as $section => $section_settings ) {
						$section_settings = ci_parse_args( $section_settings,
							[
								'label'    => '',
								'icon'     => 'fa-fw fa fa-cogs',
								'desc'     => '',
								'segments' => [],
								'sections' => [],
							] );
						if ( empty( $section_settings['label'] ) || ( empty( $section_settings['segments'] ) && empty( $section_settings['sections'] ) ) ) {
							continue;
						}
						$section_settings_values = isset( $settings[ $section ] ) ? $settings[ $section ] : [];
						?>
						<div class="box section-settings" id="settings-<?= $section; ?>">
							<div class="box-header has-collapse" data-toggle="collapse" id="settings-<?= $section; ?>-heading" data-parent="#theme-settings" href="#settings-<?= $section; ?>-collapse" aria-expanded="true" aria-controls="settings-<?= $section; ?>-collapse">
								<h2 class="blue"><i class="<?= $section_settings['icon']; ?>"></i><?= $section_settings['label']; ?></h2>
								<i class="fa-fw fa fa-compress"></i>
								<i class="fa-fw fa fa-expand"></i>
							</div>
							<div class="box-content collapse in" id="settings-<?= $section; ?>-collapse" role="tabpanel" aria-labelledby="settings-<?= $section; ?>-heading">
								<?php if ( ! empty( $section_settings['desc'] ) ) { ?>
									<p class="introtext"><?= $section_settings['desc']; ?></p>
								<?php } ?>
								<?php
								foreach ( $section_settings['segments'] as $segmentKey => $segment ) {
									$segment = ci_parse_args( $segment,
										[
											'label'       => '',
											'icon'        => 'fa-fw fa fa-cog',
											'desc'        => '',
											'sections'    => [],
											'supports'    => [],
											'widgets'     => [],
											'max_section' => 0,
										] );
									if ( empty( $segment['label'] ) || ( empty( $segment['supports'] ) && empty( $segment['widgets'] ) && empty( $segment['sections'] ) ) ) {
										continue;
									}
									$segment_settings_values = isset( $section_settings_values[ $segmentKey ] ) ? $section_settings_values[ $segmentKey ] : [];
								?>
								<div class="row" id="#theme-settings-<?= $segmentKey; ?>">
									<div class="col-md-12">
										<div class="box page-segments segments-wrap segment-<?= $segmentKey; ?>" data-max_item="<?= $segment['max_section']; ?>" data-item_count="<?= count( $segment_settings_values ); ?>" data-idx="<?= count( $segment_settings_values ); ?>">
											<div class="box-header has-collapse" data-toggle="collapse" id="settings-<?= $segmentKey; ?>-heading" data-parent="#theme-settings-<?= $segmentKey; ?>" href="#settings-<?= $segmentKey; ?>-collapse" aria-expanded="true" aria-controls="settings-<?= $segmentKey; ?>-collapse">
												<h2 class="blue"><?= $segment['icon'] ? '<i class="'.$segment['icon'].'"></i>' : ''; ?><?= $segment['label']; ?></h2>
												<i class="fa-fw fa fa-compress"></i>
												<i class="fa-fw fa fa-expand"></i>
											</div>
											<div class="box-content collapse in" id="settings-<?= $segmentKey; ?>-collapse" role="tabpanel" aria-labelledby="settings-<?= $segmentKey; ?>-heading">
												<div class="row">
													<div class="col-md-12">
													<?php if ( ! empty( $segment['desc'] ) ) { ?>
														<p class="introtext"><?= $segment['desc']; ?></p>
													<?php } ?>
													<?php if ( ! empty( $segment['sections'] ) && is_array( $segment['sections'] ) ) {
														render_settings_segments_sections( $segment['sections'], $section, $segmentKey, $segment_settings_values );
													} else if ( ! empty( $segment['supports'] ) && is_array( $segment['supports'] ) ) {
														render_settings_segments_supports( $segment['supports'], $section, $segmentKey, $segment_settings_values );
													} else if ( ! empty( $segment['widgets'] ) && is_array( $segment['widgets'] ) ) {
														$values = isset( $segment_settings_values['widgets'] ) ? $segment_settings_values['widgets'] : [];
														render_settings_segments_widgets( $segment['widgets'], $section, $segmentKey, $values );
													} ?>
												   </div>
											   </div>
										   </div>
									   </div>
									</div>
								</div>
								<?php } ?>
							</div>
						</div>
					<?php
					}
					?>
						<div class="box custom-css">
							<div class="box-header has-collapse" data-toggle="collapse" id="settings-css-heading" data-parent="#theme-settings" href="#settings-css-collapse" aria-expanded="true" aria-controls="settings-css-collapse">
								<h2 class="blue"><i class="fa-fw fa fa-css3"></i>Custom CSS</h2>
								<i class="fa-fw fa fa-compress"></i>
								<i class="fa-fw fa fa-expand"></i>
							</div>
							<div class="box-content collapse in" id="settings-css-collapse" role="tabpanel" aria-labelledby="settings-css-heading">
								<div class="code-edit not-init" data-type="css">
									<textarea name="custom_css" class="form-control skip store"><?= isset( $settings['custom_css'] ) ? $settings['custom_css'] : ''; ?></textarea>
									<div class="stage" style="width: 100%;height: 350px;display: none;padding: 10px 0;background: #1e1e1e;"></div>
								</div>
							</div>
						</div>
						<div class="box custom-js">
							<div class="box-header has-collapse" data-toggle="collapse" id="settings-js-heading" data-parent="#theme-settings" href="#settings-js-collapse" aria-expanded="true" aria-controls="settings-js-collapse">
								<h2 class="blue"><i class="fa-fw fa fa-jsfiddle"></i>Custom JS</h2>
								<i class="fa-fw fa fa-compress"></i>
								<i class="fa-fw fa fa-expand"></i>
							</div>
							<div class="box-content collapse in" id="settings-js-collapse" role="tabpanel" aria-labelledby="settings-js-heading">
								<div class="code-edit not-init" data-type="javascript">
									<textarea name="custom_js" class="form-control skip store"><?= isset( $settings['custom_js'] ) ? $settings['custom_js'] : ''; ?></textarea>
									<div class="stage" style="width: 100%;height: 350px;display: none;padding: 10px 0;background: #1e1e1e;"></div>
								</div>
							</div>
						</div>
						<?= form_submit('update', lang('update'), 'class="btn btn-primary"'); ?>
					</div>
				</div>
				<?= form_close(); ?>
			</div>
		</div>
	</div>
</div>
