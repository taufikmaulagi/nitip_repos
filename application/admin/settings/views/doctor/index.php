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
	table_open('',true,base_url('settings/doctor/data'),'dokter');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('nama'),'','data-content="nama"');
				th(lang('tanggal_lahir'),'','data-content="tanggal_lahir" data-type="daterange"');
				th(lang('spesialist'),'','data-content="nama" data-table="spesialist"');
				th(lang('subspesialist'),'','data-content="nama" data-table="sub_spesialist"');
				th(lang('branch'),'','data-content="nama" data-table="branch"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/doctor/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('nama'),'nama','required|max-length:75');
			input('date',lang('tanggal_lahir'),'tanggal_lahir');
			select2(lang('spesialist'),'spesialist','required',get_data('spesialist')->result_array(),'id','nama');
			select2(lang('subspesialist'),'subspesialist','',get_data('sub_spesialist')->result_array(),'id','nama');
			select2(lang('branch'),'branch','required',get_data('branch')->result_array(),'id','nama');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/doctor/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
