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
	table_open('',true,base_url('settings/potensi_dokter/data'),'rumus_kriteria_potensi');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('produk_grup'),'','data-content="nama" data-table="produk_grup"');
				th(lang('min_pasien'),'text-center','data-content="min_pasien"');
				th(lang('max_pasien'),'text-center','data-content="max_pasien"');
				th(lang('potensi'),'text-center','data-content="potensi"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/potensi_dokter/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			select2(lang('produk_grup'),'produk_grup','required',get_data('produk_grup','is_active',1)->result_array(),'kode','nama');
			select('Type','type', '',[
				'A' => 'A',
				'B' => 'B',
			]);
			input('number',lang('min_pasien'),'min_pasien');
			input('number',lang('max_pasien'),'max_pasien','required');
			echo '<div id="b_type_box" style="display:none; margin-bottom:10px">';
			input('number','Min Fee Patient','min_fee_patient');
			input('number','Max Fee Patient','max_fee_patient');
			toggle('R/ Original','ap_original');
			echo '</div>';
			input('text',lang('potensi'),'potensi','required');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/potensi_dokter/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
<script>
	$('#type').on('change', function(){
		switch($(this).val()){
			case 'B':
				$('#b_type_box').show(1000);
			break
			default: 
				$('#b_type_box').hide(1000);
			break;
		}
	})
</script>