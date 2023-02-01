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
	table_open('',true,base_url('settings/kpdm/data'),'kpdm');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('nama'),'','data-content="nama"');
				th(lang('rumah_sakit'),'','data-content="nama_rumah_sakit" data-custom="true"');
				th(lang('jabatan'),'','data-content="jabatan"');
				th(lang('branch'),'','data-content="nama_branch" data-custom="true"');
				// th(lang('no_hp'),'','data-content="no_hp"');
				// th(lang('alamat'),'','data-content="alamat"');
				th(lang('tanggal_lahir'),'','data-content="tanggal_lahir" data-type="daterange"');
				th(lang('jenis_kelamin'),'','data-content="jenis_kelamin" data-custom="true"');
				// th(lang('keterangan'),'','data-content="keterangan"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/kpdm/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('nama'),'nama','required');
			select2(lang('branch'),'branch','required',get_data('branch')->result_array(),'id', 'nama');
			select2(lang('rumah_sakit'),'rumah_sakit','required');
			input('text',lang('jabatan'),'jabatan','required');
			input('text',lang('no_hp'),'no_hp');
			textarea(lang('alamat'),'alamat');
			input('date',lang('tanggal_lahir'),'tanggal_lahir');
			radio(lang('jenis_kelamin'),'jenis_kelamin',[
				'L' => 'Laki-laki','P' => 'Perempuan'
			]);
			textarea(lang('keterangan'),'keterangan');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/kpdm/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
<script>

	$('#branch').on('change', function() {
		var branch = $(this).val();
		$('select[name="rumah_sakit"]').select2({
			placeholder: 'Pilih Rumah Sakit',
			minimumInputLength: 3,
			allowClear: true,
			dropdownParent: $('#modal-form'),
			ajax: {
				url: base_url + 'transaction/profiling/get_outlet/' + branch,
				dataType: 'json',
				delay: 250,
				processResults: function(data) {
					return {
						results: data
					};
				},
				cache: true
			}
		});
	});

	$('#modal-form').on('shown.bs.modal', function(e){
		let data_edit = response_edit
		let id_rs = data_edit.rumah_sakit

		if(id_rs != ''){
			$.ajax({
				url: base_url + 'settings/kpdm/get_detail_rs?id='+id_rs,
				success: function(r){
					let html = '<option value="'+r.id+'">'+r.nama+'</option>'
					$('#rumah_sakit').html(html)
				}
			})
		}
	})
</script>