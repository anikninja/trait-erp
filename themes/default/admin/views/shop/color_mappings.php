<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
	.input-group {
		max-width: 100%;
		width: 100%;
		display: block;
	}
	.file-input {
		width: 100%;
	}
	.form-control.file-caption.kv-fileinput-caption {
		width: calc( 100% - 210px );
	}
</style>
<div class="box" id="theme-settings">
	<div class="box-header">
		<h2 class="blue"><i class="fa-fw fa fa-cogs"></i><?= lang('color_mappings' ); ?></h2>
	</div>
	<div class="box-content">
		<div class="row">
			<div class="col-lg-12">
				<?php echo admin_form_open_multipart( 'shop_settings/color_mappings', ['data-toggle' => 'validator', 'role' => 'form'] ); ?>
				<div class="row">
					<div class="col-md-12">
						<div class="row">
						<?php foreach ( $colors as $k => $color ) {
							$color['value'] = ci_parse_args( $color['value'], [ 'code' => '', 'swatch' => '' ] );
							$type    = '';
							$swatch  = '';
							$_swatch = explode( ';base64,', $color['value']['swatch'] );
							if ( 2 === count( $_swatch ) ) {
								$type = $_swatch[0];
								$swatch = $_swatch[1];
							}
							?>
							<div class="col-md-3">
								<label for="color-<?= $k ?>"><?= $color['label']; ?></label>
								<div class="form-group">
									<div class="row">
										<div class="col-md-12">
											<label for="color-<?= $k ?>-code">Color Code</label>
											<input type="text" name="colors[<?= $k; ?>][code]" value="<?= $color['value']['code']; ?>" class="form-control tip" id="color-<?= $k ?>-code">
										</div>
										<div class="col-md-12">
											<label for="color-<?= $k ?>-swatch">Swatch Image</label>
										</div>
										<div class="col-md-12" id="preview-<?= $k; ?>-swatch">
											<div class="preview product-gallery-image" style="width: 50px; height: 50px; margin: 0 auto 15px auto;">
												<input type="hidden" class="file-type" name="colors[<?= $k; ?>][file_type]" value="<?= $type; ?>">
												<input type="hidden" class="file-string" name="colors[<?= $k; ?>][swatch]" value="<?= $swatch; ?>">
												<?php if ( ! empty( $color['value']['swatch'] ) ) { ?>
												<a href="#" class="remove-image">
													<i class="fa-fw fa fa-trash-o"></i>
												</a>
												<img style="width:50px;height:50px;" src="<?= $color['value']['swatch']; ?>" alt="<?= $color['label']; ?>">
												<?php } ?>
											</div>
											<div class="preview-new product-gallery-image" style="width: 50px; height: 50px; margin: 0 auto 15px auto; display: none;">
												<input type="hidden" class="file-type" name="colors[<?= $k; ?>][file_type]" value="" disabled>
												<input type="hidden" class="file-string" name="colors[<?= $k; ?>][swatch]" value="" disabled>
												<a href="#" class="remove-image">
													<i class="fa-fw fa fa-trash-o"></i>
												</a>
												<img style="width:50px;height:50px;" src="" alt="<?= $color['label']; ?>">
											</div>
										</div>
										<div class="col-md-12">
											<input type="file" data-browse-label="<?= lang('browse'); ?>" name="colors[<?= $k; ?>][file]" id="color-<?= $k ?>-swatch" data-show-upload="false" data-show-preview="false" class="form-control file swatch-image" accept="image/*">
										</div>
										<div class="col-md-12">
											<code class="desc">50&times;50px jpg, png or gif only.</code>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
						</div>
					<?php
					?>
					</div>
					<div class="col-md-12">
						<?= form_submit('update', lang('update'), 'class="btn btn-primary"'); ?>
					</div>
				</div>
				<?= form_close(); ?>
			</div>
		</div>
	</div>
</div>
<script>
	(function($){
		function in_array( needle, haystack ) {
			return -1 !== haystack.indexOf( needle );
		}
		var supportedFormats = [ 'image/jpg', 'image/jpeg', 'image/png', 'image/gif' ];
		// supportedFormats[] = 'image/webp'; // <== for easy test
		$('.swatch-image').on('change.bs.fileinput', function ( e ) {
			var file = this.files[0] || false, error = '';
			if ( file ) {
				if ( ! file.size ) {
					error = 'Invalid File';
				}
				if ( file.size > 10000 ) {
					error = 'You can upload file size upto 10kb';
				}
				if ( ! in_array( file.type, supportedFormats ) ) {
					error = 'Invalid File. Only the following formats are supported: ' + supportedFormats.filter( x => x !== 'image/jpeg' ).map( x => x.replace( 'image/', '' ) ).join( ', ' ) + '.';
				}
			}
			if ( '' !== error ) {
				alert( error );
				e.preventDefault();
				$(this).val('');
				return;
			}
			if ( '' === error ) {
				var id = $(this).attr('id').replace( 'color', '' ),
					gallery = $( '#preview' + id ),
					oldGallery = gallery.find('.preview'),
					newGallery = gallery.find( '.preview-new' ),
					val = newGallery.find('.file-string'),
					type = newGallery.find( '.file-type' );
				if ( false === file ) {
					newGallery.hide();
					oldGallery.show();
					newGallery.find('img').attr( 'src', '' );
					val.val( '' );
					type.val( '' );
					val.prop( 'disabled', true );
					type.prop( 'disabled', true );
					return;
				} else {
					var reader = new FileReader();
					reader.onload = function ( frEvent ) {
						var image = new Image();
						image.src = frEvent.target.result;
						image.onload = function() {
							if ( image.width !== 50 || image.height !== 50 ) {
								error = 'Image needs to be 50x50px';
							} else {
								var typeVal = image.src.split( ';base64,' );
								if ( typeVal.length > 0 ) {
									newGallery.find( 'img' ).attr( 'src', image.src );
									newGallery.find('input').prop( 'disabled', false );
									newGallery.find( 'input' ).val( image.src );
									val.val( typeVal[1] );
									type.val( typeVal[0] );
									val.prop( 'disabled', false );
									type.prop( 'disabled', false );
									newGallery.show();
									oldGallery.hide();
								}
							}
						}
					}
					reader.readAsDataURL( file );
				}
			}
			if ( '' !== error ) {
				alert( error );
				e.preventDefault();
				$(this).val('');
				return;
			}
		});
		$(document).on( 'click', '.remove-image', function( e ) {
			e.preventDefault();
			$(this).closest( '.form-group' ).find( '.fileinput-remove' ).click();
		});
	})(jQuery);
</script>
