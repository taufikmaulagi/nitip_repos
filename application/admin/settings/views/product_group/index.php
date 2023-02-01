<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<label>Divisi</label>
			<select class="select2" id="fdivision" onchange="filter()">
				<option value="">ALL DIVISION</option>
				<?php
					foreach(get_data('divisi')->result_array() as $val){
						echo '<option value="'.$val['kode'].'">'.$val['nama'].'</option>';
					}
				?>
			</select>
			<label>Team</label>
			<select class="select2" id="fteam" style="width: 100px;" onchange="filter()">
				<option value="">ALL TEAM</option>
				<?php
					foreach(get_data('tim')->result_array() as $val){
						echo '<option value="'.$val['kode'].'">'.$val['nama'].'</option>';
					}
				?>
			</select>
			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/product_group/data'),'produk_grup');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('kode'),'','data-content="kode"');
				th(lang('nama'),'','data-content="nama"');
				th(lang('divisi'),'','data-content="nama" data-table="divisi"');
				th('Team','','data-content="nama" data-table="tim"');
				th('JML. Approve Profiling','text-center','data-content="jumlah_profiling"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/product_group/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('kode'),'kode','required|max-length:15');
			input('text',lang('nama'),'nama','required|max-length:55');
			select2(lang('divisi'),'kode_divisi','required',get_data('divisi')->result_array(),'kode','nama');
			select2('Team','kode_team','required',get_data('tim')->result_array(),'kode','nama');
			input('number','JML. Approve Profiling','jumlah_profiling','required');
			select2('Spesialist','spesialist[]','required',get_data('spesialist')->result_array(), 'id', 'nama','','multiple');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/product_group/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
<script>
	function filter(){
		var fdivision = $('#fdivision').val();
		var fteam = $('#fteam').val();
		$('[data-serverside]').attr('data-serverside', base_url + 'settings/product_group/data?division='+fdivision+'&team='+fteam);
		refreshData();
	}
</script>