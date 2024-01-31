<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-cogs"></i><?= lang('slider_settings'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('update_info'); ?></p>
                <?php
                
                $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart( 'shop_settings/slider', $attrib );
                ?>
                <div class="row">
                    <div class="col-md-12">
                    <?php for ( $i = 1; $i < 5; $i ++ ) {
	                    $slider = $slider_settings[ ( $i - 1 ) ];
                    	?>
	                    <div class="row _slider slider_<?php echo $i; ?>">
		                    <div class="col-md-3">
			                    <div class="form-group">
				                    <?= lang('image', 'image' . $i ); ?> <?php echo $i ?>
				                    <?php
				                    $inClass = 'image-input';
				                    if ( isset( $slider->image ) ) {
					                    $inClass .= ' hidden';
			                        ?>
					                    <div class="slider-preview">
						                    <a href="#" class="remove-image" title="Remove/Change Image">
							                    <i class="fa-fw fa fa-times"></i>
						                    </a>
						                    <img class="" src="<?php echo base_url('assets/uploads/' . $slider->image ); ?>" alt="Slider Preview">
					                    </div>
					                    <input type="hidden" class="del_image" name="del_image<?php echo $i; ?>" value="0">
					                    <input type="hidden" class="slider_image" name="file_image<?php echo $i; ?>" value="<?php echo $slider->image; ?>">
				                    <?php } ?>
				                    <div class="<?php echo $inClass; ?>" style="display: inline-block; margin-left: 20px;">
				                        <input id="image<?php echo $i; ?>" type="file" name="image<?php echo $i; ?>" data-browse-label="<?= lang('browse'); ?>"  data-show-upload="false" data-show-preview="false" class="form-control file">
				                    </div>
			                    </div>
		                    </div>
		                    <div class="col-md-2">
			                    <div class="form-group">
				                    <?= lang('title', 'title' . $i ); ?> <?php echo $i ?>
				                    <?= form_input( 'title' . $i, set_value( 'title' . $i, ( isset( $slider->title ) ? $slider->title : '' ) ), 'class="form-control tip" id="title' . $i . '"' ); ?>
			                    </div>
		                    </div>
		                    <div class="col-md-2">
			                    <div class="form-group">
				                    <?= lang('link', 'link' . $i ); ?> <?php echo $i ?>
				                    <?= form_input( 'link' . $i, set_value( 'link' . $i, ( isset( $slider->link ) ? $slider->link : '' ) ), 'class="form-control tip" id="link' . $i . '"' ); ?>
			                    </div>
		                    </div>
		                    <div class="col-md-5">
			                    <div class="form-group">
				                    <?= lang( 'caption', 'caption' . $i ); ?> <?php echo $i ?>
				                    <?= form_input( 'caption' . $i, set_value( 'caption' . $i, ( isset( $slider->caption ) ? $slider->caption : '' ) ), 'class="form-control tip slider-caption" id="caption' . $i . '"' ); ?>
				                    <a href="#" class="btn btn-inline btn-link slider-remove" style="display: inline-block;">
					                    <i class="fa-fw fa fa-trash-o"></i>
				                    </a>
			                    </div>
		                    </div>
	                    </div>
                    <?php } ?>
                        <?= form_submit('update', lang('update'), 'class="btn btn-primary"'); ?>
                    </div>
                </div>
                <?= form_close(); ?>
	            <script>
		            (function($){
		            	$(document).ready(function(){
		            		$(document).on( 'click', '.slider-remove', function( e ) {
		            			e.preventDefault();
		            			$(this).closest( '._slider' ).hide();
		            			$(this).closest( '._slider' ).find( '.slider_image' ).val('del');
				            } );
		            		$(document).on( 'click', '.remove-image', function( e ) {
		            			e.preventDefault();
		            			$(this).closest( '._slider' ).find( '.image-input' ).removeClass( 'hidden' );
		            			$(this).closest( '._slider' ).find( '.slider-preview' ).hide();
		            			$(this).closest( '._slider' ).find( '.del_image' ).val('1');
				            } );
			            });
		            })(jQuery);
	            </script>
            </div>
        </div>
    </div>
</div>
