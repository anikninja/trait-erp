<?php
defined('BASEPATH') or exit('No direct script access allowed');
if ( isset( $home_slider ) && ! empty( $home_slider ) ) {
?>
	<div class="ui-banner-slider">
		<?php foreach ( $home_slider as $slide ) {
			$slide = prepare_slide_data( $slide );
			if ( ! $slide ) {
				continue;
			} ?>
			<div class="item">
				<div class="ui-banner-img">
					<?php if ( ! $slide['button'] ) { ?>
						<a href="<?= $slide['link']; ?>">
							<img src="<?= $slide['image'] ?>" alt="<?= $slide['title']; ?>">
						</a>
					<?php } else { ?>
						<img src="<?= $slide['image'] ?>" alt="<?= $slide['title']; ?>">
					<?php } ?>
				</div>
				<?php if ( $slide['button'] ) { ?>
					<div class="ui-banner-description <?= 'desc-' . $slide['alignment'] . ' ' . $slide['alignment']; ?>">
						<?php
						echo $slide['title'] ? '<h3>' . $slide['title'] . '</h3>' : '';
						echo $slide['subtitle'] ? '<h4 class="hidden-xs">' . $slide['subtitle'] . '</h4>' : '';
						echo $slide['caption'] ? '<p class="hidden-xs">' . $slide['caption'] . '</p>' : '';
						?>
						<a class="btn" href="<?= $slide['link']; ?>"<?= $slide['target'] ? ' target="' . $slide['target'] . '"' : ''; ?>><?= $slide['button']; ?></a>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	<?php
}
// End of file.php.
