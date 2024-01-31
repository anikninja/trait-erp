<?php
defined('BASEPATH') or exit('No direct script access allowed');
if ( ! empty( $home_slider ) && $showSlider ) { ?>
	<!-- ========================================== SECTION – HERO ========================================= -->
	<div id="hero">
		<div id="owl-main" class="owl-carousel owl-inner-nav owl-ui-sm">
			<?php
			foreach( $home_slider as $i => $slide ) {
				$slide = prepare_slide_data( $slide );
				if( ! $slide ) {
					continue;
				}
				$bg = 'background-image: url('.$slide['image'].');';
				if ( $slide['no_content'] ) { ?>
					<a href="<?= $slide['link']; ?>" class="disabled" aria-label="<?= $slide['button']; ?>"><div class="item slide-<?= $i; ?>" style="<?= $bg; ?>"></div></a>
				<?php } else { ?>
					<div class="item slide-<?= $i; ?>" style="<?= $bg; ?>">
						<?php if ( ! empty( $slide['title'] ) || ! empty( $slide['caption'] ) ) { ?>
							<div class="container-fluid">
								<div class="caption bg-color vertical-center <?= 'text-' . $slide['alignment'] . ' ' . $slide['alignment']; ?>">
									<?php if ( $slide['title'] ) { ?>
										<div class="slider-header fadeInDown-<?php echo $slide['subtitle'] ? 2 : 1; ?>"><?= $slide['title']; ?></div>
										<?php if ( $slide['subtitle'] ) { ?>
											<div class="big-text fadeInDown-1 hidden-xs"><?= $slide['subtitle']; ?></div>
										<?php } ?>
									<?php } ?>
									<?php if ( $slide['caption'] ) { ?>
										<div class="excerpt fadeInDown-2 hidden-xs"><?= $slide['caption']; ?></div>
									<?php } ?>
									<?php if ( $slide['button'] ) { ?>
										<div class="button-holder fadeInDown-3">
											<a class="btn-lg btn btn-uppercase btn-primary shop-now-button" href="<?= $slide['link']; ?>"<?= $slide['target'] ? ' target="' . $slide['target'] . '"' : ''; ?>><?= $slide['button']; ?></a>
										</div>
									<?php } ?>
								</div><!-- /.caption -->
							</div><!-- /.container-fluid -->
						<?php } ?>
						<?php if( ! $slide['button'] ) { ?>
							<a class="hidden-slider-button hidden" href="<?= $slide['link']; ?>"<?= $slide['target'] ? ' target="' . $slide['target'] . '"' : ''; ?>><span></span></a>
						<?php } ?>
					</div><!-- /.item -->
				<?php } ?>
			<?php } ?>
		</div>
		<!-- /.owl-carousel -->
	</div>
	<!-- ========================================= SECTION – HERO : END ========================================= -->
<?php  }
// End of file.php.
