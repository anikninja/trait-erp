<?php
defined('BASEPATH') or exit('No direct script access allowed');
if ( ! empty( $home_slider ) && $showSlider ) { ?>
	<!-- ========================================== SECTION – HERO ========================================= -->
<div class="row">
    <div class="<?php if ( isset( $theme_options['slider-side'] ) && ! empty( $theme_options['slider-side'] ) ) { echo 'col-lg-9 col-md-8 col-sm-8 col-xs-12 col2'; } else { echo 'col-md-12'; } ?>">
        <div id="sohomepage-slider1">
            <div class="so-homeslider sohomeslider-inner-1 main-homeslider">
			<?php
			foreach( $home_slider as $slide ) {
				if( empty( $slide->image ) ) {
					continue;
				}
				$link = ! empty ( $slide->link ) ? $slide->link : '#';
				$title = ! empty ( $slide->title ) ? $slide->title : '';
			?>
                <div class="item">
                    <a href="<?php echo $link; ?>" title="slide 1" target="_self">
                        <img class="lazyload"   data-sizes="auto" src="<?php echo base_url('assets/uploads/' . $slide->image) ?>"  alt="<?php echo $title; ?>" />
                    </a>
				    <?php if ( ! empty( $slide->title ) ) { ?>
                    <div class="sohomeslider-description">
                        <h2><?php echo $title; ?></h2>
                    </div>
                    <?php } ?>
                </div>
				<?php
			}
			?>
            </div>
        </div>
    </div>
    <?php if ( isset( $theme_options['slider-side'] ) && ! empty( $theme_options['slider-side'] ) ) {
        echo $theme_options['slider-side'];
    } ?>
</div>
	<!-- ========================================= SECTION – HERO : END ========================================= -->
<?php  }
// End of file.php.
