<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<label> Call Type </label>
			<select class="select2" id="fcalltype">
				<option value="">ALL CALL TYPE</option>
				<?php foreach(get_data('call_type')->result_array() as $val){
					echo '<option value="'.$val['id'].'">'.$val['tipe'].'</option>';
				} ?>
			</select>
			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/sub_call_type/data'),'sub_call_type');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('nama'),'','data-content="nama"');
				th(lang('call_type'),'','data-content="tipe" data-table="call_type"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/sub_call_type/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('nama'),'nama','required|max-length:55');
			select2(lang('call_type'),'call_type','required',get_data('call_type')->result_array(),'id','tipe');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/sub_call_type/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
<script>
	function filter(){
		var call_type = $('#fcalltype').val();
		$('[data-serverside]').attr('data-serverside',base_url+'settings/sub_call_type/data?call_type='+call_type);
		refreshData();
	}
</script>