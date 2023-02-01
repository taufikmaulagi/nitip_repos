<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/target_dfr/data'),'target_dfr');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('produk_grup'),'','data-content="nama_produk_grup" data-custom="true"');
				th('Team','','data-content="nama_tim" data-custom="true"');
				th(lang('target'),'','data-content="target"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/target_dfr/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			select2(lang('produk_grup'),'produk_grup','required',$produk_grup,'kode','nama');
			select2('Team','tim','',get_data('tim', [
				'where' => [
					'is_active' => 1
				]
			])->result_array(),'id','nama');
			input('text',lang('target'),'target','required');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/target_dfr/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
