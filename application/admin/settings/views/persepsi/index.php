<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<label> Product Group </label>
			<select class="select2" style="width: 175px;" onchange="filter()" id="fpgroup">
				<?php
					$produk_grup = get_data('produk_grup', [
						'where' => [
							'is_active' => 1
						]
					])->result_array();
					echo '<option value="">Select Produk Grup</option>';
					foreach($produk_grup as $val){
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
	table_open('',true,base_url('settings/persepsi/data'),'persepsi_acara');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('tipe'),'','data-content="tipe"');
				th(lang('persepsi'),'','data-content="persepsi"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/persepsi/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			select2('Produk Grup','produk_grup','required',$produk_grup,'kode','nama');
			select(lang('tipe'),'tipe','required',[
				'Sebelum',
				'Setelah'
			]);
			textarea(lang('persepsi'),'persepsi','required');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/persepsi/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>

<script>

	function filter(){
		var pgroup = $('#fpgroup').val();
		if(pgroup != ''){
			$('[data-serverside]').attr('data-serverside', base_url + 'settings/persepsi/data?produk_grup='+pgroup)
			refreshData();	
		}
	}

</script>
