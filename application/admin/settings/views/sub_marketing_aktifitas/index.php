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
	table_open('',true,base_url('settings/sub_marketing_aktifitas/data'),'sub_marketing_aktifitas');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('marketing_aktifitas'),'','data-content="nama_market" data-custom="true"');
				th(lang('nama'),'','data-content="nama"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/sub_marketing_aktifitas/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			select2(lang('marketing_aktifitas'),'marketing_aktifitas','required',get_data('marketing_aktifitas', [
				'select' => 'marketing_aktifitas.id, concat(produk_grup.nama," - ",marketing_aktifitas.nama) as nama',
				'join' => [
					'produk_grup on produk_grup.kode = marketing_aktifitas.produk_grup'
				],
				'where' => [
					'produk_grup.is_active' => 1,
					'marketing_aktifitas.is_active' => 1,
				]
			])->result_array(), 'id', 'nama');
			input('text',lang('nama'),'nama');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/sub_marketing_aktifitas/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
