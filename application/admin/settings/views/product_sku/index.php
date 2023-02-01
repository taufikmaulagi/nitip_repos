<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<label>Group</label>
			<select class="select2" id="fproductgroup" onchange="filter()">
				<option value="">ALL PRODUCT GROUP</option>
				<?php foreach(get_data('produk_grup')->result_array() as $val){
					echo '<option value="'.$val['kode'].'">'.$val['nama'].'</option>';
				}?>
			</select>
			<label>Subgroup</label>
			<select class="select2" id="fsubproductgroup" onchange="filter()">
				<option value="">ALL PRODUCT SUBGROUP</option>
			</select>
			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/product_sku/data'),'produk');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('kode'),'','data-content="kode"');
				th(lang('nama'),'','data-content="nama"');
				th(lang('alias'),'','data-content="alias"');
				th('PRODUK GROUP','','data-content="nama" data-table="produk_grup"');
				th('PRODUK SUB GROUP','','data-content="nama" data-table="produk_subgrup"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/product_sku/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('kode'),'kode','required|max-length:15');
			input('text',lang('nama'),'nama','required|max-length:75');
			input('text',lang('alias'),'alias','required|max-length:100');
			select2(lang('kode_subgrup'),'kode_subgrup','required',get_data('produk_subgrup', [
				'select' => 'produk_subgrup.kode, concat(produk_grup.nama," - ",produk_subgrup.nama) as sub',
				'join' => [
					'produk_grup on produk_subgrup.kode_grup = produk_grup.kode'
				]
			])->result_array(),'kode','sub');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/product_sku/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
<script> 

	$('#fproductgroup').on('change', function(){
		$.ajax({
			url: base_url + 'settings/product_sub_group/get_all?produk_group='+$(this).val(),
			success: function(resp){
				htmlOption = '<option value="">ALL PRODUCT GROUP</option>';
				$.each(resp, function(index, val){
					htmlOption += '<option value="'+val.kode+'">'+val.nama+'</option>';
				});
				$('#fsubproductgroup').html(htmlOption);
				filter();
			}
		})
	});

	function filter(){
		var group = $('#fproductgroup').val();
		var subgroup = $('#fsubproductgroup').val();
		$('[data-serverside]').attr('data-serverside', base_url + 'settings/product_sku/data?subgroup='+subgroup+'&group='+group);
		refreshData();
	}

</script>