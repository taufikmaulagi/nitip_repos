<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<label>Product Group</label>
			<select class="select2" id="fpgroup" onchange="filter()">
				<option value="">ALL PRODUCT GROUP</option>
				<?php foreach(get_data('produk_grup')->result_array() as $val){
					echo '<option value="'.$val['kode'].'">'.$val['nama'].'</option>';
				} ?>
			</select>
			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/product_sub_group/data'),'produk_subgrup');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('kode'),'','data-content="kode"');
				th(lang('nama'),'','data-content="nama"');
				th('PRODUK GROUP','','data-content="nama" data-table="produk_grup"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/product_sub_group/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('kode'),'kode','required|max-length:20');
			input('text',lang('nama'),'nama','required|max-length:30');
			select2(lang('kode_grup'),'kode_grup','required',get_data('produk_grup')->result_array(),'kode','nama');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/product_sub_group/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
<script>
	function filter(){
		var fpgroup = $('#fpgroup').val();
		$('[data-serverside]').attr('data-serverside', base_url + 'settings/product_sub_group/data?product_group='+fpgroup);
		refreshData();
	}
</script>