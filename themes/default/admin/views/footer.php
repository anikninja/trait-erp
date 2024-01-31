<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="clearfix"></div>
<?= '</div></div></div></td></tr></table></div></div>'; ?>
<div class="clearfix"></div>
<footer>
<a href="#" id="toTop" class="blue" style="position: fixed; bottom: 30px; right: 30px; font-size: 30px; display: none;">
    <i class="fa fa-chevron-circle-up"></i>
</a>

    <p style="text-align:center;">&copy; <?= date('Y') . ' ' . $Settings->site_name; ?> (<a href="<?= base_url('documentation.pdf'); ?>" target="_blank">v<?= $Settings->version; ?></a>
        ) <?php if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    echo ' - Page rendered in <strong>{elapsed_time}</strong> seconds';
} ?></p>
</footer>
<?= '</div>'; ?>
<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal fade in" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true"></div>
<div id="modal-loading" style="display: none;">
    <div class="blackbg"></div>
    <div class="loader"></div>
</div>
<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>
<?php unset($Settings->setting_id, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->update, $Settings->reg_ver, $Settings->allow_reg, $Settings->default_email, $Settings->mmode, $Settings->timezone, $Settings->restrict_calendar, $Settings->restrict_user, $Settings->auto_reg, $Settings->reg_notification, $Settings->protocol, $Settings->mailpath, $Settings->smtp_crypto, $Settings->corn, $Settings->customer_group, $Settings->envato_username, $Settings->purchase_code); ?>
<script type="text/javascript">
var dt_lang = <?= $dt_lang; ?>, dp_lang = <?= $dp_lang; ?>,
	noImage = '<?= isset( $Settings->noImage ) ? $Settings->noImage : ''; ?>',
	site = <?=
	json_encode( [
		'url'              => base_url(),
		'base_url'         => admin_url(),
		'assets'           => $assets,
		'settings'         => $Settings,
		'dateFormats'      => $dateFormats,
		'csrf_token'       => $this->security->get_csrf_token_name(),
		'csrf_token_value' => $this->security->get_csrf_hash(),
	] ); ?>;
var lang = {
	paid: '<?=lang('paid');?>',
	pending: '<?=lang('pending');?>',
	completed: '<?=lang('completed');?>',
	ordered: '<?=lang('ordered');?>',
	received: '<?=lang('received');?>',
	partial: '<?=lang('partial');?>',
	sent: '<?=lang('sent');?>',
	r_u_sure: '<?=lang('r_u_sure');?>',
	due: '<?=lang('due');?>',
	returned: '<?=lang('returned');?>',
	transferring: '<?=lang('transferring');?>',
	active: '<?=lang('active');?>',
	inactive: '<?=lang('inactive');?>',
	unexpected_value: '<?=lang('unexpected_value');?>',
	select_above: '<?=lang('select_above');?>',
	download: '<?=lang('download');?>',
	required_invalid: '<?=lang('required_invalid');?>'
};
</script>
<?php
$s2_lang_file = read_file('./assets/config_dumps/s2_lang.js');
foreach (lang('select2_lang') as $s2_key => $s2_line) {
    $s2_data[$s2_key] = str_replace(['{', '}'], ['"+', '+"'], $s2_line);
}
$s2_file_date = $this->parser->parse_string($s2_lang_file, $s2_data, true);
?>
<script type="text/javascript" src="<?= $assets ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery.dataTables.dtFilter.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/select2.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/jquery.calculator.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/core.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/perfect-scrollbar.min.js"></script>
<?= ($m == 'purchases' && ($v == 'add' || $v == 'edit' || $v == 'purchase_by_csv')) ? '<script type="text/javascript" src="' . $assets . 'js/purchases.js"></script>' : ''; ?>
<?= ($m == 'transfers' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/transfers.js"></script>' : ''; ?>
<?= ($m == 'sales' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/sales.js"></script>' : ''; ?>
<?= ($m == 'returns' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/returns.js"></script>' : ''; ?>
<?= ($m == 'quotes' && ($v == 'add' || $v == 'edit')) ? '<script type="text/javascript" src="' . $assets . 'js/quotes.js"></script>' : ''; ?>
<?= ($m == 'products' && ($v == 'add_adjustment' || $v == 'edit_adjustment')) ? '<script type="text/javascript" src="' . $assets . 'js/adjustments.js"></script>' : ''; ?>
<?= ($m == 'warehouse' && ($v == 'add_adjustment' || $v == 'edit_adjustment')) ? '<script type="text/javascript" src="' . $assets . 'js/adjustments.js"></script>' : ''; ?>

<script type="text/javascript" charset="UTF-8">var oTable = '', r_u_sure = "<?=lang('r_u_sure')?>";
    <?=$s2_file_date?>
    $.extend(true, $.fn.dataTable.defaults, {"oLanguage":<?=$dt_lang?>});
    $.fn.datetimepicker.dates['erp'] = <?=$dp_lang?>;
    $(window).load(function () {
        $('.mm_<?=$m?>').addClass('active');
        $('.mm_<?=$m?>').find("ul").first().slideToggle();
        $('#<?=$m?>_<?=$v?>').addClass('active');
        $('.mm_<?=$m?> a .chevron').removeClass("closed").addClass("opened");
    });
    // $.fn.modal.prototype.constructor.Constructor.DEFAULTS.backdrop = 'static';
</script>
<?= (DEMO) ? '<script src="' . $assets . 'js/ppp_ad.min.js"></script>' : ''; ?>
<script type="text/javascript" src="<?= base_url('assets/custom/custom.js') ?>"></script>
<?php if ( 'products' === $m && ( 'add' === $v || 'edit' === $v ) ) { ?>
<script>
	(function($){
		var gh  = '.group-header',
			gis = '.group-items',
			gi  = '.group-item',
			gil = '.item-label > span',
			gp  = '.group-field';
		function updateGroupItemCount( groupItems ) {
			if ( groupItems.length ) {
				groupItems.each( function( idx ) {
					idx++;
					var self = $(this),
						label = self.find( gil ),
						_label = label.text().replace( /\d{1,}/g, idx );
					label.text( _label );
				} );
			}
		}
		function initializeEditor( el ) {
			el.find('textarea')
			.not('.skip')
			.redactor({
				buttons: [
					'formatting',
					'|',
					'alignleft',
					'aligncenter',
					'alignright',
					'justify',
					'|',
					'bold',
					'italic',
					'underline',
					'|',
					'unorderedlist',
					'orderedlist',
					'table',
					'|',
					'indent', 'outdent',
					'|',
					'image', 'video',
					'|',
					'link',
					'|',
					'html',
				],
				formattingTags: ['p', 'pre', 'h3', 'h4'],
				minHeight: 100,
				changeCallback: function(e) {
					var editor = this.$editor.next('textarea');
					if ($(editor).attr('required')) {
						$('form[data-toggle="validator"]').bootstrapValidator('revalidateField', $(editor).attr('name'));
					}
				},
			})
		}
		$(document)
		.on( 'click', gh + ' .remove-item', function ( e ) {
			e.preventDefault();
			var item = $(this).closest( gi ),
				parent = item.closest( gis );
			item.remove();
			updateGroupItemCount( parent.find( gi ) );
		} )
		.on( 'click', '.group-bottom .add-item', function ( e ) {
			e.preventDefault();
			var self = $(this),
				templateEl = self.closest( '.group-bottom' ).find( 'script' ),
				idx = templateEl.data( 'idx' ),
				template = templateEl.text().trim().replace( /__IDX__/g, idx ),
				parent = self.closest( gp );
			templateEl.data( 'idx', ( idx + 1 ) );
			template = $( template );
			template.appendTo( parent.find( gis ) );
			initializeEditor( template );
			updateGroupItemCount( parent.find( gi ) );
		} );
	})(jQuery);
</script>
<?php } ?>
<?php if ( 'shop_settings' === $m && 'theme_settings' === $v ) { ?>
<script>var require = { paths: { 'vs': 'themes/default/admin/assets/vs-code/min/vs' } };</script>
<script src="<?= $assets; ?>vs-code/min/vs/loader.js"></script>
<script src="<?= $assets; ?>vs-code/min/vs/editor/editor.main.nls.js"></script>
<script src="<?= $assets; ?>vs-code/min/vs/editor/editor.main.js"></script>
<script src="<?= $assets; ?>vs-code/emmet-monaco.min.js"></script>
<!--suppress JSValidateTypes -->
<script>
	var editorInit,
		editorOpt = {
			tabCompletion: 'on',
			theme: 'vs-dark',
			model: null,
			lineNumbers: true,
			tabSize: 4,
			insertSpaces: true,
		},
		changed = false,
		editors = $('.code-edit');
	(function($){
		"use strict";
		require(['vs/editor/editor.main'], function () {
			emmetMonaco.emmetHTML(monaco);
			editorInit = function( el, content, type ) {
				var stage = el.find('.stage'),
					store = el.find('.store');
				store.hide();
				stage.show();
				var editor = monaco.editor.create( stage.get(0), editorOpt ),
					model = monaco.editor.createModel( store.text(), type );
				el.removeClass( 'not-init' );
				editor.setModel(model);
				//monaco.editor.setTheme( 'vs-dark' );
				el.data( 'editor', editor );
			};
			$('.code-edit').each( function() {
				var self = $(this),
					type = self.data('type');
				// only html & css support.
				if ( 'html' === type || 'css' === type || 'javascript' === type ) {
					editorInit( self, '', type );
				}
			});
		});
		$('form').on( 'submit', function() {
			$('.code-edit').each( function() {
				var self = $(this),
					editor = self.data( 'editor' ),
					store = self.find( '.store' );
				if ( store.text().trim() != editor.getValue().trim() ) {
					changed = false;
					store.text( editor.getValue().trim() );
				}
			} );
		} );
		$(window)
		.on('beforeunload', function(){
			$('.code-edit').each( function() {
				var self = $(this),
					editor = self.data( 'editor' ),
					store = self.find( '.store' );
				if ( store.text().trim() != editor.getValue().trim() ) {
					changed = true;
				}
			} );
			if ( changed ) {
				return 'Changes that you made may not be saved.';
			}
		})
		.on( 'resize', function() {
			$('.code-edit').each( function() {
				$(this).data( 'editor' ).layout();
			} );
		} );
		var csrf_token = '<?= $this->security->get_csrf_token_name() ?>',
			csrf = {};
		csrf[csrf_token] = '<?= $this->security->get_csrf_hash() ?>';
		$(document)
		.on( 'ready', function () {
			$('.segments-wrap').each( function () {
				var segment = $( this ),
					max     = segment.data( 'max_item' ),
					count   = segment.data( 'item_count' ),
					target  = segment.find( '.addSection' );
				if ( max && count >= max ) {
					target.hide();
				} else {
					target.show();
				}
			} );
		} )
		.on( 'change', '.addSection', function ( e ) {
			e.preventDefault();
			var self     = $( this ),
				segment  = self.closest( '.segments-wrap' ),
				isWidget = segment.hasClass( 'segment-widgets' ),
				//target   = self.closest( '.row' ).next( '.rendered-section' ),
				target   = segment.find( '.rendered-section' ),
				max      = segment.data( 'max_item' ),
				count    = segment.data( 'item_count' ),
				idx      = segment.data( 'idx' ),
				selected = self.find( 'option:selected' ),
				name     = selected.data( 'key' ),
				type     = selected.val();
			selected.prop( 'selected', false );
			if ( isWidget ) {
				name = name.replace( /__WID__/g, segment.data( 'widget' ) );
				name = name.replace( /__IDX__/g, idx );
			} else {
				name = name.replace( /__IDX__/g, idx );
			}
			idx += 1;
			count += 1;
			segment.data( 'item_count', count );
			segment.data( 'idx', idx );
			var data = $.extend( {}, csrf, { name, type, count } );
			$.post( '<?= admin_url( 'shop_settings/get_theme_settings_field' ); ?>', data, function( resp ) {
				if ( resp.success ) {
					target.append( resp.data );
					if ( 'custom' === type || 'copyright' === type ) {
						editorInit( target.find( '.code-edit.not-init' ), '', 'html' );
					}
				} else {
					alert( resp.data );
				}
			} ).fail( function () {
				alert( 'There\'s some error, please reload and try again' );
			});
			
			segment.data( 'item_count', count );
			if ( max && count >= max ) {
				self.hide();
			}
		} )
		.on( 'click', '.remove-segment', function ( e ) {
			e.preventDefault();
			var self = $(this),
				segment = self.closest( '.segments-wrap' ),
				target  = self.closest( '.setting-group' ),
				count   = segment.data( 'item_count' ),
				max     = segment.data( 'max_item' );
			target.remove();
			count -= 1;
			segment.data( 'item_count', count );
			if ( max && count < max ) {
				segment.find( '.addSection' ).show();
			}
		} )
		.on( 'click', '.remove-url-list', function ( e ) {
			e.preventDefault();
			var self = $(this),
				wrap = self.closest( '.list-items' ),
				count = wrap.data( 'item_count' );
			count -= 1;
			wrap.data( 'item_count', count );
			self.closest( '.list-item' ).remove();
		} )
		.on( 'click', '.addLink', function ( e ) {
			e.preventDefault();
			var self = $(this),
				target = self.closest( '.row' ).find( '.list-items' ),
				count = target.data( 'item_count' ),
				idx = target.data( 'idx' ),
				template = self.closest( '.form-group' ).find( 'script[type="text/template"]' ).text().trim();
			count += 1;
			idx += 1;
			template = template.replace( /__IDX__/g, idx );
			target.append( template );
			target.data( 'item_count', count );
			target.data( 'idx', idx );
		} )
		.on( 'click', '.has-collapse', function() {
			setTimeout( function() {
				$(window).resize();
			}, 500 );
		} );
	})(jQuery);
</script>
<?php } ?>
<script>
	(function($){
		"use strict";
		$(window).keydown( function(e) {
			var cc = e.keyCode || e.charCode || 0,
				submit = $('form').find( '[type="submit"]' ),
				collapse = $('.has-collapse');
			if ( collapse.length && ( e.ctrlKey || e.metaKey ) && e.shiftKey && ( cc === 189 || cc === 187 ) ) {
				e.preventDefault();
				collapse.click();
				setTimeout( function() {
					$(window).resize();
				}, 150 );
			}
			if ( ( e.ctrlKey || e.metaKey ) && cc === 83 && submit.length ) {
				e.preventDefault();
				if ( confirm( '<?= lang( 'save_settings' ); ?>' ) ) {
					submit.removeAttr( 'disabled' );
					$('form').find( '[type="submit"]' ).trigger( 'click' );
				}
			}
		})
	})(jQuery);
</script>
</body>
</html>
