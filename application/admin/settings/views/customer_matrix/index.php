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
	table_open('',true,base_url('settings/customer_matrix/data'),'rumus_customer_matrix');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('produk_grup'),'','data-content="nama" data-table="produk_grup"');
				th(lang('potensi'),'text-center','data-content="potensi"');
				th(lang('status_dokter'),'text-center','data-content="status_dokter"');
				th(lang('matrix'),'text-center','data-content="matrix"');
				th(lang('standard_call'),'text-center','data-content="standard_call"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/customer_matrix/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			select2(lang('produk_grup'),'produk_grup','required',get_data('produk_grup','is_active',1)->result_array(),'kode','nama');
			input('text',lang('potensi'),'potensi','required');
			select(lang('status_dokter'),'status_dokter','required',[
				'NU' => 'NU',
				'USE' => 'USE',
				'CONFIRM' => 'CONFIRM'
			]);
			input('text',lang('matrix'),'matrix','required');
			input('text',lang('standard_call'),'standard_call');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/customer_matrix/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
